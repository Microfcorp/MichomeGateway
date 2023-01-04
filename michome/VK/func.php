<?
require_once('keyboards.php');
//define('MAX_FILE_SIZE', 30000000);
require_once(__DIR__."/../../site/simple_html_dom.php");

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
function MessSend($peer_id, $message,$token){
	global $chunkCounts;
	$rts = [];
	$chanks = str_split($message, $chunkCounts);
	foreach($chanks as $tmp)
		$rts[] = MessSendChank($peer_id, "\t ".$tmp, $token);
	return $rts;
}
function MessSendChank($peer_id, $message,$token){
	$request_params = array(
            'message' => $message,
            'peer_ids' => $peer_id,
            'access_token' => $token,
			'random_id' => random_int(-2147483647, 2147483647),
            'v' => '5.130',
            'keyboard' => json_encode(array(
                'one_time' => false,
                'buttons' => GetKeyboard($peer_id),
            )),            
        );
 
       $get_params = http_build_query($request_params);  

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/messages.send?' . $get_params);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   //curl_setopt($ch, CURLOPT_POST, 1);
       $m = @curl_exec($ch);
       curl_close($ch);
       if($peer_id < 0)
           return json_decode($m, true)['response'][0]['conversation_message_id'];
       else
           return json_decode($m, true)['response'][0]['message_id'];
}
function MessSendAttach($peer_id, $message, $atach){
	global $token;
	$request_params = array(
        'message' => $message,
        'peer_id' => $peer_id,
		'attachment' => $atach,
        'access_token' => $token,
		'random_id' => 0,
        'v' => '5.89',
        'keyboard' => json_encode(array(
            'one_time' => false,
            'buttons' => GetKeyboard($peer_id),
        )),        
    );

    $get_params = http_build_query($request_params); 
       
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/messages.send?' . $get_params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $m = @curl_exec($ch);
    curl_close($ch);

    return json_decode($m, true);
}

function _vkApi_call($method, $params = array()) {
	global $token;
  $params['access_token'] = $token;
  $params['v'] = "5.130";

  $query = http_build_query($params);
  $url = 'https://api.vk.com/method/'.$method.'?'.$query;

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);

  curl_close($curl);

  $response = json_decode($json, true);
  return $response['response'];
}

function vkApi_photosGetMessagesUploadServer() {
	global $chatid;
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

function uploadPhoto($url, $file_name) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_name)));
  $json = curl_exec($curl);
  curl_close($curl);

  $response = json_decode($json, true);

  return $response;
}

function _bot_uploadPhoto($file_name) {
  $upload_server_response = vkApi_photosGetMessagesUploadServer();
  $upload_response = uploadPhoto($upload_server_response['upload_url'], $file_name);

  $photo = $upload_response['photo'];
  $server = $upload_response['server'];
  $hash = $upload_response['hash'];

  $save_response = vkApi_photosSaveMessagesPhoto($photo, $server, $hash);
  $photo = array_pop($save_response);

  return $photo;
}

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
function AddNot($id){
    global $link;
    $res = mysqli_query($link, "SELECT `ID` FROM `UsersVK` WHERE `ID` = ".$id);
    $count = mysqli_num_rows($res);
    if($count <= 0)
        mysqli_query($link, "INSERT INTO `UsersVK`(`ID`, `Type`, `Enable`) VALUES ('$id','all','1')");
}
function ChangeNot($id, $group){
    global $link;
    AddNot($id);    
    mysqli_query($link, "UPDATE `UsersVK` SET `Type`='$group', `Enable`=1 WHERE `ID`=".$id);
}
function RemoveNot($id){
    global $link;
    mysqli_query($link, "DELETE FROM `UsersVK` WHERE `ID`=".$id);
}

function mymisto(){
	$url = "http://abitur.bsu.edu.ru/abitur/priem/competition/index.php?idFakultet=2168&type=1&forma=1";
	$html = file_get_html($url);
	$tr = $html->find('.TabTr',22)->find('tr');
	//$tr = $html->find('.TabTr',10);
	$kolpod = 0;
	$isorig = 0;
	$tmp = "";
	foreach($tr as $tt) {
		//echo $tt->find('td', 1);
		$misto = $tt->find('td', 0);
		$snils = $tt->find('td', 1);
		$ispod = $tt->find('td', 9);
		$typedoc = $tt->find('td', 10);
		
		if(strpos($ispod, "Подано") !== FALSE){
			$kolpod = $kolpod + 1;
		}
		
		if(strpos($typedoc, "Оригинал") !== FALSE){
			$isorig = $isorig + 1;
		}
		
		if(strpos($snils, "159-363-741-00") !== FALSE){
			$tmp .= "Твой снилс: 159-***-***-00
";
			$tmp .= "Твое место среди подавих заявление: " . $kolpod . '
';
			$tmp .= "Твое место среди предоставивших оригинал: " . $isorig . '
';
			//$tmp .= "Твое место среди общего рейтинга: " . $misto . '\n';
			$tmp .= intval($kolpod) <= 22 ? "Ты пока проходишь))" : "Упс, ты в пролете((" . '
';
			$tmp .= '
';
		}
	}
	$tmp .= '
';
	$tmp .= "Всего подавих заявление: " . $kolpod . '
';
	$tmp .= "Всего предоставивших оригинал: " . $isorig . '
';
	$tmp .= '
';
	$tmp .= "Всего доступных бюджетных мест: 22 (25)" . '
';
	//echo($tr);
	//echo($html);
	return $tmp;
}
?>
