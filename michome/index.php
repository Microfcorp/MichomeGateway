<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?	
$API = new MichomeAPI('localhost', $link);

header("Michome-Page: Main-Page");
    
function scandir_by_mtime($folder) {
  $dircontent = scandir($folder);
  $arr = array();
  foreach($dircontent as $filename) {
    if ($filename != '.' && $filename != '..') {
      if (filemtime($folder.$filename) === false) return false;
      $dat = filemtime($folder.$filename);
      $arr[$dat] = $filename;
    }
  }
  if (!ksort($arr)) return false;
  return $arr;
}

 /*$dir = __DIR__."//../site/image/graphical/";
 $files = array();
 foreach (scandir($dir) as $file) $files[$file] = filemtime("$dir/$file");
 asort($files);
 $files = array_keys($files);

$lastfile = $files[count($files)-2];*/

?>
<!Doctype html>
<html>
	<head>
		<title>Управление Michome</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript">
            function GetData()
            {
				// получаем индекс выбранного элемента
				var selind = document.getElementById("select").options.selectedIndex;
				var txt= document.getElementById("textcmd").value;
				var val= document.getElementById("select").options[selind].value;

				document.getElementById("cmdresult").innerHTML = "Отправка данных: " + txt + " На: " + val + ". Пожалуйста подождите..."; //log

				postAjax('api/setcmd.php?device='+ val +'&cmd='+ txt.replaceAll( /&/g, "%26" ), "POST", "", function(d){document.getElementById("cmdresult").innerHTML = d;
					window.setTimeout(function(){document.getElementById("cmdresult").innerHTML = ""; /*m_SendCmd.style.display = 'none';*/},6000);
				});              
            }                               
		   
			function time(){		
				window.setTimeout("time()",1000);
				document.getElementById('datetime').innerHTML = "Текущее время: " + CurrentTime();
			}		
		
			window.setTimeout("time()",1);
		</script>
		<?
			$page = $API->GetWebPagesFromType('M')->SortSubType('HeaderValue')->WebPages();
			foreach($page as $tmp)
			{
				$textV = $API->GetConstant($tmp->Value);
				$postle = $API->GetWebConstant($textV);
				echo $postle.PHP_EOL;
			}
		?>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Главная</div>
			<div class = "com">
                <div style="width: 100%; height: 100%;" class = "components">
                
					<div class="padding: 15px;" class = "components_alfa">
						<!--<div class = "components_title">192.168.45.201 - Что-то тут интересное...</div>-->
						<div class = "components_text">                       
                            <div style="float: right;">
                                <p id='datetime'>Текущая дата</p>                                                           
                            </div>                           
							<div class="SendCmd">
								<p><a href="#" onclick="CloseOpen(m_SendCmd); return false;">Отправить комманду</a></p>                           
								<div style="padding: 4px; margin-top: 5px; display: none;" id="m_SendCmd">
									<p class="SendCmdF">Команда: <input style="width: 80%;" required type="text" name="cmd" id='textcmd' /></p>	  
									<p class="SendCmdF">На:  
									    <select style="width: 80%;" name="send" id='select'>
										    <option name='select' value="localhost">localhost</option>
										    <?php
												$results = mysqli_query($link, "SELECT ip, mID FROM modules");
												while($row = $results->fetch_assoc()) {
													if($row['ip'] != "" & $row['mID'] != "" & $row['ip'] != "localhost"){
														echo "<option name='select' value=".$row['mID'].">".$row['mID']." (".$row['ip'].")</option>";
													}
												}
										    ?>
									    </select>
									</p>
								   
									<p class="SendCmdF"><input class="SendCmdButton" name="sendcmd" value="Отправить" OnClick="GetData()" type="button" /></p>

									<div class="SendCmdF" style="background-color:#03899C;">
										<p style="color: #6ffff8; text-decoration: underline;">Состояние: </p>
										<span style="color: #6ffff8;" id="cmdresult"></span>
									</div>                                 
								</div>
							</div>
                            <br />
                            <div>
								<?
									$page = $API->GetWebPagesFromType('M')->WebPages();
									foreach($page as $tmp)
									{
										$textV = $API->GetConstant($tmp->Value);
										$postle = $API->GetWebConstant($textV);
										if($tmp->SubType == "TextValue")
											echo "<a name=\"".$tmp->Name."\" class=\"tooltip noselect DataV\"><p>".$postle."</p></a>".PHP_EOL;
										elseif($tmp->SubType == "SpanValue")
											echo "<span name=\"".$tmp->Name."\" class=\"noselect DataV\">".$postle."</span><br />".PHP_EOL;
									}								
								?>
                                                                
                                <? //echo($alarm);?>
							</div>                         
                        </div>
					</div>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?>       
	</body>
</html>	
