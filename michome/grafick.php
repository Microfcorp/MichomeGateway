<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once("lib/michom.php"); ?>
<?php //include_once(__DIR__."//../site/secur.php"); ?>
<?
//вспомогательная функция для определения цвета
function ImageColor($im, $color_array)
{
  return ImageColorAllocate(
  $im,
  isset($color_array['r']) ? $color_array['r'] : 0, 
  isset($color_array['g']) ? $color_array['g'] : 0, 
  isset($color_array['b']) ? $color_array['b'] : 0 
  );
}
 
$par = "C";

$arrrevers = false;
//определим массив с данными, которые необходимо вывести в виде графика.

$ipd = (!empty($_GET['ip'])) ? $_GET['ip'] : 'localhost';

$ipd = str_replace("-", '_', $ipd);
$ipd = is_valid_ip($ipd) ? "'".$ipd."'" : "(SELECT t.ip FROM modules AS t WHERE t.mID = '$ipd' ORDER BY t.id DESC LIMIT 1)";
    
if(!empty($_GET['period'])){
    $period = $_GET['period']; //144-oneday				  
    if(!empty($_GET['start'])){
        $results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip=".$ipd." AND `id` >= ".$_GET['start']." AND `id` <= (".$_GET['start']." + ".$period.") ORDER BY id ASC LIMIT " . $period);
    }
	else{
        $results = mysqli_query($link, "SELECT * FROM michom WHERE michom.ip=".$ipd." ORDER BY id DESC LIMIT " . $period);
        $arrrevers = true;
	}
}
else{
    $results = mysqli_query($link, "SELECT * FROM michom WHERE ip='".$ipd."' ORDER BY id DESC LIMIT 1000");
}
    	

while($row = $results->fetch_assoc()) {
	if($_GET['type'] == "humm"){
		$par = "%";
		if($row['humm'] != ""){
            $data[] = $row['humm'];
            if(!empty($_GET['mode']))
                $date[] = $row['date'];
        }
	}
	elseif($_GET['type'] == "tempul" or $_GET['type'] == "temperbatarey" or $_GET['type'] == "temp" or $_GET['type'] == "Temp"){
		$par = "C";
		if($row['temp'] != ""){
            $data[] = $row['temp'];
            if(!empty($_GET['mode']))
                $date[] = $row['date'];
        }
	}
	elseif($_GET['type'] == "visota"){
		$par = "М";
		if($row['visota'] != ""){
            $data[] = $row['visota'];
            if(!empty($_GET['mode']))
                $date[] = $row['date'];
        }
	}
    elseif($_GET['type'] == "data"){
		$par = "";
		if($row['data'] != ""){
            $data[] = $row['data'];
            if(!empty($_GET['mode']))
                $date[] = $row['date'];
        }
	}
	else{
        $par = "мм";
		if($row['dawlen'] != ""){			
            $data[] = $row['dawlen'];
            if(!empty($_GET['mode']))
                $date[] = $row['date'];
        }
	}
}

if($arrrevers){
    $data = array_reverse($data);
    if(!empty($_GET['mode']))
        $date = array_reverse($date);
}
 
    //параметры изображения  
    $width   = 540; //ширина
    $height  = 304 + 21; //высота
    $padding = 15;  //отступ от края 
    $step = 0.5;      //шаг координатной сетки
 
    if(empty($_GET['mode'])){
        //создаем изображение
        $im = @ImageCreate ($width, $height) or die ("Cannot Initialize new GD image stream");
    
        //задаем цвета, которые будут использоваться при отображении картинки
        $bgcolor = ImageColor($im, array('r'=>255, 'g'=>255, 'b'=>255)); 
        $color = ImageColor($im, array('b'=>175)); 
        $green = ImageColor($im, array('g'=>175)); 
        $gray = ImageColor($im, array('r'=>175, 'g'=>175, 'b'=>175));
        $maxmin = ImageColor($im, array('r'=>3, 'g'=>104, 'b'=>58));	   
    }
 
    //определяем область отображения графика
    $gwidth  = ($width - 2 * $padding); 
    $gheight = ($height - 21) - 2 * $padding; 
 
    //вычисляем минимальное и максимальное значение  
    $min = min($data) - 0.5;
    $min = floor($min/$step) * $step;
    $max = max($data) + 0.5;
    $max = ceil($max/$step) * $step;
 
    //рисуем сетку значений
    for($i = $min; $i < $max + $step; $i += $step)
    {
      $y = $gheight - ($i - $min) * ($gheight) / ($max - $min) + $padding;
      if(empty($_GET['mode'])){
          ImageLine($im, $padding, $y, $gwidth + 1.5 * $padding, $y, $gray);
          ImageTTFText($im, 8, 0, $padding + 1, $y - 1, $gray, __DIR__."//../site/Verdana.ttf", $i);
      }
    }
 
    //отображение графика
    $cnt = count($data);
    $x2 = $padding;
    $i  = 0;
 
    //стоит отметить, что начало координат для картинки находится 
    //в левом верхнем углу, что определяет формулу вычисления координаты y
    $y2 = $gheight - ($data[$i] - $min) * ($gheight) / ($max - $min) + $padding;
 
    for($i = 0; $i < $cnt; $i++)
    {
      $x1 = $x2;
      $x2 = $x1 + (($gwidth) / ($cnt - 1));
      $y1 = $y2;
      $y2 = $gheight - ($data[$i] - $min) * ($gheight) / ($max - $min) + $padding;     
      
      if(!empty($_GET['mode'])){
        $arr[] = ($x1).';'.($y1).";".$data[$i].";".$date[$i];
        $arr[] = ($x2).';'.($y2).";".$data[$i].";".$date[$i];
        $arr[] = (($x1+$x2)/2).';'.(($y1+$y2)/2).";".$data[$i].";".$date[$i];
      }
      else{
        //Рисуются две линии, чтобы сделать график более заметным      
        ImageLine($im, $x1, $y1, $x2, $y2, $color);
        ImageLine($im, $x1 + 1, $y1, $x2 + 1, $y2, $color); 
      }
    }
	
    if(!empty($_GET['mode'])){
        exit(json_encode(array($arr)));
    }
    else{   
        $SrAr = array_sum($data)/count($data);       
        ImageTTFText($im, 10, 0, 10, $height - 21, $maxmin, __DIR__."//../site/Verdana.ttf", "Максимальное значение на графике " . max($data) . $par.". Минимальное " . min($data) . $par.".\nАмплитуда равна " . substr((max($data) - min($data)),0,4) . $par.". Среднее значение равно ".substr($SrAr,0,5). $par.".");     
        //Отдаем полученный график браузеру, меняя заголовок файла
        header ("Content-type: image/png");	
        ImagePng ($im);
        imagedestroy($im);
    }
?>
