<?
require_once(__DIR__."/../../site/mysql.php");
header('Access-Control-Allow-Origin: *');
function is_valid_ip($ip) {
    $ipv4 = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
    $ipv6 = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';
    return preg_match("/^($ipv4|$ipv6)\$/", trim($ip));
}

$isJSON = isset($_GET['apitype']) && $_GET['apitype'] == 'json';

$timeout = 4000;
$device = $_GET['device'];
if(!is_valid_ip($device)){
	$results = mysqli_query($link, "SELECT ip FROM modules WHERE mID = '$device'");

	while($row = $results->fetch_assoc())
        if($row['ip'] != "")
            $device = $row['ip'];
}

if(isset($_GET['timeout']))
	$timeout = intval($_GET['timeout']);

$cmd = $_GET['cmd'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://'.$device.'/'.$cmd);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
curl_setopt ($ch, CURLOPT_TIMEOUT_MS, $timeout);
$m = @curl_exec($ch);
curl_close($ch);

if(!$isJSON){
	if($m === FALSE)
		exit("Ошибка соеденения с модулем");
	else
		exit($m);
}
else{
	$js = array('deviceIP' => $device);
	if($m === FALSE){
		$js['status'] = false;
		$js['response'] = '';
	}
	else{
		$js['status'] = true;
		$js['response'] = $m;
	}
	exit(json_encode($js));
}
?>