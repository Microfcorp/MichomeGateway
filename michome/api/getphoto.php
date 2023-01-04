<?php include_once("../../site/mysql.php"); ?>
<?php
//p-11-13-2018 18-40	
$dater = $_GET['date'];
$dateq = new DateTime($dater);
$date = $dateq->format('m-d-Y');
//echo $date;

$datefile = substr(explode(' ', $date)[0], 2);

$sc = scandir("../site/image/graphical/");

foreach($sc as $tmp){
	if($date == substr(explode(' ', $tmp)[0], 2)){
		exit($tmp);
	}
}

?>
