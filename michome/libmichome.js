const ActualMichomeVersion = 2.25;

function IsModuleInNetwork(mID){
	return postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		if(d.lastIndexOf("Ошибка") != -1)
			return false;
		else //Если гуд соеденились
			return true;
	});
}

function GetFirmwareVersionModule(mID, fun){
	postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		if(d.lastIndexOf("Ошибка") != -1)
			fun(0.0);
		else //Если гуд соеденились
			fun(parseFloat(d.split('/n')[9]));
	});
}

function SendCMD(device, cmd){
	return postAjax('api/setcmd.php?device='+ device +'&cmd='+ cmd, "GET", "", function(d){});
}
function SendCMDAlert(device, cmd){
	return postAjax('api/setcmd.php?device='+ device +'&cmd='+ cmd, "GET", "", function(d){alert(d);});
}

function IsStr(str, find){
	return str.indexOf(find) > -1;
}