<?
$OLEDModuleModule = new MichomeModule("OLEDModule", MichomeModuleType::ModuleCore);
$OLEDModuleModule->BaseClass = new MichomeModuleCore("OLED", "Модуль OLED дисплея", "", [], [["{modir}/oled.php?module={id}", "Конфигурация OLED модуля"]], __FILE__);

$OLEDModuleModule->BaseClass->InstallFunction = function($modClass) {
    
};

$OLEDModuleModule->BaseClass->SettingsFunction = function($modClass) {
    printf("Привет, %s\r\n", "ЛОХ");
};

$MODs["OLEDModule"] = $OLEDModuleModule;
?>