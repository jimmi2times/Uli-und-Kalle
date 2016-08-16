<?php
/*
 * Created on 18.03.2009
 *
 * Mainlib
 *
 * Hier liegen alle Basisfunktionen, die immer ben�tigt werden
 */


/* Funktionen f�r die Managerteams */

/**
 * holt die uliID
 * in Abh�ngigkeit der UserID
 * 18.03.09
 */
function get_uliID($user) {
	$cond[] = array("col" => "userID", "value" => $user);
	$result = uli_get_var('uli', $cond, 'ID');
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * holt alle Daten eines UliTeams
 * in Abh�ngigkeit der UliID
 * 30.03.09
 */
function get_uli($ID) {
	$cond[] = array("col" => "ID", "value" => $ID);
	$result = uli_get_row('uli', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}


/**
 * holt Manager einer Liga
 * 28.05.09
 */
function get_ulis($leagueID) {
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$result = uli_get_results('uli', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * holt alle Daten eines UliTeams
 * in Abh�ngigkeit der userID
 * 30.03.09
 */
function get_uli_userID($ID) {
	$cond[] = array("col" => "userID", "value" => $ID);
	$result = uli_get_row('uli', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/** function get_user_name
 *
 * liefert nach ID den usernamen
 * bleibt in diesem Muster, da nicht auf eine Uli Tabelle zugegriffen wird
 * 15.01.09
 */
function get_user_name($ID) {
	global $wpdb;
	$sql = 	'SELECT display_name FROM '.$wpdb->prefix.'users WHERE ID = "'.$ID. '" ';
	$value = $wpdb->get_var($sql);
	return $value;
}

/* Funktionen f�r die Ligen */
/**
 * Holt alle Daten einer Liga nach ID
 * 25.03.09
 *
 * hiess vorher get_league_data
 */
function get_league($ID) {
	$cond[] = array("col" => "ID", "value" => $ID);
	$result = uli_get_row('leagues', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * holt alle Ligen
 *
 * 12.01.11
 * @return unknown_type
 */
function get_leagues() {
	$result = uli_get_results('leagues');
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * holt alle Spieler, die gerade in einem Bundesligateam unter Vertrag sind
 * 21.03.2011
 */
function get_players(){
	$cond[] = array("col" => "team", "value" => "999", "func" => "!=");
	$players = uli_get_results('player', $cond);
	if ($players){return $players;}
	else {return FALSE;}
}


/**
 * Holt die Angaben zu einem Jahr nach ID
 * 25.03.09
 */
function get_uli_year($ID){
	$cond[] = array("col" => "ID", "value" => $ID);
	$results = uli_get_row('years', $cond);
	if ($results){return $results;}
	else {return FALSE;}
}

/* Funktionen fuer die Jahre */

/**
 * holt alle aktiven Haupt-Jahre des Managerspiels
 * 16.01.09
 * 25.03.09
 * 03.08.15
 */
function get_uli_years($sort = '', $noLimit = ''){
	global $option, $league;
	if (!$sort){$sort = 'ASC';}
	if (!$noLimit){
		$cond[] = array("col" => "ID", "value" => $league['startyear'], "func" => ">=");
	}
	$cond[] = array("col" => "parent", "value" => 0);
	$cond[] = array("col" => "start", "value" => mktime(), "func" => "<");
	$order[]= array("col" => "ID", "sort" => $sort);
	$results = uli_get_results('years', $cond, NULL, $order);
	if ($results){return $results;}
	else {return FALSE;}
}

/**
 * holt alle aktiven Unterjahre des Managerspiels
 * 12.06.09
 */
function get_uli_child_years(){
	global $option, $league;
	$cond[] = array("col" => "ID", "value" => $league['startyear'], "func" => ">=");
	$cond[] = array("col" => "parent", "value" => 0, "func" => "!=");
	$cond[] = array("col" => "start", "value" => mktime(), "func" => "<");
	$order[]= array("col" => "ID", "sort" => "ASC");
	$results = uli_get_results('years', $cond, NULL, $order);
	if ($results){return $results;}
	else {return FALSE;}
}


/**
 * holt das aktuelle Uli Jahr
 * (nur die Saison-Jahre nicht die Halbserien)
 * 18.03.09
 */
function get_current_year_uli(){
	global $option;
	$cond[] = array("col" => "archived", "value" => 0);
	$cond[] = array("col" => "parent", "value" => 0);
	$cond[] = array("col" => "ID", "value" => $option['league']['startyear'], "func" => ">=");
	$order[] = array("col" => "ID", "sort" => "ASC");
	$var = uli_get_var('years', $cond, 'ID', $order);
	if ($var){return $var;}
	else {return FALSE;}
}

/**
 * holt das aktuelle Unterjahr eines Jahrs
 * 08.06.09
 */
function get_current_child_year_uli($ID){
	global $option;
	$cond[] = array("col" => "archived", "value" => 0);
	$cond[] = array("col" => "parent", "value" => $ID);
	$order[] = array("col" => "ID", "sort" => "ASC");
	$var = uli_get_var('years', $cond, 'ID', $order);
	if ($var){return $var;}
	else {return FALSE;}
}


/* Datenbank schonende Funktionen */

/**
 * Liefert ein array mit allen Ulinamen (einer Liga)
 * Array Z�hler entspricht der UliID
 * 14.04.09
 */
function get_all_uli_names($leagueID = ''){
	if ($leagueID){$cond[] = array("col" => "leagueID", "value" => $leagueID);}
	$fields = array('ID', 'uliname');
	$result = uli_get_results('uli', $cond, $fields);
	if ($result){
		foreach ($result as $name){
			$uliname[$name['ID']] = $name['uliname'];
		}}
		if ($uliname){return $uliname;}
		else {return FALSE;}
}

/**
 * Liefert ein array mit allen Bundesligateamname
 * Array Zaehler entspricht der TeamID aus der uli_teams Tabelle
 * 14.04.09
 */
function get_all_team_names(){
	$cond[] = array("col" => "active", "value" => 0);

	$fields = array('ID', 'teamname');
	$result = uli_get_results('teams', $cond, $fields);
	if ($result){
		foreach ($result as $name){
			$teamname[$name['ID']] = $name['teamname'];
		}}
		if ($teamname){return $teamname;}
		else {return FALSE;}
}

/**
 * holt einen einzelnen (Bundesliga)Teamnamen
 * 06.05.09
 */
function get_team_name($ID) {
	$cond[] = array("col" => "ID", "value" => $ID);
	$result = uli_get_var('teams', $cond, 'teamname');
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * gibt, wenn vorhanden, das wappen fuer den Bundesligaverein zurueck
 * 15.04.09
 */
function get_ligateam_wappen($team, $ligateam){
	global $option;
	if (file_exists($option['ulidirroot'].'/theme/graphics/ligateams/'.$team.'.jpg')){
		$html = '<img src = "'.$option['uliroot'].'/theme/graphics/ligateams/'.$team.'.jpg" title="'.$ligateam[$team].'" alt="'.$ligateam[$team].'" height = 12>';
	}
	if ($html){return $html;}
	else {return FALSE;}
}


/* Neue Datenbankfunktionen */

/**
 * Funktion uli_get_results
 * liefert ein Array zur�ck
 * Es muss mindestens die tabelle angeben werden
 * 15.01.09
 */
function uli_get_results($table, array $conditions = NULL, array $fields = NULL, array $order = NULL, $limit = '', $group = '', $sql='', $debug = ''){
	global $wpdb;
	if (!$table){return false;}
	elseif ($table == "uli"){$table = 'tip_uli';}
	else {$table = 'tip_uli_'.$table;}
	/* Alle agefragten Felder */
	if (!$fields){$fieldstring = '*';}
	else {$fieldstring = implode(", ", $fields);}
	/* Limit */
	if ($limit){$limitstring = 'LIMIT '.$limit;}
	/* Sortierung */
	if (count($order) > 0){
		$orderArray = array();
		foreach ($order as $order){
			$orderArray[] = $order['col'] .' '.$order['sort'];
		}
		$orderquery = " ORDER BY ".implode(", ", $orderArray);
	}
	/* Bedingungen */
	if(count($conditions) > 0) {
		$criteriaArray = array();
		foreach($conditions as $condition) {
			if (!$condition['func']){$condition['func'] = '=';}
			if ($condition['value'] AND $condition['func'] != "IN"){$condition['value'] = '"'.$condition['value'].'"';}
			if ($condition['conj'] == "OR"){$or = "yes";}

			$conditionArray[] = $condition['col'] .' '.$condition['func'].' '.$condition['value'].' ';
		}
		if ($or == "yes"){
			$conditionquery = " WHERE ".implode(" OR ", $conditionArray);
		}
		else {
			$conditionquery = " WHERE ".implode(" AND ", $conditionArray);
		}
	}
	/* Zusammensetzen der sql-Abfrage */
	$sql = 'SELECT '.$fieldstring.' FROM '.$table.' '.$conditionquery.' '.$group.' '.$orderquery.' '.$limitstring;

	/* Debugg. */
	if ($_REQUEST['debug'] == "getresults" OR $debug == 1){global $user_ID;if ($user_ID == 1){echo $sql;}}

	$results = $wpdb->get_results($sql, ARRAY_A);
	if($results){return $results;}
	else {return FALSE;}
}


/**
 * Funktion uli_get_var
 * liefert eine einzelne Variable zur�ck
 * Es muss Tabelle, abgefragtes Feld, Bedingung spezifiziert werden
 * Limit ist zwangsl�ufig 1
 * Sortierung bei der DB Abfrage ist optional
 * 15.01.09
 */
function uli_get_var($table, array $conditions = NULL, $field, array $order = NULL, $group='', $limit='', $sql=''){
	global $wpdb;
	if (!$table){return false;}
	elseif ($table == "uli"){$table = 'tip_uli';}
	else {$table = 'tip_uli_'.$table;}
	if (!$field){return false;}
	/* Limit */
	$limitstring = 'LIMIT 1';
	if ($limit){$limitstring = $limit;}
	/* Sortierung */
	if (count($order) > 0){
		$orderArray = array();
		foreach ($order as $order){
			$orderArray[] = $order['col'] .' '.$order['sort'];
		}
		$orderquery = " ORDER BY ".implode(", ", $orderArray);
	}

	/* Bedingungen */
	if(count($conditions) > 0) {
		$criteriaArray = array();
		foreach($conditions as $condition) {
			if (!$condition['func']){$condition['func'] = '=';}
			$conditionArray[] = $condition['col'] .' '.$condition['func'].' "'.$condition['value'].'" ';
		}
		$conditionquery = " WHERE ".implode(" AND ", $conditionArray);
	}
	/* Zusammensetzen der sql-Abfrage */
	$sql = 'SELECT '.$field.' FROM '.$table.' '.$conditionquery.' '.$group.' '.$orderquery.' '.$limitstring;
	$result = $wpdb->get_var($sql);

	/* Debugg. */
	if ($_REQUEST['debug'] == "getvar"){global $user_ID;if ($user_ID == 1){echo $sql;}}


	if($result){return $result;}
	else {return FALSE;}
}


/**
 * Funktion uli_get_row
 * liefert eine einzelne Zeile zur�ck
 * Es muss Tabelle, und Bedingung spezifiziert werden
 * Limit ist zwangsl�ufig 1
 * Sortierung bei der DB Abfrage ist optional
 * 19.03.09
 */
function uli_get_row($table, array $conditions = NULL, array $order = NULL, $group = '', $limit='', $sql=''){
	global $wpdb;
	if (!$table){return false;}
	elseif ($table == "uli"){$table = 'tip_uli';}
	else {$table = 'tip_uli_'.$table;}
	/* Limit */
	$limitstring = 'LIMIT 1';
	if ($limit){$limitstring = $limit;}
	/* Sortierung */
	if (count($order) > 0){
		$orderArray = array();
		foreach ($order as $order){
			$orderArray[] = $order['col'] .' '.$order['sort'];
		}
		$orderquery = " ORDER BY ".implode(", ", $orderArray);
	}
	/* Bedingungen */
	if(count($conditions) > 0) {
		$criteriaArray = array();
		foreach($conditions as $condition) {
			if (!$condition['func']){$condition['func'] = '=';}
			$conditionArray[] = $condition['col'] .' '.$condition['func'].' "'.$condition['value'].'" ';
		}
		$conditionquery = " WHERE ".implode(" AND ", $conditionArray);
	}
	/* Zusammensetzen der sql-Abfrage */
	$sql = 'SELECT * FROM '.$table.' '.$conditionquery.' '.$group.' '.$orderquery.' '.$limitstring;
	$result = $wpdb->get_row($sql, ARRAY_A);

	/* Debugg. */
	if ($_REQUEST['debug'] == "getrow"){global $user_ID;if ($user_ID == 1){echo $sql;}}

	if($result){return $result;}
	else {return FALSE;}
}


/**
 * aktualisiert einen Eintrag
 * Tabelle, Feld, Wert und Bedingung m�ssen �bergeben werden
 * 19.03.09
 */
function uli_update_record($table, array $conditions = NULL, array $values = NULL, $sql=''){
	global $wpdb;
	if (!$table){return false;}
	elseif ($table == "uli"){$table = 'tip_uli';}
	else {$table = 'tip_uli_'.$table;}
	/* Bedingungen */
	if(count($conditions) > 0) {
		$criteriaArray = array();
		foreach($conditions as $condition) {
			if (!$condition['func']){$condition['func'] = '=';}

			if ($condition['func'] == "IN"){
				$conditionArray[] = $condition['col'] .' '.$condition['func'].' ('.$condition['value'].') ';
			}
			else {
				$conditionArray[] = $condition['col'] .' '.$condition['func'].' "'.$condition['value'].'" ';
			}

		}
		$conditionquery = " WHERE ".implode(" AND ", $conditionArray);
	}

	if(count($values) > 0) {
		$valueArray = array();
		foreach($values as $value) {
			$valueArray[] = $value['col'] .' = "'.$value['value'].'" ';
		}
		$valueQuery = " SET ".implode(", ", $valueArray);
	}

	/* Zusammensetzen der sql-Abfrage */
	$sql = 'UPDATE '.$table.' '.$valueQuery.' '.$conditionquery;

	/* Debugg. */
	if ($_REQUEST['debug'] == "update"){global $user_ID;if ($user_ID == 1){echo $sql;}}


	if($wpdb->query($sql)){return TRUE;}
	else {return FALSE;}
}


/**
 * fügt einen Eintrag ein
 * Tabelle und Werte müssen übergeben werden
 * 27.7.15
 * umgestellt auf neue mysql syntax (bzw. die bessere wordpress myql klasse)
 */
function uli_insert_record($table, array $values = NULL, $sql=''){
	global $wpdb;
	if (!$table){return false;}
	elseif ($table == "uli"){$table = 'tip_uli';}
	else {$table = 'tip_uli_'.$table;}
	if(count($values) > 0) {
		$valueFieldsArray = array();
		$valueValuesArray = array();
		foreach($values as $value) {
			$thisValues[$value['col']] = $value['value'];			
			$valueFieldsArray[] = '`'.$value['col'].'`';
			$valueValuesArray[] = '\''.$value['value'].'\'';
		}
		$valueFieldsQuery = " (".implode(", ", $valueFieldsArray).")";
		$valueValuesQuery = " VALUES (".implode(", ", $valueValuesArray).")";
	}
	/* Debugg. */
	$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : null;	
	if ($debug == "insert"){global $user_ID;if ($user_ID == 1){echo $sql;}}

	if ($wpdb->insert($table, $thisValues)){
		return 	$wpdb->insert_id;
	} else {
		return false;
	}
}


/**
 * L�scht einen bzw. mehrere Eintr�ge
 * Tabelle und Bedingung muss �bergeben werden
 * 19.03.09
 */
function uli_delete_record($table, array $conditions = NULL){
	global $wpdb;
	if (!$table){return false;}
	elseif ($table == "uli"){$table = 'tip_uli';}
	else {$table = 'tip_uli_'.$table;}
	/* Bedingungen */
	if(count($conditions) > 0) {
		$criteriaArray = array();
		foreach($conditions as $condition) {
			if (!$condition['func']){$condition['func'] = '=';}
			$conditionArray[] = $condition['col'] .' '.$condition['func'].' "'.$condition['value'].'" ';
		}
		$conditionquery = " WHERE ".implode(" AND ", $conditionArray);
	}
	/* Zusammensetzen der sql-Abfrage */
	$sql = 'DELETE FROM '.$table.' '.$conditionquery;

	/* Debugg. */
	if ($_REQUEST['debug'] == "delete"){global $user_ID;if ($user_ID == 1){echo $sql;}}

	if($wpdb->query($sql)){return TRUE;}
	else {return FALSE;}
}


/************************/
/* f�r die Berechnungen */
/************************/

/**
 * linear regression function
 * @param $x array x-coords
 * @param $y array y-coords
 * @returns array() m=>slope, b=>intercept
 *
 * Erstellt die m und b Parameter f�r y = mx* b
 */
function linear_regression($x, $y) {
	// calculate number points
	$n = count($x);

	// ensure both arrays of points are the same size
	if ($n != count($y)) {
		trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);
	}

	// calculate sums
	$x_sum = array_sum($x);
	$y_sum = array_sum($y);

	$xx_sum = 0;
	$xy_sum = 0;

	for($i = 0; $i < $n; $i++) {
		$xy_sum+=($x[$i]*$y[$i]);
		$xx_sum+=($x[$i]*$x[$i]);
	}

	// calculate slope
	if ((($n * $xx_sum) - ($x_sum * $x_sum)) != 0){
		$m = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));
	}
	// calculate intercept
	$b = ($y_sum - ($m * $x_sum)) / $n;

	// return result
	return array("m"=>$m, "b"=>$b);
}


/**
 * uli_atan_function
 * Diese Funktion berechnet nach Uebergabe eines x Wertes einen y Wert zwischen 0 und 10
 * Der x Wert muss zwischen 0 und 10 liegen (mit Komma)
 * Das steht alles in einer Funktion, damit diese global justiert werden kann
 *
 * Stand 1.6.
 * Die Funktion ist
 * 10 / PI * (atan(x - 5) + PI/2)
 * wenn der wert unter 1 liegt greift eine lineare funktion
 * die atan funktion ist bei x = 1 -> y = 0,77, also geht es von da abwaerts
 * zurueck geliefert wird ein y Wert, der dann als Faktor (nach linearer Regression fuer den zu berechnenden Wert genommen werden kann)
 */
function uli_atan_function($x){
	$y = (10)/pi() * (Atan($x-(5)) + pi()/2 );
	if ($y < 0.77979){
		$y = 0.77979 * $x;
	}
	if ($y < 0){
		$y = 0;
	}
	if ($y){
		return $y;
	}
	else {
		return FALSE;
	}
}

/**
 * fuehert eine Berechnung durch, die fuer eine Vielzahl von Formeln genutzt werden sollte
 * Es werden Quelle und Zielspannen uebergeben und mithilfe der linearen Regression und atan Funktion wird ein Y Wert aus der Zielspanne uebergeben
 * Dadurch muss in den eigentlichen Berechnungsformeln nur die Spanne definiert werden
 * und dort sind nich mehr so viele Formeln im Quelltext
 *
 * @param unknown_type $x
 * @param unknown_type $SpanDest
 * @param unknown_type $SpanSource
 * @param unknown_type $yArray
 * @return INT
 */
function uli_calculate_function($x, $SpanDest, $SpanSource, $yArray = NULL) {
	if (!$yArray){$yArray = array(0,10);}

	$mbDest = linear_regression(array($SpanDest['min'], $SpanDest['max']), $yArray);
	$mbSource = linear_regression(array($SpanSource['min'], $SpanSource['max']), $yArray);
	$x = $mbSource['m'] * $x + $mbSource['b'];
	$y = uli_atan_function($x);
	$yReturn = ($y - $mbDest['b'])/ $mbDest['m'];
	settype($yReturn, INT);
	if ($yReturn < $SpanDest['min']){
		$yReturn = $SpanDest['min'];
	}
	if ($yReturn){
		return $yReturn;
	}
	else {
		return FALSE;
	}
}


/**
 * berechnet dynamisch einen "schaetzwert"
 * nimmt einen varibalen +- wert und sagt dann ca. x bis y
 * es wird die letzte stelle der uebergebenen Zahl (playerID, etc) genommen und dann sortiert
 * das ist immer gleich aber zufaellig genug :)
 */
function get_estimated_value($value, $faktor = ''){

	if (!$faktor){
		$faktor = 1;
	}
	$faktor = $faktor % 10;


	if ($faktor >= 0){
		$minvalue = $value * 7 / 10;
		$maxvalue = $value * 11 / 10;
	}
	if ($faktor >= 3){
		$minvalue = $value * 8 / 10;
		$maxvalue = $value * 12 / 10;
	}
	if ($faktor >= 5){
		$minvalue = $value * 9 / 10;
		$maxvalue = $value * 12 / 10;
	}
	if ($faktor >= 8){
		$minvalue = $value * 9 / 10;
		$maxvalue = $value * 13 / 10;
	}


	if ($maxvalue < 1000000){$maxvalue = round($maxvalue, -3);}
	if ($minvalue < 1000000){$minvalue = round($minvalue, -3);}

	$result['minvalue'] = $minvalue;
	$result['maxvalue'] = $maxvalue;
	return ($result);
}

?>
