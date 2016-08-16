<?php
/*
 Spieler werden beschrieben durch

 Merkmal				Realitaet		Liga 		berechnete Dinge (einzeln pro Liga)

 Name				x (player)
 Alter 				x (player)
 Geburtstag			x (calc)
 BL-Klub				x (player)
 Groesse				x (player)
 Gewicht				x (player)
 Nationalitaet		x (player)
 Position			x (player)
 Nebenposition(en)	x (player)
 Fuss				x (player)
 Kicker-Rangliste	x (player) NEU Alle Eintr�ge bekommen einen einzelnen Wert (1 Weltklasse 2 internationale Klasse 3 Im weiteren Kreis 4 Blickfeld)
 Spiele seit 2004	x (calc) (0 Eintrag bei player_points ADMIN)
 Punkte seit 2004	x (calc) (0 Eintrag bei player_points ADMIN)
 Durchschnittspunkte x (calc)
 Verteilung Punkte	x (calc)
 Uli-Klub							x (player_league)
 akt. Trikotnummer					x (player_league)
 Gehalt								x (player_contracts)
 Vertragsdauer						x (player_contracts)
 Vertragsende						x (player_contracts)
 Transfers							x (alle)
 Gesamtsumme Transfers				x (player_league)
 Gesamtanzahl Transfers				x (player_league)
 Summe letzter Transfer				x (transfers)
 Datum letzter Transfer				x (transfers)
 Spiele f�r und nach Uli Klubs		x (calc) (ab 2006. Kriegt man die alten Daten noch? Alte DB Datei suchen ...)
 davon auf der Bank					x (calc)
 davon benotet						x (calc)
 davon Kapit�n						x (calc)
 Punkte f�r und nach Uli Klubs		x (calc)
 verkaufte Trikots f�r und nach
 Uli Klubs							x (calc)
 Marktwert										x
 Status im Team									x (in der lib_player.php - ingesamt 8 stati)
 akt. Zufriedenheit								x (0-100) Welche Einflussfaktoren?
 Charakter (?)									x
 Loyalit�t										x (wenn laenger als ein Jahr im Verein, dann Marker setzen)
 Gespraechsbereitschaft (lauf. Verhandl.)		x ("Aktionen")
 Gehaltswunsch (lauf. Verhandl.)				x ("Aktionen")
 Attraktivitaet fuer Presse						x ("Aktionen")



 In welchen Tabellen werden Spielerinformationen gespeichert

 uli_player - Hauptinfos (Realitaet)
 uli_player_points - Punkte (Realitaet) - Alte "playerpoints"

 uli_player_league (das alte Attributes. Hier kommen alle relevanten Dinge fuer die aktuelle Liga rein.)
 uli_player_league_contracts - Unterebene fuer Vertraege
 uli_player_league_games - Unterebene zum Speichern der Leistungsdaten. Vereinfacht die Komplexitaet Abfragen



 uli_userteams - Spiele und Punkte in der Liga
 uli_transfers - Transfers



 Pruefen, welche berechneten Werte zur Performance-Optimierung
 einmal pro Spieltag oder bei einer relevanten Aktion in extra Feldern gespeichert werden koennen

 // TODO
 // Zufriedenheit



 Aufbau der lib_player.php sollte sein:
 moeglichst keine 1.000 einzelfunktionen
 Vielleicht sollten die "Print" Funktionen in eine andere Datei (output) Gerade wegen Ajax-Massaker
 Wo wird abgefangen, dass es Unterschiede in der Darstellung zwischen "Besitzer" und "Konkurrent" gibt?
 1. idealerweise eine get_player_infos die mit parametern angesteuert wird
 2. dann die calc-funktionen
 3. und dann die helfer-funktionen fuer updates und zuweisungen (bild, immer wiederkehrende dinge) um platz zu sparen


 ZUFRIEDENHEIT
 ??? lieber Skala umbauen auf -50 bis 50 ???
 Start bei 50
 Nach jedem Wechsel normalisiert sich der Wert und geht in den mittleren Bereich zwischen 40 und 60

 Event							Check
 In Echt gespielt 				beim Uli Klub gespielt?
 Vertragsverhandlung			Erfolgreich?
 neues Gehalt					hoeher oder niedriger?
 Anfrage eines anderen Klubs	Gehaltsangebot?
 Wechsel						besserer oder schlechterer Klub
 Neueinkauf eines Spielers		Konkurrent? (gleiche Position, Marktwert)

 TODO nachdenken, ob man da eine Historie einbaut. Koennte eine grosse Tabelle geben
 Allerdings ist das so irreversibel

 */



/*******************/
/* Hauptfunktionen */
/*******************/

/**
 * die Hauptfunktion um Spielerinfos zu holen
 * Diese Funktion muss immer gepflegt werden
 * 19.1.2011
 * playerID - Pflicht
 * leagueID - optional
 * $items = all f�r alles oder einzelne Datenfelder
 * return $player
 * ein Array mit allem drinne
 *
 * TODO vielleicht sollte die nochmal mit joins gebaut werden um die kiste schneller zu machen?
 * obwohl das wahrscheinlich nicht geht.
 *
 */
function get_player_infos($playerID, $leagueID = '', $items = array()) {
	global $option, $wpdb;

	if (!$leagueID){$leagueID = $option['leagueID'];}


	// Um den Aufruf einfacher zu machen
	if ($items[0] == 'all'){$items = array('scores', 'transfers', 'contracts', 'soldtrikots', 'league_games');}

	if(!$items){$items = array();}

	// Hier die allgemeinen Infos
	$sql = 'SELECT * FROM tip_uli_player p ';
	if ($leagueID) {$sql .= 'LEFT JOIN tip_uli_player_league pl ON p.ID = pl.playerID AND pl.leagueID = '.$leagueID.' ';}
	$sql .= 'WHERE p.ID = '.$playerID.' ';
	$player = $wpdb->get_row($sql, ARRAY_A);

	$player['buliteam'] = $player['team'];
	$player['age'] = player_age($player['birthday']);
	$player['pichtml'] = get_player_pic($playerID);
	$player['picurl'] = get_player_pic_url($playerID);
	$player['stariconhtml'] = get_star_icon($player['star']);

	// happiness
	$cond = array();
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "active", "value" => 1);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$player['smile'] = uli_get_var("player_league_smile", $cond, "smile");

	//print_r($player);


	// injurycheck
	$cond = array();
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$injured = uli_get_row("player_injured", $cond);
	if ($injured){
		$player['injury'] = TRUE;
		$player['injury_cause'] = $injured['cause'];
		$player['injury_update'] = $injured['timestamp'];
	}


	// Hier die Punkte

	if(in_array("scores", $items)){
		unset($cond);
		$cond[] = array("col" => "playerID", "value" => $playerID);
		$order[] = array("col" => "year", "sort" => "ASC");
		$order[] = array("col" => "round", "sort" => "ASC");
		$player_score = uli_get_results('player_points', $cond, NULL, $order);
		if ($player_score){
			foreach ($player_score as $score){
				$player['scores'][$score['year']][$score['round']] = $score['score'];
			}}
	}
	if ($leagueID){
		// Transfers
		if(in_array("transfers", $items)){
			unset($cond);
			unset($order);
			$cond[] = array("col" => "playerID", "value" => $playerID);
			$cond[] = array("col" => "leagueID", "value" => $leagueID);
			$order[] = array("col" => "time", "sort" => "DESC");
			$player_transfers = uli_get_results('transfers', $cond, NULL, $order);
			if ($player_transfers){
				foreach ($player_transfers as $transfer){
					$player['transferdetails'][] = $transfer;
				}
			}
			if($player['transferdetails']){
				$player['lasttransfersum'] = $player['transferdetails'][0]['sum'];
			}

		}
		// Vertraege
		if(in_array("contracts", $items)){
			unset($cond);
			unset($order);
			$cond[] = array("col" => "playerID", "value" => $playerID);
			$cond[] = array("col" => "leagueID", "value" => $leagueID);
			$order[] = array("col" => "history", "sort" => "DESC");
			$order[] = array("col" => "end", "sort" => "DESC");
			$player_contracts = uli_get_results('player_contracts', $cond, NULL, $order);
			if ($player_contracts){
				foreach ($player_contracts as $contract){
					$player['contractdetails'][] = $contract;
				}}
		}
		// verkaufte Trikots
		if(in_array("sold_trikots", $items)){
		}

		// Punkte pro UliTeam
		if(in_array("league_games", $items)){
			unset($cond);
			unset($order);
			$cond[] = array("col" => "playerID", "value" => $playerID);
			//$cond[] = array("col" => "leagueID", "value" => $leagueID);
			$league_games = uli_get_results('player_league_games', $cond);
			if ($league_games){
				foreach ($league_games as $league_game){
					$player['league_games'][] = $league_game;
				}}
		}
	}
	if($player['contractdetails']){
		foreach($player['contractdetails'] as $contract){
			for ($x = 1; $x <= 1; $x++){
				$player['salary'] = $contract['salary'];
				$player['contractend'] = $contract['end'];
			}
		}
	}


	// check ob der typ aktuell kapitaen ist
	unset($cond);
	$cond[] = array("col" => "number", "value" => 15);
	$cond[] = array("col" => "uliID", "value" => $player['uliID']);
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "year", "value" => $option['currentyear']);
	$cond[] = array("col" => "playerID", "value" => $player['playerID']);

	$kapitaen = uli_get_var("userteams", $cond, "ID");
	if ($kapitaen){
		$player['captain'] = 1;
	}

	$player['playerID'] = $playerID;

	if ($player){return $player;}
	else {return FALSE;}
}




/*******************/
/* Berechnungen    */
/*******************/

/**
 * 07.06.2011
 * provisorisch fertig. Muss dran gedreht werden
 *
 * Berechnet den Gehaltswunsch eines Spielers
 *
 *
 * Viele Sachen sind aehnlich der Marktwertberechnung
 *
 *
 * Grundlagen:
 * letzte Abloesesumme
 * Durchschnittsgehalt im Team, Allgemeines Gehaltsniveau in der Liga (Basiswert?) => ERST EINMAL NICHT
 * Top-Gehalt im Team => ERST EINMAL NICHT Wird dynamischer verglichen
 * Eigener Status in Abgleich mit dem Topverdiener mit dem selben Status
 * TeamRanking (bessere Teams m�ssen mehr bezahlen?)
 * eigene Leistung
 * bisheriges Gehalt (egal wo)
 * KickerStatus
 * Alter? (Ganz junge wollen kurze Vertr�ge und weniger Geld, Ganz alte wollen lange Vertr�ge und weniger Geld)
 * Zufriedenheit (Wenn bei selben Klub positiv, wenn bei fremden Klub umgekehrt)
 * Wie lange schon im Verein
 *
 * unattraktive Faktoren wie
 * - keine Perspektive
 * - starke Konkurrenz
 * - zu viele Spieler im Kader
 * - scheiss Stimmung
 * schlagen sich auf die Gehaltssumme drauf
 *
 *
 */
function calculate_salary($player, $playerID = '', $uliID, $leagueID = '', $dur){
	global $option;
	$ulinames = get_all_uli_names($option['leagueID']);

	if (!$leagueID){$leagueID = 1;}

	if (!$player){
		$player = get_player_infos($playerID, $leagueID, array('transfers', 'contracts'));
	}
	if (!$player){return FALSE;}

	//print_r($player);

	//$DataLeague = get_salary_score_data($leagueID); /* Punkte und Gehalt der Liga */
	//$DataUli = get_salary_score_data(NULL, $uliID); /* Punkte und Gehalt des neuen Teams */


	// Holt das aktuelle Gehalt
	if($player['contractdetails']){
		foreach($player['contractdetails'] as $contract){
			for ($x = 1; $x <= 1; $x++){
				$player['salary'] = $contract['salary'];
				$player['contractend'] = $contract['end'];
			}
		}
	}

	// Altersklassen
	// 1 - bis 21 - 110%
	// 2 - bis 23 - 100%
	// 3 - bis 27 - 95%
	// 4 - bis 30 - 90%
	// 5 - bis 33 - 80%
	// 6 �ber 33 - 70%
	if ($player['age'] < 22){$faktor['age'] = 110;}
	elseif ($player['age'] < 24){$faktor['age'] = 100;}
	elseif ($player['age'] < 28){$faktor['age'] = 90;}
	elseif ($player['age'] < 31){$faktor['age'] = 80;}
	elseif ($player['age'] < 34){$faktor['age'] = 70;}
	else {$faktor['age'] = 70;}

	// Unterteilung nach Positionen
	// 1 - Sturm und Mittelfeld = 100
	// 2 - Abwehr = 80
	// 3 - Tormann = 65
	if ($player['hp'] >= 4){$faktor['position'] = 100;}
	elseif ($player['hp'] >= 2){$faktor['position'] = 90;}
	else {$faktor['position'] = 65;}


	// Das Ranking des "Besitzers"
	$TR = get_TR($uliID);
	$faktor['TR'] = $TR['TR_gesamt'];

	// Status
	// 1 - Kapitaen/Kopf der Mannschaft/Star
	// 2 - Leistungstr�ger
	// 3 - Mitlaeufer
	// 4 - Fehleinkauf
	// 5 - spielt keine Rolle
	// diese werden vergeben, wenn keine Leistungsdinger festzustellen sind ???
	// 6 - Talent
	// 7 - Hoffnungstraeger
	// 8 - Ergaenzungsspieler
	$faktor['status'] = 100;
	if ($player['status'] > 0){
		if ($player['status'] == 1){$faktor['status'] = 130;}
		if ($player['status'] == 2){$faktor['status'] = 120;}
		if ($player['status'] == 3){$faktor['status'] = 95;}
		if ($player['status'] == 4){$faktor['status'] = 85;}
		if ($player['status'] == 5){$faktor['status'] = 90;}
		if ($player['status'] == 6){$faktor['status'] = 120;}
		if ($player['status'] == 7){$faktor['status'] = 110;}
		if ($player['status'] == 8){$faktor['status'] = 90;}
	}

	// Kicker Stern
	// nach den Eintraegen
	$faktor['star'] = 100;
	if ($player['star'] > 0){
		if ($player['star'] == 1){$faktor['star'] = 150;}
		if ($player['star'] == 2){$faktor['star'] = 130;}
		if ($player['star'] == 3){$faktor['star'] = 120;}
		if ($player['star'] == 4){$faktor['star'] = 105;}

	}

	// Gehalt
	if ($player['salary'] > 0){$faktor['salary'] = $player['salary'];}

	// Durchschnittswert der letzten zwei Saisons
	// wird nur gezaehlt, wenn es mindestens 8 Spiele waren

	if ($player['scores'][$option['currentyear']]){
		$games['currentyear'] = count($player['scores'][$option['currentyear']]) - 1;
	}
	if ($player['scores'][$option['lastyear']]){
		$games['lastyear'] = count($player['scores'][$option['lastyear']]) - 1;
	}

	$scores['games'] = $games['currentyear'] + $games['lastyear'];
	$scores['scores'] = $player['scores'][$option['currentyear']][0] + $player['scores'][$option['lastyear']][0];
	if ($scores['games'] >= 8){
		$faktor['av_score'] = $scores['scores'] / $scores['games'];
	}

	// Hier wird geschaut, wieviele Spieler der Knilch verpasst hat
	// Rauskriegen, wann das erste Spiel war
	if ($faktor['av_score']){
		if ($player['scores'][$option['lastyear']]){
			foreach($player['scores'][$option['lastyear']] as $key => $score){
				if (!$firstgame AND $key != 0){$firstgame = $key;}
			}
			$possiblegames = 35 - $firstgame + $option['nextday'] - 1;
		}

		if (!$firstgame){
			if ($player['scores'][$option['currentyear']]){
				foreach($player['scores'][$option['currentyear']] as $key => $score){
					if (!$firstgame AND $key != 0){$firstgame = $key;}
				}
			}
			$possiblegames = $option['nextday'] - 1 - $firstgame + 1;
		}
		$missedgames = $possiblegames - $scores['games'];
		$missedgamespercentage = $missedgames * 100 / $possiblegames;

		// Jetzt werten, je nach Status
		// Das ist eigentlich bisschen bloed, weil bei Spielern mit negativem Faktor das jetzt besser gemacht wird.
		// Aber das koennen wir erst einmal unter den Tisch fallen lassen ...
		if ($missedgamespercentage <= 0){$faktor['av_score'] = $faktor['av_score'] * 1.2;}
		elseif ($missedgamespercentage <= 25){$faktor['av_score'] = $faktor['av_score'] * 1.1;}
		elseif ($missedgamespercentage <= 50){$faktor['av_score'] = $faktor['av_score'] * 0.6;}
		elseif ($missedgamespercentage <= 75){$faktor['av_score'] = $faktor['av_score'] * 0.4;}
		else {unset ($faktor['av_score']);}
	}


	// Letze Abloese
	$faktor['lasttransfersum'] = $player['lasttransfersum'];


	// Basis sind die Punkte (evtl auch der Martkwert?)
	// Wenn keine Punktewertung dann zaehlt die letzte Abloese
	// Jeder Spieler will mindestens 1000 und maximal 45000K wert
	$SpanSalary = array("min" => "1000", "max" => "400000");
	//$SpanMarktwert = array("min" => "100000", "max" => "45000000");
	$SpanScore = array("min" => "-2", "max" => "4.5");

	// Das bisherige Gehalt muss auch immer eine Rolle spielen
	$m_basis['salary'] = $faktor['salary'];

	if ($faktor['av_score']){
		$m_basis['av_score'] = uli_calculate_function(($faktor['av_score']), $SpanSalary, $SpanScore);
		$m_basis['lasttransfersum'] = $faktor['lasttransfersum'] / 100;
	}
	else {
		$m_basis['lasttransfersum'] = $faktor['lasttransfersum'] / 100;
	}
	// Basis
	$Claim = array_sum($m_basis)/count($m_basis);

	// Je nach Team wird was raufgeschlagen oder weniger gefordert
	$teamranking_league = get_topdown_TR($leagueID);
	$prozentTR = array("min" => 75, "max" => 150);
	$SpanTR = array("min" => $teamranking_league['down'], "max" => $teamranking_league['top']);
	$faktor['TR'] = uli_calculate_function(($faktor['TR']), $prozentTR, $SpanTR);

	$Claim = $Claim * ($faktor['age']/100) *  ($faktor['position']/100) * ($faktor['TR']/100) * ($faktor['status']/100) * ($faktor['star']/100);


	// Jetzt die "Perspektivfragen"
	// Wenn ein Spieler schon sehr lange bei einem Klub ist, m�chte er bei Verlaengerung weniger, bei Wechsel mehr
	$FaktorLoyalty = 1;
	if ($player['loyalty'] == 1 AND $player['uliID'] != $uliID)
	{$FaktorLoyalty = 1.1;}
	if ($player['loyalty'] == 1 AND $player['uliID'] == $uliID)
	{$FaktorLoyalty = 0.9;}

	// wenn es eine feindliche uebernahme ist, moechte der knabe schon prinzipiell mehr geld
	$FaktorTakeOver = 1;
	if ($player['uliID'] != $uliID){
		$FaktorTakeOver = 1.2;
		// dann haengt das weitere von der zufriedenheit ab
	}

	$Claim = $Claim * $FaktorTakeOver * $FaktorLoyalty;


	// TODO Was machen wir hiermit?
	//$PlayerScores = get_player_scores($playerID, $player['userteamuliID']); /* Punkte im realen und im Managerleben */


	// Der Spieler lotet die Perspektive des zukuenftigen Teams aus
	// Wenn er was negatives findet, steigt der Gehaltswunsch

	// Holt das Team in ein Array
	$uliteam = get_user_team($uliID);
	if ($uliteam){
		// zaehlt die Spieler
		$numberofplayers = count($uliteam);
		// Zu viele Spieler im Kader (mehr als 25)
		if ($numberofplayers > 30){$Claim = $Claim * 1.5;}
		elseif ($numberofplayers > 25){$Claim = $Claim * 1.1;}

		foreach($uliteam as $uliteam){
			// Holt sich die relevanten Infos um zu vergleichen
			$smile = $smile + $uliteam['smile'];
			// Der ganze Aufwand wird nur betrieben, wenn es nicht derselbe Spieler ist
			if ($uliplayer['ID'] != $player['ID']){
				$uliplayer = get_player_infos($uliteam['playerID'], $uliteam['leagueID'], array('contracts'));// packt die Spieler auf der Hauptposition in ein Array

				// Schaut, wie denn so das Gehaltsgefuege in diesem Sektor aussieht ??


				// Hauptposition ist gleich
				if ($uliplayer['hp'] == $player['hp']){
					$betterplayers = 0;
					$worseplayers = 0;
					$worseplayersalary = 0;
					// Durchschnittswert der letzten zwei Saisons fuer den positionsgleichen Spieler
					// wird nur gezaehlt, wenn es mindestens 8 Spiele waren
					// Genauso wie weiter oben
					if ($uliplayer['scores'][$option['currentyear']]){
						$games['currentyear'] = count($uliplayer['scores'][$option['currentyear']]) - 1;
					}
					if ($uliplayer['scores'][$option['lastyear']]){
						$games['lastyear'] = count($uliplayer['scores'][$option['lastyear']]) - 1;
					}

					$scores['games'] = $games['currentyear'] + $games['lastyear'];
					$scores['scores'] = $uliplayer['scores'][$option['currentyear']][0] + $uliplayer['scores'][$option['lastyear']][0];
					if ($scores['games'] >= 8){
						$uliplayer['av_score'] = $scores['scores'] / $scores['games'];
					}
					// Es ist jemand besser
					if ($uliplayer['av_score'] > $faktor['av_score']){
						$betterplayers = $betterplayers + 1;
					}
					// Es ist jemand schlechter
					if ($uliplayer['av_score'] < $faktor['av_score']){
						$worseplayers = $worseplayers + 1;
						// Holt das aktuelle Gehalt
						if($uliplayer['contractdetails']){
							foreach($uliplayer['contractdetails'] as $contract){
								for ($x = 1; $x <= 1; $x++){
									$uliplayer['salary'] = $contract['salary'];
									$uliplayer['contractend'] = $contract['end'];
								}
							}
						}

						if ($uliplayer['salary'] > $worseplayersalary){
							$worseplayersalary = $uliplayer['salary'];
						}
					}
				}
			}
		}

		// Stimmung im Team
		$stimmung = $smile / $numberofplayers;
		if ($smile > 75){$Claim = $Claim * 0.9;}
		elseif ($smile <= 75){$Claim = $Claim * 1;}
		elseif ($smile <= 50){$Claim = $Claim * 1.1;}
		elseif ($smile <= 25){$Claim = $Claim * 1.2;}

		// Mehr als 1 Spieler besser auf meiner Position
		// Dann moechte sich der Spieler das bitteschoen ordentlich bezahlen lassen
		if ($betterplayers > 1){
			$Claim = Claim * 1.2;
		}

		// Sonderfall Es gibt einen Stammkeeper
		// Also jemand der auf der Position bessere Punkte hat
		// Dann noch einmal einen saftigen Aufschlag
		if ($player['hp'] == 1 AND $betterplayers >= 1){
			$Claim = Claim * 1.4;
		}

		// Wenn es schlechtere Spieler gibt, dann schaut der Knilch, was die so verdienen und bezieht das mit ein
		if ($worseplayers){
			if ($worseplayersalary AND $Claim < $worseplayersalary){
				// es wird berechnet wieviel prozent mehr der Spieler mit dem hoechsten Gehalt, der schlechter ist mehr verdient
				$diffprozent = ($worseplayersalary - $Claim) * 100 / $worseplayersalary;
				$aufschlag = $diffprozent / 2;
				$Claim = $diffprozent / 100 * $Claim + $Claim;
			}
		}
	}



	// Dauer des Vertrages
	if ($dur == 3){$Claim = $Claim * 1.1;}
	if ($dur == 6){$Claim = $Claim * 1.0;}
	if ($dur == 12){$Claim = $Claim * 0.9;}
	if ($dur == 24){$Claim = $Claim * 0.8;}
	if ($dur < 12 AND player_age($player['birthday']) > 30){$Claim = $Claim * 1.1;}
	if ($dur > 10 AND player_age($player['birthday']) < 21){$Claim = $Claim * 1.2;}



	/* Ganz zum Schluss wird eine Zufallsformel raufgepackt */
	$Claim = $Claim * rand(90,110) / 100;

	//echo $player['name'].' ('.$player['status'].') ('.player_age($player['birthday']).') ('.$faktor['av_score'].') Forderung von '.$ulinames[$uliID].': '.uli_money($Claim).' <br/>';
	return $Claim;
}


/**
 * Berechnet den Martkwert eines Spielers
 * 01.06.11
 *
 *
 * PROVISORISCH FERTIG.
 *
 *
 * Variablen
 *	 	Alter
 * 		letzte Abloese
 * 		Gehalt
 * 		Vertragsaufzeit
 * 		Position
 * 		Kicker Stern
 * 		Zufriedenheit
 * 		Punkte-Schnitt letzte 25 Spiele
 * 		Teamranking Verein Liga
 * 		(Marktwert TM.de - wenn es zu parsen geht)
 *  	Spieler Status
 *
 *
 *
 * EINE RELATIV AUFWAENDIGE FUNKTION (ZUMINDEST, WENN MAN ALLE SPIELER DURCHGEHT)
 * KEINE AHNUNG, WIE MAN DIE RESSOURCENSCHONENDER HINBEKOMMT
 *
 * return $marktwert INT
 */
function get_marktwert($player = array(), $playerID = '', $leagueID = ''){
	global $option;

	if (!$leagueID){$leagueID = 1;}

	if (!$player){
		$player = get_player_infos($playerID, $leagueID, array('all'));
	}
	if (!$player){return FALSE;}


	if($player['contractdetails'] AND !$player['salary']){
		foreach($player['contractdetails'] as $contract){
			for ($x = 1; $x <= 1; $x++){
				$player['salary'] = $contract['salary'];
				$player['contractend'] = $contract['end'];
			}
		}
	}


	// Vertragslaufzeit in 4 Stufen umrechnen
	// 1 - laenger als 18 Monate = 110
	// 2 - laenger als 12 Monate = 100
	// 3 - laenger als 6 Monate = 90
	// 4 - kuerzer als 6 Monate = 75
	$timestamp = mktime();
	$player['laufzeit'] = ($player['contractend'] - $timestamp) / 3600 / 24; // in Tagen
	if ($player['laufzeit'] > (18*30)){$faktor['laufzeit'] = 110;}
	elseif ($player['laufzeit'] > (12*30)){$faktor['laufzeit'] = 100;}
	elseif ($player['laufzeit'] > (6*30)){$faktor['laufzeit'] = 90;}
	else {$faktor['laufzeit'] = 75;}

	// Altersklassen
	// 1 - bis 21 - 110%
	// 2 - bis 23 - 100%
	// 3 - bis 27 - 95%
	// 4 - bis 30 - 90%
	// 5 - bis 33 - 80%
	// 6 �ber 33 - 70%
	if ($player['age'] < 22){$faktor['age'] = 110;}
	elseif ($player['age'] < 24){$faktor['age'] = 100;}
	elseif ($player['age'] < 28){$faktor['age'] = 95;}
	elseif ($player['age'] < 31){$faktor['age'] = 90;}
	elseif ($player['age'] < 34){$faktor['age'] = 80;}
	else {$faktor['age'] = 70;}

	// Unterteilung nach Positionen
	// 1 - Sturm und Mittelfeld = 100
	// 2 - Abwehr = 80
	// 3 - Tormann = 65
	if ($player['hp'] >= 4){$faktor['position'] = 100;}
	elseif ($player['hp'] >= 2){$faktor['position'] = 75;}
	else {$faktor['position'] = 60;}


	// Das Ranking des "Besitzers"
	$TR = get_TR($player['uliID']);
	$faktor['TR'] = $TR['TR_gesamt'];

	// Status
	// 1 - Kapitaen/Kopf der Mannschaft/Star
	// 2 - Leistungstr�ger
	// 3 - Mitlaeufer
	// 4 - Fehleinkauf
	// 5 - spielt keine Rolle
	// diese werden vergeben, wenn keine Leistungsdinger festzustellen sind ???
	// 6 - Talent
	// 7 - Hoffnungstraeger
	// 8 - Ergaenzungsspieler
	$faktor['status'] = 100;
	if ($player['status'] > 0){
		if ($player['status'] == 1){$faktor['status'] = 115;}
		if ($player['status'] == 2){$faktor['status'] = 105;}
		if ($player['status'] == 3){$faktor['status'] = 95;}
		if ($player['status'] == 4){$faktor['status'] = 85;}
		if ($player['status'] == 5){$faktor['status'] = 90;}
		if ($player['status'] == 6){$faktor['status'] = 100;}
		if ($player['status'] == 7){$faktor['status'] = 105;}
		if ($player['status'] == 8){$faktor['status'] = 90;}
	}

	// Kicker Stern
	// nach den Eintraegen
	$faktor['star'] = 100;
	if ($player['star'] > 0){
		if ($player['star'] == 1){$faktor['star'] = 125;}
		if ($player['star'] == 2){$faktor['star'] = 115;}
		if ($player['star'] == 3){$faktor['star'] = 110;}
		if ($player['star'] == 4){$faktor['star'] = 105;}

	}

	// Gehalt
	if ($player['salary'] > 0){$faktor['salary'] = $player['salary'];}

	// Durchschnittswert der letzten zwei Saisons
	// wird nur gezaehlt, wenn es mindestens 8 Spiele waren

	if ($player['scores'][$option['currentyear']]){
		$games['currentyear'] = count($player['scores'][$option['currentyear']]) - 1;
	}
	if ($player['scores'][$option['lastyear']]){
		$games['lastyear'] = count($player['scores'][$option['lastyear']]) - 1;
	}

	$scores['games'] = $games['currentyear'] + $games['lastyear'];
	$scores['scores'] = $player['scores'][$option['currentyear']][0] + $player['scores'][$option['lastyear']][0];
	if ($scores['games'] >= 8){
		$faktor['av_score'] = $scores['scores'] / $scores['games'];
	}

	// Hier wird geschaut, wieviele Spieler der Knilch verpasst hat
	// Rauskriegen, wann das erste Spiel war
	if ($faktor['av_score']){
		if ($player['scores'][$option['lastyear']]){
			foreach($player['scores'][$option['lastyear']] as $key => $score){
				if (!$firstgame AND $key != 0){$firstgame = $key;}
			}
			$possiblegames = 35 - $firstgame + $option['nextday'] - 1;
		}

		if (!$firstgame){
			if ($player['scores'][$option['currentyear']]){
				foreach($player['scores'][$option['currentyear']] as $key => $score){
					if (!$firstgame AND $key != 0){$firstgame = $key;}
				}
			}
			$possiblegames = $option['nextday'] - 1 - $firstgame + 1;
		}
		$missedgames = $possiblegames - $scores['games'];
		$missedgamespercentage = $missedgames * 100 / $possiblegames;

		// Jetzt werten, je nach Status
		// Das ist eigentlich bisschen bloed, weil bei Spielern mit negativem Faktor das jetzt besser gemacht wird.
		// Aber das koennen wir erst einmal unter den Tisch fallen lassen ...
		if ($missedgamespercentage <= 0){$faktor['av_score'] = $faktor['av_score'] * 1.2;}
		elseif ($missedgamespercentage <= 25){$faktor['av_score'] = $faktor['av_score'] * 1.1;}
		elseif ($missedgamespercentage <= 50){$faktor['av_score'] = $faktor['av_score'] * 0.6;}
		elseif ($missedgamespercentage <= 75){$faktor['av_score'] = $faktor['av_score'] * 0.4;}
		else {unset ($faktor['av_score']);}
	}


	// Letze Abloese
	// Spieler, die keine letzte Abloese haben (oder 0) haben per Definition keinen Marktwert
	$faktor['lasttransfersum'] = $player['lasttransfersum'];

	// Jetzt sind alle Faktoren eingesammelt
	// Basis sind
	// Gehalt, Punkte und letzte Abl�se
	// Alles weitere gibt plus oder minus
	// Top Marktwert in Deutschland
	// Ribery 45 Mio
	// Fuer die Basiswerte wird mit Max 45 Mio gerechnet
	// Top Gehalt angeblich 10 Mio pro Jahr (Ribery)
	// Durchschnittlicher MW laut tm.de ist 3,4 Mio (25.4.11)

	// Jeder Spieler ist mindestens 100K und maximal 45000K wert
	$SpanMarktwert = array("min" => "100000", "max" => "40000000");
	$SpanSalary = array("min" => "34000", "max" => "10000000");
	$SpanScore = array("min" => "-2", "max" => "6");

	// TODO eventuell muss an dem atan noch gedreht werden !!! Die Mitte ist ein bisschen schwach im Moment
	$m_basis['salary'] = uli_calculate_function(($faktor['salary'] * 34), $SpanMarktwert, $SpanSalary);
	$m_basis['salary2'] = $m_basis['salary'];

	$m_basis['lasttransfersum'] = $faktor['lasttransfersum'];
	if ($faktor['av_score']){
		$m_basis['av_score'] = uli_calculate_function(($faktor['av_score']), $SpanMarktwert, $SpanScore);
	}

	//print_r($m_basis);

	// Basis
	$marktwert = array_sum($m_basis)/count($m_basis);
	$teamranking_league = get_topdown_TR($leagueID);
	$prozentTR = array("min" => 75, "max" => 125);
	$SpanTR = array("min" => $teamranking_league['down'], "max" => $teamranking_league['top']);
	$faktor['TR'] = uli_calculate_function(($faktor['TR']), $prozentTR, $SpanTR);

	// Jetzt die Prozente
	//print_r($faktor);

	$marktwert = $marktwert * ($faktor['age']/100) * ($faktor['laufzeit']/100) * ($faktor['position']/100) * ($faktor['TR']/100) * ($faktor['status']/100) * ($faktor['star']/100);

	// Jetzt diejenigen, die unter 2 Mio liegen noch einmal sanft abwerten
	if ($marktwert < 2000000){
		$diff = 2000000 - $marktwert;
		$minus = $diff/100000*3*$marktwert/100;
		$marktwert = $marktwert - $minus;
	}

	// Jeder Spieler ist mindestens 100000 wert
	if ($marktwert < 100000){
		$marktwert = 100000;
	}

	//echo ''.$player['name'].' - '.uli_money($marktwert).' <br/>';
	return $marktwert;
}



/******************************/
/**** START PLAYER STATUS  ****/
/******************************/

/**
 * Funktion zum Berechnen des Status eine Spielers
 * 1 - Kapitaen/Kopf der Mannschaft/Star
 * 2 - Leistungstr�ger
 * 3 - Mitlaeufer
 * 4 - Fehleinkauf
 * 5 - spielt keine Rolle
 * diese werden vergeben, wenn keine Leistungsdinger festzustellen sind ???
 * 6 - Talent
 * 7 - Hoffnungstraeger
 * 8 - Ergaenzungsspieler
 *
 * Erst wird geschaut wie lange der Spieler im Kader ist
 * LAENGER ALS 3 MONATE (10 SPIELE) IM KADER --> 1 - 4
 * KUERZER --> 5 - 7
 *
 * Folgende Infos werden gebraucht
 * Punkte in den letzten Spielen (f�r das Userteam)
 * Abloesesumme
 * Userteam
 * seit wann im Kader
 * Alter
 * Tabellenstand des Kaders
 * Vertrag
 *
 * TODO: EVENTUELL DIE PUNKTMARKEN FUER JEDEN KLUB DYNAMISCH ERRECHNEN
 * TODO: Mit Einbeziehen, ob der Spieler in echt gespielt hat?
 * return $status
 *
 * 02.06.09
 */
function calculate_player_status($playerID, $leagueID){
	global $option;
	$statusNames[1] = "Kapit&auml;n";
	$statusNames[2] = "Leistungstr&auml;ger";
	$statusNames[3] = "normaler Proffi";
	$statusNames[4] = "Fehleinkauf";
	$statusNames[5] = "spielt keine Rolle";
	$statusNames[6] = "Talent";
	$statusNames[7] = "Hoffnungstr&auml;ger";
	$statusNames[8] = "Erg&auml;nzungsspieler";

	$player = get_player_infos($playerID, $leagueID, array('transfers'));
	//print_r($player);
	if ($player['uliID']){
		if($player['transferdetails']){
			$x = 0;
			foreach($player['transferdetails'] as $transfers){
				if ($x == 0){$transfer = $transfers;}
				$x = $x + 1;
			}}
			// Kuerzer als 90 Tage
			if ((mktime() - $transfer['time']) <  (90 * 24 * 60 * 60)){
				if ($transfer['sum'] > 5000000){$status = 7;}
				if ($transfer['sum'] <= 5000000)
				{
					/* Wenn Spieler juenger als 22 */
					if ($player['age'] < 22){$status = 6;}
					else {$status = 8;}
				}
			}
			// Spieler schon laenger im Kader
			else {
				// War der Spieler in den letzten 20 Spielen Kapitaen ?
				if (Player_Status_Captain($playerID, $player['uliID'])){$status = 1;}
				if (!$status){
					$playerStatusScore = Player_Status_Score($playerID, $player['uliID']);
					// Hat der Spieler in mindestens 10 der 20 Spiele gespielt und im Schnitt 2.5 Punkte gemacht
					if ($playerStatusScore > 1.5){$status = 2;}
					if ($playerStatusScore <= 1.5 AND $transfer['sum']){$status = 3;}
					if ($playerStatusScore <= 1.5 AND $transfer['sum'] > 6000000){$status = 4;}
					if ($playerStatusScore <= 0.5 AND $transfer['sum'] > 4000000){$status = 4;}
					if ($playerStatusScore <= 0 AND $transfer['sum'] > 2000000){$status = 4;}

					if (!$playerStatusScore){$status = 5;}
					if ($status == 5 AND $transfer['sum'] < 1000000 AND (mktime() - $player['birthday']) < (22 * 365 * 24 * 60 * 60)){$status = 6;}
				}
			}
			//echo $player['name'].' ('.$player['uliID'].'): '.$statusNames[$status].'<br/>';
			if ($status){
				update_player_status($status, $playerID, $leagueID);
				return TRUE;
			}
			else {return FALSE;}
	}
}

/**
 * Gib den Durchschnittspunktwert der letzten 20 Spiele zur�ck,
 * wenn der Spieler mindestens 10 Spiele gemacht hat
 * sonst FALSE
 * 02.06.09
 */
function Player_Status_Score($playerID, $uliID){
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "number", "value" => "15", "func" => "!=");
	$order[] = array("col" => "year", "sort" => "DESC");
	$order[] = array("col" => "round", "sort" => "DESC");
	$limit = 30;
	$result = uli_get_results('userteams', $cond, NULL, $order, $limit);
	/* Nur Berechnen, wenn mehr als 10 Eins�tze */
	if (count($result) > 10){
		foreach ($result as $player){
			$score = $score + $player['points'];
		}
		$AvScore = $score / count($result);
		return $AvScore;
	}
	return FALSE;
}


/**
 * schaut nach ob ein Spieler in den letzten 20 Partien Kapitaen war
 * wird gebraucht um den Spieler Status zu ermitteln
 * wenn mindestens 8 Spiele Kapitaen --> RETURN TRUE
 * sonst RETURN FALSE
 * 01.06.09
 */
function Player_Status_Captain($playerID, $uliID){
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "number", "value" => "15");
	$order[] = array("col" => "year", "sort" => "DESC");
	$order[] = array("col" => "round", "sort" => "DESC");
	$limit = 20;
	$result = uli_get_results('userteams', $cond, NULL, $order, $limit);
	if ($result){
		foreach ($result as $result){
			if ($result['playerID'] == $playerID){$Cap = $Cap + 1;}
		}}
		if ($Cap > 7){return TRUE;}
		else {return FALSE;}
}

/**
 * aktualisiert die Eintraege fuer den Spielerstatus in der playerattributes Tabelle
 * 02.06.09
 */
function update_player_status($status, $playerID, $leagueID){
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$value[] = array("col" => "status", "value" => $status);
	if (uli_update_record('player_league', $cond, $value)){return TRUE;}
	else {return FALSE;}
}


/*****************************/
/**** ENDE PLAYER RANKING ****/
/*****************************/



/********************************/
/* html fuer spielerinfo ********/
/********************************/


/**
 * Gibt die Spielerinfobox als HTML aus.
 * 23.07.2010
 * Es wird nur der Content zurueckgegeben
 * Deswegen kann diese Funktion sowohl als Panel, als auch als HTML oder in der Sidebar angesprochen werden
 * Es wird unterteilt zwischen Headline und Content
 *
 * TODO Neu machen. nach aktuellen funktionen in der lib.player
 *
 * Was wird ausgegeben
 *
 * Trikotnummer, Vorname Nachname, Alter, Position
 * Bild
 * Verein (echt)
 * Verein (Uli)
 * letzte Abloesesumme
 * Gehalt (nur Naeherungswert, wenn fremder Klub schaut)
 * Marktwert
 * Punkte (echt) alle Saisons
 * Spiele (echt)
 * Transfers
 * TODO
 * Punkte/Spiele fuer die einzelnen Teams
 *
 *
 *
 */
function print_player_info($playerID, $leagueID = ''){
	global $option;
	if (!$leagueID){$leagueID = $option['leagueID'];}
	$uliteams = get_all_uli_names($leagueID);
	$ligateams = get_all_team_names();
	$player = get_player_infos($playerID, $leagueID, array("all"));

	//print_r($player);


	// Name
	if ($player['vorname']){
		$name .= $player['vorname'].' '.$player['nachname'];
		// Kuenstlername
		if ($player['nachname'] != $player['name'] AND $player['name'] != ($player['vorname'].' '.$player['nachname'])){
			$name .= ' ('.$player['name'].')';
		}}
		else {$name = $player['name'];}

		// Bild
		$playerpic = get_player_pic($player['playerID']);

		// Alter
		$age = player_age($player['birthday']);

		// Star
		if ($player['star'] > 0){
			$staricon = get_star_icon($player['star']);}

			// Position
			$position .= $option['position'.$player['hp'].'-2'].' ';
			if ($player['np1']){$position .= ''.$option['position'.$player['np1'].'-2'].' ';}
			if ($player['np2']){$position .= ''.$option['position'.$player['np2'].'-2'].' ';}
			$position .= ' ('.$option['foot'.$player['foot'].'-2'].')';

			// Buli-Team
			$ligateam = $ligateams[$player['buliteam']];

			// Diese ganzen Sachen werden nur ausgeben, wenn der Spieler einen Besitzer hat
			if ($player['uliID']){
				// aktuellen Vertrag holen

				// Trikotnummer
				if ($player['jerseynumber']){
					$jerseynumber = '<div class="jerseynumber">'.$player['jerseynumber'].'</div>';
				}
				// wenn nicht der "Besitzer" schaut, wird nur ein schaetzwert ausgegeben
				if ($player['uliID'] != $option['uliID']){
					$result = get_estimated_value($player['salary'], $playerID);
					$salary = 'ca. '.uli_money($result['minvalue']).' - '.uli_money($result['maxvalue']);

				}
				else {
					$salary = uli_money($player['salary']);
				}
				$contractend = Contract.' '.until.' '.uli_date($player['contractend']);
				$userteam = $uliteams[$player['userteamuliID']];
				$marktwert = uli_money(round($player['marktwert'], -5));
			}
			// Der Spieler ist nicht unter Vertrag
			else {
				$userteam = NoJob;
			}

			// HEADLINE
			// CONTENT
			$html .= '<div class="playerinfo">';
			$html .= "\n";
			$html .= '<div class="pic">';
			$html .= $playerpic;
			$html .= '</div>';
			$html .= "\n";
			$html .= "\n";
			$html .= '<div class="content">';
			$html .= '<div class="name">';
			$html .= get_ligateam_wappen($player['team'], $ligateam).' ';
			$html .= $name.' ('.$age.')';
			$html .= "\n";
			$html .= '</div>';


			$html .= $jerseynumber;
			$html .= $position.'<br/>';
			$html .= $userteam.'<br/>';

			if ($player['injury']){
				$html .= get_injury_pic($player['injury_cause'].' (letztes Update: '.uli_date($player['injury_update']).')').' <b>'.$player['injury_cause'].'</b><br/>';
			}

			if ($player['uliID']){
				$html .= '('.$contractend.')<br/>';
				if ($salary){$html .= Salary.': '.$salary.'<br/>';}
				if ($marktwert){$html .= Marktwert.': '.$marktwert.'<br/>';}
				$html .= '</div>';
			}

			$html .= get_further_infos($player);


			$html .= '<div class="clear"></div>';

			$html .= "\n";

			$html .= '<div class="details">';

			// Punkte
			if ($player['scores']){
				$years = get_uli_years('DESC', 1);

				if ($years){
					foreach ($years as $year){
						$score = array();
						$score = $player['scores'][$year['ID']];
						$html .= '<select>';
						$html .= '<option>'.$year['name'].': ';
						if (isset($score[0])) {$html .= $score[0].' '.Points;}
						else { $html .= ' --- ';}
						$html .= '</option>';
						for ($x=1; $x<=34; $x++)
						{ $html .= '<option>'.$x.'. Spieltag:  ';
						if (isset($score[$x])) {$html .= '<b>'.$score[$x].'</b> '.Points.'</option>'; }
						else { $html .= ' --- ';}
						}
						$html .= '</select><br/>';
					}}
			}

			if ($player['scores'][$option['currentyear']]){
				$games['currentyear'] = count($player['scores'][$option['currentyear']]) - 1;
			}

			// Transfers
			// TODO die leeren Felder besser abfangen (oder das in der DB reparieren)
			$transfers = $player['transferdetails'];
			if ($transfers){
			 $html .= '<select>';
			 $html .= '<option>'.Transfers.':</option>';
			 foreach ($transfers AS $transfer)
			 {
			 	$html .= '<option>';
			 	$old = '';
			 	$new = '';
			 	if($transfer['externold']) {$old = $transfer['externold'];}
			 	if($transfer['uliold']) {$old = $uliteams[$transfer['uliold']];}
			 	if (!$old){$old = NoJob;}
			 	if($transfer['ulinew']) {$new = $uliteams[$transfer['ulinew']];}
			 	if($transfer['externnew']) {$new = $transfer['externnew'];}
			 	$datum = date("d.m.y",$transfer['time']);
			 	$html .= $datum.': '.$old.' >> '.$new.' ('.uli_money($transfer['sum']).')';
			 	$html .= '</option>';
			 }
			 $html .= '</select><br/>';
			}
			$html .= '</div>';
			$html .= "\n";
			$html .= '</div>';
			$html .= "\n";


			$playerinfo['headline'] = $headline;
			$playerinfo['content'] = $html;
			return $playerinfo;
}


/*******************/
/* Hilfsfunktionen */
/*******************/


/**
 * Array der Laender, fuer das Wappen
 */
function get_further_infos($player){

	$nationalitaet = trim($player['nationalitaet']);
	$nationalitaet = trim($nationalitaet);



	$nationalitaet = str_replace("&Ouml;", "oe", $nationalitaet);
	$nationalitaet = str_replace("�", "oe", $nationalitaet);
	$nationalitaet = str_replace("�", "ae", $nationalitaet);
	$nationalitaet = str_replace("�", "ae", $nationalitaet);
	$nationalitaet = str_replace("�", "ue", $nationalitaet);
	$nationalitaet = str_replace("�", "ue", $nationalitaet);

	//echo $nationalitaet;


	$weight = $player['gewicht'];
	$height = $player['groesse'];


	$html = $nationalitaet.' | '.($height/100).'m | '.$weight.'kg';


	return $html;

}



/**
 * aktualisiert die Zufriedenheit eines Spielers
 * es kann eine Differenz oder ein Wert uebergeben werden
 * NEU 2013: eigene Tabelle
 *
 */
function update_smile($playerID, $leagueID, $diff = '', $value='', $round = '', $year = ''){
	//global $option;

	$playerinfo = get_player_infos($playerID, $leagueID);
	if (!$value){
		$value = $playerinfo['smile'];
		if ($value){$value = $value + $diff;}
		else {$value = 50;}
	}

	if ($value > 100){$value = 100;}
	if ($value < 0){$value = 0;}

	// jetzt erst einmal alle alten werte auf active = 0 setzen
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$values[] = array("col" => "active", "value" => 0);
	uli_update_record('player_league_smile', $cond, $values);

	// jetzt den neuen eintrag schreiben
	unset ($values);
	$values[] = array("col" => "smile", "value" => $value);
	$values[] = array("col" => "timestamp", "value" => mktime());
	$values[] = array("col" => "year", "value" => $year);
	$values[] = array("col" => "playerID", "value" => $playerID);
	$values[] = array("col" => "round", "value" => $round);
	$values[] = array("col" => "leagueID", "value" => $leagueID);
	$values[] = array("col" => "active", "value" => 1);
	$values[] = array("col" => "uliID", "value" => $playerinfo['uliID']);
	if ($round > 0){
		// checken, ob es schon einen eintrag fuer diese runde gibt
		$cond = array();
		$cond[] = array("col" => "round", "value" => $round);
		$cond[] = array("col" => "year", "value" => $year);
		$cond[] = array("col" => "playerID", "value" => $playerID);
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$check = uli_get_row("player_league_smile", $cond);
		if ($check){
				$cond = array();
				// den eintrag von "davor" holen
				$cond[] = array("col" => "playerID", "value" => $playerID);
				$cond[] = array("col" => "leagueID", "value" => $leagueID);
				$cond[] = array("col" => "timestamp", "value" => $check['timestamp'], "func" => "<");
				$order[] = array("col" => "timestamp", "sort" => "DESC");
				$olderSmile = uli_get_row("player_league_smile", $cond, $order);
				$values[0] = array("col" => "smile", "value" => ($olderSmile['smile'] + $diff));
				$cond = array();
				$cond[] = array("col" => "ID", "value" => $check['ID']);
				uli_update_record("player_league_smile", $cond, $values);
			}
			else {
				$id = uli_insert_record("player_league_smile", $values);
			}
		}
	else {
		$id = uli_insert_record("player_league_smile", $values);
	}


	if ($id){
		return TRUE;
	}
	else {
		return FALSE;
	}
}




/**
 * berechnet aus dem Geburtstag das aktuelle Alter
 * 18.04.09
 */
function player_age($timestamp){
	$age = (mktime() - $timestamp) / 3600 / 24 / 365.25; settype($age, INT);
	if ($age){return $age;}
	else {return FALSE;}
}


/**
 * erzeugt das bild fuer das star icon
 */
function get_star_icon($star){
	global $option;
	if ($star == 4){$alt = Blickfeld;}
	if ($star == 3){$alt = WeitererKreis;}
	if ($star == 2){$alt = InternationaleKlasse;}
	if ($star == 1){$alt = Weltklasse;}
	if (file_exists($option['ulidirroot'].'/theme/graphics/icons/star'.$star.'.png')){
		$html = '<img src = "'.$option['uliroot'].'/theme/graphics/icons/star'.$star.'.png" title="'.$alt.'" alt="'.$alt.'">';
	}
	if ($html){return $html;}
	else {return FALSE;}
}


/**
 * erzeugt das bild fuer das smile icon
 */
function get_smile_icon($smile){
	global $option;
	if ($smile >= 80){$pic = 'vh';}
	elseif ($smile >= 50){$pic = 'h';}
	elseif ($smile >= 30){$pic = 'm';}
	else {$pic = 's';}
	$html = '<img src = "'.$option['uliroot'].'/theme/graphics/icons/smile_'.$pic.'.gif" width=12 height=12>';
	return $html;
}



/**
 * holt ein spieler bild
 * 20.07.10
 */
function get_injury_pic($cause = ''){
	global $option;
	$html = '<img class="icon_injured" height="11px" width="11px" alt = "'.$cause.'" title = "'.$cause.'" src = "'.$option['uliroot'].'/theme/graphics/icons/injured.jpg">';

	if ($html){return $html;}
}


/**
 * holt ein spieler bild
 * 20.07.10
 */
function get_player_pic($playerID){
	global $option;
	if (file_exists($option['ulidirroot'].'/theme/graphics/pic_players/'.$playerID.'.jpg')){
		$html = '<img src = "'.$option['uliroot'].'/theme/graphics/pic_players/'.$playerID.'.jpg">';
	}
	// Platzhalterbild
	else {
		$html = '<img src = "'.$option['uliroot'].'/theme/graphics/pic_players/1379.jpg">';
	}
	if ($html){return $html;}
}





/**
 * holt ein spieler bild
 * gibt aber nur die URL zurueck (fuer backgrounds, etc.)
 * 20.07.10
 */
function get_player_pic_url($playerID){
	global $option;
	if (file_exists($option['ulidirroot'].'/theme/graphics/pic_players/'.$playerID.'.jpg')){
		$html = $option['uliroot'].'/theme/graphics/pic_players/'.$playerID.'.jpg';
	}
	// Platzhalterbild
	else {
		$html = $option['uliroot'].'/theme/graphics/pic_players/1379.jpg';
	}
	if ($html){return $html;}
}

/**
 * holt die Spiele eines Spielers f�r einen bestimmten Verein
 * @return unknown_type
 */
function get_player_league_games($playerID, $uliID){
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$result = uli_get_row('player_league_games', $cond);
	if ($result){
		return $result;
	}
	else {
		return FALSE;
	}
}

?>
