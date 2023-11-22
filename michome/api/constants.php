<?php
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");
include_once("../lib/michom.php");

$API = new MichomeAPI('localhost', $link); 
header("Michome-Page: API-Service");
header("Michome-API: Constants");

//Метод API для работы с константами

//Структура запросов:
//type=validate - Тип обработчика констант (run, validate)
//cmd=temp - Строка константы для обработки
//view=text ИЛИ view=json - Тип представления данных (json по умолчанию) или текст

$viewType = "json";
if(isset($_GET['view']))
	$viewType = $_GET['view'];

$type = "validate";
if(isset($_GET['type']))
	$type = $_GET['type'];

$cmd = "";
if(isset($_GET['cmd']))
	$cmd = $_GET['cmd'];

$JSONData = Array("name"=>"constant", "type"=>$type, "cmd"=>$cmd); //Основная структура данных

if($type == "validate"){
	$isValid = ValidateConst($cmd);
	if($viewType == "text")
		exit($isValid ? "1" : "0");
	
	$JSONData["isvalid"] = $isValid;
	exit(json_encode($JSONData));
}
elseif($type == "run"){
	$isValid = ValidateConst($cmd);
	$strn = $API->GetConstant($cmd);
	if($viewType == "text")
		exit($isValid ? $strn : ("Ошибка преобразования констант: ".$cmd));
	
	$JSONData["isvalid"] = $isValid;
	$JSONData["str"] = $strn;
	exit(json_encode($JSONData));
}

?>