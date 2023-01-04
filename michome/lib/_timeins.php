<?php
function _TimeIns($link, $device = 1, $type = FALSE, $datee = "", $isArray = FALSE){   

    $device = ($device != 1) ? ("`ip`=".(is_valid_ip($device) ? "'".$device."'" : "(SELECT t.ip FROM modules AS t WHERE t.mID = '$device' ORDER BY t.id DESC LIMIT 1)")) : 1;

    if($type == "oneday"){
        $id = 0;
        $id1 = 0;
        
        //SELECT * FROM michom WHERE `date` >= CURDATE() AND `ip` = '192.168.1.11' ORDER BY id LIMIT 1 
        $results = mysqli_query($link, "SELECT * FROM michom WHERE `date` >= CURDATE() AND ".$device." ORDER BY id LIMIT 1");

        while($row = $results->fetch_assoc()) {
        $id = $row['id'];
        }
        
        $results = mysqli_query($this->$link, "SELECT * FROM michom WHERE `id` >= ".$id." AND ".$device."");

        while($row = $results->fetch_assoc()) {
        $id1 = $row['id'];
        }
        
        return $id1 - $id;
    }
    elseif($type == "selday"){
        //SELECT * FROM michom WHERE `date` >= "2018-08-06 00:00:00" AND `date` <= "2018-08-07 00:00:00"
        
        $date1 = $datee;
        
        $date = new DateTime($date1);
        $date->add(new DateInterval('P1D'));
        //echo $date->format('Y-m-d') . "\n";
        
        
        $results = mysqli_query($link, "SELECT MAX(`id`), MIN(`id`) FROM michom WHERE ".$device." AND `date` >= '".$date1."' AND `date` <= '".$date->format('Y-m-d')."' LIMIT 1");
        
        while($row = $results->fetch_assoc()) {
            $max = $row['MAX(`id`)'];
            $min = $row['MIN(`id`)'];
        }
        
		
        if(empty($max)){
            return !$isArray ? ('nan'.";".'nan'.";".'nan') : ['nan', 'nan', 'nan'];
        }
        else{
            return !$isArray ? ($min.";".$max.";".($max-$min)) : [$min, $max, ($max-$min)];
        }
    }
    elseif($type == "diap"){
        //SELECT * FROM michom WHERE `date` >= "2018-08-06 00:00:00" AND `date` <= "2018-08-07 00:00:00"
        
        $date1 = $datee;
        
        //echo $date->format('Y-m-d') . "\n";
        
        
        $results = mysqli_query($link, "SELECT * FROM michom WHERE ".$device." AND `date` >= '".$date1."'");

        $ids = array();
        
        while($row = $results->fetch_assoc()) {
        $ids[] = $row['id'];
        }
        
        if(count($ids) < 1){
            return !$isArray ? ('nan'.";".'nan'.";".'nan') : ['nan', 'nan', 'nan'];
        }
        else{
            return !$isArray ? (min($ids).";".max($ids).";".(max($ids)-min($ids))) : [min($ids), max($ids), (max($ids)-min($ids))];
        }
    }
}
?>
