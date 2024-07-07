<?php
//Последние данные
function _GetPosledData($link, $ip){
    //Сначала получаем из основной базы
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM michom WHERE `ip` = '$ip' ORDER BY `id` DESC LIMIT 1");
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM michom WHERE 1 ORDER BY `id` DESC LIMIT 1");
	else
        $results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) ORDER BY michom.`id` DESC LIMIT 1");
    
    while($row = $results->fetch_assoc()) {
        if($row['id'] != "" && $row['ip'] != "")
            return new BDData($row['id'], $row['ip'], $row['type'], $row['data'], $row['temp'], $row['humm'], $row['dawlen'], $row['visota'], $row['date'], $link);
    }
    
    //Потом получает из логиновой базы
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM logging WHERE `ip` = '$ip' ORDER BY `id` DESC LIMIT 1");
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM logging WHERE 1 ORDER BY `id` DESC LIMIT 1");
	else
        $results = mysqli_query($link, "SELECT * FROM logging WHERE logging.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) ORDER BY logging.`id` DESC LIMIT 1");
    
    while($row = $results->fetch_assoc()) {
        if($row['id'] != "")
            return new BDLogData($row['id'], $row['ip'], $row['type'], $row['rssi'], $row['log'], $row['date'], $link);
    }
    
    return new BDLogData('0','','','','','', $link);
}
//Данные с конца по количеству или минутам с приставкой m
function _GetFromEndData($link, $ip, $count){
	if(is_numeric($count)){ //Если по количеству измерений
		$sortBD = "ORDER BY `id` DESC LIMIT ".intval($count);
	}
	else{ //Если по времени
		if(substr($count, -1) == "m")
			$p = "MINUTE";
		elseif(substr($count, -1) == "h")
			$p = "HOUR";
		elseif(substr($count, -1) == "d")
			$p = "DAY";
		elseif(substr($count, -1) == "s")
			$p = "SECOND";
		elseif(substr($count, -1) == "c")
			$p = "MONTH";
		elseif(substr($count, -1) == "i"){
			$p = "HOUR";
			$count = date("i");
		}
		elseif(substr($count, -1) == "v"){
			$p = "DAY";
			$count = date("d");
		}
		else
			$p = "MINUTE";
		
		$sortBD = "AND `date` >= DATE_SUB(NOW(), INTERVAL ".intval($count)." ".$p.")";
	}
	
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM michom WHERE `ip` = '$ip' ".$sortBD);
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM michom WHERE 1 ".$sortBD);
	else
        $results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) ".$sortBD);
    
    $ret =[];
    
    while($row = $results->fetch_assoc()) {
        if($row['id'] != "")
            $ret[] = new BDData($row['id'], $row['ip'], $row['type'], $row['data'], $row['temp'], $row['humm'], $row['dawlen'], $row['visota'], $row['date'], $link);
    }
    
	if(count($ret) > 0)
		return new BDDataCollection($ret);
    
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM logging WHERE `ip` = '$ip' ORDER BY `id` DESC LIMIT ".$count);
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM logging WHERE 1 ORDER BY `id` DESC LIMIT ".$count);
	else
        $results = mysqli_query($link, "SELECT * FROM logging WHERE logging.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) AND ORDER BY logging.`id` DESC LIMIT ".$count);
    
    while($row = $results->fetch_assoc()) {
        if($row['id'] != "")
            $ret[] = new BDLogData($row['id'], $row['ip'], $row['type'], $row['rssi'], $row['log'], $row['date'], $link);
    }
    
    return new BDDataCollection($ret);
}
//Данные за определенный день
function _GetDataForDay($link, $ip, $day, $IsLog){
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM michom WHERE `ip` = '$ip' AND `date` >= '$day' AND `date` < ADDDATE('$day', 1)");
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM michom WHERE `date` >= '$day' AND `date` < ADDDATE('$day', 1)");
	else
        $results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) AND `date` >= '$day' AND `Date` < ADDDATE('$day', 1)");
    
    $ret =[];
    
    while($row = $results->fetch_assoc()) {
        if($row['id'] != "")
            $ret[] = new BDData($row['id'], $row['ip'], $row['type'], $row['data'], $row['temp'], $row['humm'], $row['dawlen'], $row['visota'], $row['date'], $link);
    }
    
	if(count($ret) > 0 || !$IsLog)
		return new BDDataCollection($ret);
    
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM logging WHERE `ip` = '$ip' AND `date` >= '$day' AND `Date` < ADDDATE('$day', 1)");
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM logging WHERE `date` >= '$day' AND `Date` < ADDDATE('$day', 1)");
	else
        $results = mysqli_query($link, "SELECT * FROM logging WHERE logging.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) AND `date` >= '$day' AND `date` < ADDDATE('$day', 1) ");
    
    while($row = $results->fetch_assoc()) {
        if($row['id'] != "")
            $ret[] = new BDLogData($row['id'], $row['ip'], $row['type'], $row['rssi'], $row['log'], $row['date'], $link);
    }
    
    return new BDDataCollection($ret);
}
//Данные с начала id по конец id
function _GetDataRange($link, $ip, $startID, $endID, $IsLog = false){
	$ret =[];
	if(!$IsLog){
		if(is_valid_ip($ip))
			$results = mysqli_query($link, "SELECT * FROM michom WHERE `ip` = '$ip' AND `id` >= '$startID' AND `id` <= '$endID'");
		else if($ip == '1')
			$results = mysqli_query($link, "SELECT * FROM michom WHERE `id` >= '$startID' AND `id` <= '$endID'");
		else
			$results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) AND `id` >= '$startID' AND `id` <= '$endID'");
			
		while($row = $results->fetch_assoc()) {
			if($row['id'] != "")
				$ret[] = new BDData($row['id'], $row['ip'], $row['type'], $row['data'], $row['temp'], $row['humm'], $row['dawlen'], $row['visota'], $row['date'], $link);
		}
		
		return new BDDataCollection($ret);
	}
    else{
		if(is_valid_ip($ip))
			$results = mysqli_query($link, "SELECT * FROM logging WHERE `ip` = '$ip' AND `id` >= '$startID' AND `id` <= '$endID'");
		else if($ip == '1')
			$results = mysqli_query($link, "SELECT * FROM logging WHERE `id` >= '$startID' AND `id` <= '$endID'");
		else
			$results = mysqli_query($link, "SELECT * FROM logging WHERE logging.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) AND `id` >= '$startID' AND `id` <= '$endID' ");
		
		while($row = $results->fetch_assoc()) {
			if($row['id'] != "")
				$ret[] = new BDLogData($row['id'], $row['ip'], $row['type'], $row['rssi'], $row['log'], $row['date'], $link);
		}
		
		return new BDDataCollection($ret);
	}
}
//Добавить лог
function _AddLog($link, $ip, $type, $rssi, $log, $date){
    $guery = "INSERT INTO `logging`(`ip`, `type`, `rssi`, `log`, `date`) VALUES ('$ip', '$type','$rssi','$log','$date')";
    $result = mysqli_query($link, $guery);
}

function _MaxMinValue($link, $ip, $datetype, $date = 1, $enddate = 1){
	if($enddate == 1){
		$enddate = date("Y-m-d H:i:s");
	}
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM michom WHERE `ip` = '$ip' AND `date` >= '$date' AND `date` <= '$enddate'");
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM michom WHERE `date` >= '$date' AND `date` <= '$enddate'");
	else
        $results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) AND `date` >= '$date' AND `date` <= '$enddate'");

	$ret = [];
    while($row = $results->fetch_assoc()) {
        $ret[] = new BDData($row['id'], $row['ip'], $row['type'], $row['data'], $row['temp'], $row['humm'], $row['dawlen'], $row['visota'], $row['date'], $link);		
    }
	
	$values = (new BDDataCollection($ret))->SelectFloat($datetype);
	
    return [max($values), min($values)];
}

class BDData
{
    public $ID;
    public $IP;
    public $Type;
    public $Data;
    public $Temp;
    public $Humm;
    public $Dawlen;
    public $Visota;
    public $Date;
    public $IsNull;
    
    public $link;
    
    public function __construct($ID, $IP, $Type, $Data, $Temp, $Humm, $Dawlen, $Visota, $Date, $link) {
       $this->ID = $ID;
       $this->IP = $IP;
       $this->Type = $Type;
       $this->Data = $Data;
       $this->Temp = $Temp;
       $this->Humm = $Humm;
       $this->Dawlen = $Dawlen;
       $this->Visota = $Visota;
       $this->Date = $Date;
       $this->link = $link;
       $this->IsNull = ($ID == '0' && $IP == "");
    }
    
    public function GetFromName($name){
		$name = mb_strtolower($name);
        if($name == "id") return $this->ID;
        elseif($name == "ip") return $this->IP;
        elseif($name == "type") return $this->Type;
        elseif($name == "data") return $this->Data;
        elseif($name == "temp" || $name == "temper") return $this->Temp;
        elseif($name == "humm") return $this->Humm;
        elseif($name == "dawlen" || $name == "press") return $this->Dawlen;
        elseif($name == "visota" || $name == "alt") return $this->Visota;
        elseif($name == "date") return $this->Date;
        else{
			$arr = explode(";", $this->Data);
			foreach($arr as $tmp){
				if($tmp == "") continue;
				$na = explode("=", $tmp)[0];
				$va = explode("=", $tmp)[1];
				if(mb_strtolower($na) == $name){
					return $va;
				}
			}
			return null;
		}
    }
    
    public function Update($key, $data){
        $results = mysqli_query($this->$link, "UPDATE `michom` SET `".$key."`='$data' WHERE `id`=".$this->ID."");
        return $results;
    }
}

class BDDataCollection
{
	public $BDData;
	public function BDDatas(){
		return $this->BDData;
	}
	public function __construct($BDData) {
       $this->BDData = $BDData;       
    }
	public function SortType($type){
		return new BDDataCollection(array_values(array_filter($this->BDData, function($v, $k) use($type) : string {
			return $v->Type == $type || $type == '1';
		}, ARRAY_FILTER_USE_BOTH)));
	}
	public function SortByID(){
		usort($this->BDData, function($a, $b) {
			return $a->ID - $b->ID;
		});
		return $this;
	}
	public function Select($skey){
		$tmp = $this->BDDatas();
		array_walk($tmp, function (&$value, $key) use($skey) {
			$value = $value->GetFromName($skey);
		});
		return $tmp;
	}
	public function SelectFloat($skey){
		$tmp = $this->BDDatas();
		array_walk($tmp, function (&$value, $key) use($skey) {
			$v1 = $value->GetFromName($skey);
			$value = floatval($v1);
		});
		return $tmp;
	}
	public function SelectInt($skey){
		$tmp = $this->BDDatas();
		array_walk($tmp, function (&$value, $key) use($skey) {
			$value = intval($value->GetFromName($skey));
		});
		return $tmp;
	}
	public function SelectUnixTime(){
		$tmp = $this->BDDatas();
		array_walk($tmp, function (&$value, $key) {
			$value = strtotime($value->Date);
		});
		return $tmp;
	}
	public function GetTypes($filter = "none"){
		$rt = ['id', 'ip', 'type', 'data', 'temp', 'humm', 'dawlen', 'visota', 'date'];
		foreach($this->Select('data') as $tmp){
			$arr = explode(";", $tmp);
			$et = 0;
			foreach($arr as $tmp1){
				if($tmp1 == "") continue;				
				$na = explode("=", $tmp1)[0];
				$va = explode("=", $tmp1)[1];
				$para = mb_strtolower(preg_replace('/[^a-zA-Zа-яА-Я]/ui', '',$na));
				if(!in_array(mb_strtolower($na), $rt) && (isset($this->SelectFloat($para)[$et]) && $this->SelectFloat($para)[$et] != floatval($va)))
					$rt[] = mb_strtolower($na);
				$et = $et + 1;
			}		
		}
		if($filter != "none"){
			foreach($rt as $tmp){
				$lineData = $this->Select($tmp);
				if($filter == "nonenull" && count(array_filter($lineData)) < 1)
					unset($rt[array_search($tmp, $rt)]);
			}
		}
		sort($rt);
		return $rt;
	}
	public function First(){
		return $this->BDData[0];
	}
	public function Last(){
		return $this->BDData[count($this->BDData) - 1];
	}
	public function CountBD(){
		return count($this->BDData);
	}
	public function IsNull(){
		return $this->CountBD() == 0;
	}
}

class BDLogData
{
    public $ID;
    public $IP;
    public $Type;
    public $RSSI;
    public $Log;
    public $Date;
	public $IsNull;
    
    public $link;
    
    public function __construct($ID, $IP, $Type, $RSSI, $Log, $Date, $link) {
       $this->ID = $ID;
       $this->IP = $IP;
       $this->Type = $Type;
       $this->RSSI = $RSSI;
       $this->Log = $Log;
       $this->Date = $Date;
       $this->link = $link;       
	   $this->IsNull = ($ID == '0' && $IP == "");
    }
	
	public function GetFromName($name){
		$name = mb_strtolower($name);
        if($name == "id") return $this->ID;
        elseif($name == "ip") return $this->IP;
        elseif($name == "type") return $this->Type;
        elseif($name == "rssi") return $this->RSSI;
        elseif($name == "log" || $name == "data") return $this->Log;
        elseif($name == "date") return $this->Date;
        else{
			$arr = explode(";", $this->Log);
			foreach($arr as $tmp){
				if($tmp == "") continue;
				$na = explode("=", $tmp)[0];
				$va = explode("=", $tmp)[1];
				if(mb_strtolower($na) == $name){
					return $va;
				}
			}
			return "";
		}
    }
    
    public function Update($key, $data){
        $results = mysqli_query($this->$link, "UPDATE `logging` SET `".$key."`='$data' WHERE `id`=".$this->$ID."");
        return $results;
    }
}
?>