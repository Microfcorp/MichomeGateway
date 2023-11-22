<?php
define('MAX_FILE_SIZE', 300000000);
require_once(__DIR__."/../../site/simple_html_dom.php");
class Foreca
{  
    public $html;
    
    public $location = ["",""];
    
    // объявление метода
    public function __construct($country, $city) {
        
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, "https://www.foreca.ru/".$country."/".$city."?quick_units=metric&tf=12h&lang=ru/");
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

       $m = @curl_exec($ch);
       curl_close($ch);
        
       $this->html = new simple_html_dom();
       $this->html->load(str_get_html($m));
       $this->location[0] = $country;
       $this->location[1] = $city;
    }
    
    public function CurrentTemperature() {
		//var_dump($this->html->find('div[id=obs-container]')[0]);
       return preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=obs-container]')[0]->find('div[class=header]')[0]->find('div[class=tf]')[0]->find('p[class=t]')[0]->find('span[class=value temp temp_c warm]')[0]);
    }   
    
    public function Feeling() {
       return preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[0]);
    } 
    
    public function Pressure() {
       return round(preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[1])/1.334-10, 2);
    } 
    
    public function DewPoint() {
       return preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[2]);
    } 
    
    public function Humidity() {
       return preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[3]);
    } 
    
    public function Visibility() {
       return preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[4]);
    } 
    
    public function Rising() {
       return preg_replace("/[^-0-9:]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[5]);
    } 
    
    public function Sunset() {
       return preg_replace("/[^-0-9:]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[6]);
    } 
    
    /*public function Longitude() {
       return strval($this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=right txt-tight]')[0]->find('strong')[7]->innertext);
    }*/ 
    
    public function WindSpeed() {       
       $data = $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=left]')[0]->find('strong');
       
       if(count($data) > 1)
        return preg_replace("/[^-0-9.]/", '', $data[1]);
        else return preg_replace("/[^-0-9.]/", '', "0");
    }
    
    public function WindDeg() {
       $data = $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=left]')[0]->find('img');
	   
       if(count($data) > 1)
            return strval($this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c1]')[0]->find('div[class=left]')[0]->find('img')[0]->alt);
        else return strval("None");
    }
    
    public function Wind() {
       return new Wind($this->WindDeg(), $this->WindSpeed());
    }
    
    public function GetDates($data) {
        //2006-12-12 10:00:00.5
        $d = date_parse($data);
        return ($d['year'] < 10 ? '0'.$d['year'] : $d['year']).($d['month'] < 10 ? '0'.$d['month'] : $d['month']).($d['day'] < 10 ? '0'.$d['day'] : $d['day']);
    }
    
    public function GetPrognoz() {
        //https://www.foreca.ru/Russia/Ostrogozhsk?details=20190415
       $htm = new simple_html_dom();
       $htm->load_file("https://www.foreca.ru/".$this->location[0]."/".$this->location[1]."?tenday&quick_units=metric&tf=12h&lang=ru");
       
       $row1 = $htm->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[class=content-right]')[0]->find('div[class=table t_longfore]')[0]->find('div[class=row]')[0]->find('.c1 ]');
       $row2 = $htm->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[class=content-right]')[0]->find('div[class=table t_longfore]')[0]->find('div[class=row]')[1]->find('.c1 ]');
       
       return array_merge($row1, $row2);
       //return new Prognoz(preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c2]')[0]->find('div[class=c2_a]')[0]->find('a')[0]->find('span')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $this->html->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[id=webslice_content]')[0]->find('div[class=entry-content]')[0]->find('div[class=table t_cond]')[0]->find('div[class=c2]')[0]->find('div[class=c2_a]')[0]->find('a')[0]->find('span')[1]->find('strong')[0]));
    }
    
    public function GetNumPrognoz($id) {
        $pr = $this->GetPrognoz()[$id];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    
    public function GetAllPrognoz() {
        $pr = $this->GetPrognoz();
        
        $ret = [
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[0]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[0]->find('a')[0]->find('strong')[1]), $pr[0]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[0]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[0]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[0]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[1]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[1]->find('a')[0]->find('strong')[1]), $pr[1]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[1]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[1]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[1]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[2]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[2]->find('a')[0]->find('strong')[1]), $pr[2]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[2]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[2]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[2]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[3]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[3]->find('a')[0]->find('strong')[1]), $pr[3]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[3]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[3]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[3]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[4]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[4]->find('a')[0]->find('strong')[1]), $pr[4]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[4]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[4]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[4]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[5]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[5]->find('a')[0]->find('strong')[1]), $pr[5]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[5]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[5]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[5]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[6]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[6]->find('a')[0]->find('strong')[1]), $pr[6]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[6]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[6]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[6]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[7]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[7]->find('a')[0]->find('strong')[1]), $pr[7]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[7]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[7]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[7]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[8]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[8]->find('a')[0]->find('strong')[1]), $pr[8]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[8]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[8]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[8]->find('a')[0]->href), 
                new Prognoz(preg_replace("/[^-0-9.]/", '', $pr[9]->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr[9]->find('a')[0]->find('strong')[1]), $pr[9]->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr[9]->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr[9]->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr[9]->find('a')[0]->href)
            ];
       return $ret;
    }
    
    public function GetTodayPrognoz() {
        $pr = $this->GetPrognoz()[0];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetTomorrowPrognoz() {
        $pr = $this->GetPrognoz()[1];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetAfterTomorrowPrognoz() {
        $pr = $this->GetPrognoz()[2];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
     public function GetAfterThreePrognoz() {
        $pr = $this->GetPrognoz()[3];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetAfterFourPrognoz() {
        $pr = $this->GetPrognoz()[4];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetAfterFivePrognoz() {
        $pr = $this->GetPrognoz()[5];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetAfterSixPrognoz() {
        $pr = $this->GetPrognoz()[6];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetAfterSevenPrognoz() {
        $pr = $this->GetPrognoz()[7];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetAfterEightPrognoz() {
        $pr = $this->GetPrognoz()[8];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
    public function GetAfterNinePrognoz() {
        $pr = $this->GetPrognoz()[9];
       return new Prognoz(preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[0]), preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('strong')[1]), $pr->find('a')[0]->find('span')[0]->innertext, preg_replace("/[^-0-9.]/", '', $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('strong')[0]), $pr->find('a')[0]->find('span')[1]->find('span')[0]->find('img')[0]->alt, $pr->find('a')[0]->href);
    }
}
class Prognoz
    {
        public $TDay;
        public $TNight;
        public $Text;
        public $Wind;
        public $Date;
        public $TextDate;
        public function __construct($TDay, $TNight, $Text, $Degree, $Speed, $Date) {
           $this->TDay = $TDay;
           $this->TNight = $TNight;
           $this->Text = $Text;
           $this->Wind = new Wind($Speed, $Degree);
           $this->Date = explode('=', $Date)[1];
           $this->TextDate = $Date;
        }
		public function GetHourlyPrognoz(){
			return new PrognozDay($this->TextDate, $this->Date);
		}
    }
class PrognozDay
    {
        public $PrognozHours = [];
        public $Date;
        public $link;
        public function __construct($link, $Date) {
           $this->link = $link;
           $this->Date = $Date;
           $this->Load();
        }
		
		public function Load() {
            //https://www.foreca.ru/Russia/Ostrogozhsk?details=20190415
			$htm = new simple_html_dom();
			$htm->load_file("https://www.foreca.ru/".$this->link."&quick_units=metric&tf=24h&lang=ru");

			$row = $htm->find('div[class|=row]');

			//print_r($htm->find('div[id=wrap]')[0]->find('div[id=pagewrapper]')[0]->find('div[class=content]')[0]->find('div[class=content_2col]')[0]->find('div[class=content-right]')[0]->innerText());
			
			echo count($row);
			echo ($row[3]->innertext);
			for($i = 1; $i < count($row); $i++){
				//print_r($row);
				echo $row[$i]->children(0)->innertext;
				/*$ar = $row[$i]->find('div');
				echo ($ar[0]->find('strong')[0]);
				$hour = $row[$i]->find('div[class=c0]')[0]->find('strong')[0];
				$text = $row[$i]->find('div[class=c1]')[0]->find('div')[0]->alt;
				echo $row[$i]->innertext();
				$temp = $row[$i]->find('div')[2]->find('span[class=warm]')[0]->find('strong')[0];
				$wind = new Wind($row[$i]->find('div[class=c2]')[0]->find('img')[0]->alt, $row[$i]->find('div[class=c2]')[0]->find('strong')[0]);
				$feel = $row[$i]->find('div[class=c3]')[0]->find('strong')[0];
				$rain = $row[$i]->find('div[class=c3]')[0]->find('strong')[1];
				$humm = $row[$i]->find('div[class=c3]')[0]->find('strong')[2];
				
				$PrognozHours[] = new PrognozHour($hour, '', $wind, $text, $temp, $feel, $rain, $humm, $this->Date);*/
				break;
			}
		}
    }
class PrognozHour
    {
        public $Hour;
        public $Icon;
        public $Wind;
        public $Text;
        public $Temperature;
        public $Feeling;
        public $KRain;
        public $Humm;
        public $Date;
        public function __construct($Hour, $Icon, $Wind, $Text, $Temperature, $Feeling, $KRain, $Humm, $Date) {
           $this->Hour = $Hour;
           $this->Icon = $Icon;
           $this->Wind = $Wind;
           $this->Text = $Text;
           $this->Temperature = $Temperature;
           $this->Feeling = $Feeling;
           $this->KRain = $KRain;
           $this->Humm = $Humm;
           $this->Date = $Date;
        }
    }
class Wind
    {
        public $Degree;
        public $Speed;
        public function __construct($Degree, $Speed) {
           $this->Degree = $Degree;
           $this->Speed = $Speed;
        }
    }
?>
