<?php
set_time_limit(5);
define("ServerPath", __DIR__."/../");
$MODs = array();
//require_once("/var/www/html/site/mysql.php");
require_once("_settings.php");
require_once("_timeins.php");
require_once("_module.php");
require_once("_bddata.php");
require_once("_webpages.php");
require_once("_rooms.php");
require_once("moduleSystem.php");
require_once(__DIR__."/../VK/func.php");
require_once(__DIR__."/../TG/func.php");
require_once(__DIR__."/../../site/BotSet.php");
class MichomeAPI
{
    // объявление свойства
    public $Gateway = 'localhost';    
    public $link;
	public $constantAction = array();
	public $portServer = 80;
    
    // объявление метода
    public function __construct($Gateway, $link) {
	   global $MODs;
       $this->Gateway = $Gateway;
       $this->link = $link;
	   $this->portServer = $this->GetSettingORCreate('ServerPort', '80', 'Порт сервера Michome')->Value;	  
	   
	   $this->ConstantON("Временные", "unix", "^unix_2023-05-12 11:01:12; - преобразует время в UNIXTime", function($expl): string {return strtotime($expl[0]);}, 1);
	   $this->ConstantON("Временные", "cd", "^cd; - Возвращает текущее время в виде 2023-05-12 11:01:12", function($expl): string {return date("Y-m-d H:i:s");}, 0);
	   $this->ConstantON("Математическе", "summ", "^summ_2_2; - Возвращает сумму двух аругентов", function($expl): string {return intval($expl[0]) + intval($expl[1]);}, 2);
	   $this->ConstantON("Математическе", "diff", "^diff_4_2; - Возвращает разность двух аругентов", function($expl): string {return intval($expl[0]) - intval($expl[1]);}, 2);
	   $this->ConstantON("Математическе", "mult", "^mult_2_2; - Возвращает произведение двух аругентов", function($expl): string {return intval($expl[0]) * intval($expl[1]);}, 2);
	   $this->ConstantON("Математическе", "div", "^div_4_2; - Возвращает частное двух аругентов", function($expl): string {return intval($expl[0]) / intval($expl[1]);}, 2);
	   $this->ConstantON("Погодные", "watterb", "^watterb_754; - Возвращает температуру закипания воды для давления", function($expl): string {return round((234.175 * log10((floatval($expl[0]) * 133.332) / 6.1078)) / (17.08085 - log10((floatval($expl[0]) * 133.332) / 6.1078)));}, 1);
	   $this->ConstantON("Данные из БД", "rmmax", "^rmmax_192.168.1.11_Temp_5h; - Возвращает максимальное значение за диаппазон", function($expl): string 
	   {
		   $rd = $this->GetFromEndData(str_ireplace("-", "_", $expl[0]), $expl[2])->SelectFloat($expl[1]);
		   return max($rd);
	   }, 3);
	   $this->ConstantON("Данные из БД", "rmmin", "^rmmin_192.168.1.11_Temp_5h; - Возвращает минимальное значение за диаппазон", function($expl): string 
	   {
		   $rd = $this->GetFromEndData(str_ireplace("-", "_", $expl[0]), $expl[2])->SelectFloat($expl[1]);
		   return min($rd);
	   }, 3);
	   $this->ConstantON("Данные из БД", "rmup", "^rmup_192.168.1.11_Temp; - Запрашивает данные данные с модуля", function($expl): string 
	   {
		   $rd = $this->SendCmdUpdate(str_ireplace("-", "_", $expl[0]));
		   return "";
	   }, 3);
	   $this->ConstantON("Данные из БД", "grurl", "^grurl_192.168.1.11_Temp_curday; - Получает ссылку на график", function($expl): string 
	   {		   
		   $module = str_ireplace("-", "_", $expl[0]);
		   $typedata = $expl[1];
		   $period = $expl[2];
			
		   $Timeins = $this->GraphicTimeInt($module, $period);
		   
		   $host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $this->Gateway);
			
		   $rd = "http://".$host.(!IsStr($host, ":") ? ":".$this->portServer : "")."/michome/grafick.php?ip=".$expl[0]."&type=".$typedata."&start=".$Timeins[0]."&period=".$Timeins[2]."&mode=jpg&width=1080&height=620&timestamp=1";
		   		   		   
		   return $rd;
	   }, 4);
	   $this->ConstantON("Уведомления", "sg", "^sg_all_Привет, мир_192.168.1.11_Temp_curday; - Отправляет всем уведомление с графиками", function($expl): string 
	   {
		   $group = $expl[0];		   		   
		   $countG = floor((count($expl) - 2) / 3);
		   
		   $files = [];
		   $txt = [];
		   
		   for($i = 1; $i <= $countG * 3; $i += 4){
			   $text = $expl[$i];
			   $text = str_replace("--", "_", $text);
			   
			   $module = str_ireplace("-", "_", $expl[$i + 1]);
			   $typedata = $expl[$i + 2];
			   $period = $expl[$i + 3];
				
			   $Timeins = $this->GraphicTimeInt($module, $period);
			
				$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $this->Gateway);
			
			   $rd = "http://".$host.(!IsStr($host, ":") ? ":".$this->portServer : "")."/michome/grafick.php?ip=".$module."&type=".$typedata."&start=".$Timeins[0]."&period=".$Timeins[2]."&mode=jpg&width=1080&height=620&timestamp=1&file=styles/sgr".($i-2).".jpg";
			   file_get_contents($rd);			 			 
			   
			   $files[] = "styles/sgr".($i-2).".jpg";
			   $txt[] = $text;
		   }
		   
		   if(count($files) > 1)
               $this->SendImageNotification($txt, $group, $files);
		   else
			   $this->SendImageNotification($txt[0], $group, $files[0]);
		   
		   foreach($files as $tmp){
			   unlink(__DIR__."/../".$tmp);
		   }
		   
		   return "";
	   }, 4);
	   
	   $this->ConstantON("Уведомления", "si", "^si_all_Привет, мир_http://localhost/michome/styles/button.png; - Отправляет всем уведомление с изображением(-ями)", function($expl): string 
	   {
		   $group = $expl[0];		   		   
		   $countG = floor((count($expl) - 1) / 2);
		   
		   $files = [];
		   $txt = [];		   
		   
		   for($i = 1; $i <= $countG * 2; $i += 2){
			   $txt[] = str_ireplace("--", "_", $expl[$i]);			   
			   $files[] = str_ireplace("--", "_", $expl[$i + 1]);						
		   }
		   
		   if(count($files) > 1)
               $this->SendImageNotification($txt, $group, $files);
		   else
			   $this->SendImageNotification($txt[0], $group, $files[0]);		  		   
		   return "";
	   }, 3);
	   
	   foreach($MODs as $tmp){
		   if($tmp->BaseClass->InitialFunction)
			   $tmp->BaseClass->InitialFunction->call($this, $tmp);
	   }
    }
    
	//Возвращает объект с диапазоном id записей по устройству, типу и дате
    public function TimeIns($device = 1, $type = FALSE, $datee = "", $IsArray = FALSE) {
       return _TimeIns($this->link, $device, $type, $datee, $IsArray);
    }
	
	//Возвращает объект с диапазоном id записей по устройству, типу и дате
    public function GraphicTimeInt($module, $period = 'curday') {
       $Timeins = NULL;
	   if($period == "curday")
		   $Timeins = $this->TimeIns($module, 'selday', date("Y-m-d"), true);
	   return $Timeins;
    }
	
	public function SendCmdUpdate($device, $timeout=2000, $isOOP = false){
		return $this->SendCmd($device, "update", $timeout, $isOOP);
	}
    
	//Отправляет комманду на модуль
    public function SendCmd($device, $cmd, $timeout=2000, $isOOP = false) {
		if(!is_valid_ip($device)){
			$device = $this->GetIPModule($device);
		}
	   $cmd = trim($cmd, " \\/");	   
	   $m = _SendGET('http://'.$device.'/'.$cmd, $timeout);
       
       if($m === FALSE)
           return ($isOOP ? FALSE : "Ошибка соеденения с модулем");
       else
           return $m;
    }
	
	//Получает IP адрес модуля по его имени или mac
	public function GetIPModule($moduleID){ 
		if(!is_valid_ip($moduleID)){
			$results = mysqli_query($this->link, "SELECT `ip` FROM modules WHERE `mID` = '$moduleID' OR `MAC` = '$moduleID'");

			while($row = $results->fetch_assoc())
				if($row['ip'] != "")
					$moduleID = $row['ip'];
		}
		return $moduleID;
	}
	
	//Получает ID модуля по его имени или mac
	public function GetIDModule($moduleIP){ 
		if(is_valid_ip($moduleIP)){
			$results = mysqli_query($this->link, "SELECT `mID` FROM modules WHERE `ip` = '$moduleIP'");

			while($row = $results->fetch_assoc())
				if($row['mID'] != "")
					$moduleIP = $row['mID'];
		}
		return $moduleIP;
	}
    
	//Получает описание всех поддерживаемых модулей
    public function GetModulesInfo() { 
       return _AllModuleInfo();
    }
	
	//Получает описание поддерживаемых модулей с поиком по типу	
	public function GetModuleInfoFromType($type) {	
       return _GetModuleInfoFromType($type);
    }
	
	//Получает IP адреса всех модулей
    public function GetModulesIP() { 
       return _GetModulesIP($this->link);
    }
	
	//Получает информацию о модуле через /getmoduleinfo по его ip (mac) или имени
    public function GetModule($device) { 
       return _GetModule($device, $this);
    }
	
	//Поулчает массив всех модулей из базы данных
	public function GetAllModulesBD() { 
       return _GetAllModulesBD($this->link);
    }
	
	//Получает настройки для модуля
    public function GetSettings($ip) {
       return _GetSettings($this->link, $ip);
    }
	public function GetSettingsFromName($name) {
       return _GetSettingsFromName($this->link, $name);
    }
    
	public function GetFromEndData($ip, $count){
       return _GetFromEndData($this->link, $ip, $count);
    }
    public function GetPosledData($ip){
       return _GetPosledData($this->link, $ip);
    }
	public function GetDataForDay($ip, $day, $IsLog = true){
       return _GetDataForDay($this->link, $ip, $day, $IsLog);
    }
	public function GetDataRange($ip, $startID, $endID, $IsLog = false){
       return _GetDataRange($this->link, $ip, $startID, $endID, $IsLog);
    }
	
	public function GetWebPagesFromType($typePage){
       return _GetWebPagesFromType($this->link, $typePage);
    }
	public function AddWebPage($typePage){
       return _AddWebPage($this->link, $typePage);
    }
	public function SetWebPage($id, $subtype, $name, $value, $newid = -1){
       return _SetWebPage($this->link, $id, $subtype, $name, $value, $newid);
    }
	public function RemoveWebPage($id){
       return _RemoveWebPage($this->link, $id);
    }
    
	//Отпраляет уведомление пользователям с указанной группой (несколькими группами через ,)
    public function SendNotification($text, $group){
       global $token;
	   $gr = "";
	   if(is_numeric($group)){ //Если id
		   $gr = " AND `ID` = '".$group."'";
	   }
	   else{ //Если группа
		   foreach(explode(',', $group) as $tmp)
				$gr = $gr." AND `Type` = '".$group."'";
	   }
			
       $results = mysqli_query($this->link, "SELECT `ID` FROM `UsersVK` WHERE `Enable`=1 AND `Messanger` = 'VK'".$gr);
       while($row = $results->fetch_assoc()) {
            MessSend($row['ID'], $text, $token);
       }  
	   $results = mysqli_query($this->link, "SELECT `ID` FROM `UsersVK` WHERE `Enable`=1 AND `Messanger` = 'TG'".$gr);
       while($row = $results->fetch_assoc()) {
            MessageSendTG($row['ID'], $text);
       } 
    }
	//Отпраляет уведомление с картинкой пользователям с указанной группой (несколькими группами через ,)
    public function SendImageNotification($text, $group, $file){
       global $token;
	   $gr = "";
	   if(is_numeric($group)){ //Если id
		   $gr = " AND `ID` = '".$group."'";
	   }
	   else{ //Если группа
		   foreach(explode(',', $group) as $tmp)
				$gr = $gr." AND `Type` = '".$group."'";
	   }
			
       /*$results = mysqli_query($this->link, "SELECT `ID` FROM `UsersVK` WHERE `Enable`=1 AND `Messanger` = 'VK'".$gr);
       while($row = $results->fetch_assoc()) {
            MessSend($row['ID'], $text, $token);
       }*/
	   $results = mysqli_query($this->link, "SELECT `ID` FROM `UsersVK` WHERE `Enable`=1 AND `Messanger` = 'TG'".$gr);
       while($row = $results->fetch_assoc()) {
            ImageSendTG($row['ID'], $text, $file);
       } 
    }
	
	//Возвращает массив с коммандами для бота
	public function GetBotCmd(){
	   $arr = array();
	   $results = mysqli_query($this->link, "SELECT * FROM `botcmd` WHERE 1");
       while($row = $results->fetch_assoc()) {
            $arr[] = array("ID"=>$row['ID'], "Name"=>$row['Name'], "Desc"=>$row['Desc'], "Cmd"=>$row['Cmd'], "Enabled"=>$row['Enabled']);
       }
	   return $arr;
	}
    
	//Добавляет лог
    public function AddLog($ip, $type, $rssi, $log, $date){
       return _AddLog($this->link, $ip, $type, $rssi, $log, $date);
    }
    
	//Возвращает максимальное и минимальное значение из БД
    public function MaxMinValue($device, $type, $date = 1){
       return _MaxMinValue($this->link, $device, $type, $date);
    }
    
    public function GetSettingsFromType($type){
        return _GetSettingsFromType($this->link, $type);
    }
	
	//Возвращает массив всех комнат
	public function GetRooms(){
        return _GetRooms($this->link);
    }
	
	public function AddRoom(){
        return _AddRoom($this->link);
    }
	
	public function RemoveRoom($id){
        return _RemoveRoom($this->link, $id);
    }
	
	public function EditRoom($id, $name, $data, $modules){
        return _EditRoom($this->link, $id, $name, $data, $modules);
    }
	
	public function GetAllSetting(){
        return _GetAllSetting($this->link);
    }
	
	public function GetSetting($Name){
        return _GetSetting($this->link, $Name);
    }
	
	public function GetSettingByID($ID){
        return _GetSettingByID($this->link, $ID);
    }
	
	public function GetSettingORCreate($Name, $ValueDefault, $Desc){
        return _GetSettingORCreate($this->link, $Name, $ValueDefault, $Desc);
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
		if(!ValidateConst($str)){
			$this->AddLog("localhost", "Incorrect constant", "0", "Incorrecting constant on: " . $str, date("Y-m-d H:i:s"));
			return "Ошибка преобразования констант";
		}
        //^gr_192.168.1.11_Temp_curday;
        //^gr_192.168.1.11_Temp_curday_median;
        //^gr_192.168.1.11_Temp_curday_median_540_325;
        //^gr_192.168.1.11_Temp_curday_median_540_325_auto_png;
        //^gr_192.168.1.11_Temp_curday_median_540_325_auto_png_timestamp;
        while(IsStr($str, "^gr_")){
            $expl = substr($str, stripos($str, "^gr_")+4, (stripos($str, ";", stripos($str, "^gr_")) - (stripos($str, "^gr_")+4)));     
            $module = str_ireplace("-", "_", explode('_', $expl)[0]);
			$typedata = explode('_', $expl)[1];
			$period = explode('_', $expl)[2];
			$filter = isset(explode('_', $expl)[3]) ? explode('_', $expl)[3] : false;
			$width = isset(explode('_', $expl)[4]) ? explode('_', $expl)[4] : false;
			$heigth = isset(explode('_', $expl)[5]) ? explode('_', $expl)[5] : false;
			$parametr = isset(explode('_', $expl)[6]) ? explode('_', $expl)[6] : false;
			$imagetype = isset(explode('_', $expl)[7]) ? explode('_', $expl)[7] : false;
			$timestamp = isset(explode('_', $expl)[8]) ? explode('_', $expl)[8] == 'timestamp' : false;
			
			$Timeins = $Timeins = $this->GraphicTimeInt($module, $period);
			
            $rd = "<span><img class=\"graphics\" src=\"grafick.php?ip=".$module."&type=".$typedata."&start=".$Timeins[0]."&period=".$Timeins[2].(!$width ? "" : "&width=".$width).(!$heigth ? "" : "&height=".$heigth).(!$parametr ? "" : "&par=".$parametr).(!$imagetype ? "" : "&mode=".$imagetype).(!$filter ? "" : "&filter=".$filter).(!$timestamp ? "" : "&timestamp=1")."\"/></span>";
            $str = str_ireplace("^gr_".$expl.";", $rd, $str);      
        }
		//^lk_ircontrol.php?type=sab_Управление сабвуфером;
		while(IsStr($str, "^lk_")){
            $expl = substr($str, stripos($str, "^lk_")+4, (stripos($str, ";", stripos($str, "^lk_")) - (stripos($str, "^lk_")+4)));     
            $link = str_ireplace("-", "_", explode('_', $expl)[0]);
			$text = explode('_', $expl)[1];
						
            $rd = "<span><a class=\"linkMain\" href='".$link."'>".$text."</a></span>";
            $str = str_ireplace("^lk_".$expl.";", $rd, $str);      
        }
		//^sp_Данные;
		/*while(IsStr($str, "^sp_")){
            $expl = substr($str, stripos($str, "^sp_")+4, (stripos($str, ";", stripos($str, "^sp_")) - (stripos($str, "^sp_")+4)));     
            $dat = str_ireplace("-", "_", explode('_', $expl)[0]);
						
            $rd = "<span class=\"roomspan\">".$dat."</span>";
            $str = str_ireplace("^sp_".$expl.";", $rd, $str);      
        }*/
		//^tx_Элемент_Подсказка;
		while(IsStr($str, "^tx_")){
            $expl = substr($str, stripos($str, "^tx_")+4, (stripos($str, ";", stripos($str, "^tx_")) - (stripos($str, "^tx_")+4)));     
            $dat = explode('_', $expl)[0];
            $hov = explode('_', $expl)[1];
						
            $rd = "<span title='".$hov."'>".$dat."</span>";
            $str = str_ireplace("^tx_".$expl.";", $rd, $str);      
        }
		//^pr_sborinfo-tv_termometr-okno;
		while(IsStr($str, "^pr_")){
            $expl = substr($str, stripos($str, "^pr_")+4, (stripos($str, ";", stripos($str, "^pr_")) - (stripos($str, "^pr_")+4)));     
            $modD = str_ireplace("-", "_", explode('_', $expl)[0]);
            $modT = str_ireplace("-", "_", explode('_', $expl)[1]);
						
            $str = str_ireplace("^pr_".$expl.";", _GenerateHTMLPrognoz($modD, $modT), $str);      
        }
		return $str;
	}
	
	public function ConstantON($groupe, $cmd, $desc, $action, $maxargument = -1){
		$this->constantAction[] = [$groupe, $cmd, $desc, $action, $maxargument];
	}
	
    public function GetConstant($strl){
        $str = $strl;		
		if(!ValidateConst($str)){
			$this->AddLog("localhost", "Incorrect constant", "0", "Incorrecting constant on: " . $str, date("Y-m-d H:i:s"));
			return "Ошибка преобразования констант";
		}
		
		//Начало рассвета
		//^ds;
		while(IsStr($str, "^ds")){
			$sun_info = date_sun_info(time(), floatval($this->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value), floatval($this->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value));
            $str = str_ireplace("^ds;", date("H:i", $sun_info['sunrise']), $str);      
        }
		//Начало заката
		//^de;
		while(IsStr($str, "^de")){
			$sun_info = date_sun_info(time(), floatval($this->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value), floatval($this->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value));
            $str = str_ireplace("^de;", date("H:i", $sun_info['sunset']), $str);      
        }
		//Длинна дня
		//^dd;
		while(IsStr($str, "^dd")){
			$sun_info = date_sun_info(time(), floatval($this->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value), floatval($this->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value));
            $str = str_ireplace("^dd;", date("H:i", $sun_info['sunrise'] - $sun_info['sunset']), $str);      
        }	
		//Чтение данных с модуля
        //^rm_192.168.1.11_Temp;
        while(IsStr($str, "^rm_")){
            $expl = substr($str, stripos($str, "^rm_")+4, (stripos($str, ";", stripos($str, "^rm_")) - (stripos($str, "^rm_")+4))); 
            $rd = $this->GetPosledData(str_ireplace("-", "_", explode('_', $expl)[0]))->GetFromName(explode('_', $expl)[1]);
            $rd = str_ireplace("_","-", $rd);
            $str = str_ireplace("^rm_".$expl.";", $rd, $str);      
        }
        while(IsStr($str, "^rmavg_")){
            $expl = substr($str, stripos($str, "^rmavg_")+7, (stripos($str, ";", stripos($str, "^rmavg_")) - (stripos($str, "^rmavg_")+7)));  		
            $rd = $this->GetFromEndData(str_ireplace("-", "_", explode('_', $expl)[0]), explode('_', $expl)[2])->BDDatas();
            $func = function($value) use ($expl): string {
                $t = $value->GetFromName(explode('_', $expl)[1]);
                return str_ireplace("_","-", $t);
            };
            $rd = array_map($func, $rd);
            $rd = round(array_sum($rd) / count($rd), 2);
            $str = str_ireplace("^rmavg_".$expl.";", $rd, $str);      
        }
		//Амлитуда данных за 5 измерений (минут, часов, секунд, дней с приставкой)
		//^rmamp_192.168.1.11_Temp_5;
        while(IsStr($str, "^rmamp_")){
            $expl = substr($str, stripos($str, "^rmamp_")+7, (stripos($str, ";", stripos($str, "^rmamp_")) - (stripos($str, "^rmamp_")+7)));     
            $rd = $this->GetFromEndData(str_ireplace("-", "_", explode('_', $expl)[0]), explode('_', $expl)[2])->SelectFloat(explode('_', $expl)[1]);
			//$max = max($rd);
			//$min = min($rd);
			list($m1, $m2) = array_chunk($rd, ceil(count($rd)/2));
            //$rd = round($rd[count($rd) - 1] - $rd[0], 2);
            $rd = round(min($m2) - max($m1), 2);
            $str = str_ireplace("^rmamp_".$expl.";", $rd, $str);      
        }
		//Отправить запрос на модуля
		//^send_192.168.1.14_meteo;
		while(IsStr($str, "^send_")){
            $expl = substr($str, stripos($str, "^send_")+6, (stripos($str, ";", stripos($str, "^send_")) - (stripos($str, "^send_")+6)));     
            $ip = explode('_', $expl)[0];
            $req = explode('_', $expl)[1];
            $rd = $this->SendCmd($ip, $req);
            $str = str_ireplace("^send_".$expl.";", $rd, $str);      
        }
		//Преобразовать SQL время в unixtime
		//^unix_2023-05-12 11:01:12;
		/*while(IsStr($str, "^unix_")){
            $expl = substr($str, stripos($str, "^unix")+6, (stripos($str, ";", stripos($str, "^unix")) - (stripos($str, "^unix")+6)));     
            $time = explode('_', $expl)[0];
            $rd = strtotime($time);
            $str = str_ireplace("^unix_".$expl.";", $rd, $str);      
        }*/	
		
		$at = 0;
		$tmparr = $this->constantAction;
		$nstr = array_walk($tmparr, function (&$value, $key) {
			$value = "^".$value[1];
		});
		while(IsStr($str, $tmparr)){
			foreach($this->constantAction as $tmp)
			{			
				$fullName = "^".$tmp[1].($tmp[4] == 0 ? ";" : "_");
				$countName = strlen($fullName);
				
				while(IsStr($str, $fullName)){
					$startargs = stripos($str, $fullName)+$countName;
					$lengthargs = stripos($str, ";", stripos($str, $fullName));
					if($startargs > $lengthargs)
						$expl = "";
					else
						$expl = substr($str, $startargs, ($lengthargs - $startargs));

					if(IsStr($expl, "^"))
						break;
					
					$rd = $tmp[3](explode('_', $expl));
					if($tmp[4] == 0)
						$str = str_ireplace($fullName, $rd, $str);     
					else
						$str = str_ireplace($fullName.$expl.";", $rd, $str);      
				}
			}
			if($at++ > 3) {$this->AddLog("localhost", "Incorrect constant", "0", "Incorrecting constant on: " . $str, date("Y-m-d H:i:s")); break;}
		}
		
        return $str;
    }
    public function GetButton($strl, $m, $p, $c){
        $str = $strl;
        //^bt_192.168.1.34_1_1_1;
        while(IsStr($str, "^bt")){
            $expl = substr($str, stripos($str, "^bt")+4, (stripos($str, ";", stripos($str, "^bt")) - (stripos($str, "^bt")+4)));
            
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
            
            $str = str_ireplace("^bt_".$expl.";", $rd, $str);      
        }
        return $str;
    }
    public function GetIFs($strl, $enb, $id = 0){
        $Name = $strl;
        $enable = $enb;
        if($enb == '0') return [$Name, $enable];
        while(IsStr($Name, "^if_")){
            $expl = substr($Name, stripos($Name, "^if_")+4, (stripos($Name, ";", stripos($Name, "^if_")) - (stripos($Name, "^if_")+4))); 
            if(IsStr($expl, "<")){  if(doubleval(preg_replace("/[^-0-9\.]/","",explode('<', $expl)[0])) > doubleval(preg_replace("/[^-0-9\.]/","",explode('<', $expl)[1]))){ $enable = "0"; echo "1>2 ";}}
            elseif(IsStr($expl, ">")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('>', $expl)[0])) < doubleval(preg_replace("/[^-0-9\.]/","",explode('>', $expl)[1]))){ $enable = "0"; echo "1<2 ";}}
            elseif(IsStr($expl, "<=")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('<=', $expl)[0])) >= doubleval(preg_replace("/[^-0-9\.]/","",explode('<=', $expl)[1]))){ $enable = "0"; echo "1>=2 ";}}
            elseif(IsStr($expl, ">=")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('>=', $expl)[0])) <= doubleval(preg_replace("/[^-0-9\.]/","",explode('>=', $expl)[1]))){ $enable = "0"; echo "1<=2 ";}}
            elseif(IsStr($expl, "==")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('==', $expl)[0])) != doubleval(preg_replace("/[^-0-9\.]/","",explode('==', $expl)[1]))){ $enable = "0"; echo "1!=2 ";}}
            elseif(IsStr($expl, "!=")){ if(doubleval(preg_replace("/[^-0-9\.]/","",explode('!=', $expl)[0])) == doubleval(preg_replace("/[^-0-9\.]/","",explode('!=', $expl)[1]))){ $enable = "0"; echo "1==2 ";}}
            $Name = str_ireplace("^if_".$expl.";", "", $Name);
        }
		while(IsStr($Name, "^en_")){
            $expl = substr($Name, stripos($Name, "^en_")+4, (stripos($Name, ";", stripos($Name, "^en_")) - (stripos($Name, "^en")+4))); 			
			$results = mysqli_query($this->link, "SELECT `Enable` FROM `scenes` WHERE `ID` = \"".$expl."\"");//Жестко качаем все из БД
				while($row = $results->fetch_assoc()) {
					if($row['Enable'] == '0') $enable = '0';
				}
            $Name = str_ireplace("^en_".$expl.";", "", $Name);
        }
		while(IsStr($Name, "^cs_")){
            $expl = substr($Name, stripos($Name, "^cs_")+4, (stripos($Name, ";", stripos($Name, "^cs_")) - (stripos($Name, "^cs")+4))); 			
			$results = mysqli_query($this->link, "SELECT `CSE` FROM `scenes` WHERE `ID` = \"".$expl."\"");//Жестко качаем все из БД
				while($row = $results->fetch_assoc()) {
					if($row['CSE'] == '00:00:00') $enable = '0';
				}
            $Name = str_ireplace("^cs_".$expl.";", "", $Name);
        }
        while(IsStr($Name, "^ons;")){
            $results = mysqli_query($this->link, "SELECT `CSE` FROM `scenes` WHERE `ID` = \"".intval($id)."\"");//Жестко качаем все из БД
				while($row = $results->fetch_assoc()) {
					if($row['CSE'] != '00:00:00') $enable = '0';
				}
            $Name = str_replace("^ons;", "", $Name);
        }
        return [$Name, $enable];
    }
    public function GetNotification($strl){
        $str = $strl;
        //^sn_all_Привет, мир;
        while(IsStr($str, "^sn")){
            $expl = substr($str, stripos($str, "^sn")+4, (stripos($str, ";") - (stripos($str, "^sn")+4)));     
            $text = explode('_', $expl)[1];
            $group = explode('_', $expl)[0];
			$text = str_replace("--", "_", $text);
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
	if(is_array($search)){
		foreach($search as $tmp){
			if(IsStr($str, $tmp))
				return true;
		}
		return false;
	}
	else{
		if(stripos($str, $search) !== FALSE) return true;
		else return false;
	}
}
function is_valid_ip($ip) {
    $ipv4 = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
    $ipv6 = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';
    return preg_match("/^($ipv4|$ipv6)\$/", trim($ip));
}
function br2nl($str) {
	$str = preg_replace("/(\r\n|\n|\r)/", "", $str);
	return preg_replace("=&lt;br */?&gt;=i", "\n", $str);
}
function roundToHalf($x)
{
    return ceil($x/0.5)*0.5;
}
function medianeFilter($a, $b, $c){
	//$middle;
	if (($a <= $b) && ($a <= $c)) $middle = ($b <= $c) ? $b : $c;
	else {
		if (($b <= $a) && ($b <= $c)) $middle = ($a <= $c) ? $a : $c;
		else $middle = ($a <= $b) ? $a : $b;
	}
	return $middle;
}
function expRunningAverage($prVal, $newVal, $k = 0.1) {
	$filVal = $prVal;
	$filVal = $filVal + ($newVal - $filVal) * $k;
	return $filVal;
}
function ValidateConst($str){
	//Для валиации количество ^ должно равняться ;
	if(IsStr($str, '^'))
		if(substr_count($str, '^') != substr_count($str, ';'))
			return false;
	
	return true;
}
function even($var)
{
    // является ли переданное число четным
    return !($var & 1);
}
function AutoNewLine($text, $fontsize, $width){
	$onesym = $fontsize / 2.2;
	$countsym = strlen($text);
	if($countsym * $onesym <= $width)
		return $text;
	
	$sl = explode(' ', $text);
	
	$t = "";
	$lastl = '';
	foreach($sl as $tmp){
		if(strlen($lastl.$tmp) * $onesym <= $width){
			$t = $t.$tmp.' ';
			$lastl = $lastl.$tmp.' ';
		}
		else{
			$t = $t."\n".$tmp.' ';
			$lastl = '';
		}
	}
	
	return $t;
}
?>
