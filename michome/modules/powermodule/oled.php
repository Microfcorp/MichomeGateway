<?php include_once(__DIR__."//../../../site/mysql.php"); ?>
<?php include_once(__DIR__."//../../../site/secur.php"); ?>
<?php
require_once("../../lib/michom.php");
header("Michome-Page: API-Service");
header("Michome-API: GETDevice");

$API = new MichomeAPI('localhost', $link); 

$module = "";

if(isset($_GET['module'])){
	$module = $API->GetIDModule($_GET['module']);
}

?>
<html>
<head>
<title>Управление термометром</title>
<link rel="stylesheet" type="text/css" href="/michome/styles/style.css"/>
<script type="text/javascript" src="/site/MicrofLibrary.js"></script>
<script type="text/javascript" src="/michome/libmichome.js"></script>
<script type="text/javascript">	
	var moduleAddress = "<? echo $module; ?>";
	
	function saveSetting(id){
		var value = document.getElementById('settingValue'+id).value;
		postAjax('/michome/settings.php?id='+ id, "POST", "value="+value.replace( /&/g, "%26" ), function(d){
			asyncAlert(d);
		});
	}
	function saveParam(id, name, val){
		postAjax('/michome/michomemodule.php?edit=1&id='+ id+"&mod=OLEDModule", "POST", "name="+name.replace( /&/g, "%26" )+"&"+"value="+val.replace( /&/g, "%26" ), function(d){
			asyncAlert(d);
			postAjax('/michome/api/setcmd.php?device='+moduleAddress+'&cmd=refresh&timeout=5000', "GET", "", function(d){});
		});
	}
	function addParam(paramtype){
		postAjax('/michome/michomemodule.php?add=1&paramtype='+ paramtype+"&mod=OLEDModule", "POST", "", function(d){
			document.location.reload();
			asyncAlert(d);
		});
	}
	function removeParam(id){
		postAjax('/michome/michomemodule.php?remove=1&id='+ id+"&mod=OLEDModule", "POST", "", function(d){
			document.location.reload();
			asyncAlert(d);
		});
	}
	function LoadModuleParams(){
		postAjax('/michome/api/setcmd.php?device='+moduleAddress+'&cmd=oled?type=get&timeout=5000&apitype=json', "GET", "", function(d){
			var req = JSON.parse(d);
			if(req['status'] == true){
				var params = req["response"].split(";");
				var maximumPage = params[5];
				var usepage = <? echo count($OLEDModuleModule->GetParamsType($API, $module)); ?>;
				document.getElementById('maxPage').innerHTML = maximumPage;
				if(usepage >= maximumPage){
					document.getElementById('addBT').disabled = true;
					document.getElementById('addBT').title = "Превышен лимит доступных страниц для данного модуля";
				}
			}
			else{
				document.getElementById('maxPage').innerHTML = "(Модуль недоступен)";
			}
		});
	}
	window.setTimeout("LoadModuleParams()", 1);
  </script>
</head>
<body>
	<div class = "body_alfa"></div>
	<div class = "body">
		<div class = "title_menu">Управление Michome. Конфигурация OLED модуля</div>
			<div style="width: 100%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Общие настройки</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<p>Страницы для отображения на модуле:</p>
					<p>Используется страниц <? echo count($OLEDModuleModule->GetParamsType($API, $module)); ?>/<span id='maxPage'>0</span></p>
					<table>
						<tr>
							<td><b>Название страницы</b></td>
							<td><b>Текст страницы</b></td>
						</tr>
						<?
							foreach($OLEDModuleModule->GetParamsType($API, $module) as $tmp){
								echo "<tr><td><input required maxlength='40' id='name".$tmp->ID."' style='width:150px;height:20px;' type='text' value='".$tmp->ParamName."'></td><td><input required maxlength='80' id='val".$tmp->ID."' style='width:500px;height:20px;' type='text' value='".$tmp->ParamValue."'></td><td><input type='button' onclick='saveParam(".$tmp->ID.", name".$tmp->ID.".value, val".$tmp->ID.".value)' value='Сохранить'></td><td><input onclick='removeParam(".$tmp->ID.")' type='button' value='Удалить'></td></tr>";
							}
						?>
						<tr><td><input id="addBT" onclick='addParam(moduleAddress)' type='button' value='Добавить'></td></tr>
					</table>
				</div>
			</div>               
	</div>	
	<?php require_once(__DIR__."//../../../site/verhn.php");?>        
</body>

</html>
