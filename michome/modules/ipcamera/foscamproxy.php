<?
	function IsStr($str, $search){
		if(stripos($str, $search) !== FALSE) return true;
		else return false;
	}

	header("Camera-Page: Proxy-Module");       
	header('Content-Transfer-Encoding: binary');
    header('Cache-Control: no-cache');
		
	$cmd = $_GET['cmd'];
	$login = $_GET['login'];
	$pass = $_GET['password'];
	
	if(IsStr($cmd, ".jpg")){
		header("Content-type: image/jpg");
	}
	
	$ch = curl_init();
	$cmd = trim($cmd, " \\/");
	curl_setopt($ch, CURLOPT_URL, 'http://'.$cmd);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 300);
	curl_setopt($ch, CURLOPT_TIMEOUT_MS, 300);
	curl_setopt($ch, CURLOPT_USERPWD, $login.":".$pass);  
	$m = @curl_exec($ch);
	curl_close($ch);
	header('Content-Length: '.strlen(($m)));
	if(IsStr($cmd, ".jpg")){
		
	}
	exit($m);
?>