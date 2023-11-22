<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	$API = new MichomeAPI('localhost', $link);
	
	$typePage = isset($_GET['type']) ? $_GET['type'] : 'M';
	
	if(isset($_GET['action']) && $_GET['action'] == "new"){
		$API->AddWebPage($typePage);
		exit("OK");
	}
	if(isset($_GET['action']) && $_GET['action'] == "remove"){
		$API->RemoveWebPage(intval($_GET['id']));
		exit("OK");
	}
	
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$name = $_GET['name'];
		$subtype = $_GET['subtype'];
		$value = $_POST['value'];
		$newid = $_GET['newid'];
		$API->SetWebPage($id, $subtype, $name, $value, $newid);
		exit("OK");
	}	
	
	header("Michome-Page: WebPageSettings-Page");   
?>
<!Doctype html>
<html>
	<head>
		<title>Настройки веб страниц</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
		<script>			
			function saveSetting(id){
				var subtype = document.getElementById('pageSub'+id).value;
				var name = document.getElementById('pageName'+id).value;
				var value = document.getElementById('pageValue'+id).value;
				var num = document.getElementById('pageNum'+id).value;
				postAjax('webpagesettings.php?id='+ id + '&name=' + name + '&subtype=' + subtype + '&type=<? echo $typePage; ?>' + '&newid=' + num, "POST", "value="+value.replace( /&/g, "%26" ), function(d){
					alert(d);
					if(num != id){
						document.location.reload();
					}
				});
			}
			function addNew(){
				postAjax('webpagesettings.php?action=new' + '&type=<? echo $typePage; ?>', "POST", "", function(d){
					document.location.reload();
				});
			}
			function removeSettings(id){
				postAjax('webpagesettings.php?action=remove' + '&type=<? echo $typePage; ?>&id=' + id, "POST", "", function(d){
					document.location.reload();
				});
			}
		</script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Настройки веб страниц</div>
			<div style="width: 100%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Настройки страницы</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<table cellspacing="0" cellpadding="4"><tbody>
					<tr><td><b>ID</b></td><td></td><td><b>Тип</b></td><td></td><td><b>Имя</b></td><td></td><td><b>Значение</b></td></tr>
					<?
						$page = $API->GetWebPagesFromType($typePage)->WebPages();
						foreach($page as $tmp){
							echo "<tr><td><input style='width: 50px;' id='pageNum".$tmp->ID."' type='number' value='".$tmp->ID."' /></td><td> - </td><td><select id='pageSub".$tmp->ID."'><option ".($tmp->SubType == "TextValue" ? "selected" : "")." value='TextValue'>Текстовое значение</option><option ".($tmp->SubType == "SpanValue" ? "selected" : "")." value='SpanValue'>Спановое значение</option><option ".($tmp->SubType == "HeaderValue" ? "selected" : "")." value='HeaderValue'>Заголовочное значение</option></select></td><td> - </td><td><input id='pageName".$tmp->ID."' type='text' value='".$tmp->Name."' /></td><td> - </td><td><input style='width: 600px;' id='pageValue".$tmp->ID."' type='text' value='".$tmp->Value."' /></td> <td><input value='Сохранить' type='button' onclick='saveSetting(".$tmp->ID.")' /></td> <td><input value='Удалить' type='button' onclick='removeSettings(".$tmp->ID.")' /></td></tr>";
						}
					?>
					<tr><td></td><td></td><td><a href="#" onclick="addNew();">Вставить новую</a></td></tr>
					</table></tbody>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	