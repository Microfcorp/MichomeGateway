<?
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");

$temperbater = exec("sudo python3 /etc/gettermist.py");
$date = date("Y-m-d H:i:s");
    
    $guery = "INSERT INTO `michom`(`ip`, `type`, `data`, `temp`, `humm`, `dawlen`, `visota`, `date`) VALUES ('localhost', 'temperbatarey','','$temperbater','','','','$date')"; 
	$result = mysqli_query($link, $guery);
    echo($result);
?>
