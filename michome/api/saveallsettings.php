<?php include_once("../../site/mysql.php"); ?>
<?php
header('Access-Control-Allow-Origin: *');

$ip = $_GET['device'];
$data = $_GET['d'];

$ip = mysqli_real_escape_string($link, $ip);
$data = mysqli_real_escape_string($link, $data);

$results = mysqli_query($link, "UPDATE `modules` SET `setting`='$data' WHERE ip = '$ip'");

if($results){
    exit("OK");
}
?>
