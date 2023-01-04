function IsModuleInNetwork(mID){
	return postAjax('api/setcmd.php?device='+ mID +'&cmd='+ 'getmoduleinfo', "GET", "", function(d){
		if(d.lastIndexOf("Ошибка") != -1)
			return false;
		else //Если гуд соеденились
			return true;
	});
}