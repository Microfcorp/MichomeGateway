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
//Данные с конца по количеству
function _GetFromEndData($link, $ip, $count){
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT * FROM michom WHERE `ip` = '$ip' ORDER BY `id` DESC LIMIT ".$count);
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT * FROM michom WHERE 1 ORDER BY `id` DESC LIMIT ".$count);
	else
        $results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) ORDER BY michom.`id` DESC LIMIT ".$count);
    
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
function _GetDataForDay($link, $ip, $day){
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
    
	if(count($ret) > 0)
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
//Добавить лог
function _AddLog($link, $ip, $type, $rssi, $log, $date){
    $guery = "INSERT INTO `logging`(`ip`, `type`, `rssi`, `log`, `date`) VALUES ('$ip', '$type','$rssi','$log','$date')";
    $result = mysqli_query($link, $guery);
}

function _MaxMinTemper($link, $ip, $date = 1){
    if(is_valid_ip($ip))
        $results = mysqli_query($link, "SELECT MAX(`temp`), MIN(`temp`) FROM michom WHERE `ip` = '$ip' AND `date` >= '$date'");
    else if($ip == '1')
		$results = mysqli_query($link, "SELECT MAX(`temp`), MIN(`temp`) FROM michom WHERE `date` >= '$date'");
	else
        $results = mysqli_query($link, "SELECT MAX(`temp`), MIN(`temp`) FROM michom WHERE michom.ip = (SELECT t.ip FROM modules AS t WHERE t.mID = '$ip' ORDER BY t.id DESC LIMIT 1) AND `date` >= '$date'");

    while($row = $results->fetch_assoc()) {
        $data[] = $row['MAX(`temp`)'];
        $data[] = $row['MIN(`temp`)'];
    }
    return $data;
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
        else return "";
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
	public function Select($skey){
		$tmp = $this->BDDatas();
		array_walk($tmp, function (&$value, $key) use($skey) {
			$value = $value->GetFromName($skey);
		});
		return $tmp;
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
    
    public $link;
    
    public function __construct($ID, $IP, $Type, $RSSI, $Log, $Date, $link) {
       $this->ID = $ID;
       $this->IP = $IP;
       $this->Type = $Type;
       $this->RSSI = $RSSI;
       $this->Log = $Log;
       $this->Date = $Date;
       $this->link = $link;       
    }
	
	public function GetFromName($name){
		$name = mb_strtolower($name);
        if($name == "id") return $this->ID;
        elseif($name == "ip") return $this->IP;
        elseif($name == "type") return $this->Type;
        elseif($name == "rssi") return $this->RSSI;
        elseif($name == "log") return $this->Log;
        elseif($name == "date") return $this->Date;
        else return "";
    }
    
    public function Update($key, $data){
        $results = mysqli_query($this->$link, "UPDATE `logging` SET `".$key."`='$data' WHERE `id`=".$this->$ID."");
        return $results;
    }
}
?>