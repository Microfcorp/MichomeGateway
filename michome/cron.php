<?php require_once("lib/michom.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php
$API = new MichomeAPI('127.0.0.1', $link); //создание класса АПИ
$API->ResetScenesTimer(); //Сбрасывает таймер в начале суток

set_time_limit(50); //Время выполения скрипта 50 секунд

if((isset($argc) && $argc > 1) || isset($_GET['interval']))
	$interval = (isset($argc) && $argc > 1) ? intval($argv[1]) : intval($_GET['interval']); //Интервал выполнения в кроне
else
	$interval = 1; //Интервал выполнения в кроне (998 и 998 - событие запуска и выключения, соответственно)

if($interval == 1){ //Это для минутного крона
	$todayH = date("H");//Получаем часы
	$todayM = date("i");//Получаем минуты
	$today = ($todayH*60)+$todayM;//Переводим в минуты
	
	echo "Current time: " . $todayH . ":" . $todayM . "<br />";

	$results = mysqli_query($link, "SELECT * FROM `scenes`");//Жестко качаем все из БД
	while($row = $results->fetch_assoc()) {
		$Name = $API->GetConstant($row['Name']); //Получаем имя, сразу отпарсив константы
		$enable = $row['Enable']; //Получаем состояние тогла включения      
		$ID = $row['ID']; //Получаем ИД     
		$typeS = '0'; //Тип селектора выполнения (0 - обычный, 1 - действия с кнопкой, 2 - событие включения/выключения системы)
	  	
		if(IsStr($Name, "^bt")) { $enable = '0'; $typeS = '1';} //События с кнопкой выполняются в getpost.php
		if(IsStr($Name, "^sts")) { $enable = '0'; $typeS = '2';} //События при запуске системы выполняются ниже

		$N = $API->GetIFs($Name, $enable, $ID); //Проверяем все условия
		$Name = $N[0];
		$enable = $N[1];        
		
		echo $ID . " - ";
		if($row['Enable'] == "0" && $typeS == '0') echo "Disable toggle <br />"; //Выключено тоглом
		elseif($enable == "0" && $typeS == '0') echo "Disable ifs <br />"; //Выключено условием
		elseif($typeS == '1') echo "Button disable <br />"; //Выключено, потому что события кнопки выполняются не здесь
		elseif($typeS == '2') echo "StartStopSystem disable <br />"; //Выключено, потому что события включения/выключения системы выполняются не здесь
		else{
			$CSEinterval = $row['Timeout']; //Интервал отправки данных
			
			$ST = (intval(explode(':', $row['TStart'])[0])*60) + intval(explode(':', $row['TStart'])[1]); //Получаем стартовое время выполнения
			$ET = (intval(explode(':', $row['TEnd'])[0])*60) + intval(explode(':', $row['TEnd'])[1]); //Получаем конечное время выполнения
			$CSE = (intval(explode(':', $row['CSE'])[0])*60) + intval(explode(':', $row['CSE'])[1]); //Получаем время последней отправки						

			$sun_info = $API->GetSunInfo();
			
			if(IsStr($Name, "^sds;")) $ST = (intval(date("H", $sun_info['sunrise']))*60) + intval(date("i", $sun_info['sunrise'])); //Начало выполнения по рассвету
			if(IsStr($Name, "^sde;")) $ST = (intval(date("H", $sun_info['sunset']))*60) + intval(date("i", $sun_info['sunset'])); //Начало выполнения по закату
			
			if(IsStr($Name, "^eds;")) $ET = (intval(date("H", $sun_info['sunrise']))*60) + intval(date("i", $sun_info['sunrise'])); //Конец выполнения по рассвету
			if(IsStr($Name, "^ede;")) $ET = (intval(date("H", $sun_info['sunset']))*60) + intval(date("i", $sun_info['sunset'])); //Конец выполнения по закату                                      
			            
			
			if(($today >= $ST && $today < $ET) && ($CSE+$CSEinterval < $today)){ //Если текущее время больше стартового и меньше конечного
				$data = $API->GetConstant($row['Data']); //Преобразуем константы
				$data = $API->GetNotification($data); //Отправляем все уведомления
				if(($row['Module'] != "" && $API->SendCmd($row['Module'], $data.'&m=cron', true) === TRUE) || (IsStr($Name, "^nos;") || $row['Module'] == "")){ //Отправляем команду и проверяем удачность
					mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$ID); //Записывает время последней отправки
					echo "Send ON Success <br />";
				}
				else{
					echo "Send ON Error <br />";
				}
			}
			elseif(!($today >= $ST && $today < $ET) && ($today >= $ST && $today < $ET+$interval)){ //Если текущее время не больше стартового и меньше конечного
				$ndata = $API->GetConstant($row['NData']); //Преобразуем константы
				$ndata = $API->GetNotification($ndata); //Отправляем все увдомления
				if(($row['Module'] != "" && $API->SendCmd($row['Module'], $ndata.'&m=cron', true) === TRUE) || (IsStr($Name, "^nos;") || $row['Module'] == "")){ //Отправляем команду и проверяем удачность
					mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$ID);//Запоминаем время последней отправки
					echo "Send OFF Success <br />";
				}
				else{
					echo "Send OFF Error <br />";
				}
			}
			else{
				echo "No Send <br />";
			}
		}
	}
}

if($interval == 5){ //Для 5ти минутного выполнения для модулей
	foreach($MODs as $tmp){
		$vod = $tmp->BaseClass->CronFunction5min;
		if($vod != NULL)
			$vod->call($API, $tmp);
	}
}
if($interval == 10){ //Для 5ти минутного выполнения для модулей
	foreach($MODs as $tmp){
		$vod = $tmp->BaseClass->CronFunction10min;
		if($vod != NULL)
			$vod->call($API, $tmp);
	}
}

if($interval == 998 || $interval == 999){ //При запуске и остановке системы	
	foreach($MODs as $tmp){
		$vod = $tmp->BaseClass->CronFunctionStartup;
		if($vod != NULL)
			$vod->call($API, $tmp);
	}
	
	$results = mysqli_query($link, "SELECT * FROM `scenes` WHERE `Enable`=1 AND `Name` LIKE '%^sts%'"); //Получаем данные из бд, которые имеют тег ^sts
	while($row = $results->fetch_assoc()) {
		$Name = $API->GetConstant($row['Name']);
		$enable = $row['Enable'];        
		$ID = $row['ID'];        
		$typeS = '0';
	  
		if(IsStr($Name, "^bt")) { $enable = '0'; $typeS = '1';} //События с кнопкой выполняются в getpost.php

		$N = $API->GetIFs($Name, $enable, $ID);
		$Name = $N[0];
		$enable = $N[1];        
		
		if($enable == "0" && $typeS == '0') echo "Disable toggle <br />";
		elseif($typeS == '1') echo "Button disable <br />";
		else{							
			$data = $API->GetConstant($interval == 998 ? $row['Data'] : $row['NData']); //998 - запуск, 999 - остановка
			$data = $API->GetNotification($data);
			if(($row['Module'] != "" && $API->SendCmd($row['Module'], $data.'&m=cron', true) === TRUE) || (IsStr($Name, "^nos;") || $row['Module'] == "")){ //Отправляем команду
				mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$ID); //Записывает время последней отправки
				echo "Send ".($interval == 998 ? "ON" : "OFF")." Success <br />";
			}
			else{
				echo "Send ".($interval == 998 ? "ON" : "OFF")." Error <br />";
			}
		}
	}
}
?>
