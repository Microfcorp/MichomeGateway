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
<title>Управление термометром</title>
<link rel="stylesheet" type="text/css" href="styles/style.css"/>
<script type="text/javascript" src="/site/MicrofLibrary.js"></script>
<script type="text/javascript" src="libmichome.js"></script>
<script type="text/javascript">	
	var moduleAddress = "<? echo $module; ?>";
	
	function CreateMeteo(id, num){
		//<tr><td><p>Свет 1: <input type="range" min="0" max="1023" oninput="sizePic(0, this.value)" value="0"><input type="number" min="0" max="1023" oninput="sizePic(0, this.value)" value="0"><input type="button" onclick="sizePic(0, 1023)" value="На всю"><input type="button" onclick="sizePic(0, 0)" value="На 0"></p></tr></td>
		let tr = document.createElement('tr');
		let td = document.createElement('td');
		let p = document.createElement('p');
		let spanText = document.createElement('span');
		let spanText1 = document.createElement('span');
		let spanText2 = document.createElement('span');
		let spanValues = document.createElement('span');
		let spanValues1 = document.createElement('span');
		let spanValues2 = document.createElement('span');
		let DescText = document.createElement('span');		
		
		spanText.innerHTML = "Температура с термометра " + num + ": ";
		spanText1.innerHTML = "Влажность с гигрометра " + num + ": ";		
		spanText2.innerHTML = "Давление с барометра " + num + ": ";		
		spanValues.style.color = "indianred";	
		spanValues1.style.color = "indianred";	
		spanValues2.style.color = "indianred";	
		DescText.style.fontStyle = "italic";
		DescText.style.color = "aquamarine";
		
		postAjax('/michome/api/setcmd.php?device='+moduleAddress+'&cmd=getmeteoinfo?nonehtml=1%26id='+id+'&timeout=5000', "GET", "", function(d){
			spanText.title = d.replaceAll("<br />", "\n");
			DescText.innerHTML = "<br />Описание датчика " + num + " (" + id + "): <br />" + d;			
		});
		
		let f = function(){
			postAjax('api/setcmd.php?device='+ moduleAddress +'&cmd='+ 'getmeteo?id='+id+"%26nonehtml=1", "GET", "", function(d)
			{
				var ds = d.split('\n');
				if(ds.length > 0 && ds[0] != ""){
					spanValues.innerHTML = ds[0] + "  ";
				}
				else{
					spanText.style.display = "none";
					spanValues.style.display = "none";
				}
				if(ds.length > 1 && ds[1] != ""){
					spanValues1.innerHTML = ds[1] + "  ";
				}
				else{
					spanText1.style.display = "none";
					spanValues1.style.display = "none";
				}
				if(ds.length > 2 && ds[2] != ""){
					spanValues2.innerHTML = ds[2] + "  ";
				}	
				else{
					spanText2.style.display = "none";
					spanValues2.style.display = "none";
				}				
			});
		};
		window.setInterval(f, 6000);
		f();
		
		tr.append(td);
		td.append(p);
		if(spanValues.innerHTML != "0.00C  ")
			p.append(spanText);
		if(spanValues1.innerHTML != "0.00%  ")
			p.append(spanText1);
		if(spanValues2.innerHTML != "0.00 мм.рт.ст  ")
			p.append(spanText2);
		p.append(DescText);
		spanText.append(spanValues);
		spanText1.append(spanValues1);
		spanText2.append(spanValues2);
		
		//changeBR.append(tr);
		document.getElementById("changeBR").append(tr);
	}

	function initD(){
		var Did = document.getElementById("selectInit").value;
		postAjax('api/setcmd.php?device='+moduleAddress+'&cmd=initmeteo?id='+Did+'&timeout=5000', "GET", "", function(d){
			if(d == "Init Error"){
				alert("Ошибка инициализации");
			}
			else if(d == "Init OK"){
				alert("Успешная инициализация");
			}
			else{
				alert("Неизвестная ошибка");
			}				
		});
	}
	
	function resetD(){
		var Did = document.getElementById("selectInit").value;
		postAjax('api/setcmd.php?device='+moduleAddress+'&cmd=resetmeteo?id='+Did+'&timeout=5000', "GET", "", function(d){
			if(d == "Reset Error"){
				alert("Ошибка сброса");
			}
			else if(d == "Reset OK"){
				alert("Успешный сброс");
			}
			else{
				alert("Неизвестная ошибка");
			}				
		});
	}
	
	function FromLoadPage(){
		//alert(GetFirmwareVersionModule(moduleAddress));
		//if(GetFirmwareVersionModule(moduleAddress) >= 1.63){
			postAjax('api/setcmd.php?device='+moduleAddress+'&cmd=meteoenable&timeout=5000', "GET", "", function(d){
				var ids = d.split(',');
				for(var i = 0; i < ids.length; i++){								
					CreateMeteo(ids[i], i);	

					let op = document.createElement('option');
					op.innerHTML = "Датчик " + i + " (" + ids[i] + ")";
					op.value = ids[i];
					document.getElementById("selectInit").append(op);	
					let op1 = document.createElement('option');
					op1.innerHTML = "Датчик " + i + " (" + ids[i] + ")";
					op1.value = ids[i];					
					document.getElementById("selectReset").append(op1);	
				}			
			});
			
			postAjax('api/setcmd.php?device='+moduleAddress+'&cmd=countmeteo&timeout=5000', "GET", "", function(d){
				for(var i = 0; i < parseInt(d); i++){	
							
				}			
			});
		/*}
		else{
			alert("Для использования данного функционала, пожалуйста, обновите модуль до версии 1.63 или выше");
		}*/
	}	
	window.setTimeout("FromLoadPage()", 1);
  </script>
</head>
<body>
		<div class = "body_alfa"></div>
		<div class = "body">
			<div class = "title_menu">Управление Michome. Управление метеостанцией</div>
			<div style="text-align: left;" class = "com">
                <div style="width: 100%; height: auto; text-align: left;" class = "components">
					<div class = "components_alfa">
						<div style="width: 100%" class = "components_text">
							<table id="changeBR" style="width: 100%; text-align: start; padding: 0; margin: 0; font-size: large; display: inline-block; color: aliceblue;">
								<tbody>
								</tbody>
							</table>
							<br />
							<div>
								<p><span>Инициализировать датчик:</span> <select id='selectInit'></select> <input type='button' value='Инициализировать' onclick='initD()' /></p>
								<p><span>Сбросить датчик:</span> <select id='selectReset'></select> <input type='button' value='Сбросить' onclick='resetD()' /></p>
							</div>
                        </div>
					</div>
				</div>               
			</div>
		</div>
        
		<?php require_once(__DIR__."//../site/verhn.php");?>        
</body>

</html>
