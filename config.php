<?php
/**
 main configuration
 */

function getScoreWayTableLink($id){
	
	return str_replace('{id}',$id,"http://www.scoresway.com/b/block.competition_table?tabletype=total&round_id={id}&sport=soccer&localization_id=www");
	
}

function getScoreWayScheduleLink($id){

	return str_replace('{id}',$id,"http://www.scoresway.com/b/block.competition_matches?coverage=gameweek&round_id={id}&sport=soccer&localization_id=www");

}

$defaultYear = "2013";
$config = array(

		"football" =>
		array("tables" =>
				array("countries" =>
						array("Italy" =>
								array("leagues" =>
										array(
												"Serie A" => array(
														"years" => array(
																"2013" =>getScoreWayTableLink(18219)
														)
														,"defaultYear" => $defaultYear
														,"schedule" => getScoreWayScheduleLink(21388)
												),

												"Serie B" => array(
														"years" => array(
																"2013" => getScoreWayTableLink(18411)
																
														)
														,"defaultYear" => $defaultYear
														,"schedule" => getScoreWayScheduleLink(18411)
												)
										)
								),
								"Netherlands" =>
								array("leagues" =>
										array(
												"Eerste Divisie" => array(
														"years" => array(
																"2013" => getScoreWayTableLink(21384)
																
														)
														,"defaultYear" => $defaultYear
														,"schedule" => getScoreWayScheduleLink(21384)
												),

												"Ere Divisie" => array(
														"years" => array(
																"2013" => getScoreWayTableLink(21387)
																
														)
														,"defaultYear" => $defaultYear
														,"schedule" => getScoreWayScheduleLink(21387)
												),
												"Top Klasse zaterdag" => array(
														"years" => array(
																"2013" => getScoreWayTableLink(21769)
																
														)
														,"defaultYear" => $defaultYear
														,"schedule" => getScoreWayScheduleLink(21769)
												),
												"Top Klasse zondag" => array(
														"years" => array(
																"2013" => getScoreWayTableLink(21768)
												
														)
														,"defaultYear" => $defaultYear
														,"schedule" => getScoreWayScheduleLink(21768)
												)
										)
								)
						)
				)
		)
);