<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	$API = new MichomeAPI('localhost', $link);
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$value = $_POST['value'];
		$API->GetSettingByID($id)->SetValue($value);
		exit("OK");
	}	
	header("Michome-Page: Settings-Page");   
?>
<!Doctype html>
<html>
	<head>
		<title>Настройки системы</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
		<script>
			function getLocation() {
				if(document.getElementsByName("latitude").length > 0 || document.getElementsByName("longitude").length > 0){
					if (navigator.geolocation) {
						navigator.geolocation.getCurrentPosition(showPosition);
					}
				}
			}
			function showPosition(position) {
				let latitudes = document.getElementsByName("latitude");
				let longitudes = document.getElementsByName("longitude");
				for(var i = 0; i < latitudes.length; i++)
				{
					latitudes[i].value = position.coords.latitude;
				}
				for(var i = 0; i < longitudes.length; i++)
				{
					longitudes[i].value = position.coords.longitude;
				}
			}
			function saveSetting(id){
				var value = document.getElementById('settingValue'+id).value;
				postAjax('settings.php?id='+ id, "POST", "value="+value.replace( /&/g, "%26" ), function(d){
					alert(d);
				});
			}
			//window.setTimeout("getLocation()",10);
		</script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Настройки системы</div>
			<div style="width: 100%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Модификации</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<table><tbody>
					<?
						foreach($MODs as $tmp){
							echo "<tr><td><b>".$tmp->Name."<b></td><td><i>".$tmp->Type->web()."<i></td><td><a href=\"michomemodule.php?mod=".$tmp->Name."\" class=\"linkMain\">Настройки мода</a></td></tr>";
						}
					?>
					
					</table></tbody>
				</div>
			</div>
			<div style="width: 100%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Настройки Michome</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<p><a href="room.php?setting=1">Настройки комнат</a></p>
					<p><a href="webpagesettings.php?type=M">Настройки главной страницы</a></p>
					<p><a href="notificationsettings.php">Настройка уведомлений</a></p>
					<p><a href="botsettings.php">Настройка ботов</a></p>
					<br />
					<table><tbody>
					<?
						foreach(array_filter($API->GetAllSetting(), function($v, $k) {
									return !IsStr($v->Name, "_");
								}, ARRAY_FILTER_USE_BOTH) as $tmp){
							echo "<tr><td class='roomLink'><b>".$tmp->Name."</b></td><td> - </td><td class='roomLink'>".$tmp->Desc."</td><td> - </td><td><input class='settingValue' name='".$tmp->Name."' id='settingValue".$tmp->ID."' type='text' value='".$tmp->Value."' /></td><td> <input class='sb' value='Сохранить' type='button' onclick='saveSetting(".$tmp->ID.")' /></td></tr>";
						}
					?>
					</table></tbody>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	
