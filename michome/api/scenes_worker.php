<?php
set_time_limit(40); //Время выполения скрипта 40 секунд

include_once(__DIR__."/../../site/mysql.php");
include_once(__DIR__."/../lib/michom.php");

$API = new MichomeAPI('localhost', $link); 

$sceneID = -1;
$sceneType = -1;

if (PHP_SAPI === 'cli') {
    if((isset($argc) && $argc > 2)){
		$sceneID = intval($argv[1]);
		$sceneType = intval($argv[2]);
	}
}

// Проверка на PHP-FPM
if (PHP_SAPI === 'fpm-fcgi' || PHP_SAPI === 'fpm-cgi') {
    header("Michome-Page: API-Service");
	header("Michome-API: Scenes_Worker");
	$sceneID = intval($_GET['id']);
	$sceneID = intval($_GET['type']);
}

if($sceneID < 0)
	exit("SceneID is none set");
if($sceneType < 0)
	exit("SceneType is none set");

if($sceneType == 1){
	$literaMode = "ON";
	$results = mysqli_query($link, "SELECT `Data` AS `Code`, `Timeout`, `Module`, `Name` FROM `scenes` WHERE `ID`='$sceneID'"); //Жестко качаем все из БД
}
else{
	$literaMode = "OFF";
	$results = mysqli_query($link, "SELECT `NData` AS `Code`, `Timeout`, `Module`, `Name` FROM `scenes` WHERE `ID`='$sceneID'");
}

while($row = $results->fetch_assoc()) {
	$Code = $row['Code'];
	$Timeout = $row['Timeout'];
	$Module = $row['Module'];
	$Name = $row['Name'];
	
	$data = str_replace("^cse;", $Timeout, $Code);
	$data = $API->GetConstant($data); //Преобразуем константы
	$data = $API->GetNotification($data); //Отправляем все уведомления
	if(($Module != "" && $API->SendCmd($Module, $data.'&m=cron', true) === TRUE) || (IsStr($Name, "^nos;") || $Module == "")){ //Отправляем команду и проверяем удачность
		mysqli_query($link, "UPDATE `scenes` SET `CSE`='".date("H:i")."' WHERE `ID`='$sceneID'"); //Записывает время последней отправки
		echo "Send $literaMode Success";
	}
	else{
		echo "Send $literaMode Error";
	}
}
?>