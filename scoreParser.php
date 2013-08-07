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
		
		try{
		$html = file_get_html($this->url,false,proxyList::getContext());
		}
		catch (Exception $e){
		
		}
		
		if (!$html){
			return false;
		}
		
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
		
		try{
		$html = file_get_html($this->url);
		}	
		catch (Exception $e){
			
		}
		
		if (!$html){
			return false;
		}
		
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
	protected $imageDir;
	
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
			"points"=>10
	);
	
	public function __construct($url,$imageDir){
		$this->url = $url;
		$this->imageDir = $imageDir;
	}
	
	public function parse(){
		
		try{
			$html = file_get_html($this->url,false,proxyList::getContext());
		}
		catch (Exception $e){
		
		}
		
		if (!$html){
			Proxylist::notifyBrokenProxy();
			return $this->parse();
		}
		
		$teams = Array();
		
		$trs = $html->find('tr');
		
		//echo $html->outertext;
		//echo "\n";
		foreach ($trs as $key => $el){
				
			$row = new stdclass();
				
			foreach ($this->layoutDefinition as $name => $place){
				//echo $place.'=['.trim($el->find('td',$place)->outertext)."]\n";
				$row->$name = trim($el->find('td',$place)->plaintext);			
			}
			
			//get team id and fetch image
			$tid = $el->find('td',2);
			
			$urlString = explode("=",$tid->children[0].outertext);
			$teamid=explode("\"",$urlString[4]);
			$teamid = $teamid[0];
			
			$imageUrl = "http://cache.images.globalsportsmedia.com/soccer/teams/150x150/".$teamid.".png";
			
			$this->storeImage($imageUrl,$this->imageDir.'/'.str_replace(" ",'_',$row->team).".png");
			echo '['.$teamid.']---------------------------';
			//$href = $tid->find('a');
			//echo $href->href;
			usleep(100);
			
				
			$teams[] = $row;
		}
		$result = $teams;
		
		
		//if no result probably proxy is down, try again (with different proxy)
		if (!$result){
			Proxylist::notifyBrokenProxy();
			return $this->parse();
		}
		
		//remove first row. it doesnt contain info
		array_shift($result);
		return $result;
	}	
	
	public function storeImage($imageUrl,$target){
		echo "target ".$target."\n";
		
		$file = file_get_contents($imageUrl, false,proxyList::getContext());
		$src = imagecreatefromstring($file);
		imagealphablending($src, true);
		
		$width = imagesx($src);
		$height = imagesy($src);
			
		$img = imagecreatetruecolor(128,128);
		//imagealphablending($img, false);
		//imagesavealpha($img, true);
		
		imagecolortransparent($img, imagecolorallocatealpha($img, 0, 0, 0, 127));
		imagealphablending($img, false);
		imagesavealpha($img, true);
		
		imagecopyresampled($img,$src,0,0,0,0,128,128,$width,$height);
		mkdir( $this->imageDir."/");
		imagepng($img, $target);
		
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

		//if no result probably proxy is down, try again (with different proxy)
		if (!$result){
			return $this->parse();
		}
		
		
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