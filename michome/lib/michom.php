<?php
//require_once("/var/www/html/site/mysql.php");
require_once("_timeins.php");
require_once("_module.php");
require_once("_bddata.php");
require_once("_webpages.php");
require_once(__DIR__."/../VK/func.php");
require_once(__DIR__."/../../site/BotSet.php");
class MichomeAPI
{
    // объявление свойства
    public $Gateway = 'localhost';    
    public $link;
    
    // объявление метода
    public function __construct($Gateway, $link) {
       $this->Gateway = $Gateway;
       $this->link = $link;
    }
    
    public function TimeIns($device = 1, $type = FALSE, $datee = "", $IsArray = FALSE) {
       return _TimeIns($this->link, $device, $type, $datee, $IsArray);
    }
    
    public function SendCmd($device, $cmd, $timeout=2000) {
		if(!is_valid_ip($device)){
			$results = mysqli_query($this->link, "SELECT ip FROM modules WHERE mID = '$device'");

			while($row = $results->fetch_assoc())
				if($row['ip'] != "")
					$device = $row['ip'];
		}
       $ch = curl_init();
	   $cmd = trim($cmd, " \\/");
       curl_setopt($ch, CURLOPT_URL, 'http://'.$device.'/'.$cmd);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
       curl_setopt ($ch, CURLOPT_TIMEOUT_MS, $timeout);
       $m = @curl_exec($ch);
       curl_close($ch);
       
       if($m === FALSE)
           return "Ошибка соеденения с модулем";
       else
           return $m;
    }
	
	public function GetIPModule($moduleID){
		if(!is_valid_ip($moduleID)){
			$results = mysqli_query($this->link, "SELECT ip FROM modules WHERE mID = '$moduleID'");

			while($row = $results->fetch_assoc())
				if($row['ip'] != "")
					$moduleID = $row['ip'];
		}
		return $moduleID;
	}
    
    public function GetModuleInfo() {
       return _AllModuleInfo();
    }
    public function GetModulesIP() {
       return _GetModulesIP($this->link);
    }
    public function GetModules($ip) {
       return _GetModules($ip, $this);
    }
    public function GetAllModules() {
       return _GetModules($this->GetModulesIP(), $this);
    }
    public function GetSettings($ip) {
       return _GetSettings($this->link, $ip);
    }
	public function GetSettingsFromName($name) {
       return _GetSettingsFromName($this->link, $name);
    }
    
    public function GetPosledData($ip){
       return _GetPosledData($this->link, $ip);
    }
	public function GetDataForDay($ip, $day){
       return _GetDataForDay($this->link, $ip, $day);
    }
	
	public function GetWebPagesFromType($typePage){
       return _GetWebPagesFromType($this->link, $typePage);
    }
    
    public function SendNotification($text, $group){
        global $token;
       $results = mysqli_query($this->link, "SELECT `ID` FROM `UsersVK` WHERE `Enable`=1 AND `Type` = '".$group."'");
       while($row = $results->fetch_assoc()) {
            MessSend($row['ID'], $text, $token);
       }  
    }
    
    public function GetFromEndData($ip, $count){
       return _GetFromEndData($this->link, $ip, $count);
    }
    
    public function AddLog($ip, $type, $rssi, $log, $date){
       return _AddLog($this->link, $ip, $type, $rssi, $log, $date);
    }
    
    public function MaxMinTemper($ip, $date = 1){
       return _MaxMinTemper($this->link, $ip, $date);
    }
    
    public function GetSettingsFromType($type){
        return _GetSettingsFromType($this->link, $type);
    }
	
	public function IsCurrentDay($unixtime1, $unixtime2){
		$ddt = date("d", $unixtime1);
		$mdt = date("m", $unixtime1);
		$ydt = date("Y", $unixtime1);
		
		$dct = date("d", $unixtime2);
		$mct = date("m", $unixtime2);
		$yct = date("Y", $unixtime2);
		
		return ($ddt == $dct && $mdt == $mdt && $ydt == $ydt);
	}
    
    public function GetTemperatureDiap($device = 1, $type = FALSE, $datee = ""){
        $devicex = ($device != 1) ? ("`ip`='".$device."'") : 1;
        
        if($datee != ""){
            $dates = $datee;
            $req = $this->TimeIns($device, 'diap', $datee);
            //$req = file_get_contents("http://".$_SERVER['HTTP_HOST']."/michome/api/timeins.php?device=".$_GET['device']."&type=selday&date=".substr($dates, 0, -6));		
            $results = mysqli_query($this->link, "SELECT * FROM `michom` WHERE `id` >= '".explode(';',$req)[0]."' AND `id` <= '".explode(';',$req)[1]."' AND ".$devicex);
        }
        else{
            $results = mysqli_query($this->link, "SELECT * FROM `michom` WHERE ".$devicex);
        }

        $num = 0;
        //$data[] = [];
        //$date[] = [];
        
        while($row = $results->fetch_assoc()) {
            $data[] = intval($row['temp']);
            $date[] = $row['date'];
            $num = $num + 1;
        }
        $cart = array(
          "name" => "getdata",
          "col" => $num,
          "device" => $device,
          "data" => $data,
          "date" => $date
        );
        return $cart;
    }
    
    public function GetDateDevice($device = 1){
        $devicex = ($device != 1) ? ("`ip`='".$device."'") : 1;
        $num = 0;
        $date[] = [];
        $date[] = 'никого';
         
        $results = mysqli_query($this->link, "SELECT * FROM `logging` WHERE ".$devicex);
                         
        while($row = $results->fetch_assoc()) {
            $date[] = $row['date'];
            $num = $num + 1;
        }
        
        $results1 = mysqli_query($this->link, "SELECT * FROM `michom` WHERE ".$devicex);
                       
        while($row1 = $results1->fetch_assoc()) {
            $date[] = $row1['date'];
            $num = $num + 1;
        }     
        $cart = array(
          "name" => "getdata",
          "col" => $num,
          "device" => $device,
          "date" => $date
        );
        return $cart;
    }
    
	public function GetWebConstant($strl){
		$str = $strl;
        //^gr_192.168.1.11_Temp_curday;
        while(IsStr($str, "^gr_")){
            $expl = substr($str, strpos($str, "^gr")+4, (strpos($str, ";") - (strpos($str, "^gr")+4)));     
            $module = str_replace("-", "_", explode('_', $expl)[0]);
			$typedata = explode('_', $expl)[1];
			$period = explode('_', $expl)[2];
			
			$Timeins = NULL;
			if($period == "curday")
				$Timeins = $this->TimeIns($module, 'selday', date("Y-m-d"), true);
			
            $rd = "<span><img src=\"grafick.php?ip=".$module."&type=".$typedata."&start=".$Timeins[0]."&period=".$Timeins[2]."\"/></span>";
            $str = str_replace("^gr_".$expl.";", $rd, $str);      
        }
		return $str;
	}
	
    public function GetConstant($strl){
        $str = $strl;
        //^rm_192.168.1.11_Temp;
        while(IsStr($str, "^rm_")){
            $expl = substr($str, strpos($str, "^rm")+4, (strpos($str, ";") - (strpos($str, "^rm")+4)));     
            $rd = $this->GetPosledData(str_replace("-", "_", explode('_', $expl)[0]))->GetFromName(explode('_', $expl)[1]);
            $rd = str_replace("_","-", $rd);
            $str = str_replace("^rm_".$expl.";", $rd, $str);      
        }
        //^rmavg_192.168.1.11_Temp_5;
        while(IsStr($str, "^rmavg_")){
            $expl = substr($str, strpos($str, "^rmavg")+7, (strpos($str, ";") - (strpos($str, "^rmavg")+7)));     
            $rd = $this->GetFromEndData(str_replace("-", "_", explode('_', $expl)[0]), intval(explode('_', $expl)[2]))->BDDatas();
            $func = function($value) use ($expl): string {
                $t = $value->GetFromName(explode('_', $expl)[1]);
                return str_replace("_","-", $t);
            };
            $rd = array_map($func, $rd);
            $rd = round(array_sum($rd) / count($rd), 2);
            $str = str_replace("^rmavg_".$expl.";", $rd, $str);      
        }
		//^send_192.168.1.14_meteo;
		while(IsStr($str, "^send_")){
            $expl = substr($str, strpos($str, "^send")+6, (strpos($str, ";") - (strpos($str, "^send")+6)));     
            $ip = explode('_', $expl)[0];
            $req = explode('_', $expl)[1];
            $rd = $this->SendCmd($ip, $req);
            $str = str_replace("^send_".$expl.";", $rd, $str);      
        }          
        return $str;
    }
    public function GetButton($strl, $m, $p, $c){
        $str = $strl;
        //^bt_192.168.1.34_1_1_1;
        while(IsStr($str, "^bt")){
            $expl = substr($str, strpos($str, "^bt")+4, (strpos($str, ";") - (strpos($str, "^bt")+4)));
            
            if(count(explode('_', $expl)) == 4) $fullif = '1';
            elseif(count(explode('_', $expl)) == 2) $fullif = '0';
            elseif(count(explode('_', $expl)) == 3) $fullif = '3';
            else $fullif = '2';
            
            $md = explode('_', $expl)[0];                                   
                       
            if($fullif == '1'){
                $pi = explode('_', $expl)[1];
                $co = explode('_', $expl)[2];           
                if($m == $md & $pi == $p & $co == $c) $rd = '1';
                else $rd = '0';                   
                $rd = "^if_".$rd."==".explode('_', $expl)[3].";";
            }
            elseif($fullif == '3'){
                $pi = explode('_', $expl)[1];
                $co = explode('_', $expl)[2];           
                if($m == $md & $pi == $p & $co == $c) $rd = '1';
                else $rd = '0';                   
                $rd = "^if_".$rd."==1;";
            }
            elseif($fullif == '0'){
                $pi = explode('_', $expl)[1];
                if($m == $md & $pi == $p) $rd = '1';
                else $rd = '0';                   
                $rd = "^if_".$rd."==1;";
            }
            else{
                if($m == $md) $rd = '1';
                else $rd = '0';                   
                $rd = "^if_".$rd."==1;";
            }          
            
            $str = str_replace("^bt_".$expl.";", $rd, $str);      
        }
        return $str;
    }
    public function GetIFs($strl, $enb){
        $Name = $strl;
        $enable = $enb;
        if($enb == '0') return [$Name, $enable];
        while(IsStr($Name, "^if")){
            $expl = substr($Name, strpos($Name, "^if")+4, (strpos($Name, ";") - (strpos($Name, "^if")+4))); 
            if(IsStr($expl, "<")){  if(doubleval(preg_replace("/[^-0-9\.]/","",explode('<', $expl)[0])) > doubleval(preg_replace("/[^-0-9\.]/","",explode('<', $expl)[1]))){ $enable = "0"; echo "1>2 ";}}
            elseif(IsStr($expl, ">")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('>', $expl)[0])) < doubleval(preg_replace("/[^-0-9\.]/","",explode('>', $expl)[1]))){ $enable = "0"; echo "1<2 ";}}
            elseif(IsStr($expl, "<=")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('<=', $expl)[0])) >= doubleval(preg_replace("/[^-0-9\.]/","",explode('<=', $expl)[1]))){ $enable = "0"; echo "1>=2 ";}}
            elseif(IsStr($expl, ">=")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('>=', $expl)[0])) <= doubleval(preg_replace("/[^-0-9\.]/","",explode('>=', $expl)[1]))){ $enable = "0"; echo "1<=2 ";}}
            elseif(IsStr($expl, "==")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('==', $expl)[0])) != doubleval(preg_replace("/[^-0-9\.]/","",explode('==', $expl)[1]))){ $enable = "0"; echo "1!=2 ";}}
            elseif(IsStr($expl, "!=")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('!=', $expl)[0])) == doubleval(preg_replace("/[^-0-9\.]/","",explode('!=', $expl)[1]))){ $enable = "0"; echo "1==2 ";}}
            $Name = str_replace("^if_".$expl.";", "", $Name);
        }
		while(IsStr($Name, "^en")){
            $expl = substr($Name, strpos($Name, "^en")+4, (strpos($Name, ";") - (strpos($Name, "^en")+4))); 			
			$results = mysqli_query($this->link, "SELECT `Enable` FROM `scenes` WHERE `ID` = \"".$expl."\"");//Жестко качаем все из БД
				while($row = $results->fetch_assoc()) {
					if($row['Enable'] == '0') $enable = '0';
				}
            $Name = str_replace("^en_".$expl.";", "", $Name);
        }
		while(IsStr($Name, "^cs")){
            $expl = substr($Name, strpos($Name, "^cs")+4, (strpos($Name, ";") - (strpos($Name, "^cs")+4))); 			
			$results = mysqli_query($this->link, "SELECT `CSE` FROM `scenes` WHERE `ID` = \"".$expl."\"");//Жестко качаем все из БД
				while($row = $results->fetch_assoc()) {
					if($row['CSE'] == '00:00:00') $enable = '0';
				}
            $Name = str_replace("^cs_".$expl.";", "", $Name);
        }
        /*if(IsStr($Name, "^ond")){
            $results = mysqli_query($this->link, "SELECT `CSE` FROM `scenes` WHERE `Name` = \"".$strl."\"");//Жестко качаем все из БД
				while($row = $results->fetch_assoc()) {
					if($row['CSE'] == '00:00:00') $enable = '0';
				}
            $Name = str_replace("^ond", "", $Name);
        }*/
        return [$Name, $enable];
    }
    public function GetNotification($strl){
        $str = $strl;
        //^sn_all_Привет, мир;
        while(IsStr($str, "^sn")){
            $expl = substr($str, strpos($str, "^sn")+4, (strpos($str, ";") - (strpos($str, "^sn")+4)));     
            $text = explode('_', $expl)[1];
            $group = explode('_', $expl)[0];
            $this->SendNotification($text, $group);
            $str = str_replace("^sn_".$expl.";", "", $str);
            //echo "SendNotification ";
        }
        return $str;
    }
	public function ResetScenesTimer(){
		$todayH = date("H");//Получаем часы
		$todayM = date("i");//Получаем минуты
		$today = ($todayH*60)+$todayM;//Переводим в минуты
		$results = mysqli_query($this->link, "SELECT * FROM `scenes`");//Жестко качаем все из БД
		while($row = $results->fetch_assoc()) {
			if($todayH == 0 & $todayM == 0){
				mysqli_query($this->link, "UPDATE `scenes` SET `CSE`='00:00' WHERE `ID`=".$row['ID']);//Полночь. Сбрасываем таймер
			}
		}
	}
}
function IsStr($str, $search){
    if(strpos($str, $search) !== FALSE) return true;
    else return false;
}
function is_valid_ip($ip) {
    $ipv4 = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
    $ipv6 = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';
    return preg_match("/^($ipv4|$ipv6)\$/", trim($ip));
}
?>
