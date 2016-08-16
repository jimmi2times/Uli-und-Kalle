<?php
/*
 * Alle Funktionen fuer Vertraege
 *
 * MainLib. Die Funktionen stehen immer zur Verf�gung
 */


/** ueberprueft ob ein Vertrag ausgelaufen ist
 * Wenn ja: Transfer des Spielers zum Arbeitsamt
 * Aus Performance-Gr�nden wird nur die Liga �berpr�ft, in der der Nutzer spielt
 * Aus Performance-Gruenden werden immer nur 5 ausgelaufene Vertraege beendet
 * 19.03.09
 */

function check_contracts() {
	global $option;
	$timestamp = mktime();
	$cond[] = array("col" => "end", "value" => $timestamp, "func" => "<");
	$cond[] = array("col" => "end", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
	$cond[] = array("col" => "history", "value" => 0);
	$limit  = 5;
	$result = uli_get_results('player_contracts', $cond, NULL, NULL, $limit);
	if ($result) {
		foreach ($result as $contract) {
			$contract['endofcontract'] = 1;
			trade_player($contract['playerID'], $contract['leagueID'], NULL, $contract);
		}}
}


/**
 * alte Verhandlungen werden archiviert
 * es wird geprueft, ob ein type mehr geld will.
 * dann wird die verhandlung nicht archiviert, sondern verlaengert
 * 16.07. 2011
 */
function check_negotiations() {
	global $option;

	
	$timestamp = mktime();
	$cond[] = array("col" => "end", "value" => $timestamp, "func" => "<");
	$cond[] = array("col" => "end", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
	$cond[] = array("col" => "history", "value" => 0);
	$result = uli_get_results('player_contracts_negotiations', $cond);

	if ($result){
		foreach($result as $negotiation){
			// Wenn faktor kleiner als, will der Typ mehr Geld
			if ($negotiation['klubdecision'] == 1 AND $negotiation['faktor'] > 25){
				$form['klubdecision'] = 4;
				$form['faktor'] = 0;
				$form['ID'] = $negotiation['ID'];
				$form['end'] = mktime() + (3600*24*7);
				update_negotiation($form);
			}
			// Eine Gehaltserhoehung geht zu Ende
			// Der Spieler wird unzufrieden
			elseif ($negotiation['klubdecision'] == 4){
				update_smile($negotiation['playerID'], $negotiation['leagueID'], rand(-12, -8), NULL, NULL, $option['currentyear']);
				unset($cond);
				unset($value);
				$cond[] = array("col" => "ID", "value" => $negotiation['ID']);
				$value[] = array("col" => "history", "value" => 1);
				uli_update_record('player_contracts_negotiations', $cond, $value);
			}
			else {
				unset($cond);
				unset($value);
				$cond[] = array("col" => "ID", "value" => $negotiation['ID']);
				$value[] = array("col" => "history", "value" => 1);
				uli_update_record('player_contracts_negotiations', $cond, $value);
			}
		}
	}
}


/**
 * beendet einen Vertrag
 * history Spalte wird auf 1 gesetzt
 * end-wert wird angepasst
 */
function end_contract($playerID, $uliID){
	$values[] 	= array("col" => "history", "value" => 1);
	$values[] 	= array("col" => "end", "value" => mktime());
	$cond[]		= array("col" => "playerID", "value" => $playerID);
	$cond[]		= array("col" => "uliID", "value" => $uliID);
	$ID = uli_update_record('player_contracts', $cond, $values);
	if ($ID){return $ID;}
	else {return FALSE;}
}

/**
 * schreibt einen neuen vertrag
 * achtung ab 3.0 wird hier nichts mehr aktualisiert, sondern ein komplett neuer datensatz mit "History = 0" geschrieben
 * alle Vertraege werden archiviert
 */
function write_new_contract($contract) {
	foreach ($contract as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	$ID = uli_insert_record('player_contracts',$values);
	if ($ID){return $ID;}
	else {return FALSE;}
}



/**
 * aktualisiert eine Verhandlung
 * oder traegt sie neu ein.
 * 18.07.2010
 */
function update_negotiation($form) {
	foreach ($form as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	if ($form['ID']){
		$cond[] = array("col" => "ID", "value" => $form['ID']);
		$ID = uli_update_record('player_contracts_negotiations', $cond, $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
	else {
		$ID = uli_insert_record('player_contracts_negotiations', $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
}
?>
