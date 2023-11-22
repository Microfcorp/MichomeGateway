<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?
$module = "";

if(isset($_GET['module'])){
	$module = $_GET['module'];
}

?>
<html>
<head>
<title>Управление Michome</title>
<link rel="stylesheet" type="text/css" href="styles/style.css"/>
<script type="text/javascript">	
	//var caz = document.getElementById("cazestvo").value;
	function createXMLHttp() {
        if (typeof XMLHttpRequest != "undefined") { // для браузеров аля Mozilla
            return new XMLHttpRequest();
        } else if (window.ActiveXObject) { // для Internet Explorer (all versions)
            var aVersions = [
                "MSXML2.XMLHttp.5.0",
                "MSXML2.XMLHttp.4.0",
                "MSXML2.XMLHttp.3.0",
                "MSXML2.XMLHttp",
                "Microsoft.XMLHttp"
            ];
            for (var i = 0; i < aVersions.length; i++) {
                try {
                    var oXmlHttp = new ActiveXObject(aVersions[i]);
                    return oXmlHttp;
                } catch (oError) {}
            }
            throw new Error("Невозможно создать объект XMLHttp.");
        }
    }

// фукнция Автоматической упаковки формы любой сложности
function getRequestBody(oForm) {
    var aParams = new Array();
    for (var i = 0; i < oForm.elements.length; i++) {
        var sParam = encodeURIComponent(oForm.elements[i].name);
        sParam += "=";
        sParam += encodeURIComponent(oForm.elements[i].value);
        aParams.push(sParam);
    }
    return aParams.join("&");
}
// функция Ajax POST
function postAjax(url, oForm, callback) {
    // создаем Объект
    var oXmlHttp = createXMLHttp();
    // получение данных с формы
    var sBody = oForm;
    // подготовка, объявление заголовков
    oXmlHttp.open("POST", url, true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//oXmlHttp.addHeader("Access-Control-Allow-Origin", "*");
    // описание функции, которая будет вызвана, когда придет ответ от сервера
    oXmlHttp.onreadystatechange = function() {
        if (oXmlHttp.readyState == 4) {
            if (oXmlHttp.status == 200) {
                callback(oXmlHttp.responseText);
            } else {
                callback('error' + oXmlHttp.statusText);
            }
        }
    };
    // отправка запроса, sBody - строка данных с формы
    oXmlHttp.send(sBody);
}
  
	var moduleAddress = "<? echo $module; ?>";
	
	function CreateLight(pinID, pinType, pinCMD){
		//<tr><td><p>Свет 1: <input type="range" min="0" max="1023" oninput="sizePic(0, this.value)" value="0"><input type="number" min="0" max="1023" oninput="sizePic(0, this.value)" value="0"><input type="button" onclick="sizePic(0, 1023)" value="На всю"><input type="button" onclick="sizePic(0, 0)" value="На 0"></p></tr></td>
		let tr = document.createElement('tr');
		let td = document.createElement('td');
		let p = document.createElement('p');
		let spanText = document.createElement('span');
		let spanInputs = document.createElement('span');		
		let b1 = document.createElement('input');
		let b2 = document.createElement('input');
		let Ranger = document.createElement('input');
		
		spanText.innerHTML = "Вывод " + pinID + ": ";
		if(pinType == "PWM"){
			Ranger.type = "range";
			Ranger.min = 0;
			Ranger.max = 1023;
			Ranger.value = pinCMD;
			spanInputs.append(Ranger);
		}
		else if(pinType == "Relay"){
			
		}
				
		b1.type = "button";
		b1.value = "Включить";
		if((pinCMD == 1 && pinType == "Relay") || (pinCMD == 1023 && pinType == "PWM"))
			b1.className = "selButton";
		b1.onclick = function(){postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'setlight?p='+pinID+'%26s='+(pinType == "Relay" ? '1' : '1023'), "", function(){if(pinType == "PWM"){Ranger.value = 1023;} b2.className = ""; b1.className = "selButton";});;};
		b2.type = "button";
		b2.value = "Выключить";
		if(pinCMD == 0)
			b2.className = "selButton";
		b2.onclick = function(){postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'setlight?p='+pinID+'%26s=0', "", function(){if(pinType == "PWM"){Ranger.value=0;} b1.className = ""; b2.className = "selButton";});;};
		spanInputs.append(b1);
		spanInputs.append(b2);
		//Ranger.oninput = alert('hh');
		
		if(pinType == "PWM"){
			Ranger.oninput = function(){let val = this.value; let max = this.max; let min = this.min; postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'setlight?p='+pinID+'%26s='+val, "", function(){if(val == min){b1.className = ""; b2.className = "selButton";} else if(val == max) {b2.className = ""; b1.className = "selButton";} else {b1.className = ""; b2.className = "";} });;};
		}
		
		tr.append(td);
		td.append(p);
		p.append(spanText);
		p.append(spanInputs);
		
		//changeBR.append(tr);
		document.getElementById("changeBR").append(tr);
	}
	
	function CreateStrobo(pinID, pinType, pinCMD){
		//<tr><td><p>Свет 1 Стробо: <input type="number" min="0" max="100" id="sb1" value="3"><input type="number" min="0" max="500" id="st1" value="30"><input type="button" onclick="Strobo(0, document.getElementById('sb1').value, document.getElementById('st1').value)" value="Старт"></p></tr></td>
		let tr = document.createElement('tr');
		let td = document.createElement('td');
		let p = document.createElement('p');
		let spanText = document.createElement('span');
		let spanInputs = document.createElement('span');		
		let b1 = document.createElement('input');
		let b2 = document.createElement('input');
		let Ranger1 = document.createElement('input');
		let Ranger2 = document.createElement('input');
		
		spanText.innerHTML = "Вывод " + pinID + " стробо: ";
		Ranger1.type = "number";
		Ranger1.min = 0;
		Ranger1.max = 100;
		Ranger1.value = 3;
		Ranger2.type = "number";
		Ranger2.min = 0;
		Ranger2.max = 500;
		Ranger2.value = 30;
		spanInputs.append(Ranger1);
		spanInputs.append(Ranger2);
				
		b1.type = "button";
		b1.value = "Старт";
		b1.onclick = function(){postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'strobo?p='+pinID+'%26s='+Ranger1.value+'%26t='+Ranger2.value, "", function(){});;};
		
		spanInputs.append(b1);
		//Ranger.oninput = alert('hh');
		
		tr.append(td);
		td.append(p);
		p.append(spanText);
		p.append(spanInputs);
		
		changeBR.append(tr);
	}
	
	function CreateStroboAll(){
		//<tr><td><p>Стробо: <input type="number" min="0" max="100" id="sb4" value="3"><input type="number" min="0" max="500" id="st4" value="30"><input type="button" onclick="Stroboall(document.getElementById('sb4').value, document.getElementById('st4').value)" value="Старт"></p></tr></td>
		let tr = document.createElement('tr');
		let td = document.createElement('td');
		let p = document.createElement('p');
		let spanText = document.createElement('span');
		let spanInputs = document.createElement('span');		
		let b1 = document.createElement('input');
		let b2 = document.createElement('input');
		let Ranger1 = document.createElement('input');
		let Ranger2 = document.createElement('input');
		
		spanText.innerHTML = "Все стробо: ";
		Ranger1.type = "number";
		Ranger1.min = 0;
		Ranger1.max = 100;
		Ranger1.value = 3;
		Ranger2.type = "number";
		Ranger2.min = 0;
		Ranger2.max = 500;
		Ranger2.value = 10;
		spanInputs.append(Ranger1);
		spanInputs.append(Ranger2);
				
		b1.type = "button";
		b1.value = "Старт";
		b1.onclick = function(){postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'stroboall?s='+Ranger1.value+'%26t='+Ranger2.value, "", function(){});;};
		
		spanInputs.append(b1);
		//Ranger.oninput = alert('hh');
		
		tr.append(td);
		td.append(p);
		p.append(spanText);
		p.append(spanInputs);
		
		changeBR.append(tr);
	}
	
	function CreateScriptsAndEffects(){
		//<tr><td><p>Стробо: <input type="number" min="0" max="100" id="sb4" value="3"><input type="number" min="0" max="500" id="st4" value="30"><input type="button" onclick="Stroboall(document.getElementById('sb4').value, document.getElementById('st4').value)" value="Старт"></p></tr></td>
		postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'lightscript?type=get', "", function(d){
			var linesa = d.split('$');
			for(var i = 0; i < linesa.length; i++){
				if(linesa[i].split('|').length < 3) continue;
				let id = linesa[i].split('|')[0].trim();
				let en = linesa[i].split('|')[1].trim();
				let name = linesa[i].split('|')[2].trim();
				let scr = linesa[i].split('|')[3].trim();								
			
				if(en != "1") continue;
			
				let tr = document.createElement('tr');
				let td = document.createElement('td');
				let p = document.createElement('p');
				let spanText = document.createElement('span');
				let spanInputs = document.createElement('span');		
				let b1 = document.createElement('input');
				let b2 = document.createElement('input');
				let Ranger1 = document.createElement('input');
				let Ranger2 = document.createElement('input');
				
				spanText.innerHTML = name + ": ";						
				b1.type = "button";
				b1.value = "Выполнить скрипт";
				b1.onclick = function(){postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'runscript?s='+id, "", function(){});;};
				
				spanInputs.append(b1);
				//Ranger.oninput = alert('hh');
				
				tr.append(td);
				td.append(p);
				p.append(spanText);
				p.append(spanInputs);
				
				changeBR.append(tr);
			}
			CreateEffects();
		});
	}
	
	function CreateEffects(){
		//<tr><td><p>Стробо: <input type="number" min="0" max="100" id="sb4" value="3"><input type="number" min="0" max="500" id="st4" value="30"><input type="button" onclick="Stroboall(document.getElementById('sb4').value, document.getElementById('st4').value)" value="Старт"></p></tr></td>
		postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'effects?type=get', "", function(d){
			var linesa = d.split('$');
			for(var i = 0; i < linesa.length; i++){
				if(linesa[i].split('|').length < 3) continue;
				let id = linesa[i].split('|')[0].trim();
				let en = linesa[i].split('|')[1].trim();
				let name = linesa[i].split('|')[2].trim();
				let dsc = linesa[i].split('|')[3].trim();								
			
				if(en != "1") continue;
			
				let tr = document.createElement('tr');
				let td = document.createElement('td');
				let p = document.createElement('p');
				let spanText = document.createElement('span');
				let spanInputs = document.createElement('span');		
				let b1 = document.createElement('input');
				let b2 = document.createElement('input');
				let Ranger1 = document.createElement('input');
				let Ranger2 = document.createElement('input');
				
				spanText.innerHTML = name + ": ";						
				b1.type = "button";
				b1.value = "Управление эффектом";
				b1.onclick = function(){postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'effects?type=startstop%26id='+id, "", function(){});;};
				
				spanInputs.append(b1);
				//Ranger.oninput = alert('hh');
				
				tr.append(td);
				td.append(p);
				p.append(spanText);
				p.append(spanInputs);
				
				changeBR.append(tr);
			}
		});
	}
	
	function CreatePad(){
		let tr = document.createElement('tr');
		let td = document.createElement('td');
		td.innerHTML = "<br />";
		tr.append(td);
		changeBR.append(tr);
	}
	
	function FromLoadPage(){
		postAjax('api/setcmd.php?device='+moduleAddress+'&cmd=getpins&timeout=5000', "", function(d){
			var lines = d.split("<br />");
			for(var i = 0; i < lines.length; i++){
				if(lines[i].split('-').length < 2) continue;
				let pinID = lines[i].split('-')[0].trim().split('(')[0];
				let pinType = lines[i].split('-')[1].trim();
				let pinCMD = lines[i].split('-')[2].trim();
				
				CreateLight(pinID, pinType, pinCMD);				
			}
			CreatePad();
			for(var i = 0; i < lines.length; i++){
				if(lines[i].split('-').length < 2) continue;
				let pinID = lines[i].split('-')[0].trim().split('(')[0];
				let pinType = lines[i].split('-')[1].trim();
				let pinCMD = lines[i].split('-')[2].trim();
				
				CreateStrobo(pinID, pinType, pinCMD);				
			}
			CreatePad();
			CreateStroboAll();
			CreatePad();
			//CreateScripts();
			//CreateEffects();	
			CreateScriptsAndEffects();
		});
	}
	
	function sleep(ms) {
		ms += new Date().getTime();
		while (new Date() < ms){}
	}

	function sizePic(p, s) {
		var size = s;

		var host = '<?echo $_SERVER['HTTP_HOST'];?>';
	
		if(host != "192.168.1.42"){
		//console.log('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'setlight?p='+p+'%26s='+size);
		postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'setlight?p='+p+'%26s='+size, "", function(){});
		}
		else{
			//console.log('http://192.168.1.34/setlight?p='+p+'&s='+size);
			postAjax('http://192.168.1.34/setlight?p='+p+'&s='+size, "", function(){});
		}
		sleep(500);
	}
   
	function Strobo(p, s, d) {
	   
		var size = s;

		var host = '<?echo $_SERVER['HTTP_HOST'];?>';
		
		if(host != "192.168.1.42"){
		//console.log('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'setlight?p='+p+'%26s='+size);
		postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'strobo?p='+p+'%26s='+size+'%26t='+d, "", function(){});
		}
		else{
			//console.log('http://192.168.1.34/setlight?p='+p+'&s='+size);
			postAjax('http://192.168.1.34/strobo?p='+p+'&s='+size+'&t='+d, "", function(){});
		}	
	}
	function Stroboall(s, d) {
	   
		var size = s;

		var host = '<?echo $_SERVER['HTTP_HOST'];?>';
		
		if(host != "192.168.1.42"){
		//console.log('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'setlight?p='+p+'%26s='+size);
		postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/setcmd.php?device='+ '192.168.1.34' +'&cmd='+ 'stroboall?s='+size+'%26t='+d, "", function(){});
		}
		else{
			//console.log('http://192.168.1.34/setlight?p='+p+'&s='+size);
			postAjax('http://192.168.1.34/stroboall?&s='+size+'&t='+d, "", function(){});
		}	
	}
	
	window.setTimeout("FromLoadPage()", 1);
  </script>
</head>
<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Управление освещением</div>
			<div style="text-align: left;" class = "com">
                <div style="width: 50%; height: auto; text-align: left;" class = "components">
					<div class = "components_alfa">
						<div style="width: 100%" class = "components_text">
							<table id="changeBR" style="width: 100%; text-align: start; padding: 0; margin: 0; font-size: large; display: inline-block; color: aliceblue;">
								<tbody>
								</tbody>
							</table>
                        </div>
					</div>
				</div>               
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?>        
</body>

</html>
