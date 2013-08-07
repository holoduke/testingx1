<?php 

include('scoreParser.php');

//print_r($config['football']['tables']['countries']);
//generate tables for each country and leagues in config
//proxyList::fetchProxies();
//proxyList::storeToFile();
function run($config){

	foreach ($config['football']['tables']['countries'] as $country => $leagues){
		
		echo "Create data for ".$country."\n";
		
		$leaguesData = Array();
		
		foreach ($leagues['leagues'] as $league => $options){
			
			echo "Create data for league ".$league."\n";
			
			$leagueData = new stdclass();
			$leagueData->name = $league;
			$leagueData->country = $country;
			$leagueData->years = Array();
			$leagueData->defaultYear = $options['defaultYear'];
			//$leagueData->images = "images/".$country."_".$league."/";
			$leagueData->imageDir = str_replace(' ', '_',"images/".$country."_".$league."/");
			$leagueData->schedule = str_replace(' ', '_', ("schedule/schedule_".$country."_".$league.".json"));
			
			//generate tables for each year
			foreach ($options['years'] as $year => $url){
	
				echo "Create table data for year ".$year."\n";
				
				$leagueYearData = new stdclass();
				$leagueYearData->year = $year;
				
				$leagueYearData->table = str_replace(' ', '_', ('table/table_'.$year.'_'.$country."_".$league.".json"));  			
				$leagueData->years[] = $leagueYearData;
				
				$result = Score::getScores(new footballTableParser($url,$leagueData->imageDir));
				$result = str_replace('null','0',$result);
								
				//print_r($leagueData);
				file_put_contents($leagueYearData->table, $result);
				sleep(rand(2,4));
			}
			
			echo "Create schedule data \n";
			if (isset($options['schedule'])){
				$result = Score::getScores(new footballScheduleParser($options['schedule']));
				$result = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $result);
				file_put_contents($leagueData->schedule, $result);
				echo "create schedule for league ".$leagueData->name."\n";
			}
			
			$leaguesData[] = $leagueData;
		}
		
		echo "Store league list file\n";
	
		$file = str_replace(' ', '_', ('leagues_'.$country.".json"));
		file_put_contents('league/'.$file, JSON_encode($leaguesData));
		sleep(rand(2, 1));
		
		echo "-----------------------------------------\n\n";
		//every
	}
}

$i =1;
while(true){
	if ($i%10 == 0){
		proxyList::fetchProxies();
		proxyList::storeToFile();
	}
	run($config);
	sleep(rand(60, 80));
	$i++;	
}



