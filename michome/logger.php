<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
$API = new MichomeAPI('localhost', $link);
header("Michome-Page: Logger-Page");

$resultsall = mysqli_query($link, "SELECT COUNT(`id`) AS `CNT` FROM logging")->fetch_assoc()['CNT'];

//478500

$maxPage = ceil($resultsall/StandartCountPromPage);
$page = !empty($_GET['p']) ? ($_GET['p']) : (1);

$results = mysqli_query($link, "SELECT * FROM logging ORDER BY `id` DESC LIMIT ".StandartCountPromPage." OFFSET ".(($page - 1)*StandartCountPromPage));
$serv = [];
while($row = $results->fetch_assoc()) {
    $serv[] = Array($row['id'],$row['ip'],$row['type'],$row['rssi'],$row['log'],$row['date']);
}

$modulesBD = $API->GetAllModulesBD();

function GetIPName($id){
	global $API;
	global $modulesBD;
	
    foreach($modulesBD as $module){
		$idBD = $module['mid'];
		$typeBD = $module['type'];
		if($id == $idBD){
			$moduleDesc = $API->GetModuleInfoFromType($typeBD);
			return ($moduleDesc ? $moduleDesc->Descreption : "Описание данного модуля отсутствует");
		}
	}
	return "Описание данного модуля отсутствует";
}
function GetTextLog($text){
	if(IsStr($text, "Text=")){
		$params = ParseParameters($text);
		return $params['Text'];
	}
	
	return $text;
}
?>
<!Doctype html>
<html>
	<head>
		<title>Просмотр логов</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>  
		<style>
			.tooltip-span {
				display: inline-block;
				max-width: 200px; /* ширина в которой будет обрезаться текст */
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis; /* многоточие */
				vertical-align: bottom;
				cursor: pointer;
			}
			/* стиль контейнера */
			.tooltip-container {
				position: relative;
				display: inline-block;
				max-width: 350px; /* ширина обрезки */
			}

			/* сам текст */
			.tooltip-text {
				display: inline-block;
				white-space: nowrap;
				max-width: 350px;
				overflow: hidden;
				text-overflow: ellipsis;
				cursor: pointer;
			}

			/* скрытая плашка */
			.tooltip-box {
				visibility: hidden;
				opacity: 0;
				width: max-content;
				max-width: 600px;
				background-color: #333;
				color: #fff;
				text-align: left;
				border-radius: 8px;
				padding: 10px;
				position: absolute;
				z-index: 10;
				bottom: 100%; /* появляется сверху */
				left: 0%;
				transform: translateX(-50%) translateY(-8px);
				transition: opacity 0.3s, visibility 0.3s;
			}

			/* показать плашку при наведении */
			.tooltip-container:hover .tooltip-box {
				visibility: visible;
				opacity: 1;
			}
		</style>		
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Логи</div>
			<div class = "com">
                <div style="width: 100%; height: 100%;" class = "components">
					<div class = "components_alfa">
						<div style="width: auto" class = "components_text">                      
                            <table style="width: 100%; text-align: start; padding: 0; margin: 0; font-size: large; display: inline-block;">
                                <tbody>
                                    <tr>
                                        <td class='logcell'><b>ID</b></td>
                                        <td class='logcell'><b>Тип модуля</b></td>
                                        <td class='logcell'><b>Тип</b></td>
                                        <td class='logcell'><b>RSSI</b></td>
                                        <td class='logcell'><b>Сообщение</b></td>
                                        <td class='logcell'><b>Дата</b></td>
                                    </tr>
                                    <?
                                        for($i = 0; $i < StandartCountPromPage && $i < count($serv); $i++){
                                            echo "<tr>";
                                            echo "<td class='logcell'><b>".$serv[$i][0]."</b></td>";
                                            echo "<td class='logcell' style='color: orangered;' title='".GetIPName($serv[$i][1])."'>".$serv[$i][1]."</td>";
                                            echo "<td class='logcell'>".$serv[$i][2]."</td>";
                                            echo "<td class='logcell' style='color: blueviolet;'>".($serv[$i][3] == 0 ? '--' : $serv[$i][3])."</td>";
                                            echo "<td class='logcell' style='color: ghostwhite;'><div class='tooltip-container'><span class='tooltip-text'>".substr(GetTextLog($serv[$i][4]), 0, 100)."</span><div class='tooltip-box'>".GetTextLog($serv[$i][4])."</div></div></td>";
                                            echo "<td class='logcell'>".$serv[$i][5]."</td>";
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <a href="logger.php?p=<? echo ($page > 0) ? floor($page - 1) : 0; ?>"><<</a>
                            <a href="logger.php?p=<? echo ($page < $maxPage) ? floor($page + 1) : $maxPage; ?>">>></a>
                            <br />
							<a href="logger.php?p=<? echo (1); ?>">Первая</a>
                            <a href="logger.php?p=<? echo $maxPage; ?>">Последняя</a>                         
                        </div>
					</div>
				</div>               
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	
