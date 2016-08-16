<?php
// hier werden die aufstellungen geprueft und die aenderungen gespeichert.

require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

global $option;



if ($_POST['action'] == "printuserteam"){
	include('../_kabine/lib_kabine.php');
	$round = $_POST['round'];
	if (!$round){
		$round = 0;
	}
	$year = $_POST['year'];
	if (!$year){
		$year = $option['currentyear'];
	}
	$uliID = $_POST['uliID'];


	$formation= get_userformation($round, $uliID, $year);
	read_styles_ajax($formation);

	echo '<div style="margin-top: 5px;margin-left: 5px;position: relative;">';
	echo "\n";
	echo '<div id ="spielfeld">';
	echo "\n";

	print_slots($formation);
	echo fill_slots($formation, $uliID, $round, $year);
	echo "\n";
	echo '</div>';
	echo '</div>';

	// Jetzt noch die Punkte hindengeln
	if ($round != 0){
		$team = get_userteam($uliID, $year, $round);
		if ($team){
			echo '<script>';
			foreach($team as $player){
				if ($player['number'] == 15){
					echo '$("#'.$player['playerID'].' .position").html("'.($player['points']*2).'");';
				}
				else {
					echo '$("#'.$player['playerID'].' .position").html("'.$player['points'].'");';
				}
			}
			echo '</script>';
		}

	}



}


?>