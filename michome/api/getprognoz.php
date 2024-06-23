<?php
//error_reporting(0);
header('Access-Control-Allow-Origin: *');
include_once("../../site/mysql.php");
require_once("../lib/michom.php");

$today = date("H");
if($today > 21 || $today < 7){
    $b = 0;
}
else{
    $b = 1;
}

$michome = new MichomeAPI("localhost", $link);

$ultemper = $michome->GetPosledData("192.168.1.11")->Temp;
//$Garage = $michome->GetPosledData("192.168.1.14");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://openweathermap.org/data/2.5/onecall?lat=".$michome->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value."&lon=".$michome->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value."&units=metric&appid=439d4b804bc8187953eb36d2a8c26a02"); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$mainreq = @curl_exec($ch);

if($mainreq === FALSE){
    $dd = ($b == 0 ? "4" : "3");
    $ret = Array('type'=>'json', 'd'=>$dd);
    exit(json_encode($ret));
}
$jmrq = json_decode($mainreq, true);    
$req = $jmrq["daily"][0];
$req1 = $jmrq["daily"][1];
$req2 = $jmrq["daily"][2];
$req3 = $jmrq["daily"][3];
$req4 = $jmrq["daily"][4];
$req5 = $jmrq["daily"][5];
$hourly = $jmrq["hourly"];
$current = $jmrq["current"];

$ids = Array(200=>0,201=>0,202=>0,210=>0,211=>0,212=>0,221=>0,230=>0,231=>0,232=>0,300=>1,301=>1,302=>1,310=>1,311=>1,312=>1,313=>1,314=>1,321=>1,500=>6,501=>2,502=>2,503=>2,504=>2,511=>2,520=>2,521=>2,522=>2,531=>2,600=>3,601=>3,602=>3,611=>3,612=>3,615=>3,616=>3,620=>3,621=>3,622=>3,800=>4,801=>5,802=>5,803=>5,804=>5);

$local = ["Гроза","Мелкий дождик","Дождик","Снежок","Солнечно","Облачно", "Что-то неизвестное"];    

function GetHourlyFromDay($day){
	global $michome;
	global $hourly;
	$hourspr = [];
	for($i=0; $i < count($hourly); $i++){
		if($michome->IsCurrentDay($day, $hourly[$i]["dt"]))
			$hourspr[] = $hourly[$i];
	}
	return $hourspr;
}

function GetNightWeather($dayar){ //На 21 час вечера

	if(count($dayar) > 4)
		return [$dayar[count($dayar) - 3]['weather'][0]['id'], date("H:i", $dayar[count($dayar) - 3]['dt'])];
	elseif(count($dayar) != 0) return [$dayar[count($dayar)-1]['weather'][0]['id'], date("H:i", $dayar[count($dayar)-1]['dt'])];
	else return [200, "00:00"];
}

function WindDegParse($degree){ //Конвертация угла к напревлению ветра
	$idsW = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
	if($degree > 150 && $degree < 210)
		return $idsW[1];
	elseif($degree > 210 && $degree < 240)
		return $idsW[2];
	elseif($degree > 240 && $degree < 300)
		return $idsW[3];
	elseif($degree > 300 && $degree < 330)
		return $idsW[4];
	elseif(($degree > 0 && $degree < 30) || ($degree > 330 && $degree < 360))
		return $idsW[5];
	elseif($degree > 30 && $degree < 60)
		return $idsW[6];
	elseif($degree > 60 && $degree < 120)
		return $idsW[7];
	elseif($degree > 120 && $degree < 150)
		return $idsW[8];
	else return $idsW[0];
}
    
if(empty($_GET['type'])){              
    echo("Прогноз на сегодня: "."<br />");    
    echo(" Днём: ".$prognoz[0]->TDay."<br />");
    echo(" Ночью: ".$prognoz[0]->TNight."<br />");
    echo(" Ветер: ".$req['wind_speed']." m/s"."<br />");
    echo(" Давление: ".$req['pressure']."<br />");
    echo(" Прогноз: ".$local[$ids[$req['weather'][0]['id']]]."<br />");
	echo(" Прогноз на вечер (".GetNightWeather(GetHourlyFromDay($req['dt']))[1]."): ".$local[$ids[GetNightWeather(GetHourlyFromDay($req['dt']))[0]]]."<br />");
    echo("<br />");
    echo("Прогноз на завтра: "."<br />");    
    echo(" Днём: ".$prognoz[1]->TDay."<br />");
    echo(" Ночью: ".$prognoz[1]->TNight."<br />");
    echo(" Ветер: ".$req1['wind_speed']." m/s"."<br />");
    echo(" Давление: ".$req1['pressure']."<br />");
    echo(" Прогноз: ".$local[$ids[$req1['weather'][0]['id']]]."<br />");
    echo(" Прогноз на вечер (".GetNightWeather(GetHourlyFromDay($req1['dt']))[1]."): ".$local[$ids[GetNightWeather(GetHourlyFromDay($req1['dt']))[0]]]."<br />");
}
elseif($_GET['type'] == "VK"){
    if($_GET['d'] == '1'){
        echo("Прогноз на сегодня: "."\r\n");    
        echo(" Днём: ".$prognoz[0]->TDay."\r\n");
        echo(" Ночью: ".$prognoz[0]->TNight."\r\n");
        echo(" Ветер: ".$req['wind_speed']." m/s"."\r\n");
        echo(" Давление: ".round($req['pressure'] / 1.33)."мм.рт.ст\r\n");
        echo(" Прогноз: ".$local[$ids[$req['weather'][0]['id']]]."\r\n");
		echo(" Прогноз на вечер: ".$local[$ids[GetNightWeather(GetHourlyFromDay($req['dt']))]]."<br />");
    }
    else{
        echo("Прогноз на завтра: "."\r\n");    
        echo(" Днём: ".$prognoz[1]->TDay."\r\n");
        echo(" Ночью: ".$prognoz[1]->TNight."\r\n");
        echo(" Ветер: ".$req1['wind_speed']." m/s"."\r\n");
        echo(" Давление: ".round($req1['pressure'] / 1.33)."мм.рт.ст\r\n");
        echo(" Прогноз: ".$local[$ids[$req1['weather'][0]['id']]]."\r\n");
		echo(" Прогноз на вечер: ".$local[$ids[GetNightWeather(GetHourlyFromDay($req1['dt']))]]."<br />");
    }
}
else{      
    if(!isset($_GET['dt'])){	
		$ret = Array('type'=>'json', 'curdate'=>date("Y-m-d"), 'd'=>$b, 'dawlen'=>round($michome->GetPosledData("192.168.1.10")->Dawlen, 1), 'tempgr'=>'0', 'hummgr'=>'0', 'temp'=>$ultemper, 'time'=>date("H:i:s"), 'unixtime'=>time(), 'ic'=>$ids[$current['weather'][0]['id']], 
		 'data'=>Array(
			Array('unixtime'=>$req['dt'], 'times'=>date("Y-m-d", $req['dt']),  '0'=>round($req['temp']["day"], 1),  '1'=>round($req['temp']["night"], 1),  '2'=>$req["wind_speed"],  '3'=>round($req['pressure']/1.334, 1),  '4'=>($ids[$req['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req['dt']))[0]]), '6'=>WindDegParse($req['wind_deg']) ), 
			Array('unixtime'=>$req1['dt'], 'times'=>date("Y-m-d", $req1['dt']), '0'=>round($req1['temp']["day"], 1), '1'=>round($req1['temp']["night"], 1), '2'=>$req1["wind_speed"], '3'=>round($req1['pressure']/1.334, 1), '4'=>($ids[$req1['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req1['dt']))[0]]), '6'=>WindDegParse($req1['wind_deg']) ), 
			Array('unixtime'=>$req2['dt'], 'times'=>date("Y-m-d", $req2['dt']), '0'=>round($req2['temp']["day"], 1), '1'=>round($req2['temp']["night"], 1), '2'=>$req2["wind_speed"], '3'=>round($req2['pressure']/1.334, 1), '4'=>($ids[$req2['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req2['dt']))[0]]), '6'=>WindDegParse($req2['wind_deg']) ),
			
			/*Array('unixtime'=>$req['dt'], 'times'=>date("Y-m-d", $req['dt']),  '0'=>round($prognoz[0]->TDay, 1),  '1'=>round($prognoz[0]->TNight, 1),  '2'=>$prognoz[0]->Wind->Speed,  '3'=>round($req['pressure']/1.334, 1),  '4'=>($ids[$req['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req['dt']))[0]]), '6'=>WindDegParse($req['wind_deg']) ), 
			Array('unixtime'=>$req1['dt'], 'times'=>date("Y-m-d", $req1['dt']), '0'=>round($prognoz[1]->TDay, 1), '1'=>round($prognoz[1]->TNight, 1), '2'=>$prognoz[1]->Wind->Speed, '3'=>round($req1['pressure']/1.334, 1), '4'=>($ids[$req1['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req1['dt']))[0]]), '6'=>WindDegParse($req1['wind_deg']) ), 
			Array('unixtime'=>$req2['dt'], 'times'=>date("Y-m-d", $req2['dt']), '0'=>round($prognoz[2]->TDay, 1), '1'=>round($prognoz[2]->TNight, 1), '2'=>$prognoz[2]->Wind->Speed, '3'=>round($req2['pressure']/1.334, 1), '4'=>($ids[$req2['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req2['dt']))[0]]), '6'=>WindDegParse($req2['wind_deg']) ), 
			//Array('unixtime'=>$req3['dt'], 'times'=>date("Y-m-d", $req3['dt']), '0'=>round($prognoz[3]->TDay, 1), '1'=>round($prognoz[3]->TNight, 1), '2'=>$prognoz[3]->Wind->Speed, '3'=>round($req3['pressure']/1.334, 1), '4'=>($ids[$req3['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req3['dt']))[0]]) ), 
			//Array('unixtime'=>$req4['dt'], 'times'=>date("Y-m-d", $req4['dt']), '0'=>round($prognoz[4]->TDay, 1), '1'=>round($prognoz[4]->TNight, 1), '2'=>$prognoz[4]->Wind->Speed, '3'=>round($req4['pressure']/1.334, 1), '4'=>($ids[$req4['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req4['dt']))[0]]) ), 
			//Array('unixtime'=>$req5['dt'], 'times'=>date("Y-m-d", $req5['dt']), '0'=>round($prognoz[5]->TDay, 1), '1'=>round($prognoz[5]->TNight, 1), '2'=>$prognoz[5]->Wind->Speed, '3'=>round($req5['pressure']/1.334, 1), '4'=>($ids[$req5['weather'][0]['id']]), '5'=>($ids[GetNightWeather(GetHourlyFromDay($req5['dt']))[0]]) )*/
		));
		echo(json_encode($ret));
	}
	else{		
		$dt = $_GET['dt'];
		$ddt = date("d", $dt);
		$mdt = date("m", $dt);
		$ydt = date("Y", $dt);
		
		$hourspr = [];
		for($i=0; $i < count($hourly); $i++){
			if($michome->IsCurrentDay($dt, $hourly[$i]["dt"]))
				$hourspr[] = Array('date'=>date("Y-m-d", $hourly[$i]["dt"]), 'times'=>date("H:i", $hourly[$i]["dt"]), '0'=>round($hourly[$i]["temp"], 0) . "  ", '1'=>($ids[$hourly[$i]['weather'][0]['id']]));
		}
		
		$ret = Array('type'=>'json', 'count'=>count($hourspr), 'curdate'=>date("Y-m-d"), 
			'data'=>$hourspr
		);
		echo(json_encode($ret));
	}
}
//var_dump($req);
?>
