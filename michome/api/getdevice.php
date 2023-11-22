<?php
require_once("../../site/mysql.php");
require_once("../lib/michom.php");
header('Access-Control-Allow-Origin: *');
header("Michome-Page: API-Service");
header("Michome-API: GETDevice");

$API = new MichomeAPI('localhost', $link); 

//Метод API для получения данных обо всех модулях и информацию о них

//Структура запросов:
//type=alldevice - Тип получаемых данных. По умолчанию - alldevice. Структура: alldevice - список всех зарегистрированных устройств; deviceinfo - информация о конкретном модуле; typeinfo - информация о типе модуля
//device=192.168.1.11 ИЛИ device=termometr_okno - Устройство для deviceinfo. Поддержка и ip и имени устройства
//typemodule=msinfoo - Тип модуля для typeinfo

$viewType = "alldevice";
if(isset($_GET['type']))
	$viewType = $_GET['type'];

$JSONData = Array("name"=>"getdevice", "type"=>$viewType, "col"=>0); //Основная структура данных

$num = 0;
if($viewType == "alldevice"){
	$modules = $API->GetAllModulesBD();
	foreach($modules as $tmp){
		$JSONData["ips"][] = $tmp['ip'];
		$JSONData["devicename"][] = $tmp['mid'];
		$JSONData["devicetype"][] = $tmp['type'];
		$JSONData["devicemac"][] = $tmp['mac'];
		$JSONData["deviceid"][] = $tmp['id'];
		$JSONData["col"] = $JSONData["col"] + 1;
	}
}
elseif($viewType == "deviceinfo"){
	$device = (isset($_GET["device"]) ? $_GET["device"] : "");
	$module = $API->GetModule($device);
	
	if(!$module){
		$JSONData["error"] = "1";
	}
	else{
		$JSONData["error"] = "0";
		$JSONData["devicename"] = $module[0];
		$JSONData["devicetype"] = $module[1];
		$JSONData["devicerssi"] = $module[2];
		$JSONData["deviceip"] = $module[3];
		$JSONData["deviceflashsize"] = $module[4];
		$JSONData["deviceadc"] = $module[5];
		$JSONData["devicefreeheap"] = $module[6];
		$JSONData["devicefreesketchspace"] = $module[7];
		$JSONData["deviceresetreason"] = $module[8];
		$JSONData["devicefirversion"] = $module[9];
		$JSONData["devicemichomeversion"] = $module[10];
		$JSONData["devicedatecompil"] = $module[11];
		$JSONData["devicetimecompil"] = $module[12];
		//$JSONData["devicemichomelib"] = $module[13];
		$JSONData["devicefsmanager"] = $module[14];
		$JSONData["devicemd5hash"] = $module[15];
		$JSONData["col"] = count($module);
	}
}
elseif($viewType == "typeinfo"){
	$typemodule = (isset($_GET["typemodule"]) ? $_GET["typemodule"] : "");
	$module = $API->GetModuleInfoFromType($typemodule);

	if(!$module){
		$JSONData["error"] = "1";
	}
	else{
		$JSONData["error"] = "0";
		$JSONData["typename"] = $module->Type;
		$JSONData["typedesc"] = $module->Descreption;
		$JSONData["typeurl"] = $module->URL;
		$JSONData["typemurl"] = $module->MURL;
		$JSONData["col"] = 1;
	}
	
}

exit(json_encode($JSONData));
?>
