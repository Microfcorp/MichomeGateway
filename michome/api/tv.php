<?php
header('Access-Control-Allow-Origin: *');
require_once("../../michome/lib/michom.php");

$ip = $_GET['ip'];
$cmd = $_GET['cmd'];
$smg = new SamsungTV($ip);

if($cmd == 'PowerOnCEC')
    $smg->PowerOnCEC();
elseif($cmd == 'PowerOffCEC')
    $smg->PowerOffCEC();
elseif($cmd == 'AsHDMICEC')
    $smg->AsHDMICEC();
elseif($cmd == 'PowerOffTCP')
    $smg->PowerOffTCP();
elseif($cmd == 'VolumeUP')
    $smg->VolumeUP();
elseif($cmd == 'VolumeDown')
    $smg->VolumeDown();
elseif($cmd == 'Mute')
    $smg->Mute();
elseif($cmd == 'DTV')
    $smg->DTV();
else
    $smg->RunCmdTCP($cmd);    
?>
