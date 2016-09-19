<?php
/*
 *Hier stehen alle Transferfunktionen
 */

/*
 Transferfunktionen (nur Transfers und nicht die ganzen Aktionen, die im Transfermarkt passieren koennen.

 checken ob auktionen ausgelaufen sind
 auktion beenden

 spieler traden - das ist eine master-funktion, die auch von anderen elementen angesteuert wird

 */


/**
 * Es wird ueberprueft, ob eine Auktion ausgelaufen ist und der Transfer
 * vollzogen werden muss
 * wenn der transfer geklappt hat, dann wird die auktion archiviert
 */
function check_transfers() {
	global $option;
	$timestamp = mktime();
	/* Holt alle Auktionen, bei denen die Endzeit vorbei ist */
	$cond[] = array("col" => "end", "value" => ($timestamp - 30), "func" => "<");
	$cond[] = array("col" => "end", "value" => "", "func" => "IS NOT NULL");
	$cond[] = array("col" => "end", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "history", "value" => 0);
	$result = uli_get_results('auctions', $cond);
	if ($result){
		foreach ($result as $auction){
			/* Transfer vorbereiten */
			if (trade_player($auction['playerID'], $auction['leagueID'], $auction)){
				archive_auctions($auction['playerID'], $auction['leagueID']);
			}
		}}
}


/**
 * beendet eine oder mehrere Auktionen
 * uebergabe entweder nach ID oder playerID und leagueID
 * gleichezeitig werden alle geboe auf archiviert gesetzt
 * 08.05.09
 */
function archive_auctions($playerID = '', $leagueID = '', $ID = ''){
	if ($ID){
		$cond[] = array("col" => "ID", "value" => $ID);
	}
	else {
		$cond[] = array("col" => "playerID", "value" => $playerID);
		$cond[] = array("col" => "history", "value" => 0);
		if ($leagueID){
			$cond[] = array("col" => "leagueID", "value" => $leagueID);
		}
		$auctionIDs = uli_get_results('auctions', $cond, array('ID'));
	}
	if ($auctionIDs){
		$values[] = array("col" => "history", "value" => 1);
		uli_update_record('auctions', $cond, $values);

		/* Gebote werden auch archiviert */
		// TODO checken ... hier gibt es irgendwelche fehler ...

		foreach($auctionIDs as $value){
			$auctionID[] = $value['ID'];
		}
		$condString = '('.implode(',', $auctionID).')';
		unset($cond);
		if ($ID){
			$cond[] = array("col" => "auctionID", "value" => $ID);
		}
		else {
			$cond[] = array("col" => "auctionID", "value" => $auctionID);
		}
		uli_update_record('auctions_bets', $cond, $values);
	}
}

/**
 * 2011
 * TODO noch fuer alle Tranferarten
 * bislang nur fuer Auktionen gebaut
 *
 * Ein Spieler wird transferiert
 * Funktion f�r Trades
 * vollzieht den Wechsel eines Spielers
 * In Abh�ngigkeit von der Art des Wechsels werden alle relevanten Aktionen durchgef�hrt
 *
 * Schreiben der History
 * Wechsel der Teamzugeh�rigkeit des Spielers
 * In den neuen Kader rein
 * Aus der alten Aufstellung raus
 * Schreiben der Vertragsdaten
 * Neu berechnen des Marktwertes
 * Aktualisieren der Spielerattribute
 * Geldtransaktionen bei den Usermanagern
 * Auktion hinzuf�gen, etc.
 *
 *
 * Transfertypen
 * 1 - Auktion
 * 2 - Usertrade
 * 3 - feindliche Uebernahme
 * 4 - Vertragsende
 * 5 - Admintrade
 *
 *
 */
function trade_player($playerID, $leagueID = '', $auction = NULL, $contract = NULL, $admintrade = NULL){
	global $option;

	$teamnames = get_all_team_names();


	// Die Admintrades werden rausgeloest, weil fuer potentiell alle Ligen
	if ($admintrade){
		// hier die globalen sachen
		$transfer['type'] = 5;
		$transfer['sum'] = $admintrade['sum'];
		$transfer['ulinew'] = 0;
		$transfer['time'] = mktime();
		$transfer['playerID'] = $playerID;

		if ($admintrade['externold']){
			$transfer['externold'] = $admintrade['externold'];
		}


		if ($admintrade['externnew']){
			$transfer['externnew'] = $admintrade['externnew'];
			$newteam = 999;
		}
		else {
			$transfer['externnew'] = $teamnames[$admintrade['ligateamnew']].' (Arbeitsamt)';
			$newteam = $admintrade['ligateamnew'];
		}
		// neues team (oder 999) wird geschrieben
		unset($cond);
		unset($value);
		$cond[] = array("col" => "ID", "value" => $playerID);
		$value[] = array("col" => "team", "value" => $newteam);
		uli_update_record('player', $cond, $value);

		// und jetzt die ligen
		$leagues = get_leagues();
		if ($leagues){
			foreach ($leagues as $league){
				$player = get_player_infos($playerID, $league['ID'], array('contracts'));
				$transfer['uliold'] = $player['uliID'];
				$transfer['leagueID'] = $league['ID'];
				// Transfergeschichte schreiben
				write_transfer_history($transfer);

				// Wenn der Spieler vorher bei einem UliTeam war muessen einige Dinge erledigt werden
				if ($transfer['uliold']){
					//	Spieler aus allen Aufstellungen der Zukunft entfernen
					remove_player_from_userformation($transfer['playerID'],$transfer['uliold']);
					//	Alten Vertrag als beendet erklaeren
					end_contract($transfer['playerID'], $transfer['uliold']);
					//	Geld an abgebenden Verein �berweisen
					if ($transfer['sum'] > 0){
						calculate_money(10, $transfer['sum'], $transfer['uliold'], 0, $option['currentyear'], 'add', 'income');
						// TODO alle Gebote und laufenden Verhandlungen zuruecksetzen (negotiations)
						// Erst machen, wenn diese Tabelle so bleiben soll
					}
				}
				// Zufriedenheit wird zurueck gesetzt (auf 50)
				//$playerleague['smile'] = 50;
				update_smile($playerID, $league['ID'], NULL, 50, NULL, $option['currentyear']);


				$playerleague['uliID'] = 0;
				$playerleague['loyalty'] = 0;
				$playerleague['status'] = 0;
				update_player_league($playerID, $league['ID'], $playerleague);
				// alle alten auktionen beenden
				archive_auctions($transfer['playerID'], $transfer['leagueID']);
				// Wenn es ein Team gibt und der Spieler keinem Manager gehoert
				// neue Auktion schreiben
				if ($player['team'] != 999) {
					// alle eventuell laufenden Auktionen werden beendet (archiviert)
					// wenn Typ = 1, dann ist das schon geschehen
					$auction = array();
					$auction['playerID']  = $player['playerID'];
					$auction['leagueID']  = $transfer['leagueID'];
					$auction['start']  	  = mktime();
					start_auction($auction);
				}
			}
		}
	}

	// hier die normalen Trades
	else {
		// Alle Daten vom Spieler werden geholt
		$player = get_player_infos($playerID, $leagueID, array('contracts'));
		//print_r($player);
		// Checks
		$check = TRUE;
		// um doppelte Transfers zu vermeiden
		if ($auction AND $auction['topbetuliID'] == $player['uliID']){
			$check = FALSE;
		}
		// wenn aus irgendwelchen gruenden keine top-gebot-id vorhanden ist
		if ($auction AND $auction['topbetuliID'] < 1){
			$check = FALSE;
		}


		// Vorbereitung
		if ($check){
			if ($auction){
				$transfer['sum'] = $auction['topbet'];
				$transfer['ulinew'] = $auction['topbetuliID'];
				$transfer['type'] = 1;
				$transfer['externold'] = ''.$teamnames[$player['team']].' (Arbeitsamt)';

			}

			if ($contract['endofcontract'] == 1){
				$transfer['sum'] = 0;
				$transfer['externnew'] = 'Arbeitsamt ('.$teamnames[$player['team']].')';
				$transfer['type'] = 4;
				$contract['uliID'] = 0;
			}

			if ($player['uliID']){
				$transfer['uliold'] = $player['uliID'];
				$transfer['externold'] = '';
			}

			// Ein neuer Klub ist da (feindliche Uebernahme oder Verkauf)
			if ($contract['uliID']){
				$transfer['sum'] = $contract['offer'];
				$transfer['ulinew'] = $contract['uliID'];
				$transfer['uliold'] = $player['uliID'];

				if ($contract['takeover'] == 1){
					$transfer['type'] = 3;
				}
				else {
					$transfer['type'] = 2;
				}
			}

			$transfer['time'] = mktime();
			$transfer['leagueID'] = $leagueID;
			$transfer['playerID'] = $playerID;
		}

		// Eigentliche Action
		if ($transfer){
			// Transfergeschichte schreiben
			write_transfer_history($transfer);

			// Wenn der Spieler vorher bei einem UliTeam war muessen einige Dinge erledigt werden
			if ($transfer['uliold']){
				//	Spieler aus allen Aufstellungen der Zukunft entfernen
				remove_player_from_userformation($transfer['playerID'],$transfer['uliold']);
				//	Alten Vertrag als beendet erklaeren
				end_contract($transfer['playerID'], $transfer['uliold']);
				// Auktionen auf history 1 setzen
				archive_auctions($transfer['playerID'], $transfer['leagueID']);
				//	Geld an abgebenden Verein �berweisen
				if ($transfer['sum'] > 0){
					calculate_money(10, $transfer['sum'], $transfer['uliold'], 0, $option['currentyear'], 'add', 'income');
					// TODO alle Gebote und laufenden Verhandlungen zuruecksetzen (negotiations)
					// Erst machen, wenn diese Tabelle so bleiben soll

				}
				// falls der typ auf der transferliste stand, muss
			}

			// Zufriedenheit wird zurueck gesetzt (auf 50)
			// Wenn auslaufender Vertrag oder Auktion vom Arbeitsamt
			if (($auction AND !$player['uliID']) OR $transfer['type'] = 4){
				update_smile($playerID, $league['ID'], NULL, 50, NULL, $option['currentyear']);
				//$playerleague['smile'] = 50;
			}

			// Jedem Anfang wohnt ein Zauber inne
			// Zufriedenheit bei einem Transfer steigt um 10, wenn der Spieler bei einer Auktion von einem UliTeam zu einem anderen wechselt
			if (($auction AND $player['uliID']) OR $transfer['type'] == 2 OR $transfer['type'] == 3){
				update_smile($playerID, $league['ID'], 10, NULL, NULL, $option['currentyear']);
				//$playerleague['smile'] = $player['smile'] + 10;
			}

			// Beim Auslaufen eines Vertrags
			if ($transfer['type'] == 4){
				$playerleague['transfers'] = $player['transfers'] + 1;
				$playerleague['jerseynumber'] = 0;
				$playerleague['loyalty'] = 0;
				$playerleague['marktwert'] = 0;
				$playerleague['uliID'] = 0;
				$playerleague['status'] = 0;
			}

			// Es gibt einen neuen Besitzer
			if ($transfer['ulinew']){
				// Alle Eigenschaften, die in der Tabelle player_league stehen
				// $playerleague['marktwert'] = get_marktwert($player, '', $leagueID);
				$playerleague['uliID'] = $transfer['ulinew'];
				$playerleague['transfers'] = $player['transfers'] + 1;
				$playerleague['totaltransfersum'] = $player['totaltransfersum'] + $transfer['sum'];
				$playerleague['jerseynumber'] = 0;
				$playerleague['loyalty'] = 0;
				// Status aktualisieren
				// Vertrag schreiben
				if (!$contract){
					$contract['playerID'] = $transfer['playerID'];
					$contract['length']   = 3;
					$contract['salary']   = $transfer['sum'] / 100;
					$contract['start']    = mktime();
					$contract['end']	  = mktime() + (3 * 30 * 24 * 60 * 60); /* 3 Monate */
					$contract['uliID']    = $transfer['ulinew'];
					$contract['leagueID'] = $transfer['leagueID'];
					$contract['history']  = 0;
				}
				else {
					$newcontract['playerID'] = $transfer['playerID'];
					$newcontract['length']   = $contract['length'];
					$newcontract['salary']   = $contract['salary'];
					$newcontract['start']    = mktime();
					$newcontract['end']		 = mktime() + ($contract['length'] * 30 * 24 * 60 * 60); /* 3 Monate */
					$newcontract['uliID']    = $transfer['ulinew'];
					$newcontract['leagueID'] = $transfer['leagueID'];
					$newcontract['history']  = 0;
					unset($contract);
					$contract = $newcontract;
				}
				write_new_contract($contract);
				calculate_player_status($playerID, $leagueID);
				//	Geld vom neuen Eigentuemer abziehen
				calculate_money(11, $transfer['sum'], $transfer['ulinew'], 0, $option['currentyear'], 'add', 'outgoings');
				// Falls der Typ auf der Transferliste stand, muss die Auktion archiviert werden
				if ($transfer['type'] == 2 OR $transfer['type'] == 3){
					archive_auctions($transfer['playerID'], $transfer['leagueID']);
				}
			}
			// Diese Tabelle muss bei jedem Transfer aktualisiert werden
			// Die Attribute werden vorher verteilt
			update_player_league($playerID, $leagueID, $playerleague);

			// Wenn es ein Team gibt und der Spieler keinem Manager gehoert
			// alle alten auktionen beenden
			// neue Auktion schreiben
			if ($player['team'] != 999 AND !$transfer['ulinew']) {
				// alle eventuell laufenden Auktionen werden beendet (archiviert)
				// wenn Typ = 1, dann ist das schon geschehen
				if ($transfer['type'] != 1){
					archive_auctions($transfer['playerID'], $transfer['leagueID']);
				}
				$auction['playerID']  = $player['playerID'];
				$auction['leagueID']  = $player['leagueID'];
				$auction['start']  	  = mktime();
				start_auction($auction);
			}

			// Neuen Marktwert berechnen
			// Aber nur, wenn ein neuer Manager im Spiel ist.
			if ($transfer['ulinew']){
				$marktwert = get_marktwert(NULL, $player['playerID'], $transfer['leagueID']);
				$value[] = array("col" => "marktwert", "value" => $marktwert);
				$cond[] = array("col" => "playerID", "value" => $playerID);
				$cond[] = array("col" => "leagueID", "value" => $leagueID);
				uli_update_record("player_league", $cond, $value);
			}

			return TRUE;
		}
		return FALSE;
	}
}

/**
 * Schreibt die Transferhistorie
 * uebergeben werden muessen im Array transfer alle Daten, die in die Tabelle sollen
 * 06.05.09
 */
function write_transfer_history($transfer){
	foreach($transfer as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	$ID = uli_insert_record('transfers', $values);
	if ($ID){return TRUE;}
	else {return FALSE;}
}


/**
 * Entfernt einen Spieler aus der aktuellen Aufstellung
 * Die Werte fuer den Spieler werden auf 0 gesetzt
 * Es entsteht eine Luecke
 * 09.05.09
 */
function remove_player_from_userformation($playerID, $uliID){
	global $option;
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "year", "value" => $option['currentyear']);
	$value[]= array("col" => "playerID", "value" => 0);
	uli_update_record('userteams', $cond,$value);
}

/**
 * Die entsprechende Zeile in player_league wird aktualisiert
 * abhaengig davon, was uebergeben wird
 */
function update_player_league($playerID, $leagueID, $player) {
	foreach($player as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	$cond[]= array("col" => "playerID", "value" => $playerID);
	$cond[]= array("col" => "leagueID", "value" => $leagueID);

	//print_r($cond);
	$ID = uli_update_record('player_league', $cond, $values);
	if ($ID){return TRUE;}
	else {return FALSE;}
}


/**
 * TODO TESTEN !!!
 * Startet eine Auktion
 * Alle Angaben m�ssen im Array $auction �bergeben werden
 * 09.05.09
 *
 *
 */
function start_auction($auction){
	foreach($auction as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	$ID = uli_insert_record('auctions', $values);
	if ($ID){return TRUE;}
	else {return FALSE;}
}

/********************????????????????********************/

/**
 * TODO !!!!!
 * f�r admintrades
 * team eines spielers updaten
 *
 *
 */
function update_team_player($playerID, $team) {
	global $wpdb, $CONFIG;
	// SQL
	$sql = 'UPDATE '.$CONFIG->prefix.'uli_player '.
		' SET team = "'.$team.'" '.
		' WHERE ID = "'.$playerID.'" ';
	$wpdb->query($sql);
}




?>
