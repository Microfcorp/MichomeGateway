<?
$BolidModuleModule = new MichomeModule("BolidC2000", MichomeModuleType::ModuleCore); //Extension
$BolidModuleModule->BaseClass = new MichomeModuleCore("BolidC2000", "Модуль соединения с приборами серии Bolid-C2000, посредством C2000-Ethernet", "", [], [["{modir}/bolidmodules.php?module={id}", "Конфигурация прибора C2000"]], __FILE__);

$BolidModuleModule->BaseClass->SettingsFunction = function($modClass) {
    echo "<script>
			function request_bolid(val){
				postAjax('/michome/modules/bolid_c2000/api/request_bolid.php?device='+val, 'POST', '', function(d){
						const resp = JSON.parse(d);
						
						alert('Обнаружен прибор: ' + resp['device']['device_name']);
					});
			}
			function open_bolid(val){
				document.location = 'modules/bolid_c2000/bolidmodules.php?device='+val;
			}
		 </script>";
	
	echo "<p><a href='#' onclick='CloseOpen(helpCodeBolid)'>Показать/Скрыть справку по кодам состояния приборов</a></p>";
	echo "<div id='helpCodeBolid' style='display: none;'><table><tr><th>Код статуса</th><th>Расшифровка статуса</th></tr>
	  <tr><td>3</td><td>Тревога (нарушение ШС)</td></tr>
      <tr><td>4</td><td>Нарушение технолог. ШС</td></tr>
      <tr><td>17</td><td>Неудачное взятие</td></tr>
      <tr><td>24</td><td>Взят под охрану</td></tr>
      <tr><td>25</td><td>Ошибка взятия (ШС нарушен)</td></tr>
      <tr><td>109</td><td>Снят с охраны</td></tr>
      <tr><td>23</td><td>Задержка взятия на охрану</td></tr>
      <tr><td>199</td><td>Вскрытие корпуса прибора (тампер)</td></tr>
      <tr><td>34</td><td>Восстановление корпуса (тампер)</td></tr>
      <tr><td>44</td><td>Обрыв ШС</td></tr>
      <tr><td>45</td><td>Короткое замыкание ШС</td></tr>
      <tr><td>131</td><td>Задержка на вход / выход</td></tr>
	  </table></div>";
	
	echo "<br />";
	echo "<p>Удаленные устройства C2000:</p><table><tr><th>Адрес прибора</th><th>Не используется</th></tr>";
	foreach($modClass->GetParamsType($this, "ToSend") as $tmp){
		echo "<tr><td class='scenesT'><input id='name".$tmp->ID."' style='width:150px;height:20px;' type='text' value='".$tmp->ParamName."'></td><td class='scenesT'><input id='val".$tmp->ID."' style='width:500px;height:20px;' type='text' value='".$tmp->ParamValue."'></td><td class='scenesT'><input type='button' onclick='saveParam(".$tmp->ID.", name".$tmp->ID.".value, val".$tmp->ID.".value)' value='Сохранить'></td><td class='scenesT'><input onclick='removeParam(".$tmp->ID.")' type='button' value='Удалить'></td><td class='scenesT'><input onclick='request_bolid(".$tmp->ParamName.")' type='button' value='Опросить'></td><td class='scenesT'><input onclick='open_bolid(".$tmp->ParamName.")' type='button' value='Перейти к прибору'></td></tr>";
	}
	echo "<tr><td><input onclick='addParam(\"ToSend\")' type='button' value='Добавить'></td></tr></table>";
	echo "<br />";
	echo "<p>Данные модуля добавляет следующие параметры для чтения: <br />status{адрес устройства}{номер ШС} - код статуса устройства, <br />adc{адрес устройства}{номер ШС} - значение АЦП</p>";
};
$BolidModuleModule->BaseClass->InstallFunction = function($modClass) {
    $modClass->GetSettingORCreate($this, 'EthernetAddress', '192.168.0.0', 'Адрес для подключения к C2000-Ethernet (через ,)');
};

$BolidModuleModule->BaseClass->InitialFunction = function($modClass) {
    $this->ConstantON("Bolid", "shstat", "^shstat_1_2; - Возвращает состояние шлейфа (в реальном времени) на приборе {1}, шлейф {2}", function($expl) use($modClass): string 
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
	
	$this->ConstantON("Bolid", "strcode", "^strcode_109; - Возвращает текстовое представление кода состояния", function($expl) use($modClass): string 
    {
		$code = intval($expl[0]);
	    switch ($code) {
			case 0x03: return "Тревога (нарушение ШС)";
			case 0x04: return "Нарушение технолог. ШС";
			case 0x11: return "Неудачное взятие";
			case 0x18: return "Взят под охрану";
			case 0x19: return "Ошибка взятия (ШС нарушен)";
			case 0x6D: return "Снят с охраны";
			case 0x17: return "Задержка взятия на охрану";
			case 0xC7: return "Вскрытие корпуса прибора (тампер)";
			case 0x22: return "Восстановление корпуса (тампер)";
			case 0x2C: return "Обрыв ШС";
			case 0x2D: return "Короткое замыкание ШС";
			case 0x83: return "Задержка на вход / выход";
			default:   return "Другой код состояния/события";
		}
    }, 1);
	
	$this->ConstantON("Bolid", "shset", "^shset_1_2_arm; - Ставить на охрану шлейф {2} на приборе {1}, доступно {arm}, {disarm}, {reset}", function($expl) use($modClass): string 
    {
		$deviceID = intval($expl[0]);
		$shID = intval($expl[1]);
		$mode = $expl[2];
		
	    $gateways = $modClass->GetSettingORCreate($this, 'EthernetAddress', '192.168.0.0', '')->Value;
		$gateways_key = "";
		foreach(explode(',', $gateways) as $tmp)
			$gateways_key = $gateways_key . "-g ".$tmp ." ";

		$binPath = ServerPath . "/modules/bolid_c2000/bolid_reader";
		$bolid_resp = shell_exec($binPath . " " . $gateways_key . " -d $deviceID -j -z $shID -a $mode");
		
		return "";
    }, 3);
	
	$this->ConstantON("Bolid", "bld", "^bld; - Возвращает адрес прибора на линии (Только для триггера в условии)", function($expl) use($modClass): string {}, 1000);
	$this->ConstantON("Bolid", "blz", "^blz; - Возвращает ШС сработки триггера (Только для триггера в условии)", function($expl) use($modClass): string {}, 1000);
	$this->ConstantON("Bolid", "bls", "^bls; - Возвращает код статус триггера (Только для триггера в условии)", function($expl) use($modClass): string {}, 1000);
	$this->ConstantON("Bolid", "bld", "^bla; - Возвращает значение АЦП триггера (Только для триггера в условии)", function($expl) use($modClass): string {}, 1000);
	$this->ConstantON("Bolid", "bld", "^blss; - Возвращает текстовое представление статуса триггера (Только для триггера в условии)", function($expl) use($modClass): string {}, 1000);
};

$BolidModuleModule->BaseClass->POSTFunction = function($arrayData) {
    
};

$MODs["BolidC2000"] = $BolidModuleModule;
?>