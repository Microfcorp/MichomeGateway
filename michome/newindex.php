<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?

    $API = new MichomeAPI('192.168.1.42', $link);
    
    header("Michome-Page: Main-Page");
    
	$visot = "";
	$temper = "";
	$temper1 = "";
	$temper2 = "";
    $temper3 = "";
	$vlazn = "";
	$davlenie = "";
	$davlenie2 = "";
	$date = "";
	$alarm = "";

	$results = mysqli_query($link, "SELECT * FROM `michom` WHERE `date` >= \"".date('Y-m-d'/*,strtotime("-1 days")*/)."\"");
    
    $fgts = $API->TimeIns('192.168.1.10', 'selday', date("Y-m-d"));
    $seldays = explode(";", $fgts);
	
    $fgts1 = $API->TimeIns('192.168.1.11', 'selday', date("Y-m-d"));
    $seldays1 = explode(";", $fgts1);
    
    /*$fgts2 = $API->TimeIns('localhost', 'selday', date("Y-m-d"));
    $seldays2 = explode(";", $fgts2);*/

while($row = $results->fetch_assoc()) {
	if($row['type'] == "msinfoo"){
    $temper = $row['temp'];
	$vlazn = $row['humm'];
	$davlenie = $row['dawlen'];
	$visot = $row['visota'];
	$date = $row['date'];
	//echo date("Y-m-d H:i:s", $date);
	}
	elseif($row['type'] == "get_light_status"){
    $statussvet = $row['data'];
	//echo date("Y-m-d H:i:s", $date);
	}
	elseif($row['type'] == "termometr"){
    $temper1 = $row['temp'];
	//echo date("Y-m-d H:i:s", $date);
	}
    elseif($row['type'] == "temperbatarey"){
    $temper3 = $row['temp'];
	//echo date("Y-m-d H:i:s", $date);
	}
	elseif($row['type'] == "hdc1080"){
    $temper2 = $row['temp'];
	$davlenie2 = $row['humm'];
	//echo date("Y-m-d H:i:s", $date);
	}
	elseif($row['type'] == "hdc1080andAlarm"){
        $temper2 = $row['temp'];
        $davlenie2 = $row['humm'];
        if($row['data'] == "Alarm"){
        $alarm = "<p style='color:red;'>Внимание! Проникновение</p>";
        }
        elseif($row['data'] == "OK"){
        $alarm = "<p style='color:green;'>Дверь закрыта охрана установлена</p>";
        }
        elseif($row['data'] == "null"){
        $alarm = "<p style='color:green;'>Охрана снята. Дверь открыта</p>";
        }
        elseif($row['data'] == "nullok"){
        $alarm = "<p style='color:green;'>Охрана снята. Дверь закрыта</p>";
        }
	//echo date("Y-m-d H:i:s", $date);
	}
}
mysqli_free_result($results);
    
function scandir_by_mtime($folder) {
  $dircontent = scandir($folder);
  $arr = array();
  foreach($dircontent as $filename) {
    if ($filename != '.' && $filename != '..') {
      if (filemtime($folder.$filename) === false) return false;
      $dat = filemtime($folder.$filename);
      $arr[$dat] = $filename;
    }
  }
  if (!ksort($arr)) return false;
  return $arr;
}

 /*$dir = __DIR__."//../site/image/graphical/";
 $files = array();
 foreach (scandir($dir) as $file) $files[$file] = filemtime("$dir/$file");
 asort($files);
 $files = array_keys($files);

$lastfile = $files[count($files)-2];*/

$lastfile = "";

?>
<!Doctype html>
<html>
	<head>
		<title>Управление Michome</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
		<style>
			dialog::backdrop {
			  background-color: rgba(0, 0, 0, 0.8);
			}
		</style>
        <script type="text/javascript">
            function GetData()
            {
                 // получаем индекс выбранного элемента
                var selind = document.getElementById("select").options.selectedIndex;
               var txt= document.getElementById("textcmd").value;
               var val= document.getElementById("select").options[selind].value;
              
               document.getElementById("cmdresult").innerHTML = "Отправка данных: " + txt + " На: " + val + ". Пожалуйста подождите..."; //log
              
               postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ val +'&cmd='+ txt.replace( /&/g, "%26" ), "POST", "", function(d){document.getElementById("cmdresult").innerHTML = d;
              
               window.setTimeout(function(){document.getElementById("cmdresult").innerHTML = ""; m_SendCmd.style.display = 'none';},6000);
               
               });              
            }            
               
            var target_date = new Date('<?echo date("Y-m-d H:i:s", strtotime($date)+(60*10));?>').getTime(); // установить дату обратного отсчета
            var days, hours, minutes, seconds; // переменные для единиц времени
             
            var countdown = document.getElementById("sledob"); // получить элемент тега         
             
            function getCountdown(){
             
                var current_date = new Date().getTime();
                var seconds_left = (target_date - current_date) / 1000;
             
                days = pad( parseInt(seconds_left / 86400) );
                seconds_left = seconds_left % 86400;
                      
                hours = pad( parseInt(seconds_left / 3600) );
                seconds_left = seconds_left % 3600;
                       
                minutes = pad( parseInt(seconds_left / 60) );
                seconds = pad( parseInt( seconds_left % 60 ) );
             
                // строка обратного отсчета  + значение тега       
                
                
                if(parseInt(days) <= 0 & parseInt(hours) <= 0 & parseInt(minutes) <= 0 & parseInt(seconds) <= 0){
                    countdown.innerHTML = "Обновление";
                }
                else{
                    countdown.innerHTML = "Следующие обновление будет через: " + hours + ":" + minutes + ":" + seconds; 
                }
            }
             
            function pad(n) {
                return (n < 10 ? '0' : '') + n;
            }

                function CloseOpen(e){
                    e.style.display = (e.style.display == "block") ? 'none' : 'block';
                }
               
                function time(){		
                    window.setTimeout("time()",1000);
                    //getCountdown();
                    Data = new Date();
                    Year = Data.getFullYear();
                    Month = Data.getMonth();
                    Day = Data.getDate();
                    Hour = Data.getHours();
                    Minutes = Data.getMinutes();
                    Seconds = Data.getSeconds();
                    document.getElementById('datetime').innerHTML= "Текущее время: " + Day + '.' + '01' + '.' + Year + ' ' + Hour + ":" + Minutes + ':' + Seconds;
                }		
            function autoload(){
               postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/prognoz.php', "GET", "", function(d){document.getElementById("prognoz").innerHTML = d;});
            }
            
            window.setTimeout("time()",1);
            window.setTimeout("autoload()",1);
    </script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Главная</div>
			<div class = "com">
                <div style="width: 100%; height: 100%;" class = "components">
                
					<div class="padding: 15px;" class = "components_alfa">
						<!--<div class = "components_title">192.168.45.201 - Что-то тут интересное...</div>-->
						<div class = "components_text">
                        
                            <div style="float: right;">
                                <p id='datetime'>Текущая дата</p>                                                           
                            </div>
                            
                            <p><a href="#" onclick="CloseOpen(m_SendCmd);">Отправить комманду</a></p>                           
                            <div style="padding: 4px; margin-top: 5px; display: none;" id="m_SendCmd">
                                <form>
                                  <p class="SendCmdF">Команда: <input required type="text" name="cmd" id='textcmd' /></p>	  
                                  <p class="SendCmdF">На:  
                                      <select name="send" id='select'>
                                          <option  name='select' value="localhost">localhost</option>
                                          <?php
                                            $results = mysqli_query($link, "SELECT ip FROM modules");
                                            while($row = $results->fetch_assoc()) {
                                                if($row['ip'] != "" & $row['ip'] != "localhost"){
                                                    echo "<option name='select' value=".$row['ip'].">".$row['ip']."</option>";
                                                }
                                            }
                                          ?>
                                      </select>
                                  </p>
                               
                                 <p class="SendCmdF"><input name="sendcmd" value="Отправить" OnClick="GetData()" type="button" /></p>
                                 </form>

                                 <div class="SendCmdF" style="background-color:#03899C;">
                                    <p style="color: black;">Status Log: </p>
                                    <span style="color: black;" id="cmdresult"></span>
                                 </div>                                 
                            </div>
                            <br />
                            <div>
								<?
									$page = $API->GetWebPagesFromType('M')->SortSubType('TextValue')->WebPages();
									foreach($page as $tmp)
									{
										$textV = $API->GetConstant($tmp->Value);
										$postle = $API->GetWebConstant($textV);
										echo "<a name=\"".$tmp->Name."\" class=\"tooltip noselect DataV\"><p>".$postle."</p></a>";
									}
								?>
                                                                
                                <? //echo($alarm);?>
                                                               
                                <!-- <a class="tooltip noselect DataV"><p>Текущая температура трубы отопления: С</p><span><img src="grafick.php?ip=localhost&type=temperbatarey&start=&period="/></span></a>-->
                                
                                <!-- <a class="tooltip noselect DataV"><p>Последнее фото: </p><span><img width="540px" height="335px" src="/site/image/graphical/"/></span></a> -->

                                <p id="prognoz">Направление ветра: Загрузка...<br />
                                                Скорость ветра: Загрузка...<br />
                                                Тенденция давления: Загрузка...<br />
                                                Прогноз: Загрузка...</p>
                                
                                <span class="noselect DataV">Время восхода солнца: <? echo(date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3)); ?></span><br>
                                <span class="noselect DataV">Время захода солнца: <? echo(date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3)); ?></span><br>
                                <span class="noselect DataV">Долгота дня: <? echo(intval(date_sunset(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3)) - intval(date_sunrise(time(),SUNFUNCS_RET_STRING,50.860145, 39.082347, 90+50/60, 3))); ?> часов</span><br>
                            </div>
                            
                        </div>
					</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?>       
	</body>
</html>	
