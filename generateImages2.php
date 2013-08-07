<?php 
include('phpdom.php');
include('approximate-search.php');
include('proxylist.php');

$dir = 'table/';

proxyList::restoreFromFile();

echo "go\n";

function getResult($url){
	try{
		$html = file_get_html($url,false,proxyList::getContext());
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
    
    
    //echo $language;
    
    //return;
    foreach ($data as $object){
    	//print_r($object);

    	if (file_exists(str_replace(' ', '_',"images/".$leaguedir."/".trim($object->team).".png"))){
    		echo "file for ".$object->team. " already exist\n";
    		continue;
    	}
    	
    	
    	//return;
    	proxyList::getRandomProxy();
    	echo $object->team."->".$language."\n";
    	//print_r($object);
    	
    	
    	$result = getResult("http://www.footballdatabase.com/search.php?q=".$object->team);
    	$trs = $result->find('table table table tr');
    	
    	foreach ($trs as $tr){
    		
    		if (strripos(trim($tr->plaintext), trim($language)) !==false){
    			echo "found\n\n";
    			//get image
    			$tds = $tr->find('img');
    			
    			
    			$file = file_get_contents("http://www.footballdatabase.com/".$tds[0]->src);
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
    			
    			
    			print_r($tds[0]->src);
    			echo "\n";
    			
    		}
    	}
    	
    	usleep(300);
   
    }
   // break;
    
}

?>