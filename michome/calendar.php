<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	$API = new MichomeAPI('localhost', $link);
	header("Michome-Page: Calendar-Page");

	$modulesOptions = "";
	
	foreach($API->GetAllModulesBD() as $tmp){
		$reqs = $API->GetDataForDay($tmp['ip'], date("Y-m-d"))->GetTypes("nonenull");
		if(count($reqs) > 0)
			$modulesOptions = $modulesOptions . "<option value='".$tmp['mid']."'>".$tmp['mid']."</option>";
	}
	
	$presetOptions = "";
	$results = mysqli_query($link, "SELECT * FROM `calendarPresets`");//Жестко качаем все из БД
	while($row = $results->fetch_assoc()) {
		$presetOptions = $presetOptions . "<option value='".$row['Module'].";".$row['Type']."'>".$row['Name']."</option>";
	}
?>
<!Doctype html>
<html>
	<head>
		<title>Управление Michome. Календарь информации</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
		<script>
			const GraphicsWidth = 640;
			const GraphicsHeight = 425;
			
			function GenerateDataType(mID){
				DateTypem.innerHTML = "";
				postAjax('api/getdata.php?device='+mID+'&cmd=unique&filter=nonenull&view=json', "GET", "", function(d){
					var lines = JSON.parse(d);
					for(var i = 0; i < lines.data.length; i++){
						if(lines.data[i] != "id" && lines.data[i] != "ip" && lines.data[i] != "data" && lines.data[i] != "type" && lines.data[i] != "date"){
							let op = document.createElement('option');
							op.innerHTML = lines.data[i];
							op.value = lines.data[i];
							document.getElementById("DateTypem").append(op);
						}						
					}								
				});
			}
			
			function downloadCSV(){
				var csvURL = document.getElementById('img1').getAttribute("csv");
				postAjax(csvURL, "GET", "", function(d){
					downloadAsFile(d, "text/csv", "MichomeCalendar.csv");
				});				
			}
			
			function downloadMAP(){
				var mapURL = document.getElementById('img1').getAttribute("map");
				postAjax(mapURL, "GET", "", function(d){
					downloadAsFile(d, "application/json", "MichomeCalendarMap.json");
				});				
			}
			
			function Graphics(typeG, value){
				vibday.value = value;
				
				var device, typeD;
				if(PresetDat.value != ""){ //Берем из пресета
					device = PresetDat.value.split(';')[0];
					typeD = PresetDat.value.split(';')[1];
				}
				else if(DevD.value != "" && DateTypem.value != ""){ //берем из ручных параметров
					device = DevD.value;
					typeD = DateTypem.value;
				}
				else{
					return false;
				}
				
				infacts.href = "facts.php?module="+device+"&type="+typeD+"&year="+value.split('-')[0]+"&mounth="+value.split('-')[1];
				
				if(typeG == "selday"){		
					GenerateGraphicsForDay(device, typeD, value);
				}
			}
			
			function GenerateGraphicsForDay(device, typeD, date){
				//p-11-13-2018 18-40
				
				postAjax('api/timeins.php?device='+device+'&type=selday&date='+date, "GET", "", function(d){
					var filter = usred.checked ? "median" : "none";
					var arr = d.split(';');
					document.getElementById('img1').src = "grafick.php?width="+GraphicsWidth+"&height="+GraphicsHeight+"&ip="+device+"&type="+typeD+"&period="+arr[2]+"&start="+arr[0]+"&filter="+filter;
					document.getElementById('img1').setAttribute("csv", "grafick.php?mode=csv&width="+GraphicsWidth+"&height="+GraphicsHeight+"&ip="+device+"&type="+typeD+"&period="+arr[2]+"&start="+arr[0]+"&filter="+filter);
					LoadMapGraphics("type="+typeD+"&period="+arr[2]+"&start="+arr[0], device);
				});							
			}
			
			function LoadMapGraphics(resp, ip){
				var filter = usred.checked ? "median" : "none";
				var mapPath = 'grafick.php?width='+GraphicsWidth+'&height='+GraphicsHeight+'&mode=map&'+resp+'&ip='+ip+"&filter="+filter;
				
				document.getElementById('img1').setAttribute("map", mapPath);
				postAjax(mapPath, "GET", "", function(d){
					
					while (maps.firstChild) {
						maps.removeChild(maps.firstChild);
					}
					var json = JSON.parse(d);
					//alert(json[0][0]);
					if(json["responce"] == "error"){
						Setn(json["text"]);
						return false;
					}
					for(var i=0; i < json["data"].length; i++){
						var str = json["data"][i];
						var radius = 5;
						var x = str.split(';')[0];
						var y = str.split(';')[1];
						///////
						var area = document.createElement('area');
						area.shape = "circle";
						area.coords = x+","+y+","+radius;
						area.target = "_blank";
						area.alt = "hyacinth";
						area.setAttribute("onclick", "Setn('"+str.split(';')[2]+" было "+str.split(';')[3]+"')");
						area.style.cursor = 'grab';
						maps.appendChild(area);
						
					}
					Setn("Что когда было");
				});
			}
			
			function LoadPreset(pres){
				Graphics('selday', vibday.value);
			}
			
			function Setn(id){
				temphist.innerHTML = "  "+id;
			}
			
			function LoadPage(){
				usred.checked = getcookie("UsredCalendar") == "on";
				
				vibday.value = CurrentDate();
				if(PresetDat.value != "")
					Graphics('selday', vibday.value);
				GenerateDataType(DevD.value);							
			}
			
			window.setTimeout("LoadPage()", 10);
		</script>
	</head>
	<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Календарь информации</div>
			<div style="width: 98%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">Настройки календаря</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<table><tbody>
						<tr><td><b>Выберите пресет данных: </b></td> <td><select id="PresetDat" onchange="extData.style.display = 'none'; LoadPreset(this.value);"><? echo $presetOptions; ?></select></td></tr>
						<tr><td><a href="#" onclick="CloseOpenTable(extData); return false;">Или укажите тип данных вручную</a></td></tr>
						<tr id='extData' style='display: none'><td><b>Выберите модуль: </b></td><td><select id="DevD" onchange="GenerateDataType(this.value)"><? echo $modulesOptions; ?></select></td><td><b>Выберите тип данные для построения графика: </b></td><td><select id="DateTypem" onchange="PresetDat.selectedIndex = -1; Graphics('selday', vibday.value)"></select></td></tr>
						<tr><td><b>Укажите за какую дату вывести данные: </b></td> <td><input onchange="Graphics('selday', this.value)" type="date" id='vibday' /></td> <td><input onclick="Graphics('selday', '<? echo date("Y-m-d"); ?>')" value="За сегодня" type="button" /></td></tr>
						<tr></tr>
						<tr><td><b><input onclick="Graphics('selday', vibday.value); setcookie('UsredCalendar', this.checked ? 'on' : 'off', 9999999999);" id="usred" type="checkbox" /> <label for="usred">Включить фильтр данных</label></b></td></tr>
						<tr></tr>
						<tr><td><a id="infacts" href="facts.php">Интересные факты</a></td></tr>
					</table></tbody>
				</div>
			</div>
			<div style="width: 98%;" class = "components">
				<div style="width: 100%; padding-left: 15px; padding-top: 8px;" class = "components_title">График данных</div>
				<div style="height: 100%; padding-left: 15px; padding-top: 0px;" class = "components_text">
					<p><a href="#" onclick="downloadCSV(); return false;">Скачать CSV график</a> <a href="#" onclick="downloadMAP(); return false;">Скачать JSON карту графика</a><p>
					<p class="temphis" id="temphist">Что когда было<p>
					<table>
						<tbody>
							<tr>
								<td><p><img class="graphics" id="img1" usemap="#flowers"></img></p></td>
							</tr>
							<tr>
								<td><map id="maps" name="flowers"></map></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?> 
	</body>
</html>	
