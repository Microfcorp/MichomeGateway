<?
$UPSModuleModule = new MichomeModule("UPSModule", MichomeModuleType::ModuleCore);
$UPSModuleModule->BaseClass = new MichomeModuleCore("UPSModule", "Модуль NUT UPS", "update=600000;minvoltage=4;fastvoltage=5", [], [], __FILE__);

$UPSModuleModule->BaseClass->InstallFunction = function($modClass) {
    
};

$UPSModuleModule->BaseClass->InitialFunction = function($modClass) {
    
};

$UPSModuleModule->BaseClass->SettingsFunction = function($modClass) {
    echo "<p>Данный тип модуля имеет следующие значение в БД: Voltage[i], Current[i], Power[i] - Напряжение, Ток, Мощность</p>";
};

$UPSModuleModule->BaseClass->POSTFunction = function($arrayData) {
    $modClass = $arrayData[0];
    $moduleData = $arrayData[1];
	
	$data = "";
	$power = $moduleData->{'data'}->{'ups'}; //Power
	for($i = 0; $i < count($power); $i++){
		if(isset($power[$i]->{"ups_name"})){
			$charge = (isset($power[$i]->{"charge"}) ? $power[$i]->{"charge"} : NULL);
			$status = (isset($power[$i]->{"status"}) ? $power[$i]->{"status"} : NULL);
			$voltage = (isset($power[$i]->{"voltage"}) ? $power[$i]->{"voltage"} : NULL);
			$voltage_out = (isset($power[$i]->{"voltageOut"}) ? $power[$i]->{"voltageOut"} : NULL);
			$freq = (isset($power[$i]->{"freq"}) ? $power[$i]->{"freq"} : NULL);
			$load = (isset($power[$i]->{"load"}) ? $power[$i]->{"load"} : NULL);
			$temperature = (isset($power[$i]->{"temperature"}) ? $power[$i]->{"temperature"} : NULL);
			$bat_volt = (isset($power[$i]->{"bat_volt"}) ? $power[$i]->{"bat_volt"} : NULL);
			$nominalPower = (isset($power[$i]->{"nominalPower"}) ? $power[$i]->{"nominalPower"} : NULL);
			$nominalPowerVA = (isset($power[$i]->{"nominalPowerVA"}) ? $power[$i]->{"nominalPowerVA"} : NULL);
			$powerWatt = (isset($power[$i]->{"powerWatt"}) ? $power[$i]->{"powerWatt"} : NULL);
			$ampers = (isset($power[$i]->{"ampers"}) ? round($power[$i]->{"ampers"}, 2) : NULL);
			$batt_capacity = (isset($power[$i]->{"batt_capacity"}) ? $power[$i]->{"batt_capacity"} : NULL);
			$remaning_time = (isset($power[$i]->{"remaning_time"}) ? $power[$i]->{"remaning_time"} : NULL);
			
			$data = $data . "Charge".(count($power) > 1 ? $i : "")."=".$charge.
							(isset($status) ? (";Status".(count($power) > 1 ? $i : "")."=".$status) : "").
							(isset($voltage) ? (";Voltage".(count($power) > 1 ? $i : "")."=".$voltage) : "").
							(isset($voltage_out) ? (";VoltageOut".(count($power) > 1 ? $i : "")."=".$voltage_out) : "").
							(isset($freq) ? (";Freq".(count($power) > 1 ? $i : "")."=".$freq) : "").
							(isset($load) ? (";Load".(count($power) > 1 ? $i : "")."=".$load) : "").
							(isset($temperature) ? (";Temp".(count($power) > 1 ? $i : "")."=".$temperature) : "").
							(isset($bat_volt) ? (";Batt".(count($power) > 1 ? $i : "")."=".$bat_volt) : "").
							(isset($nominalPower) ? (";NP".(count($power) > 1 ? $i : "")."=".$nominalPower) : "").
							(isset($nominalPowerVA) ? (";NPVA".(count($power) > 1 ? $i : "")."=".$nominalPowerVA) : "").
							(isset($powerWatt) ? (";Power".(count($power) > 1 ? $i : "")."=".$powerWatt) : "").
							(isset($ampers) ? (";Amper".(count($power) > 1 ? $i : "")."=".$ampers) : "").
							(isset($batt_capacity) ? (";Cap".(count($power) > 1 ? $i : "")."=".$batt_capacity) : "").
							(isset($remaning_time) ? (";Rem".(count($power) > 1 ? $i : "")."=".$remaning_time) : "").
							";";
			
			/*if(isset($power[$i][3]) && $power[$i][3] == "VoltageMinAlarm"){
				$this->SendNotification("Внимание! Проблемы с напряжением на канале ".$i.", модуля ".$moduleData->{'type'}.". Напряжение упало до ".$power[$i][0]." вольт", "all");
			}
			elseif(isset($power[$i][3]) && $power[$i][3] == "VoltageFastAlarm"){
				$this->SendNotification("Внимание! Проблемы с напряжением на канале ".$i.", модуля ".$moduleData->{'type'}.". Резкое изменение напряжения до ".$power[$i][0]." вольт", "all");
			}*/
		}
	}
	if($data == ""){
		return false;		
	}
	
	$tmp = new POSTData($data);
	return $tmp;
};

$MODs["UPSModule"] = $UPSModuleModule;
?>