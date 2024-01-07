<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
header("Michome-Page: Mod-Settings"); 
$API = new MichomeAPI('localhost', $link);  

$mod = $_GET['mod'];

$modClass = $MODs[$mod];

if(isset($_GET['edit'])){
	$id = $_GET['id'];
	$name = $_POST['name'];
	$value = $_POST['value'];
	$modClass->GetParamByID($API, $id)->SetValue($name, $value);
	exit("OK");
}	
elseif(isset($_GET['add'])){
	$pt = $_GET['paramtype'];
	$modClass->AddParam($API, $pt);
	exit("OK");
}
elseif(isset($_GET['remove'])){
	$id = $_GET['id'];
	$modClass->RemoveParam($API, $id);
	exit("OK");
}

//if(isset($modClass->Install))
	$modClass->Install($API);
?>
<!Doctype html>
<html>
	<head>
		<title>Управление Michome. Настройки мода</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script> 
		<script>
			function saveSetting(id){
				var value = document.getElementById('settingValue'+id).value;
				postAjax('settings.php?id='+ id, "POST", "value="+value.replace( /&/g, "%26" ), function(d){
					alert(d);
				});
			}
			function saveParam(id, name, val){
				postAjax('michomemodule.php?edit=1&id='+ id+"&mod=<?echo $mod;?>", "POST", "name="+name.replace( /&/g, "%26" )+"&"+"value="+val.replace( /&/g, "%26" ), function(d){
					alert(d);
				});
			}
			function addParam(paramtype){
				postAjax('michomemodule.php?add=1&paramtype='+ paramtype+"&mod=<?echo $mod;?>", "POST", "", function(d){
					document.location.reload();
					alert(d);
				});
			}
			function removeParam(id){
				postAjax('michomemodule.php?remove=1&id='+ id+"&mod=<?echo $mod;?>", "POST", "", function(d){
					document.location.reload();
					alert(d);
				});
			}
		</script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Настройки мода <i><? echo $mod; ?></i></div>
			<div style="width: 100%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Общие настройки</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<? if($modClass->Type == MichomeModuleType::ModuleCore){?>
						<p><i>Данный модуль добавляет поддержку нового утройства в среду Michome</i></p>
						<p></p>
						<p><? echo $modClass->BaseClass->DescModule ?></p>
					<? }?>
					<? if($modClass->Type == MichomeModuleType::Extension){?>
						<p><i>Данный модуль добавляет поддержку корневого расширения Michome</i></p>
						<br />
						<p><? echo $modClass->BaseClass->DescModule ?></p>
						<?
							foreach($modClass->BaseClass->MichomePage as $tmp){
								echo "<p><a href='".parseMichomURI($tmp[0], $modClass->BaseClass)."'>".$tmp[1]."</a></p>";
							}
						?>
						<br />
						<?
							if($modClass->BaseClass->SettingsFunction)
								$modClass->BaseClass->SettingsFunction->call($API, $modClass);
						?>
						<br /><br />
						<table><tbody>
						<?
							foreach($modClass->GetAllSettings($API) as $tmp){
								echo "<tr><td class='scenesT'><b>".$tmp->Name."</b></td><td class='scenesT'> - </td><td class='scenesT'>".$tmp->Desc."</td><td class='scenesT'> - </td><td><input name='".$tmp->Name."' id='settingValue".$tmp->ID."' type='text' value='".$tmp->Value."' /></td><td class='scenesT'> <input value='Сохранить' type='button' onclick='saveSetting(".$tmp->ID.")' /></td></tr>";
							}
						?>
						</table></tbody>
					<? }?>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	
