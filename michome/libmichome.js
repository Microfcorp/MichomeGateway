const ActualMichomeVersion = 2.25;

function IsModuleInNetwork(mID){
	return postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		if(d.lastIndexOf("Ошибка") != -1)
			return false;
		else //Если гуд соеденились
			return true;
	});
}

function IsModuleInNetworkCallBack(mID, func){
	return postAjax('api/setcmd.php?device='+ mID +'&apitype=json&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		var jsReq = JSON.parse(d);
		if(!jsReq['status'])
			func(false);
		else //Если гуд соеденились
			func(true);
	});
}

function GetFirmwareVersionModule(mID, func){
	postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		if(d.lastIndexOf("Ошибка") != -1)
			func(0.0);
		else //Если гуд соеденились
			func(parseFloat(d.split('/n')[9]));
	});
}

function SendCMD(device, cmd){
	return postAjax('api/setcmd.php?device='+ device +'&cmd='+ cmd, "GET", "", function(d){});
}
function SendCMDAlert(device, cmd){
	return postAjax('api/setcmd.php?device='+ device +'&cmd='+ cmd, "GET", "", function(d){alert(d);});
}

function IsStr(str, find){
	if(Array.isArray(find)){ //Если ищем по массиву вхождений
		for(var i = 0; i < find.length; i++){
			if(IsStr(str, find[i]))
				return true;
		}
		return false;
	}
	else //Если ищем по единственному вхождению
		return str.indexOf(find) > -1;
}

function downloadAsFile(data, metaType, fileName) {
	let a = document.createElement("a");
	let file = new Blob([data], {type: metaType});
	a.href = URL.createObjectURL(file);
	a.download = fileName;
	a.click();
}