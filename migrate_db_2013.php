<?php
/*
 * Created on 09.05.2009
 *
 *
 * migriert immer die Aktuelle (2008er) Datenbank auf den neuen Stand
 */


// TODO
// †berall die leagueIDs reparieren


require_once('../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "start", "sub" => "start");
uli_header();

global $wpdb;


/***
 * 
 * ZUFRIEDENHEIT
 * 
 */


$sql = "
CREATE TABLE  `tip_uli_player_league_smile` (
`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`playerID` INT NOT NULL ,
`uliID` INT NOT NULL ,
`leagueID` INT NOT NULL ,
`timestamp` INT NOT NULL ,
`round` INT NOT NULL ,
`year` INT NOT NULL ,
`smile` INT NOT NULL ,
`active` INT NOT NULL
) ENGINE = MYISAM ;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}


$results = uli_get_results("player_league");
if ($results){
	foreach ($results as $player){
		//$smile = $player['smile'];
		$values = array();
		$values[] = array("col" => "timestamp", "value" => mktime());
		$values[] = array("col" => "year", "value" => 21);
		$values[] = array("col" => "playerID", "value" => $player['playerID']);
		$values[] = array("col" => "smile", "value" => $player['smile']);
		$values[] = array("col" => "leagueID", "value" => 1);
		$values[] = array("col" => "active", "value" => 1);
		$values[] = array("col" => "uliID", "value" => $player['uliID']);
		//print_r($values);
		
		// NUR EINMAL SONST WIRD DIE DB ZUGEM†LLT
		//uli_insert_record("player_league_smile", $values);		
		
	}
}


$sql = "
ALTER TABLE  `tip_uli_player_league` DROP  `smile`";

if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}	


/***
 * 
 * ENDE ZUFRIEDENHEIT
 * 
 */

/**
 * DB BEREINIGUNGEN
 * 
 */
$sql = "
Delete from `tip_uli_finances` WHERE uliID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
ALTER TABLE  `tip_uli_finances` DROP  `uliIDold`";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}	

$sql = "
Delete from `tip_uli_games` WHERE year < 21;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_games_table` WHERE year < 21;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_journal_articles` WHERE status = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_merch_soldtrikots` WHERE uliID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_positions` WHERE uliID > 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_results` WHERE uliID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_stadium_seats` WHERE uliID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_transfers` WHERE playerID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_tv_contracts` WHERE uliID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_umfrage` WHERE uliID > 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_userformation` WHERE uliID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

$sql = "
Delete from `tip_uli_userteams` WHERE uliID = 0;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';} 
else {echo 'NO SUCCESS: '.$sql.'<br/>';}

/* Footer */
uli_footer();

/**
 * ENDE DB BEREINIGUNGEN
 */



?>
