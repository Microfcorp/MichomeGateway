const ActualMichomeVersion = 2.22;

function IsModuleInNetwork(mID){
	return postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		if(d.lastIndexOf("Ошибка") != -1)
			return false;
		else //Если гуд соеденились
			return true;
	});
}

async function GetFirmwareVersionModule(mID){
	var ver = "";
	var isset = false;
	postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		if(d.lastIndexOf("Ошибка") != -1)
			ver = 0.0;
		else //Если гуд соеденились
			ver = parseFloat(d.split('/n')[9]);
		isset = true;
		//console.log(d);
	});
	//while(!isset);
	return ver;
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