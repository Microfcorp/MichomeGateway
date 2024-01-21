<?
require_once('class.ponvif.php');

function getCamera($API, $modClass, $id){
	$camera = [];
	foreach($modClass->GetParamsType($API, "ManageCamera") as $tmp){
		if($tmp->ParamName == $id){
			$ex = explode("|", $tmp->ParamValue);
			return $ex;
		}
	}
	return $camera;
}

$IPCameraModule = new MichomeModule("IPCamera", MichomeModuleType::Extension);
$IPCameraModule->BaseClass = new MichomeModuleCore("IPCamera", "Модуль поддержки IP камер", "", [], [], __FILE__);
$IPCameraModule->BaseClass->SettingsFunction = function($modClass) {
	
	$onvif = new Ponvif();
	
    echo "Мод добавляет новые функции для работы с ONVIF и FOSCAM IP камерами";
	
	echo "<p>Настройка камер:</p><table><tr><td><b>ID камеры</b></td><td><span title='Список параметров, разделенных | . Список параметров: IP камеры и порт, тип камеры (onvif, foscam), логин, пароль'><b>Значение параметра</b></span></td></tr>";
	foreach($modClass->GetParamsType($this, "ManageCamera") as $tmp){
		echo "<tr><td class='scenesT'><input id='name".$tmp->ID."' style='width:150px;height:20px;' type='text' value='".$tmp->ParamName."'></td><td class='scenesT'><input id='val".$tmp->ID."' style='width:500px;height:20px;' type='text' value='".$tmp->ParamValue."'></td><td class='scenesT'><input type='button' onclick='saveParam(".$tmp->ID.", name".$tmp->ID.".value, val".$tmp->ID.".value)' value='Сохранить'></td><td class='scenesT'><input onclick='removeParam(".$tmp->ID.")' type='button' value='Удалить'></td></tr>";
	}
	echo "<tr><td><input onclick='addParam(\"ManageCamera\")' type='button' value='Добавить камеру'></td></tr></table>";
};

$IPCameraModule->BaseClass->InstallFunction = function($modClass) {
    //$modClass->GetSettingORCreate($this, 'Name', 'Michome system', 'Название прибора в проекте');
    //$modClass->GetSettingORCreate($this, 'Owner', '', 'Владелец прибора в проекте');
};

$IPCameraModule->BaseClass->InitialFunction = function($modClass) {
    $this->ConstantON("IP камера", "camimg", "^camimg_0_0; - Возвращает ссылку на изображение с камеры {0}, с потока {0}", function($expl) use($modClass): string 
    {
	   $camera = getCamera($this, $modClass, $expl[0]);	   
	   $stream = isset($expl[1]) ? $expl[1] : "0";
	   
	   try{
		   if($camera[1] == "onvif"){
			$onvif = new Ponvif();
			$onvif->setUsername($camera[2]);
			$onvif->setPassword($camera[3]);
			$onvif->setIPAddress($camera[0]);
			
			$onvif->initialize();
		
			$sources = $onvif->getSources();
			$profileToken = $sources[0][$stream]['profiletoken'];
			$mediaUri = $onvif->media_GetSnapshotUri($profileToken);
			return $mediaUri;		
		   }
		   elseif($camera[1] == "foscam"){	    
			return "http://".(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $this->Gateway).(!IsStr($_SERVER['HTTP_HOST'], ":") ? ":".$this->portServer : "")."/michome/modules/ipcamera/foscamproxy.php?cmd=".$camera[0]."/tmpfs/snap.jpg&login=".$camera[2]."&password=".$camera[3];		
		   }
	   }
	   catch(Exception $e){
		   return "http://".(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $this->Gateway).(!IsStr($_SERVER['HTTP_HOST'], ":") ? ":".$this->portServer : "")."/michome/styles/nosignal.jpg";
	   }
	   
	   return "";
    }, 3);
	
	$this->ConstantON("IP камера", "camgoto", "^camgoto_0_0_5; - Перемещает камеру {0} на позицию {0} со скоростью {5}", function($expl) use($modClass): string 
    {
	   $camera = getCamera($this, $modClass, $expl[0]);
	   $point = $expl[1];
	   $speed = isset($expl[2]) ? $expl[2] : '5';
	   
	   if($camera[1] == "onvif"){
	    $onvif = new Ponvif();
		$onvif->setUsername($camera[2]);
		$onvif->setPassword($camera[3]);
		$onvif->setIPAddress($camera[0]);
		//$onvif->setMediaUri('http://'.$camera.':8899/onvif/device_service');
		
		$onvif->initialize();
	
		$sources = $onvif->getSources();
		$profileToken = $sources[0][0]['profiletoken'];
		$ptzNodeToken = $sources[0][0]['ptz']['nodetoken'];
		$mediaUri = $onvif->ptz_GotoPreset($profileToken, $point, $speed, $speed, $speed);					
	  }
	  else if($camera[1] == "foscam"){
		  $ur = "http://".(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $this->Gateway).(!IsStr($_SERVER['HTTP_HOST'], ":") ? ":".$this->portServer : "")."/michome/modules/ipcamera/foscamproxy.php?cmd=".$camera[0]."/cgi-bin/hi3510/preset.cgi?-act=goto%26-number=$point&login=".$camera[2]."&password=".$camera[3];
		  file_get_contents($ur);
	  }
	   
	   return "";
    }, 3);
};

$MODs["IPCamera"] = $IPCameraModule;
?>