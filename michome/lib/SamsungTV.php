<?php
class SamsungTV
{
    // объявление свойства
    public $IP = 'localhost';
    
    // объявление метода
    public function __construct($IP) {
       $this->IP = $IP;
    }
    
    public function RunCmdTCP($key){
        $ret = shell_exec("python3 -m samsungctl --method legacy --name Michome --description SmartHome --host ".($this->IP)." ".$key);
        //file_put_contents("/var/www/html/t.txt", $ret);
    }
    
    public function PowerOnCEC(){
        shell_exec("echo \"on 0\" | cec-client -s");
    }
    
    public function PowerOffCEC(){
        shell_exec("echo \"standby 0\" | cec-client -s");
    }
    
    public function AsHDMICEC(){
        //shell_exec("echo \"as\" | cec-client -s");
		$this->RunCmdTCP("KEY_HDMI");
    }
    
    public function PowerOffTCP(){
        $this->RunCmdTCP("KEY_POWEROFF");
    }
    
    public function VolumeUP(){
        $this->RunCmdTCP("KEY_VOLUP");
    }
    
    public function VolumeDown(){
        $this->RunCmdTCP("KEY_VOLDOWN");
    }
    
    public function Mute(){
        $this->RunCmdTCP("KEY_MUTE");
    }
    
    public function DTV(){
        $this->RunCmdTCP("KEY_DTV");
    }
	
	public function ChUP(){
        $this->RunCmdTCP("KEY_CHUP");
    }
	
	public function ChDown(){
        $this->RunCmdTCP("KEY_CHDOWN");
    }
}
?>