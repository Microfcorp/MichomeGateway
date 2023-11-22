<?php

function _GetWebPagesFromType($link, $typePage){
	$results = mysqli_query($link, "SELECT * FROM WebPages WHERE `Type` = '$typePage'");
	$datas = [];
	while($row = $results->fetch_assoc()) {
        $datas[] = new WebPage($row['ID'], $row['Type'], $row['SubType'], $row['Name'], $row['Value'], $link);
    }
	return new WebPageCollection($datas);
}

function _AddWebPage($link, $typePage){
	$results = mysqli_query($link, "INSERT INTO `WebPages` (`Type`) VALUES ('$typePage');");
}

function _RemoveWebPage($link, $id){
	$results = mysqli_query($link, "DELETE FROM `WebPages` WHERE  `ID` = '$id'");
}

function _SetWebPage($link, $id, $subtype, $name, $value, $newid){
	$newid = (($newid == -1) ? $id : $newid);
	$id = intval($id);
	$newid = $newid;
	$name = mysqli_real_escape_string($link, $name);
	$value = mysqli_real_escape_string($link, $value);
	return mysqli_query($link, "UPDATE `WebPages` SET `SubType`='$subtype', `Name`='$name', `Value`='$value', `ID`='$newid' WHERE `ID` = '$id'");	
}

function _GenerateHTMLPrognoz($modD, $modT){
	return '<script>
			function autoloadPROGNOZ(){
			   postAjax("prognoz.php?modD='.$modD.'&modT='.$modT.'", "GET", "", function(d){document.getElementById("prognozWeather").innerHTML = d;});
			}
			window.setTimeout("autoloadPROGNOZ()",1);
			</script>
			<p id="prognozWeather">Направление ветра: Загрузка...<br />
							Скорость ветра: Загрузка...<br />
							Тенденция давления: Загрузка...<br />
							Прогноз: Загрузка...
			</p>';
}

class WebPage
{
	public $ID;
	public $Type;
	public $SubType;
	public $Name;
	public $Value;
	public $link;
	
	public function __construct($ID, $Type, $SubType, $Name, $Value, $link) {
       $this->ID = $ID;
       $this->Type = $Type;
       $this->SubType = $SubType;
       $this->Name = $Name;
       $this->Value = $Value;       
       $this->link = $link;
    }
}

class WebPageCollection
{
	public $WebPage;
	//public $link;
	
	public function WebPages(){
		return $this->WebPage;
	}
	
	public function SortType($type){
		return new WebPageCollection(array_filter($this->WebPage, function($v, $k) use($type) : string {
			return $v->Type == $type;
		}, ARRAY_FILTER_USE_BOTH));
	}
	
	public function SortSubType($type){
		return new WebPageCollection(array_filter($this->WebPage, function($v, $k) use($type) : string {
			return $v->SubType == $type;
		}, ARRAY_FILTER_USE_BOTH));
	}
	
	public function __construct($WebPage) {
       $this->WebPage = $WebPage;       
    }
}
?>