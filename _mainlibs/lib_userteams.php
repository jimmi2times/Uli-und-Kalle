<?php
/*
 * Created on 23.03.2009
 *
 * Alles was mit den Mannschaften, den Punkten, den Ergebnissen und den Formationen zu tun hat
 */
 




// schreibt oder updated das userteam
function nominate_team($round, $player, $userformation) {
global $user_ID, $wpdb, $CONFIG;
$uliID = get_uliID($user_ID);
// wenn eintrag existiert update sonst insert
$numberofgames = get_attribute('numberofrounds');

	for ($x=1; $x<=15; $x++) {
	if (!check_existing_uli_userteams($x, $roundcount)) {
	$sql  = 'INSERT INTO '.$CONFIG->prefix.'uli_userteams '.
			'(playerID, uliID, round, number) VALUES ('.
			'"'.$player[$x].'",'.
			'"'.$uliID.'",'.
			'"'.$round.'",'.
			'"'.$x.'"'.				
			')';
	}
	else {
	$sql = 'UPDATE '.$CONFIG->prefix.'uli_userteams SET '.
		' playerID 	 = '.$player[$x]. ' '.
		' WHERE uliID = '.$uliID.
		' AND number   = '.$x.
		' AND round = '.$round;
	}
	if ($wpdb->query($sql)){}
	}
	
	$formation = get_userformation($roundcount);
	if (!$formation) {
	$sql  = 'INSERT INTO '.$CONFIG->prefix.'uli_userformation '.
			'(uliID, round, formation) VALUES ('.
			'"'.$uliID.'",'.
			'"'.$round.'",'.
			'"'.$userformation.'"'.				
			')';
	}
	else {
	$sql = 'UPDATE '.$CONFIG->prefix.'uli_userformation SET '.
		' formation 	 = '.$userformation. ' '.
		' WHERE uliID = '.$uliID.
		' AND round = '.$round;
	}
	if ($wpdb->query($sql)){}
  }




/** 
 * TODO hier sind noch ein Haufen alter Tabellennamen drinne
 * TODO deswegen erst einmal ein einfache Variante
 * Holt das Team eines Managers
 * Soll die Sortierung hier schon gemacht werden ???
 * Wie soll die Sortierung umgesetzt werden ???
 * Es werden gleich alle Infos geholt die in der Anzeige zu sehen sind
 */
function get_user_team($uliID) {
global $option;
$cond[] = array("col" => "uliID", "value" => $uliID);		
$result = uli_get_results('player_league', $cond, NULL);
if ($result){return $result;}
else {return FALSE;}	
}


function get_user_team_sort($uliID, $sort = '') {
global $option;
// Baut den Join zusammen 
$tableString  = 'player_league up ';
$tableString .= ' LEFT JOIN '.$option['prefix'].'uli_player p ON up.playerID = p.ID ';
$tableString .= ' LEFT JOIN '.$option['prefix'].'uli_player_contracts c ON c.playerID = up.playerID AND c.leagueID = '.$option['leagueID'].' AND history = 0';

// Sortierung 
if ($sort == "position"){$order[] = array("col" => "p.hp");}
if ($sort == "name"){$order[] = array("col" => "p.name");}
if ($sort == "age"){$order[] = array("col" => "p.birthday", "sort" => "DESC");}
if ($sort == "jerseynumber"){$order[] = array("col" => "up.jerseynumber");}
if ($sort == "marktwert"){$order[] = array("col" => "up.marktwert", "sort" => "DESC");}
if ($sort == "salary"){$order[] = array("col" => "c.salary", "sort" => "DESC");}
if ($sort == "contractend"){$order[] = array("col" => "c.end", "sort" => "ASC");}

$cond[] = array("col" => "up.uliID", "value" => $uliID);		
$result = uli_get_results($tableString, $cond, NULL, $order);
if ($result){return $result;}
else {return FALSE;}	
}




/** 
 * Holt einen Spieler aus dem Team eines Managers
 * Es werden gleich alle Infos geholt die in der Anzeige zu sehen sind
 * 
 * TODO braucht man das noch? kann das nicht durch die normale get_player_infos erledigt werden?
 * 
 */
function get_user_team_player($playerID, $uliID = '') {
global $option;
/* Baut den Join zusammen */
$tableString  = 'userplayer up ';
$tableString .= ' LEFT JOIN '.$option['prefix'].'uli_player p ON up.playerID = p.ID ';
$tableString .= ' LEFT JOIN '.$option['prefix'].'uli_contracts c ON c.playerID = up.playerID AND c.leagueID = '.$option['leagueID'].' AND history = 0';
$tableString .= ' LEFT JOIN '.$option['prefix'].'uli_playerattributes pa ON pa.playerID = up.playerID AND pa.leagueID = '.$option['leagueID'];

$cond[] = array("col" => "up.leagueID", "value" => $option['leagueID']);		
$cond[] = array("col" => "up.playerID", "value" => $playerID);		

$result = uli_get_row($tableString, $cond);
if ($result){return $result;}
else {return FALSE;}	
}

 
/**
 * unfinished
 * 23.03.09
 * Schreibt eine Aufstellung
 * (nimmt die aktuelle Aufstellung mit Wert 0 und überträgt sie auf einen Rundenwert)
 * macht das selbe mit der Formation
 * hier sollten ein paar Checks rein. 
 * - ist der Kapitän gesetzt (ansonsten der vom letzten Spieltag)
 * - ist ein Spieler doppelt dabei
 */ 
function write_formation($round, $year) {
//global $CONFIG, $wpdb;
//$sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli ';
//$result = $wpdb->get_results($sql, ARRAY_A);
//if ($result){
//	foreach($result as $uli){
//		$sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_userteams '.
//				' WHERE uliID = '.$uli['ID'].' '.
//				' AND year = "'.$year.'" '.
//				' AND round =  0 ';
//		$result = $wpdb->get_results($sql, ARRAY_A);
//		if ($result){
//			foreach ($result as $player){
//			if (!get_player_on_this_position_uli($round, $player['number'], $player['uliID'], $year)){
//				$sql = 	'INSERT INTO '.$CONFIG->prefix.'uli_userteams '.
//						' (`ID`, `playerID`, `uliID`, `round`, `number`, `year`)'.
//						' VALUES ("", '.$player['playerID'].', '.$player['uliID'].','.$round.','.$player['number'].',"'.$year.'")';
//				}
//			else {
//				$sql = 	'UPDATE '.$CONFIG->prefix.'uli_userteams '.
//						'SET playerID = '.$player['playerID'].' '.
//						' WHERE ID = '.$player['ID'].' ';
//				}
//			$wpdb->query($sql);
//			}
//		}
//	// Jetzt die formationen
//	$formation = get_uli_user_formation(0, $year, $uli['ID']);
//	if (isset($formation)){
//		if (!get_uli_user_formation($round, $year, $uli['ID'])){
//			$sql  = 'INSERT INTO '.$CONFIG->prefix.'uli_userformation '.
//					'(`uliID`, `round`, `formation`, `year`) VALUES ('.
//					'"'.$uli['ID'].'",'.
//					'"'.$round.'",'.
//					'"'.$formation.'", '.
//					'"'.$year.'"'.				
//					')';
//			}
//		else {
//			$sql = 'UPDATE '.$CONFIG->prefix.'uli_userformation SET '.
//				' formation 	 = '.$formation. ' '.
//				' WHERE uliID = '.$uli['ID'].
//				' AND year = "'.$year.'" '.
//				' AND round = '.$round;
//			}
//		$wpdb->query($sql);
//		}
//	}
// }
}

/**
 * holt nach Runde und Jahr eine Formation eines Benutzers
 * 23.03.09
 */
function get_uli_user_formation($round, $year, $uliID) {
$cond[] = array("col" => "round", "value" => $round);		
$cond[] = array("col" => "year", "value" => $year);	
$cond[] = array("col" => "uliID", "value" => $uliID);	
$result = uli_get_var('userformation', $cond, 'formation');
if ($result){return $result;}
else {return FALSE;}
} 



/**
 * Prueft, wieviel % der Spieler auf der gerade eingesetzen Position wert ist
 *
 * ab 2011 keine Unterscheidung HP und NP
 */
function get_position_faktor($position, $playerinfo) {
	if ($position['position'] == $playerinfo['hp'] AND !$position['foot']){$faktor = 1;}
	// HP und Fuß wichtig Fuß stimmt oder beidfüßig
	elseif ($position['position'] == $playerinfo['hp'] AND $position['foot'] AND ($position['foot'] == $playerinfo['foot'] OR $playerinfo['foot'] == 3)){$faktor = 1;}
	// HP richtig Fuß falsch
	elseif ($position['position'] == $playerinfo['hp'] AND $position['foot'] AND $position['foot'] != $playerinfo['foot'] AND $playerinfo['foot'] != 3){$faktor = 0.5;}
	// NP1 und Fuß nicht wichtig
	elseif ($position['position'] == $playerinfo['np1'] AND !$position['foot']){$faktor = 1;}
	// NP1 und Fuß wichtig Fuß stimmt oder beidfüßig
	elseif ($position['position'] == $playerinfo['np1'] AND $position['foot'] AND ($position['foot'] == $playerinfo['foot'] OR $playerinfo['foot'] == 3)){$faktor = 1;}
	// NP2 und Fuß nicht wichtig
	elseif ($position['position'] == $playerinfo['np2'] AND !$position['foot']){$faktor = 1;}
	// NP2 und Fuß wichtig Fuß stimmt oder beidfüßig
	elseif ($position['position'] == $playerinfo['np2'] AND $position['foot'] AND ($position['foot'] == $playerinfo['foot'] OR $playerinfo['foot'] == 3)){$faktor = 1;}


	// NP richtig Fuß falsch
	elseif ($position['position'] == $playerinfo['np1'] AND $position['foot'] AND $position['foot'] != $playerinfo['foot'] AND $playerinfo['foot'] != 3){$faktor = 0.5;}
	// NP richtig Fuß falsch
	elseif ($position['position'] == $playerinfo['np2'] AND $position['foot'] AND $position['foot'] != $playerinfo['foot'] AND $playerinfo['foot'] != 3){$faktor = 0.5;}


	else {$faktor = 0.25;}
	return $faktor;
}



/**
 * liefert die Position und den Fuss fuer einen Slot
 * @param unknown_type $formation
 * @param unknown_type $number
 * @return unknown_type
 */
function get_formation_position($formation, $number) {
	unset($position);
	if ($formation == "442"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}
	if ($formation == "4411"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}


	if ($formation == "433"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}
	if ($formation == "4213"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 7){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}
	if ($formation == "4321"){
		if ($number == 11){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 7){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}
	if ($formation == "343"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 2){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}

	if ($formation == "532"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}

	if ($formation == "352"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 2){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}
	if ($formation == "451"){
		if ($number == 11){$position['position'] = 7; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}
	if ($formation == "460"){
		if ($number == 11){$position['position'] = 6; $position['foot'] = '';}
		if ($number == 10){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 9){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 8){$position['position'] = 5; $position['foot'] = 2;}
		if ($number == 7){$position['position'] = 5; $position['foot'] = 1;}
		if ($number == 6){$position['position'] = 4; $position['foot'] = '';}
		if ($number == 5){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 4){$position['position'] = 3; $position['foot'] = '';}
		if ($number == 3){$position['position'] = 2; $position['foot'] = 2;}
		if ($number == 2){$position['position'] = 2; $position['foot'] = 1;}
		if ($number == 1){$position['position'] = 1; $position['foot'] = '';}
	}	
	return $position;
}
?>
