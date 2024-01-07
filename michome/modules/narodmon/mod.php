<?
$NarodMonModule = new MichomeModule("NarodMon", MichomeModuleType::Extension);
$NarodMonModule->BaseClass = new MichomeModuleCore("NarodMon", "Модуль поддержки сервиса Народного мониторинга", "", [], [], __FILE__);
$NarodMonModule->BaseClass->SettingsFunction = function($modClass) {
    echo "<p>Ваш MAC для регистрации на сервисе Народного Мониторинга: <span title='MAC адрес первого модуля, среди подключенных'><i>".str_replace(":", "-", $modClass->GetSettingORCreate($this, 'MAC', $this->GetAllModulesBD()[0]['mac'], 'MAC адрес для сервиса')->Value)."</i></span></p>";
	echo "<p>Данные для передачи на сервис:</p><table><tr><td><b>Метрика</b></td><td><span title='Список параметров, разделенных | . Список параметров: name=(название датчика) value=(значение параметра) time=(дата измерений в формате unixtime)'><b>Значение параметра</b></span></td></tr>";
	foreach($modClass->GetParamsType($this, "ToSend") as $tmp){
		echo "<tr><td class='scenesT'><input id='name".$tmp->ID."' style='width:150px;height:20px;' type='text' value='".$tmp->ParamName."'></td><td class='scenesT'><input id='val".$tmp->ID."' style='width:500px;height:20px;' type='text' value='".$tmp->ParamValue."'></td><td class='scenesT'><input type='button' onclick='saveParam(".$tmp->ID.", name".$tmp->ID.".value, val".$tmp->ID.".value)' value='Сохранить'></td><td class='scenesT'><input onclick='removeParam(".$tmp->ID.")' type='button' value='Удалить'></td></tr>";
	}
	echo "<tr><td><input onclick='addParam(\"ToSend\")' type='button' value='Добавить'></td></tr></table>";
};

$NarodMonModule->BaseClass->InstallFunction = function($modClass) {
    $modClass->GetSettingORCreate($this, 'Name', 'Michome system', 'Название прибора в проекте');
    $modClass->GetSettingORCreate($this, 'Owner', '', 'Владелец прибора в проекте');
};

$NarodMonModule->BaseClass->CronFunction5min = function($modClass) {
	/*
	{
	  "devices": [
		{
		  "mac": "DEVICE_MAC", // уникальный для проекта серийный номер прибора;
		  "name": "DEVICE NAME", // название прибора в кодировке UTF-8;
		  "owner": "USERNAME", // логин или email или сотовый владельца прибора для авторегистрации;
		  "lat": 50.931593, // широта места установки прибора;
		  "lon": 39.223263, // долгота места установки прибора;
		  "alt": 29, // высота над уровнем моря места установки прибора;
		  "sensors": [
			{
			  "id": "T1", // метрика датчика, уникальная для прибора;
			  "name": "SENSOR NAME", // название датчика в кодировке UTF-8;
			  "value": 00.00, // показание датчика;
			  "unit": "C", // единица измерения для датчика в UTF-8;
			  "time": 1683878417 // время актуальности показания датчика UnixTime (UTC+0);
			},
			{
			 // прочие датчики на приборе
			}
		  ]
		},
		{
		// прочие приборы
		}
	  ]
	}
	*/
	
	//4C:75:25:0C:4F:2E
	//
	$data = array();
	$data["devices"] = array();
	$data["devices"][] = array("mac"=>$modClass->GetSettingORCreate($this, 'MAC', $this->GetAllModulesBD()[0]['mac'], 'MAC адрес для сервиса')->Value, 'name'=>$modClass->GetSettingORCreate($this, 'Name', '', '')->Value, 'owner'=>$modClass->GetSettingORCreate($this, 'Owner', '', '')->Value, 'lat'=>$this->GetSettingORCreate("latitude", "50.860145", "Широта в градусах")->Value, 'lon'=>$this->GetSettingORCreate("longitude", "39.082347", "Долгота в градусах")->Value, "sensors"=>array());
	$sensors = $data["devices"][count($data["devices"]) - 1]["sensors"];
	foreach($modClass->GetParamsType($this, "ToSend") as $tmp){
		$pV = $tmp->ParamValue;
		$ps = array();
		foreach(explode('|', $pV) as $tmp1){
			$ps[explode('=', $tmp1)[0]] = $this->GetConstant(explode('=', $tmp1)[1]);
		}
		$ps['name'] = isset($ps['name']) ? $ps['name'] : $modClass->GetSettingORCreate($this, 'Name', '', '')->Value;
		$ps['value'] = isset($ps['value']) ? $ps['value'] : "00.00";
		$ps['time'] = isset($ps['time']) ? $ps['time'] : time();
		$sensors[] = array('id'=>$tmp->ParamName, 'name'=>$ps['name'], 'value'=>$ps['value'], 'time'=>$ps['time']);
	}
	$data["devices"][count($data["devices"]) - 1]["sensors"] = $sensors;
	

	$ch = curl_init("https://narodmon.ru/json");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	$reply = curl_exec($ch);
	curl_close($ch);
	echo $reply;
};

$MODs["NarodMon"] = $NarodMonModule;
?>