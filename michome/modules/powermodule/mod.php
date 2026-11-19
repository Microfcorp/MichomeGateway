<?
$PowerModuleModule = new MichomeModule("PowerModule", MichomeModuleType::ModuleCore);
$PowerModuleModule->BaseClass = new MichomeModuleCore("PowerModules", "Модуль электромонитора", "update=600000;minvoltage=4;fastvoltage=5", [], [["{modir}/powermodules.php?module={id}", "Конфигурация модуля электромонитора"]], __FILE__);

$PowerModuleModule->BaseClass->InstallFunction = function($modClass) {
    $modClass->GetSettingORCreate($this, 'PowerCount', '4.56', 'Цена электроэнергии за 1квт');
};

$PowerModuleModule->BaseClass->InitialFunction = function($modClass) {
    $this->ConstantON("Электроэнергия", "power", "^power_192.168.1.0_5h; - Возвращает количество потребленных Ватт за последние 5 часов", function($expl) use($modClass): string 
    {
		$maxGapSeconds = 3600;
	    $unixH = 60*60-600;
	    $bdData = $this->GetFromEndData(str_ireplace("-", "_", $expl[0]), $expl[1]);
	    if($bdData->IsNull())
		    return 0;

		$bdData = $bdData->SortValue("power");
	    $powers = array_values($bdData->SelectFloat("power"));
		$times = array_values($bdData->SelectUnixTime());

		$count = count($powers);

		// Для расчета интервала нужно минимум 2 точки
		if ($count < 2 || $count !== count($times)) {
			return 0.0;
		}

		// При выборке с конца (ORDER BY id DESC) данные идут от новых к старым.
		// Сортируем по возрастанию времени, чтобы корректно считать разницу.
		array_multisort($times, SORT_ASC, $powers);

		$totalWattSeconds = 0.0;

		for ($i = 1; $i < $count; $i++) {
			$dt = $times[$i] - $times[$i - 1]; // Прошедшее время в секундах

			// Пропускаем некорректные интервалы и слишком долгие простои датчика
			if ($dt <= 0 || $dt > $maxGapSeconds) {
				continue;
			}

			// Средняя мощность на отрезке (метод трапеций)
			$avgPower = ($powers[$i] + $powers[$i - 1]) / 2.0;
			
			// Накапливаем энергию в Ватт-секундах (Джоулях)
			$totalWattSeconds += $avgPower * $dt;
		}

		// Переводим Ватт-секунды в ватт-часы:
		// 1 кВт·ч = 1000 Вт * 3600 секунд = 3 600 000 Вт·с
		return round($totalWattSeconds / 3600, 4);
    }, 3);
	
	 $this->ConstantON("Электроэнергия", "powercount", "^powercount; - Возвращает стоимость эектроэнергии за 1квт", function($expl) use($modClass): string 
    {
	   $powerCount = $modClass->GetSettingORCreate($this, 'PowerCount', '4.56', 'Цена электроэнергии за 1квт')->Value;
	   
	   return $powerCount;
    }, 0);
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