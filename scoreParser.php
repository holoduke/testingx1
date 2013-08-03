<?php
include('phpdom.php');
include('config.php');
include('proxylist.php');



//fetch proxies from file
if (!proxyList::restoreFromFile()){
	proxyList::fetchProxies();
	proxyList::storeToFile();
}


class Parser{	
}

class scoreswayTableParser extends Parser{
		
	public function parse($definition){
		
		$html = file_get_html($this->url,false,proxyList::getContext());
		
		$teams = Array();
		
		$trs = $html->find('tr');
		
		foreach ($trs as $key => $el){
			
			$row = new stdclass();
			
			foreach ($definition as $name => $place){
				$row->$name = trim($el->find('td',$place)->plaintext);
			}
			
			$teams[] = $row;
		}
		return $teams;
	}
}

class scoreswayScheduleParser extends Parser{

	public function parse($definition){
		$html = file_get_html($this->url);

		$teams = Array();

		$trs = $html->find('tr');

		foreach ($trs as $key => $el){

			$row = new stdclass();
			
 			foreach ($definition as $name => $place){

 				$row->$name = trim($el->find('td',$place)->plaintext);
 			}
				
			$teams[] = $row;
		}
		
		return $teams;
	}
}

class footballTableParser extends scoreswayTableParser{
	
	protected $url;
	
	private $layoutDefinition = Array(
			"rank"=>0, 
			"team"=>2,	
			"matchPoints"=>3,
			"totalWon"=>4,
			"totalDraw"=>5,
			"totalLost"=>6,
			"totalGoalsFor"=>7,
			"totalGoalsAgainst"=>8,
			"goalDifference"=>9,
			"points"=>10,
			"matchPoints"=>11,
			"matchPoints"=>12,
			"matchPoints"=>13,
			"matchPoints"=>14,
	);
	
	public function __construct($url){
		$this->url = $url;
	}
	
	public function parse(){
		$result = parent::parse($this->layoutDefinition);
		
		//remove first row. it doesnt contain info
		array_shift($result);
		return $result;
	}	
}

class footballScheduleParser extends scoreswayScheduleParser{
	
	protected $url;
	
	private $layoutDefinition = Array(
			"day"=>0, 
			"date"=>1,
			"home"=>2,
			"score-time"=>3,
			"away"=>4,
	);
	
	public function __construct($url){
		$this->url = $url;
	}
	
	public function parse(){
		$result = parent::parse($this->layoutDefinition);
		
		//remove first row. it doesnt contain info
		array_shift($result);
		return $result;
	}	
}

class Score{

	public static function getScores($parser){
		
		return $scores = json_encode($parser->parse());
	}
}






//print_r($result);

// $html = file_get_html($url);

// $trs = $html->find('tr');

// foreach ($trs as $key => $el){
// 	echo "-<br>";
// 	foreach ($el->find('td') as $td){
// 		echo $td->plaintext."---";
// 	}
// }