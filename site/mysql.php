<?
    //require_once("/var/www/html/site/secur.php");
    $chunkCounts = 4000; //Количество символов в одном чанке сообщения

	//error_reporting(0);
	$db_host = 'localhost'; //Хост сервера
	$db_user = 'root'; //Логин
	$db_password = 'MICHOMEBD2022'; //Пароль (Можно не волноваться у меня совсем другой)
	$db_name = 'michome';

	$link = mysqli_connect($db_host, $db_user, $db_password, $db_name);
	if (!$link) {
    	echo('<p style="color:red"> Error conected to MySql</p>');
	}
?>
