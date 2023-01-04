<?php
 include_once("../../site/mysql.php");
?>
<?
header('Access-Control-Allow-Origin: *');

$num = 0;
$results = mysqli_query($link, "SELECT * FROM modules");

    while($row = $results->fetch_assoc()) {
	  if($row['ip'] != "" && $row['ip'] != "localhost" && $row['ip'] != "gateway"){
        $ips[] = $row['ip'];
		$ipsname[] = $row['mID'];
		$ipstype[] = $row['type'];
        $num = $num + 1;
	  }
    }

	/*foreach($ips as $tmp){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://'.$tmp.'/getnameandid');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT_MS, 300);
        curl_setopt ($ch, CURLOPT_TIMEOUT_MS, 300);
        $m = @curl_exec($ch);
        
		$call = explode("/n", $m);
		//var_dump($call);
		$ipsname[] = count($call) > 0 ? $call[0] : "";        
		$ipstype[] = count($call) > 1 ? $call[1] : "";
	}*/
	
$cart = array(
  "name" => "getdivece",
  "col" => $num,
  "ips" => $ips, 
  "devicename" => $ipsname,
  "devicetype" => $ipstype
);
echo json_encode( $cart );
?>
