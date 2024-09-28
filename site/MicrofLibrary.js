function Looping(func, param, time){
	window.setTimeout(func(param),time);
	window.setTimeout(Looping(func, param, time),time);
}

function pad(n) {
	return (parseInt(n) < 10 ? '0' : '') + n;
}
   
function CurrentTime(){		
	Data = new Date();
	Year = Data.getFullYear();
	Month = Data.getMonth()+1;
	Day = Data.getDate();
	Hour = Data.getHours();
	Minutes = Data.getMinutes();
	Seconds = Data.getSeconds();
	return pad(Day) + '.' + pad(Month) + '.' + pad(Year) + ' ' + pad(Hour) + ":" + pad(Minutes) + ':' + pad(Seconds);
}

function CurrentDate(){		
	Data = new Date();
	Year = Data.getFullYear();
	Month = Data.getMonth()+1;
	Day = Data.getDate();
	return pad(Year) + '-' + pad(Month) + '-' + pad(Day);
}
	
function PrintIMG(URL)
{
	Popup(URL);		
}

function Popup(data)
{
	var mywindow = window.open('', 'my div', 'height=400,width=600');
	//var ip = <??>
	mywindow.document.write('<html><head><title>my div</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write('<img src="/printing/' + data + '"></img>');
	mywindow.document.write('</body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	//mywindow.close();

	return true;
}

	
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

function postAjax(url, type, data, callback) { 
    // создаем Объект
    var oXmlHttp = createXMLHttp();
    // получение данных с формы
    var sBody = data;
    // подготовка, объявление заголовков
    oXmlHttp.open(type, url, true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
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
	return true;
}

function formatFileSize(size) {
	var a = Array("B", "KB", "MB", "GB", "TB", "PB");
	var pos = 0;
	while (size >= 1024) {
		size /= 1024;
		pos++;
	}
	return Math.round(size,2)+" "+a[pos];
}

function CloseOpen(e){
	e.style.display = (e.style.display != "none") ? 'none' : 'block';	
}

function CloseOpenTable(e){
	e.style.display = (e.style.display != "none") ? 'none' : 'table-row';	
}

function sleep(ms) {
	ms += new Date().getTime();
	while (new Date() < ms){}
}
function getcookie(name)
{
	var strCookie=document.cookie;
    var arrCookie=strCookie.split('; ');
    for (var i=0;i<arrCookie.length;i++)
    {
		var arr=arrCookie[i].split('=');
        if (arr[0]==name)
			return unescape(arr[1]);
    }
    return '';
}
function setcookie(name,value,expirehours)
{
	var cookieString=name+'='+escape(value);
    if (expirehours>0)
    {
		var date=new Date();
        date.setTime(date.getTime()+expirehours*3600*1000*3600);
        cookieString=cookieString+'; expires='+date.toGMTString() + "; path=/";
	}
    document.cookie = cookieString;
}

function getLocalStorage(name)
{
	var strCookie=window.localStorage.getItem("cookie");
    var arrCookie=strCookie.split(';');
    for (var i=0;i<arrCookie.length;i++)
    {
		var arr=arrCookie[i].split('=');
        if (arr[0]==name)
			return unescape(arr[1]);
    }
    return '';
}
function setLocalStorage(name, value)
{
	var cc = "";
	var arrCookie = window.localStorage.cookie.split(';');
	var cookieString = name + '=' + escape(value);
	var printtxt = false;
	
	for (var i = 0; i < arrCookie.length; i++)
    {
		var arr = arrCookie[i].split('=');
        if (arr[0] == name){
			cc = cc + cookieString + ";"
			printtxt = true;
		}
		else if(arr[0] != "")
			cc = cc + arr[0] + "=" + arr[1] + ";";
    }
	
	if(!printtxt)
		cc = cc + cookieString + ";"
	
    window.localStorage.setItem("cookie", (cc == "" ? cookieString : cc));
}

function asyncAlert(show){
    setTimeout(function() { alert(show); }, 1);
}