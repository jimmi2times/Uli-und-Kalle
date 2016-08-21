<?php
/*
 * Created on 09.05.2009
 *
 *
 * migriert immer die Aktuelle (2008er) Datenbank auf den neuen Stand
 */


// TODO
// �berall die leagueIDs reparieren


require_once('../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "start", "sub" => "start");
uli_header();

global $wpdb;

// Nur einmal
$sql = "UPDATE  `tip_uli_player_league` SET smile = 50 ";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';}
else {echo 'NO SUCCESS: '.$sql.'<br/>';}


$sql = "
CREATE TABLE  `tip_uli_stadium_seats` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`uliID` INT NOT NULL ,
`block` VARCHAR( 2 ) NOT NULL ,
`seats` INT NOT NULL ,
`type_of_seats` INT NOT NULL ,
`built` INT NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM ;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';}
else {echo 'NO SUCCESS: '.$sql.'<br/>';}




$sql = "
CREATE TABLE  `tip_uli_stadium_infra` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`uliID` INT NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
`sum` INT NOT NULL ,
`built` INT NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM ;";
if ($wpdb->query($sql)){echo 'SUCCESS: '.$sql.'<br/>';}
else {echo 'NO SUCCESS: '.$sql.'<br/>';}




// Geht immer





/* Footer */
uli_footer();





?>
