<?
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");
include_once("../lib/michom.php");

$API = new MichomeAPI('localhost', $link); 
header("Michome-Page: API-Service");
header("Michome-API: GETData");

//Метод API для получения данных из базы данных с отбором по дням

//Структура запросов:
//device=192.168.1.11 ИЛИ device=termometr_okno - Устройтсво, по которому вести выборку. Поддержка и ip и имени устройства
//type=termometr - Тип данных, по которому вести выборку
//cmd=temp ИЛИ cmd=unique - Тип данных из базы данных ИЛИ список всех доступных типов для выборки
//view=text ИЛИ view=json - Тип представления данных (json по умолчанию), при тексте возвращается только последняя строка выборки
//date=2022-10-14 - За какой день вести выборку данных, если не указано, то за текущий
//filter=nonenull - Применяет фильтр к значениям (nonenull удаляет пустые элементы)

$num = 0;

$viewType = "json";
if(isset($_GET['view']))
	$viewType = $_GET['view'];

$selDate = date("Y-m-d");
if(isset($_GET['date']))
	$selDate = $_GET['date'];

if(!empty($_GET['device'])){ //Определение IP устройства
	$device = $_GET['device'];
}
else{
	$device = 1;
}

if(!empty($_GET['type'])){ //Определение типа данных
    $type = $_GET['type'];
}
else{
	$type = "1";
}
$cmd = $_GET['cmd']; //Комманда на получение типа данных
$JSONData = Array("name"=>"getdata", "type"=>$cmd, "col"=>0, "device"=>$device, "data"=>Array(), "date"=>Array()); //Основная структура данных

$reqs = $API->GetDataForDay($device, $selDate)->SortType($type);
if($cmd == "unique"){
	$filter = isset($_GET['filter']) ? $_GET['filter'] : "none";
	$JSONData['data'] = $reqs->GetTypes($filter);
}
else{
	$JSONData['data'] = $reqs->Select($cmd);
	$JSONData['date'] = $reqs->Select("date");
}
$JSONData['col'] = count($JSONData['data']);

if($viewType == "text")
	exit($JSONData['data'][array_key_last($JSONData['data'])]);
else
	exit(json_encode($JSONData));

/*$data[] = "";
$date[] = "";

if($cmd == "temper"){//Температура
	if(isset($_GET['date'])){
		$dates = $_GET['date'];
		$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND ".$type." AND ".$device);
	}
	else{
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device);
	}

while($row = $results->fetch_assoc()) {
    $data[] = $row['temp'];
	$date[] = $row['date'];
	$num = $num + 1;
	}
		$cart = array(
  "name" => "getdata",
  "type" => $cmd,
  "col" => $num,
  "device" => $device,
  "data" => $data,
  "date" => $date
);
echo json_encode( $cart );

}
elseif($cmd == "textultemp"){//Текст уличной температуры
	$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device." ORDER BY `id` DESC LIMIT 1");
    
    while($row = $results->fetch_assoc()) {
        $data = $row['temp'];	
    }
    echo $data;
}
elseif($cmd == "texthumm"){//Текст влажности
	$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device." ORDER BY `id` DESC LIMIT 1");

    while($row = $results->fetch_assoc()) {
        $data = $row['humm'];	
    }
    echo $data;
}
elseif($cmd == "textdawlen"){//Текст давления
	$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device." ORDER BY `id` DESC LIMIT 1");

    while($row = $results->fetch_assoc()) {
        $data[] = $row['dawlen'];	
    }
    echo $data;
}
elseif($cmd == "humm"){//Влажность
	if(isset($_GET['date'])){
		$dates = $_GET['date'];
		$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND ".$type." AND ".$device);
	}
	else{
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device);
	}

while($row = $results->fetch_assoc()) {
    $data[] = $row['humm'];
	$date[] = $row['date'];
	$num = $num + 1;
}
    $cart = array(
      "name" => "getdata",
      "type" => $cmd,
      "col" => $num,
      "device" => $device,
      "data" => $data,
      "date" => $date
    );
echo json_encode( $cart );
}
//delet...
elseif($cmd == "tempertemp"){
	if(isset($_GET['date'])){
		$dates = $_GET['date'];
		$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND ".$type." AND ".$device);
	}
		else{
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device);
	}

while($row = $results->fetch_assoc()) {
    $data[] = $row['temp'];
	$date[] = $row['date'];
	$num = $num + 1;
	}
		$cart = array(
  "name" => "getdata",
  "type" => $cmd,
  "col" => $num,
  "device" => $device,
  "data" => $data,
  "date" => $date
);
echo json_encode( $cart );
}
///delet...///
elseif($cmd == "dawlen"){//Давление
	if(isset($_GET['date'])){
		$dates = $_GET['date'];
		$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND ".$type." AND ".$device);
	}
	else{
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device);
	}

while($row = $results->fetch_assoc()) {
    $data[] = $row['dawlen'];
	$date[] = $row['date'];
	$num = $num + 1;
}
    $cart = array(
      "name" => "getdata",
      "type" => $cmd,
      "col" => $num,
      "device" => $device,
      "data" => $data,
      "date" => $date
    );
echo json_encode( $cart );
}
elseif($cmd == "posledob"){	//Последнее обновление
	$results = mysqli_query($link, "SELECT * FROM michom WHERE ".$type." AND ".$device);

    while($row = $results->fetch_assoc()) {
        $date = $row['date'];
    }
	
echo $date;
}
elseif($cmd == "cursvet"){//Текущее состояние света
	if(isset($_GET['date'])){
		$dates = $_GET['date'];
		$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND type = \"get_light_status\" AND ".$device);
	}
	else{
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE type = \"get_light_status\" AND ".$device);
	}

    while($row = $results->fetch_assoc()) {
        $data[] = $row['data'];
        $date[] = $row['date'];
        $num = $num + 1;
	}
	
    $cart = array(
      "name" => "getdata",
      "type" => $cmd,
      "col" => $num,
      "device" => $device,
      "data" => $data,
      "date" => $date
    );
echo json_encode( $cart );
}
elseif($cmd == "sobit"){//События
	if(isset($_GET['date'])){
		$dates = $_GET['date'];
		$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND ".$type." AND ".$device);
	}
		else{
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device);
	}

while($row = $results->fetch_assoc()) {
        $typedata[] = $row['type'];
        $data[] = $row['data'];
        $date[] = $row['date'];
        $num = $num + 1;
}
    $cart = array(
      "name" => "getdata",
      "type" => $cmd,
      "col" => $num,
      "device" => $device,
      "typedata" => $typedata,
      "data" => $data,
      "date" => $date
    );
echo json_encode( $cart );
}
elseif($cmd == "visota"){//Высота
	if(isset($_GET['date'])){
		$dates = $_GET['date'];
		$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND ".$type." AND ".$device);
	}
		else{
		$results = mysqli_query($link, "SELECT * FROM `michom` WHERE ".$type." AND ".$device);
	}

while($row = $results->fetch_assoc()) {
        $data[] = $row['visota'];
        $date[] = $row['date'];
        $num = $num + 1;
}
    $cart = array(
      "name" => "getdata",
      "type" => $cmd,
      "col" => $num,
      "device" => $device,
      "data" => $data,
      "date" => $date
    );
echo json_encode( $cart );
}
*/
?>
