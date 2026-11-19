<?
//ONLY MODULES

require_once(__DIR__."/../../../site/mysql.php");
require_once(__DIR__."/../../lib/michom.php");

if(isset($_GET['ischeckconnect']) && ($_GET['ischeckconnect'] == "1" || $_POST['ischeckconnect'] == "1"))
	exit("OK");

$API = new MichomeAPI('localhost', $link);

$data = "";
$rsid = "";
$date = date("Y-m-d H:i:s");
$isArchive = "-1";

$JSONData = Array("name"=>"getpost", "type"=>"", "serverDate"=>time(), "device"=>"", "data"=>Array(), "error"=>"none", "errorCode"=>"0"); //Основная структура данных

if(isset($_GET['6']))
	$getjson = $_GET['6'];
elseif(isset($_POST['6']))
	$getjson = $_POST['6'];
else
	$getjson = file_get_contents('php://input');

//file_put_contents("t1.txt", $getjson);

$obj = json_decode($getjson);
try{
	if(empty($obj->{'ip'})){
		$JSONData["error"] = "None IP Address";
		$JSONData["errorCode"] = 1;
		exit(json_encode($JSONData));
	}
    $ip = $obj->{'ip'};
    $type = $obj->{'type'};
    $secret = isset($obj->{'secretkey'}) ? $obj->{'secretkey'} : "";
    $sign = isset($obj->{'secret'}) ? $obj->{'secret'} : "";
    $mac = $obj->{'mac'};
    $firmware = $obj->{'firmware'};
	
	$JSONData["type"] = $type;
	$JSONData["device"] = $ip;
	
	if($ip == "192.168.1.13"){
		$JSONData["error"] = "Fuck YOU";
		$JSONData["errorCode"] = 228;
		exit(json_encode($JSONData));
	}
}
catch (Exception $e){
    $JSONData["error"] = "Unexepted error";
	$JSONData["errorCode"] = -1;
	exit(json_encode($JSONData));
}

$ModuleSerial = isset($_SERVER['HTTP_MODULE_SERIAL']) ? $_SERVER['HTTP_MODULE_SERIAL'] : false;
$ModuleMAC = isset($_SERVER['HTTP_MODULE_MAC']) ? $_SERVER['HTTP_MODULE_MAC'] : false;
$ModuleType = isset($_SERVER['HTTP_MODULE_TYPE']) ? $_SERVER['HTTP_MODULE_TYPE'] : false;
$ModuleIP = isset($_SERVER['HTTP_MODULE_IP']) ? $_SERVER['HTTP_MODULE_IP'] : false;
$ModuleRoom = isset($_SERVER['HTTP_MODULE_ROOM']) ? $_SERVER['HTTP_MODULE_ROOM'] : false;
$ModuleNetwork = isset($_SERVER['HTTP_MODULE_NETWORK']) ? $_SERVER['HTTP_MODULE_NETWORK'] : false;
$ModuleTime = isset($_SERVER['HTTP_MODULE_TIME']) ? intval($_SERVER['HTTP_MODULE_TIME']) : false;
$ModuleID = isset($_SERVER['HTTP_MODULE_ID']) ? $_SERVER['HTTP_MODULE_ID'] : false;

/*if(sha1(substr($sign, 0, 13)) != $secret){    
    $API->AddLog($ModuleID, 'LoginFailed', $rsid, 'Failed Password. '.sha1(substr($sign, 0, 13)), $date);
    $API->SendNotification("Была попытка неудачной авторизации модулем", "all");
	exit("Error");
}*/

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

if($ModuleMAC != false && $ModuleIP != false){ //Для проверки валидности по маку и айпи
	if($ModuleMAC != $mac || $ModuleIP != $ip){
		$API->AddLog($ModuleID, 'LoginFailed', $rsid, 'MAC or IP none equal on header and json', $date);
	}
}

if($ModuleNetwork != false && $ModuleNetwork != ""){ //Для отсекания устройств из чужих сетей
	$cNetwork = $API->GetSettingORCreate("mNetwork", "LeHome", "Название сети Michome")->Value;
	if($ModuleNetwork != $cNetwork){
		$API->AddLog($ModuleID, 'NetworkFailed', $rsid, 'This module is network: '.$ModuleNetwork.". Current network: ".$cNetwork, $date);
		$JSONData["error"] = "Network not found";
		$JSONData["errorCode"] = 2;
		exit(json_encode($JSONData));
	}
}

if($ModuleTime != false){ //Для проверки архивности данных
	if($ModuleTime > 1719475970){ //Значение адеквотно и модуль точно скачал свое время
		if(time() - $ModuleTime >= 90){ //Значение уходит в архив больше, чем на полторы минутф
			//Меняем временную метку этих данных на архивную с модуля
			$date = date("Y-m-d H:i:s", $ModuleTime);
			$isArchive = "1";
		}
	}
}

//Защита от частых запросов
$refCount = 0;
$res = mysqli_query($link, "SELECT  COUNT(`id`) AS `cnt` FROM `michome`.`michom` WHERE `ip`='$ip' AND `type`='$type' AND `date` >= NOW() - INTERVAL 2 SECOND ORDER BY `id` DESC");
while($row = $res->fetch_assoc()) {
	$refCount = $row['cnt'];
}

if($refCount >= intval($API->standartCountRequests)){
	$JSONData['errorCode'] = 6;
	$JSONData['error'] = "This module is many requests";
	exit(json_encode($JSONData));
}

$data = "rsid=" . $rsid . ";isArchive=" . $isArchive . ";";

if($type == "BolidMaster"){//BolidMaster
	$dev = $obj->{'data'}->{'c2000'}; //Приборы на линии
	for($i = 0; $i < count($dev); $i++){		
		$devAdr = $dev[$i]->{'deviceAddress'};
		for($a = 0; $a < count($dev[$i]->{'zones'}); $a++){
			$zone = $dev[$i]->{'zones'}[$a];
			$zoneID = $zone->{'zone_id'};
			$status = $zone->{'status'};
			$status_text = $zone->{'status_text'};
			$adc = $zone->{'adc'};
			
			//Name=Val;Name=Val;Name=Val
			$data = $data . "status".$devAdr.$zoneID."=".$status.";adc".$devAdr.$zoneID."=".$adc.";";
		}
	}
	$guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `date`) VALUES ('$ip', '$type', '$data', '$date')";
	$result = mysqli_query($link, $guery);
	
	$devTr = $obj->{'c2000_trigger'}; //Измененные шлейфы на линии
	$results = mysqli_query($link, "SELECT * FROM `scenes` WHERE `Enable`=1 AND `Name` LIKE '%^bl%%".$ip."%'");//Жестко качаем все, где есть кнопка из БД
	while($row = $results->fetch_assoc()) {
		for($i = 0; $i < count($devTr); $i++){
			$devAdr = $devTr[$i]->{'deviceAddress'};
			for($a = 0; $a < count($devTr[$i]->{'zones'}); $a++){
				$zone = $devTr[$i]->{'zones'}[$a];
				$zoneID = $zone->{'zone_id'};
				$status = $zone->{'status'};
				$status_text = $zone->{'status_text'};
				$adc = $zone->{'adc'};
				
				$na = $row['Name'];
				$na = str_replace("^bld;", $devAdr, $na);
				$na = str_replace("^blz;", $zoneID, $na);
				$na = str_replace("^bls;", $status, $na);
				$na = str_replace("^bla;", $adc, $na);
				$na = str_replace("^blst;", $status_text, $na);
				
				$N = $API->GetIFs(GetButtonBL($API->GetConstant($na), $ip, $devAdr, $zoneID), $row['Enable'], $row['ID']);
				$Name = $N[0];
				if($N[1] != "0"){ //Если выполнены все условия
					$data_alt = $row['Data'];
					$data_alt = str_replace("^bld;", $devAdr, $data_alt);
					$data_alt = str_replace("^blz;", $zoneID, $data_alt);
					$data_alt = str_replace("^bls;", $status, $data_alt);
					$data_alt = str_replace("^bla;", $adc, $data_alt);
					$data_alt = str_replace("^blst;", $status_text, $data_alt);
					
					$data_alt = $API->GetConstant($data_alt);				
					
					$API->GetNotification($data_alt);
					if(($row['Module'] != "" && $API->SendCmd($row['Module'], $data_alt.'&m=cron', true) === TRUE) || (IsStr($Name, "^nos;") || $row['Module'] == "")){ //Отправляем команду
						mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$row['ID']);//Запоминаем время работы
					}
				}
			}
		}
	}
	
	$devTr = $obj->{'c2000_adctrigger'}; //Измененные ADC на линии
	$results = mysqli_query($link, "SELECT * FROM `scenes` WHERE `Enable`=1 AND `Name` LIKE '%^ba%%".$ip."%'");//Жестко качаем все, где есть кнопка из БД
	while($row = $results->fetch_assoc()) {
		for($i = 0; $i < count($devTr); $i++){
			$devAdr = $devTr[$i]->{'deviceAddress'};
			for($a = 0; $a < count($devTr[$i]->{'zones'}); $a++){
				$zone = $devTr[$i]->{'zones'}[$a];
				$zoneID = $zone->{'zone_id'};
				$status = $zone->{'status'};
				$status_text = $zone->{'status_text'};
				$adc = $zone->{'adc'};
				$adcDiff = $zone->{'adc_diff'};
				
				$na = $row['Name'];
				$na = str_replace("^bld;", $devAdr, $na);
				$na = str_replace("^blz;", $zoneID, $na);
				$na = str_replace("^bls;", $status, $na);
				$na = str_replace("^bla;", $adc, $na);
				$na = str_replace("^blst;", $status_text, $na);
				$na = str_replace("^blad;", $adcDiff, $na);
				
				$N = $API->GetIFs(GetButtonBL($API->GetConstant($na), $ip, $devAdr, $zoneID), $row['Enable'], $row['ID']);
				$Name = $N[0];
				if($N[1] != "0"){ //Если выполнены все условия
					$data_alt = $row['Data'];
					$data_alt = str_replace("^bld;", $devAdr, $data_alt);
					$data_alt = str_replace("^blz;", $zoneID, $data_alt);
					$data_alt = str_replace("^bls;", $status, $data_alt);
					$data_alt = str_replace("^bla;", $adc, $data_alt);
					$data_alt = str_replace("^blst;", $status_text, $data_alt);
					$data_alt = str_replace("^blad;", $adcDiff, $data_alt);
					
					$data_alt = $API->GetConstant($data_alt);				
					
					$API->GetNotification($data_alt);
					if(($row['Module'] != "" && $API->SendCmd($row['Module'], $data_alt.'&m=cron', true) === TRUE) || (IsStr($Name, "^nos;") || $row['Module'] == "")){ //Отправляем команду
						mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`=".$row['ID']);//Запоминаем время работы
					}
				}
			}
		}
	}
}

function GetButtonBL($strl, $m, $d, $z){
	$str = $strl;
	//^bl_192.168.1.53_1_2;
	while(IsStr($str, "^bl")){
		$expl = substr($str, stripos($str, "^bl")+4, (stripos($str, ";", stripos($str, "^bl")) - (stripos($str, "^bl")+4)));
				   
		$md = explode('_', $expl)[0];                                   
				   
		$dv = explode('_', $expl)[1];
		$zn = explode('_', $expl)[2];           
		if($md == $m && $dv == $d && $zn == $z) $rd = '1';
		else $rd = '0';                   
		$rd = "^if_".$rd."==1;";       
		
		$str = str_ireplace("^bl_".$expl.";", $rd, $str);      
	}
	//^ba_192.168.1.53_1_2;
	while(IsStr($str, "^ba")){
		$expl = substr($str, stripos($str, "^ba")+4, (stripos($str, ";", stripos($str, "^ba")) - (stripos($str, "^ba")+4)));
				   
		$md = explode('_', $expl)[0];                                   
				   
		$dv = explode('_', $expl)[1];
		$zn = explode('_', $expl)[2];           
		if($md == $m && $dv == $d && $zn == $z) $rd = '1';
		else $rd = '0';                   
		$rd = "^if_".$rd."==1;";       
		
		$str = str_ireplace("^ba_".$expl.";", $rd, $str);      
	}
	return $str;
}
exit(json_encode($JSONData));
?>
