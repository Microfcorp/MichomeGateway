<?
require_once(__DIR__."/../../site/mysql.php");
require_once(__DIR__."/../lib/michom.php");

$API = new MichomeAPI('localhost', $link);
$bot_token = $API->GetSettingORCreate('TelegramBotToken', 'none', 'Токен для работы телеграмм бота')->Value; // токен вашего бота

// функция отправки сообщения от бота в диалог с юзером
function MessageSendTG($chat_id, $text, $reply_markup = '')
{
	global $bot_token;
    $ch = curl_init();
	$text = str_ireplace("\\n", "\n", $text);
    $ch_post = [
        CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMessage',
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POSTFIELDS => [
            'chat_id' => $chat_id,
            /*'parse_mode' => 'HTML',*/
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]
    ];

    curl_setopt_array($ch, $ch_post);
    curl_exec($ch);
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