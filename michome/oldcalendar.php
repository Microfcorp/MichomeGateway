<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<html>
<head>
<title>Управление Michome. Календарь информации</title>
<script type="text/javascript" src="/site/MicrofLibrary.js"></script>
<script type="text/javascript" src="libmichome.js"></script>
<script>


function Graphics(type,module,device,value){
    //alert(device);
	if(type == "col"){
	  var txt= value;
	  document.getElementById('img1').src = "grafick.php?ip="+device+"&type="+module+"&period="+txt;
	  Start("type="+module+"&period="+txt);
	}
	else if(type == "day"){
		var txt= value;
	    document.getElementById('img1').src = "grafick.php?ip="+device+"&type="+module+"&period="+(txt*144);
		Start("type="+module+"&period="+(txt*144));
	}
	else if(type == "curday"){		
		var datea = '<?php echo(date("Y-m-d")); ?>';
		selected(device,module,datea);
		//postAjax('http://<?echo $_SERVER['HTTP_HOST'];?>/michome/api/timeins.php?device='+device+'&type=oneday', "", function(d){document.getElementById('img1').src = "grafick.php?type="+module+"&period="+d;});			    
	}
}
function Rezim(d){   
    var ip = "";
    var type = "";
    if(d == "ulpog"){
        ip = "192.168.1.11";
        type = "temp";
        titles.innerHTML = "Просмотр температуры на улице";
    }
    else if(d == "temper" || d == "vlazn" || d == "dawlen" || d == "visota"){
        ip = "192.168.1.10";
        if(d == "temper"){
            titles.innerHTML = "Просмотр температуры в комнате"
            type = "temp";
        }
        else if(d == "vlazn"){
            titles.innerHTML = "Просмотр влажности в комнате"
            type = "humm";
        }
        else if(d == "dawlen"){
            titles.innerHTML = "Просмотр давления в комнате"
            type = "dawlen";
        }
        else if(d == "visota"){
            titles.innerHTML = "Просмотр ощущаемой высоты в комнате"
            type = "visota";
        }
    }
    else if (d == "batareya"){
        ip = "localhost";
        type = "temp";
        titles.innerHTML = "Просмотр температуры системы отопления";
    }
    
    var func1 = function(a,d){
        return function() {
            Graphics('col',d,a,document.getElementById('textcmd').value);
        };
    }; 
    var func2 = function(a,d){
        return function() {
            Graphics('day',d,a,document.getElementById('daycmd').value)
        };
    };  
    var func3 = function(a,d){
        return function() {
            Graphics('curday',d,a,'');
        };
    };  
    var func4 = function(a,d){
        return function() {
            selected(a,d,document.getElementById('vibday').value);
        };
    };     
    
    sendcmd.onclick = func1(ip,type);
    visib.onclick = func2(ip,type);
    curd.onclick = func3(ip,type);
    vibday.onchange = func4(ip,type);
    
    Graphics('curday',type,ip,'');
}

function selected(device,module,date){
	//p-11-13-2018 18-40
	
	postAjax('api/timeins.php?device='+device+'&type=selday&date='+date, "GET", "", function(d){
		
		var arr = d.split(';');
		document.getElementById('img1').src = "grafick.php?ip="+device+"&type="+module+"&period="+arr[2]+"&start="+arr[0];
		Start("ip="+device+"&type="+module+"&period="+arr[2]+"&start="+arr[0]);
	});	

	<? if(empty($_GET['nonephoto'])) { ?>

	postAjax('api/getphoto.php?date='+date, "GET", "", function(d){
		document.getElementById('img2').src = "/site/image/graphical/"+d;
	});
	<? } ?>		
		
}
function CurDate(){
	var date = new Date();
	
	return date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate();
}

function createphoto(){
    postAjax('/site/createphoto.php', "GET", "", function(d){alert("Успешно снято");});			    
}

function Load(){
    Rezim("ulpog");
	Graphics('curday','tempul','192.168.1.11',"")
}

window.setTimeout("Load()",10);
</script>
<script>
function Setn(id){
	//alert(id);
	temphist.innerHTML = "  "+id;
}
function Start(resp){
	postAjax('grafick.php?mode=1&'+resp, "GET", "", function(d){
        
        while (maps.firstChild) {
            maps.removeChild(maps.firstChild);
        }
		var json = JSON.parse(d);
		//alert(json[0][0]);
		for(var i=0; i < json[0].length; i++){
			var str = json[0][i];
			var radius = 4;
			var x = str.split(';')[0];
			var y = str.split(';')[1];
			///////
			var area = document.createElement('area');
			area.shape = "circle";
			area.coords = x+","+y+","+radius;
			area.target = "_blank";
			area.alt = "hyacinth";
			area.setAttribute("onclick", "Setn('"+str.split(';')[2]+" было "+str.split(';')[3]+"')");
			maps.appendChild(area);
			
		}
	});
}
</script>
<style>
/* скрываем чекбоксы и блоки с содержанием */
.hide,
.hide + label ~ div {
    display: none;
}
/* вид текста label */
.hide + label {
    margin: 0;
    padding: 0;
    color: green;
    cursor: pointer;
    display: inline-block;
}
/* вид текста label при активном переключателе */
.hide:checked + label {
    color: blue;
    border-bottom: 0;
}
/* когда чекбокс активен показываем блоки с содержанием  */
.hide:checked + label + div {
    display: block; 
    background: #efefef;
    -moz-box-shadow: inset 3px 3px 10px #7d8e8f;
    -webkit-box-shadow: inset 3px 3px 10px #7d8e8f;
    box-shadow: inset 1.4px 1.4px 10px #7d8e8f;
    margin-left: 20px;
    padding: 10px;
    /* чуточку анимации при появлении */
     -webkit-animation:fade ease-in 0.4s; 
     -moz-animation:fade ease-in 0.4s;
     animation:fade ease-in 0.4s; 
}

@-moz-keyframes fade {
    from { opacity: 0; }
to { opacity: 1 }
}
@-webkit-keyframes fade {
    from { opacity: 0; }
to { opacity: 1 }
}
@keyframes fade {
    from { opacity: 0; }
to { opacity: 1 }   
}

.temphis{
	padding-left: 10px;
	box-shadow: inset 10px 4px 20px 1px #1ea52e;
	color: blue;
}
.temphis:hover{
	box-shadow: inset 1px 4px 20px 1px #1ea52e;
	color: red;
}
</style>
</head>

<body>
<?php include_once(__DIR__."//../site/verh.php"); ?>

<div style="background-color: gray;">
<select onchange="Rezim(this.options[this.selectedIndex].value)">
    <option name="select" value="ulpog">График уличной температуры</option>
    <option name="select" value="batareya">График температуры батереи системы отопления</option>
    <option name="select" value="temper">График комнатной температуры</option>
    <option name="select" value="vlazn">График комнатной влажности</option>
    <option name="select" value="dawlen">График комнатного давления</option>
    <option name="select" value="visota">График ощущаемой высоты</option>
</select>
<a href="facts.php">Интересные факты</a>
<a onclick="createphoto()" href="#">Запросить фото</a>
</div>

<div style="display:block;" id="ulpog">

<p id="titles" style="color:red;">Просмотр графика температуры на улице</p>

    <input class="hide" id="hd-1" type="checkbox">
    <label for="hd-1">По количеству измерений</label>
    <div>        
		<p>Введите количество измерений. Обратим ваше внимание на то что 144 измерения равняется 1 дню, а 77 - 12 часам, 6 - одному часу</p>
		<input type="text" name="cmd" id='textcmd' />
		<input id="sendcmd" name="sendcmd" value="Прсмотреть" type="button" />
    </div>
	
</br></br>

    <input class="hide" id="hd-2" type="checkbox">
    <label for="hd-2">По количеству дней</label>
    <div>        
		<p>Введите количество дней. Обратим ваше внимание на то что за 1 день происходит 144 измерения</p>
		<input type="text" name="cmd" id='daycmd' />
		<input id="visib" value="Прсмотреть" type="button" /></br>
    </div>
</br></br>
<input value="За сегодня" id="curd" type="button" /></br>
<p>За <input type="date" id='vibday' /></p>
</div>

<p class="temphis" id="temphist">Что когда было<p>

<table>
    <tbody>
        <tr>
            <td><p><img id="img1" usemap="#flowers"></img></p></td>
            <? if(empty($_GET['nonephoto'])) { ?>
		<td><p><img id="img2" width="540px" height="335px"></img></p></td>
	    <? } ?>
        </tr>
        <tr>
            <td>
            <map id="maps" name="flowers"></map>
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>
