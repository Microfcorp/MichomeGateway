<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	$API = new MichomeAPI('localhost', $link);
	
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$enable = $_GET['enable'] == "true" ? '1' : '0';
		
		mysqli_query($link, "UPDATE `UsersVK` SET `Enable`='$enable' WHERE `ID`=".$id);
		exit("OK");
	}

	function MessangerP($t){
		if($t == "VK"){
			return "ВКонтакте";
		}
		elseif($t == "TG"){
			return "Телеграмм";
		}
	}
	function GroupP($t){
		if($t == "all"){
			return "Все";
		}
		elseif($t == "general"){
			return "Основные";
		}
	}
	
	header("Michome-Page: NotificationSettings");   
?>
<!Doctype html>
<html>
	<head>
		<title>Настройка уведомлений</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
		<script>			
			function Edit(id, state){
				postAjax('?id='+id+"&enable="+state, "GET", "", function(d){});
			}
		</script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Настройка уведомлений</div>
			<div style="width: 100%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Настройка уведомлений</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<table cellspacing="0" cellpadding="4"><tbody>
					<tr><td><b>ID пользователя</b></td><td></td><td><b>Группа уведомлений</b></td><td></td><td><b>Мессенджер</b></td><td></td><td><b>Состояние</b></td></tr>
					<?
						$results = mysqli_query($link, "SELECT * FROM `UsersVK` WHERE 1");
						while($row = $results->fetch_assoc()) {
							echo "<tr>
							<td><i>".$row['ID']."</i></td>
							<td></td>
							<td>".GroupP($row['Type'])."</td>
							<td></td>
							<td>".MessangerP($row['Messanger'])."</td>
							<td></td>
							<td><input onchange='Edit(".$row['ID'].", this.checked);' style='width: 30px'" . ($row['Enable']=="1" ? "checked" : "") . " type='checkbox'></input></td>
							</tr>";
						} 
					?>
					</table></tbody>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	