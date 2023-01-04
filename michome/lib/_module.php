<?php 
function _AllModuleInfo(){
    return [new ModuleInfo('termometr','termometr_okno','Модуль уличного термометра',[]),
            new ModuleInfo('Informetr','Informetr_Pogoda','Модуль информетра',[['onlight','Включить подсветку'],['offlight','Выключить подсветку'],['test','Тест системы']]),
            new ModuleInfo('msinfoo','sborinfo_tv','Модуль сбора информации',[]),
            new ModuleInfo('hdc1080','hdc1080_garadze','Модуль гаражной метеостанции',[['setlight?p=1','Включить реле'],['setlight?p=0','Выключить реле']]),
            new ModuleInfo('StudioLight','StudioLight_Main','Модуль объемного освещения',[]),
            new ModuleInfo('StudioLight','LightStudio_Elka','Модуль освещения ели',[]),
           ];
}

function _GetModule($id){
    foreach(_AllModuleInfo() as $tmp){
        if($tmp->ID == $id)
            return $tmp;
    }
    return null;
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
        if($row['ip'] != "" & $row['ip'] != "localhost"){
            $retur[] = $row['ip'];
        }
    }        
    return $retur;
}

function _GetModules($rete, $API){
    $ret = [];    
    foreach($rete as $tmp){
        $ret[] = new Module($tmp, $API);
    }
    return $ret;
}

function _GetSettingsFromType($link, $type){
    if($type == "termometr") return "update=600000";
    elseif($type == "msinfoo") return "update=602000";
    elseif($type == "Informetr") return "update=300000;timeupdate=10000;running=1";
    elseif($type == "hdc1080") return "update=604000";
    elseif($type == "StudioLight") return "update=606000;logging=1;adcread=100;clicktimeout=500";
    else return "";
}

class ModuleInfo
{
    public $Type;
    public $ID;
    public $Descreption;
    public $URL;
    
    public function __construct($Type, $ID, $Descreption, $URL) {
       $baseURL = [['refresh','Обновить данные'],['restart','Перезагрузить'],['clearlogs','Отчистить логи'],['cleardatalogs','Отчистить логи данных']];
       $this->Type = $Type;
       $this->ID = $ID;
       $this->Descreption = $Descreption;
       $this->URL = array_merge($URL, $baseURL);
    }
}

class Module{
    
    public $IP;
    public $RSSI;
    public $ModuleInfo;
    public $IsOnline;
    public $PosledDate;
    public $FlashSize;
    
    public function __construct($ip, $API) {
       $this->IP = $ip;
       
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, 'http://'.$ip . "/getmoduleinfo");
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT_MS, 800);
       curl_setopt ($ch, CURLOPT_TIMEOUT_MS, 800);
       
       $m = @curl_exec($ch);

       if($m === FALSE){
           $this->IsOnline = FALSE;
           $this->PosledDate = $API->GetPosledData($ip)->Date;
           //echo $ip;
           //var_dump($API->GetDateDevice($ip)['date']);
       }
       else{
           if($m != 'Not found'){
               $mod = explode('/n', $m);   
                //var_dump($mod);
               $this->RSSI = $mod[2];
               $this->ModuleInfo = _GetModule($mod[0]);
               $this->FlashSize = $mod[4];
               $this->IsOnline = TRUE;
               $this->PosledDate = $API->GetPosledData($ip)->Date;
           }          
       }
    }
}
?>