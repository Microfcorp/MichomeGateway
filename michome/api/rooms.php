<?php
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");
include_once("../lib/michom.php");
$API = new MichomeAPI('localhost', $link); 

$type = $_GET['type'];

if($type=="Edit"){
	$data = br2nl($_GET['data']);
	var_dump($data);
    $ID=intval(mysqli_real_escape_string($link, $_GET['id']));
    $Name=(mysqli_real_escape_string($link, $_GET['name']));
    $Data=(mysqli_real_escape_string($link, $data));
    $Modules=(mysqli_real_escape_string($link, $_GET['modules']));
    
    $API->EditRoom($ID, $Name, $Data, $Modules);
    echo "OK";
}
elseif($type=="Select"){
    /*$results = mysqli_query($link, "SELECT * FROM `scenes` WHERE 1");
    
    $data = array('name' => "selectscenes");
    $data['times']['start'] = date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3);
    $data['times']['end'] = date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3);
    
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
    echo json_encode($data);*/
}
elseif($type=="Add"){  
	$API->AddRoom();
}
elseif($type=="Remove"){
    $ID=intval(mysqli_real_escape_string($link, $_GET['id']));   
    
    $API->RemoveRoom($ID);
}
else{
    exit("Error");
}
?>
