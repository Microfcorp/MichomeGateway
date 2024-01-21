<?php 
function _AllModuleInfo(){
	global $MODs;
	$bas = array(
			new ModuleInfo('termometr','Модуль термометра',[],[["termometr.php?module={id}", "Управление термометром"]]),
            new ModuleInfo('Informetr','Модуль информетра',[['onlight','Включить подсветку'],['offlight','Выключить подсветку'],['test','Тест системы']],[]),
            new ModuleInfo('msinfoo','Модуль сбора информации',[],[]),
            //new ModuleInfo('hdc1080','Модуль метеостанции',[],[]),
            new ModuleInfo('StudioLight','Модуль освещения',[],[["studiolight.php?module={id}", "Управление освещением"]]),
            new ModuleInfo('meteostation','Модуль метеостанции',[],[["meteostation.php?module={id}", "Управление метеостанцией"]]),
           );
	foreach($MODs as $tmp)
	{	
		if($tmp->Type == MichomeModuleType::ModuleCore){
			$bas[] = new ModuleInfo($tmp->BaseClass->TypeModule, $tmp->BaseClass->DescModule, $tmp->BaseClass->CommandsModule, $tmp->BaseClass->GetMichomePage());
		}
	}	   
    return $bas;
}

function _GetModuleInfoFromType($type){
    foreach(_AllModuleInfo() as $tmp){		
        if(mb_strtolower($tmp->Type) == mb_strtolower($type))
            return $tmp;
    }
    return false;
}

function _GetSettings($link, $ip){
    $results = mysqli_query($link, "SELECT `setting` FROM modules WHERE `ip` = '$ip'");
    while($row = $results->fetch_assoc()) {
        return 'SCount='.(substr_count($row['setting'], ';')+2).';'.$row['setting'];
    }   
}

function _GetSettingsFromName($link, $name){
    $results = mysqli_query($link, "SELECT `setting` FROM modules WHERE `mID` = '$name' limit 1");
    while($row = $results->fetch_assoc()) {
        return 'SCount='.(substr_count($row['setting'], ';')+2).';'.$row['setting'];
    }   
}

function _GetModulesIP($link){
    $retur = [];
    $results = mysqli_query($link, "SELECT ip FROM modules");
    while($row = $results->fetch_assoc()) {
        if($row['ip'] != "" && $row['ip'] != "localhost"){
            $retur[] = $row['ip'];
        }
    }        
    return $retur;
}

function _GetModule($device, $API){
    $m = $API->SendCmd($device, "/getmoduleinfo", 2000, true);
	return ($m != false ? explode("/n", $m) : false);
}

function _GetAllModulesBD($link){
    $retur = [];
    $results = mysqli_query($link, "SELECT * FROM modules ORDER BY `laststart` DESC LIMIT 1000");
    while($row = $results->fetch_assoc()) {
        if($row['ip'] != "" && $row['ip'] != "localhost"){
			$retu = array();
            $retu['ip'] = $row['ip'];
            $retu['mac'] = $row['mac'];
            $retu['mid'] = $row['mID'];
            $retu['type'] = $row['type'];
            $retu['id'] = $row['id'];
            $retu['laststart'] = $row['laststart'];
			$retur[] = $retu;
        }
    }        
    return $retur;
}

function _GetSettingsFromType($link, $type){
	foreach($MODs as $tmp){
		if($MODs->Type == MichomeModuleType::ModuleCore && $MODs->BaseClass->TypeModule == $type){
			return $MODs->BaseClass->DefaultSettings;
		}
	}
    if($type == "termometr" || $type == "meteostation") return "update=600000";
    elseif($type == "msinfoo") return "update=602000";
    elseif($type == "Informetr") return "update=300000;timeupdate=10000;running=1";
    elseif($type == "hdc1080") return "update=604000";
    elseif($type == "StudioLight") return "update=606000;logging=0;adcread=100;clicktimeout=500";
    else return "update=600000";
}

function _SendGET($url, $timeout=2000){
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
    curl_setopt ($ch, CURLOPT_TIMEOUT_MS, $timeout);
    $m = @curl_exec($ch);
    curl_close($ch);
	return $m;
}

class ModuleInfo
{
    public $Type;
    public $Descreption;
    public $URL;
    public $MURL;
    
    public function __construct($Type, $Descreption, $URL, $MURL) {
       $baseURL = [['refresh','Обновить данные'],['restart','Перезагрузить']];
       $this->Type = $Type;
       $this->Descreption = $Descreption;
       $this->URL = array_merge($URL, $baseURL);
       $this->MURL = $MURL;
    }
}
?>