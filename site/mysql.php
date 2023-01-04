<?
    //require_once("/var/www/html/site/secur.php");
    $chunkCounts = 4000; //Количество символов в одном чанке сообщения
    
	//error_reporting(0);
	$db_host = 'localhost';
	$db_user = 'root';
	$db_password = 'MICHOMEBD2022'; //SuperSecretPasswordPHPMyaDMInGgGg
	$db_name = 'michome';
	
	$link = mysqli_connect($db_host, $db_user, $db_password, $db_name);
	if (!$link) {
    	echo('<p style="color:red"> Error conected to MySql</p>');
	}
		
	//echo "<p>Вы подключились к MySQL!</p>";

?>