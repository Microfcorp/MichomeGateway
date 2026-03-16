<?php
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");
include_once("../lib/michom.php");

$API = new MichomeAPI('localhost', $link); 
header("Michome-Page: API-Service");
header("Michome-API: SendNotification");

//Метод API для отправки уведомления

//Структура запросов:
//id=123456 - (Необязательный) Пользователь, которому отправить сообщение
//group=all - (Необязательный, по умолчанию all) Группа, которой отправить сообщение
//text=Helloy, World - Сообщение, которое отправить (Сначала идет POST, а потом GET)
//enableconstant=1 - (Необязательный) Включить использование констант

$id = isset($_GET["id"]) ? $_GET["id"] : "";
$group = isset($_GET["group"]) ? $_GET["group"] : "all";
$text = isset($_POST["text"]) ? $_POST["text"] : (isset($_GET["text"]) ? $_GET["text"] : "");

if(isset($_GET["enableconstant"]) && $_GET["enableconstant"] == '1')
	$text = $API->GetConstant($text);

$API->SendNotification($text, ($id == "" ? $group : $id));

$JSONData = Array("name"=>"sendnotification", "group"=>$group, "id"=>$id, "text"=>$text); //Основная структура данных
exit(json_encode($JSONData));
?>