<?php 
include('phpdom.php');
include('approximate-search.php');

$url = 'http://www.voetballogos.nl/nederland.html#EREDIVISIE';
$baseUrl = "http://www.voetballogos.nl/";
$source1 = file_get_html('http://www.voetballogos.nl/nederland.html#EREDIVISIE');


$pos = strpos(strtolower("AJAX&nbsp"), strtolower("Ajax"));


$target = "ajax";

$search = "ajax";

$result = levenshtein($target,$search);

echo $result;
//$search = new Approximate_Search( $target, 1);

//$matches = $search->search( $search );


//print_r($result);
//print_r($source1->plaintext);
//search in schedules

$dir = 'schedule/';

$result = $source1->find('table table table');


$i = 0;
foreach (new DirectoryIterator($dir) as $fileInfo) {
    if($fileInfo->isDot()) continue;
    //echo $fileInfo->getFilename();
    
   
	$str2 = explode("_", $fileInfo->getFilename());
	array_shift($str2);
	$leaguedir = implode("_", $str2);
	$str2 = explode(".",$leaguedir);
	$leaguedir = $str2[0];
	
	
    $content = file_get_contents($dir.$fileInfo->getFilename());
    
    $data = json_decode($content);
    
    
    foreach ($data as $object){
    	//print_r($object);

    	
    	
    	foreach ($result as $table){
    		

    		$tableResult = $table->find('tr');
    		
	    	echo $object->home."  ".$object->away."\n";
	    	//loop through html
	    	foreach($tableResult as $tr){
	    	
	    		
	    		
	    		foreach ($tr->find('td') as $td){
	    	
	    			$p = $td->find('p');
	    			$b = $td->find('b');
	    			//print_r($p[0]->plaintext)."\n";
	    			//print_r($p[1]->plaintext);
	    			//echo $td->plaintext;
	    			$image = $td->find('img');
	    			$image = $image[0]->src;
	    	
	    			$pos = strripos(trim($p[0]->plaintext), trim($object->home));
	    			if ($pos === false){
	    				$pos = strripos(trim($p[1]->plaintext), trim($object->home));
	    			}
	    			if ($pos === false){
	    				$pos = strripos(trim($b[0]->plaintext), trim($object->home));
	    			}
	    			
	    			if ($pos !== false)
	    			{
	    			echo "found found found\n";
	    			echo "saarch for ".$object->away." or ".$object->home."\n";
	    			echo "text 1".$p[0]->plaintext."\n";
	    			echo "text 2".$p[1]->plaintext."\n";
	    			echo "text 3".$b[0]->plaintext."\n";
	    			echo "image ".$image;
	    			
	    			echo "[".strripos($image,'http')."]!";
	    			if (strripos($image,'http') !== false){
	    				echo "regular";
	    				$file = file_get_contents($image);
	    			}
	    			else{
	    				$file = file_get_contents($baseUrl.$image);
	    			}
	    			
	    			$file = file_get_contents($baseUrl.$image);
	    			$src = imagecreatefromstring($file);
	    			$width = imagesx($src);
	    			$height = imagesy($src);
	    			
	    			$img = imagecreatetruecolor(64,64);
	    			imagecopyresized($img,$src,0,0,0,0,64,64,$width,$height);
	    			
	    			mkdir( "images/".$leaguedir."/");
	    			imagejpeg($img, "images/".$leaguedir."/".trim($object->home).".png");
	    			//file_put_contents("images/".$object->home."jpg","test");
	    			echo "\n------------------------\n\n\n";
	    			}
	    			
	    			
	
	    			$image = $td->find('img');
	    			$image = $image[0]->src;
	    			
	    			$pos = strripos(trim($p[0]->plaintext), trim($object->away));
	    			if ($pos === false){
	    				$pos = strripos(trim($p[1]->plaintext), trim($object->away));
	    			}
	    			if ($pos === false){
	    				$pos = strripos(trim($b[0]->plaintext), trim($object->away));
	    			}
	    			
	    			if ($pos !== false)
	    			{
	    				echo "found found found\n";
	    				echo "saarch for ".$object->away." or ".$object->home."\n";
	    				echo "text 1".$p[0]->plaintext."\n";
	    				echo "text 2".$p[1]->plaintext."\n";
	    				echo "text 3".$b[0]->plaintext."\n";
	    				echo "image ".$image;
	
	    				echo "[".strripos($image,'http')."]!";
	    				if (strripos($image,'http') !== false){
	    					echo "regular";
	    					$file = file_get_contents($image);
	    				}
	    				else{
	    					$file = file_get_contents($baseUrl.$image);
	    				}
	    				$src = imagecreatefromstring($file);
	    				$width = imagesx($src);
	    				$height = imagesy($src);
	    					
	    				$img = imagecreatetruecolor(128,128);
	    				imagecopyresized($img,$src,0,0,0,0,128,128,$width,$height);
	    				mkdir( "images/".$leaguedir."/");
	    				imagejpeg($img, str_replace(' ', '_',"images/".$leaguedir."/".trim($object->away).".png"));
	    				//file_put_contents("images/".$object->home."jpg","test");
	    				echo "\n------------------------\n\n\n";
	    			}
	    			
	    			usleep(100);
	    			//echo $tr->outertext;
	    		}
	    	}
    	}
   
    }
   // break;
    
}

?>