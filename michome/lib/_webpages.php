<?php

function _GetWebPagesFromType($link, $typePage){
	$results = mysqli_query($link, "SELECT * FROM WebPages WHERE `Type` = '$typePage'");
	$datas = [];
	while($row = $results->fetch_assoc()) {
        $datas[] = new WebPage($row['ID'], $row['Type'], $row['SubType'], $row['Name'], $row['Value'], $link);
    }
	return new WebPageCollection($datas);
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