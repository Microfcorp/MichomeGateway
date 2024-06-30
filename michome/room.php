<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
set_time_limit(5);
$API = new MichomeAPI('localhost', $link); 

if(isset($_POST['id'])){
    $ID=intval(mysqli_real_escape_string($link, $_POST['id']));
    $Name=(mysqli_real_escape_string($link, $_POST['name']));
    $Data=(mysqli_real_escape_string($link, $_POST['data']));
    $Modules=(mysqli_real_escape_string($link, $_POST['modules']));
    $mName=(mysqli_real_escape_string($link, $_POST['mName']));
	
	$API->EditRoom($ID, $Name, $Data, $Modules, $mName);
}

if(empty($_GET['setting']) || $_GET['setting'] != '1'){
	
header("Michome-Page: Room-Manager");
?>
<!Doctype html>
<html>
	<head>
		<title>Комнаты</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
        <script type="text/javascript"> 
			var allModules = "";
		
			function CreateStudioLight(mID, ip, type, id){
				if(IsModuleInNetwork(mID)){
					//<span><a href="studiolight.php?module=192.168.1.34" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Управление освещением</a></span><br />
					let sp = document.createElement('span');
					let as = document.createElement('a');
					let br = document.createElement('br');
					as.href = "studiolight.php?module="+mID;
					as.className = "roomLink";
					as.innerHTML = "Управление освещением ("+mID+")";
					sp.append(as);
					document.getElementById("roomlinks"+id).append(sp);
					document.getElementById("roomlinks"+id).append(br);
				}
			}

			function CreateTermometr(mID, ip, type, id){
				if(IsModuleInNetwork(mID)){
					//<span><a href="studiolight.php?module=192.168.1.34" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Управление освещением</a></span><br />
					let sp = document.createElement('span');
					let as = document.createElement('a');
					let br = document.createElement('br');
					as.href = "termometr.php?module="+mID;
					as.className = "roomLink";
					as.innerHTML = "Управление термометром ("+mID+")";
					sp.append(as);
					document.getElementById("roomlinks"+id).append(sp);
					document.getElementById("roomlinks"+id).append(br);
				}
			}		

			function CreateMeteo(mID, ip, type, id){
				if(IsModuleInNetwork(mID)){
					//<span><a href="studiolight.php?module=192.168.1.34" style="font-size: 12pt; font-family: Verdana, Arial, Helvetica, sans-serif;">Управление освещением</a></span><br />
					let sp = document.createElement('span');
					let as = document.createElement('a');
					let br = document.createElement('br');
					as.href = "meteostation.php?module="+mID;
					as.className = "roomLink";
					as.innerHTML = "Управление метеостанцией ("+mID+")";
					sp.append(as);
					document.getElementById("roomlinks"+id).append(sp);
					document.getElementById("roomlinks"+id).append(br);
				}
			}
			
			function LoadModules(id, modules){
				moduless = modules.split(',');
				for(var q = 0; q < moduless.length; q++){
					mods = moduless[q];
					for(var i = 0; i < allModules.devicename.length; i++){
						let mID = allModules.devicename[i];
						let ip = allModules.ips[i];
						let type = allModules.devicetype[i];
						if(mods == mID || mods == ip){
							if(type == "StudioLight")
								CreateStudioLight(mID, ip, type, id);
							else if(type == "termometr")
								CreateTermometr(mID, ip, type, id);
							else if(type == "meteostation")
								CreateMeteo(mID, ip, type, id);
						}					
					}		
				}
			}
			
			postAjax('api/getdevice.php', "GET", "", function(d){
				allModules = JSON.parse(d);								
			});
        </script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. План комнат</div>
			<div class = "com">
				<? 
					foreach($API->GetRooms() as $rm){
						echo "<div class = \"components\">";
						echo "<div class = \"components_alfa\">";
						echo "<div class = \"components_title\">".$rm['Name']."</div>";
						echo "<div style=\"width: auto\" class = \"components_text\">";
						echo "<span class='roomspan'>".nl2br($API->GetWebConstant($API->GetConstant($rm['Data'])))."</span>";
						echo "<br />";
						echo "<script>window.setTimeout(\"LoadModules('".$rm['ID']."', '".$rm['Modules']."')\", 100);</script>";
						echo "<div id=\"roomlinks".$rm['ID']."\"></div>";
						echo "</div>";
						echo "</div>";
						echo "</div>";
					}
				?>               
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?>        
	</body>
</html>	

<? } else { header("Michome-Page: Room-Setting"); ?>

<!Doctype html>
<html>
	<head>
		<title>Комнаты. Настройка</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
        <script type="text/javascript">
			function AddNew(){
				postAjax('api/rooms.php?type=Add', "GET", "", function(d){
					document.location.reload();
				});
			}
			function Remove(id){
				postAjax('api/rooms.php?type=Remove&id='+id, "GET", "", function(d){
					document.location.reload();
				});
			}
        </script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Настройка плана комнат</div>
			<div class = "com">
				<? 
					foreach($API->GetRooms() as $rm){
						echo "<div class = \"components\">";
						echo "<div class = \"components_alfa\">";
						echo "<form method=\"POST\">";
						echo "<input type='hidden' name='id' value='".$rm['ID']."'>";
						echo "<input type='hidden' name='setting' value='1'>";
						echo "<div class = \"components_title\"><input type='text' style=\"min-width: 350px; width: 100%;\" name='name' value='".$rm['Name']."' /></div>";
						echo "<div style=\"width: auto\" class = \"components_text\">";
						//echo "<div class = \"components\">";
						echo "<p><textarea style=\"min-width: 350px; min-height: 100px; width: 98%;\" name='data'>".($rm['Data'])."</textarea></p>";
						echo "<p><input style=\"min-width: 350px; width: -webkit-fill-available;\" type='text' name='modules' value='".$rm['Modules']."' /></p>";
						echo "<p><input style=\"min-width: 350px; width: -webkit-fill-available;\" type='text' name='mName' value='".$rm['mName']."' /></p>";
						echo "<p><input style=\"min-width: 350px;\" type='submit' value='Сохранить' /><button onclick='Remove(\"".$rm['ID']."\")'>Удалить</button></p>";
						//echo "</div>";
						echo "</form>";
						echo "</div>";
						echo "</div>";
						echo "</div>";
					}
				?>
				<div class = "components">
					<div class = "components_alfa">
						<div class = "components_title">Функции</div>
						<div style="width: auto" class = "components_text">
                            <p><a href="#" onclick="AddNew(); return false;">Добавить новый</a></p>  
                        </div>
					</div>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?>        

	</body>
</html>	
<? } ?>