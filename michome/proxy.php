<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once(__DIR__."//../site/secur.php"); ?>
<?php require_once("lib/michom.php"); ?>
<?
	header("Michome-Page: Proxy-Module");   
	$API = new MichomeAPI('localhost', $link);
		
	$module = $_GET['module'];
	$path = isset($_GET['path']) ? $_GET['path'] : "";
	
	$moduleip = $API->GetIPModule($module);
	$html = $API->SendCmd($moduleip, $path, 5000);
	$html = preg_replace('/(href=)\"([^\"]+\")/is', 'href="proxy.php?module='.$module.'&path=$2', $html);
	
	exit($html);
?>
<!-- <html>
<head>
</head>
<iframe id='mainFrame'>
</iframe>
<html> -->