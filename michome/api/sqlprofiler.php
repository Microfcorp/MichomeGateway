<?php
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");
set_time_limit(60);

$id = 1;
$results = mysqli_query($link, "SELECT * FROM `michom` WHERE 1");
while($row = $results->fetch_assoc()) {
    mysqli_query($link, "UPDATE `michom` SET `id`='$id' WHERE `Id`=".$row['id']);
    $id = $id + 1;
}
exit("OK ".$id);
?>
