<?
$BolidModuleModule = new MichomeModule("BolidC2000", MichomeModuleType::ModuleCore); //Extension
$BolidModuleModule->BaseClass = new MichomeModuleCore("BolidC2000", "Модуль соединения с Bolid-C2000, посредством C2000-Ethernet", "", [], [["{modir}/bolidmodules.php?module={id}", "Конфигурация прибора C2000"]], __FILE__);

$BolidModuleModule->BaseClass->SettingsFunction = function($modClass) {
    echo "<script>
			function request_bolid(val){
				postAjax('/michome/modules/bolid_c2000/api/request_bolid.php?device='+val, 'POST', '', function(d){
						const resp = JSON.parse(d);
						
						alert('Обнаружен прибор: ' + resp['device']['device_name']);
					});
			}
		 </script>";
	
	//echo "<p>Ваш MAC для регистрации на сервисе Народного Мониторинга: <span title='MAC адрес первого модуля, среди подключенных'><i></i></span></p>";
	echo "<p>Удаленные устройства C2000:</p><table><tr><th>Адрес прибора</th><th>Что то</th></tr>";
	foreach($modClass->GetParamsType($this, "ToSend") as $tmp){
		echo "<tr><td class='scenesT'><input id='name".$tmp->ID."' style='width:150px;height:20px;' type='text' value='".$tmp->ParamName."'></td><td class='scenesT'><input id='val".$tmp->ID."' style='width:500px;height:20px;' type='text' value='".$tmp->ParamValue."'></td><td class='scenesT'><input type='button' onclick='saveParam(".$tmp->ID.", name".$tmp->ID.".value, val".$tmp->ID.".value)' value='Сохранить'></td><td class='scenesT'><input onclick='removeParam(".$tmp->ID.")' type='button' value='Удалить'></td><td class='scenesT'><input onclick='request_bolid(".$tmp->ParamName.")' type='button' value='Опросить'></td></tr>";
	}
	echo "<tr><td><input onclick='addParam(\"ToSend\")' type='button' value='Добавить'></td></tr></table>";
};
$BolidModuleModule->BaseClass->InstallFunction = function($modClass) {
    $modClass->GetSettingORCreate($this, 'EthernetAddress', '192.168.0.0', 'Адрес для подключения к C2000-Ethernet (через ,)');
};

$BolidModuleModule->BaseClass->InitialFunction = function($modClass) {
    $this->ConstantON("Bolid", "shstat", "^shstat_1_2; - Возвращает состояние шлейфа на приборе {1}, шлейф {2}", function($expl) use($modClass): string 
    {
	    $deviceID = intval($expl[0]);
	    $shID = intval($expl[1])-1;
	   
	    $gateways = $modClass->GetSettingORCreate($this, 'EthernetAddress', '192.168.0.0', '')->Value;
		$gateways_key = "";
		foreach(explode(',', $gateways) as $tmp)
			$gateways_key = $gateways_key . "-g ".$tmp ." ";

		$binPath = ServerPath . "/modules/bolid_c2000/bolid_reader";
		$bolid_resp = shell_exec($binPath . " " . $gateways_key . " -d $deviceID -j");
		$bolid_resp = json_decode($bolid_resp, true);
		$sh = $bolid_resp['zones'][$shID]['status_text'];
		return $sh;
    }, 2);
	
	/* $this->ConstantON("Электроэнергия", "powercount", "^powercount; - Возвращает стоимость эектроэнергии за 1квт", function($expl) use($modClass): string 
    {
	   $powerCount = $modClass->GetSettingORCreate($this, 'PowerCount', '4.56', 'Цена электроэнергии за 1квт')->Value;
	   
	   return $powerCount;
    }, 0);*/
};

$BolidModuleModule->BaseClass->POSTFunction = function($arrayData) {
    /*$modClass = $arrayData[0];
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
	return $tmp;*/
};

$MODs["BolidC2000"] = $BolidModuleModule;
?>