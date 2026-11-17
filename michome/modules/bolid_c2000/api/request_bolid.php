<?php include_once(__DIR__."//../../../../site/mysql.php"); ?>
<?php include_once(__DIR__."//../../../lib/michom.php"); ?>
<?php
header('Access-Control-Allow-Origin: *');

$API = new MichomeAPI('localhost', $link); 
header("Michome-Page: API-Service");
header("Michome-API: GETData");

$deviceID = intval($_GET['device']);

$gateways = $MODs["BolidC2000"]->GetSettingORCreate($API, 'EthernetAddress', '192.168.0.0', '')->Value;
$gateways_key = "";
foreach(explode(',', $gateways) as $tmp){
	$gateways_key = $gateways_key . "-g ".$tmp ." ";
}

$binPath = ServerPath . "/modules/bolid_c2000/bolid_reader";

$bolid_resp = shell_exec($binPath . " " . $gateways_key . " -d $deviceID -j");

echo($bolid_resp);
?>