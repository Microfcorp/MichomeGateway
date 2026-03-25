<?php
require_once('class.ponvif.php');

function IsStr($str, $search){
	if(stripos($str, $search) !== FALSE) return true;
	else return false;
}

$ip = $_GET['ip'];
$login = $_GET['login'];
$pass = $_GET['password'];

$onvif = new Ponvif();
$onvif->setUsername($login);
$onvif->setPassword($pass);
$onvif->setIPAddress($ip);

try{
	$onvif->initialize();

	$sources = $onvif->getSources();
	$profileTokens = $sources[0];
	unset($profileTokens['sourcetoken']);
	echo "<table style='background-color: #00000094;'>";
	echo "<tr style='background-color: darkgray;'>";
	for($i = 0; $i < count($profileTokens); $i++){
		echo "<td>Поток ".$i.":</td>";
	}
	echo "</tr>";
	echo "<tr>";
	for($i = 0; $i < count($profileTokens); $i++){
		$profileToken = $profileTokens[$i]['profiletoken'];
		$mediaUri = $onvif->media_GetSnapshotUri($profileToken);
		$mediaUri = str_replace("http://", "", $mediaUri);
		$mediaUri = str_replace("https://", "", $mediaUri);
		$mediaUri = str_replace("?", "%3F", $mediaUri);
		$mediaUri = str_replace("&", "%26", $mediaUri);
		echo "<td><img width='480px' height='320px' src='/michome/modules/ipcamera/foscamproxy.php?cmd=".$mediaUri."&login=".$login."&password=".$pass."' /></td>";
	}
	echo "</tr>";
	echo "</table>";
	
	//echo $mediaUri;
}
catch(Exception $e)
{
	if(IsStr($e, "Communication error")){
		echo "Ошибка соединения с " . $ip;
	}
}	
?>