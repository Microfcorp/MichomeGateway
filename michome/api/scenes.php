<?php
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");
include_once("../lib/michom.php");

$API = new MichomeAPI('localhost', $link); 
header("Michome-Page: API-Service");
header("Michome-API: Scenes");

$type = isset($_GET['type']) ? $_GET['type'] : "Select";

if($type=="Edit"){
    $ID=intval(mysqli_real_escape_string($link, $_GET['id']));
    $Name=(mysqli_real_escape_string($link, $_GET['name']));
    $TStart=(mysqli_real_escape_string($link, $_GET['ts']));
    $TEnd=(mysqli_real_escape_string($link, $_GET['td']));
    $Module=(mysqli_real_escape_string($link, $_GET['module']));
    $Data=(mysqli_real_escape_string($link, $_GET['data']));
    $NData=(mysqli_real_escape_string($link, $_GET['ndata']));    
    $Number=intval(mysqli_real_escape_string($link, $_GET['number']));
    $Timeout=intval(mysqli_real_escape_string($link, $_GET['timeout']));
    $Enable=(mysqli_real_escape_string($link, $_GET['enable']=="true"?"1":"0"));
    
    $results = mysqli_query($link, "UPDATE `scenes` SET `ID`='$Number', `Name`='$Name',`TStart`='$TStart',`TEnd`='$TEnd',`Module`='$Module',`Data`='$Data',`NData`='$NData',`Timeout`='$Timeout',`Enable`='$Enable' WHERE `ID`='$ID'");
    
    exit("OK");
}
elseif($type=="Select"){
    $results = mysqli_query($link, "SELECT * FROM `scenes` WHERE 1");
    
    $data = array('name' => "selectscenes");
	
	$sun_info = date_sun_info(time(), floatval($API->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value), floatval($API->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value));

    $data['times']['start'] = date("H:i", $sun_info['sunrise']);
    $data['times']['end'] = date("H:i", $sun_info['sunset']);
    
    while($row = $results->fetch_assoc()) {
        $data['data'][] = array(
                        'ID' => $row['ID'],
                        'Name' => $row['Name'],
                        'TStart' => $row['TStart'],
                        'TEnd' => $row['TEnd'],
                        'Module' => $row['Module'],
                        'Data' => $row['Data'],
                        'NData' => $row['NData'],        
                        'CSE' => $row['CSE'],        
                        'Timeout' => $row['Timeout'],        
                        'Enable' => $row['Enable'],        
        );
    }
    exit(json_encode($data));
}
elseif($type=="Add"){  
    $results = mysqli_query($link, "INSERT INTO `scenes`(`Name`, `TStart`, `TEnd`, `Module`, `Data`, `NData`, `CSE`) VALUES ('','6:00','10:00','','','','00:00')");
	exit("OK");
}
elseif($type=="Remove"){
    $ID=intval(mysqli_real_escape_string($link, $_GET['id']));   
    
    $results = mysqli_query($link, "DELETE FROM `scenes` WHERE `ID`='$ID'");
    mysqli_query($link, "ALTER TABLE `scenes` auto_increment = 1");
	exit("OK");
}
elseif($type=="Run"){
	$typeRun = isset($_GET['typerun']) ? $_GET['typerun'] : "0";
    $ID=intval(mysqli_real_escape_string($link, $_GET['id']));   
    $results = mysqli_query($link, "SELECT * FROM `scenes` WHERE `ID`='$ID'");	
	
	while($row = $results->fetch_assoc()) {
		$data = $typeRun == '0' ? $row['Data'] : $row['NData'];
		$module = $row['Module'];    
    }
	$strn = $API->GetConstant($data);
	$strn = $API->GetNotification($strn);
	$rt = "";
	if($module != "")
		$rt = $API->SendCmd($module, $strn.'&m=cron');
	exit($rt);
}
else{
    exit("Error");
}
?>
