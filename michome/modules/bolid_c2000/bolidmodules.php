<?php include_once(__DIR__."//..//..//../site/mysql.php"); ?>
<?php include_once(__DIR__."//..//..//../site/secur.php"); ?>
<?
$device = "";

if(isset($_GET['device'])){
	$device = $_GET['device'];
}

?>
<html>
<head>
<title>Управление энергомонитором</title>
<link rel="stylesheet" type="text/css" href="/michome/styles/style.css"/>
<script type="text/javascript" src="/site/MicrofLibrary.js"></script>
<script type="text/javascript" src="/michome/libmichome.js"></script>
<script type="text/javascript">	
	var deviceID = "<? echo $device; ?>";
	
	function CreateBolid(zone, num){
		
		if(document.getElementById("bolid_"+num)){
			let tr = document.getElementById("bolid_"+num);
			tr.childNodes[0].children[0].children[0].children[0].innerHTML = zone['zone'] + " ";
			tr.childNodes[0].children[0].children[1].children[0].innerHTML = zone['status_text'] + " ";
			tr.childNodes[0].children[0].children[2].children[0].innerHTML = zone['adc'] + " ";
			
			return;
		}	
		let tr = document.createElement('tr');
		let td = document.createElement('td');
		let td2 = document.createElement('td');
		let p = document.createElement('p');
		
		let spanText = document.createElement('span');
		let spanText1 = document.createElement('span');
		let spanText2 = document.createElement('span');
		let spanValues = document.createElement('span');
		let spanValues1 = document.createElement('span');
		let spanValues2 = document.createElement('span');	
		
		let buttonArm = document.createElement('input');	
		let buttonDisarm = document.createElement('input');	
		
		tr.id = "bolid_"+num;
		
		spanText.innerHTML = "Номер ШС: ";
		spanText1.innerHTML = "Состояние: ";		
		spanText2.innerHTML = "Значение АЦП: ";	

		spanValues.innerHTML = zone['zone'] + "  ";
		spanValues1.innerHTML = zone['status_text'] + "  ";
		spanValues2.innerHTML = zone['adc'] + "  ";
		
		spanValues.style.color = "indianred";	
		spanValues.style.fontWeight = "bold";	
		spanValues1.style.color = "indianred";	
		spanValues1.style.fontWeight = "bold";
		spanValues2.style.color = "indianred";
		spanValues2.style.fontWeight = "bold";

		buttonArm.type = "button";		
		buttonDisarm.type = "button";
		buttonArm.value = "Поставить на охрану";		
		buttonDisarm.value = "Снять с охраны";		
		buttonArm.classList.add('sb');		
		buttonDisarm.classList.add('sb');	
		buttonArm.style.margin = '5px';		
		buttonDisarm.style.margin = '5px';
		buttonArm.addEventListener('click', function(event) {
			armBolid(zone['zone'], 'arm');
		});
		buttonDisarm.addEventListener('click', function(event) {
			armBolid(zone['zone'], 'disarm');
		});
		
		tr.append(td);
		tr.append(td2);
		td2.append(buttonArm);
		td2.append(buttonDisarm);
		td.append(p);
		p.append(spanText);
		p.append(spanText1);
		p.append(spanText2);
		spanText.append(spanValues);
		spanText1.append(spanValues1);
		spanText2.append(spanValues2);
		
		//changeBR.append(tr);
		document.getElementById("changeBR").append(tr);
	}
	
	function armBolid(zoneID, type){
		document.body.style.cursor = 'wait';
		postAjax('api/request_bolid.php?device='+deviceID+'&action='+type+'&zone='+zoneID, "GET", "", function(d){
			document.body.style.cursor = 'default';
			var resp = JSON.parse(d);
			const status = resp['command_ack'];
			if(status == true){
				FromLoadPage();
			}
			else{
				alert("Ошибка выполнения команды");
			}	
		});
	}
	
	function updateTamper(resp){
		document.getElementById('tamperState').innerHTML = resp['tamper']['status_text'];
	}
	
	function updateVoltage(resp){
		document.getElementById('uMain').innerHTML = resp['voltage']['main'];
		document.getElementById('u1').innerHTML = resp['voltage']['u1'];
		document.getElementById('u2').innerHTML = resp['voltage']['u2'];
	}
	
	function loadDeviceInfo(resp){
		document.getElementById('addrDevice').innerHTML = resp['device_addr'];
		document.getElementById('typeDevice').innerHTML = resp['device']['device_name'];
		document.getElementById('verDevice').innerHTML = resp['device']['device_version'];
		document.getElementById('verEth').innerHTML = resp['version_software'];
		document.getElementById('shCount').innerHTML = resp['device']['device_shielf'];
		document.getElementById('rlCount').innerHTML = resp['device']['device_relays'];
	}
	
	function FromLoadPage(){
		if(deviceID == ""){
			alert("Не указан модуль");
			return;
		}
		document.body.style.cursor = 'wait';
		postAjax('api/request_bolid.php?device='+deviceID, "GET", "", function(d){
			const resp = JSON.parse(d);
			loadDeviceInfo(resp);
			updateTamper(resp);
			updateVoltage(resp);
			for(var i = 0; i < resp['zones'].length; i++){								
				CreateBolid(resp['zones'][i], i);	
			}
			document.body.style.cursor = 'default';			
		});
	}	
	window.setTimeout("FromLoadPage()", 1);
  </script>
</head>
<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Управление модулей Bolid C2000</div>
			<div style="text-align: left;" class = "com">
                <div style="width: 100%; height: auto; text-align: left;" class = "components">
					<div class = "components_alfa">
						<div style="width: 100%" class = "components_text">
							<div>
								<p style='color: aliceblue;'>Адрес прибора: <span style='color: burlywood;' id='addrDevice'>0</span></p>
								<p style='color: aliceblue;'>Тип прибора: <span style='color: burlywood;' id='typeDevice'>Не известен</span></p>
								<p style='color: aliceblue;'>Версия прибора: <span style='color: burlywood;' id='verDevice'>0</span></p>
								<p style='color: aliceblue;'>Версия Ethernet-мастера: <span style='color: burlywood;' id='verEth'>0</span></p>
								<p style='color: aliceblue;'>Доступно шлейфов: <span style='color: burlywood;' id='shCount'>0</span></p>
								<p style='color: aliceblue;'>Доступно реле: <span style='color: burlywood;' id='rlCount'>0</span></p>
							</div>
							<br />
							<div>
								<p><span style='color: aliceblue;'>Датчик вскрытия корпуса: </span><span id='tamperState' style="color: indianred; font-weight: bold;">Нет данных</span></p>
							</div>
							<br />
							<div>
								<p><span style='color: aliceblue;'>Напряжение питания: </span><span id='uMain' style="color: indianred; font-weight: bold;">0</span>В</p>
								<p><span style='color: aliceblue;'>Напряжение питания U1: </span><span id='u1' style="color: indianred; font-weight: bold;">0</span>В</p>
								<p><span style='color: aliceblue;'>Напряжение питания U2: </span><span id='u2' style="color: indianred; font-weight: bold;">0</span>В</p>
							</div>
							<br />
							<table id="changeBR" style="width: 100%; text-align: start; padding: 0; margin: 0; font-size: large; display: inline-block; color: aliceblue;">
							</table>
							<br />
							<div>
								<p><input class='sb' type='button' onclick='FromLoadPage(); return false;' value='Обновить инофрмацию' /></p>
								<p><input class='sb' type='button' onclick='armBolid(1, "reset"); return false;' value='Сброс тревог' /></p>
							</div>
							<br />
                        </div>
					</div>
				</div>               
			</div>
		</div>
        
		<?php require_once(__DIR__."//..//..//../site/verhn.php");?>        
</body>

</html>
