<?php require_once("lib/michom.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php
$API = new MichomeAPI('localhost', $link);
$API->ResetScenesTimer();

if((isset($argc) && $argc > 1) || isset($_GET['interval']))
	$interval = (isset($argc) && $argc > 1) ? intval($argv[1]) : intval($_GET['interval']); //Интервал выполнения в кроне
else
	$interval = 1; //Интервал выполнения в кроне

if($interval == 1){ //Это для минутного крона
	//$interval = $interval+0; //Прибавляем минуту(на всякий случай)
	$todayH = date("H");//Получаем часы
	$todayM = date("i");//Получаем минуты
	$today = ($todayH*60)+$todayM;//Переводим в минуты
	
	echo "Current time: " . $todayH . ":" . $todayM . "<br />";

	$results = mysqli_query($link, "SELECT * FROM `scenes`");//Жестко качаем все из БД
	while($row = $results->fetch_assoc()) {
		/*$data = array('ID'=>intval($row['ID']),
						'Name'=>($row['Name']),
						'TStart'=>($row['TStart']),
						'TEnd'=>($row['TEnd']),
						'Module'=>($row['Module']),
						'Data'=>($row['Data']),
						);
						
		*/
		$Name = $API->GetConstant($row['Name']);
		$enable = $row['Enable'];        
		$ID = $row['ID'];        
		$typeS = '0';
	  
		if(IsStr($Name, "^bt")) { $enable = '0'; $typeS = '1';}

		$N = $API->GetIFs($Name, $enable, $ID);
		$Name = $N[0];
		$enable = $N[1];        
		
		if($enable == "0" & $typeS == '0') echo "Disable <br />";
		elseif($typeS == '1') echo "Button disable <br />";
		else{
			$CSEinterval = $row['Timeout']; //Интервал отправки данных
			
			$ST = (intval(explode(':', $row['TStart'])[0])*60) + intval(explode(':', $row['TStart'])[1]); //Получаем стартовое время
			$ET = (intval(explode(':', $row['TEnd'])[0])*60) + intval(explode(':', $row['TEnd'])[1]); //Получаем конечное время
			$CSE = (intval(explode(':', $row['CSE'])[0])*60) + intval(explode(':', $row['CSE'])[1]); //Получаем время последней отправки
								
			//echo $data;

			$sun_info = date_sun_info(time(), floatval($API->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value), floatval($API->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value));
			
			if(IsStr($Name, "^sds;")) $ST = (intval(date("H", $sun_info['sunrise']))*60) + intval(date("i", $sun_info['sunrise']));
			if(IsStr($Name, "^sde;")) $ST = (intval(date("H", $sun_info['sunset']))*60) + intval(date("i", $sun_info['sunset']));       
			
			if(IsStr($Name, "^eds;")) $ET = (intval(date("H", $sun_info['sunrise']))*60) + intval(date("i", $sun_info['sunrise']));
			if(IsStr($Name, "^ede;")) $ET = (intval(date("H", $sun_info['sunset']))*60) + intval(date("i", $sun_info['sunset']));                                       
			
			//echo $Name;            
			
			if(($today >= $ST & $today < $ET) & ($CSE+$CSEinterval < $today)){ //Если текущее время больше стартового и меньше конечного
				$data = $API->GetConstant($row['Data']);
				$data = $API->GetNotification($data);
				if($API->SendCmd($row['Module'], $data.'&m=cron') != "Ошибка соеденения с модулем" || IsStr($Name, "^nos;")){ //Отправляем команду
					mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$ID);//Запоминаем время работы
				}
				echo "Send ON <br />";
			}
			elseif(!($today >= $ST & $today < $ET) & ($today >= $ST & $today < $ET+$interval)){ //Если текущее время больше стартового и меньше конечного
				$ndata = $API->GetConstant($row['NData']);
				$ndata = $API->GetNotification($ndata);
				if($API->SendCmd($row['Module'], $ndata.'&m=cron') != "Ошибка соеденения с модулем" || IsStr($Name, "^nos;")){ //Отправляем команду
					mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$ID);//Запоминаем время работы
				}
				echo "Send OFF <br />";
			}
			else{
				echo "No Send <br />";
			}
		}
	}
}

if($interval == 5){
	foreach($MODs as $tmp){
		$vod = $tmp->BaseClass->CronFunction5min;
		if($vod != NULL)
			$vod->call($API, $tmp);
	}
}
if($interval == 10){
	foreach($MODs as $tmp){
		$vod = $tmp->BaseClass->CronFunction10min;
		if($vod != NULL)
			$vod->call($API, $tmp);
	}
}
?>
