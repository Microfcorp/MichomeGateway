<?php

function _GetRooms($link){
	$results = mysqli_query($link, "SELECT * FROM rooms WHERE 1");
	$rt = [];
    while($row = $results->fetch_assoc()) {
        $rt[] = ["ID" => $row['ID'], "Name" => $row['Name'], "Data" => $row['Data'], "Modules" => $row['Modules']];
    }
	return $rt;
}

function _AddRoom($link){
	$results = mysqli_query($link, "INSERT INTO `rooms`(`Data`) VALUES ('')");
}

function _RemoveRoom($link, $id){
	$results = mysqli_query($link, "DELETE FROM `rooms` WHERE `ID`='$id'");
    mysqli_query($link, "ALTER TABLE `rooms` auto_increment = 1");
}

function _EditRoom($link, $id, $name, $data, $modules){
	$results = mysqli_query($link, "UPDATE `rooms` SET `Name`='$name', `Data`='$data', `Modules`='$modules' WHERE `ID`='$id';");
}
?>