<?php include_once(__DIR__."//../site/mysql.php"); ?>
<?php include_once("lib/michom.php"); ?>
<?php //include_once(__DIR__."//../site/secur.php"); ?>
<?
$API = new MichomeAPI('localhost', $link);
header("Michome-Page: Graphic-Service");

//Скрипт формирования графика данных
//Структура запросов:
//width=540 - Ширина графика (необязательно)
//height=325 - Высота графика (необязательно)
//mode=png jpg gif ИЛИ mode=map - Формат выходного изображения или json карта координат (по умолчанию png)
//ip=192.168.1.11 ИЛИ ip=termometr_okno - Устройтво, с которого смотреть данные
//par=auto - Параметр данных для печати в тексте (по умолчанию auto)
//type=temp - Тип данных
//start=157894 - Стартовый id данных
//period=144 - Количество данных для выборки
//filter=median - Фильтр для сглаживания данных

//параметры изображения  
$width   = isset($_GET['width']) ? intval($_GET['width']) : 540; //ширина
$height  = isset($_GET['height']) ? intval($_GET['height']) : 325; //высота
$padding = 15;  //отступ от края 
$step = 0.5;      //шаг координатной сетки, считается автоматически
$minstep = 0.5;
$maxstep = 5;
$filter = isset($_GET['filter']) ? $_GET['filter'] : "none";

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
//вспомогательная функция для определения единицы измерения
function GeneratePar($typeData)
{
	$typeData = mb_strtolower($typeData);
	if(IsStr($typeData, "temp"))
		return 'C';
	elseif(IsStr($typeData, "rsid"))
		return ' dbm';
	elseif(IsStr($typeData, "humm") || IsStr($typeData, "vlazn"))
		return '%';
	elseif(IsStr($typeData, "pres") || IsStr($typeData, "dawlen"))
		return 'мм';
	elseif(IsStr($typeData, "visot") || IsStr($typeData, "alt"))
		return 'М';
	return '';
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : "png";

//Модуль для сбора информации
$ipd = (isset($_GET['ip'])) ? $_GET['ip'] : 'localhost';
$ipd = str_replace("-", '_', $ipd);
    
if(isset($_GET['period'])){ //Если указано количество строк для выборки
    $period = intval($_GET['period']); //Количество строк для выборки				  
    if(isset($_GET['start'])){ //Точка начала выборки, иначе выборка с конца
		$start = intval($_GET['start']);
		$dataBD = $API->GetDataRange($ipd, $start, $start + $period, false)->SortByID();
	}
	else{
		$dataBD = $API->GetFromEndData($ipd, $period)->SortByID();
	}
}
else{
    $dataBD = $API->GetFromEndData($ipd, 144); //Иначе выбираем 144 строки с конца
}

$par = (isset($_GET['par']) && $_GET['par'] != "auto") ? $_GET['par'] : GeneratePar($_GET['type']); //Единица измерения параметров
$data = $dataBD->SelectFloat($_GET['type']); //Получаем данные по типу в виде float

//Определяем, целочисленные ли значения у нас
$isNoneFloat = true;
foreach($data as $tmp)
	if (is_float($tmp))
		$isNoneFloat = false;
	
//Фильтруем данные
if(!$isNoneFloat){
	if($filter == "median"){
	for($i = 2; $i < count($data); $i = $i + 3){
			$data[$i] = expRunningAverage($data[$i], medianeFilter($data[$i-2], $data[$i-1], $data[$i]), 1);
			$data[$i-1] = ($data[$i-2] + expRunningAverage($data[$i-2], $data[$i-1], 0.15)) / 2;
			$data[$i-1] = round($data[$i-1], 2);
			$data[$i] = ($data[$i-1] + $data[$i]) / 2;
			$data[$i] = round($data[$i], 2);
		}
	}
}
if($mode == "map")
	$date = $dataBD->Select('date'); //Получаем данные времени

if(count(array_filter($data)) < 1){
	if($mode == "map"){
		exit(json_encode(array("responce"=>"error", "text"=>"Ошибка формирования графика. Данные отсутствуют")));
	}
	elseif($mode == "png" || $mode == "jpg" || $mode == "gif"){
		$im = @ImageCreate ($width, $height) or die ("Cannot Initialize new GD image stream");
		$bgcolor = ImageColor($im, array('r'=>255, 'g'=>255, 'b'=>255));
		ImageTTFText($im, 14, 0, 10, $height/2, ImageColor($im, array('r'=>255)), __DIR__."//../site/Verdana.ttf", "Ошибка формирования графика. Данные отсутствуют");     
		if($mode == "png"){
			header("Content-type: image/png");	
			ImagePng($im);
		}
		elseif($mode == "jpg"){
			header("Content-type: image/jpeg");	
			imagejpeg($im);
		}
		elseif($mode == "gif"){
			header("Content-type: image/gif");	
			imagegif($im);
		}
		imagedestroy($im);
	}
	else
		exit("Ошибка формирования графика. Данные отсутствуют");
}

if($mode == "png" || $mode == "jpg" || $mode == "gif"){
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

//Авто подбор шага сетки
$deltaMaxMin = max($data) - min($data);
if($deltaMaxMin > 1){ 
	//Каждые 20 единиц увеличиваем на $minstep
	$step = (round(($deltaMaxMin / 9) * $minstep, 1));
	if($isNoneFloat) $step = roundToHalf($step);
	$step = min($step, $maxstep);
}
else{
	$step = $minstep;
}

//вычисляем минимальное и максимальное значение  
$min = min($data) - $step;
$min = floor($min/$step) * $step;
$max = max($data) + $step;
$max = ceil($max/$step) * $step;

//рисуем сетку значений
for($i = $min; $i < $max + $step; $i += $step)
{
  $i = round($i, 1);
  $y = $gheight - ($i - $min) * ($gheight) / ($max - $min) + $padding;
  if($mode == "png" || $mode == "jpg" || $mode == "gif"){
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
  $x2 = $x1 + (($gwidth) / ($cnt == 1 ? 1 : ($cnt - 1)));
  $y1 = $y2;
  $y2 = $gheight - ($data[$i] - $min) * ($gheight) / ($max - $min) + $padding;     
  
  if($mode == "map"){
	$arr["data"][] = ($x1).';'.($y1).";".$data[$i].";".$date[$i];
	$arr["data"][] = ($x2).';'.($y2).";".$data[$i].";".$date[$i];
	$arr["data"][] = (($x1+$x2)/2).';'.(($y1+$y2)/2).";".$data[$i].";".$date[$i];
  }
  elseif($mode == "png" || $mode == "jpg" || $mode == "gif"){
	//Рисуются две линии, чтобы сделать график более заметным      
	ImageLine($im, $x1, $y1, $x2, $y2, $color);
	ImageLine($im, $x1 + 1, $y1, $x2 + 1, $y2, $color); 
  }
}

if($mode == "map"){
	header("Content-type: application/json");	
	$arr["responce"] = "OK";
	exit(json_encode($arr));
}
else{   
	$SrAr = array_sum($data)/count($data);       
	ImageTTFText($im, 10, 0, 10, $height - 21, $maxmin, __DIR__."//../site/Verdana.ttf", "Максимальное значение на графике " . max($data) . $par.". Минимальное " . min($data) . $par.".\nАмплитуда равна " . substr((max($data) - min($data)),0,4) . $par.". Среднее значение равно ".substr($SrAr,0,5). $par.".");     
	//Отдаем полученный график браузеру, меняя заголовок файла
	if($mode == "png"){
		header("Content-type: image/png");	
		ImagePng($im);
	}
	elseif($mode == "jpg"){
		header("Content-type: image/jpeg");	
		imagejpeg($im);
	}
	elseif($mode == "gif"){
		header("Content-type: image/gif");	
		imagegif($im);
	}
	imagedestroy($im);
}
?>
