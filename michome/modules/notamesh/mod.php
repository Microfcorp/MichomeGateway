<?
$NotaMeshModule = new MichomeModule("NotaMesh", MichomeModuleType::ModuleCore);
$NotaMeshModule->BaseClass = new MichomeModuleCore("NotaMesh", "Модуль умной гирлянды", "", [["notamesh?type=NM_ON", "Включить гирлялнду"], ["notamesh?type=NM_OFF", "Выключить гирлялнду"]], [["{modir}/notamesh.php?module={id}", "Управление гирляндой"]], __FILE__);
$NotaMeshModule->BaseClass->SettingsFunction = function($API, $mod) {
    printf("Привет, %s\r\n", $name);
};

$MODs["NotaMesh"] = $NotaMeshModule;
?>