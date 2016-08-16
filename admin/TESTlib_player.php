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
Kicker-Rangliste	x (player)
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
Spiele fŸr und nach Uli Klubs		x (calc)
davon auf der Bank					x (calc)
davon benotet						x (calc)
davon KapitŠn						x (calc)
Punkte fŸr und nach Uli Klubs		x (calc)
verkaufte Trikots fŸr und nach
Uli Klubs							x (calc)
Marktwert										x 
Status im Team									x (in der lib_player.php - ingesamt 8 stati)
akt. Zufriedenheit								x (0-100) Welche Einflussfaktoren?
Charakter (?)									x
LoyalitŠt										x (wenn laenger als ein Jahr im Verein, dann Marker setzen)
Gespraechsbereitschaft (lauf. Verhandl.)		x ("Aktionen")									
Gehaltswunsch (lauf. Verhandl.)					x ("Aktionen")
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
Vertragsverhandlung				Erfolgreich?
neues Gehalt					hoeher oder niedriger?
Anfrage eines anderen Klubs		Gehaltsangebot?
Wechsel							besserer oder schlechterer Klub
Neueinkauf eines Spielers		Konkurrent? (gleiche Position, Marktwert)

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
 * $items = all fŸr alles oder einzelne Datenfelder 
 * return $player
 * ein Array mit allem drinne
 */
/*
function get_player_infos($playerID, $leagueID = '', $items = array()) {
global $option, $wpdb;	

// Um den Aufruf einfacher zu machen
if ($items[0] == 'all'){$items = array('transfers', 'contracts', 'soldtrikots', 'league_games');}

// Hier die allgemeinen Infos
$sql = 'SELECT * FROM tip_uli_player p ';
if ($leagueID) {$sql .= 'LEFT JOIN tip_uli_player_league pl ON p.ID = pl.playerID AND pl.leagueID = '.$leagueID.' ';}
$sql .= 'WHERE p.ID = '.$playerID.' ';
$player = $wpdb->get_row($sql, ARRAY_A);
$player['buliteam'] = $player['team'];
	
// Hier die Punkte
unset($cond);
$cond[] = array("col" => "playerID", "value" => $playerID);	
$order[] = array("col" => "year", "sort" => "ASC");
$order[] = array("col" => "round", "sort" => "ASC");
$player_score = uli_get_results('player_points', $cond, NULL, $order);
if ($player_score){
	foreach ($player_score as $score){
		$player['scores'][$score['year']][$score['round']] = $score['score'];
	}}	

	
	
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
			}}	
		}
	// Vertraege
	if(in_array("contracts", $items)){
		unset($cond);
		unset($order);
		$cond[] = array("col" => "playerID", "value" => $playerID);	
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$order[] = array("col" => "history", "sort" => "ASC");
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
		$cond[] = array("col" => "leagueID", "value" => $leagueID);
		$league_games = uli_get_results('player_league_games', $cond);
		if ($league_games){
			foreach ($league_games as $league_game){
				$player['league_games'][] = $league_game;
			}}		
		}
	}
	
	
if ($player){return $player;}
else {return FALSE;}	
}
*/
?>