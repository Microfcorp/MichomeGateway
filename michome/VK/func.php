<?
require_once(__DIR__."/../../site/mysql.php");
require_once(__DIR__."/../lib/michom.php");

$API = new MichomeAPI('localhost', $link);
$VKToken = $API->GetSettingORCreate("VKtoken", "", "Токен для работы VK бота")->Value;

require_once('keyboards.php');

function GetKeyboardID($id){
    global $link;
    $results = mysqli_query($link, "SELECT `KeyboardID` FROM `UsersVK` WHERE `ID` = '".$id."'");
    while($row = $results->fetch_assoc()) {
        return $row['KeyboardID'];
    } 
}
function SetKeyboardID($id, $val){
    global $link;
    mysqli_query($link, "UPDATE `UsersVK` SET `KeyboardID`='$val' WHERE `ID`=".$id);
}
function GetKeyboard($id){
    if(GetKeyboardID($id) == '0') return Keyboard_Main();
    if(GetKeyboardID($id) == '1') return Keyboard_RC58();
    if(GetKeyboardID($id) == '2') return Keyboard_SamsungTV();
}
//890
function MessSend($peer_id, $message, $token){
	global $chunkCounts;
	$rts = [];
	$chanks = str_split($message, $chunkCounts);
	foreach($chanks as $tmp)
		$rts[] = MessSendChunk($peer_id, "\t ".$tmp, $token);
	return $rts;
}
function MessSendChunk($peer_id, $message, $token){
	global $link;
	//$message = str_replace($message, "/n", "\n");
	$request_params = array(
            'message' => $message,
            'peer_ids' => $peer_id,
            'access_token' => $token,
			'random_id' => random_int(-2147483647, 2147483647),
            'v' => '5.130',
            'keyboard' => Keyboard_VK($link, $peer_id),            
        );
 
       $get_params = http_build_query($request_params);  

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/messages.send?' . $get_params);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   //curl_setopt($ch, CURLOPT_POST, 1);
       $m = @curl_exec($ch);
       curl_close($ch);
	   $rt = json_decode($m, true);
       if($peer_id < 0)
           return isset($rt['response'][0]['conversation_message_id']) ? $rt['response'][0]['conversation_message_id'] : null;
       else
           return isset($rt['response'][0]['message_id']) ? $rt['response'][0]['message_id'] : null;
}
function MessSendVKAttach($peer_id, $message, $atachmentArray){
	global $VKToken;
	global $link;
	$request_params = array(
        'message' => $message,
        'peer_id' => $peer_id,
		'attachment' => implode(',', $atachmentArray),
        'access_token' => $VKToken,
		'random_id' => random_int(-2147483647, 2147483647),
        'v' => '5.130',
        'keyboard' => Keyboard_VK($link, $peer_id),        
    );

    $get_params = http_build_query($request_params); 
       
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/messages.send?' . $get_params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $m = @curl_exec($ch);
    curl_close($ch);

    return json_decode($m, true);
}

function ImageSendVK($chat_id, $text, $imagesToUpload, $reply_markup = '') 
{
	global $VKToken;
	$attachments = [];
	if(!is_array($imagesToUpload)) $imagesToUpload = [$imagesToUpload];
	$imagesToUpload = array_slice($imagesToUpload, 0, MaximumSendIMGAttempts);
	
	foreach ($imagesToUpload as $imageURL) {	
        // 1. Ваша логика определения пути
        $isUrl = (strpos($imageURL, "http://") === 0 || strpos($imageURL, "https://") === 0 || strpos($imageURL, "ftp://") === 0);
        $path = $isUrl ? $imageURL : (__DIR__ . "/../" . $imageURL);

        $isTemp = false;
        // Если это URL — VK не сможет скачать его сам, скачиваем во временный файл
        if ($isUrl) {
            //$ext = pathinfo(parse_url($imageURL, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $ext = 'jpg';
            $path = tempnam(sys_get_temp_dir(), 'vk_') . '.' . $ext;
            // Создаем контекст для работы по HTTP/HTTPS с поддержкой редиректов
			$context = stream_context_create([
				'http' => [
					'follow_location' => 1,  // Включает следование редиректам (301, 302 и т.д.)
					'max_redirects'   => 5,  // Максимальное количество переходов (защита от зацикливания)
					'timeout'         => 15, // Таймаут соединения в секундах
					'header'          => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 MichomeGateway/2026\r\n" // Некоторые серверы блокируют запросы без User-Agent
				],
				'ssl' => [
					'verify_peer'      => false, // Отключаем строгую проверку SSL-сертификата (полезно, если у источника кривой/самоподписанный SSL)
					'verify_peer_name' => false,
				]
			]);

			// Теперь вместо обычного copy($imageURL, $path) вызываем с контекстом:
			if (!@copy($imageURL, $path, $context)) {
				echo "Предупреждение: Не удалось скачать файл: {$imageURL}\n";
				continue;
			}
            $isTemp = true;
        }

        if (!file_exists($path) || filesize($path) === 0) {
            echo "Предупреждение: Локальный файл не найден: {$path}\n";
            continue;
        }

        // 2. Шаг 1 для VK: Получаем сервер загрузки для каждого фото
        $uploadServer = vkApi_photosGetMessagesUploadServer($chat_id);
        
        if (!$uploadServer) {
            if ($isTemp) @unlink($path);
            throw new Exception("Ошибка получения сервера загрузки VK.");
        }

        // 3. Шаг 2 для VK: Физическая отправка файла через CURLFile
        $ch = curl_init($uploadServer['upload_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['photo' => new CURLFile($path)]);		
        $uploadResult = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // Сразу удаляем временный файл, если он создавался
        if ($isTemp) { @unlink($path); }

        // 4. Шаг 3 для VK: Сохраняем фото в системе VK
        $savedPhoto = vkApi_photosSaveMessagesPhoto($uploadResult['photo'], $uploadResult['server'], $uploadResult['hash']);

        // Если фото успешно сохранилось, добавляем строку вложения в массив
        if ($savedPhoto && isset($savedPhoto[0])) {
            $photoInfo = $savedPhoto[0]; // Метод saveMessagesPhoto возвращает массив объектов
            $attachments[] = "photo" . $photoInfo['owner_id'] . "_" . $photoInfo['id'];
        }
    }

    // Проверяем, удалось ли загрузить хотя бы одно фото
    if (empty($attachments)) {
        throw new Exception("Ни одно изображение не было успешно загружено в VK.");
    }

    // 5. Финальный Шаг 4: Отправка сообщения со ВСЕМИ вложениями сразу
    // Массив вложений склеивается через запятую в одну строку
	return MessSendVKAttach($chat_id, $text, $attachments);
}

function _vkApi_call($method, $params = array()) {
	global $VKToken;
  $params['access_token'] = $VKToken;
  $params['v'] = "5.130";

  $query = http_build_query($params);
  $url = 'https://api.vk.com/method/'.$method.'?'.$query;

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);

  curl_close($curl);

  $response = json_decode($json, true);
  return isset($response['response']) ? $response['response'] : null;
}

function vkApi_photosGetMessagesUploadServer($chatid) {
	return _vkApi_call('photos.getMessagesUploadServer', array(
		'peer_id' => $chatid,
	));
}

function vkApi_photosSaveMessagesPhoto($photo, $server, $hash) {
  return _vkApi_call('photos.saveMessagesPhoto', array(
    'photo'  => $photo,
    'server' => $server,
    'hash'   => $hash,
  ));
}

//--------------------------------------
function Michome_GetParam($cmd,$device){
	return file_get_contents("http://localhost/michome/api/getdata.php?cmd=".$cmd."&device=".$device);
}
function Michome_SetCmd($cmd,$device){
	return file_get_contents("http://localhost/michome/api/setcmd.php?cmd=".$cmd."&device=".$device);
}
function Michome_GetParam_JsonParse($cmd,$device){
	$jsond = json_decode(Michome_GetParam($cmd,$device));
	
	return $jsond->data[$jsond->col];
}
function Michome_Prognoz(){	
	return file_get_contents("http://localhost/michome/prognoz.php?type=VK");
}
function Michome_DateVrem(){	
	global $API;
	return ("Время восхода солнца: ".date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3)."<br>Время захода солнца: ".date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3));
}
function Michome_SetLight($p,$s){
	return Michome_SetCmd('setlight?p='.$p.'%26s='.$s,"StudioLight_Main");
}
function Michome_SetCharModule($p,$s){
	return Michome_SetCmd('setlight?p='.$p.'%26s='.$s,"LightStudio_Elka");
}
function Michome_SetHDC1080Module($p){
    if($p == 1)
        return Michome_SetCmd('setlight?s=1',"Garland_Controller");
    else
        return Michome_SetCmd('setlight?s=0',"Garland_Controller");
}
function Michome_GetPrognoz($d){ 
	//return str_replace(file_get_contents("http://localhost:90/michome/api/getprognoz.php"), "<br />", " ");
    return file_get_contents("http://localhost/michome/api/getprognoz.php?type=VK&d=".$d);
}
function Michome_SendIR($ip, $code){
    Michome_SetCmd('ir?code='.$code, $ip);
}
//*********************
function AddNot($id){
    global $link;
    $res = mysqli_query($link, "SELECT `ID` FROM `UsersVK` WHERE `ID` = ".$id);
    $count = mysqli_num_rows($res);
    if($count <= 0)
        mysqli_query($link, "INSERT INTO `UsersVK`(`ID`, `Type`, `Enable`, `Messanger`) VALUES ('$id','all','1','VK')");
}
function ChangeNot($id, $group){
    global $link;
    AddNot($id);    
    mysqli_query($link, "UPDATE `UsersVK` SET `Type`='$group', `Enable`=1 WHERE `Messanger`='VK' AND `ID`=".$id);
}
function RemoveNot($id){
    global $link;
    mysqli_query($link, "DELETE FROM `UsersVK` WHERE `Messanger`='VK' AND `ID`=".$id);
}

function mymisto(){
	
}
?>
