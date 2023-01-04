<?php require_once("lib/michom.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php
$API = new MichomeAPI('192.168.1.42', $link);
$API->ResetScenesTimer();

$interval = 1; //Интервал выполнения в кроне

//$interval = $interval+0; //Прибавляем минуту(на всякий случай)
$todayH = date("H");//Получаем часы
$todayM = date("i");//Получаем минуты
$today = ($todayH*60)+$todayM;//Переводим в минуты

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
	$typeS = '0';
  
    if(IsStr($Name, "^bt")) { $enable = '0'; $typeS = '1';}

    $N = $API->GetIFs($Name, $enable);
    $Name = $N[0];
    $enable = $N[1];        
    
    if($enable == "0" & $typeS == '0') echo "Disable <br />";
	elseif($typeS == '1') echo "Button disable <br />";
    else{
        $CSEinterval = $row['Timeout']; //Интервал отправки данных
        
        $ST = (intval(explode(':', $row['TStart'])[0])*60) + intval(explode(':', $row['TStart'])[1]); //Получаем стартовое время
        $ET = (intval(explode(':', $row['TEnd'])[0])*60) + intval(explode(':', $row['TEnd'])[1]); //Получаем конечное время
        $CSE = (intval(explode(':', $row['CSE'])[0])*60) + intval(explode(':', $row['CSE'])[1]); //Получаем время последней отправки
        
        $data = $API->GetConstant($row['Data']);
        $ndata = $API->GetConstant($row['NData']);        
        //echo $data;

        if(strpos($Name, "^sds") !== FALSE) $ST = (intval(explode(':', date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[0])*60) + intval(explode(':', date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[1]);
        if(strpos($Name, "^sde") !== FALSE) $ST = (intval(explode(':', date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[0])*60) + intval(explode(':', date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[1]);       
        
        if(strpos($Name, "^eds") !== FALSE) $ET = (intval(explode(':', date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[0])*60) + intval(explode(':', date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[1]);
        if(strpos($Name, "^ede") !== FALSE) $ET = (intval(explode(':', date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[0])*60) + intval(explode(':', date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))[1]);                                        
        
        //echo $Name;            
        
        if(($today >= $ST & $today < $ET) & ($CSE+$CSEinterval < $today)){ //Если текущее время больше стартового и меньше конечного
            $API->GetNotification($data);
            if($API->SendCmd($row['Module'], $data.'&m=cron') != "Ошибка соеденения с модулем" || IsStr($Name, "^nos")){ //Отправляем команду
                mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$row['ID']);//Запоминаем время работы
            }
            echo "Send ON <br />";
        }
        elseif(!($today >= $ST & $today < $ET) & ($today >= $ST & $today < $ET+$interval)){ //Если текущее время больше стартового и меньше конечного + 5минут
            $API->GetNotification($ndata);
            if($API->SendCmd($row['Module'], $ndata.'&m=cron') != "Ошибка соеденения с модулем" || IsStr($Name, "^nos")){ //Отправляем команду
                mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$row['ID']);//Запоминаем время работы
            }
            echo "Send OFF <br />";
        }
        else{
            echo "No Send <br />";
        }
    }
}

/*if($today >= 6 & $today < 8){
    $API->SendCmd("192.168.1.14", 'setlight?s=1&m=cron');
}
else{
    $API->SendCmd("192.168.1.14", 'setlight?s=0&m=cron');
}

if($today >= 16 & $today < 21){
    $API->SendCmd("192.168.1.12", 'setlight?p=1&s=1&m=cron');
}
else{
    $API->SendCmd("192.168.1.12", 'setlight?p=1&s=0&m=cron');
}*/
?>
