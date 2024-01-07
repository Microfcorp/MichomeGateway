<?
/**
*   Michome TG Bot
*   17.11.2023
*/
require_once("func.php");

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8'); // на всякий случай досообщим PHP, что все в кодировке UTF-8 

$site_dir = dirname(dirname(__FILE__)).'/'; // корень сайта
$data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
$data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив

// Для отладки, добавим запись полученных декодированных данных в файл message.txt, 
// который можно смотреть и понимать, что происходит при запросе к боту
// Позже, когда все будет работать закомментируйте эту строку:
//file_put_contents(__DIR__ . '/message.txt', print_r($data, true));

// Основной код: получаем сообщение, что юзер отправил боту и 
// заполняем переменные для дальнейшего использования
if (!empty($data['message']['text'])) {
    $chat_id = $data['message']['from']['id'];
    $user_name = $data['message']['from']['username'];
    $first_name = $data['message']['from']['first_name'];
    $text = mb_strtolower(trim($data['message']['text']));
    $update_id = $data['update_id'];
    $text_array = explode(" ", $text);
    
    if ($text == '/help') {
        $text_return = "$first_name, вот команды, что я понимаю: 
						/help - список команд
						/subscribe - Подписаться на уведомления
						/unsubscribe - Отписаться от уведомлений
						/allnotification - Сменить группу уведомлений на \"Все\"
						/generalnotification - Сменить группу уведомлений на \"Основные\"
						/location - Получить местоположение Michome
						Рекомендую закрепить это сообщение
						";
		foreach($API->GetBotCmd() as $tmp)
		{
			if($tmp["Enabled"] == 1)
				$text_return .= "/".$tmp["Name"] . " - " . $tmp["Desc"]."\r\n						";
		}
        MessageSendTG($chat_id, $text_return);
    }
	elseif ($text == '/start') {
        $text_return = "Добро пожаловать в бота управления Michome: Введите /help для списка комманд";
        MessageSendTG($chat_id, $text_return);
    }
    elseif ($text == '/about') {
        $text_return = "";
        MessageSendTG($chat_id, $text_return);
    }
	elseif ($text == '/subscribe') {
		AddNotTG($chat_id);
        MessageSendTG($chat_id, "Вы успешно подписались на уведомления группы \"Все\"");
    }
	elseif ($text == '/unsubscribe') {
		RemoveNotTG($chat_id);
        MessageSendTG($chat_id, "Вы успешно отписались от всех уведомлений");
    }
	elseif ($text == '/allnotification') {
		ChangeNotTG($chat_id, 'all');
        MessageSendTG($chat_id, "Группа обновлений изменена на \"Все\"");
    }
	elseif ($text == '/generalnotification') {
		ChangeNotTG($chat_id, 'general');
        MessageSendTG($chat_id, "Группа обновлений изменена на \"Основные\"");
    }
	elseif ($text == '/setcmd') {
		setCommand();
    }
	elseif ($text == '/location') {
		$lat = floatval($API->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value);
		$lon = floatval($API->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value);
		
		MessageSendTG($chat_id, "Текущее местоположение сети Michome");
		sendTelegram("sendLocation", array('chat_id' => $chat_id, 'latitude' => $lat, 'longitude' => $lon));
    }
	else
	{
		foreach($API->GetBotCmd() as $tmp)
		{
			if($text == "/".$tmp["Name"] && $tmp["Enabled"] == 1)
			{
				$img = [];
				$txt = [];
				$ret = $tmp["Cmd"];
				$ret = $API->GetConstant($ret);
				while(IsStr($ret, "^img")){
					$expl = substr($ret, stripos($ret, "^img_")+5, (stripos($ret, ";", stripos($ret, "^img_")) - (stripos($ret, "^img_")+5)));     
					$cap = str_ireplace("--", "_", explode('_', $expl)[0]);
					$url = str_ireplace("--", "_", explode('_', $expl)[1]);
					$img[] = $url;					
					$txt[] = $cap;					
					$ret = str_ireplace("^img_".$expl.";", "", $ret);      
				}
				MessageSendTG($chat_id, $ret);
				if(count($img) > 1)
					ImageSendTG($chat_id, $txt, $img);
				else if(count($img) > 0)
					ImageSendTG($chat_id, $txt[0], $img[0]);
			}
			else if($text == "/".$tmp["Name"] && $tmp["Enabled"] == 0){
				MessageSendTG($chat_id, "Данная команда выключена в панели управления Michome");
			}
		}
	}
}
?>