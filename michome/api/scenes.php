<?php
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");

$type = $_GET['type'];

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
    
    echo "OK";
}
elseif($type=="Select"){
    $results = mysqli_query($link, "SELECT * FROM `scenes` WHERE 1");
    
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
    echo json_encode($data);
}
elseif($type=="Add"){  
    $results = mysqli_query($link, "INSERT INTO `scenes`(`Name`, `TStart`, `TEnd`, `Module`, `Data`, `NData`, `CSE`) VALUES ('','6:00','10:00','','','','00:00')");
}
elseif($type=="Remove"){
    $ID=intval(mysqli_real_escape_string($link, $_GET['id']));   
    
    $results = mysqli_query($link, "DELETE FROM `scenes` WHERE `ID`='$ID'");
    mysqli_query($link, "ALTER TABLE `scenes` auto_increment = 1");
}
else{
    exit("Error");
}
?>
