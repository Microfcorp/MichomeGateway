
# Написание модулей для системы Michome

## Структура модуля

Каждый модуль - это файл mod.php в директории с названием модуля

## Стуркутра файла модуля

Внутри файла mod.php инициализируется специальный класс модуля, а затем данный класс добавляется в массив $MODs

```php
	$MODs["libSensors"] = $libSensorsModule; //Загружаем модуль libSensors в Michome
```

Иначально создается класс MichomeModule, где в конструктор класса передается название модуля и его тип
```php
	$libSensorsModule = new MichomeModule("LibSensors", MichomeModuleType::Extension); //Создание класса MichomeModule
	/*
		Доступные типы:
		MichomeModuleType::ModuleExtension => 'Добавление функций стандартному модулю',
		MichomeModuleType::ModuleCore => 'Добавление поддержки модуля',
		MichomeModuleType::Extension => 'Расширение Michome',
		MichomeModuleType::Web => 'Веб-интерфейс',
	*/
```

Затем создается класс MichomeModuleCore, где в конструктор класса передается название, описание модуля, настройки по умолчанию (только для MichomeModuleType::ModuleCore или ModuleExtension), команды (только для MichomeModuleType::ModuleCore или ModuleExtension), страница управления внутри Michome, путь до этого скрипта (\_\_FILE\_\_)
```php
	//Пример для интеграции Extension
	$libSensorsModule->BaseClass = new MichomeModuleCore("LibSensors", "Модуль интеграции показаний sensors", "", [], [], __FILE__);
	//Пример для интеграции ModuleCore
	$OLEDModuleModule->BaseClass = new MichomeModuleCore("OLED", "Модуль OLED дисплея", "", [], [["{modir}/oled.php?module={id}", "Конфигурация OLED модуля"]], __FILE__);
```

У данного класса есть следующие параметры:
```php
	public $InstallFunction; //function($mod) //Выполняется при установке (Расширение - 1 раз)
	public $SettingsFunction; //function($mod) //Выполняется каждый раз на странице настроек, this - объект класса michome
	public $InitialFunction; //function($mod) //Выполняется каждый раз при загрузки библиотеки Michome
	public $POSTFunction; //function($mod) //Выполняется каждый раз при обработке события получения данных. Должен возвращать класс POSTData

	public $CronFunction; //function($mod) //Выполняется каждую минуту
	public $CronFunction5min; //function($mod) //Выполняется каждые 5 минут
	public $CronFunction10min; //function($mod) //Выполняется каждые 10 минут
	public $CronFunctionStartup; //function($mod) //Выполняется при запуске системы
	public $CronFunctionStop; //function($mod) //Выполняется при выключении системы
```

Описание класса MichomeModule
```php
	class MichomeModule{
		public function GetAllSettings($API);
		public function GetSettingORCreate($API, $Names, $ValueDefault, $Desc);
		public function IsInstalled();
		public function Install($API);
		public function AddParam($API, $ParamType);
		public function RemoveParam($API);
		public function GetParamByID($API);
		public function GetParams($API);
		public function GetParamsType($API, $type);
	}
```

Описание класса POSTData
```php
	class POSTData{
		public function __construct($data = "", $temp = 0, $humm = 0, $dawlen = 0, $visota = 0);
	}
```

Описание класса ModsParam
```php
	class MichomeModule{
		public function __construct($ID, $ModName, $ParamType, $ParamName, $ParamValue, $link);
		public function GetValue();
		public function SetValue($name, $data);
	}
```
