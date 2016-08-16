<?php
/*
 * Created on 23.03.2009
 *
 */

include('lang_admin.php');
include('../_transfermarkt/lib_transfermarkt.php');
include('../_stadion/lib_stadion.php');
include('../_finanzen/lib_sponsoring.php');




/*
 * create uli games table
 *
 */
/*
global $wpdb;

$ulis = get_ulis(1);
$sql = 'SELECT * FROM '.$wpdb->prefix.'pl_teams '.
		'WHERE competition_id = '.$option['currentcompetition'].' ';
$teams = $wpdb->get_results($sql, ARRAY_A);

$x = 0;
foreach ($ulis as $uli){

	$thisulis[$teams[$x]['id']] = $uli['ID'];
	echo $uli['uliname'].' - '.$teams[$x]['team_name'].'<br/>';
	$x++;
}

$sql = 'SELECT * FROM '.$wpdb->prefix.'pl_games '.
		'WHERE competition_id = '.$option['currentcompetition'].' ';
$games = $wpdb->get_results($sql, ARRAY_A);

foreach ($games as $game){
	$values = array();
	$values[] = array("col" => "team1", "value" => $thisulis[$game['team1']]);
	$values[] = array("col" => "team2", "value" => $thisulis[$game['team2']]);
	$values[] = array("col" => "round", "value" => $game['round']);
	$values[] = array("col" => "leagueID", "value" => 1);
	$values[] = array("col" => "year", "value" => $option['currentyear']);
	//uli_insert_record('games', $values);

}
*/


function calculate_uli_games($year, $leagueID = 1, $round) {
	global $option, $wpdb;


	$cond[] = array("col" => "year", "value" => $year);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "round", "value" => $round);
	$result = uli_get_results('games', $cond);


	unset($cond);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "year", "value" => $year);
	$cond[] = array("col" => "round", "value" => $round);
	$order[] = array("col" => "score", "sort" => "DESC");
	$scores = uli_get_results('results', $cond, NULL, $order);
	if ($scores){
		foreach ($scores as $score){
			$thisscore[$score['uliID']] = $score['score'];
		}
	}

	if ($result){
		foreach ($result as $game){
			$team1points = $thisscore[$game['team1']];
			$team2points = $thisscore[$game['team2']];

			$team1score = 0;
			if ($team1points >= 9){$team1score = 1;}
			if ($team1points >= 19){$team1score = 2;}
			if ($team1points >= 29){$team1score = 3;}
			if ($team1points >= 39){$team1score = 4;}
			if ($team1points >= 49){$team1score = 5;}
			if ($team1points >= 59){$team1score = 6;}

			$team2score = 0;
			if ($team2points >= 13){$team2score = 1;}
			if ($team2points >= 23){$team2score = 2;}
			if ($team2points >= 33){$team2score = 3;}
			if ($team2points >= 43){$team2score = 4;}
			if ($team2points >= 53){$team2score = 5;}
			if ($team2points >= 63){$team2score = 6;}

			if ($team1points < 0){$team2score = $team2score + 1;}
			if ($team2points < 0){$team1score = $team1score + 1;}

			unset($cond);
			$cond[] = array("col" => "ID", "value" => $game['ID']);
			unset($values);
			$values[] = array("col" => "team1score", "value" => $team1score);
			$values[] = array("col" => "team2score", "value" => $team2score);
			uli_update_record('games', $cond, $values);
		}
	}
//$CONFIG->prefix = "tip_";
$sql = 	'SELECT * FROM tip_uli '.
		'WHERE leagueID = "'.$leagueID.'"';
$klub = $wpdb->get_results($sql,ARRAY_A);
foreach ($klub as $klub) {
	$team[$klub['ID']]->g_scored = 0;
	$team[$klub['ID']]->g_against = 0;
	$team[$klub['ID']]->points = 0;
	}

for ($x = 1; $x<=$round; $x++) {
	$sql = 	'SELECT * FROM tip_uli_games '.
		'WHERE round = "'.$x.
		'" AND year = "'.$year.'" AND leagueID = '.$leagueID.' ';
	$result = $wpdb->get_results($sql,ARRAY_A);
	if(!empty($result))
			{foreach ($result as $game) {
			$team[$game['team1']]->g_scored  = $team[$game['team1']]->g_scored + $game['team1score'];
			$team[$game['team1']]->g_against = $team[$game['team1']]->g_against + $game['team2score'];

			$team[$game['team2']]->g_scored  = $team[$game['team2']]->g_scored + $game['team2score'];
			$team[$game['team2']]->g_against = $team[$game['team2']]->g_against + $game['team1score'];

			if ($game['team1score'] == $game['team2score']) {
				$team[$game['team1']]->points = $team[$game['team1']]->points + 1;
				$team[$game['team2']]->points = $team[$game['team2']]->points + 1;
				}
			if ($game['team1score'] > $game['team2score']) {
				$team[$game['team1']]->points = $team[$game['team1']]->points + 3;
				}
			if ($game['team1score'] < $game['team2score']) {
				$team[$game['team2']]->points = $team[$game['team2']]->points + 3;
				}
			}}
		}
	$sql  = 'DELETE FROM tip_uli_games_table '.
			' WHERE leagueID = '.$leagueID.'';
	if ($wpdb->query($sql)){}

	foreach ($team as $key => $team) {
	$team->g_diff = $team->g_scored - $team->g_against;

	$sql  = 'INSERT INTO tip_uli_games_table '.
			'(`uliID` , `g_scored` , `g_against` , `g_diff` , `points` , `games` , `position`, `leagueID`, `year` ) VALUES ('.
			'"'.$key.'",'.
			'"'.$team->g_scored.'",'.
			'"'.$team->g_against.'",'.
			'"'.$team->g_diff.'",'.
			'"'.$team->points.'",'.
			'"'.$round.'",'.
			'"'.$team->position.'", '.
			'"'.$leagueID.'", '.
			'"'.$year.'" '.

			')';
	if ($wpdb->query($sql)){}
	}

}




function calculate_smile($round){
	global $option;

	$leagues = get_leagues();
	if ($leagues){
		foreach ($leagues as $league){

			$cond = array();
			$cond[] = array("col" => "round", "value" => $round);
			$cond[] = array("col" => "year", "value" => $option['currentyear']);
			$order[] = array("col" => "playerID", "sort" => "ASC");
			$players = uli_get_results("player_points", $cond, NULL, $order);
			$order[] = array("col" => "number", "sort" => "ASC");
			$userplayer = uli_get_results("userteams", $cond, NULL, $order);

			if ($userplayer){
				foreach ($userplayer as $thisplayer){
					$thisuserplayer[$thisplayer['playerID']] = $thisplayer;
				}
			}

			if ($players){
				foreach ($players as $player){
					$score = $player['score'];
					$number = $thisuserplayer[$player['playerID']]['number'];
					$points = $thisuserplayer[$player['playerID']]['points'];
					if ($score > 5){
						if ($number == 15){
							$smile = rand(5, 15);
						}
						elseif ($number < 12 AND $number > 0){
							$smile = rand(1, 6);
						}
						elseif ($number > 11){
							$smile = rand(-10, 1);
						}
						else {
							$smile = rand(-15, -5);
						}
					}
					elseif ($score > 0){
						if ($number == 15){
							$smile = rand(1, 6);
						}
						elseif ($number < 12 AND $number > 0){
							$smile = rand(0, 3);
						}
						elseif ($number > 11){
							$smile = rand(-4, 2);
						}
						else {
							$smile = rand(-8, -2);
						}
					}
					else {
						if ($number == 15){
							$smile = rand(-10, -1);
						}
						elseif ($number < 12 AND $number > 0){
							$smile = rand(-5, -1);
						}
						elseif ($number > 11){
							$smile = rand(-2, 0);
						}
						else {
							$smile = rand(-3, 1);
						}
					}
					update_smile($player['playerID'], $league['ID'], $smile, NULL, $round, $option['currentyear']);
					$allsmiles = $allsmiles + $smile;
					echo $player['playerID'].' - '.$score.' - '.$points.' - '.$number.' - SMILE: '.$smile;
					echo '<br/>';
				}
			}
			echo 'Saldo: '.$allsmiles;
		}
	}



	//echo $round;



/*

if (!$leagueID){$leagueID=1;}
global $user_ID, $CONFIG, $wpdb;
unset($didheplay);
unset($playeruliID);
unset($smile);
unset($points);


+2 wenn gespielt und besser als 5
+1 wenn gespielt und besser 0
-1 wenn nicht gespielt und besser als 0
-2 wenn nicht gespielt und besser als 5
ACHTUNG nie mehr als einmal machen.



$sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_player WHERE team != 999 ';
$players = $wpdb->get_results($sql,ARRAY_A);
if ($players){
	foreach ($players as $player) {
	$points = get_player_points($player['ID'], $round, $year, $leagueID);

	echo $playeruliID = get_player_userteam($player['ID'], $leagueID);
	echo $didheplay = did_he_play($round, $player['ID'], $year, $playeruliID, $leagueID);

	//nur wenn spieler in einem kader+
	$smile = 0;
	if ($playeruliID)
		{
		if ($didheplay == 'S' OR $didheplay == 'C')
			{
			if ($points > 5){$smile = 2;}
			elseif($points > 0){$smile = 1;}
			elseif($points < 0){$smile = -1;}
			}
		if ($didheplay == 'B')
			{
			if ($points > 5){$smile = -3;}
			elseif($points > 0){$smile = 1;}
			}
		if ($didheplay == 'X')
			{
			if ($points > 5){$smile = -4;}
			elseif($points > 0){$smile = -2;}
			}
		}
	else {$smile = 0;}
	echo ''.$player['name'].': '.$points.' Smile: <b>'.$smile.'</b><br>';
	update_smile($smile, $player['ID'], $leagueID);

	}}

*/


}



/**
 *
 * schaut, welche auktionen, keine transferhistorie bekommen haben und repariert das
 *
 */
function repair_transfers(){
	$cond[] = array("col" => "history", "value" => "1");
	$cond[] = array("col" => "end", "value" => "1311552000", "func" => ">");
	$auctions = uli_get_results("auctions", $cond);

	if ($auctions){
		foreach($auctions as $auction){
			// Jetzt fuer jede Auktion checken, ob innerhalb der naechsten 48 Stunden ein Transfer geschrieben wurde
			unset($cond);
			$cond[] = array("col" => "playerID", "value" => $auction['playerID']);
			$cond[] = array("col" => "time", "value" => ($auction['end'] - 100), "func" => ">");
			$cond[] = array("col" => "time", "value" => $auction['end'] + (2*86400), "func" => "<");
			//$cond[] = array("col" => "ulinew", "value" => $auction['topbetuliID']);
			$transfer = uli_get_row("transfers", $cond);
			if (!$transfer){
				//print_R($auction);
				$player = get_player_infos($auction['playerID']);
				if ($auction['topbetuliID'] == $player['uliID']){
					echo 'Kein Transfer gefunden fuer: ';
					echo $player['name'].' '.$auction['topbetuliID'].' '.$player['uliID'].' --- '.uli_date($auction['end']);
					echo '<br/>';

					$transfer['time'] = $auction['end'];
					$transfer['ulinew'] = $auction['topbetuliID'];
					$transfer['sum'] = $auction['topbet'];
					$transfer['playerID'] = $auction['playerID'];
					$transfer['leagueID'] = $auction['leagueID'];
					$transfer['externold'] = 'Arbeitsamt (wahrscheinlich)';
					$transfer['type'] = 1;
					write_transfer_history($transfer);
				}
			}

		}
	}

}


/*
 * zapft transfermarkt.de an um die verletzten spieler zu uebertragen
 */
function check_verletzte(){
	$host = "http://www.transfermarkt.de/de/1-bundesliga/verletzt/wettbewerb_L1.html";
	$filestring = file_get_contents($host);
	$startpos = 0;
	while($pos = strpos($filestring, 'Derzeit im Aufbautraining', $startpos)){
		//  $string[] = substr($filestring, $pos, strpos($filestring, "<h2", $pos + 1) - $pos);
		$string[] = substr($filestring, $pos, strpos($filestring, '<div id="footer">', $pos + 1) - $pos);

		//echo $string."</br>";
		$startpos = $pos + 1;
	}
	if ($string){
	 foreach ($string as $string){
	 	//$string = str_replace("<img", "", $string);
	 	//echo $string;
	 	uli_delete_record("tm_string");
	 	$value[] = array("col" => "timestamp", "value" => mktime());
	 	$value[] = array("col" => "string", "value" => $string);
	 	uli_insert_record("tm_string", $value);
	 	//echo $result[] = $string;
	 }
	}
	$result = uli_get_results("tm_string");
	if ($result){
		foreach ($result as $string){
			$trs = explode('<tr class="', $string['string']);

			if ($trs){
				foreach ($trs as $tr){
					//echo $tr;
					$player = array();
					$tables = explode("<table>", $tr);
					if ($tables){
						foreach ($tables as $table){
							// weiterzerhacken
							$startpos = 0;
							$thisstring = array();
							while($pos = strpos($table, '<a href="', $startpos))
							{
								$thisstring[] = substr($table, $pos, strpos($table, '</a>', $pos + 1) - $pos);
								$startpos = $pos + 1;
							}
							if ($thisstring[0] AND $thisstring[1]){
								//echo $thisstring[0];
								$startpos = 0;
								while($pos = strpos($thisstring[0], 'title="', $startpos))
								{
									$player['name'] = substr($thisstring[0], $pos + 7, strpos($thisstring[0], '">', $pos + 1) - $pos);
									$player['name'] = str_replace('">', '', $player['name']);
									$startpos = $pos + 1;
								}
							}

							if ($player['name']){
								// Transfermarkt ID
								$thisstring = array();
								while($pos = strpos($table, 'spieler_', $startpos))
								{
									$player['tm_id'] = substr($table, $pos + 8, strpos($table, '.html', $pos + 1) - $pos - 8);
									$startpos = $pos + 1;
								}

								// Grund
								$thisstring = array();
								while($pos = strpos($table, 'al s10">', $startpos))
								{
									$thisstring[] = substr($table, $pos + 8, strpos($table, '</tr>', $pos + 1) - $pos);
									$startpos = $pos + 1;
								}

								if ($thisstring){
									foreach ($thisstring as $thisstring){

										$text = explode("</td>", $thisstring);
										if ($text){
											$player['cause'] = trim(strip_tags($text[0]));
											$player['since'] = trim(strip_tags($text[1]));
											$player['until'] = trim(strip_tags($text[2]));
										}
									}
								}
							}
							if ($player){
								$players[] = $player;
							}
						}
					}
				}
			}
		}
	}

	//print_r($players);

	if ($players){
		// tabelle leeren
		uli_delete_record("player_injured");
		foreach ($players as $player){
			// schauen ob der typ eindeutig identifiziert werden kann



			unset($values);
			$values[] = array("col" => "tm_id", "value" => $player['tm_id']);
			$uliplayer = uli_get_row("player", $values);

			//print_r($uliplayer);

			if ($uliplayer){
				unset($values);
				$values[] = array("col" => "playerID", "value" => $uliplayer['ID']);
				$values[] = array("col" => "timestamp", "value" => mktime());
				$values[] = array("col" => "cause", "value" => $player['cause'].', seit: '.$player['since'].', '.$player['until']);

				uli_insert_record("player_injured", $values);
				//unset($uliplayer);
				// print_r($result);
			}
			else {
				unset($values);
				$name = explode(" ", $player['name']);
				$nachname = trim($name[count($name)-1]);
				$values[] = array("col" => "name", "value" => $nachname, "func" => "LIKE");
				$result = uli_get_results("player", $values);
				//print_r($result);

				if ($result){
					foreach ($result as $uliplayer){
						echo '<a class = "tm_id" data-playerid = "'.$uliplayer['ID'].'" id = "'.$player['tm_id'].'" href="#">'.$player['name'].' - '.$uliplayer['name'].'</a>';
						echo '<br>';
					}
				}
				else {
					echo '<form class="form_tm_id" id = "'.$player['tm_id'].'">';
					echo $player['name'].' <input type = "text" id = "playerid-'.$player['tm_id'].'" name = "playerID"  size = "5">';
					echo '<input type = "submit" value = "Save"></form>';
					echo '<br>';
				}
			}
		}
	}
}



/**
 * die neue grosse berechnungsfunktion
 * mal schauen, ob wir das alles in einem abfackeln koennen
 */
function calculate_round($round){
	global $option;
	$year = $option['currentyear'];

	echo $round;

	// Als erstes der Check ob hier ne Runde uebergeben wurde
	if ($round < 1 OR $round > 35){echo 'Das ist kein realistischer Spieltag.'; return FALSE;}


	$leagues = get_leagues();
	if ($leagues){
		foreach ($leagues as $league){
			$ulis = get_ulis($league['ID']);
			// Punkte berechnen
			$scores = calculate_uli_scores($round, $year, $ulis, $league['ID']);

			// Zuschauer
			calculate_uli_visitors_per_round($round, $year, $ulis, $league['ID'], $scores);



			// TV-Einnahmen
			calculate_uli_tv_per_round($round, $year, $ulis, $league['ID'], $scores);


			// Sponsoring
			calculate_uli_sponsoring_per_round($round, $year, $ulis, $league['ID'], $scores);


			// Dispozinsen
		}
	}
}

function calculate_merch($round){
	global $option;
	$year = $option['currentyear'];

	// Als erstes der Check ob hier ne Runde uebergeben wurde
	if ($round < 1 OR $round > 35){echo 'Das ist kein realistischer Spieltag.'; return FALSE;}



	// Punkte berechnen
	//$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "round", "value" => $round);
	$cond[] = array("col" => "year", "value" => $year);
	$players = uli_get_results('userteams', $cond);
	unset($cond);

	//print_r($players);

	if ($players){
		foreach ($players as $player){
			$playerinfo = get_player_infos($player['playerID'], $leagueID, array('scores'));
			//print_r($playerinfo);

			$punkte = $playerinfo['scores'][$year][$round];
			if ($punkte < 1) {$trikots = rand(0,50);}
			if ($punkte == 1) { $trikots = 150 * (rand(800,1200)/1000); }
			if ($punkte == 2) { $trikots = 400 * (rand(800,1200)/1000); }
			if ($punkte == 3) { $trikots = 750 * (rand(800,1200)/1000);  } /* 750 */
			if ($punkte == 4) { $trikots = 1500 * (rand(800,1200)/1000);  } /* 1500 */
			if ($punkte == 6) { $trikots = 3000 * (rand(800,1200)/1000);  } /* 3000 */
			if ($punkte == 8) { $trikots = 6500 * (rand(800,1200)/1000);  } /* 7500 */
			if ($punkte == 10) { $trikots = 8500 * (rand(800,1200)/1000);  } /* 11111 */
			//echo $trikots.' ';
			settype($trikots, INT);

			// Die Trikotverkaeufe eintragen
			// checken, ob es schon einen eintrag gibt
			$cond[] = array("col" => "round", "value" => $round);
			$cond[] = array("col" => "year", "value" => $year);
			$cond[] = array("col" => "playerID", "value" => $player['playerID']);
			$cond[] = array("col" => "uliID", "value" => $player['uliID']);



			$soldtrikots = uli_get_var('merch_soldtrikots', $cond, 'ID');
			if ($soldtrikots){
				$values[] = array("col" => "number", "value" => $trikots);
				$values[] = array("col" => "income", "value" => $trikots * 25);
				uli_update_record('merch_soldtrikots', $cond, $values);
			}
			else {
				$values = $cond;
				$values[] = array("col" => "number", "value" => $trikots);
				$values[] = array("col" => "income", "value" => $trikots * 25);

				uli_insert_record('merch_soldtrikots', $values);
			}
			unset($cond);
			unset($values);
			$alltrikots[$player['uliID']] = $trikots + $alltrikots[$player['uliID']];
		}
	}
	if ($alltrikots){
		foreach ($alltrikots as $uliID => $ulitrikots){
			$merch = $ulitrikots * 25;
			echo $uliID.': '.$merch.'<br/>';
			calculate_money(15, $merch, $uliID, $round, $year, 'new', 'income');
		}
	}
}



/**
 * berechnet die TV-Einnahmen pro Spieltag nach dem gueltigen vertrag
 *
 */
function calculate_uli_tv_per_round($round, $year, $ulis, $leagueID, $scores){
	global $option;
	if ($scores){
		foreach ($scores as $score){
			$scoreuli[$score['uliID']] = $score['SUM(points)'];
			//echo 'Uli: '.$score['uliID'].' '.$score['SUM(points)'].'<br/>';
		}
	}
	foreach ($ulis as $uli){
		$uliIDs[] = $uli['ID'];
	}
	$cond[] = array("col" => "decision", "value" => 1);
	$cond[] = array("col" => "year", "value" => $option['currentyear-2']);
	$contracts = uli_get_results('tv_contracts', $cond);


	if ($contracts){
		foreach ($contracts as $contract){
			if (in_array($contract['uliID'], $uliIDs)) {
				$money = $contract['perpoint'] * $scoreuli[$contract['uliID']];
				calculate_money(17, $money, $contract['uliID'], $round, $year, 'new', 'income');
			}
		}
	}
}

// Year muss ein halbjahr sein
function calculate_uli_sponsoring_per_year($year){


	$leagues = get_leagues();
		if ($leagues){
			foreach ($leagues as $league){
			$leader = array();
			$top5 = array();


			$ulis = get_ulis($league['ID']);
			foreach ($ulis as $uli){
				$ulinames[$uli['ID']] = $uli['uliname'];
				}

			// 3 praemien: meister, top5, zuschauer
			// ACHTUNG HIER MUSS FUER DIE RUECKRUNDE NOCH DIE ADDITION EINGEBAUT WERDEN
			// IM MOMENT SIND DIE EINNAHMEN "NEW"


			// Position holen
			$cond = array();
			$fields = array();
			// ACHTUNG PER HAND

			$cond[] = array("col" => "leagueID", "value" => $league['ID']);
			$cond[] = array("col" => "year", "value" => 30);
			$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
			//Alle 34 Spieltage, dann nächste Zeile auskommentiert
			//$cond[] = array("col" => "round", "value" => 18, "func" => "<");
			$fields[] = "uliID";
			$fields[] = "SUM(score)";

			$order[] = array("col" => "SUM(score)", "sort" => "DESC");
			$result = uli_get_results('results', $cond, $fields, $order, NULL, "GROUP by uliID");
			if ($result){
				$x = 1;
				foreach ($result as $uli){
					if ($x == 1){
						$leader[$uli['uliID']] = TRUE;
					}
					elseif ($x < 6){
						$top5[$uli['uliID']] = TRUE;
					}

					$x++;
				}
			}

			// Zuschauer holen
			$cond = array();
			$fields = array();
			// ACHTUNG PER HAND
			//$cond[] = array("col" => "leagueID", "value" => 1);
			$cond[] = array("col" => "year", "value" => 30);
			$cond[] = array("col" => "type", "value" => 5, "func" => "<");
			$cond[] = array("col" => "type", "value" => 2, "func" => ">");
			$cond[] = array("col" => "sum", "value" => 0, "func" => ">");

			$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
			//Alle 34 Spieltage, dann nächste Zeile auskommentiert
			//$cond[] = array("col" => "round", "value" => 18, "func" => "<");
			$fields[] = "uliID";
			$fields[] = "SUM(sum)";
			$fields[] = "COUNT(ID)";
			$result = uli_get_results('finances', $cond, $fields, NULL, NULL, "GROUP by uliID");
			if ($result){
				foreach ($result as $result){
					$visitorAv[$result['uliID']] = $result['SUM(sum)'] / $result['COUNT(ID)'] * 2;
				}

			}

			foreach ($ulis as $uli){
				$cond = array();
				$cond[] = array("col" => "team_id", "value" => $uli['ID']);
				$cond[] = array("col" => "status", "value" => 2);
				$cond[] = array("col" => "year", "value" => $year);
				$contract = uli_get_row('sponsoring', $cond);
				echo $uli['ID'].' ... ';
				if ($leader[$uli['ID']]){
					$sum = $contract['extra_championship'];
					echo 'leader ';
					calculate_money(23, $sum, $uli['ID'], 0, 30, 'new', 'income');
					//calculate_money(23, $sum, $uli['ID'], 0, 30, 'add', 'income');

				}
				if ($top5[$uli['ID']]){
					$sum = $contract['extra_top5'];
					calculate_money(24, $sum, $uli['ID'], 0, 30, 'new', 'income');
					//calculate_money(24, $sum, $uli['ID'], 0, 30, 'add', 'income');
					echo 'top5 ';

				}
				if ($visitorAv[$uli['ID']] > 40000){
					$sum = $contract['extra_audience'];
					calculate_money(22, $sum, $uli['ID'], 0, 30, 'new', 'income');
					//calculate_money(22, $sum, $uli['ID'], 0, 30, 'add', 'income');
					echo 'visitors ';

				}
				echo '<br><br>';
			}
		}
	}


}


function calculate_uli_sponsoring_per_year_hack($year){
	// Fake Vertrag fuer 2011/12 und 2012/13

	/*
	 * F�r 2011/12 und 2012/13 wird nachtr�glich ein "Grundbetrag Sponsoring" bezahlt,
	 * der sich nach den Punkten in dieser Saison richtet.
	 * Punkte / 1000 * 10000000, das ist quasi ein Verfielfachen der Sponsoring Pr�mien pro Spieltag
	 * Ein Berechnen nach TR h�tte die Schere ein bisschen zu weit aufgemacht.
	 * In Zukunft muss das leistungsbezogener sein.
	 *
	 *
	 *
	 */

	//$contract['audience'] = 5000;
	$contract['champion'] = 5000000; // beeinflusst nach teamranking
	$contract['base'] = 10000000; // beeinfluss nach teamranking
	$contract['top5'] = 10000000; // top5 nach Punkten

	$ulis = get_ulis(1);
	foreach ($ulis as $uli){
		$ulinames[$uli['ID']] = $uli['uliname'];
		}

		// Base
		$cond[] = array("col" => "leagueID", "value" => 1);
		$cond[] = array("col" => "year", "value" => $year);
		$cond[] = array("col" => "round", "value" => 0);
		$order[] = array("col" => "score", "sort" => "DESC");
		$result = uli_get_results('results', $cond, NULL, $order);
		//print_R($result);
		if ($result){
			foreach ($result as $result){
				//$TR = get_TR($result['uliID']);
				$base = $result['score'] / 1000 * $contract['base'];
				$top5 = 0;
				echo uli_money($base).' - '.$ulinames[$result['uliID']].'<br><br>';
				$sum = $sum + $top5;

				//calculate_money(19, $base, $result['uliID'], 0, $year, 'new', 'income');
				//calculate_money(24, $top5, $result['uliID'], 0, $year, 'new', 'income');
			}
		}


	echo '<br><br>';
	echo 'Summe: '.uli_money($sum);

}


/**
 * berechnet die Sponsoring Einnahmen pro Runde
 *
 */
function calculate_uli_sponsoring_per_round($round, $year, $ulis, $leagueID, $scores){
	global $option;
	if ($scores){
		foreach ($scores as $score){
			$scoreuli[$score['uliID']] = $score['SUM(points)'];
		}
	}
	arsort($scoreuli);
	// Fake Vertrag fuer 2011/12

	//$contract['perpoint'] = 5000;
	//$contract['first'] = 1000000;
	//$contract['second'] = 750000;
	//$contract['third'] = 500000;


	$i = 1;
	foreach($scoreuli as $uliID => $score){

		$sponsoringOffers = get_sponsoring_offers($uliID);

		$signed = FALSE;
		if ($sponsoringOffers){
			foreach ($sponsoringOffers as $contract){
				if ($contract['status'] == 2){
					$signed = TRUE;
					$thiscontract = $contract;
				}
			}
		}

		$money = $thiscontract['per_point'] * $score;


		calculate_money(20, $money, $uliID, $round, $year, 'new', 'income');
		if ($score != $lastscore){
			$z = $i;
		}
		if ($z == 1){
			calculate_money(21, $thiscontract['extra_rank1'], $uliID, $round, $year, 'new', 'income');
		}
		elseif ($z == 2){
			calculate_money(21, $thiscontract['extra_rank2'], $uliID, $round, $year, 'new', 'income');
		}
		elseif ($z == 3){
			calculate_money(21, $thiscontract['extra_rank3'], $uliID, $round, $year, 'new', 'income');
		}
		else {
			calculate_money(21, 0, $uliID, $round, $year, 'new', 'income');
		}
		$lastscore = $score;
		$i++;

	}
}


/**
 * berechnet die Zuschauer Einnahmen pro Runde
 * AKTUALISIERT JANUAR 2014
 */
function calculate_uli_visitors_per_round($round, $year, $ulis, $leagueID, $scores){
	global $option;

	// Wir unterscheiden in Event Fans und Hardcore Fans
	// Event Fans moechten Komfort und Stars und laute Hardcore Fans (stimmung)
	// Hardcore Fans moechten Spieler, die lange im Verein sind und Stehplaetze
	// Ausserdem spielt die Performance des Teams in den letzten 5 Spielen sowie der aktuelle Tabellenplatz eine Rolle
	// Loyalitaet = Durchschnittszeit der Spieler im Kader

	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "uliID", "value" => 0, "func" => "!=");
	$result = uli_get_results(player_league, $cond);
	if ($result){
		foreach($result as $player){
			// Jetzt den letzten Transfer des Spielers holen
			$cond = array();
			$order = array();
			$cond[] = array("col" => "leagueID", "value" => $leagueID);
			$cond[] = array("col" => "playerID", "value" => $player['playerID']);
			$order[] = array("col" => "time", "sort" => "DESC");
			$transfer = uli_get_row('transfers', $cond, $order, NULL, "LIMIT 1");
			$allTransfers[$player['uliID']]['time'] = $allTransfers[$player['uliID']]['time'] + (mktime() - $transfer['time']);
			$allTransfers[$player['uliID']]['player'] = $allTransfers[$player['uliID']]['player'] + 1;
		}
	}

	foreach($allTransfers as $key => $uliTransfers){
		$avInTeam[$key] = ($uliTransfers['time'] / $uliTransfers['player']) / (3600 * 24);
	}

	if ($avInTeam){
		$minSize = min($avInTeam);
		$maxSize = max($avInTeam);
		foreach ($avInTeam as $key => $result){
			$faktorLoyalty[$key] = ($result - $minSize) * 100 / ($maxSize - $minSize);
			settype($faktorLoyalty[$key], INT);
		}
	}


	if ($ulis){
		foreach ($ulis as $uli){
			// Komfort
			$stadium = get_stadium($uli['ID']);
			$komfort[$uli['ID']] = 0;
			if ($stadium['infra']){
				foreach ($stadium['infra'] as $infra){
					$komfort[$uli['ID']] = $komfort[$uli['ID']] + $infra['sum'];
					if ($infra['type'] == "fanprojekt"){
						$FanProjekt[$uli['ID']] = 1.2;
					}
				}
			}
			// Performance
			// Schnitt der letzten 5 Spiele im Vergleich
			$cond = array();
			$order = array();
			$fields = array();
			$cond[] = array("col" => "uliID", "value" => $uli['ID']);
			$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
			$order[] = array("col" => "year", "sort" => "DESC");
			$order[] = array("col" => "round", "sort" => "DESC");
			$fields[] = "score";
			$fields[] = "year";
			$fields[] = "round";
			$allScores = uli_get_results("results", $cond, $fields, $order, 30);
			// fuer rueckberechnungen
			$history = FALSE;
			$scores = array();
			foreach	($allScores as $thisScore){
				if ($round == $thisScore['round']){
					$history = TRUE;
				}
				if ($history){
					if (isset($thisScore['score'])){
						$scores[] = $thisScore['score'];
					}
				}
				if ($count == 5){
					break;
				}
			}
			$sumScore = 0;
			//$avScore = 0;
			if ($scores){
				for ($i = 0; $i < count($scores); $i++){
					$sumScore = $scores[$i] + $sumScore;
				}
				$avScore[$uli['ID']] = $sumScore / count($scores);
				//print_r($avScore);
			}
		}
		$maxScore = max($avScore);
		$minScore = min($avScore);
		foreach ($avScore as $key => $result){
			$faktorPerformance[$key] = ($result - $minScore) * 100 / ($maxScore - $minScore);
			settype($faktorPerformance[$key], INT);
		}
		$maxScore = max($komfort);
		$minScore = min($komfort);
		foreach ($komfort as $key => $result){
			$faktorKomfort[$key] = ($result - $minScore) * 100 / ($maxScore - $minScore);
			settype($faktorKomfort[$key], INT);
		}
	}
	if ($ulis){
		foreach ($ulis as $uli){
			$calcVisitors = FALSE;
			if ($round % 2 != 0 AND $uli['ID'] % 2 != 0){
				$calcVisitors = TRUE;
			}
			if ($round % 2 == 0 AND $uli['ID'] % 2 == 0){
				$calcVisitors = TRUE;
			}

			if ($calcVisitors){
				$TR = get_TR($uli['ID']);
				$stadium = get_stadium($uli['ID']);
				echo '<br><br>';

				$stadium[0]['preissteh'] = 20;
				$stadium[0]['preissitz'] = 30;

				//echo $stadium['stehplaetze'];
				$faktor['komfort'] = $faktorKomfort[$uli['ID']];
				$faktor['stars'] = $TR['TR_marktwert'];
				$faktor['performance'] = $faktorPerformance[$uli['ID']];
				$faktor['loyalty'] = $faktorLoyalty[$uli['ID']];
				$faktor['fanProjekt'] = $FanProjekt[$uli['ID']];
				if (!$faktor['fanProjekt']){
					$faktor['fanProjekt'] = 1;
					}
				echo $uli['uliname'].' | '.$avScore[$uli['ID']];

				// Stehplaetze
				// Die Basis ist 25000 und 50
				// Standardpreis = 15 // TODO Das dynamisch machen?
				// Ein Fanprojekt multipliziert die Chose mit 1.2
				// 17.8.2015 Preiserhöhung auf 20

				$basis = 10000;

				$preisFaktor = ($stadium[0]['preissteh'] - 20) * - 1;
				if ($preisFaktor > 10){$preisfaktor = 10;}
				$preisFaktor = ($preisFaktor + 10) * 100 / (40) / 50;
				echo '<br><br>';

				echo ($faktor['loyalty'] / 50 * $basis).' | ';
				echo ($faktor['performance'] / 50 * $basis).' | ';
				echo ($preisFaktor * $basis).' | ';
				echo ($faktor['fanProjekt'] * $basis).' | ';

				$steh = (($faktor['loyalty'] / 50 * $basis) + ($faktor['performance'] / 50 * $basis) + ($preisFaktor * $basis) + ($faktor['fanProjekt'] * $basis)) / 4;

				if ($steh > $stadium['stehplaetze']){
					$steh = $stadium['stehplaetze'];
				}


				// Sitzplaetze
				// Die Basis ist 35000 und 50
				// Standardpreis = 25 // TODO Das dynamisch machen?
				// Presierhöhung auf 30
				$basis = 35000;
				$preisFaktor = ($stadium[0]['preissitz'] - 30) * - 1;
				if ($preisFaktor > 10){$preisfaktor = 10;}
				$preisFaktor = ($preisFaktor + 15) * 100 / (40) / 50;
				echo '<br><br>';

				echo ($faktor['stars'] / 50 * $basis).' | ';
				echo ($faktor['performance'] / 50 * $basis).' | ';
				echo ($preisFaktor * $basis).' | ';
				echo ($faktor['komfort'] /50 * $basis).' | ';

				$sitz = (($faktor['stars'] / 50 * $basis) + ($faktor['performance'] / 50 * $basis) + ($preisFaktor * $basis) + ($faktor['komfort'] / 50 * $basis)) / 4;

				if ($sitz > $stadium['sitzplaetze']){
					// wenn die sitzplatzzuschauer keinen platz haben, kaufen sie ein stehplatzticket
					$steh = $steh + $sitz - $stadium['sitzplaetze'];
					if ($steh > $stadium['stehplaetze']){
						$steh = $stadium['stehplaetze'];
					}
					$sitz = $stadium['sitzplaetze'];
				}
				settype($sitz, INT);

				settype($steh, INT);
				echo $steh.' - '.uli_money($steh * $stadium[0]['preissteh']);
				echo '<br><br>';



				echo $sitz.' - '.uli_money($sitz * $stadium[0]['preissitz']);

				$zuschauerEinnahmen = ($sitz * $stadium[0]['preissitz']) + ($steh * $stadium[0]['preissteh']);

				echo '<br><br>';

				// Catering
				// Eine Bude kann 5.000 Zuschauer versorgen und macht pro Zuschauer 2 Euro Gewinn

				if ($stadium['infra']){
					$bier = 0;
					$bratwurst = 0;
					foreach ($stadium['infra'] as $infra){
						if ($infra['type'] == "bier"){
							$bier = $bier + 1;
						}
						if ($infra['type'] == "bratwurst"){
							$bratwurst = $bratwurst + 1;
						}

					}
					if (($bier * 5000) < ($sitz + $steh)){
						$bierMoney = $bier * 5000 * 2;
					} else {
						$bierMoney = ($sitz + $steh) * 2;
					}
					if (($bratwurst * 5000) < ($sitz + $steh)){
						$bratwurstMoney = $bratwurst * 5000 * 2;
					} else {
						$bratwurstMoney = ($sitz + $steh) * 2;
					}
					$catering = ($bratwurstMoney + $bierMoney) * rand(90,110) / 100;


				}
				$uliID = $uli['ID'];
				calculate_money(2, $zuschauerEinnahmen, $uliID, $round, $year, 'new', 'income');
				calculate_money(3, $steh, $uliID, $round, $year, 'new', 'income');
				calculate_money(4, $sitz, $uliID, $round, $year, 'new', 'income');
				calculate_money(27, $catering, $uliID, $round, $year, 'new', 'income');
			}
		}
	}



	// holt das Stadion

	// Sitplaetze
	// Stehplaetze
	// Aus dem Enviroment wird die Attraktivitaet berechnet
	// Aus anderen Parametern wird die Attraktivitaet berechnet

	// NEUE Einnahmen 2013 "Catering und Sonstiges"
	// Heimspiel, wenn Heimspiel bei "Jeder gegen Jeden" oder jeden zweiten Spieltag


	/*


	if ($scores){
		foreach ($scores as $score){
			$scoreuli[$score['uliID']] = $score['SUM(points)'];
		}
	}
	arsort($scoreuli);
	// Fake fuer 2011/12
	// 50.000 Stadion
	// 30.000 Sitzplaetze
	// 20.000 Stehplaetze
	// Preise 25/15




	//





	foreach($scoreuli as $uliID => $score){
		$steh = 0;
		$sitz = 0;
		$sum = 0;
		if ($round % 2 != 0 AND $uliID % 2 != 0){
			$TR = get_TR($uliID);
			$sitz = ($score * 666);
			$steh = ($score * 333);
			if ($steh < 0){$steh = 0;}
			if ($sitz < 0){$sitz = 0;}
			$steh = ($TR['TR_gesamt'] * 150 ) + $steh;
			$sitz = ($TR['TR_gesamt'] * 150 ) + $sitz;
			if ($sitz > 30000){$sitz = 30000;}
			if ($steh > 20000){$steh = 20000;}
			settype($sitz, INT);
			settype($steh, INT);
			$sum = ($steh * 15) + ($sitz * 25);
			//echo $uliID.':'.$sitz.'|'.$steh.'|'.$sum.'</br>';
		}
		if ($round % 2 == 0 AND $uliID % 2 == 0){
			$TR = get_TR($uliID);
			$sitz = ($score * 666);
			$steh = ($score * 333);
			if ($steh < 0){$steh = 0;}
			if ($sitz < 0){$sitz = 0;}
			$steh = ($TR['TR_gesamt'] * 150 ) + $steh;
			$sitz = ($TR['TR_gesamt'] * 150 ) + $sitz;
			if ($sitz > 30000){$sitz = 30000;}
			if ($steh > 20000){$steh = 20000;}
			settype($sitz, INT);
			settype($steh, INT);
			$sum = ($steh * 15) + ($sitz * 25);
			//echo $uliID.':'.$sitz.'|'.$steh.'|'.$sum.'</br>';
		}

		//calculate_money(2, $sum, $uliID, $round, $year, 'new', 'income');
		//calculate_money(3, $steh, $uliID, $round, $year, 'new', 'income');
		//calculate_money(4, $sitz, $uliID, $round, $year, 'new', 'income');

		calculate_money(2, 0, $uliID, $round, $year, 'new', 'income');
		calculate_money(3, 0, $uliID, $round, $year, 'new', 'income');
		calculate_money(4, 0, $uliID, $round, $year, 'new', 'income');
	}
	*/
}



function calculate_uli_scores($round, $year, $ulis, $leagueID){
	global $wpdb;

	foreach ($ulis as $uli){
		$uliIDs[] = $uli['ID'];
	}

	// wie macht man das am schnellsten?
	// Alle Spieler, holen, die Punkte haben an diesem Spieltag
	unset($cond);
	$table = 'player_points pp ';
	$table .= 'LEFT JOIN tip_uli_player p ON p.ID = pp.playerID';
	$cond[] = array("col" => "pp.round", "value" => $round);
	$cond[] = array("col" => "pp.year", "value" => $year);
	$players = uli_get_results($table, $cond);
	if ($players){
		foreach ($players as $player){

			$allplayers[$player['playerID']] = $player;
		}
	}

	unset($cond);
	$cond[] = array("col" => "round", "value" => $round);
	$cond[] = array("col" => "year", "value" => $year);
	$userteams = uli_get_results('userteams', $cond);

	unset($cond);
	$cond[] = array("col" => "round", "value" => $round);
	$cond[] = array("col" => "year", "value" => $year);
	$userformation = uli_get_results('userformation', $cond);
	if ($userformation){
		foreach ($userformation as $userformation){
			$formation[$userformation['uliID']] = $userformation['formation'];
		}
	}

	if ($userteams){
		foreach ($userteams as $entry){
			if (in_array($entry['uliID'], $uliIDs)){
				// Wir probieren mal die ganze Kiste zu beschleunigen indem gesammelt wird welcher punktwert wo eingetragen werden muss :)
				// lustiger denksport
				$thisformation = '';
				$thisrealscore = '';
				$thisformation = $formation[$entry['uliID']];
				$thisrealscore = $allplayers[$entry['playerID']]['score'];

				if ($entry['number'] > 11 AND $entry['number'] < 15){
					$score = $thisrealscore / 2;
				}
				elseif ($entry['number'] == 15){
					$score = $thisrealscore;
				}
				else {
					// positionscheck nach system
					$position = get_formation_position($thisformation, $entry['number']);
					$faktor = get_position_faktor($position, $allplayers[$entry['playerID']]);
					if ($faktor == 0.25){$score = $thisrealscore * 0.25;}
					if ($faktor == 0.5){$score = $thisrealscore * 0.5;}
					if ($faktor == 0.75){$score = $thisrealscore * 0.75;}
					if ($faktor == 1){$score = $thisrealscore;}

				}

				settype($score, STRING);
				$scoresarray[$score][] = $entry['playerID'];
			}
		}
	}





	if ($scoresarray){
		foreach ($scoresarray as $key => $scores){
			//echo $key.' ';
			//print_r($scores);
			settype($key, float);
			unset($cond);
			unset($value);
			$cond[] = array("col" => "playerID", "value" => implode(",", $scores), "func" => "IN");
			$cond[] = array("col" => "uliID", "value" => implode(",", $uliIDs), "func" => "IN");
			$cond[] = array("col" => "round", "value" => $round);
			$cond[] = array("col" => "year", "value" => $year);
			$value[] = array("col" => "points", "value" => $key);
			//$_REQUEST['debug'] = "update";

			uli_update_record('userteams', $cond, $value);
		}
	}


	// Und jetzt die Punkte berechnen
	unset($cond);
	unset($value);
	//$_REQUEST['debug'] = "getresults";
	$cond[] = array("col" => "uliID", "value" => "(".implode(",", $uliIDs).")", "func" => "IN");
	$cond[] = array("col" => "round", "value" => $round);
	$cond[] = array("col" => "year", "value" => $year);
	$fields = array("SUM(points)", "uliID");

	$result = uli_get_results('userteams', $cond, $fields, NULL, NULL, 'GROUP by uliID');
	$scoresperday = $result;

	//unset($_REQUEST['debug']);

	if ($result){
		foreach ($result as $uli){
			$uliTeam = get_uli($uli['uliID']);
			unset($cond);
			unset($value);
			$cond[] = array("col" => "round", "value" => $round);
			$cond[] = array("col" => "year", "value" => $year);
			$cond[] = array("col" => "leagueID", "value" => $uliTeam['leagueID']);
			$cond[] = array("col" => "uliID", "value" => $uli['uliID']);
			$value[] = array("col" => "score", "value" => $uli['SUM(points)']);
			if (uli_get_row('results', $cond)){
				uli_update_record('results', $cond, $value);
			}
			else {
				$cond[] = array("col" => "score", "value" => $uli['SUM(points)']);
				uli_insert_record('results', $cond);
			}
		}
	}

	// Und jetzt die Gesamtpunkte berechnen
	unset($cond);
	unset($value);
	$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "year", "value" => $year);
	$fields = array("SUM(points)", "uliID");
	$result = uli_get_results('userteams', $cond, $fields, NULL, NULL, 'GROUP by uliID');

	if ($result){
		foreach ($result as $uli){
			unset($cond);
			unset($value);
			$uliTeam = get_uli($uli['uliID']);
			$cond[] = array("col" => "round", "value" => 0);
			$cond[] = array("col" => "year", "value" => $year);
			$cond[] = array("col" => "leagueID", "value" => $uliTeam['leagueID']);
			$cond[] = array("col" => "uliID", "value" => $uli['uliID']);
			$value[] = array("col" => "score", "value" => $uli['SUM(points)']);
			if (uli_get_row('results', $cond)){
				uli_update_record('results', $cond, $value);
			}
			else {
				$cond[] = array("col" => "score", "value" => $uli['SUM(points)']);
				uli_insert_record('results', $cond);
			}
		}
	}
	/*
	echo '<pre>';
	print_r($scoresperday);
	echo '</pre>';
	*/
	return $scoresperday;

}




/**
 * ueberprueft welche Spieler nicht unter Vertrag stehen und wer
 */
function check_everybody($leagueID, $ID = ''){
	$cond[] = array("col" => "pl.leagueID", "value" => $leagueID);
	$cond[] = array("col" => "pl.uliID", "value" => 0);
	$cond[] = array("col" => "p.team", "value" => 999, "func" => "!=");


	echo '<h3>Welche Spieler gibt es in echt, haben keinen Verein im Uli und Kalle Spiel und es gibt keine aktive Auktion.</h3>';
	$errors = FALSE;
	//$_REQUEST['debug'] = "getresults";
	$result = uli_get_results('player_league pl LEFT JOIN tip_uli_player p ON p.ID = pl.playerID', $cond);
	if ($result){
		foreach ($result as $player){
			$auction = get_auction_player($player['playerID'], $leagueID);
			if (!$auction){echo $player['name'].'<br/>';$errors = TRUE;}
		}
	}
	if ($errors){
		echo 'Wenn hier Spieler stehen, die Kumpels am besten einmal "blind" (also zu ihrem heimatverein) transferieren, dann muesste ne Auktion anspringen.';
	}

	// Jetzt die Importdatei checken
	// dafuer aber die aktuellen daten in tip_uli_calc nehmen

	// jetzt den abgleich
	// ersteinmal nur die nach kickerid

	echo '<br/>';
	echo '<br/>';
	echo '<h3>Abgleich mit den Kicker-Kadern</h3>';

	$result = uli_get_results('calc');
	if ($result){
		foreach ($result as $playercalc){
			unset($cond);
			$cond[] = array("col" => "kickerID", "value" => $playercalc['kickerID']);
			$match = uli_get_row('player', $cond);
			if (!$match){
				echo 'Der Typ steht in den Kicker-Kadern ist dem Spiel aber nicht bekannt: <br/>';
				echo '<b>'.$playercalc['vorname'].' '.$playercalc['nachname'].'</b> Verein laut Kicker: '.$playercalc['verein'].' KickerID: '.$playercalc['kickerID'];
				echo '<br/>';

				// Jetzt in den alten Spielern schauen, ob da jemand passen koennte.
				unset($cond);
				$cond[] = array("col" => "name", "value" => "%".$playercalc['nachname']."%", "func" => "LIKE");
				$possibleplayers = uli_get_results('player', $cond);
				if ($possibleplayers){
					foreach($possibleplayers as $possibleplayer){
						echo 'Wir haben folgende &auml;nliche Spieler in der Datenbank: ';
						echo '<b>'.$possibleplayer['name']. '</b> KickerID: '.$possibleplayer['kickerID'];
						echo '<br/>';

					}
				}
				echo '<br/><br/>';
			}
		}
	}


	$ligateam = get_all_team_names();

	echo '<h3>Spieler im Spiel, die keine KickerID haben.</h3>';
	unset($cond);
	$cond[] = array("col" => "kickerID", "value" => 0);
	$cond[] = array("col" => "team", "value" => 999, "func" => "!=");
	$result = uli_get_results('player', $cond);
	if ($result){
		foreach ($result as $player){
			echo 'Der Typ steht in unserer Tabelle aber nicht in der Kicker Import-Datei<br/>';
			echo '<b>'.$player['name'].' </b> Verein: '.$ligateam[$player['team']];
			echo '<br/><br/>';
		}
	}

	// TODO. Dann hier die moeglichen Zuordnungen. Am besten gleich ueber die KickerID.
}


/**
 * ueberprueft die doppelten eintraege in der playerleague tabelle.
 */
function check_contracts_table($leagueID, $ID = ''){
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "history", "value" => 0);

	$order[] = array("col" => "playerID", "sort" => "ASC");
	$result = uli_get_results('player_contracts', $cond, NULL, $order);
	if ($result){
		$errors = FALSE;
		foreach ($result as $player){
			if ($lastplayer['playerID'] == $player['playerID']){
				$errors = TRUE;
				echo '<a class="deletecontract" id="contract'.$lastplayer['ID'].'">';
				echo $lastplayer['ID'];
				echo '(ID) | ';
				echo $lastplayer['playerID'];
				echo '(playerID) | ';
				echo $lastplayer['uliID'];
				echo '(uliID) | ';
				echo $lastplayer['salary'];
				echo '(Gehalt) | ';
				echo $lastplayer['length'];
				echo '(Dauer)</a>';
				echo '<br/>';

				echo '<a class="deletecontract" id="contract'.$player['ID'].'">';
				echo $player['ID'];
				echo '(ID) | ';
				echo $player['playerID'];
				echo '(playerID) | ';
				echo $player['uliID'];
				echo '(uliID) | ';
				echo $player['salary'];
				echo '(Gehalt) | ';
				echo $player['length'];
				echo '(Dauer)</a>';
				echo '<br/>';
				echo '<br/>';
				echo '<br/>';
			}
			else {
				$lastplayer = $player;
			}
		}
	}
	if (!$errors){
		echo '<h3>Alles Schick</h3>';
	}
}


/**
 * ueberprueft die doppelten eintraege in der playerleague tabelle.
 */
function check_player_league_table($leagueID, $ID = ''){
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$order[] = array("col" => "playerID", "sort" => "ASC");
	$result = uli_get_results('player_league', $cond, NULL, $order);
	if ($result){
		$errors = FALSE;
		foreach ($result as $player){
			if ($lastplayer['playerID'] == $player['playerID']){
				$errors = TRUE;
				echo '<a class="deleteplayerleague" id="playerleague'.$lastplayer['ID'].'">';
				echo $lastplayer['ID'];
				echo '(ID) | ';
				echo $lastplayer['playerID'];
				echo '(playerID) | ';
				echo $lastplayer['uliID'];
				echo '(uliID) | ';
				echo $lastplayer['jerseynumber'];
				echo '(Trikotnummer) | ';
				echo $lastplayer['marktwert'];
				echo '(MW) | ';
				echo $lastplayer['status'];
				echo '(Status)</a>';
				echo '<br/>';

				echo '<a class="deleteplayerleague" id="playerleague'.$player['ID'].'">';
				echo $player['ID'];
				echo '(ID) | ';
				echo $player['playerID'];
				echo '(playerID) | ';
				echo $player['uliID'];
				echo '(uliID) | ';
				echo $player['jerseynumber'];
				echo '(Trikotnummer) | ';
				echo $player['marktwert'];
				echo '(MW) | ';
				echo $player['status'];
				echo '(Status)</a>';
				echo '<br/>';
				echo '<br/>';
				echo '<br/>';
			}
			else {
				$lastplayer = $player;
			}
		}
	}
	if (!$errors){
		echo '<h3>Alles Schick</h3>';
	}
}

/**
 * ueberpreueft die lustigen doppelten transfers und loescht die jeweils letzten
 */
function check_double_transfers(){
	global $wpdb;
	$cond[] = array("col" => "leagueID", "value" => 1);
	$order[] = array("col" => "time", "sort" => "ASC");
	$result = uli_get_results('transfers', $cond, NULL, $order, "3999,6000");
	if ($result){
		foreach ($result as $transfer){
			if ($transfer['playerID'] == $lasttransfer['playerID'] AND ($lasttransfer['time'] + 20) > $transfer['time']){
				unset($cond);
				$cond[] = array("col" => "ID", "value" => $transfer['ID']);
				uli_delete_record('transfers', $cond);
				$deletedentries = $deletedentries + 1;
			}
			else {
				$lasttransfer = $transfer;
			}
		}
		echo '<h3>'.$deletedentries.' doppelte Transfers geloescht.</h3>';
	}
	else {
		echo '<h3>Nichts zu reparieren.</h3>';
	}

}


/*
 * neuer Spieler
 * Es wird erst einmal nur eine Namensfeld eingegeben
 *
 *
 */
function print_new_player_form(){


	$html .= '<form id="newplayerform" method="POST">';
	$html .= '<span id="title">Spielername </span><br/>';
	$html .= '<input class="newplayernameinput" name="playername" type="text" id="newplayer" />';
	$html .= '<br> <select name="team" id="newplayerteam">';

	$ligateam = get_all_team_names();
	foreach ($ligateam as $key => $teamname){
		if ($key != 999){
			$html .= '<option '.$select.' value="team'.$key.'">'.$teamname.'</option>';
			$html .= "\n";
		}
	}
	$html .= '</select>';
	$html .= '<div id="searchresultdata"></div>';
	$html .= '<input type="submit" id="insertnewplayer" value="Spieler Neu eingeben.">';
	$html .= '</form>';

	return $html;
}

//  Schaut, ob es den neuen Spieler gibt.
function check_new_player($playername) {
	$cond[] = array("col" => "name", "value" => "%".$playername."%", "func" => "LIKE");
	$result = uli_get_results('player', $cond);
	if (!$result) {
		return FALSE;
	}
	else {
		return $result;
	}
}



function edit_player($teamview = '', $oldPlayers = '') {

	if (!$teamview){
		$teamview = 33;
	}

	/* Es werden alle Ulinamen eingelesen */
	$uliname = get_all_uli_names($option['leagueID']);
	/* Es werden alle Bundesligateamnamen eingelesen */
	$ligateam = get_all_team_names();


	if ($oldPlayers){
		$cond[] = array("col" => "team", "value" => "999");
		$players = uli_get_results('player', $cond);
	}
	else {
		$cond[] = array("col" => "team", "value" => $teamview);
		$players = uli_get_results('player', $cond);
	}

	if ($players){
		$x = 1;
		foreach ($players as $players){
			//if ($x < 20){
			$player = get_player_infos($players['ID']);
			if ($oldPlayers){
				$html .= print_admin_edit_player_old($player, $uliname, $ligateam);
			}
			else {
				$html .= print_admin_edit_player($player, $uliname, $ligateam);
			}
			$x = $x + 1;
			//}
		}
	}

	return $html;
}



function calculate_marktwert_all(){
	$players = get_players();
	$leagues = get_leagues();
	if ($players){
		foreach ($players as $player){
			foreach ($leagues as $league){
				$marktwert[$player['ID']] = get_marktwert(NULL, $player['ID'], $league['ID']);
				$cond = array();
				$value = array();
				$cond[] = array("col" => "playerID", "value" => $player['ID']);
				$cond[] = array("col" => "leagueID", "value" => $league['ID']);
				$value[] = array("col" => "marktwert", "value" => $marktwert[$player['ID']]);
				uli_update_record('player_league', $cond, $value);
				$playerse[$player['ID']] = $player;
			}

		}
		echo '<h3>Fertig.</h3>';
	}
}



function calculate_status_all_player(){
	$leagues = get_leagues();
	if ($leagues){
		foreach ($leagues as $league){
			$players = get_players();
			if ($players){
				foreach ($players as $player){
					calculate_player_status($player['ID'], $league['ID']);
				}
			}
		}
		echo '<h3>Fertig.</h3>';
	}
}


function edit_stars() {
	$cond = array();
	$cond[] = array("col" => "team", "value" => "999", "func" => "!=");
	$order[] = array("col" => "hp", "sort" => "ASC");
	$order[] = array("col" => "name", "sort" => "ASC");
	$result = uli_get_results('player', $cond, NULL, $order);
	if ($result){
		$content .= '<form action ="?action=change" METHOD = "POST">';
		$x = 1;
		$colh[1] = Player;
		$colh[2] = Blickfeld;
		$colh[3] = WeitererKreis;
		$colh[4] = InternationaleKlasse;
		$colh[5] = Weltklasse;

		foreach ($result as $result) {
			$player = get_player_infos($result['ID']);
			$sum = round($result['marktwert']/1000000, 2);

			$starcheck = array();
			$starcheck[$player['star']] = 'checked = "checked"';


			$data[$x][] = '<a class="playerinfo" id = "'.$result['ID'].'" href="#">'.$player['name']. '</a>';
			$data[$x][] =  '<input type = "checkbox" class="Blickfeld" id = "'.$result['ID'].'" '.$starcheck[4].'>';
			$data[$x][] =  '<input type = "checkbox" class="WeitererKreis" id = "'.$result['ID'].'" '.$starcheck[3].'>';
			$data[$x][] =  '<input type = "checkbox" class="IK" id = "'.$result['ID'].'" '.$starcheck[2].'>';
			$data[$x][] =  '<input type = "checkbox" class="WK" id = "'.$result['ID'].'" '.$starcheck[1].'>';

			$x = $x + 1;
		}}
		if ($colh AND $data){
			$content .= uli_table($colh, $data, '');
		}

		//$content .=  '<input type = "submit" value = "GO">';
		$content .= '</form>';

		return $content;
}

function print_admin_player_menue(){
	global $option;


	$SelectOptions[] = array("view" => "editplayer", "desc" => EditPlayer);
	$SelectOptions[] = array("view" => "reactivate", "desc" => Reaktivieren);
	$SelectOptions[] = array("view" => "newplayers", "desc" => NewPlayer);
	$SelectOptions[] = array("view" => "repairtransfers", "desc" => TransfersReparieren);




	//$SelectOptions[] = array("view" => "checkimport", "desc" => CheckKickerImport);



	$SelectOptions[] = array("view" => "check_verletzte", "desc" => VerletzteSpieler);

	$SelectOptions[] = array("view" => "status", "desc" => StatusBerechnen);
	$SelectOptions[] = array("view" => "marktwert", "desc" => MarktwertBerechnen);
	$SelectOptions[] = array("view" => "editstars", "desc" => EditStars);
	$SelectOptions[] = array("view" => "checktransfers", "desc" => CheckTransfers);
	$SelectOptions[] = array("view" => "checkfinances", "desc" => CheckFinances);
	$SelectOptions[] = array("view" => "checkplayerleague", "desc" => CheckPlayerLeague);
	$SelectOptions[] = array("view" => "checkcontracts", "desc" => CheckContracts);
	$SelectOptions[] = array("view" => "calculateteamranking", "desc" => CalculateTeamRanking);
	$SelectOptions[] = array("view" => "everybodythere", "desc" => EverybodyThere);
	$SelectOptions[] = array("view" => "updateplayerleaguegames", "desc" => CheckPlayerLeagueTable);



	$html .= "\n";
	if ($SelectOptions){
		foreach ($SelectOptions as $SelectOption){
			$active = '';
			if ($view == $SelectOption['view']){$active = 'active';}
			$html .= '<a href="?view='.$SelectOption['view'].'&amp;year='.$year.'" class="'.$active.'">'.$SelectOption['desc'].'</a>';
			$html .= '<br/>';
			$html .= "\n";
		}}
		$html .= "\n";
		$html = uli_box(Admin.' '.Menu, $html);
		return $html;
}


function print_admin_menu_calculate_rounds(){
	$html .= '<form METHOD="POST" action="?view=calculateround">';
	$html .= '<input type="text" size = "2" name="round">. Spieltag';
	$html .= '<input type="submit" value="Alles ausrechnen.">';

	$html .= '</form>';
	$html .= "\n";
	$html = uli_box(Admin.' '.Menu, $html);

	$shtml .= '<form METHOD="POST" action="?view=merch">';
	$shtml .= '<input type="text" size = "2" name="round">. Spieltag';
	$shtml .= '<input type="submit" value="Merch ausrechnen.">';

	$shtml .= '</form>';
	$shtml .= "\n";

	$html .= uli_box(Admin.' '.Menu, $shtml);

	$xhtml .= '<form METHOD="POST" action="?view=smile">';
	$xhtml .= '<input type="text" size = "2" name="round">. Spieltag';
	$xhtml .= '<input type="submit" value="Zufriedenheit ausrechnen.">';

	$xhtml .= '</form>';
	$xhtml .= "\n";
	$html .= uli_box(Admin.' '.Menu, $xhtml);


	//$html = uli_box(Admin.' '.Menu, $html);


	return $html;
}



/**
 * Editieren eines Spielers mit allen schicken Klassen fuer das Ajax
 *
 */
function print_admin_edit_player($player, $uliname, $ligateam){
	global $option;

	//echo $player['uliID'];
	//print_r($player);

	$player_age = player_age($player['birthday']);
	if ($player_age < 20){$age = 1;}
	elseif ($player_age <= 25){$age = 2;}
	elseif ($player_age <= 30){$age = 3;}
	else {$age = 4;}

	$html .= '<div id="editplayer-'.$player['playerID'].'" class="ageshow positionshow teamshow auction team'.$player['team'].' age'.$age.' position'.$player['hp'].' '.$class.'">';

	/* Wappenbild des Bundesligateams */
	$html .= "\n";
	$html .= '<div class="player" style="width: 20%;">';
	$html .= get_ligateam_wappen($player['team'], $ligateam);
	$html .= "\n";

	$html .= '<b><span><a href="#" class="playerinfo" id = "'.$player['playerID'].'">'.$player['name'].'</a></span></b> ';
	// Alter
	$html .= '('.player_age($player['birthday']).')<br/>';
	$html .= $uliname[$player['uliID']].'';
	$htnl .= '';

	/* Div Marker fuer die Spielerinfo */
	$html .= '<div class="marker" id="player-'.$player['playerID'].'"></div>';

	/* Positionen und Fu� */
	$html .= '</div>';

	/* aktuelles H�chstgebot */
	/* Hier umschlie�t ein DIV die Angaben damit aktualisiert werden kann */
	$html .= "\n";
	$html .= '<div style="width: 20%;" class="auctionbet positions" id ="positions-'.$player['ID'].'">';

	$html .=  '<select id="hp-'.$player['playerID'].'" name="hp-'.$player['playerID'].'">';
	unset ($checked1);unset ($checked2);unset ($checked3);unset ($checked4);unset ($checked5);unset ($checked6);unset ($checked7);
	if ($player['hp']==1){$checked1 = 'selected';}
	if ($player['hp']==2){$checked2 = 'selected';}
	if ($player['hp']==3){$checked3 = 'selected';}
	if ($player['hp']==4){$checked4 = 'selected';}
	if ($player['hp']==5){$checked5 = 'selected';}
	if ($player['hp']==6){$checked6 = 'selected';}
	if ($player['hp']==7){$checked7 = 'selected';}
	$html .=  '<option value="0" '.$checked0.'>?</option><option value="1" '.$checked1.'>TW</option><option value="2" '.$checked2.'>AV</option><option value="3" '.$checked3.'>IV</option><option value="4" '.$checked4.'>ZMF</option><option value="5" '.$checked5.'>FMF</option><option value="6" '.$checked6.'>S2</option><option value="7" '.$checked7.'>MS</option>';
	$html .=  '</select>';

	$html .=  '<select id = "np1-'.$player['playerID'].'" name="np1-'.$player['playerID'].'">';
	unset($checked0);unset($checked1);unset ($checked2);unset ($checked3);unset ($checked4);unset ($checked5);unset ($checked6);unset ($checked7);
	if ($player['np1']==0){$checked0 = 'selected';}
	if ($player['np1']==1){$checked1 = 'selected';}
	if ($player['np1']==2){$checked2 = 'selected';}
	if ($player['np1']==3){$checked3 = 'selected';}
	if ($player['np1']==4){$checked4 = 'selected';}
	if ($player['np1']==5){$checked5 = 'selected';}
	if ($player['np1']==6){$checked6 = 'selected';}
	if ($player['np1']==7){$checked7 = 'selected';}
	$html .=  '<option value="0" '.$checked0.'>-</option><option value="1" '.$checked1.'>TW</option><option value="2" '.$checked2.'>AV</option><option value="3" '.$checked3.'>IV</option><option value="4" '.$checked4.'>ZMF</option><option value="5" '.$checked5.'>FMF</option><option value="6" '.$checked6.'>S2</option><option value="7" '.$checked7.'>MS</option>';
	$html .=  '</select>';

	$html .=  '<select id = "np2-'.$player['playerID'].'" name="np2-'.$player['playerID'].'">';
	unset($checked0);unset($checked1);unset ($checked2);unset ($checked3);unset ($checked4);unset ($checked5);unset ($checked6);unset ($checked7);
	if ($player['np2']==0){$checked0 = 'selected';}
	if ($player['np2']==1){$checked1 = 'selected';}
	if ($player['np2']==2){$checked2 = 'selected';}
	if ($player['np2']==3){$checked3 = 'selected';}
	if ($player['np2']==4){$checked4 = 'selected';}
	if ($player['np2']==5){$checked5 = 'selected';}
	if ($player['np2']==6){$checked6 = 'selected';}
	if ($player['np2']==7){$checked7 = 'selected';}
	$html .=  '<option value="0" '.$checked0.'>-</option><option value="1" '.$checked1.'>TW</option><option value="2" '.$checked2.'>AV</option><option value="3" '.$checked3.'>IV</option><option value="4" '.$checked4.'>ZMF</option><option value="5" '.$checked5.'>FMF</option><option value="6" '.$checked6.'>S2</option><option value="7" '.$checked7.'>MS</option>';
	$html .=  '</select>';

	$html .=  '<select id = "foot-'.$player['playerID'].'" name="foot-'.$player['playerID'].'">';
	unset($checked1);unset ($checked2);unset ($checked3);
	if ($player['foot']==1){$checked1 = 'selected';}
	if ($player['foot']==2){$checked2 = 'selected';}
	if ($player['foot']==3){$checked3 = 'selected';}

	$html .=  '<option value="0" '.$checked0.'>-</option><option value="1" '.$checked1.'>R</option><option value="2" '.$checked2.'>L</option><option value="3" '.$checked3.'>L+R</option>';
	$html .=  '</select>';

	//$html .= HP.NP1.NP2.Foot;
	$html .= '</div>';
	$html .= "\n";

	/* Ende der Auktion */
	$html .= '<div class="auctionend" style="width: 42%;">';
	$html .= "\n";
	$html .= '<form class="admintrade" id="'.$player['playerID'].'" method ="POST">';
	$html .= "\n";
	$html .= '<select class="transfertrade" id = "ligateamnew'.$player['playerID'].'" name = "newligateam">';
	$html .= "\n";
	foreach ($ligateam as $key => $teamname){
		if ($key != 999){
			if ($key == $player['team']){$select = 'selected = "selected"';}
			else {$select = '';}
			$html .= '<option '.$select.' value="team'.$key.'">'.$teamname.'</option>';
			$html .= "\n";
		}
	}
	$html .= '</select>';
	$html .= '<input type="hidden" id="playerID'.$player['playerID'].'" name="playerID'.$player['playerID'].'" size="10" value="'.$player['playerID'].'">';
	$html .= "\n";

	$html .= '<input onfocus="this.value=\'\'" value="Abl&ouml;se" class="formauction" type="text" id="sum-auction'.$player['playerID'].'" name="sum-auction'.$player['playerID'].'" size="10">';
	$html .= "\n";

	$html .= '<input onfocus="this.value=\'\'" value="externer Transfer" class="formauction" type="text" id="transfer-extern'.$player['playerID'].'" name="transfer-extern'.$player['playerID'].'" size="23">';
	$html .= "\n";

	$html .= '<input class="formauction" type="submit" value="'.AdminTrade.'">';
	$html .= "\n";
	$html .= '</form>';



	$html .= "\n";
	$html .= '</div>';
	$html .= "\n";

	/* Gebotsformular */
	$html .= '<div class="yourbet"style="width: 10%;">';

	$html .= '<a class="statuscheck" id = "status'.$player['ID'].'" href="#">'.StatusNew.'</a>';
	$html .= '<br/>';
	$html .= '<a class="mwcheck" id = "mw'.$player['ID'].'" href="#">'.MWNew.'</a>';

	$html .= '</div>';
	$html .= '</div>';
	return $html;

}




/**
 * Alte Spieler wieder holen
 *
 */
function print_admin_edit_player_old($player, $uliname, $ligateam){
	global $option;

	//echo $player['uliID'];
	//print_r($player);

	$player_age = player_age($player['birthday']);

	$html .= '<div id="editplayer-'.$player['playerID'].'" class="ageshow positionshow teamshow auction team'.$player['team'].' age'.$age.' position'.$player['hp'].' '.$class.'">';

	/* Wappenbild des Bundesligateams */
	$html .= "\n";
	$html .= '<div class="player" style="width: 20%;">';
	$html .= get_ligateam_wappen($player['team'], $ligateam);
	$html .= "\n";

	$html .= '<b><span><a href="#" class="playerinfo" id = "'.$player['playerID'].'">'.$player['name'].'</a></span></b> ';
	// Alter
	$html .= '('.player_age($player['birthday']).')<br/>';
	$html .= $uliname[$player['uliID']].'';
	$htnl .= '';

	/* Div Marker fuer die Spielerinfo */
	$html .= '<div class="marker" id="player-'.$player['playerID'].'"></div>';

	/* Positionen und Fu� */
	$html .= '</div>';

	/* aktuelles H�chstgebot */
	/* Hier umschlie�t ein DIV die Angaben damit aktualisiert werden kann */
	$html .= "\n";
	$html .= '<div style="width: 20%;" class="auctionbet positions" id ="positions-'.$player['ID'].'">';

	$html .=  '<select id="hp-'.$player['playerID'].'" name="hp-'.$player['playerID'].'">';
	unset ($checked1);unset ($checked2);unset ($checked3);unset ($checked4);unset ($checked5);unset ($checked6);unset ($checked7);
	if ($player['hp']==1){$checked1 = 'selected';}
	if ($player['hp']==2){$checked2 = 'selected';}
	if ($player['hp']==3){$checked3 = 'selected';}
	if ($player['hp']==4){$checked4 = 'selected';}
	if ($player['hp']==5){$checked5 = 'selected';}
	if ($player['hp']==6){$checked6 = 'selected';}
	if ($player['hp']==7){$checked7 = 'selected';}
	$html .=  '<option value="0" '.$checked0.'>?</option><option value="1" '.$checked1.'>TW</option><option value="2" '.$checked2.'>AV</option><option value="3" '.$checked3.'>IV</option><option value="4" '.$checked4.'>ZMF</option><option value="5" '.$checked5.'>FMF</option><option value="6" '.$checked6.'>S2</option><option value="7" '.$checked7.'>MS</option>';
	$html .=  '</select>';

	$html .=  '<select id = "np1-'.$player['playerID'].'" name="np1-'.$player['playerID'].'">';
	unset($checked0);unset($checked1);unset ($checked2);unset ($checked3);unset ($checked4);unset ($checked5);unset ($checked6);unset ($checked7);
	if ($player['np1']==0){$checked0 = 'selected';}
	if ($player['np1']==1){$checked1 = 'selected';}
	if ($player['np1']==2){$checked2 = 'selected';}
	if ($player['np1']==3){$checked3 = 'selected';}
	if ($player['np1']==4){$checked4 = 'selected';}
	if ($player['np1']==5){$checked5 = 'selected';}
	if ($player['np1']==6){$checked6 = 'selected';}
	if ($player['np1']==7){$checked7 = 'selected';}
	$html .=  '<option value="0" '.$checked0.'>-</option><option value="1" '.$checked1.'>TW</option><option value="2" '.$checked2.'>AV</option><option value="3" '.$checked3.'>IV</option><option value="4" '.$checked4.'>ZMF</option><option value="5" '.$checked5.'>FMF</option><option value="6" '.$checked6.'>S2</option><option value="7" '.$checked7.'>MS</option>';
	$html .=  '</select>';

	$html .=  '<select id = "np2-'.$player['playerID'].'" name="np2-'.$player['playerID'].'">';
	unset($checked0);unset($checked1);unset ($checked2);unset ($checked3);unset ($checked4);unset ($checked5);unset ($checked6);unset ($checked7);
	if ($player['np2']==0){$checked0 = 'selected';}
	if ($player['np2']==1){$checked1 = 'selected';}
	if ($player['np2']==2){$checked2 = 'selected';}
	if ($player['np2']==3){$checked3 = 'selected';}
	if ($player['np2']==4){$checked4 = 'selected';}
	if ($player['np2']==5){$checked5 = 'selected';}
	if ($player['np2']==6){$checked6 = 'selected';}
	if ($player['np2']==7){$checked7 = 'selected';}
	$html .=  '<option value="0" '.$checked0.'>-</option><option value="1" '.$checked1.'>TW</option><option value="2" '.$checked2.'>AV</option><option value="3" '.$checked3.'>IV</option><option value="4" '.$checked4.'>ZMF</option><option value="5" '.$checked5.'>FMF</option><option value="6" '.$checked6.'>S2</option><option value="7" '.$checked7.'>MS</option>';
	$html .=  '</select>';

	$html .=  '<select id = "foot-'.$player['playerID'].'" name="foot-'.$player['playerID'].'">';
	unset($checked1);unset ($checked2);unset ($checked3);
	if ($player['foot']==1){$checked1 = 'selected';}
	if ($player['foot']==2){$checked2 = 'selected';}
	if ($player['foot']==3){$checked3 = 'selected';}

	$html .=  '<option value="1" '.$checked1.'>R</option><option value="2" '.$checked2.'>L</option><option value="3" '.$checked3.'>L+R</option>';
	$html .=  '</select>';

	//$html .= HP.NP1.NP2.Foot;
	$html .= '</div>';
	$html .= "\n";

	/* Ende der Auktion */
	$html .= '<div class="auctionend" style="width: 42%;">';
	$html .= "\n";
	$html .= '<form class="admintrade" id="'.$player['playerID'].'" method ="POST">';
	$html .= "\n";
	$html .= '<select class="transfertrade" id = "ligateamnew'.$player['playerID'].'" name = "newligateam">';
	$html .= "\n";
	foreach ($ligateam as $key => $teamname){
		if ($key != 999){
			if ($key == $player['team']){$select = 'selected = "selected"';}
			else {$select = '';}
			$html .= '<option '.$select.' value="team'.$key.'">'.$teamname.'</option>';
			$html .= "\n";
		}
	}
	$html .= '</select>';
	$html .= '<input type="hidden" id="playerID'.$player['playerID'].'" name="playerID'.$player['playerID'].'" size="10" value="'.$player['playerID'].'">';
	$html .= "\n";

	$html .= '<input class="formauction" type="submit" value="'.Reactivate.'">';
	$html .= "\n";
	$html .= '</form>';



	$html .= "\n";
	$html .= '</div>';
	$html .= "\n";

	/* Gebotsformular */
	$html .= '</div>';
	return $html;

}



/**
 * Die Moeglichkeit ein ganzes Team absteigen zu lassen
 * 22.7.2013
 * aktualisiert auf neue Trade funktion
 * 14.8.16 alle Ligen
 */
function abstieg_players($team) {
	global $wpdb;
	$leagues = get_leagues();
	foreach ($leagues as $league){
		$leagueID = $league['ID'];
		$sql = 'SELECT * FROM tip_uli_player '.
				' WHERE team = "'.$team.'" ';
		$resulttwo = $wpdb->get_results($sql,ARRAY_A);
		if ($resulttwo) {
			foreach ($resulttwo as $player){
					$playerinfo = get_player_infos($player['ID'], $leagueID);
					$uliold = $playerinfo['uliID'];
					//$leagueID = 1;
					echo $playerinfo['name'].' '.$playerinfo['uliID'].'<br>';
					$admintrade['sum'] = 0;
					$admintrade['externnew'] = "2. Liga";
					trade_player($player['ID'], $leagueID, $auction = NULL, $contract = NULL, $admintrade);
				}
		}
	}
}



function calculate_uli($round, $year) {
	global $wpdb;

	$sql = 	'SELECT * FROM tip_uli ';
	$ulis = $wpdb->get_results($sql,ARRAY_A);
	if($ulis){
		foreach ($ulis as $uli){
			$points = calculate_uli_points($round, $year, $uli['ID']);
			write_uli_points($uli['ID'], $round, $year, $points);

			// Gesamstand
			$sql = 	'SELECT * FROM tip_uli_results'.
			' WHERE uliID = "'.$uli['ID'].'"'.
			' AND year = "'.$year.'"'.
			' AND round != 0';
			$result = $wpdb->get_results($sql,ARRAY_A);
			unset($points);
			// den gesamtstand ausrechnen
			/////////////////////////////
			if($result){
				foreach ($result as $point){
					$points = $points + $point['score'];
					write_uli_points($uli['ID'], 0, $year, $points);
				}}
		}}
		calculate_position_uli($year, $round);

}
/////////////////////////////////////////

function calculate_uli_points($round, $year, $uliID) {
	global $wpdb, $CONFIG;
	$sql = 	'SELECT SUM(points) FROM tip_uli_userteams '.
		' WHERE uliID = '.$uliID.' '.
		' AND round = '.$round.' '.
		' AND year = "'.$year.'" ';
	$points = $wpdb->get_var($sql);
	return $points;
}
/////////////////////////////////////////

function write_uli_points($uliID, $round, $year, $points) {
	global $CONFIG, $wpdb;
	$leagueID = get_leagueID($uliID);
	if (!check_uli_results($uliID, $round, $year)) {
		$sql  = 'INSERT INTO tip_uli_results '.
			'(uliID, score, round, year, leagueID) VALUES ('.
			'"'.$uliID.'",'.
			'"'.$points.'",'.
			'"'.$round.'",'.
			'"'.$year.'",'.
			'"'.$leagueID.'"'.
			')';
	}
	else {
		$sql = 'UPDATE tip_uli_results SET '.
		' score 	 = "'.$points. '", '.
		' leagueID 	 = "'.$leagueID. '" '.
		' WHERE uliID = '.$uliID.
		' AND round   = '.$round.
		' AND year = "'.$year.'"';
	}
	if ($wpdb->query($sql)){}
}
///////////////////////////////


function check_uli_player_points($playerID, $round, $year) {
	global $wpdb, $CONFIG;

	$sql = 	'SELECT * FROM tip_uli_playerpoints '.
		'WHERE playerID = "'.$playerID. '" '.
		'AND round = "'.$round. '" '.
		'AND year = "'.$year.'" ';
	// echo $sql.'<br>';

	if($wpdb->get_var($sql)){return TRUE;}
	else {return FALSE;}
}

///////////////////////////////

function check_uli_results($uliID, $round, $year) {
	global $wpdb, $CONFIG;

	$sql = 	'SELECT * FROM tip_uli_results '.
		'WHERE uliID = "'.$uliID. '" '.
		'AND round = "'.$round. '" '.
		'AND year = "'.$year.'" ';
	if($wpdb->get_var($sql)){return TRUE;}
	else {return FALSE;}
}




function write_player_points_to_userteams($round){
	global $CONFIG, $wpdb;
	$year = $CONFIG->currentyear;

	// gruppiert nach Managern
	$sql = 	'SELECT * FROM tip_uli';
	$result = $wpdb->get_results($sql, ARRAY_A);
	if ($result){
		foreach ($result as $uli){
			unset($formation);
			$formation = get_uli_user_formation($round, $year, $uli['ID']);
			echo '<h3>'.$uli['uliname'].' - '.$formation.'</h3>';
			// Alle Spieler der Useraustellung einlesen
			$sql = 	'SELECT * FROM tip_uli_userteams '.
				' WHERE round = '.$round.' '.
				' AND year ="'.$year.'"'.
				' AND uliID = '.$uli['ID'].' ORDER by number';
			$result = $wpdb->get_results($sql, ARRAY_A);
			if ($result){
				foreach ($result as $player){
					// schauen ob es eine formation gibt
					if (!$formation) {echo 'KEINE AUFSTELLUNG GEFUNDEN.';}
					if ($formation){
						$playerinfo = get_player_infos($player['playerID'], array('player'));
						// POSITIONEN Zuordnung und Faktoren
						// $number =
						// hp, np1, np2, foot
						// formation
						$position = get_formation_position($formation, $player['number']);
						unset ($faktor);
						// HP und Fu� nicht wichtig
						if ($position->position == $playerinfo['hp'] AND !$position->foot){$faktor = 1;}
						// HP und Fu� wichtig Fu� stimmt oder beidf��ig
						elseif ($position->position == $playerinfo['hp'] AND $position->foot AND ($position->foot == $playerinfo['foot'] OR $playerinfo['foot'] == 3)){$faktor = 1;}
						// HP richtig Fu� falsch
						elseif ($position->position == $playerinfo['hp'] AND $position->foot AND $position->foot != $playerinfo['foot'] AND $playerinfo['foot'] != 3){$faktor = 0.5;}
						// NP1 und Fu� nicht wichtig
						elseif ($position->position == $playerinfo['np1'] AND !$position->foot){$faktor = 0.75;}
						// NP1 und Fu� wichtig Fu� stimmt oder beidf��ig
						elseif ($position->position == $playerinfo['np1'] AND $position->foot AND ($position->foot == $playerinfo['foot'] OR $playerinfo['foot'] == 3)){$faktor = 0.75;}
						// NP2 und Fu� nicht wichtig
						elseif ($position->position == $playerinfo['np2'] AND !$position->foot){$faktor = 0.75;}
						// NP2 und Fu� wichtig Fu� stimmt oder beidf��ig
						elseif ($position->position == $playerinfo['np2'] AND $position->foot AND ($position->foot == $playerinfo['foot'] OR $playerinfo['foot'] == 3)){$faktor = 0.75;}
						else {$faktor = 0.25;}

						if ($player['number'] == 12 OR $player['number'] == 13 OR $player['number'] == 14){$faktor = 0.5;}
						if ($player['number'] == 15){$faktor = 1;}

						$points = get_player_points($player['playerID'], $round, $year);
						$score = $points * $faktor;
						$score = round($score, 2);

						echo $player['number'].' '.$position->position.' '.$position->foot.' - '.$playerinfo['name'].' '.$faktor.': <b>'.$points.' bzw. '.$score.'</b><br>';
						// schreibt die ausgerechneten punkte direkt in die aufstellung
						update_single_value_allgemein('uli_userteams','POINTS', $score, 'ID', $player['ID']);
					}
				}
			}
		}
	}
}





/**
 * aktualisiert die tabelle player_league_games
 * @return unknown_type
 */
function update_league_games(){

	// Tabelle leeren
	uli_delete_record('player_league_games');

	$fields = array("COUNT(ID) as games", "leagueID", "playerID", "uliID", "SUM(points) as points");

	$leagues = get_leagues();
	foreach ($leagues as $league){
		$ulis = get_ulis($league['ID']);
		foreach ($ulis as $entry){
			$uli[$entry['ID']] = $league['ID'];
		}
	}

	//print_r($uli);
	unset($cond);
	$cond[] = array("col" => "playerID", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "number", "value" => 12, "func" => "<");
	$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
	$result = uli_get_results('userteams', $cond, $fields, NULL, '', 'GROUP by uliID, playerID');
	if ($result){
		foreach($result as $result){
			unset($values);
			$values[] = array("col" => "playerID", "value" => $result['playerID']);
			$values[] = array("col" => "leagueID", "value" => $uli[$result['uliID']]);
			$values[] = array("col" => "uliID", "value" => $result['uliID']);
			$values[] = array("col" => "games", "value" => $result['games']);
			$values[] = array("col" => "status", "value" => 1);
			$values[] = array("col" => "points", "value" => $result['points']);
			uli_insert_record('player_league_games', $values);
		}
	}

	unset($cond);
	$cond[] = array("col" => "playerID", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "number", "value" => 15);
	$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
	$result = uli_get_results('userteams', $cond, $fields, NULL, '', 'GROUP by uliID, playerID');
	if ($result){
		foreach($result as $result){
			unset($values);
			$values[] = array("col" => "playerID", "value" => $result['playerID']);
			$values[] = array("col" => "leagueID", "value" => $uli[$result['uliID']]);
			$values[] = array("col" => "uliID", "value" => $result['uliID']);
			$values[] = array("col" => "games", "value" => $result['games']);
			$values[] = array("col" => "status", "value" => 2);
			$values[] = array("col" => "points", "value" => $result['points']);
			uli_insert_record('player_league_games', $values);
		}
	}

	unset($cond);
	$cond[] = array("col" => "playerID", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "number", "value" => 11, "func" => ">");
	$cond[] = array("col" => "number", "value" => 15, "func" => "<");
	$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
	$result = uli_get_results('userteams', $cond, $fields, NULL, '', 'GROUP by uliID, playerID');
	if ($result){
		foreach($result as $result){
			unset($values);
			$values[] = array("col" => "playerID", "value" => $result['playerID']);
			$values[] = array("col" => "leagueID", "value" => $uli[$result['uliID']]);
			$values[] = array("col" => "uliID", "value" => $result['uliID']);
			$values[] = array("col" => "games", "value" => $result['games']);
			$values[] = array("col" => "status", "value" => 1);
			$values[] = array("col" => "points", "value" => $result['points']);
			uli_insert_record('player_league_games', $values);
		}
	}

	$html = 'Fertig';
	return $html;
}


/*
 * erst einmal nur das stadion checken
 */
function check_finances($leagueID = '') {
	global $option;

	$leagues = get_leagues();
	foreach ($leagues as $league){
		$leagueID = $league['ID'];

		$ulis = get_ulis($leagueID);
		if ($ulis){
			print_r($ulis);
			foreach ($ulis as $uli){
				/*
				$sum = 0;
				$stadium = get_stadium($uli['ID']);
				if ($stadium['infra']){
					foreach ($stadium['infra'] as $infra){
						$sum = $infra['sum'] + $sum;
					}
				}
				$sum = $sum + ($stadium['sitzplaetze'] / 5000 * 8000000) + ($stadium['stehplaetze'] / 8000 * 6000000) - 8000000;
				echo $uli['ID'].': '.$sum.' | ';
				$cond = array();
				$cond[] = array("col" => "uliID", "value" => $uli['ID']);
				$cond[] = array("col" => "type", "value" => 12);
				$cond[] = array("col" => "round", "value" => 0);
				echo $financeentry = uli_get_var('finances', $cond, 'SUM(sum)');
				if ($financeentry != $sum){
					echo 'PROBLEM';
					//repair($uli['ID'], 12, $sum, 18);
				}
				echo '<br/>';
				*/

			check_finances2($uli['ID'], FALSE, $league);
			}




		}
	}

}


/*
  * TODO
  * nachrechnen der sponsoring und tv einnahmen
  */
function check_finances2($uliID, $repair, $league = '') {
global $wpdb, $CONFIG;
//$CONFIG->prefix = "tip_";

$uli = get_uli($uliID);
// Check von allen Posten pro Jahr
echo '<h3>'.$uli['uliname'].'</h3>';
// holt alle jahre

$uliyears = get_uli_years();
if ($uliyears){
	foreach ($uliyears as $uliyear){
	$year = $uliyear['ID'];
	$seasonstart = $uliyear['start'];
	$seasonend = $uliyear['end'];
	$type = array(1,2,13,15,17,20,21,27);
	foreach ($type as $key => $type){
		$value='';
		$value_gesamt='';
		$style='';
		$sql = 	'SELECT SUM(sum) FROM tip_uli_finances '.
				' WHERE uliID = '.$uliID.' '.
				' AND year = '.$year.' '.
				' AND type = '.$type.' '.
				' AND round != 0';
		$value = $wpdb->get_var($sql);
		$value_gesamt = get_value_bank($type, 0, $year, $uliID);
		settype($value, INT);
		settype($value_gesamt, INT);
		if ($value != $value_gesamt){
			repair($uliID, $type, $value, $year);
			echo '<p>'.$uliyear['name'].'</p>';
			echo '<b><p>Type '.$type.' '.$uliyear['name'].'</b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($value).'</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type='.$type.'&uliID='.$uliID.'&sum='.$value.'&year='.$year.'">Gesamtwert: '.uli_money($value_gesamt).' ... Repariert</a></p>';
			}
		else {//echo 'Type: '.$type.' Ok.<BR>';
			}
		}



	// Stadion
	$stadionAusgabenGesamt = get_value_bank(12, 0, $year, $uliID);
	$sql = 	'SELECT SUM(seats) FROM tip_uli_stadium_seats '.
				' WHERE uliID = '.$uliID.' '.
				' AND built < '.$seasonend.' AND built > '.$seasonstart.' AND block != "A1" AND type_of_seats = 1';
	$sitzplaetze = $wpdb->get_var($sql);
	settype($sitzplaetze, "INT");

	$sql = 	'SELECT SUM(seats) FROM tip_uli_stadium_seats '.
				' WHERE uliID = '.$uliID.' '.
				' AND built < '.$seasonend.' AND built > '.$seasonstart.' AND block != "A1" AND type_of_seats = 2';
	$stehplaetze = $wpdb->get_var($sql);
	settype($stehplaetze, "INT");

	$sql = 	'SELECT SUM(sum) FROM tip_uli_stadium_infra '.
				' WHERE uliID = '.$uliID.' '.
				' AND built < '.$seasonend.' AND built > '.$seasonstart.' ';
	$infra = $wpdb->get_var($sql);

	echo $stadionAusgaben = ($sitzplaetze / 5000 * 8000000) + ($stehplaetze / 8000 * 6000000) + $infra;
	echo '<br>';
	if ($stadionAusgaben != $stadionAusgabenGesamt){
	repair($uliID, 12, $stadionAusgaben, $year);
	echo '<b><p>Stadion '.$uliyear['name'].'</b></p>';
	echo '<p>Summe der Einzelwerte: '.uli_money($stadionAusgaben).'</p>';
	$style = 'style = "background-color: red;"';
	echo '<p '.$style.'><a href="?action=repair&type=5&uliID='.$uliID.'&sum='.$zinsen.'&year='.$year.'">Gesamtwert: '.uli_money($stadionAusgabenGesamt).'</a></p>';
	}

	// �berpr�fen der Kredite
	$result='';
	$zinsen_gesamt='';
	$zinsen='';
		$sql = 	'SELECT * FROM tip_uli_kredite '.
				'WHERE toklub  = '.$uliID.' '.
				' AND paid = 1 '.
				' AND end < '.$seasonend.' AND end > '.$seasonstart.' ';
		$result = $wpdb->get_results($sql,ARRAY_A);
		if ($result){
		foreach ($result as $kredit){
			$years = ($kredit['end'] - $kredit['start']) / 60 / 60 / 24 / 365;
			$zinsen = $zinsen + ($kredit['sum'] / 100 * $kredit['percent'] * $years);
		}}
		$zinsen_gesamt = get_value_bank(5, 0, $year, $uliID);
		settype($zinsen, INT);
		settype($zinsen_gesamt, INT);



		if ($zinsen != $zinsen_gesamt){
			repair($uliID, 5, $zinsen, $year);
			echo '<b><p>Kreditzinsen '.$uliyear['name'].'</b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($zinsen).'</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type=5&uliID='.$uliID.'&sum='.$zinsen.'&year='.$year.'">Gesamtwert: '.uli_money($zinsen_gesamt).'</a></p>';
			}





	// �berpr�fen der Transfereinnahmen
	unset($result);
	$t_einnahmen='';
	$t_einnahmen_gesamt='';
		$sql = 'SELECT SUM(sum) FROM tip_uli_transfers '.
				'WHERE uliold  = '.$uliID.' '.
				' '.
				' AND time < '.$seasonend.' AND time > '.$seasonstart.' ';
		$t_einnahmen = $wpdb->get_var($sql);
		$t_einnahmen_gesamt = get_value_bank(10, 0, $year, $uliID);
		settype($t_einnahmen, INT);
		settype($t_einnahmen_gesamt, INT);
		if ($t_einnahmen != $t_einnahmen_gesamt){
			repair($uliID, 10, $t_einnahmen, $year);
			echo '<b><p>Transfereinnahmen '.$uliyear['name'].'</b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($t_einnahmen).'</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type=10&uliID='.$uliID.'&sum='.$t_einnahmen.'&year='.$year.'">Gesamtwert: '.uli_money($t_einnahmen_gesamt).'</a></p>';
			}
		else {//echo 'Transferseinnahmen ok<br/>';
		}
	unset($result);
	$t_ausgaben='';
	$t_ausgaben_gesamt='';
		$sql = 	'SELECT SUM(sum) FROM tip_uli_transfers '.
				'WHERE ulinew  = '.$uliID.' '.
				' '.
				' AND time < '.$seasonend.' AND time > '.$seasonstart.' ';
		$t_ausgaben = $wpdb->get_var($sql);
		$t_ausgaben_gesamt = get_value_bank(11, 0, $year, $uliID);
		settype($t_ausgaben, INT);
		settype($t_ausgaben_gesamt, INT);
		if ($t_ausgaben != $t_ausgaben_gesamt){
			repair($uliID, 11, $t_ausgaben, $year);
			echo '<b><p>Transferausgaben '.$uliyear['name'].'</b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($t_ausgaben).'</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type=11&uliID='.$uliID.'&sum='.$t_ausgaben.'&year='.$year.'">Gesamtwert: '.uli_money($t_ausgaben_gesamt).'</a></p>';
			}
		else {//echo 'Transferausgaben ok<br/>';
		}

	// �berpr�fen der Sponsoring-Einnahmen
	if ($year >= 6){
		/*
		 18 = Sponsoring Gesamt (0)
		 19 = Sponsoring Base
		 20 = Sponsoring pro Punkt (0)
		 21 = Sponsoring pr�mie
		 22 = Sponsoring Audience (0)
		 23 = Sponsoring Meister (0)
		 24 = Sponsoring Top 5 (0)
		*/
		// 	echo $SponsGesamt= get_value_bank(18, 0, $year, $uliID);

		$Basis 		= get_value_bank(19, 0, $year, $uliID);
		$PerPoint 	= get_value_bank(20, 0, $year, $uliID);
		$Top5 		= get_value_bank(24, 0, $year, $uliID);
		$Zuschauer 	= get_value_bank(22, 0, $year, $uliID);
		$Meister 	= get_value_bank(23, 0, $year, $uliID);
		$praemie 	= get_value_bank(21, 0, $year, $uliID);


		if ($year == 12){
			$sql = 	'SELECT SUM(base) FROM tip_uli_sponsoring WHERE team_ID = '.$uliID.' AND status = 3 and year IN (13,14)';
			$BaseSum = $wpdb->get_var($sql);
			if ($Basis != $BaseSum){
				repair($uliID, 19, $BaseSum, 12);
				echo 'SPONSERROR';
			}

		}
		if ($year == 15){
			$sql = 	'SELECT SUM(base) FROM tip_uli_sponsoring WHERE team_ID = '.$uliID.' AND status = 3 and year IN (16,17)';
			$BaseSum = $wpdb->get_var($sql);
			if ($Basis != $BaseSum){
				repair($uliID, 19, $BaseSum, 15);
				echo 'SPONSERROR';
			}

		}

	}

	// �berpr�fen der Ausgaben

	//if ($value == "26" OR $value == "2" OR $value == "6" OR $value == "10" OR $value == "15" OR $value == "16" OR $value == "17" OR $value == "18" OR $value == "19" OR $value == "20" OR $value == "21" OR $value == "22" OR $value == "23" OR $value == "24" OR $value == "26"){$type = "income";}
	//if ($value == "1" OR $value == "5" OR $value == "11" OR $value == "12" OR $value == "13"){$type =	"outgoings";}



	$types_ausgaben = array(1,5,11,12,13);
	$types_einnahmen = array(2,6,10,15,16,17,19,20,21,22,23,24,25,26,27);

	$ausgaben_single='';
	foreach ($types_ausgaben as $key => $types_ausgaben){
		$ausgaben_single = $ausgaben_single + get_value_bank($types_ausgaben, 0, $year, $uliID);
	}
	$ausgaben_gesamt = get_value_bank(9, 0, $year, $uliID);
	settype($ausgaben_single, INT);
	settype($ausgaben_gesamt, INT);
		if ($ausgaben_single != $ausgaben_gesamt){
			repair($uliID, 9, $ausgaben_single, $year);
			echo '<b><p>AUSGABEN '.$uliyear['name'].'</b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($ausgaben_single).'</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type=9&uliID='.$uliID.'&sum='.$ausgaben_single.'&year='.$year.'">Gesamtwert: '.uli_money($ausgaben_gesamt).'</a></p>';
			}


	/////////////////////////////////////////////////////////////////////
	// �berpr�fen der Einnahmen
	$einnahmen_single='';
	foreach ($types_einnahmen as $key => $types_einnahmen){
		$einnahmen_single = $einnahmen_single + get_value_bank($types_einnahmen, 0, $year, $uliID);
	}
	$einnahmen_gesamt = get_value_bank(8, 0, $year, $uliID);
	settype($einnahmen_single, INT);
	settype($einnahmen_gesamt, INT);
		if ($einnahmen_single != $einnahmen_gesamt){
			repair($uliID, 8, $einnahmen_single, $year);
			echo '<b><p>EINNAHMEN '.$uliyear['name'].'</b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($einnahmen_single).'</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type=8&uliID='.$uliID.'&sum='.$einnahmen_single.'&year='.$year.'">Gesamtwert: '.uli_money($einnahmen_gesamt).'</a></p>';
			}
	/////////////////////////////////////////////////////////////////////
	// �berpr�fen des Saldo Ausgaben
	$saldo_single = get_value_bank(8, 0, $year, $uliID) - get_value_bank(9, 0, $year, $uliID);
	$saldo_gesamt = get_value_bank(7, 0, $year, $uliID);
	settype($saldo_single, INT);
	settype($saldo_gesamt, INT);
		if ($saldo_single != $saldo_gesamt){
			repair($uliID, 7, $saldo_single, $year);
			echo '<b><p>SALDO '.$uliyear['name'].'</b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($saldo_single).'</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type=7&uliID='.$uliID.'&sum='.$saldo_single.'&year='.$year.'">Gesamtwert: '.uli_money($saldo_gesamt).'</a></p>';
			}
	/////////////////////////////////////////////////////////////////////
	}

	// �berpr�fen des Gesamtkontostandes
	$saldo='';
	foreach ($uliyears as $uliyear){
		$saldo = $saldo + get_value_bank(7, 0, $uliyear['ID'], $uliID);
		}
	$guthaben = get_value_bank(14, 0, 0, $uliID);
	$sollguthaben = $saldo + 50000000 + get_all_kredite($uliID);
	settype($sollguthaben, INT);
	settype($guthaben, INT);
		if ($guthaben !=  $sollguthaben){

			repair($uliID, 14, $sollguthaben, 0);
			echo '<b><p>GUTHABEN </b></p>';
			echo '<p>Summe der Einzelwerte: '.uli_money($sollguthaben).')</p>';
			$style = 'style = "background-color: red;"';
			echo '<p '.$style.'><a href="?action=repair&type=14&uliID='.$uliID.'&sum='.$sollguthaben.'&year=0">Gesamtwert: '.uli_money($guthaben).'</a></p>';
			}
		else {
			echo 'GESAMTKONTOSTAND OK';

		}

}




	/*









	// �berpr�fen der TV-Einnahmen
	if ($year >=3){
	/*
	 16 = TV Einnahmen Jahresbetrag
	 17 = TV Einnahmen Erfolgsabh�ngig
	*/

		/*
		$TVyear 	= get_value_bank(16, 0, $year, $uliID);
		$TVpraemie	= get_value_bank(17, 0, $year, $uliID);
	}



/////////////////////////////////////////////////////////////////////

}


		*/
}


function repair($uliID, $type, $sum, $year){

	if (!get_value_bank($type, 0, $year, $uliID))
			{insert_single_value_finance($type, $sum, $uliID, 0, $year);}
		else {update_single_value_finance($type, $sum, $uliID, 0, $year);}

}


// Zuerst definieren wir die Funktionen, die sp�ter auf
// die diversen Ereignisse reagieren sollen

/**
 * Diese Funktion behandelt ein �ffnendes Element.
 * Alle Parameter werden automatisch vom Parser �bergeben
 *
 * @param    parser    Object    Parserobjekt
 * @param    name      string    Name des �ffnenden Elements
 * @param    atts      array     Array mit Attributen
 */
function startElement($parser, $name, $atts) {
	global $html, $spielerdaten, $ID, $value, $nachname, $vorname, $note, $spieltag;

	// Die XML-Namen werden in Gro�buchstaben �bergeben.
	// Deshalb wandeln wir sie mit strtolower() in Klein-
	// buchstaben um.
	switch (strtolower($name)) {
		case "spielerdaten":
			break;
		case "spieler";
		// echo "<h3>".$atts["ID"]."</h3>";
		$ID = $atts["ID"];
		$value = 'ID';
		// $html .= "<h3>".$atts["ID"]."</h3>";
		break;
		case "name";
		$value = 'name';
		break;
		case "vorname";
		$value = 'vorname';
		break;
		case "geburtsdatum";
		$value = 'birth';
		break;
		case "position";
		$value = 'position';
		break;

		case "verein";
		$value = 'verein';
		break;

		case "groesse";
		$value = 'groesse';
		break;

		case "gewicht";
		$value = 'gewicht';
		break;

		case "nationalitaet";
		$value = 'nationalitaet';
		break;

		case "saison";
		$value = 'saison';
		break;
		case "spieltag";
		$spieltag = $atts['NR'];
		$value = 'spieltag';
		break;
		case "note";
		$value = 'note';
		break;
		default:
			// Ein ung�ltiges Element ist vorgekommen.
			$error = "Undefiniertes Element <".$name.">";
			die($error . " in Zeile " .
			xml_get_current_line_number($parser));
			break;
	}
}
/**
 * Diese Funktion behandelt ein abschlie�endes Element
 * Alle Parameter werden automatisch vom Parser �bergeben
 *
 * @param  parser    Object    Parserobjekt
 * @param  name      string    Name des schlie�enden Elements
 */
function endElement($parser, $name) {
	global $html;

	switch (strtolower($name)) {
		case "ref":
			// Den HTML-Link schlie�en:
			$html .= "</a>";
			break;
	}
}

/**
 * Diese Funktion behandelt normalen Text
 * Alle Parameter werden automatisch vom Parser �bergeben
 *
 * @param    parser    Object    Parserobjekt
 * @param    text      string    Der Text
 */
function cdata($parser, $text) {
	global $html, $spielerdaten, $ID, $value, $nachname, $birth, $vorname,$note, $spieltag, $verein, $groesse, $gewicht, $nationalitaet;

	if ($value == "ID"){
		// $spielerdaten[$ID] = $html;
		$vorname = '';
		$nachname = '';
		$birthday = '';
		$verein = '';
		$groesse = '';
		$gewicht = '';
		$nationalitaet = '';
	}
	if ($value== "spieltag") {
		$note = '';
	}

	if ($value=="name"){
		$nachname .= $text;
		$spielerdaten[$ID]->name = $nachname;
	}
	if ($value=="vorname"){
		$vorname .= $text;
		$spielerdaten[$ID]->vorname = $vorname;
	}

	/* Neu in 2009/10 */
	if ($value=="verein"){
		$verein .= $text;
		$spielerdaten[$ID]->verein= $verein;
	}
	if ($value=="groesse"){
		$groesse .= $text;
		$spielerdaten[$ID]->groesse= $groesse;
	}
	if ($value=="gewicht"){
		$gewicht .= $text;
		$spielerdaten[$ID]->gewicht= $gewicht;
	}
	if ($value=="nationalitaet"){
		$nationalitaet .= $text;
		$spielerdaten[$ID]->nationalitaet = $nationalitaet;
	}

	if ($value=="note"){
		$note .= $text;
		$spielerdaten[$ID]->note[$spieltag] = $note;
	}

	if ($value =="birth"){
		$x = 0;
		// echo $text.'<br>';
		$datum = explode('.',$text);
		$x=0;
		foreach ($datum as $datum){
			$x = $x+1;
			if ($x == 1){$tag = $datum;settype($tag, INT);}
			if ($x == 2){$monat = $datum;settype($monat, INT);}
			if ($x == 3){$jahr = $datum; settype($jahr, INT);}
		}
		if ($x>1){
			$birthday = mktime(0,0,0,$monat, $tag, $jahr);
			// echo $birthday.'<br>';
			$spielerdaten[$ID]->birthday = $birthday;
		}
	}
	// Der normale Text wird einfach an $html angeh�ngt:
	//

	//print_r($spielerdaten);


}






?>
