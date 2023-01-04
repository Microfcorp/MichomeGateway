 <?
	include_once(__DIR__."//../site/mysql.php");
	require_once("lib/foreca.php");
	require_once("lib/michom.php");
	$foreca = new Foreca('Russia', 'Ostrogozhsk');
	
	echo "Направление ветра: ".$foreca->CurrentTemperature()."<br />";
    echo "Скорость ветра: ".$foreca->Feeling()." м/с<br />";
    echo "Скорость ветра: ".$foreca->Pressure()." м/с<br />";
    echo "Скорость ветра: ".$foreca->DewPoint()." м/с<br />";
    echo "Скорость ветра: ".$foreca->Humidity()." м/с<br />";
    echo "Скорость ветра: ".$foreca->Visibility()." м/с<br />";
    echo "Скорость ветра: ".$foreca->Rising()." м/с<br />";
    echo "Скорость ветра: ".$foreca->Sunset()." м/с<br />";
    echo "Скорость ветра: ".$foreca->WindSpeed()." м/с<br />";
    echo "Скорость ветра: ".$foreca->WindDeg()." м/с<br />";
	
	
    var_dump($foreca->GetTodayPrognoz()->GetHourlyPrognoz());
 ?>