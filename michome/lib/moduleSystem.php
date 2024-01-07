<?php
class MichomeModule
{
	public $Name;
	public ?MichomeModuleType $Type;
	public $BaseClass;
	
	public function __construct($Name, $Type) {
       $this->Name = $Name;
       $this->Type = $Type;
    }
	
	public function GetAllSettings($API){
		return array_filter($API->GetAllSetting(), function($v, $k) {
								return IsStr($v->Name, $this->Name."_");
							}, ARRAY_FILTER_USE_BOTH);
	}
	
	public function GetSettingORCreate($API, $Names, $ValueDefault, $Desc){
		return $API->GetSettingORCreate($this->Name . "_" . $Names, $ValueDefault, $Desc);
	}
	
	public function IsInstalled(){
		return file_exists(__DIR__."/".$this->Name . "_installed");
	}
	
	public function Install($API){
		if($this->IsInstalled()) return false;
		$this->BaseClass->InstallFunction->call($API, $this);
		file_put_contents(__DIR__."/".$this->Name . "_installed", date("Y-m-d H:i:s"));
	}
	
	public function AddParam($API, $ParamType){
		$results = mysqli_query($API->link, "INSERT INTO `mods` (`ModName`, `ParamType`) VALUES ('".$this->Name."', '$ParamType');");
		return $results;
	}
	public function RemoveParam($API, $id){
		$results = mysqli_query($API->link, "DELETE FROM `mods` WHERE `ID`='".$id."'");
		return $results;
	}
	
	public function GetParamByID($API, $id){
		$results = mysqli_query($API->link, "SELECT * FROM mods WHERE `ID` = '".$id."'");
		while($row = $results->fetch_assoc()) {
			if(isset($row['ID']) && $row['ID'] != "")
				return new ModsParam($row['ID'], $row['ModName'], $row['ParamType'], $row['ParamName'], $row['ParamValue'], $API->link);
		}
		return NULL;
	}
	
	public function GetParams($API){
		$pars = array();
		$results = mysqli_query($API->link, "SELECT * FROM mods WHERE `ModName` = '".$this->Name."'");
		while($row = $results->fetch_assoc()) {
			if(isset($row['ID']) && $row['ID'] != "")
				$pars[] = new ModsParam($row['ID'], $row['ModName'], $row['ParamType'], $row['ParamName'], $row['ParamValue'], $API->link);
		}
		return $pars;
	}
	
	public function GetParamsType($API, $type){
		$pars = array();
		$results = mysqli_query($API->link, "SELECT * FROM mods WHERE `ModName` = '".$this->Name."' AND `ParamType` = '$type'");
		while($row = $results->fetch_assoc()) {
			if(isset($row['ID']) && $row['ID'] != "")
				$pars[] = new ModsParam($row['ID'], $row['ModName'], $row['ParamType'], $row['ParamName'], $row['ParamValue'], $API->link);
		}
		return $pars;
	}
}

class MichomeModuleCore
{
	public $TypeModule;
	public $DescModule;
	public $DefaultSettings;
	public $CommandsModule;
	public $MichomePage;
	public $PathModule;
	
	public $InstallFunction; //function($mod) //Выполняется при установке (Расширение - 1 раз)
	public $SettingsFunction; //function($mod) //Выполняется каждый раз на странице настроек, this - микхом
	public $InitialFunction; //function($mod) //Выполняется каждый раз при подключении библиотеки микхома
	
	public $CronFunction; //function($mod) //Выполняется каждую минуту
	public $CronFunction5min; //function($mod) //Выполняется каждые 5 минут
	public $CronFunction10min; //function($mod) //Выполняется каждые 10 минут
	
	public function __construct($TypeModule, $DescModule, $DefaultSettings, $Commands, $MichomePage, $PathModule) {
       $this->TypeModule = $TypeModule;
       $this->DescModule = $DescModule;
       $this->DefaultSettings = $DefaultSettings;
       $this->CommandsModule = $Commands;
       $this->MichomePage = $MichomePage;
	   $this->PathModule = str_replace($_SERVER['DOCUMENT_ROOT'], '', $PathModule);
    }
	
	public function GetMichomePage(){
		$tmp = array();
		foreach($this->MichomePage as $tmp1){
			$tmp[] = [parseMichomURI($tmp1[0], $this), parseMichomURI($tmp1[1], $this)];
		}
		return $tmp;
	}
}

class ModsParam
{
    public $ID;
    public $ModName;
    public $ParamType;
    public $ParamName;
    public $ParamValue;

    public $IsNull;
    
    public $link;
    
    public function __construct($ID, $ModName, $ParamType, $ParamName, $ParamValue, $link) {
       $this->ID = $ID;
       $this->ModName = $ModName;
       $this->ParamType = $ParamType;
       $this->ParamName = $ParamName;
       $this->ParamValue = $ParamValue;
       $this->link = $link;
       $this->IsNull = ($ID == '0' || $ModName == "");
    }
    
    public function GetValue(){
		return $this->Value;
    }
    
	public function SetValue($name, $data){
        $results = mysqli_query($this->link, "UPDATE `mods` SET `ParamName`='$name', `ParamValue`='$data' WHERE `ID`='".$this->ID."'");
        return $results;
    }
}

enum MichomeModuleType
{
    case ModuleExtension;
    case ModuleCore;
    case Extension;
    case Web;
	
	public function web(): string {
		return match($this) {
		  MichomeModuleType::ModuleExtension => 'Поддержка модуля',
		  MichomeModuleType::ModuleCore => 'Добавление команд модуля',
		  MichomeModuleType::Extension => 'Расширение Michome',
		  MichomeModuleType::Web => 'Веб-интерфейс',
		};
	}
}

function parseMichomURI($uri, $baseclass){
	$uri = str_replace("{modir}", $baseclass->PathModule, $uri);
	$uri = str_replace("/mod.php", "", $uri);
	return $uri;
}

foreach (glob(ServerPath."/modules/*/mod.php") as $modpath) {
	require_once($modpath);
}
?>