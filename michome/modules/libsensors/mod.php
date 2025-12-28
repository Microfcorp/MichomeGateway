<?

function getSensorsData(){
	$tmp = shell_exec('sensors -j');
	
	if(IsStr($tmp, "not found"))
		return NULL;
	return $tmp;
}

function getSensorsDiskTemp($disk){
	$temp = shell_exec("sudo hddtemp {$disk} 2>&1");
	if(IsStr($temp, "not found"))
		return NULL;
	return trim(explode(':', $temp)[2]);
}

function getSensorsAllDISKS(){
	$pizza = shell_exec("sh ".__DIR__."/devparse.sh 2>&1");
	return explode("\n", $pizza);
}

$libSensorsModule = new MichomeModule("LibSensors", MichomeModuleType::Extension);
$libSensorsModule->BaseClass = new MichomeModuleCore("LibSensors", "Модуль интеграции показаний sensors", "", [], [], __FILE__);
$libSensorsModule->BaseClass->SettingsFunction = function($modClass) {
	
	$sensorsData = getSensorsData();
	if($sensorsData == NULL)
		echo "<h3 style='color: red'>ОШИБКА! Приложение sensors не установлено<h3>";
	else{
		$sensorsData = str_replace('_', '-', $sensorsData);
		$sensorsData = json_decode($sensorsData, true);
		$values = array_values($sensorsData);
		
		echo "<p><h3>Доступные данные (libsensors): </h3>";
		for($i = 0; $i < count($values); $i++){
			$adapterName = $values[$i]['Adapter'];
			$portNames = array_keys($values[$i]);
			
			echo "<h4>Доступные устройства на адаптере {$adapterName}:</h4>";
			for($a = 0; $a < count($portNames); $a++){
				$portName = $portNames[$a];
				if($portName == "Adapter") continue;
				$valueNames = array_keys($values[$i][$portName]);
				
				echo "<h5>Доступные показания на входе <span style='color: red'>{$portName}:</span></h5>";
				for($b = 0; $b < count($valueNames); $b++){
					$valueName = $valueNames[$b];
					
					$valueValue = $values[$i][$portName][$valueName];
					
					echo "<span>^rsens_{$i}_{$portName}_{$valueName}; - <span style='color: lightgreen;'>Текущее значение: {$valueValue}</span></span><br />";
				}
			}
		}
		
		echo "</p> <br />";
	}
	
	echo "<p><h3>Доступные данные (hddtemp): </h3>";
	$allDisk = getSensorsAllDISKS();	
	for($i = 0; $i < count($allDisk); $i++){
		$tmp = $allDisk[$i];
		if($tmp != "/dev" || $tmp != ""){
		   $exp = explode(" ", $tmp);
		   $exp = array_diff($exp, array(''));
		   $exp = array_values($exp);
		   
		   if(count($exp) > 1){
			   
			   echo "<h4>Доступные параметры для диска {$exp[0]}:</h4>";
			   $diskName = str_replace("/dev/", "", $exp[0]);
			   $diskFullSize = $exp[1];
			   $diskUsedSize = $exp[2];
			   $diskFreeSize = $exp[3];
			   $diskPercentSize = $exp[4];
			   $diskMountPoint = $exp[5];
			   $diskTemp = getSensorsDiskTemp($exp[0]);
			   
			   if($diskTemp != null)
					echo "<span>^disktemp_{$diskName}; - <span style='color: lightgreen;'>Текущее значение: {$diskTemp}</span></span><br />";
		       echo "<span>^diskfs_{$diskName}; - <span style='color: lightgreen;'>Текущее значение: {$diskFullSize}</span></span><br />";
		       echo "<span>^diskus_{$diskName}; - <span style='color: lightgreen;'>Текущее значение: {$diskUsedSize}</span></span><br />";
		       echo "<span>^diskfree_{$diskName}; - <span style='color: lightgreen;'>Текущее значение: {$diskFreeSize}</span></span><br />";
		       echo "<span>^diskperc_{$diskName}; - <span style='color: lightgreen;'>Текущее значение: {$diskPercentSize}</span></span><br />";
		       echo "<span>^diskmount_{$diskName}; - <span style='color: lightgreen;'>Текущее значение: {$diskMountPoint}</span></span><br />";
		   }		  
		}
	}
	
	echo "</p>";
	
	/*echo "<p>Настройка камер:</p><table><tr><td><b>ID камеры</b></td><td><span title='Список параметров, разделенных | . Список параметров: IP камеры и порт, тип камеры (onvif, foscam), логин, пароль'><b>Значение параметра</b></span></td></tr>";
	foreach($modClass->GetParamsType($this, "ManageCamera") as $tmp){
		echo "<tr><td class='scenesT'><input id='name".$tmp->ID."' style='width:150px;height:20px;' type='text' value='".$tmp->ParamName."'></td><td class='scenesT'><input id='val".$tmp->ID."' style='width:500px;height:20px;' type='text' value='".$tmp->ParamValue."'></td><td class='scenesT'><input type='button' onclick='saveParam(".$tmp->ID.", name".$tmp->ID.".value, val".$tmp->ID.".value)' value='Сохранить'></td><td class='scenesT'><input onclick='removeParam(".$tmp->ID.")' type='button' value='Удалить'></td></tr>";
	}
	echo "<tr><td><input onclick='addParam(\"ManageCamera\")' type='button' value='Добавить камеру'></td></tr></table>";*/
};

$libSensorsModule->BaseClass->InstallFunction = function($modClass) {
    //$modClass->GetSettingORCreate($this, 'Name', 'Michome system', 'Название прибора в проекте');
    //$modClass->GetSettingORCreate($this, 'Owner', '', 'Владелец прибора в проекте');
};

$libSensorsModule->BaseClass->InitialFunction = function($modClass) {
    $this->ConstantON("LibSensors", "rsens", "^rsens_0_Core 0_temp2-input; - Возвращает значение датчика на адаптере {0}, устройстве {Core 0}, значение {temp2-input}", function($expl) use($modClass): string 
    {
		$adapter = intval($expl[0]);
		$device = str_replace('-', '_', $expl[1]);
		$valType = str_replace('-', '_', $expl[2]);
		
		$sensorsData = getSensorsData();
		if($sensorsData == NULL)
			return "";
	
	    //$sensorsData = str_replace('_', '-', $sensorsData);
		$sensorsData = json_decode($sensorsData, true);
		$values = array_values($sensorsData);
		
		$tmp = $values[$adapter][$device][$valType] ?? "-1";
		
		return $tmp;
    }, 3);
	
	$this->ConstantON("LibSensors", "disktemp", "^disktemp_sda1; - Возвращает температуру для диска {sda1}", function($expl) use($modClass): string 
    {
		$disk = $expl[0];
		return intval((getSensorsDiskTemp("/dev/{$disk}") ?? '-1'));
    }, 1);
	
	$this->ConstantON("LibSensors", "diskfs", "^diskfs_sda1; - Возвращает полный объем диска {sda1}", function($expl) use($modClass): string 
    {
		$disk = $expl[0];
		$allDisk = getSensorsAllDISKS();	
		for($i = 0; $i < count($allDisk); $i++){	
			$tmp = $allDisk[$i];
			if(IsStr($tmp, '/dev/'.$disk)){
			   $exp = explode(" ", $tmp);
			   $exp = array_diff($exp, array(''));
			   $exp = array_values($exp);		   
			   if(count($exp) > 1){
				   return intval($exp[1]);
			   }		  
			}
		}		
		return '-1';
    }, 1);
	
	$this->ConstantON("LibSensors", "diskus", "^diskus_sda1; - Возвращает использованный объем диска {sda1}", function($expl) use($modClass): string 
    {
		$disk = $expl[0];
		$allDisk = getSensorsAllDISKS();	
		for($i = 0; $i < count($allDisk); $i++){	
			$tmp = $allDisk[$i];
			if(IsStr($tmp, '/dev/'.$disk)){
			   $exp = explode(" ", $tmp);
			   $exp = array_diff($exp, array(''));
			   $exp = array_values($exp);		   
			   if(count($exp) > 1){
				   return intval($exp[2]);
			   }		  
			}
		}		
		return '-1';
    }, 1);
	
	$this->ConstantON("LibSensors", "diskfree", "^diskfree_sda1; - Возвращает свободный объем диска {sda1}", function($expl) use($modClass): string 
    {
		$disk = $expl[0];
		$allDisk = getSensorsAllDISKS();	
		for($i = 0; $i < count($allDisk); $i++){	
			$tmp = $allDisk[$i];
			if(IsStr($tmp, '/dev/'.$disk)){
			   $exp = explode(" ", $tmp);
			   $exp = array_diff($exp, array(''));
			   $exp = array_values($exp);		   
			   if(count($exp) > 1){
				   return intval($exp[3]);
			   }		  
			}
		}		
		return '-1';
    }, 1);
	
	$this->ConstantON("LibSensors", "diskperc", "^diskperc_sda1; - Возвращает процентаж занятого объема диска {sda1}", function($expl) use($modClass): string 
    {
		$disk = $expl[0];
		$allDisk = getSensorsAllDISKS();	
		for($i = 0; $i < count($allDisk); $i++){	
			$tmp = $allDisk[$i];
			if(IsStr($tmp, '/dev/'.$disk)){
			   $exp = explode(" ", $tmp);
			   $exp = array_diff($exp, array(''));
			   $exp = array_values($exp);		   
			   if(count($exp) > 1){
				   return intval($exp[4]);
			   }		  
			}
		}		
		return '-1';
    }, 1);
	
	$this->ConstantON("LibSensors", "diskmount", "^diskmount_sda1; - Возвращает точку монтирования диска {sda1}", function($expl) use($modClass): string 
    {
		$disk = $expl[0];
		$allDisk = getSensorsAllDISKS();	
		for($i = 0; $i < count($allDisk); $i++){	
			$tmp = $allDisk[$i];
			if(IsStr($tmp, '/dev/'.$disk)){
			   $exp = explode(" ", $tmp);
			   $exp = array_diff($exp, array(''));
			   $exp = array_values($exp);		   
			   if(count($exp) > 1){
				   return $exp[5];
			   }		  
			}
		}		
		return '-1';
    }, 1);
};

$MODs["LibSensors"] = $libSensorsModule;
?>