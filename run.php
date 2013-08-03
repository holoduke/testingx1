<?php 

include('scoreParser.php');

//print_r($config['football']['tables']['countries']);
//generate tables for each country and leagues in config

function run($config){

	foreach ($config['football']['tables']['countries'] as $country => $leagues){
		
		echo "Going to create data for ".$country."\n\n";
		
		$leaguesData = Array();
		
		foreach ($leagues['leagues'] as $league => $options){
			
			$leagueData = new stdclass();
			$leagueData->name = $league;
			$leagueData->years = Array();
			$leagueData->defaultYear = $options['defaultYear'];
			$leagueData->schedule = str_replace(' ', '_', ("schedule/schedule_".$country."_".$league.".json"));
			
			//generate tables for each year
			foreach ($options['years'] as $year => $url){
	
				$leagueYearData = new stdclass();
				$leagueYearData->year = $year;
				$leagueYearData->table = str_replace(' ', '_', ('table/table_'.$year.'_'.$country."_".$league.".json"));  			
				$leagueData->years[] = $leagueYearData;
				
				$result = Score::getScores(new footballTableParser($url));
				$result = str_replace('null','0',$result);
				echo "Created table ".$year." results for league ".$leagueData->name."\n";
				
				file_put_contents($leagueYearData->table, $result);
				sleep(5);
			}
			if (isset($options['schedule'])){
				$result = Score::getScores(new footballScheduleParser($options['schedule']));
				$result = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $result);
				file_put_contents($leagueData->schedule, $result);
				echo "create schedule for league ".$leagueData->name."\n";
			}
	
			//create leagues overview
			$leaguesData[] = $leagueData;
			$file = str_replace(' ', '_', ('leagues_'.$country.".json"));
			file_put_contents('league/'.$file, JSON_encode($leaguesData));
			sleep(rand(2, 10));
		}
		
		echo "-----------------------------------------\n\n";
		//every
	}
}

$i =1;
while(true){
	if ($i%60 == 0){
		proxyList::fetchProxies();
		proxyList::storeToFile();
	}
	run($config);
	sleep(rand(50, 70));
	$i++;	
}



