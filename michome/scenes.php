<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	$API = new MichomeAPI('localhost', $link);

	$data = array();

	$datas = array();

	$results = mysqli_query($link, "SELECT * FROM `scenes`");
	while($row = $results->fetch_assoc()) {
		$data[] = array('ID'=>intval($row['ID']),
						'Name'=>($row['Name']),
						'TStart'=>($row['TStart']),
						'TEnd'=>($row['TEnd']),
						'Module'=>($row['Module']),
						'Data'=>($row['Data']),
						'NData'=>($row['NData']),
						'Timeout'=>($row['Timeout']),
						'Enable'=>($row['Enable']),
						);
		if(!in_array($row['Data'], $datas)) $datas[] = $row['Data'];
		if(!in_array($row['NData'], $datas)) $datas[] = $row['NData'];
	}

	header("Michome-Page: Scenes");
?>
<!Doctype html>
<html>
	<head>
		<title>Сценарии</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
        <script type="text/javascript">
			<?php
				echo "const allIDS = [";
				for($i=0; $i<count($data); $i++){
					$v = $data[$i];
					$id = $v['ID'];
					echo $id . ",";
				}
				echo "];";
			?>
				
			const TagDisabledStartTime = ["^sts;", "^sds;", "^sde;", "^bt"];
			const TagDisabledEndTime = ["^sts;", "^eds;", "^ede;", "^bt"];
			const TagDisabledNDataTime = ["^bt", "^cbp;", "^pbp;"];
			
			function UpdateVisible(id){ //Обновляет отображение активности блоков сценария
				var datas = document.getElementsByClassName('i_'+id);
                
                var na = datas[1].value;
                var ts = datas[2].value;
                var te = datas[3].value;
                var md = datas[4].value;
                var dt = datas[5].value.replace( /&/g, "%26" );
                var nt = datas[6].value.replace( /&/g, "%26" );
                var tm = datas[7].value;
                var en = datas[8].checked;
                var nu = datas[0].value;
				
				datas[2].disabled = IsStr(na, TagDisabledStartTime);
				datas[3].disabled = IsStr(na, TagDisabledEndTime);
				datas[6].disabled = IsStr(na, TagDisabledNDataTime);
			}
            function Edit(id){
                var datas = document.getElementsByClassName('i_'+id);
                
                var na = datas[1].value;
                var ts = datas[2].value;
                var te = datas[3].value;
                var md = datas[4].value;
                var dt = datas[5].value.replace( /&/g, "%26" );
                var nt = datas[6].value.replace( /&/g, "%26" );
                var tm = datas[7].value;
                var en = datas[8].checked;
                var nu = datas[0].value;
				
				UpdateVisible(id);
				
				postAjax('api/constants.php?type=validate&view=json&cmd='+na, "GET", "", function(d){
					var j = JSON.parse(d);
					if(j['isvalid'] != true){
						alert("Ошибка валидации констант в поле \"Наименование\"");
					}
				});
				postAjax('api/constants.php?type=validate&view=json&cmd='+dt, "GET", "", function(d){
					var j = JSON.parse(d);
					if(j['isvalid'] != true){
						alert("Ошибка валидации констант в поле \"Данные в периоде\"");
					}
				});
				postAjax('api/constants.php?type=validate&view=json&cmd='+nt, "GET", "", function(d){
					var j = JSON.parse(d);
					if(j['isvalid'] != true){
						alert("Ошибка валидации констант в поле \"Данные за периодом\"");
					}
				});
				
				if(te == "00:00"){
					datas[3].value = "23:59";
					te = "23:59";
				}
                
                postAjax('api/scenes.php?type='+'Edit'+'&id='+id+'&name='+na+'&ts='+ts+'&td='+te+'&module='+md+'&data='+dt+'&ndata='+nt+'&number='+nu+'&timeout='+tm+'&enable='+en, "POST", "", function(d){if(d != "OK")alert(d); if(nu!=id){document.location = document.location;}});
            }
            function Delet(id){
                postAjax('api/scenes.php?type='+'Remove'+'&id='+id, "POST", "", function(d){if(d == "OK") location.reload(); else alert("Ошибка: " + d);});
            }
            function Add(){
                postAjax('api/scenes.php?type='+'Add', "POST", "", function(d){if(d == "OK") location.reload(); else alert("Ошибка: " + d);});
            }
			function Run(id){
                postAjax('api/scenes.php?type='+'Run'+'&id='+id+"&typerun="+'0', "POST", "", function(d){if(d != "") alert(d); else alert("OK");});
            }
			function Check(id){
				var datas = document.getElementsByClassName('i_'+id);
				var dt = datas[5].value.replace( /&/g, "%26" );
                postAjax('api/constants.php?type=run&view=json&cmd='+dt, "POST", "", function(d){
					var j = JSON.parse(d);
					if(j['isvalid'] != true){
						alert("Ошибка валидации констант");
					}
					else{
						alert(j['str']);
					}
				});
            }
            function showConst(){
                if(sconst.style.display == "block") sconst.style.display = "none";
                else sconst.style.display = "block";
				return false;
            }
			function UpdateVisibleAll(){
				for(var i = 0; i < allIDS.length; i++){
					UpdateVisible(allIDS[i]);
				}
			}
			window.setTimeout("UpdateVisibleAll()", 10);
        </script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Сценарии</div>
			<div class = "com">
                <div class = "components">
					<div class = "components_alfa">
						<div class = "components_title">
                        <p>Сценарии</p>
                        <p><a href="#" onclick="return showConst();">Расшифровка констант:</a></p>
                        <div style="display: none" id="sconst">
                            <p> ^sds; - Начало выполнения по рассвету</p>
                            <p> ^sde; - Начало выполнения по закату</p>
                            <p> ^eds; - Конец выполнения по рассвету</p>
                            <p> ^ede; - Конец выполнения по закату</p>
                            <p> ^nos; - Не проверять, были ли отправлены данные</p>
                            <p> ^ons; - Выполнить только единожды в сутки</p>
                            <p> ^sts; - Выполнять при запуске системы</p>
                            <p> ^cbp; - Только в действиях с кнопкой! Заменяется на количество нажатий</p>
                            <p> ^pbp; - Только в действиях с кнопкой! Заменяется на нажатый пин</p>
                            <p> ^cs_14; - Если был выполнен сценарий {14}</p>
                            <p> ^en_14; - Если включен сценарий {14}</p>
                            <p> ^rm_192.168.1.11_Temp; - Заменяет на прочитанные данные из модуля</p>
                            <p> ^rmavg_192.168.1.11_Temp_5; - Заменяет на усредненное количество {5} прочитанных данных из модуля</p>
                            <p> ^rmamp_192.168.1.11_Temp_5h; - Заменяет на амплитуду за {5} часов</p>
                            <p> ^sn_all_Привет, мир!; - Отправляет уведомление категории all с текстом "Привет, мир!"</p>
                            <p> ^if_(0)>30; - Выполнить если {0} {>} {30}. (> < == !=)</p>
                            <p> ^bt_192.168.1.34_5_1_1; - Выполнять при нажатии на кнопку на модуле {192.168.1.34}, с пином {5}, нажатие {1} раз, истина(1) или ложь(0)</p>
                            <p> ^bt_192.168.1.34_5; - Выполнять при нажатии на кнопку на модуле {192.168.1.34}, с пином {5}</p>
                            <p> ^bt_192.168.1.34; - Выполнять при нажатии на кнопку на модуле {192.168.1.34}</p>                 
							<?php
								$lastgr = "";
								foreach($API->constantAction as $tmp){
									if($tmp[0] != $lastgr){
										$lastgr = $tmp[0];
										echo "<p style='color:#ff6b6b;'> $tmp[0]:</p>";
									}
									echo "<p> $tmp[2]</p>";
								}
							?>
							<p>Запрещено использовать спецсимволы - '^', ';', '_'</p>
                        </div>
                        </div>
						<div style="width: max-content;" class = "components_text">
                            <input class="sb" type="button" onclick="Add();" value="Добавить сценарий"></input>
                            <table class="tablePage">
                                <tr class='scenesH'>
                                    <td><b>№.</b></td>
                                    <td><b>Наименование</b></td>
                                    <td><b>Старт</b></td>
                                    <td><b>Конец</b></td>
                                    <td><b>Модуль</b></td>
                                    <td><b>Данные в периоде</b></td>
                                    <td><b>Данные за периодом</b></td>
                                    <td><b>Тайм-аут</b></td>
                                    <td><b>Включен</b></td>
                                    <td><b>Действия</b></td>
                                </tr>
								<tr><td></td></tr>
								
                                <?
                                    for($i=0; $i<count($data); $i++){
                                        $v = $data[$i];
										$cl = (even($i+1) ? "isev" : "isnev");
                                        echo "<tr class='scenesT ".$cl."'>";
                                        echo "<td class='scenesT'><input onchange='Edit(".$v['ID'].");' style='width: 30px' class='i_".$v['ID']." nu' name='numberic' value='".$v['ID']."' type='number'></input></td>";
                                        echo "<td class='scenesT'><textarea onchange='Edit(".$v['ID'].");' class='i_".$v['ID']." na' style='width: 280px' name='nams' type=\"text\" placeholder='Название' value=\"".$v['Name']."\">".$v['Name']."</textarea></td>";
                                        echo "<td class='scenesT'><input onchange='Edit(".$v['ID'].");' class='i_".$v['ID']." ts' name='tstart' type=\"time\" value='".$v['TStart']."'></input></td>";
                                        echo "<td class='scenesT'><input onchange='Edit(".$v['ID'].");' class='i_".$v['ID']." td' name='tend' type=\"time\" value='".$v['TEnd']."'></input></td>";
                                        echo "<td class='scenesT'><input onchange='Edit(".$v['ID'].");' style='width: 100px' class='i_".$v['ID']." se' list='moduleslist' name='module' value='".$v['Module']."'></input></td>";
                                        echo "<td class='scenesT'><textarea onchange='Edit(".$v['ID'].");' class='i_".$v['ID']." da' name='data' placeholder=\"Данные во время периода\" value=\"".$v['Data']."\">".$v['Data']."</textarea></td>";
                                        echo "<td class='scenesT'><textarea onchange='Edit(".$v['ID'].");' class='i_".$v['ID']." nt' name='ndata' placeholder=\"Данные за периодом\" value=\"".$v['NData']."\">".$v['NData']."</textarea></td>";
                                        echo "<td class='scenesT'><input onchange='Edit(".$v['ID'].");' style='width: 45px' class='i_".$v['ID']." tm' name='timeout' value='".$v['Timeout']."' type='number'></input></td>";
										echo "<td class='scenesT'><div class='checkbox-toggle'>";
                                        echo "<input id='cbt-".$v['ID']."' onchange='Edit(".$v['ID'].");' class='i_".$v['ID']." en' name='enable' ".($v['Enable']=="1" ? "checked" : "")." type='checkbox'></input>";
                                        echo "<label for='cbt-".$v['ID']."' class='CToggle'><span></span></label></td></div>";
										echo "<td class='scenesT'><input type=\"button\" onclick='Delet(".$v['ID'].");' value=\"Удалить\" /> <input type=\"button\" onclick='Check(".$v['ID'].");' value=\"Проверить\" /> <input type=\"button\" onclick='Run(".$v['ID'].");' value=\"Выполнить\" /></td>";
                                        echo "</tr>";
                                    }
                                ?>
                                <datalist id="moduleslist">
									<?php
										$allModules = $API->GetAllModulesBD();
										foreach($allModules as $module){
											echo "<option value=".$module['ip'].">".$module['mid']."</option>";
										}
									?>
                                </datalist>
                            </table>
                        </div>
					</div>
				</div>
			</div>
		</div>            
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	
