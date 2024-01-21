<?
$PowerModuleModule = new MichomeModule("PowerModule", MichomeModuleType::ModuleCore);
$PowerModuleModule->BaseClass = new MichomeModuleCore("PowerModules", "Модуль электромонитора", "update=600000;minvoltage=4;fastvoltage=5", [], [["{modir}/powermodules.php?module={id}", "Конфигурация модуля электромонитора"]], __FILE__);

$PowerModuleModule->BaseClass->InstallFunction = function($modClass) {
    
};

$PowerModuleModule->BaseClass->SettingsFunction = function($modClass) {
    echo "<p>Данный тип модуля имеет следующие значение в БД: Voltage[i], Current[i], Power[i] - Напряжение, Ток, Мощность</p>";
};

$PowerModuleModule->BaseClass->POSTFunction = function($arrayData) {
    $modClass = $arrayData[0];
    $moduleData = $arrayData[1];
	
	$data = "";
	$power = $moduleData->{'data'}->{'power'}; //Power
	for($i = 0; $i < count($power); $i++){		
		if(floatval($power[$i][0]) > 1024 || floatval($power[$i][1]) > 1024) continue;	
		$data = $data . "Voltage".(count($power) > 1 ? $i : "")."=".$power[$i][0].(isset($power[$i][1]) ? (";Current".(count($power) > 1 ? $i : "")."=".$power[$i][1]) : "").(isset($power[$i][2]) ? (";Power".(count($power) > 1 ? $i : "")."=".$power[$i][2]) : "").";";
		if(isset($power[$i][3]) && $power[$i][3] == "VoltageMinAlarm"){
			$this->SendNotification("Внимание! Проблемы с напряжением на канале ".$i.", модуля ".$moduleData->{'type'}.". Напряжение упало до ".$power[$i][0]." вольт", "all");
		}
		elseif(isset($power[$i][3]) && $power[$i][3] == "VoltageFastAlarm"){
			$this->SendNotification("Внимание! Проблемы с напряжением на канале ".$i.", модуля ".$moduleData->{'type'}.". Резкое изменение напряжения до ".$power[$i][0]." вольт", "all");
		}
	}
	if($data == ""){
		return false;		
	}
	
	$tmp = new POSTData($data);
	return $tmp;
};

$MODs["PowerModule"] = $PowerModuleModule;
?>