<?php
/*
Was muss regelmaessig in der Datnebank gecheckt werden um Inkosistenzen aufzutreiben
Hier kann erst einmal ruhig alles rein, was auch in der Migrationszeit gebraucht wird
*/


require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');


/* Header */
$page = array("main" => "123124", "sub" => "134234");
uli_header();
global $wpdb;


// TODO check ob es doppelte 0 Werte in der finance tabelle gibt...

// Gibt es fuer jeden Eintrag in der "player" Tabelle pro Liga einen Eintrag in "player_league"?
// Wenn nicht, muss der erzeugt werden
// Wie wird der Widerspruch mit "alten" Spielern aufgeloest. Also Spielern, die beim Erstellen der Liga schon nicht mehr da waren?
// Was ist der Marker um rauszukriegen, ob ein Spieler noch "relevant" fuer eine Liga ist?
// GEHT: team != 999 OR minimum ein Eintrag bei "league_games" ??? 
// Damit duerften auch die ganzen unrelevanten Karteileichen abgeschossen werden




// Bereinigen der player Tabellen
// Wenn kein Eintrag der PlayerID in den leagueID Tabellen (der Typ also nicht eine einzige Spur hinterlassen hat) kann der Eintrag auch geloescht werden
// Damit duerften eigentlich so diverse lustige Ersatzspieler wegfallen an die sich sowieso keiner erinnern kann


echo 'hallo';
/*
// LeagueID EintrŠge repariern in der Tabelle Userteams
echo $sql = 'SELECT * FROM  `tip_uli_userteams` WHERE leagueID = 0 OR leagueID IS NULL';
echo $result = $wpdb->get_results($sql, ARRAY_A);

print_r($result);

if ($result){
	foreach($result as $result){
		$uli = get_uli($result['uliID']);
		echo $sql ="UPDATE `tip_uli_userteams` set leagueID = ".$uli['leagueID']." where ID = ".$result['ID']." ";
		if ($wpdb->query($sql)){} 	
	}}
else {
	echo 'Nichts zu reparien in "userteams"<br/>';
}

*/


// LeagueID EintrŠge repariern in der Tabelle Uli_Results
echo $sql = 'SELECT * FROM  `tip_uli_results`';
$result = $wpdb->get_results($sql, ARRAY_A);

if ($result){
	foreach($result as $result){
		$uli = get_uli($result['uliID']);
		echo $sql ="UPDATE `tip_uli_results` set leagueID = ".$uli['leagueID']." where ID = ".$result['ID']." ";
		if ($wpdb->query($sql)){} 	
	}}
else {
	echo 'Nichts zu reparien in "userteams"<br/>';
}


// Die UliID eintrŠge in "Contracts" sind teileise falsch. Das ist der bekannt Bug, der immer zu Problemen fuehrte, wenn ein Team aufgeloest wurde
echo $sql = 'SELECT * FROM  `tip_uli_player_league` WHERE uliID > 0';
$result = $wpdb->get_results($sql, ARRAY_A);
if ($result){
	foreach($result as $result){
		//$uli = get_uli($result['uliID']);
		echo $sql ="UPDATE `tip_uli_player_contracts` set uliID = ".$result['uliID']." where playerID = ".$result['playerID']." AND leagueID = ".$result['leagueID']." AND history = 0";
		echo '<br/>';
		if ($wpdb->query($sql)){} 	
	}}
else {
	echo 'Nichts zu reparien in "userteams"<br/>';
}


// Die UliID die bei claimuliID stehen muessten, stehen da nicht.
echo $sql = 'SELECT * FROM  `tip_uli_player_league` WHERE uliID > 0';
$result = $wpdb->get_results($sql, ARRAY_A);
if ($result){
	foreach($result as $result){
		//$uli = get_uli($result['uliID']);
		echo $sql ="UPDATE `tip_uli_auctions` set claimuliID = ".$result['uliID']." where playerID = ".$result['playerID']." AND leagueID = ".$result['leagueID']." AND history = 0";
		echo '<br/>';
		if ($wpdb->query($sql)){} 	
	}}
else {
	echo 'Nichts zu reparien in "userteams"<br/>';
}



uli_footer();
?>