<?php 
include('phpdom.php');
include('approximate-search.php');
include('proxylist.php');

$dir = 'table/';

proxyList::restoreFromFile();


function getResult($url){
	try{
		$html = file_get_html($url,false,proxyList::getContext("Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3"));
	}
	catch (Exception $e){
	
	}
	
	if (!$html){
		return getResult($url);
	}
	
	return $html;
}

foreach (new DirectoryIterator($dir) as $fileInfo) {
    if($fileInfo->isDot()) continue;
    //echo $fileInfo->getFilename();
    
   
	$str2 = explode("_", $fileInfo->getFilename());
	$language = $str2[2];

	$temp = explode(".",$fileInfo->getFilename());
	$temp = $temp[0];
	$temp = explode("_",$temp);
	array_shift($temp);
	array_shift($temp);
	$leaguedir = implode("_",$temp);
	//print_r($dirname);
	
    $content = file_get_contents($dir.$fileInfo->getFilename());
    
    $data = json_decode($content);
    
        
    //return;
    foreach ($data as $object){
    	//print_r($object);

    	if (file_exists(str_replace(' ', '_',"images/".$leaguedir."/".trim($object->team).".png"))){
    		echo "file for ".$object->team." already exist\n"; 
    		//continue;
    	}
    	
    	//echo "ik besta niet\n";
    	//continue;
    	
    	//return;
    	//proxyList::getRandomProxy();
    	//echo $object->team."->".$language."\n";
    	
    	
    	//continue;
    	$search = urlencode("voetbal+".$object->team."+club+logo");
    	
    	echo $search."\n";
    	//continue;
    	$result = file_get_contents("https://www.googleapis.com/customsearch/v1?key=AIzaSyCwzYmCLF1_eipM6W1b2iaeKDXPhvVXZrk&q=".$search."&fileType=png&searchType=image&cx=018256920734818117687:itc5imjcr-q");
    	echo '.';
    	$json = json_decode($result);
    	
    	
    	
    	foreach ($json->items as $i){
    		print_r($i->link);
    		echo "\n\n";

    		
    		$file = file_get_contents($i->link);
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
    		mkdir( "images/".$leaguedir."/");
    		imagepng($img, str_replace(' ', '_',"images/".$leaguedir."/".trim($object->team).".png"));
    		 
    		 
    		
    		
    		
    		
    		
    		
    		break;
    	}
    	usleep(500);
   
    }
   // break;
    
}

?>