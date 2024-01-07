<?
require_once(__DIR__."/../../site/mysql.php");
require_once(__DIR__."/../lib/michom.php");

$API = new MichomeAPI('localhost', $link);
$bot_token = $API->GetSettingORCreate('TelegramBotToken', 'none', 'Токен для работы телеграмм бота')->Value; // токен вашего бота

// функция отправки сообщения от бота в диалог с юзером
function MessageSendTG($chat_id, $text, $reply_markup = '')
{
	if($text == "")
		return;
	
	global $bot_token;
    $ch = curl_init();
	$text = str_ireplace("\\n", "\n", $text);
	$text = str_ireplace("<br/>", "\n", $text);
	$text = str_ireplace("<br />", "\n", $text);
	$text = str_ireplace("<br>", "\n", $text);
    $ch_post = [
        CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMessage',
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POSTFIELDS => [
            'chat_id' => $chat_id,
            /*'parse_mode' => 'html',*/
            'text' => $text,
            /*'replyparameters' => array('message_id' => $reply_markup),*/
        ]
    ];
    curl_setopt_array($ch, $ch_post);
    $res = curl_exec($ch);
	curl_close($ch);
}

function ImageSendTG($chat_id, $text, $imageURL, $reply_markup = '')
{
	global $bot_token;
	
	if(!is_array($imageURL)){
		$path = (IsStr($imageURL, "http://") || IsStr($imageURL, "https://") || IsStr($imageURL, "ftp://") ? $imageURL : (__DIR__."/../".$imageURL));
		$arrayQuery = array(
			'chat_id' => $chat_id,
			'caption' => $text,
			'photo' => curl_file_create($path)
		);		
		$ch = curl_init('https://api.telegram.org/bot'. $bot_token .'/sendPhoto');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		curl_close($ch);
	}
	else{
		$jsm = [];
		$req = array (
			'chat_id' => $chat_id,
			'media' => ''
		);
		for($i = 0; $i < count($imageURL); $i++){
			$path = (IsStr($imageURL[$i], "http://") || IsStr($imageURL[$i], "https://") || IsStr($imageURL[$i], "ftp://") ? $imageURL[$i] : (__DIR__."/../".$imageURL[$i]));
			$jsm[] = ['type' => 'photo', 'media' => 'attach://'.$i.'.jpg', 'caption' => (isset($text[$i]) ? $text[$i] : "") ];
			$req[$i.'.jpg'] = curl_file_create($path);
		}
		$req['media'] = json_encode($jsm);
		$res = sendTelegram('sendMediaGroup', $req);		
	}
}

function sendTelegram($method, $response) {
	global $bot_token;
	$ch = curl_init('https://api.telegram.org/bot' . $bot_token . '/' . $method);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$res = curl_exec($ch);
	curl_close($ch);
	return $res;
}

function setCommand(){
	global $API;
	
	$comm = array();
	$comm[] = array('command' => 'help', 'description' => 'Список всех комманд');
	$comm[] = array('command' => 'location', 'description' => 'Местоположение системы');
	foreach($API->GetBotCmd() as $tmp)
	{
		if($tmp["Enabled"] == 1)
			$comm[] = array('command' => $tmp["Name"], 'description' => $tmp["Desc"]);
	}
	sendTelegram("setMyCommands", array('commands' => json_encode($comm)));
}

function AddNotTG($id){
    global $link;
    $res = mysqli_query($link, "SELECT `ID` FROM `UsersVK` WHERE `ID` = ".$id);
    $count = mysqli_num_rows($res);
    if($count <= 0)
        mysqli_query($link, "INSERT INTO `UsersVK`(`ID`, `Type`, `Enable`, `Messanger`) VALUES ('$id','all','1','TG')");
}
function ChangeNotTG($id, $group){
    global $link;
    AddNot($id);    
    mysqli_query($link, "UPDATE `UsersVK` SET `Type`='$group', `Enable`=1 WHERE `Messanger`='TG' AND `ID`=".$id);
}
function RemoveNotTG($id){
    global $link;
    mysqli_query($link, "DELETE FROM `UsersVK` WHERE `Messanger`='TG' AND `ID`=".$id);
}
?>