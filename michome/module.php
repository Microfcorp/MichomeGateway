<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	header("Michome-Page: Module-Configuration");   
?>
<!Doctype html>
<html>
	<head>
		<title>Управление Michome. Модули</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
        <script type="text/javascript" src="/site/MicrofLibrary.js"></script>
        <script type="text/javascript" src="libmichome.js"></script>
		<style>
			dialog::backdrop {
			  background-color: rgba(0, 0, 0, 0.8);
			}
		</style>
        <script type="text/javascript">        
        function SendModule(url, data){
            postAjax('api/setcmd.php?device='+ url +'&cmd='+ data.replace( /&/g, "%26" ), "GET", "", function(d){alert(d)});
        }

        var ips = "";

        function show(ip){
            postAjax('api/getallsetting.php?device='+ ip, "POST", "", function(d)
                {
                    var table = document.createElement('table');
                    var html = '<tbody>';
                    for (var i = 0; i < d.split(';').length; i++) {
                        html += '<tr><td class="n n|'+i+'">'+d.split(';')[i].split('=')[0]+'</td><td><input class="v v|'+i+'" type="text" value="'+d.split(';')[i].split('=')[1]+'"></input></td></tr>';
                    }
                    table.innerHTML = html + '</tbody>';
                    tables.innerHTML = table.outerHTML;
                }
            );
            ips = ip;
            dialog.showModal();
        }

        function save(){
            var names = document.getElementsByClassName('n');
            var values = document.getElementsByClassName('v');
            
            var result = "";
            
            for (var i = 0; i < names.length; i++) {

                var v1 = names[i].innerHTML;
                var v2 = values[i].value;
                result += (v1 + "=" + v2 + ";");
            }
            result = result.substring(0, result.length - 1)
            
            postAjax('api/saveallsettings.php?device='+ ips + '&d='+result, "POST", "", function(d)
                {
                   if(d!="OK"){
                       alert("Ошибка");
                   }
                }
            );
            SendModule(ips, 'setsettings?s='+ result);
            
            dialog.close();
        }

        function closed(){
            dialog.close();
        }		
		
		function CreateModule(mID, ip, type){
			let divC = document.createElement('div');
			let divCA = document.createElement('div');
			let divCT = document.createElement('div');
			let divCMain = document.createElement('div');
			let divB = document.createElement('div');	
			let settingB = document.createElement('a');
			let b2 = document.createElement('input');
			let Ranger = document.createElement('input');
			
			divC.className = "components";
			divCA.className = "components_alfa";
			divCT.className = "components_title";
			divCMain.className = "components_text";
			divB.className = "components_button";
			
			settingB.href = "#";
			settingB.onclick = function(){return false;};
			divCT.innerHTML = mID + " (" + ip + ")";
			postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
				//alert(GetFirmwareVersionModule(mID));
				if(d.lastIndexOf("Ошибка") != -1){ //Если ошибка соединения
					//<p style=\"color: red;\">Модуль не в сети </p><p>Последний раз был в сети: ".$tmp->PosledDate."</p>
					let p1 = document.createElement('p');
					p1.style.color = "red";
					p1.innerHTML = "Модуль не в сети";
					let p8 = document.createElement('p');
					p8.innerHTML = "Тип модуля: " + type;
					let p2 = document.createElement('p');
					p2.innerHTML = "Последний раз был в сети: не используется";
					divC.setAttribute("isonline", "0");
					divB.onclick = function(){show(ip);};
					divCMain.append(p1);
					divCMain.append(p2);
					divCMain.append(p8);
				}
				else{ //Если гуд соеденились
					//Informetr_Pogoda/nInformetr/n-57/n192.168.1.13/n4194304/n64.00/n23296/n1638400/nPower On/n2.40/n2.15/nNov 27 2022/n15:40:41/nC:\Users\Lexap\OneDrive\���������\Arduino\libraries\Michome\Michom.cpp/nfsmanageron/n
					divC.setAttribute("isonline", "1");
					var IsSafeMode = false;
					
					var md = d.split('/n');
					if(md.length < 5)
						IsSafeMode = true;
					
					var rssi = IsSafeMode ? "-99" : md[2];
					ip = IsSafeMode ? ip : md[3];
					var flashsize = IsSafeMode ? "0" : md[4];
					var vcc = IsSafeMode ? "0" : md[5];
					var ram = IsSafeMode ? "0" : md[6];
					var startState = IsSafeMode ? "0" : md[8];
					var FirVer = IsSafeMode ? "0" : md[9];
					var MicVer = IsSafeMode ? "0" : md[10];
					var DateCompilation = IsSafeMode ? "" : (md[11] + " " + md[12]);
					
					divB.onclick = function(){show(ip);};
					
					let p8 = document.createElement('p');
					p8.innerHTML = "Тип модуля: " + type;
					
					if(IsSafeMode){
						let p1 = document.createElement('p');
						p1.style.color = "orange";
						p1.innerHTML = "Модуль в сети. Безопасный режим";
						
						let a00 = document.createElement('a');
						a00.innerHTML = "Открыть логи";
						a00.href = "proxy.php?module=" + mID + "&path=/getlogs";
						a00.target = "_blank";
						a00.rel = "noopener noreferrer";
						let a0 = document.createElement('p');
						a0.append(a00);
						let a11 = document.createElement('a');
						a11.innerHTML = "Открыть страницу модуля (локально)";
						a11.href = "http://" + ip;
						let a12 = document.createElement('a');
						a12.innerHTML = "Открыть страницу модуля (прокси)";
						a12.href = "proxy.php?module=" + mID;
						let a1 = document.createElement('p');
						a1.append(a11);
						let a112 = document.createElement('p');
						a112.append(a12);
						
						divCMain.append(p1);
						divCMain.append(a0);
						divCMain.append(a1);
						divCMain.append(a112);
					}
					else{
						let p1 = document.createElement('p');
						p1.style.color = "green";
						p1.innerHTML = "Модуль в сети";
						let p9 = document.createElement('p');
						p9.innerHTML = "ID модуля: " + mID;
						let p2 = document.createElement('p');
						p2.innerHTML = "Версия ПО модуля: " + FirVer;
						let p3 = document.createElement('p');
						p3.innerHTML = "Версия системы Michome: " + MicVer + (ActualMichomeVersion > parseFloat(MicVer) ? " <i>(Устаревшая версия)</i>" : "");
						let p4 = document.createElement('p');
						p4.innerHTML = "Уровень сигнала: " + rssi;
						let p5 = document.createElement('p');
						p5.innerHTML = "Размер Flash: " + formatFileSize(flashsize);
						let p6 = document.createElement('p');
						p6.innerHTML = "Свободно ОЗУ: " + formatFileSize(ram);
						let p7 = document.createElement('p');
						p7.innerHTML = "Напряжение питания модуля: " + vcc;
						let a00 = document.createElement('a');
						a00.innerHTML = "Открыть логи";
						a00.href = "proxy.php?module=" + mID + "&path=/getlogs";
						a00.target = "_blank";
						a00.rel = "noopener noreferrer";
						let a0 = document.createElement('p');
						a0.append(a00);
						let a11 = document.createElement('a');
						a11.innerHTML = "Открыть страницу модуля (локально)";
						a11.href = "http://" + ip;
						let a12 = document.createElement('a');
						a12.innerHTML = "Открыть страницу модуля (прокси)";
						a12.href = "proxy.php?module=" + mID;
						let a1 = document.createElement('p');
						a1.append(a11);
						let a112 = document.createElement('p');
						a112.append(a12);					
						
						divCMain.append(p1);
						divCMain.append(p8);
						divCMain.append(p9);
						divCMain.append(p2);
						divCMain.append(p3);
						divCMain.append(p4);
						divCMain.append(p5);
						divCMain.append(p6);					
						if(vcc != "null")
							divCMain.append(p7);
						divCMain.append(a0);
						divCMain.append(a1);
						divCMain.append(a112);
					}
					
					if(!IsSafeMode){
						postAjax('api/getdevice.php?type=typeinfo&typemodule='+type, "GET", "", function(deinf){
							var moduleinfo = JSON.parse(deinf);
							
							if(moduleinfo["error"] == 0){
								p8.title = moduleinfo["typedesc"];
								for(var u = 0; u < moduleinfo["typeurl"].length; u++){
									var urlcmd = moduleinfo["typeurl"][u][0];
									var namecmd = moduleinfo["typeurl"][u][1];
									
									let acmd = document.createElement('a');
									acmd.innerHTML = namecmd;
									acmd.href = "#";
									acmd.name = urlcmd;
									acmd.setAttribute("module", mID);
									acmd.onclick = function(){SendCMDAlert(this.attributes["module"].value, this.name); return false;};
									let pcmd = document.createElement('p');
									pcmd.append(acmd);
									divCMain.append(pcmd);
								}
								for(var u = 0; u < moduleinfo["typemurl"].length; u++){
									var urlcmd = moduleinfo["typemurl"][u][0];
									var namecmd = moduleinfo["typemurl"][u][1];
									
									let acmd = document.createElement('a');
									acmd.innerHTML = namecmd;
									acmd.href = urlcmd.replace("{id}", mID);
									let pcmd = document.createElement('p');
									pcmd.append(acmd);
									divCMain.append(pcmd);
								}
							}
						});
					}
				}			
			});
			
			divB.append(settingB);
			divC.append(divCA);
			divCA.append(divCT);
			divCA.append(divCMain);
			divCA.append(divB);			
			
			document.getElementById("mainContainer").append(divC);
		}
		
		function resort(selector) {
			const nodeList = document.querySelectorAll(selector);
			const dict = {};			
			const parent = nodeList[0].parentNode;		
			nodeList.forEach(node => {
				console.log(node.attributes[1].value == '1');
				const key = node.attributes[1].value;
				dict[key] = node;
				node.parentNode.removeChild(node);
			});
			const keys = Object.keys(dict);
			keys.sort().forEach(k => parent.appendChild(dict[k]));
		}
		
		function FromLoadPage(){
			postAjax('api/getdevice.php', "GET", "", function(d){
				var lines = JSON.parse(d);
				for(var i = 0; i < lines.devicename.length; i++){
					let mID = lines.devicename[i];
					let ip = lines.ips[i];
					let type = lines.devicetype[i];
					
					CreateModule(mID, ip, type);				
				}
								
			});		
		}
	window.setTimeout("FromLoadPage()", 10);
	//window.addEventListener("load", FromLoadPage);
    </script>
	</head>
	<body>
		<div class="body_alfa"></div>
		<div class="body">
			<div class="title_menu">Управление Michome. Модули</div>
			<div class="com" id="mainContainer">               
                <!--<div class = "components">
					<div class = "components_alfa">
						<div class = "components_title">192.168.45.201 - Что-то тут интересное...</div>
						<div class = "components_text">А тут брет нести можно, несу и буду нести, а что еще прикажете делать, не знаю я, всю жизнь ерундой занимаемся</div>
						<div class = "components_button"><a href = "#"></a></div>
	
					</div>
				</div>-->
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
