<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
header("Michome-Page: Room-Manager");   

$temp1 = "";
$temp2 = "";
$temp3 = "";

$humm1 = "";
$humm2 = "";

$abc1 = "";
$abc2 = "";

$pressed = false;

$log1 = "";

$results = mysqli_query($link, "SELECT `temp`,`humm`,`dawlen` FROM michom WHERE ip='192.168.1.10' ORDER BY `id` DESC limit 1");
while($row = $results->fetch_assoc()) {
	$temp1 = $row['temp'];
	$humm1 = $row['humm'];
	$abc1 = $row['dawlen'];
}
$results = mysqli_query($link, "SELECT `temp` FROM michom WHERE ip='192.168.1.11' ORDER BY `id` DESC limit 1");
while($row = $results->fetch_assoc()) {
	$temp2 = $row['temp'];
}
$results = mysqli_query($link, "SELECT `temp`,`humm` FROM michom WHERE ip='192.168.1.14' ORDER BY `id` DESC limit 1");
while($row = $results->fetch_assoc()) {
	$humm2 = $row['humm'];
	$temp3 = $row['temp'];
}

/*$results = mysqli_query($link, "SELECT * FROM michom WHERE ip='192.168.1.34' ORDER BY `id` DESC limit 1");
while($row = $results->fetch_assoc()) {
	$pressed = boolval($row['data']);
    $pressed =! $pressed;
}*/

$results = mysqli_query($link, "SELECT COUNT(`id`) FROM `logging` WHERE `date` >= CURDATE() AND ip='192.168.1.10' AND Log=\"MsinfooNAN\" LIMIT 1");
while($row = $results->fetch_assoc()) {
	$log1 = $row["COUNT(`id`)"];
}
?>
<!Doctype html>
<html>
	<head>
		<title>Комнаты</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
        <script type="text/javascript">        
            function Giron(q){
                var host = '<?echo $_SERVER['HTTP_HOST'];?>';
                
                if(host != "192.168.1.42"){
                    //console.log('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'setlight?p='+p+'%26s='+size);
                    postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.12' +'&cmd='+ 'setlight?p=1%26s='+q,'POST', "", function(){});
                }
                else{
                    //console.log('http://192.168.1.34/setlight?p='+p+'&s='+size);
                    postAjax('http://192.168.1.12/setlight?p=1&s='+q, 'POST', "", function(){});
                }
                //sleep(500);
            }
            
            function Winon(q){
                var host = '<?echo $_SERVER['HTTP_HOST'];?>';
                
                if(host != "192.168.1.42"){
                    //console.log('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'setlight?p='+p+'%26s='+size);
                    if(q == 1)
                        postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.14' +'&cmd='+ 'setlight?s=1','POST', "", function(){});
                    else postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.14' +'&cmd='+ 'setlight?s=0','POST', "", function(){});
                }
                else{
                    //console.log('http://192.168.1.34/setlight?p='+p+'&s='+size);
                    if(q == 1) postAjax('http://192.168.1.14/setlight?s=1', 'POST', "", function(){});
                    else postAjax('http://192.168.1.14/setlight?s=0', 'POST', "", function(){});
                }
                //sleep(500);
            }

            function GetButton(a){
                var checkbox = document.querySelector('input[type="checkbox"]');
                  postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'getdigital?p=5','POST' , "", function(d){
                      if(d=='0')
                        checkbox.checked = true;
                      else
                        checkbox.checked = false;
                  });
                  
                 if(a)
                     window.setTimeout("GetButton(true)", 2000);
            }

            document.addEventListener('DOMContentLoaded', function () {
              var checkbox = document.querySelector('input[type="checkbox"]');
              //checkbox.checked = <? echo $pressed ? "true" : "false"; ?>;
              GetButton(false);
              
              checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    var host = '<?echo $_SERVER['HTTP_HOST'];?>';
                    var data = '{"name":"123","Params":[{"name":"setlight","pin":"0","brightness":"1023"},{"name":"setlight","pin":"1","brightness":"1023"},{"name":"setlight","pin":"2","brightness":"1023"}]}';
                    if(host != "192.168.1.42"){     
                        postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'jsonget?g='+data, "POST", "", function(d){});
                    }
                    else{            
                        postAjax('http://192.168.1.34/jsonget?g='+data, "POST", "", function(d){});
                    }
                    
                } 
                else {
                    var host = '<?echo $_SERVER['HTTP_HOST'];?>';
                    var data = '{"name":"123","Params":[{"name":"setlight","pin":"0","brightness":"0"},{"name":"setlight","pin":"1","brightness":"0"},{"name":"setlight","pin":"2","brightness":"0"}]}';
                    if(host != "192.168.1.42"){     
                        postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'jsonget?g='+data, "POST", "", function(d){});
                    }
                    else{            
                        postAjax('http://192.168.1.34/jsonget?g='+data, "POST", "", function(d){});
                    }
                }
              });
            });
			
			function CreateStudioLight(mID, ip, type){
				if(IsModuleInNetwork(mID)){
					//<span><a href="studiolight.php?module=192.168.1.34" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Управление освещением</a></span><br />
					let sp = document.createElement('span');
					let as = document.createElement('a');
					let br = document.createElement('br');
					as.href = "studiolight.php?module="+mID;
					as.className = "roomLink";
					as.innerHTML = "Управление освещением ("+mID+")";
					sp.append(as);
					document.getElementById("roomlinks").append(sp);
					document.getElementById("roomlinks").append(br);
				}
			}
			
			function FromLoadPage(){
				postAjax('api/getdevice.php', "GET", "", function(d){
					var lines = JSON.parse(d);
					for(var i = 0; i < lines.devicename.length; i++){
						let mID = lines.devicename[i];
						let ip = lines.ips[i];
						let type = lines.devicetype[i];
						
						if(type == "StudioLight")
							CreateStudioLight(mID, ip, type);				
					}								
				});
			}

            //window.setTimeout("GetButton(false)", 2000);
            window.setTimeout("FromLoadPage()", 10);
        </script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. План комнат</div>
			<div class = "com">
                <div class = "components">
					<div class = "components_alfa">
						<div class = "components_title">Зал</div>
						<div style="width: auto" class = "components_text">
                            <!-- <p style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;" id="light1">Центральная люстра: <? //echo(mysqli_query($link, "SELECT * FROM michom  WHERE ip = '192.168.1.11'")->fetch_assoc()['data']);?> <a href="#" style="font-size: 11pt; font-family: Verdana, Arial, Helvetica, sans-serif;" OnClick="setlight(light1)">Изменить состояние</a></p>
                            <p style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;" id="light1">Прожектор: <? //echo(mysqli_query($link, "SELECT * FROM michom  WHERE ip = '192.168.1.46'")->fetch_assoc()['data']);?> <a href="#" style="font-size: 11pt; font-family: Verdana, Arial, Helvetica, sans-serif;" OnClick="setlight(light2)">Изменить состояние</a></p> -->
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Температура: <? echo($temp1);?></span><br />
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Давление: <? echo($abc1);?></span><br />
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Влажность: <? echo($humm1);?></span><br />
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Всего за сегодня "Нанов": <? echo($log1);?></span><br />
                            <div id="roomlinks"></div>
							<span><a href="ircontrol.php?type=sab" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Управление сабвуфером</a></span><br />                           
							<span>Положение выключателя</span> <label class="switch"><input type="checkbox"><div class="slider"></div></label>
                            
                            <!--<p><a href="#" onclick="Giron('1')" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Включить ель</a></p>
                            <p><a href="#" onclick="Giron('0')" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Выключить ель</a></p>
                            
                            <p><a href="#" onclick="Winon(1)" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Включить окно</a></p>
                            <p><a href="#" onclick="Winon(0)" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Выключить окно</a></p>-->
                        </div>
					</div>
				</div>
                <div class = "components">
					<div class = "components_alfa">
						<div class = "components_title">Улица</div>
						<div style="width: auto" class = "components_text">
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Температура: <? echo($temp2);?></span>                    
                        </div>
					</div>
				</div>
                <div class = "components">
					<div class = "components_alfa">
						<div class = "components_title">Гараж</div>
						<div style="width: auto" class = "components_text">
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Температура: <? echo($temp3);?></span><br />
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Влажность: <? echo($humm2);?></span><br />                        
                        </div>
					</div>
				</div>
                <div class = "components">
					<div class = "components_alfa">
						<div class = "components_title">Улица</div>
						<div style="width: auto" class = "components_text">
                            <span style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Температура: <? echo($temp2);?></span>  
                        </div>
					</div>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
        
        <div>
            <dialog style="padding: 16px; margin: auto;" id="dialog">
              <div id="tables"></div>
              <button onclick="save()">Сохранить</button>
              <button onclick="closed()">Закрыть</button>
            </dialog>
        </div>
	</body>
</html>	
