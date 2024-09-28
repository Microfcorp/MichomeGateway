<?php include_once("../../site/mysql.php"); ?>
<?php include_once("func.php"); ?>
<?php include_once("../lib/SamsungTV.php"); ?>
<?php include_once("../lib/michom.php"); ?>

<?
$API = new MichomeAPI('127.0.0.1', $link);

//Строка для подтверждения адреса сервера из настроек Callback API
$confirmationToken = $API->GetSettingORCreate("VKconfirm", "ccc00000", "Токен VK для подтверждения сервера")->Value;
//Секретный ключ
$secretKey = $API->GetSettingORCreate("VKsecret", "Michome", "VK Секретный ключ")->Value;
//Ключ доступа сообщества
$token = $API->GetSettingORCreate("VKtoken", "", "Токен для работы VK бота")->Value;

if (!isset($_REQUEST)) {
    return;
}

$tv = new SamsungTV("192.168.1.31");

//Получаем и декодируем уведомление
$data = json_decode(file_get_contents('php://input'));
 
// проверяем secretKey
if(strcmp($data->secret, $secretKey) !== 0 && strcmp($data->type, 'confirmation') !== 0)
    return;
 
    //Если это уведомление для подтверждения адреса сервера...
    if($data->type == 'confirmation'){
        //...отправляем строку для подтверждения адреса
        die($confirmationToken);
	}
 
    //Если это уведомление о новом сообщении...
    elseif($data->type == 'message_new' || $data->type == 'message_edit'){
        //...получаем id его автора
        $userId = $data->object->user_id;
		$body = $data->object->body;
        
		if(mb_strtolower($body) == "привет"){
			MessSend($userId, "Умный дом приветствует тебя",$token);
		}
		elseif(mb_strtolower($body) == "место в рейтинге"){
			MessSend($userId, mymisto(), $token);
		}
		elseif(mb_strtolower($body) == "температура на улице" || mb_strtolower($body) == "какая температура на улице"){
			MessSend($userId, "Сейчас на улице ".$API->GetPosledData("termometr_okno")->Temp."C",$token);
		}
		elseif(mb_strtolower($body) == "температура в комнате" || mb_strtolower($body) == "какая температура в комнате"){			
			MessSend($userId, "Сейчас в комнате ".$API->GetPosledData("sborinfo_tv")->Temp."C",$token);
		}
		elseif(mb_strtolower($body) == "влажность в комнате" || mb_strtolower($body) == "какая влажность в комнате"){			
			MessSend($userId, "Сейчас в комнате ".$API->GetPosledData("sborinfo_tv")->Humm."%",$token);
		}
		elseif(mb_strtolower($body) == "давление в комнате" || mb_strtolower($body) == "какое давление в комнате"){			
			MessSend($userId, "".$API->GetPosledData("sborinfo_tv")->Dawlen." мм.рт.ст.",$token);
		}
		elseif(mb_strtolower($body) == "ощущение высоты"){			
			MessSend($userId, "Сейчас прям как на".$API->GetPosledData("sborinfo_tv")->Visota." метрах",$token);
		}		
		elseif(mb_strtolower($body) == "свет 1 на всю" || mb_strtolower($body) == "включить свет 1" || mb_strtolower($body) == "включи свет 1"){			
			MessSend($userId, Michome_SetLight('0','1023'),$token);
		}
		elseif(mb_strtolower($body) == "свет 2 на всю" || mb_strtolower($body) == "включить свет 2" || mb_strtolower($body) == "включи свет 2"){			
			MessSend($userId, Michome_SetLight('1','1023'),$token);
		}
		elseif(mb_strtolower($body) == "свет 3 на всю" || mb_strtolower($body) == "включить свет 3" || mb_strtolower($body) == "включи свет 3"){			
			MessSend($userId, Michome_SetLight('2','1023'),$token);
		}
		elseif(mb_strtolower($body) == "свет 1 на 0" || mb_strtolower($body) == "выключить свет 1" || mb_strtolower($body) == "выключи свет 1"){			
			MessSend($userId, Michome_SetLight('0','0'),$token);
		}
		elseif(mb_strtolower($body) == "свет 2 на 0" || mb_strtolower($body) == "выключить свет 2" || mb_strtolower($body) == "выключи свет 2"){			
			MessSend($userId, Michome_SetLight('1','0'),$token);
		}
		elseif(mb_strtolower($body) == "свет 3 на 0" || mb_strtolower($body) == "выключить свет 3" || mb_strtolower($body) == "выключи свет 3"){			
			MessSend($userId, Michome_SetLight('2','0'),$token);
		}
        elseif(mb_strtolower($body) == "свет на пиано" || mb_strtolower($body) == "включи пианино" || mb_strtolower($body) == "свет на пианино"){			
			MessSend($userId, Michome_SetLight('0','207'),$token);
		}        
		elseif(mb_strtolower($body) == "включить ель" || mb_strtolower($body) == "вруби ель" || mb_strtolower($body) == "запусти ель"){			
			MessSend($userId, Michome_SetCharModule('1','1'),$token);
		}
		elseif(mb_strtolower($body) == "выключить ель" || mb_strtolower($body) == "выруби ель" || mb_strtolower($body) == "выпусти ель"){			
			MessSend($userId, Michome_SetCharModule('1','0'),$token);
		}
        elseif(mb_strtolower($body) == "включить фитолампу" || mb_strtolower($body) == "вруби фитолампу" || mb_strtolower($body) == "запусти фитолампу"){			
			MessSend($userId, Michome_SetHDC1080Module(1),$token);
		}
		elseif(mb_strtolower($body) == "выключить фитолампу" || mb_strtolower($body) == "выруби фитолампу" || mb_strtolower($body) == "выпусти фитолампу"){			
			MessSend($userId, Michome_SetHDC1080Module(0),$token);
		}        
		elseif(mb_strtolower($body) == "погода"){			
			MessSend($userId, Michome_Prognoz(),$token);
		}
        elseif(IsStr(mb_strtolower($body), "прогноз погоды")){
            if(IsStr(mb_strtolower($body), "сегодня")){
                MessSend($userId, Michome_GetPrognoz('1'),$token);
            }
            else{
                MessSend($userId, Michome_GetPrognoz('2'),$token); 
            }		
		}
		elseif(mb_strtolower($body) == "ссылка"){			
			MessSend($userId, "http://91.202.27.167/michome/",$token);
		}
        elseif(mb_strtolower($body) == "подписаться на уведомления"){	
            AddNot($userId);
			MessSend($userId, "Вы успешно подписались на уведомления", $token);
		}
        elseif(mb_strtolower($body) == "отписаться от уведомлений"){
            RemoveNot($userId);
			MessSend($userId, "Вы успешно отписались от уведомлений", $token);
		}
        elseif(mb_strtolower($body) == "все уведомления"){	
            ChangeNot($userId, 'all');
			MessSend($userId, "Тип обновлений изменен на \"Все\"", $token);
		}
        elseif(mb_strtolower($body) == "основные уведомления"){	
            ChangeNot($userId, 'general');
			MessSend($userId, "Тип уведомлений изменен на \"Основные\"", $token);
		}
		elseif(mb_strtolower($body) == "время дня" || mb_strtolower($body) == "долгота дня"){			
			MessSend($userId, Michome_DateVrem(),$token);
		}
		elseif(mb_strtolower($body) == "последнее обновление"){			
			MessSend($userId, "Последнее обновление модуля сбора информации было: ".Michome_GetParam("posledob","192.168.1.10"),$token);
		}
		elseif(mb_strtolower($body) == "справка"){			
			MessSend($userId, "Привет! Я бот для удобной навигации в системе умного дома Michom<br>Я понимаю команды:<br>Температура на улице,<br>Температура в комнате,<br>Влажность в комнате,<br>Давление в комнате,<br>Прогноз погоды,<br>Прогноз,<br>Время дня,<br>Ощущение высоты,<br>Последнее обновление,<br>Ссылка,<br>Включение/выключение света,<br>Включи/Выключи гирлянду",$token);
		}
        elseif(mb_strtolower($body) == "управление аккустикой"){
            SetKeyboardID($userId, '1');			
			MessSend($userId, "Управление аккустикой",$token);
		}
        elseif(mb_strtolower($body) == "управление телевизором"){
            SetKeyboardID($userId, '2');			
			MessSend($userId, "Управление телевизором",$token);
		}
        elseif(mb_strtolower($body) == "домой"){
            SetKeyboardID($userId, '0');			
			MessSend($userId, "Главная",$token);
		}
        elseif(IsStr(mb_strtolower($body), "график")){
            if(IsStr(mb_strtolower($body), "температур")){
                $fgts = $API->TimeIns('192.168.1.11', 'selday', date("Y-m-d"));
                $seldays = explode(";", $fgts);
                file_put_contents("tmp.jpg", file_get_contents("http://localhost/michome/grafick.php?ip=192.168.1.11&type=tempul&start=".$seldays[0]."&period=".$seldays[2]));          
            }
            elseif(IsStr(mb_strtolower($body), "давлен")){
                $fgts = $API->TimeIns('192.168.1.10', 'selday', date("Y-m-d"));
                $seldays = explode(";", $fgts);
                file_put_contents("tmp.jpg", file_get_contents("http://localhost/michome/grafick.php?ip=192.168.1.10&type=dawlen&start=".$seldays[0]."&period=".$seldays[2]));           
            }
            else{
                $fgts = $API->TimeIns('192.168.1.10', 'selday', date("Y-m-d"));
                $seldays = explode(";", $fgts);
                file_put_contents("tmp.jpg", file_get_contents("http://localhost/michome/grafick.php?ip=192.168.1.10&type=humm&start=".$seldays[0]."&period=".$seldays[2]));           
            }
            $photo = _bot_uploadPhoto("tmp.jpg");	
            MessSendAttach($userId, "График", 'photo'.$photo['owner_id'].'_'.$photo['id']);
		}
        elseif(mb_strtolower($body) == "включить аккустику"){	
            Michome_SendIR('192.168.1.34', '1119703215');
			MessSend($userId, "Аккустика включена/выключена",$token);
		}
        elseif(mb_strtolower($body) == "увеличить громкость"){	
            Michome_SendIR('192.168.1.34', '1119739935');
			MessSend($userId, "Громкость увеличена",$token);
		}
        elseif(mb_strtolower($body) == "уменьшить громкость"){	
            Michome_SendIR('192.168.1.34', '1119709335');
			MessSend($userId, "Громкость уменьшена",$token);
		}
        elseif(mb_strtolower($body) == "переключить вход"){	
            Michome_SendIR('192.168.1.34', '1119684855');
			MessSend($userId, "Вход переключен",$token);
		}
        elseif(mb_strtolower($body) == "переключить режим"){	
            Michome_SendIR('192.168.1.34', '1119701175');
			MessSend($userId, "Изменен режим 2.1/5.1",$token);
		}
        elseif(mb_strtolower($body) == "звук с малинки"){	
            Michome_SendIR('192.168.1.34', '1119684855');
            Michome_SendIR('192.168.1.34', '1119709335');
			MessSend($userId, "Вход переключен на малинку. Громкость уменьшена",$token);
		}
        elseif(mb_strtolower($body) == "звук с пеки"){	
            Michome_SendIR('192.168.1.34', '1119684855');
            Michome_SendIR('192.168.1.34', '1119701175');
			MessSend($userId, "Вход переключен на пеку. Режим 5.1",$token);
		}
        elseif(mb_strtolower($body) == "включить тв"){	
            //$tv->PowerOnCEC();
			MessSend($userId, "CEC не работает",$token);
		}
        elseif(mb_strtolower($body) == "выключить тв"){	
            $tv->PowerOffTCP();
			MessSend($userId, "ТВ выключен",$token);
		}
        elseif(mb_strtolower($body) == "переключить вход на hdmi"){	
            $tv->AsHDMICEC();
			MessSend($userId, "Вход изменен на HDMI",$token);
		}
        elseif(mb_strtolower($body) == "переключить выход на dtv"){	
            $tv->DTV();
			MessSend($userId, "Вход изменен на DTV",$token);
		}
        elseif(mb_strtolower($body) == "увеличить громкость тв"){	
            $tv->VolumeUP();
			MessSend($userId, "Громкость ТВ увеличена",$token);
		}
        elseif(mb_strtolower($body) == "выключить звук тв"){	
            $tv->Mute();
			MessSend($userId, "Состояние Mute на ТВ изменено",$token);
		}
        elseif(mb_strtolower($body) == "уменьшить громкость тв"){	
            $tv->VolumeDown();
			MessSend($userId, "Громкость ТВ уменьшена",$token);
		}
		elseif(mb_strtolower($body) == "канал +"){	
            $tv->ChUp();
			MessSend($userId, "Переключен на следующий канал",$token);
		}
        elseif(mb_strtolower($body) == "канал -"){	
            $tv->ChDown();
			MessSend($userId, "Переключен на предыдущий канал",$token);
		}
        else{
            MessSend($userId, "Я не понимаю", $token);
        }
	}
 
    // Если это уведомление о вступлении в группу
    elseif($data->type == 'group_join'){
        //...получаем id нового участника
        $userId = $data->object->user_id;
 
        //затем с помощью users.get получаем данные об авторе
        //$userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.8"));
 
        //и извлекаем из ответа его имя
        //$user_name = $userInfo->response[0]->first_name;
 
        //С помощью messages.send и токена сообщества отправляем ответное сообщение
        $request_params = array(
            'message' => "Michom приветствует тебя",
            'user_id' => $userId,
            'access_token' => $token,
            'v' => '5.0'
        );
 
        $get_params = http_build_query($request_params);
 
        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
 
	}

echo('ok');
?>
