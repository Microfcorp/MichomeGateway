<?
//ONLY MODULES

require_once(__DIR__."/../site/mysql.php");
require_once("lib/michom.php");

if(isset($_GET['ischeckconnect']) && ($_GET['ischeckconnect'] == "1" || $_POST['ischeckconnect'] == "1"))
	exit("OK");

$API = new MichomeAPI('localhost', $link);

$temperdht = "";
$hummdht = "";
$temperbmp = "";
$davlen = "";
$data = "";
$rsid = "";
$date = date("Y-m-d H:i:s");

if(isset($_GET['6']))
	$getjson = $_GET['6'];
elseif(isset($_POST['6']))
	$getjson = $_POST['6'];
else
	$getjson = file_get_contents('php://input');

var_dump($getjson);

//file_put_contents("t1.txt", $getjson);

$obj = json_decode($getjson);

if(empty($obj->{'ip'})){
	exit("None IP Address");
}

try{
    $ip = $obj->{'ip'};
    $type = $obj->{'type'};
    $secret = $obj->{'secretkey'};
    $sign = $obj->{'secret'};
    $mac = $obj->{'mac'};
    $firmware = $obj->{'firmware'};
}
catch (Exception $e){
    exit("Error");
}

if(sha1(substr($sign, 0, 13)) != $secret){    
    $API->AddLog($ip, 'LoginFailed', $rsid, 'Failed Password. '.sha1(substr($sign, 0, 13)), $date);
    $API->SendNotification("Была попытка неудачной авторизации модулем", "all");
	exit("Error");
}

//Получение rsid
if(isset($obj->{'rsid'})){
    $rsid = $obj->{'rsid'};
}
elseif(isset($obj->{'rssi'})){
    $rsid = $obj->{'rssi'};
}
else{
    $rsid = '';    
}

$data = "rsid=" . $rsid . ";";

$isMods = false;
foreach($MODs as $tmp){
	if($tmp->BaseClass->TypeModule == $type && $tmp->Type == MichomeModuleType::ModuleCore){
		$isMods = true;
		$vod = $tmp->BaseClass->POSTFunction;
		if($vod != NULL){
			$rt = $vod->call($API, array($tmp, $obj));
			if($rt == false){
				$API->AddLog($ip, $type, $rsid, "Text=None valid ".$type." data;", $date);
			}
			$data = $data . $rt->data;
			
			$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `dawlen`, `visota`, `date`) VALUES ('$ip', '$type', '$data', '".$rt->temp."', '".$rt->humm."', '".$rt->dawlen."', '".$rt->visota."', '$date')";		
			
			$result = mysqli_query($link, $guery);
		}
	}
}

if(!$isMods){
	if($type == "msinfoo"){	//Модуль сбора информации
		$temperdht = $obj->{'data'}->{'temper'}; //Температура DHT11
		$temp = $obj->{'data'}->{'temperbmp'}; //Температура BMP180
		
		//$temp = ($temperdht + $temperbmp) / 2;
		$humm = $obj->{'data'}->{'humm'};//Влажность			
		$davlen = $obj->{'data'}->{'davlen'};//Давление
		$visot = $obj->{'data'}->{'visot'};//Высота
		$data = $data . "temperdht=" . $temperdht . ";";
		
		if($temperdht != "nan"){
			$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `dawlen`, `visota`, `date`) VALUES ('$ip', 'msinfoo','$data','$temp','$humm','$davlen','$visot','$date')"; 
			$result = mysqli_query($link, $guery);
		}
		else{      
			$API->AddLog($ip, 'msinfoo', $rsid, 'Text=MsinfooNAN;', $date);

			$data1 = $API->GetPosledData('192.168.1.10')->Humm;

			$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `dawlen`, `visota`, `date`) VALUES ('$ip', 'msinfoo','$data','$temp','$data1','$davlen','$visot','$date')"; 
			$result = mysqli_query($link, $guery);
		}
	}
	elseif($type == "termometr"){	//Термометр
		$temper = $obj->{'data'}->{'temper'}; //Температура
		if(is_array($temper)){
			for($i = 0; $i < count($temper); $i++){
				$data = $data . "Temp".$i."=".$temper[$i].";";
			}
			$tempmain = $temper[0];
			$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `date`) VALUES ('$ip', 'termometr', '$data','$tempmain','$date')";
		}
		else{
			$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `date`) VALUES ('$ip', 'termometr', '$data','$temper','$date')"; 
		}
		$result = mysqli_query($link, $guery);
	}
	elseif($type == "meteostation"){//Метеостация
		$meteo = $obj->{'data'}->{'meteo'}; //Метео
		$tempmain = -1;
		$hummmain = -1;
		$dawlenmain = -1;
		for($i = 0; $i < count($meteo); $i++){		
			if(intval($meteo[$i][0]) > 1024 || intval($meteo[$i][1]) > 1024) continue;	
			$data = $data . "Temp".$i."=".$meteo[$i][0].((isset($meteo[$i][1]) && intval($meteo[$i][1]) != 0) ? (";Humm".$i."=".$meteo[$i][1]) : "").((isset($meteo[$i][2]) && intval($meteo[$i][2]) != 0) ? (";Dawlen".$i."=".$meteo[$i][2]) : "").";";

			if($tempmain == -1 && $hummmain == -1){
				$tempmain = $meteo[$i][0];
				$hummmain = $meteo[$i][1];
			}
			if($dawlenmain == -1 && isset($meteo[$i][2]) && intval($meteo[$i][2]) != 0){
				$dawlenmain = $meteo[$i][2];
			}
		}
		if($tempmain == -1 && $hummmain == -1){
			$API->AddLog($ip, $type, $rsid, "Text=None valid MeteoStation data;", $date);
		}
		else{
			$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `dawlen`, `date`) VALUES ('$ip', '$type', '$data', '$tempmain', '$hummmain', '$dawlenmain', '$date')";
			$result = mysqli_query($link, $guery);
		}	
	}
	elseif($type == "Informetr"){	//Информетр
		$type = $obj->{'data'}->{'data'};
		$message = "Text=Informetr: ".$type.";";
		
		$API->AddLog($ip, 'Informetr', $rsid, $message, $date);
		
		if($type == "GetData"){

		}
	}
	elseif($type == "hdc1080" || $type == "hdc1080mx"){ //HDC1080	
		$temper = $obj->{'data'}->{'temper'};
		$humm = $obj->{'data'}->{'humm'};

		$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `date`) VALUES ('$ip', 'hdc1080','$data','$temper','$humm','$date')"; 
		$result = mysqli_query($link, $guery);
	}
	elseif($type == "hdc1080andAlarm"){	//HDC1080 и сигнализация
		$temper = $obj->{'data'}->{'temper'};
		$humm = $obj->{'data'}->{'humm'};
		$status = $obj->{'data'}->{'butt'};

		$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `date`) VALUES ('$ip', 'hdc1080andAlarm','$status','$temper','$humm','$date')"; 
		$result = mysqli_query($link, $guery);
	}
	elseif($type == "get_light_status"){//Модуль света	
		$status = $obj->{'data'}->{'status'};

		$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `date`) VALUES ('$ip', 'get_light_status','$status','$date')"; 
		$result = mysqli_query($link, $guery);
	}
	elseif($type == "get_button_press"){//Событие нажатия кнопки
		$status = $obj->{'data'}->{'status'};
		$pin = explode('=',$status)[0];
		$count = explode('=',$status)[1];

		$results = mysqli_query($link, "SELECT * FROM `scenes` WHERE `Enable`=1 AND `Name` LIKE '%^bt%%".$ip."%'");//Жестко качаем все из БД
		while($row = $results->fetch_assoc()) {
			$na = $row['Name'];
			$na = str_replace("^cbp;", $count, $na);
			$na = str_replace("^pbp;", $pin, $na);
			
			$N = $API->GetIFs($API->GetButton($API->GetConstant($na), $ip, $pin, $count), $row['Enable'], $row['ID']);
			$Name = $N[0];
			if($N[1] != "0"){
				$data = $API->GetConstant($row['Data']);
				$data = str_replace("^cbp;", $count, $data);
				$data = str_replace("^pbp;", $pin, $data);
				
				$API->GetNotification($data);
				if($API->SendCmd($row['Module'], $data) != "Ошибка соеденения с модулем" || IsStr($Name, "^nos")){ //Отправляем команду
					mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$row['ID']);//Запоминаем время работы
				}
			}
		}
		
		//$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `dawlen`, `visota`, `date`) VALUES ('$ip', 'get_button_press','$status','','','','','$date')"; 
		//$result = mysqli_query($link, $guery);
	}
	elseif($type == "StudioLight"){	//Модуль освещения
		$status = $obj->{'data'}->{'status'};
		
		$API->AddLog($ip, 'StudioLight', $rsid, 'Text=OK;', $date);
	}
	elseif($type == "OLED"){	//Модуль освещения
		//$status = $obj->{'data'}->{'status'};
		
		//$API->AddLog($ip, 'StudioLight', $rsid, 'Text=OK;', $date);
	}
	elseif($type == "Log"){	//Лог
		$status = "Text=" . $obj->{'data'}->{'log'} . ";";
		
		$API->AddLog($ip, 'Log', $rsid, $status, $date);
	}
	elseif($type == "init"){ //Инициализация модуля
		$moduletype = $obj->{'data'}->{'type'};
		$moduleid = $obj->{'data'}->{'id'};

		$res = mysqli_query($link, "SELECT `id` FROM modules WHERE `mID` = \"".$moduleid."\" OR `mac`='$mac' limit 1");
		$count = mysqli_num_rows($res);
		if( $count > 0 ) {
			$bdid = $res->fetch_assoc()['id'];
			$res = mysqli_query($link, "UPDATE `michome`.`modules` SET `ip`='$ip', `type`='$moduletype', `mac`='$mac', `laststart`=CURTIME() WHERE `id`='$bdid';");
		} //Обновляем IP...
		else { //Добавляем в базу модулей
			$setting = $API->GetSettingsFromType($moduletype);
			$guery = "INSERT INTO `modules`(`mac`, `ip`, `type`, `mID`, `urls`, `setting`) VALUES ('$mac', '$ip','$moduletype','$moduleid','refresh=Обновить данные;restart=Перезагрузить;clearlogs=Отчистить логи;cleardatalogs=Отчистить логи данных', '$setting')";       
			$result = mysqli_query($link, $guery);
		}    
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://'.$ip.'/setsettings?s='.$API->GetSettingsFromName($moduleid));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
		$pr = curl_exec($ch);
		curl_close($ch);
		
		$API->AddLog($ip, 'StartingModule', $rsid, "Text=Module ".$moduleid." Starting;", $date);
	}
	else{//Произвольное событие
		$data = $data . $obj->{'data'};
		$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `date`) VALUES ('$ip', '$type','$data','$date')"; 
		$result = mysqli_query($link, $guery);
	}
}
echo $ip . "<br>";
echo $rsid. "<br>";
?>
