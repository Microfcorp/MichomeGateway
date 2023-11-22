<?
function _GetAllSetting($link){
	$results = mysqli_query($link, "SELECT * FROM settings WHERE 1");
	$rt = [];
	while($row = $results->fetch_assoc()) {
        if(isset($row['ID']) && $row['ID'] != "")
            $rt[] = new SettingData($row['ID'], $row['Name'], $row['Value'], $row['Descreption'], $row['LastModify'], $link);
    }
	return $rt;
}

function _GetSetting($link, $Name){
	$results = mysqli_query($link, "SELECT * FROM settings WHERE `Name` = '$Name'");
	while($row = $results->fetch_assoc()) {
        if(isset($row['ID']) && $row['ID'] != "")
            return new SettingData($row['ID'], $row['Name'], $row['Value'], $row['Descreption'], $row['LastModify'], $link);
    }
	return false;
}

function _GetSettingByID($link, $ID){
	$results = mysqli_query($link, "SELECT * FROM settings WHERE `ID` = '$ID'");
	while($row = $results->fetch_assoc()) {
        if(isset($row['ID']) && $row['ID'] != "")
            return new SettingData($row['ID'], $row['Name'], $row['Value'], $row['Descreption'], $row['LastModify'], $link);
    }
	return false;
}

function _GetSettingORCreate($link, $Name, $ValueDefault, $Desc){
	$st = _GetSetting($link, $Name);
	if($st == false){
		$st = new SettingData('0', $Name, $ValueDefault, $Desc, '', $link);
		$st->Create($ValueDefault, $Desc);
		return $st;
	}
	else
		return $st;
}

class SettingData
{
    public $ID;
    public $Name;
    public $Value;
    public $Desc;
    public $LastModify;

    public $IsNull;
    
    public $link;
    
    public function __construct($ID, $Name, $Value, $Desc, $LastModify, $link) {
       $this->ID = $ID;
       $this->Name = $Name;
       $this->Value = $Value;
       $this->Desc = $Desc;
       $this->LastModify = $LastModify;
       $this->link = $link;
       $this->IsNull = ($ID == '0' || $Name == "");
    }
	
	public function Create($Value, $Desc){
		if(!$this->IsNull) return false;
		$this->Value = $Value;
        $this->Desc = $Desc;
		$nm = $this->Name;
	    mysqli_query($this->link, "INSERT INTO `settings` (`Name`, `Value`, `Descreption`) VALUES ('$nm', '$Value', '$Desc');");
		return true;
	}
    
    public function GetValue(){
		return $this->Value;
    }
    
    public function SetValue($data){
		$lst = date("Y-m-d H:i:s");
        $results = mysqli_query($this->link, "UPDATE `settings` SET `Value`='$data', `LastModify`='$lst' WHERE `id`=".$this->ID."");
        return $results;
    }
}
?>