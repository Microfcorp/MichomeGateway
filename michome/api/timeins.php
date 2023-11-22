<?
header('Access-Control-Allow-Origin: *');
require_once("../../site/mysql.php");
require_once("../lib/michom.php");

$API = new MichomeAPI('localhost', $link); 
header("Michome-Page: API-Service");
header("Michome-API: TimeIns");


$device = (isset($_GET['device'])) ? $_GET['device'] : 'localhost';
$type = (isset($_GET['type'])) ? $_GET['type'] : 'curday';

if($type == "oneday"){
	$id = 0;
	$id1 = 0;
	
	//SELECT * FROM michom WHERE `date` >= CURDATE() AND `ip` = '192.168.1.11' ORDER BY id LIMIT 1 
	$results = mysqli_query($link, "SELECT * FROM michom WHERE `date` >= CURDATE() AND ".$device." ORDER BY id LIMIT 1");

	while($row = $results->fetch_assoc()) {
    $id = $row['id'];
	}
	
	$results = mysqli_query($link, "SELECT * FROM michom WHERE `id` >= ".$id." AND ".$device."");

	while($row = $results->fetch_assoc()) {
    $id1 = $row['id'];
	}
	
	echo $id1 - $id;
}
elseif($type == "selday"){
	//SELECT * FROM michom WHERE `date` >= "2018-08-06 00:00:00" AND `date` <= "2018-08-07 00:00:00"
	
	$date1 = $_GET['date'];
    
	$Timeins = $API->TimeIns($device, 'selday', $date1);
	
	exit($Timeins);
}
elseif($type == "curday"){  
	$Timeins = $API->TimeIns($device, 'selday', date("Y-m-d"));
	
	exit($Timeins);
}
?>
