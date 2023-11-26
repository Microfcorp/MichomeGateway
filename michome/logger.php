<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
$API = new MichomeAPI('localhost', $link);
header("Michome-Page: Logger-Page");
$countfrompage = 35;

$resultsall = mysqli_query($link, "SELECT COUNT(`id`) FROM logging WHERE 1")->fetch_assoc()['COUNT(`id`)'];

$page = !empty($_GET['p']) ? ($_GET['p'] * $countfrompage) : floor($resultsall/$countfrompage) * $countfrompage;

$results = mysqli_query($link, "SELECT * FROM logging WHERE `id` > ".$page . " AND `id` < " . ($page + $countfrompage));
$serv = [];
while($row = $results->fetch_assoc()) {
    $serv[] = Array($row['id'],$row['ip'],$row['type'],$row['rssi'],$row['log'],$row['date']);
}

//$serv = array_reverse($serv);

function GetIPName($ip){
    if($ip == "localhost"){
        return "Малинка";
    }
    elseif($ip == "192.168.1.10"){
        return "Модуль сбора информации";
    }
    elseif($ip == "192.168.1.11"){
        return "Модуль уличного термометра";
    }
    elseif($ip == "192.168.1.12"){
        return "Модуль 'Царского света'";
    }
    elseif($ip == "192.168.1.13"){
        return "Модуль информетра";
    }
    elseif($ip == "192.168.1.14"){
        return "Модуль HDC1080";
    }
    elseif($ip == "192.168.1.34"){
        return "Модуль освещения";
    }
}
?>
<!Doctype html>
<html>
	<head>
		<title>Настройки</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>      
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
                                        <td class='logcell'><b>IP</b></td>
                                        <td class='logcell'><b>Тип</b></td>
                                        <td class='logcell'><b>RSSI</b></td>
                                        <td class='logcell'><b>Сообщение</b></td>
                                        <td class='logcell'><b>Дата</b></td>
                                    </tr>
                                    <?
                                        for($i = 0; $i < $countfrompage && $i < count($serv); $i++){
                                            echo "<tr>";
                                            echo "<td class='logcell'><b>".$serv[$i][0]."</b></td>";
                                            echo "<td class='logcell' title='".GetIPName($serv[$i][1])."'>".$serv[$i][1]."</td>";
                                            echo "<td class='logcell'>".$serv[$i][2]."</td>";
                                            echo "<td class='logcell'>".$serv[$i][3]."</td>";
                                            echo "<td class='logcell'>".$serv[$i][4]."</td>";
                                            echo "<td class='logcell'>".$serv[$i][5]."</td>";
                                            echo "</tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <a href="logger.php?p=<? echo floor($page/$countfrompage - 1); ?>"><<</a>
                            <a href="logger.php?p=<? echo floor($page/$countfrompage + 1); ?>">>></a>
                            <br />
                            <a href="logger.php?p=<? echo floor($resultsall/$countfrompage); ?>">Последняя</a>
                            <a href="logger.php?p=<? echo (0); ?>">Первая</a>
                            
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
