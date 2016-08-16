<?php
/*
 * Created on 24.03.2009
 *
 *Alle Funktionen, die nur in der Statistik Abteilung gebraucht werden
 *
 // TODO
 // immer marker, wenn es das eigene Team betrifft
 *ALT
 *
 *ALLGEMEIN
 *Tabelle
 *letzte Transfers
 *
 *COMPETITION MODE
 *Spiele
 *Tabelle
 *
 *FINANZEN
 *Lage der Nation
 *Finanzen
 *Gehaltskosten
 *wertvollste Teams
 *meistverkaufte Trikots
 *zuschauerschnitt
 *TV-Vertr�ge
 *Sponsoren
 *
 *SPIELER
 *Rekord Transfers
 *wertvollsten Spieler
 *beste/schlechteste Spieler /Jahr/Gesamt
 *meisten Spiele
 *Kicker Stars
 *
 *MEINE STATISTIKEN
 *meine Transfers
 *meine Besten
 *Eins�tze
 *Zuschauer
 *Geh�lter
 *
 *
 *
 *
 *
 *NEU
 *
 *AKTUELL
 *Tabelle
 *ewige Tabelle
 *letzer Spieltag
 *letzte Transfers
 *
 *SPIELER
 *Elf des Tages
 *Elf der Saison
 *meisten Spiele
 *beste Spieler
 *TOP-Verdiener
 *Kicker Stars
 *Rekord Transfers
 *Trikot
 *Wanderv�gel
 *Transfererl�se
 *
 *
 *TEAMS
 *Marktwert
 *Durchschnittsalter
 *Gehalt (ca.)
 *Punkteschnitt
 *
 *Statistiken pro Team:
 *- Alter
 *- Punkte
 *- teuerster Einkauf
 *- letzter Transfer
 *- Summe Transfers
 *- ...
 *- ...
 *
 *LIGA
 *TV Vertr�ge
 *Sponsoren
 *FINANZEN (ca.)
 *Zuschauerschnitt
 *gr��te Stadien
 *
 *MEINE STATISTIKEN
 *Transfers
 *Punkte
 *
 *
 *
 *
 *
 *
 *
 *
 */


/* Bindet die Sprachdatei ein */
include('lang_stats.php');
include('../_stadion/lib_stadion.php');

function print_stats($view, $round = '', $year = '', $leagueID = ''){
	global $option;


	if ($view=="stadien"){
		//$ligateam = get_all_team_names();


		$cond[] = array("col" => "uliID", "value" => 0, "func" => "!=");
		$order[] = array("col" => "SUM(seats)", "sort" => "DESC");
		$result = uli_get_results('stadium_seats', $cond, array('uliID', 'SUM(seats) as seats'), $order, NULL, 'GROUP BY uliID');




		if ($result) {
			$colh[1] = UliTeam;
			$colh[2] = "Liga";
			$colh[3] = "Name";

			$colh[4] = "Plätze (Sitz-/Stehplätze)";
			$colh[5] = "Ausstattung";

			$leagues = get_leagues();
			foreach ($leagues as $league){
				$leaguenames[$league['ID']] = $league['name'];
			}

			$x = 1;
			foreach ($result as $result) {
				$stadium = get_stadium($result['uliID']);
				$uli = get_uli($result['uliID']);
				$sum = 0;
				if ($stadium['infra']){
					foreach ($stadium['infra'] as $infra){
						$sum = $infra['sum'] + $sum;
					}
				}
				if ($sum > 20000000){
					$ausstattung = "Ein Tempel";
				} else if ($sum > 10000000){
					$ausstattung = "Bundesliganiveau";
				}
				else if ($sum > 5000000){
					$ausstattung = "Passt schon.";
				}
				else if ($sum > 2000000){
					$ausstattung = "Investitionsstau";
				} else {
					$ausstattung = "Eine Ruine mit Rasen.";
				}


				$data[$x][] = $uli['uliname']. ' ('.get_user_name($uli['userID']).')';
				$data[$x][] = $leaguenames[$uli['leagueID']];
				$data[$x][] = $stadium[0]['name'];
				$data[$x][] = number_format($stadium['sitzplaetze'] + $stadium['stehplaetze'], 0, '','.').' <br/>('.number_format($stadium['sitzplaetze'], 0, '','.').'/'.number_format($stadium['stehplaetze'], 0, '','.').')';
				$data[$x][] = $ausstattung;

				$x = $x + 1;
			}
		}

	}


	if ($view == "whoiswho"){
		if (!$leagueID){$leagueID = $option['leagueID'];}
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);
		echo print_year_round_menu('', '', $leagueID, true);
		$ulis = get_ulis($leagueID);
		//$ligateam = get_all_team_names();
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$order[] = array("col" => "uliname", "sort" => "ASC");
		$result = uli_get_results('uli', $cond, NULL, $order);


		if ($result) {
			$colh[1] = UliTeam;
			$colh[2] = Manager;

			$x = 1;
			foreach ($result as $result) {
				$data[$x][] = '<a class="showteam" id = "'.$result['ID'].'" href="#">'.$result['uliname']. '</a>';
				$data[$x][] = get_user_name($result['userID']);
				$x = $x + 1;
			}
		}
	}


	if ($view == "games"){
		$uliname = get_all_uli_names($option['leagueID']);
		echo print_year_round_menu($round, $year);

		if ($round > 0){
			$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
			$cond[] = array("col" => "year", "value" => $year);
			$cond[] = array("col" => "round", "value" => $round);
			$result = uli_get_results('games', $cond);
			//print_r($result);
			if ($result) {
				$colh[1] = Team1;
				$colh[2] = Ergebnis;
				$colh[3] = Team2;

				$x = 1;
				foreach ($result as $result) {

					$data[$x][] = $uliname[$result['team1']];
					$data[$x][] = $result['team1score'].' : '.$result['team2score'];
					$data[$x][] = $uliname[$result['team2']];
					$x = $x + 1;
				}
			}
			if ($colh AND $data){
				$content .= uli_table($colh, $data, '');
			}

		}

		unset($cond);
		unset($colh);
		unset($data);
		$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
		$cond[] = array("col" => "year", "value" => $year);
		$cond[] = array("col" => "uliID", "value" => 126, "func" => "!=");
		$order[] = array("col" => "points", "sort" => "DESC");
		$order[] = array("col" => "g_diff", "sort" => "DESC");
		$order[] = array("col" => "g_scored", "sort" => "DESC");
		$result = uli_get_results('games_table', $cond, NULL, $order);
		if ($result) {
			$colh[1] = Place;
			$colh[2] = UliTeam;
			$colh[3] = Punkte;
			$colh[4] = Tordifferenz;
			$colh[5] = Tore;
			$colh[6] = Gegentore;

			$x = 1;
			foreach ($result as $result) {

				$data[$x][] = $x.'.';
				$data[$x][] = '<a class="showteam" id = "'.$result['uliID'].'" href="#">'.$uliname[$result['uliID']]. '</a>';
				$data[$x][] = round($result['points']);
				$data[$x][] = round($result['g_diff']);
				$data[$x][] = round($result['g_scored']);
				$data[$x][] = round($result['g_against']);
				$x = $x + 1;
			}
		}
	}


	if ($view == "vermoegen"){
		if (!$leagueID){$leagueID = $option['leagueID'];}
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);
		echo print_year_round_menu('', '', $leagueID, true);
		$ulis = get_ulis($leagueID);
		foreach($ulis as $uli){
			$guthaben    = get_value_bank(14, 0, 0, $uli['ID']);
			$kredite     = get_all_kredite($uli['ID']);
			$vermoegen   = $guthaben - $kredite;
			$ulivermoegen[$uli['uliname']] = $vermoegen;
		}
		arsort($ulivermoegen);

		/*
		 * AAA		+80 Mio
		 * AA+		+50 Mio
		 * AA		+30
		 * A		+10
		 * BBB		+0
		 * B		-10
		 * CCC		-30
		 * DDD		<-30
		 */
		$colh[1] = Rating;
		$colh[2] = Verein;
		$x = 1;
		foreach ($ulivermoegen as $key => $money){
			if ($money > 80000000){$rating = "AAA";}
			elseif ($money > 50000000){$rating = "AA+";}
			elseif ($money > 30000000){$rating = "AA";}
			elseif ($money > 10000000){$rating = "A";}
			elseif ($money > 0){$rating = "BBB";}
			elseif ($money > -10000000){$rating = "B";}
			elseif ($money > -30000000){$rating = "CCC";}
			else{$rating = "DDD";}
			$data[$x][] = $rating;
			$data[$x][] = $key;
			$x = $x + 1;

		}
		$data[$x][] = '<b>'.uli_money(array_sum($ulivermoegen)/1000000).' Mio</b>';
		$data[$x][] = '<b>'.Vermoegengesamt.'</b>';
	}

	if ($view == "goodstreaks"){
		$players = uli_get_results('player');
		//$players = get_players();

		//print_r($players);
		if ($players) {
			foreach ($players as $thisplayer) {
				$player = array();
				$player = get_player_infos($thisplayer['ID'], 1, array('scores'));
					$streak = 0;
					$currentstreak = 0;
				if ($player['scores']){
					$scores = $player['scores'];

					foreach ($scores as $yearscores){
						foreach ($yearscores as $round => $score){
							if ($round != 0){
								if ($score > 2){
									$currentstreak = $currentstreak + 1;

								}
								else {
									if ($streak < $currentstreak){
										$streak = $currentstreak;
									}
									$currentstreak = 0;
								}
							}
						}
					}
				}
				$streaks[$player['playerID']] = $streak;
				//echo $player['name'].' - '.$streak.'<br>';
			}
		}
		arsort($streaks);
			if ($streaks){
			foreach ($streaks as $key => $streak){
				$player = get_player_infos($key);
				echo $player['name'].' ('.$streak.')<br/>';
			}
		}

	}

	if ($view == "gamesinarow"){
		$players = uli_get_results('player');
		//$players = get_players();

		//print_r($players);
		if ($players) {
			foreach ($players as $thisplayer) {
				$player = array();
				$player = get_player_infos($thisplayer['ID'], 1, array('scores'));
					$streak = 0;
					$currentstreak = 0;
					$lastround = 0;
					if ($player['scores']){
					$scores = $player['scores'];
					foreach ($scores as $yearscores){
						foreach ($yearscores as $round => $score){
							if ($round != 0){

								if ($round == ($lastround + 1)){
									$currentstreak = $currentstreak + 1;

								}
								else {
									if ($streak < $currentstreak){
										$streak = $currentstreak;
									}
									$currentstreak = 0;
								}
								$lastround = $round;
								if ($lastround == 34){
									$lastround = 0;
								}
							}
						}
					}
				}
				$streaks[$player['playerID']] = $streak;
				//echo $player['name'].' - '.$streak.'<br>';
			}
		}
		arsort($streaks);
			if ($streaks){
			foreach ($streaks as $key => $streak){
				$player = get_player_infos($key);
				echo $player['name'].' ('.$streak.')<br/>';
			}
		}
	}

	if ($view == "schnitt"){
		$players = uli_get_results('player');
		//print_r($players);
		if ($players) {
			foreach ($players as $thisplayer) {
				$player = array();
				$player = get_player_infos($thisplayer['ID'], 1, array('scores'));
					$streak = 0;
					$currentstreak = 0;
				if ($player['scores']){
					$scores = $player['scores'];
					$playerGames = 0;
					$noteSum = 0;
					foreach ($scores as $yearscores){
						foreach ($yearscores as $round => $score){
							if ($round != 0){
								if ($score == 10){$note = 1;}
								if ($score == 8){$note = 1.5;}
								if ($score == 6){$note = 2;}
								if ($score == 4){$note = 2.5;}
								if ($score == 3){$note = 3;}
								if ($score == 2){$note = 3.5;}
								if ($score == 1){$note = 4;}
								if ($score == 0){$note = 4.5;}
								if ($score == -2){$note = 5;}
								if ($score == -4){$note = 5.5;}
								if ($score == -10){$note = 6;}
								$noteSum = $noteSum + $note;
								$playerGames = $playerGames + 1;
							}
						}
					}

				}
				if ($playerGames > 50){
					$streaks[$player['playerID']] = $noteSum/$playerGames;
					//echo $player['name'].' - '.$streak.'<br>';
				}
			}
		}
		arsort($streaks);
			if ($streaks){
			foreach ($streaks as $key => $streak){
				$player = get_player_infos($key);
				echo $player['name'].' ('.$streak.')<br/>';
			}
		}
	}


	if ($view == "goodstreak"){
		$players = uli_get_results('player');
		//$players = get_players();

		//print_r($players);
		if ($players) {
			foreach ($players as $thisplayer) {
				$player = array();
				$player = get_player_infos($thisplayer['ID'], 1, array('scores'));
					$streak = 0;
					$currentstreak = 0;
				if ($player['scores']){
					$scores = $player['scores'];

					foreach ($scores as $yearscores){
						foreach ($yearscores as $round => $score){
							if ($round != 0){
								if ($score == -10){
									$currentstreak = $currentstreak + 1;

								}
								else {
									if ($streak < $currentstreak){
										$streak = $currentstreak;
									}
									$currentstreak = 0;
								}
							}
						}
					}
				}
				$streaks[$player['playerID']] = $streak;
				//echo $player['name'].' - '.$streak.'<br>';
			}
		}
		arsort($streaks);
			if ($streaks){
			foreach ($streaks as $key => $streak){
				$player = get_player_infos($key);
				echo $player['name'].' ('.$streak.')<br/>';
			}
		}

	}
	if ($view == "34games"){
		$years = get_uli_years();
		foreach ($years as $year) {
			unset($cond);
			$cond[] = array("col" => "year", "value" => $year['ID']);
			$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
			//$cond[] = array("col" => "COUNT (ID)", "value" => 34);
			$order = array("col" => "COUNT (ID)", "sort" => "DESC");
			$result = uli_get_results('player_points', $cond, array('COUNT(ID)', 'playerID', 'year'), NULL, NULL, 'GROUP BY playerID');

			//echo $year['ID'];

			//print_r($result);
			if ($result){
				foreach ($result as $player) {
					if ($player['COUNT(ID)'] == 34) {
						$games[$player['playerID']] = $games[$player['playerID']] + 1;
						//echo $player['year'];
					}
					else {
						//break;
					}
				}


			}
		}
		echo '<br>';
		arsort($games);
		//print_r($games);
		if ($games){
			foreach ($games as $key => $game){
				$player = get_player_infos($key);
				echo $player['name'].' ('.$game.')<br/>';
			}
		}
	}

	if ($view=='tabelle'){
		if (!$round){$round = 0;}
		if (!$year){$year = $option['currentyear'];}
		if (!$leagueID){$leagueID = $option['leagueID'];}
		echo print_year_round_menu($round, $year, $leagueID);
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);
		/* Es werden alle Bundesligateamnamen eingelesen */
		//$ligateam = get_all_team_names();
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$cond[] = array("col" => "year", "value" => $year);
		$cond[] = array("col" => "round", "value" => $round);

		$order[] = array("col" => "score", "sort" => "DESC");
		$result = uli_get_results('results', $cond, NULL, $order);

		if ($year == 99){
			unset($cond);
			$cond[] = array("col" => "leagueID", "value" => $leagueID);
			$cond[] = array("col" => "round", "value" => $round);
			$result = uli_get_results('results', $cond, array('uliID', 'SUM(score) as score'), $order, NULL, 'GROUP BY uliID');

		}

		if ($result) {
			$colh[1] = Place;
			$colh[2] = Punkte;

			$colh[3] = UliTeam;
			$x = 1;
			foreach ($result as $result) {
				$data[$x][] = $x.'.';
				$data[$x][] = round($result['score'], 2);
				$data[$x][] = '<a class="showteam" id = "'.$result['uliID'].'" href="#">'.$uliname[$result['uliID']]. '</a>';
				$x = $x + 1;
			}
		} else {


		}
	}


	if ($view=='bestplayers'){
		if (!$round){$round = 0;}
		if (!$year){$year = $option['currentyear'];}
		if (!$leagueID){$leagueID = $option['leagueID'];}
		echo print_year_round_menu($round, $year, $leagueID);
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);
		/* Es werden alle Bundesligateamnamen eingelesen */
		//$ligateam = get_all_team_names();
		//$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
		$cond[] = array("col" => "year", "value" => $year);
		$cond[] = array("col" => "round", "value" => $round);

		$order[] = array("col" => "score", "sort" => "DESC");
		$result = uli_get_results('player_points', $cond, NULL, $order);

		if ($year == 99){
			unset($cond);
			//$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
			$cond[] = array("col" => "round", "value" => 0);
			$result = uli_get_results('player_points', $cond, array('*', 'SUM(score) as score'), $order, NULL, 'GROUP BY playerID');

		}

		if ($result) {
			$colh[1] = Score;
			$colh[2] = Player;
			$colh[3] = UliTeam;
			$x = 1;



			foreach ($result as $result) {
				$player = get_player_infos($result['playerID'], $leagueID);
				$ulinameString = '';
				if ($player['uliID'] == $option['uliID']){$ulinameString .= '<b>';}
				$ulinameString .= $uliname[$player['uliID']];
				if ($player['uliID'] == $option['uliID']){$ulinameString .= '</b>';}
				$data[$x][] = $result['score'].'';
				$data[$x][] = '<a class="playerinfo" id = "'.$result['playerID'].'" href="#">'.$player['name'].'</a>';
				$data[$x][] = $ulinameString;
				$x = $x + 1;
			}
		}
	}


	if ($view=='mybestplayers'){
		if (!$round){$round = 0;}
		if (!$year){$year = $option['currentyear'];}
		//echo print_year_round_menu($round, $year);
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);


		$uliID = $option['uliID'];
		$userTeam = get_user_team_sort($uliID, 'marktwert');

		if ($userTeam){
			$colh[1] = Spieler;
			$colh[2] = "Abl&ouml;se";
			$colh[3] = "Marktwert";

			$colh[4] = "Im Kader seit";
			$colh[5] = "Spiele f&uuml;r mich";
			$colh[6] = "Punkte f&uuml;r mich";
			$colh[7] = "Kapit&auml;n";
			$colh[8] = "Punkte diese Saison";


			$x = 1;

			foreach($userTeam as $player){
				$player = get_player_infos($player['playerID'], $option['leagueID'], $items = array('all'));
				/*
				echo $player['vorname'].' '.$player['nachname'].'<br>';
				echo uli_date($player['transferdetails'][0]['time']).' <br>';
				echo uli_money($player['lasttransfersum']).'<br>';
				*/
				//print_r($player['league_games']);

				//print_r($player);

				$allgames = 0;
				$points = 0;
				$gamesCap = 0;
				if ($player['league_games']){
					foreach ($player['league_games'] as $games){
						//print_r($games);
						if ($games['uliID'] == $uliID){
							$points = $points + $games['points'];
							if ($games['status'] == 1){
								$allgames = $allgames + $games['games'];
							}

							if ($games['status'] == 2){
								$gamesCap = $games['games'];

								//echo 'Kapit&auml;n: '.$games['games'].'<br>';
							}
						}
					}
				}

				$data[$x][] = '<a class="playerinfo" id = "'.$player['playerID'].'" href="#">'.$player['name'].'</a>';
				$data[$x][] =  uli_money($player['lasttransfersum']);
				$data[$x][] =  uli_money($player['marktwert']);

				$data[$x][] = uli_date($player['transferdetails'][0]['time']);
				$data[$x][] = $allgames;
				$data[$x][] = $points;
				$data[$x][] = $gamesCap;
				$data[$x][] = $player['scores'][$option['currentyear']][0];




				//$data[$x][] = $uliname[$player['uliID']];

				$x = $x + 1;
				/*
				echo 'Spiele: '.$allgames.' ---- Punkte: '.$points.'<br>';
				echo round($points/$allgames,2).'<br>';
				echo '<br><br><br>';
				*/
			}
		}


		/* Es werden alle Bundesligateamnamen eingelesen */
		//$ligateam = get_all_team_names();
		//$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
		//$cond[] = array("col" => "year", "value" => $year);
		//$cond[] = array("col" => "round", "value" => $round);

		//$order[] = array("col" => "score", "sort" => "DESC");
		//$result = uli_get_results('player_points', $cond, NULL, $order);
/*
		if ($year == 99){
			unset($cond);
			//$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
			$cond[] = array("col" => "round", "value" => 0);
			$result = uli_get_results('player_points', $cond, array('*', 'SUM(score) as score'), $order, NULL, 'GROUP BY playerID');

		}
*/
		if ($result) {
			$colh[1] = Score;
			$colh[2] = Player;
			$colh[3] = "Im Kader seit";

			//$colh[3] = UliTeam;

			$x = 1;


			foreach ($result as $result) {
				$player = get_player_infos($result['playerID']);




				if ($player['uliID'] == $option['uliID']){
					$cond = array();
					$order = array();
					$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
					$cond[] = array("col" => "playerID", "value" => $result['playerID']);
					$order[] = array("col" => "time", "sort" => "DESC");
					$transfer = uli_get_row('transfers', $cond, $order, NULL, "LIMIT 1");


					$data[$x][] = $result['score'].'';

					$data[$x][] = '<a class="playerinfo" id = "'.$result['playerID'].'" href="#">'.$player['name'].'</a>';
					$data[$x][] = uli_date($transfer['time']);

					//$data[$x][] = $uliname[$player['uliID']];

					$x = $x + 1;
				}
			}
		}
	}

	if ($view == 'kadermarktwerte'){
		if (!$leagueID){$leagueID = $option['leagueID'];}
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);

		echo print_year_round_menu('', '', $leagueID, true);
		/* Es werden alle Bundesligateamnamen eingelesen */
		//$ligateam = get_all_team_names();
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$cond[] = array("col" => "uliID", "value" => 0, "func" => "!=");
		$order[] = array("col" => "SUM(marktwert)", "sort" => "DESC");
		$field = array("SUM(marktwert)", "COUNT(ID)", "uliID");
		$group = "GROUP by uliID";
		$result = uli_get_results('player_league', $cond, $field, $order, '', $group);
		if ($result) {
			$colh[1] = UliKlub;
			$colh[2] = AnzahlSpieler;
			$colh[3] = MarktwertProSpieler;
			$colh[4] = Gesamtmarktwert;
			$x = 1;
			foreach ($result as $result) {
				$sumGesamt = round($result['SUM(marktwert)']/1000000, 2);
				$sumProPlayer = round($result['SUM(marktwert)']/$result['COUNT(ID)']/1000000, 2);
				$data[$x][] = $uliname[$result['uliID']];
				$data[$x][] = $result['COUNT(ID)'];
				$data[$x][] = $sumProPlayer;
				$data[$x][] = $sumGesamt;
				$x = $x + 1;
			}
		}
	}


	if ($view == 'topmarktwerte'){
		if (!$leagueID){$leagueID = $option['leagueID'];}
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);
		echo print_year_round_menu('', '', $leagueID, true);
		/* Es werden alle Bundesligateamnamen eingelesen */
		//$ligateam = get_all_team_names();
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$cond[] = array("col" => "uliID", "value" => 0, "func" => "!=");
		$order[] = array("col" => "marktwert", "sort" => "DESC");
		$result = uli_get_results('player_league', $cond, NULL, $order);
		if ($result) {
			$colh[1] = Player;
			$colh[2] = Sum;
			$colh[3] = UliKlub;
			$x = 1;
			foreach ($result as $result) {
				$player = get_player_infos($result['playerID'], $leagueID);
				$ulinameString = '';
				if ($player['uliID'] == $option['uliID']){$ulinameString .= '<b>';}
				$ulinameString .= $uliname[$player['uliID']];
				if ($player['uliID'] == $option['uliID']){$ulinameString .= '</b>';}
				$sum = round($result['marktwert']/1000000, 2);
				$data[$x][] = '<a class="playerinfo" id = "'.$result['playerID'].'" href="#">'.$player['name']. '</a>';
				$data[$x][] = $sum;
				$data[$x][] = $ulinameString;
				$x = $x + 1;
			}
		}
	}


	if ($view == 'mytransfers'){
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();
		$cond[] = array("col" => "uliold", "value" => $option['uliID']);
		$cond[] = array("col" => "ulinew", "value" => $option['uliID'], "conj" => "OR");

		$order[] = array("col" => "time", "sort" => "DESC");
		$result = uli_get_results('transfers', $cond, NULL, $order);
		if ($result) {
			$colh[1] = Date;
			$colh[2] = Player;
			$colh[3] = FromKlub;
			$colh[4] = ToKlub;
			$colh[5] = Sum;
			$x = 1;
			foreach ($result as $transfer) {
				$player = get_player_infos($transfer['playerID']);
				// TODO noch schicker machen mit den lustigen Vereinsnamen
				$von = $uliname[$transfer['uliold']];
				$zu = $uliname[$transfer['ulinew']];
				if (!$zu){$zu = $transfer['externnew'];}
				if (!$zu){$zu = $ligateam[$player['team']];}
				if (!$von){$von = $transfer['externold'];}
				if (!$von){$von = $ligateam[$player['team']];}
				$sum = round($transfer['sum']/1000000, 2);
				if (!$sum){$sum = Abloesefrei;}
				$data[$x][] = uli_Date($transfer['time']);
				$data[$x][] = '<a class="playerinfo" id = "'.$transfer['playerID'].'" href="#">'.$player['name']. '</a>';
				$data[$x][] = $von;
				$data[$x][] = $zu;
				$data[$x][] = $sum;
				$x = $x + 1;
			}
		}
		else {
			$content .= '<h3>Es gab noch keine Transfers</h3>';
		}
	}
	if ($view == 'mytransfers3'){
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();
		$cond[] = array("col" => "uliold", "value" => $_REQUEST['uliID']);
		//$cond[] = array("col" => "ulinew", "value" => $option['uliID']);

		$order[] = array("col" => "time", "sort" => "DESC");
		$result = uli_get_results('transfers', $cond, NULL, $order);
		if ($result) {
			$colh[1] = Date;
			$colh[2] = Player;
			$colh[3] = FromKlub;
			$colh[4] = ToKlub;
			$colh[5] = Sum;
			$x = 1;
			foreach ($result as $transfer) {
				$player = get_player_infos($transfer['playerID']);
				// TODO noch schicker machen mit den lustigen Vereinsnamen
				$von = $uliname[$transfer['uliold']];
				$zu = $uliname[$transfer['ulinew']];
				if (!$zu){$zu = $transfer['externnew'];}
				if (!$zu){$zu = $ligateam[$player['team']];}
				if (!$von){$von = $transfer['externold'];}
				if (!$von){$von = $ligateam[$player['team']];}
				$sum = round($transfer['sum']/1000000, 2).' Mio';
				if (!$sum){$sum = Abloesefrei;}
				$data[$x][] = uli_Date($transfer['time']);
				$data[$x][] = '<b>'.$player['name']. '</b>';
				$data[$x][] = $von;
				$data[$x][] = $zu;
				$data[$x][] = $sum;
				$x = $x + 1;
			}
		}
		else {
			$content .= '<h3>Es gab noch keine Transfers</h3>';
		}
		if ($data){
			foreach ($data as $data){
				echo ' '.$data[0].' | '.$data[1].' | zu: '.$data[3].' | '.$data[4].' ||';
			}
		}
	}
	if ($view == 'mytransfers2'){
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();
		//$cond[] = array("col" => "uliold", "value" => $option['uliID']);
		$cond[] = array("col" => "ulinew", "value" => $_REQUEST['uliID']);

		$order[] = array("col" => "time", "sort" => "DESC");
		$result = uli_get_results('transfers', $cond, NULL, $order);
		if ($result) {
			$colh[1] = Date;
			$colh[2] = Player;
			$colh[3] = FromKlub;
			$colh[4] = ToKlub;
			$colh[5] = Sum;
			$x = 1;
			foreach ($result as $transfer) {
				$player = get_player_infos($transfer['playerID']);
				// TODO noch schicker machen mit den lustigen Vereinsnamen
				$von = $uliname[$transfer['uliold']];
				$zu = $uliname[$transfer['ulinew']];
				if (!$zu){$zu = $transfer['externnew'];}
				if (!$zu){$zu = $ligateam[$player['team']];}
				if (!$von){$von = $transfer['externold'];}
				if (!$von){$von = $ligateam[$player['team']];}
				$sum = round($transfer['sum']/1000000, 2).' Mio';
				if (!$sum){$sum = Abloesefrei;}
				$data[$x][] = uli_Date($transfer['time']);
				$data[$x][] = '<b>'.$player['name']. '</b>';
				$data[$x][] = $von;
				$data[$x][] = $zu;
				$data[$x][] = $sum;
				$x = $x + 1;
			}
		}
		else {
			$content .= '<h3>Es gab noch keine Transfers</h3>';
		}
		if ($data){
			foreach ($data as $data){
				echo ' '.$data[0].' | '.$data[1].' | von: '.$data[2].' | '.$data[4].' ||';
			}
		}
	}

	if ($view == 'uligames'){
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();

		$uliID = $_REQUEST['uliID'];

		$cond[] = array("col" => "uliID", "value" => $_REQUEST['uliID']);
		$cond[] = array("col" => "status", "value" => 1);
		$fields = array("SUM(games)", "playerID", "uliID", "status");
		$order[] = array("col" => "SUM(games)", "sort" => "DESC");

		$result = uli_get_results('player_league_games', $cond, $fields, $order, NULL, "Group by playerID");

		if ($result){
			foreach($result as $player){
				print_r($player);
				$player = get_player_infos($player['playerID'], 1, $items = array('all'));
				echo $player['vorname'].' '.$player['nachname'].'<br>';
				echo $player['SUM(games)'].' <br>';
			}
		}
		echo '<br><br><br><br>';

		unset($cond);
		unset($order);

		$cond[] = array("col" => "uliID", "value" => $_REQUEST['uliID']);
		$fields = array("SUM(points)", "playerID", "uliID", "status");
		$order[] = array("col" => "SUM(points)", "sort" => "DESC");

		$result = uli_get_results('player_league_games', $cond, $fields, $order, NULL, "Group by playerID");


		if ($result){
			foreach($result as $player){
				print_r($player);
				$player = get_player_infos($player['playerID'], 1, $items = array('all'));
				echo $player['vorname'].' '.$player['nachname'].'<br>';
				echo $player['SUM(points)'].' <br>';
			}
		}
		echo '<br><br><br><br>';

		unset($cond);
		unset($order);

		$cond[] = array("col" => "uliID", "value" => $_REQUEST['uliID']);
		$cond[] = array("col" => "status", "value" => 2);
		$fields = array("SUM(games)", "playerID", "uliID", "status");
		$order[] = array("col" => "SUM(games)", "sort" => "DESC");

		$result = uli_get_results('player_league_games', $cond, $fields, $order, NULL, "Group by playerID");

		if ($result){
			foreach($result as $player){
				print_r($player);
				$player = get_player_infos($player['playerID'], 1, $items = array('all'));
				echo $player['vorname'].' '.$player['nachname'].'<br>';
				echo $player['SUM(games)'].' <br>';
			}
		}
	}

	if ($view == 'uliplakat'){
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();

		$uliID = $_REQUEST['uliID'];
		$userTeam = get_user_team_sort($uliID, 'marktwert');

		if ($userTeam){
			foreach($userTeam as $player){
				$player = get_player_infos($player['playerID'], 1, $items = array('all'));
				echo $player['vorname'].' '.$player['nachname'].'<br>';
				echo uli_date($player['transferdetails'][0]['time']).' <br>';
				echo uli_money($player['lasttransfersum']).'<br>';

				//print_r($player['league_games']);

				$allgames = 0;
				$points = 0;

				if ($player['league_games']){
					foreach ($player['league_games'] as $games){
						//print_r($games);
						if ($games['uliID'] == $uliID){
							$points = $points + $games['points'];
							if ($games['status'] == 1){
								$allgames = $allgames + $games['games'];
							}

							if ($games['status'] == 2){
								echo 'Kapit&auml;n: '.$games['games'].'<br>';
							}
						}
					}
				}

				echo 'Spiele: '.$allgames.' ---- Punkte: '.$points.'<br>';
				echo round($points/$allgames,2).'<br>';
				echo '<br><br><br>';

			}
		}


	}



	if ($view == 'lasttransfers'){
		if (!$leagueID){$leagueID = $option['leagueID'];}
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);
		echo print_year_round_menu('', '', $leagueID, true);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$order[] = array("col" => "time", "sort" => "DESC");
		$result = uli_get_results('transfers', $cond, NULL, $order, 100);
		if ($result) {
			$colh[1] = Date;
			$colh[2] = Player;
			$colh[3] = FromKlub;
			$colh[4] = ToKlub;
			$colh[5] = Sum;
			$x = 1;
			foreach ($result as $transfer) {
				$player = get_player_infos($transfer['playerID']);
				// TODO noch schicker machen mit den lustigen Vereinsnamen
				$von = $uliname[$transfer['uliold']];
				$zu = $uliname[$transfer['ulinew']];
				if (!$zu){$zu = $transfer['externnew'];}
				elseif (!$zu){$zu = $ligateam[$player['team']];}
				if (!$von AND $transfer['externold'] != "undefined"){$von = $transfer['externold'];}
				if (!$von AND $transfer['externold'] == "undefined"){$von = "externer Transfer";}
				if (!$von){$von = $ligateam[$player['team']];}

				if ($von == $zu){
					$von = "Arbeitsamt";
				}

				// $sum = str_replace(" &euro;", "", uli_money($transfer['sum']));
				$sum = round($transfer['sum']/1000000, 2);


				if (!$sum){$sum = Abloesefrei;}
				$data[$x][] = uli_Date($transfer['time']);
				$data[$x][] = '<a class="playerinfo" id = "'.$transfer['playerID'].'" href="#">'.$player['name']. '</a>';
				$data[$x][] = $von;
				$data[$x][] = $zu;
				$data[$x][] = $sum;
				$x = $x + 1;
			}
		}
		else {
			$content .= '<h3>Es gab noch keine Transfers</h3>';
		}

	}

	if ($view == 'toptransfers'){
		if (!$leagueID){$leagueID = $option['leagueID'];}
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($leagueID);
		echo print_year_round_menu('', '', $leagueID, true);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$order[] = array("col" => "sum", "sort" => "DESC");
		$result = uli_get_results('transfers', $cond, NULL, $order, 100);
		if ($result) {
			$colh[1] = Date;
			$colh[2] = Player;
			$colh[3] = FromKlub;
			$colh[4] = ToKlub;
			$colh[5] = Sum;
			$x = 1;
			foreach ($result as $transfer) {
				$player = get_player_infos($transfer['playerID']);
				// TODO noch schicker machen mit den lustigen Vereinsnamen
				$von = $uliname[$transfer['uliold']];
				$zu = $uliname[$transfer['ulinew']];
				if (!$zu){$zu = $transfer['externnew'];}
				if (!$zu){$zu = $ligateam[$player['team']];}
				if (!$von){$von = $transfer['externold'];}
				if (!$von){$von = $ligateam[$player['team']];}

				// $sum = str_replace(" &euro;", "", uli_money($transfer['sum']));
				$sum = round($transfer['sum']/1000000, 2);


				if (!$sum){$sum = Abloesefrei;}
				$data[$x][] = uli_Date($transfer['time']);
				$data[$x][] = '<a class="playerinfo" id = "'.$transfer['playerID'].'" href="#">'.$player['name']. '</a>';
				$data[$x][] = $von;
				$data[$x][] = $zu;
				$data[$x][] = $sum;
				$x = $x + 1;
			}
		}
		else {
			$content .= '<h3>Es gab noch keine Transfers</h3>';
		}
	}
	if ($view == 'visitors'){
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();




			$colh[1] = Spieltag;
			$colh[2] = Sitzplaetze;
			$colh[3] = Stehplaetze;
			$colh[4] = Gesamt;
			$colh[5] = Einnahmen;
			$colh[6] = CateringEinnahmen;

			$x = 1;
			$uliID = $option['uliID'];
			for ($x=1; $x < $option['nextday']; $x++) {
				$data[$x][] = $x;
				$sitz = get_value_bank(3, $x, $option['currentyear'], $uliID);
				$steh = get_value_bank(4, $x, $option['currentyear'], $uliID);
				$data[$x][] = $steh;
				$data[$x][] = $sitz;
				$data[$x][] = ($sitz + $steh);
				$data[$x][] = uli_money(get_value_bank(2, $x, $option['currentyear'], $uliID));
				$data[$x][] = uli_money(get_value_bank(27, $x, $option['currentyear'], $uliID));
			}

	}


	if ($view == 'zuschauerschnitt'){
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();
		if (!$leagueID){$leagueID = $option['leagueID'];}

		//$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$cond[] = array("col" => "year", "value" => $year);
		$cond[] = array("col" => "round", "value" => 0);
		$cond[] = array("col" => "type", "value" => 3);
		$order[] = array("col" => "sum", "sort" => "DESC");
		$sitz = uli_get_results('finances', $cond, NULL, $order, NULL, 'GROUP BY uliID');
		if ($sitz){
			foreach ($sitz as $entry){
				$sitzAll[$entry['uliID']] = $entry['sum'];
			}
		}
		unset($cond);
		$cond[] = array("col" => "year", "value" => $year);
		$cond[] = array("col" => "round", "value" => 0);
		$cond[] = array("col" => "type", "value" => 4);
		$order[] = array("col" => "sum", "sort" => "DESC");
		$steh = uli_get_results('finances', $cond, NULL, $order, NULL, 'GROUP BY uliID');
			if ($steh){
			foreach ($steh as $entry){
				$stehAll[$entry['uliID']] = $entry['sum'];
			}
		}
		foreach ($stehAll as $key => $entry){
			$zuschauergesamt[$key] = $entry + $sitzAll[$key];
			unset($cond);
			$cond[] = array("col" => "sum", "value" => 0, "func" => ">");
			$cond[] = array("col" => "type", "value" => 4);
			$cond[] = array("col" => "year", "value" => $year);
			$cond[] = array("col" => "uliID", "value" => $key);
			$anzahl = uli_get_var('finances', $cond, "COUNT(ID)");
			if ($anzahl > 0){
			$zuschauerschnitt[$key] = $zuschauergesamt[$key]/$anzahl;
			}
		}
			arsort($zuschauerschnitt);

			$colh[1] = Place;
			$colh[2] = "Team // Liga";
			$colh[3] = "Zuschauer Gesamt";
			$colh[4] = "Zuschauerschnitt";



			$x = 1;
			$uliID = $option['uliID'];
			foreach ($zuschauerschnitt as $key => $entry) {

				$uli = get_uli($key);
				$league = get_league($uli['leagueID']);
				//echo '<br>';
				$data[$x][] = $x;
				$data[$x][] = $uli['uliname'].' // '.$league['name'].'';
				$data[$x][] = number_format($zuschauergesamt[$key],0, ",", ".");
				$data[$x][] = number_format($entry,0, ",", ".");
				$x++;

			}

	}


	if ($colh AND $data){
		$content .= uli_table($colh, $data, '');
	}


	return $content;
}





function print_stats_menue(){
	global $option;

	$SelectOptions[] = array("view" => "tabelle", "desc" => Tabelle);
	$SelectOptions[] = array("view" => "whoiswho", "desc" => "Wer macht denn mit?");
	$SelectOptions[] = array("view" => "games", "desc" => "Jeder gegen Jeden");
	$SelectOptions[] = array("view" => "lasttransfers", "desc" => Transfers);
	$SelectOptions[] = array("view" => "toptransfers", "desc" => TopTransfers);
	$SelectOptions[] = array("view" => "topmarktwerte", "desc" => MarktWerte);
	$SelectOptions[] = array("view" => "kadermarktwerte", "desc" => MarktWerteKader);
	$SelectOptions[] = array("view" => "vermoegen", "desc" => Vermoegen);
	$SelectOptions[] = array("view" => "bestplayers", "desc" => BestPlayers);
	$SelectOptions[] = array("view" => "stadien", "desc" => "Die schönsten Stadien der Welt");
	$SelectOptions[] = array("view" => "zuschauerschnitt", "desc" => "Zuschauerschnitt");

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
		$html = uli_box("Statistiken (Allgemein)", $html);
		return $html;
}

function print_my_stats_menue(){
	global $option;

	$SelectOptions[] = array("view" => "mytransfers", "desc" => MyTransfers);
	$SelectOptions[] = array("view" => "mybestplayers", "desc" => MyBestPlayers);
	$SelectOptions[] = array("view" => "visitors", "desc" => Visitors);

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
		$html = uli_box("Statistiken (Dein Team)", $html);
		return $html;
}



function print_year_round_menu($round, $year, $leagueID = '', $onlyLeagues = ''){
	global $option;

	$html .= '<div class="filter">';

	if (!$onlyLeagues){
		$html .= '<select id="year">';
		$html .= "\n";
		$html .= '<option value="99">'.Ewigkeit.'</option>';
		$html .= "\n";
		$uliyears = get_uli_years();
		foreach ($uliyears as $years){
			if ($year == $years['ID']){$selected = 'selected = "selected"';}
			else {$selected = '';}
			$html .= '<option '.$selected.' value="'.$years['ID'].'">'.$years['name'].'</option>';
			$html .= "\n";
		}
		$html .= '</select>';
		$html .= "\n";

		if ($year != 99){

			$html .= '<select id="round">';
			$html .= "\n";
			$html .= '<option value="0">'.Gesamt.'</option>';
			$html .= "\n";
			for ($x = 1; $x <= 34; $x++){
				if ($round == $x){$selected = 'selected = "selected"';}
				else {$selected = '';}
				$html .= '<option '.$selected.' value="round'.$x.'">'.$x.'. '.Round.'</option>';
				$html .= "\n";
			}
			$html .= '</select>';
		}

		if (!$leagueID){
			$leagueID = $option['leagueID'];
		}
	}
	$html .= '<select id="league" style="margin-left: 5px;">';
	$html .= "\n";
	$html .= "\n";
	$leagues = get_leagues();
	foreach ($leagues as $league){
		if ($leagueID == $league['ID']){$selected = 'selected = "selected"';}
		else {$selected = '';}
		$html .= '<option '.$selected.' value="'.$league['ID'].'">'.$league['name'].'</option>';
		$html .= "\n";
	}
	$html .= '</select>';
	$html .= "\n";

	$html .= "\n";
	$html .= '</div>';
	$html .= '<br/>';



	return $html;
}




?>
