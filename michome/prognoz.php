<?
include_once(__DIR__."//../site/mysql.php");
require_once("lib/foreca.php");
require_once("lib/michom.php");

//$z_where  Северное = 1 (это наше, если что) или Южное = 2 полушарие
//$z_baro_top  верхний предел 'погодного окна' (1050.0 гПа для Великобритании)
//$z_baro_bottom     нижний предел 'погодного окна' (950.0 гПа для Великобритании)

// usage:   forecast = betel_cast( $z_hpa, $z_month, $z_wind, $z_trend [, $z_where] [, $z_baro_top] [, $z_baro_bottom]);
// $z_hpa - Атмосферное давление в гПа
// $z_month текущий месяц, от 1 до 12
// $z_wind текущее направление ветра в английской системе координат типа N, NNW, NW и т.д.
// $z_trend изменения в атмосферном давлении: 0 = не меняется, 1 = растет, 2 = снижается

$API = new MichomeAPI('localhost', $link);

function betel_cast( $z_hpa = 740, $z_month = 4, $z_wind = "W", $z_trend = 2, $z_where = 1, $z_baro_top = 1050, $z_baro_bottom = 950, $wh_temp_out = 9)
{

$z_forecast_uk = Array("Settled fine", "Fine weather", "Becoming fine", "Fine,
becoming less settled", "Fine, possible showers", "Fairly fine,
improving", "Fairly fine, possible showers early", "Fairly fine,
showery later", "Showery early, improving", "Changeable,
mending", "Fairly fine, showers likely",
"Rather unsettled clearing later", "Unsettled, probably improving",
"Showery, bright intervals", "Showery, becoming less settled",
"Changeable, some rain", "Unsettled, short fine intervals",
"Unsettled, rain later", "Unsettled, some rain",
"Mostly very unsettled", "Occasional rain, worsening",
"Rain at times, very unsettled", "Rain at frequent intervals",
"Rain, very unsettled", "Stormy, may improve", "Stormy,
much rain");

// Если зима, то снег, а не дожди!
if ( $wh_temp_out < 2 )
$z_forecast = Array("Отличная, ясно", "Хорошая, ясно",
"Становление хорошей, ясной", "Хорошая, но ухудшается",
"Хорошая, возможен снегопад", "Достаточно хорошая, улучшается",
"Достаточно хорошая, возможен снегопад",
"Достаточно хорошая, но ожидается снегопад",
"Снегопад, но улучшается", "Переменчивая, но улучшается",
"Достаточно хорошая, вероятен снегопад", "Пасмурно, но проясняется",
"Пасмурно, возможно, улучшение",
"Снегопады, возможны временные прояснения",
"Снегопады, становится менее устойчивой",
"Переменчивая, небольшой снег", "Пасмурная, короткие прояснения",
"Пасмурная, ожидается снег", "Пасмурная, временами снег",
"Преимущественно, очень пасмурная", "Временами снег, ухудшение",
"Временами снег, очень плохая, пасмурно", "Снег очень часто",
"Снег, очень плохая, пасмурно", "Штормовая, но улучшается",
"Штормовая!, снегопад");
else
$z_forecast = Array("Отличная, ясно", "Хорошая, ясно",
"Становление хорошей, ясной", "Хорошая, но ухудшается",
"Хорошая, возможен ливень", "Достаточно хорошая, улучшается",
"Достаточно хорошая, возможен ливень",
"Достаточно хорошая, но ожидается ливень", "Ливень, но улучшается",
"Переменчивая, но улучшается", "Достаточно хорошая, вероятны ливни",
"Пасмурно, но проясняется", "Пасмурно, возможно, улучшение",
"Ливни, возможны временные прояснения",
"Ливни, становится менее устойчивой",
"Переменчивая, небольшие дожди", "Пасмурная, короткие прояснения",
"Пасмурная, ожидаются дожди", "Пасмурная, временами дожди",
"Преимущественно, очень пасмурная", "Временами дожди, ухудшение",
"Временами дожди, очень плохая, пасмурно", "Дожди очень часто",
"Дожди, очень плохая, пасмурно", "Штормовая, но улучшается",
"Штормовая!, дожди");

// equivalents of Zambretti 'dial window' letters A - Z
$rise_options  = Array(25,25,25,24,24,19,16,12,11,9,8,6,5,2,1,1,0,0,0,0,0,0) ;
$steady_options  = Array(25,25,25,25,25,25,23,23,22,18,15,13,10,4,1,1,0,0,0,0,0,0) ;
$fall_options = Array(25,25,25,25,25,25,25,25,23,23,21,20,17,14,7,3,1,1,1,0,0,0) ;

    $z_range = $z_baro_top - $z_baro_bottom;
    $z_constant = round(($z_range / 22), 3);

    $z_season = (($z_month >= 4) && ($z_month <= 9)) ;    // true if 'Summer'

    if ($z_where == 1) {         // North hemisphere
        if ($z_wind == "N") { 
            $z_hpa += 6 / 100 * $z_range ; 
        } else if ($z_wind == "NNE") { 
            $z_hpa += 5 / 100 * $z_range ; 
        } else if ($z_wind == "NE") { 
            $z_hpa += 5 / 100 * $z_range ; 
        } else if ($z_wind == "ENE") { 
            $z_hpa += 2 / 100 * $z_range ; 
        } else if ($z_wind == "E") { 
            $z_hpa -= 0.5 / 100 * $z_range ; 
        } else if ($z_wind == "ESE") { 
            $z_hpa -= 2 / 100 * $z_range ; 
        } else if ($z_wind == "SE") { 
            $z_hpa -= 5 / 100 * $z_range ; 
        } else if ($z_wind == "SSE") { 
            $z_hpa -= 8.5 / 100 * $z_range ; 
        } else if ($z_wind == "S") { 
            $z_hpa -= 12 / 100 * $z_range ; 
        } else if ($z_wind == "SSW") { 
            $z_hpa -= 10 / 100 * $z_range ;  //
        } else if ($z_wind == "SW") { 
            $z_hpa -= 6 / 100 * $z_range ; 
        } else if ($z_wind == "WSW") { 
            $z_hpa -= 4.5 / 100 * $z_range ;  //
        } else if ($z_wind == "W") { 
            $z_hpa -= 3 / 100 * $z_range ; 
        } else if ($z_wind == "WNW") { 
            $z_hpa -= 0.5 / 100 * $z_range ; 
        }else if ($z_wind == "NW") { 
            $z_hpa += 1.5 / 100 * $z_range ; 
        } else if ($z_wind == "NNW") { 
            $z_hpa += 3 / 100 * $z_range ; 
        }
        if ($z_season == TRUE) {     // if Summer
            if ($z_trend == 1) {     // rising
                $z_hpa += 7 / 100 * $z_range; 
            } else if ($z_trend == 2) {  //    falling
                $z_hpa -= 7 / 100 * $z_range;
            }
        }
    } else {     // must be South hemisphere
        if ($z_wind == "S") { 
            $z_hpa += 6 / 100 * $z_range ; 
        } else if ($z_wind == "SSW") { 
            $z_hpa += 5 / 100 * $z_range ; 
        } else if ($z_wind == "SW") { 
            $z_hpa += 5 / 100 * $z_range ; 
        } else if ($z_wind == "WSW") { 
            $z_hpa += 2 / 100 * $z_range ; 
        } else if ($z_wind == "W") { 
            $z_hpa -= 0.5 / 100 * $z_range ; 
        } else if ($z_wind == "WNW") { 
            $z_hpa -= 2 / 100 * $z_range ; 
        } else if ($z_wind == "NW") { 
            $z_hpa -= 5 / 100 * $z_range ; 
        } else if ($z_wind == "NNW") { 
            $z_hpa -= 8.5 / 100 * $z_range ; 
        } else if ($z_wind == "N") { 
            $z_hpa -= 12 / 100 * $z_range ; 
        } else if ($z_wind == "NNE") { 
            $z_hpa -= 10 / 100 * $z_range ;  //
        } else if ($z_wind == "NE") { 
            $z_hpa -= 6 / 100 * $z_range ; 
        } else if ($z_wind == "ENE") { 
            $z_hpa -= 4.5 / 100 * $z_range ;  //
        } else if ($z_wind == "E") { 
            $z_hpa -= 3 / 100 * $z_range ; 
        } else if ($z_wind == "ESE") { 
            $z_hpa -= 0.5 / 100 * $z_range ; 
        }else if ($z_wind == "SE") { 
            $z_hpa += 1.5 / 100 * $z_range ; 
        } else if ($z_wind == "SSE") { 
            $z_hpa += 3 / 100 * $z_range ; 
        }
        if ($z_season == FALSE) {    // if Winter
            if ($z_trend == 1) {  // rising
                $z_hpa += 7 / 100 * $z_range; 
            } else if ($z_trend == 2) {  // falling
                $z_hpa -= 7 / 100 * $z_range;
            }
        }
    }    // END North / South

    if($z_hpa == $z_baro_top) {$z_hpa = $z_baro_top - 1;}
    $z_option = floor(($z_hpa - $z_baro_bottom) / $z_constant);
     $z_output = "";

    if($z_option < 0) {
        $z_option = 0;
        //$z_output = "Exceptional Weather, ";
    }
    if($z_option > 21) {
        $z_option = 21;
       // $z_output = "Exceptional Weather, ";
    }

    if ($z_trend == 1) {
        $z_output .= $z_forecast[$rise_options[$z_option]] ;
    } else if ($z_trend == 2) {
        $z_output .= $z_forecast[$fall_options[$z_option]] ;
    } else {
        $z_output .= $z_forecast[$steady_options[$z_option]] ;
    }
    return ($z_output) ;
}       
?>

<?
$wind_dir_text_uk = array("N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW", "None");

// Переменные $abs_pressure, $abs_pressure_1h, $wind_dir_avg, $wh_temp_out берутся из базы данных.
// Процесс заполнения базы и выборки из нее подробно рассмотрен в предыдущей статье,
// поэтому здесь опускаем эту часть кода

$moduleDawlen = isset($_GET["modD"]) ? $_GET["modD"] : "";
$moduleTemp = isset($_GET["modT"]) ? $_GET["modT"] : "";

$pres = $API->GetPosledData($moduleDawlen, '1h');

if($press->CountBD() < 2 || $API->GetPosledData($moduleTemp)->IsNull){
	exit("Недостаточно данных для рассчета прогноза погоды");
}

$abs_pressure = $pres->First()->Dawlen * 1.333;
$abs_pressure_1h = $pres->Last()->Dawlen * 1.333;
$ul_temp = $API->GetPosledData($moduleTemp)->Temp;

$foreca = new Foreca($API->GetSettingORCreate('Country', 'Russia', 'Страна проживания (на английском)')->Value, $API->GetSettingORCreate('Sity', 'Ostrogozhsk', 'Город проживания (на английском)')->Value);

// Бесхитростное определение тенденции в изменении давления
// Здесь и в основной функции значения переведены из мм.рт.ст в гПа
if ( $abs_pressure > $abs_pressure_1h + 0.25)
{
    $pressure_trend = 1;
    $pressure_trend_text = "Рост";
}
elseif ( $abs_pressure_1h > $abs_pressure + 0.25)
{
    $pressure_trend = 2;
    $pressure_trend_text = "Падение";
}
else
{
    $pressure_trend = 0;
    $pressure_trend_text = "Не меняется";
}

$forecast = betel_cast($abs_pressure, date('n'), $foreca->Wind()->Degree, $pressure_trend, 1, 1050, 950, $ul_temp);

if(isset($_GET['type']) && $_GET['type'] = "VK"){
    echo "Направление ветра: ".$foreca->Wind()->Degree."\n";
    echo "Скорость ветра: ".$foreca->Wind()->Speed." м/с\n";
    echo "Тенденция давления: $pressure_trend_text\n";
    echo "Прогноз: $forecast";
}
else{
    echo "<p style=\"padding-left: 10px;\">Направление ветра: ".$foreca->Wind()->Degree."<br>";
    echo "Скорость ветра: ".$foreca->Wind()->Speed." м/с<br>";
    echo "Тенденция давления: $pressure_trend_text<br>";
    echo "Прогноз: $forecast";
    echo "</p>";
}
?>
