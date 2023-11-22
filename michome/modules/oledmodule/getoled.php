<?php
//error_reporting(0);
header('Access-Control-Allow-Origin: *');
include_once("../../../site/mysql.php");
require_once("../../lib/michom.php");

$API = new MichomeAPI('localhost', $link); 
$module = $_GET["module"];

$Pages = array();
$JSONData = Array("name"=>"getoled", "col"=>0);

foreach($OLEDModuleModule->GetParamsType($API, $module) as $tmp){
	$Pages[] = [$tmp->ParamName, $API->GetConstant($tmp->ParamValue)];
}

$JSONData["col"] = count($Pages);
$JSONData["pages"] = $Pages;

echo(json_encode($JSONData));
?>
