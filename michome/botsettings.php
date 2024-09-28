<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	$API = new MichomeAPI('localhost', $link);
	
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		if(empty($_GET['name'])){
			mysqli_query($link, "DELETE FROM `botcmd` WHERE `ID`=".$id);
			exit("OK");
		}
		$name = $_GET['name'];
		$desc = $_GET['desc'];
		$cmd = $_GET['cmd'];
		$en = $_GET['en'];
		
		mysqli_query($link, "UPDATE `botcmd` SET `Name`='$name', `Desc`='$desc', `Cmd`='$cmd', `Enabled`='$en' WHERE `ID`=".$id);
		setCommand();
		exit("OK");
	}
	elseif(isset($_GET['type']) && $_GET['type'] == "add"){
		mysqli_query($link, "INSERT INTO `botcmd`(`Name`, `Desc`, `Cmd`) VALUES ('new','','')");
		exit("OK");
	}
	
	header("Michome-Page: BotSettings");   
?>
<!Doctype html>
<html>
	<head>
		<title>Настройка комманд бота</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
		<script>			
			function Save(id){
				var name = document.getElementById("name"+id).value;
				var desc = document.getElementById("desc"+id).value;
				var cmd = document.getElementById("cmd"+id).value.replace( /&/g, "%26" );
				var en = (document.getElementById("enabled"+id).checked ? "1" : "0");
				
				postAjax('?id='+id+"&name="+name+"&desc="+desc+"&cmd="+cmd+"&en="+en, "GET", "", function(d){alert("OK");});
			}
			
			function Add(id){			
				postAjax('?type=add', "GET", "", function(d){location.reload();});
			}
			
			function Delete(id){			
				postAjax('?id='+id, "GET", "", function(d){location.reload();});
			}
		</script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Настройка комманд бота</div>
			<div style="width: 100%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Настройка комманд бота</div>
				<div style="width: max-content; height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<table class="tablePage" cellspacing="0" cellpadding="4"><tbody>
						<tr class="scenesH">
							<td><b>Состояние</b></td>
							<td></td>
							<td><b>Комманда</b></td>
							<td></td>
							<td><b>Описание</b></td>
							<td></td>
							<td><b>Действие</b></td>
							<td></td>
							<td></td>
						</tr>
						<?
							$results = mysqli_query($link, "SELECT * FROM `botcmd` WHERE 1");
							while($row = $results->fetch_assoc()) {//$row['ID']
								echo "<tr>
								<td class='scenesT'><div class='checkbox-toggle'><input class='en' type='checkbox' ".($row['Enabled'] == '1' ? "checked" : "")." id='enabled".$row['ID']."' /><label for='enabled".$row['ID']."' class='CToggle'><span></span></label></div></td>							
								<td></td>
								<td class='scenesT'><input type='text' value='".$row['Name']."' id='name".$row['ID']."' /></td>
								<td></td>
								<td class='scenesT'><input style='width: 300px;' type='text' value='".$row['Desc']."' id='desc".$row['ID']."' /></td>
								<td></td>
								<td class='scenesT'><textarea style='width: 400px;' id='cmd".$row['ID']."'>".$row['Cmd']."</textarea></td>
								<td></td>
								<td class='scenesT'><input type='button' onclick='Save(".$row['ID'].")' value='Сохранить' /> <input type='button' onclick='Delete(".$row['ID'].")' value='Удалить' /></td>
								</tr>";
							} 
						?>
					</tbody></table>
					<p><a href="#" onclick="Add();">Добавить новый</a></p>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	