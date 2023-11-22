<?php
$str = "Температура на улице: ^rm_termometr-okno_Temp;С
Температура дома: ^rm_termometr-okno_Temp;С
Восход: ^ds;";

$expl = substr($str, strpos($str, "^rm")+4, (strpos($str, ";") - (strpos($str, "^rm")+4)));     
//$rd = $this->GetPosledData(str_replace("-", "_", explode('_', $expl)[0]))->GetFromName(explode('_', $expl)[1]);
//$rd = str_replace("_","-", $rd);
$str = str_replace("^rm_".$expl.";", "ЗАМЕНА", $str);

echo $str;
?>