<?php
/*
 * Setup Datei
 * Hier werden alle immer benoetigten Daten eingelesen und Aktionen durchgefuehrt
 * Au�erdem werden die immer benoetigten Bibliotheken eingebunden
 */

/* Login Check */
include('mainlib.php');
require_once(ABSPATH.'/uli/_mainlibs/login.php');


/* Alle relevanten Infos aus der Options Tabelle werden eingelesen */
$results = uli_get_results('options');
if($results){
	$option = array();
	foreach ($results as $result){
		$option[$result['attribut']] = $result['value'];
		if ($result['secondvalue']) {$option[$result['attribut'].'-2'] = $result['secondvalue'];}
	}
}

global $user_ID;
$option['userID'] 	= $user_ID;
$uli					= get_uli_userID($user_ID);
$option['uliID']		= $uli['ID'];

/* Wenn der Nutzer kein Team hat, landet er auf der Anmeldeseite */
if(!$option['uliID']){/* Zur Anmeldung */}


$option['leagueID']		= $uli['leagueID'];

$option['currentchildyear'] = $option['currentyear-2'];


// TODO in die verwaltung einbauen
// Jahre werden nicht mehr jedes Mal abgefragt sondern gespeichert
//$option['currentyear'] 	= get_current_year_uli();
//$option['currentchildyear'] = get_current_child_year_uli($option['currentyear']);


/* Alle Informationen der Liga werden abgelegt */
$league					= get_league($option['leagueID']);

/* Ende Options */


/**
 * Wie kann man die Menge der Funktionen sinnvoll aufteilen
 * Was sind Funktionen, die immer ben�tigt werden
 *
 * In den Dateien der MainLib stehen jeweils die Funktionen, die �bergreifend ben�tigt werden
 *
 *
 * Sortierung der Bereiche
 *
 * Basis Funktionen (DB, etc)
 * mainlib.php
 *  *
 * Finanzielle Berechnungen aller Art
 * lib_finances.php
 *
 * Vertr�ge, Transfers von Spielern
 * lib_contracts.php
 *
 * Spielereigenschaften, Punkte von Spielern
 * lib_player.php
 *
 * Alles zum Uli Verein
 * lib_uli.php
 *
 *
 *
 *
 */


/* Einbinden der Bibliotheken, die immer ben�tigt werden */
include('lib_player.php');
include('lib_userteams.php');
include('lib_finances.php');
include('lib_contracts.php');
include('lib_filter.php');
include('lib_transfers.php');
include('lib_teamranking.php');
include('lib_print.php');
include('lib_medien.php');
include('lib_message.php');

/* Einbinden der Sprachen */
include('lang_main.php');

/* Check, ob irgendwas getan werden muss */
// Aber nur, wenn es ein gültiges Team gibt (Sonst MySQL Errors)

if ($_REQUEST['short'] != 1 and $option['leagueID']){


	if ($_REQUEST['debug'] == 1){
		echo "checK";
	}

	/* wenn ein Spieltag beginnt */
	if (check_round_is_starting()){
		// update nextround
		$option['nextday'] = $option['nextday'] + 1;
		$cond[] = array("col" => "attribut", "value" => "nextday");
		$value[] = array("col" => "value", "value" => ($option['nextday']));
		uli_update_record('options', $cond, $value);
		write_userteams($option['nextday'] -1);
		pay_salary($option['nextday'] - 1);
		pay_dispo($option['nextday'] - 1);
	}

	/* Kredite */
	check_credits();

	/* Verhandlungen */
	check_negotiations();

	/* Transfers */
	check_transfers();

	/* Vertragsende */
	check_contracts();
}


/**
 * in wpdb syntx wegen anderer Tabelle
 */
function check_round_is_starting() {
	global $option, $wpdb;
	$sql = 'SELECT gametime FROM '.$wpdb->prefix.'pl_games '.
		'WHERE round = "'.$option['nextday'].'" '.
		'AND competition_id = '.$option['currentcompetition'].' ORDER by gametime asc LIMIT 1';
	$nextgametime = $wpdb->get_var($sql);

//echo '<br>'.mktime();

	// nochmal checken aufm server, wegen utc und so.
	if ((mktime() + 7200) > $nextgametime AND $option['nextday'] < 35){
		//echo "true";
		return TRUE;
	}
	return FALSE;
}

/**
 * schreibt die aufstellungen
 */
function write_userteams($round){
	global $option;
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "year", "value" => $option['currentyear']);
	$result = uli_get_results('userteams', $cond);
	if ($result){
		foreach ($result as $value){
			$value['round'] = $round;
			unset($value['ID']);
			$newentry = array();
			foreach($value as $key => $entry){
				$newentry[] = array("col" => $key, "value" => $entry);
			}
			unset($cond);
			$cond[] = array("col" => "round", "value" => $round);
			$cond[] = array("col" => "uliID", "value" => $value['uliID']);
			$cond[] = array("col" => "year", "value" => $value['year']);
			$cond[] = array("col" => "number", "value" => $value['number']);
			if (!uli_get_results('userteams', $cond)){
				uli_insert_record('userteams', $newentry);
			}
			else {
				uli_update_record('userteams', $cond, $newentry);
			}
		}
	}
	unset($cond);
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "year", "value" => $option['currentyear']);
	$result = uli_get_results('userformation', $cond);
	if ($result){
		foreach ($result as $value){
			$value['round'] = $round;
			unset($value['ID']);
			$newentry = array();
			foreach($value as $key => $entry){
				$newentry[] = array("col" => $key, "value" => $entry);
			}
			unset($cond);
			$cond[] = array("col" => "round", "value" => $round);
			$cond[] = array("col" => "uliID", "value" => $value['uliID']);
			$cond[] = array("col" => "year", "value" => $value['year']);
			if (!uli_get_results('userformation', $cond)){
				uli_insert_record('userformation', $newentry);
			}
			else {
				uli_update_record('userformation', $cond, $newentry);
			}
		}
	}
}

/**
 * bezahlt das gehalt
 */
function pay_salary($round){
	global $option;
	$cond[] = array("col" => "history", "value" => 0);
	$fields = array('SUM(salary), uliID');
	$result = uli_get_results('player_contracts', $cond, $fields, NULL, NULL, 'Group by uliID');
	if ($result){
		foreach($result as $result){
			calculate_money(1, $result['SUM(salary)'], $result['uliID'], $round, $option['currentyear'], $action='new', $type='outgoings');
		}
	}
}

/**
 * bezahlt die dispozinsen
 */
function pay_dispo($round){
	global $option;
	$cond[] = array("col" => "type", "value" => 14);
	$result = uli_get_results('finances', $cond);
	if ($result){
		foreach($result as $result){
			if ($result['sum'] < 0){
				$zinsen = abs(round($result['sum']/100*2));
				calculate_money(13, $zinsen, $result['uliID'], $round, $option['currentyear'], $action='new', $type='outgoings');
			}
		}
	}
}


?>
