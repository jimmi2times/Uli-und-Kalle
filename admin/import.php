<?php 

require_once('../../config.php');
require_once('../../lib/lib.php');
global $CONFIG;
require_once($CONFIG->dirroot . '/module/admin/lib.php');

require_once($CONFIG->dirroot . '/wp-config.php' );
// check login //
if (!is_user_logged_in()){wp_redirect($CONFIG->wwwroot.'/wp-login.php?redirect_to='.$CONFIG->wwwroot.'/module/uli');}

require_once($CONFIG->dirroot . '/module/uli/_mainlibs/lib.php');
require_once($CONFIG->dirroot . '/module/uli/_mainlibs/lib_calculate_admin.php');

$action = $_REQUEST['action'];
$round = $_REQUEST['round'];
$action = $_REQUEST['action'];
$file = $_REQEUEST['file'];
///////////


$kickerID = $_REQUEST['kickerID'];
$playerID = $_REQUEST['playerID'];

if ($kickerID AND $playerID){
global $CONFIG, $wpdb;
$sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_calc WHERE kickerID = '.$kickerID.' ';
$player= $wpdb->get_row($sql);
    $sql = 'UPDATE '.$CONFIG->prefix.'uli_player '.
              ' Set nachname = "'.$player->nachname.'", '.
              ' vorname = "'.$player->vorname.'", '. 
              ' kickerID = "'.$player->kickerID.'", '. 
              ' birthday = "'.$player->birthday.'" '.
              ' WHERE ID = '.$playerID.' ';
    $wpdb->query($sql);


}

get_header(); 
get_k2_contentstyles_begin();



global $CONFIG, $ID, $spielerdaten, $value;
unset($spielerdaten);
// Die XML-Datei wird in die Variable $xmlFile eingelesen
$xmlFile = implode("", file("spielerdaten2008-09.xml"));

// Der Parser wird erstellt
$parser = xml_parser_create();
// Setzen der Handler
xml_set_element_handler($parser,"startElement","endElement");
// Setzen des CDATA-Handlers
xml_set_character_data_handler($parser, "cdata");
// Parsen
xml_parse($parser, $xmlFile);
// Gibt alle verbrauchten Ressourcen wieder frei.
xml_parser_free($parser);


global $wpdb;
	$sql = 'TRUNCATE '.$CONFIG->prefix.'uli_calc ';
                $wpdb->query($sql);

foreach ($spielerdaten as $key => $spieler){
	
if ($spieler->note[$round]){
ltrim($spieler->note[$round]);
$spieler->note[$round] = str_replace(',','.', $spieler->note[$round]);
settype($spieler->note[$round], "double");
}
else {$spieler->note[$round] = 'NULL';}
$spieler->name = trim($spieler->name);

	$sql = 'INSERT INTO '.$CONFIG->prefix.'uli_calc '.
			'(ID, nachname, vorname, kickerID, birthday, note, round, verein, groesse, gewicht, nationalitaet) VALUES '.
			'("","'.$spieler->name.'", '.
			'"'.$spieler->vorname.'", '.
			'"'.$key.'", '.			
			'"'.$spieler->birthday.'", '.
			''.$spieler->note[$round].', '.	
			'"'.$round.'", '.		
			'"'.$spieler->verein.'", '.
			'"'.$spieler->groesse.'", '.
			'"'.$spieler->gewicht.'", '.
			'"'.$spieler->nationalitaet.'" '.
			')';
	
	$wpdb->query($sql);
}

// jetzt den abgleich
// ersteinmal nur die nach kickerid

$sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_calc ';
$result = $wpdb->get_results($sql, ARRAY_A);
if ($result){
  foreach($result as $player){

$player['nachname'] = utf8_decode($player['nachname']); 
// $player['nachname']= strtolower($name); 
  $player['nachname']= str_replace(' - ', '-', $player['nachname']); 
  $player['nachname']= str_replace(' ', '-', $player['nachname']); 
  $player['nachname']= str_replace('.', '-', $player['nachname']); 
  $player['nachname']= str_replace('Ä', '&Auml;', $player['nachname']); 
  $player['nachname']= str_replace('Ö', '&Ouml;', $player['nachname']); 
  $player['nachname']= str_replace('Ü', '&Uuml;', $player['nachname']); 
  $player['nachname']= str_replace('ä', '&auml;', $player['nachname']); 
  $player['nachname']= str_replace('ö', '&ouml;', $player['nachname']); 
  $player['nachname']= str_replace('ü', '&uuml;', $player['nachname']); 
  $player['nachname']= str_replace('ß', '&szlig;', $player['nachname']); 
  $player['nachname']= utf8_encode($player['nachname']); 

// ERSTEINMAL DIE EINFACHEN
  $sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_player '.
		' WHERE name LIKE "'.$player['nachname'].'" AND team != 999  AND kickerID = 0';
  $result = $wpdb->get_results($sql, ARRAY_A);
  if($result){
    foreach($result as $playername) {
    echo $player['nachname'].' - '.$playername['name'].'<br/>';

     $sql = 'UPDATE '.$CONFIG->prefix.'uli_player '.
              ' Set nachname = "'.$player['nachname'].'", '.
              ' vorname = "'.$player['vorname'].'", '. 
              ' kickerID = "'.$player['kickerID'].'", '. 
              ' birthday = "'.$player['birthday'].'" '.
              ' WHERE ID = '.$playername['ID'].' ';
    $wpdb->query($sql);
    }}

// VOR UND NACHNAME
$player['vorname'] = ltrim($player['vorname']);
$player['vorname'] = rtrim($player['vorname']);

 $sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_player '.
		' WHERE name LIKE "'.$player['vorname'].' '.$player['nachname'].'" AND team != 999  AND kickerID = 0';
  $result = $wpdb->get_results($sql, ARRAY_A);
  if($result){
    foreach($result as $playername) {
   echo $player['vorname'].' '.$player['nachname'].' - '.$playername['name'].'<br/>';

     $sql = 'UPDATE '.$CONFIG->prefix.'uli_player '.
              ' Set nachname = "'.$player['nachname'].'", '.
              ' vorname = "'.$player['vorname'].'", '. 
              ' kickerID = "'.$player['kickerID'].'", '. 
              ' birthday = "'.$player['birthday'].'" '.
              ' WHERE ID = '.$playername['ID'].' ';
    $wpdb->query($sql);
    }}


// JETZT KOMPLIZIERTER
  $sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_player '.
		' WHERE name LIKE "%'.$player['nachname'].'%" AND team != 999 AND kickerID = 0';

  $result = $wpdb->get_results($sql, ARRAY_A);
  if($result){
    foreach($result as $playername) {
    // LINK zum Übertragen

  $sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_player '.
		' WHERE kickerID = '.$player['kickerID'].'';
  $result = $wpdb->get_results($sql, ARRAY_A);

if (!$result){
    echo '<a href="?kickerID='.$player['kickerID'].'&playerID='.$playername['ID'].'">'.$player['vorname'].' '.$player['nachname'].' - '.$playername['name'].'</a><br/>';
        }
   

    }}
}}


$sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_calc ';
$result = $wpdb->get_results($sql, ARRAY_A);
if ($result){
  foreach($result as $player){
echo '<a name="'.$player['kickerID'].'"></a><h3>'.$player['vorname'].' '.$player['nachname'].'</h3>';

  $sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_player WHERE kickerID = '.$player['kickerID'].' '; 
  $result = $wpdb->get_results($sql, ARRAY_A);
  if ($result){
      foreach($result as $playername){
         echo '<p>'.$playername['vorname'].' '.$playername['nachname'].' - '.$playername['name'].'</p>';
         }
      }
  // keine Zuordnung
  // alle möglichen spieler zur auswahl
  else {
	$missing_players[] = $player['vorname'].' '.$player['nachname'];
       $sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_player WHERE kickerID = 0 AND team != 999 '; 
       $result = $wpdb->get_results($sql, ARRAY_A);
       if ($result){
       foreach($result as $playername){       
              echo '<a href="?kickerID='.$player['kickerID'].'&playerID='.$playername['ID'].'#'.$player['kickerID'].'">'.$player['vorname'].' '.$player['nachname'].' - '.$playername['name'].'</a><br/>';
          }}
 
      }


  }}

	echo '<h3><b>Fehlende Spieler in der Liga.parkdrei.de Datenbank</b></h3>';
	if ($missing_players){
		foreach($missing_players as $player){
			echo '<p><b>'.$player.'</b></p>';		
		}}


birthday_check();

unset($menus);
$menus = array('');
get_k2_contentstyles_sidebar();
get_footer(); 




function birthday_check() {
global $CONFIG, $wpdb;
  $sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_player where team != 999 ';
$result = $wpdb->get_results($sql, ARRAY_A);
if ($result){
foreach($result as $player){

$sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_calc where kickerID = '.$player['kickerID'].' ';
$player_new = $wpdb->get_row($sql);   



 if ($player_new->birthday != $player['birthday'] AND $player_new->birthday != 0){
          	echo '<h3>Geburtstag repariert: '.$player['name'].'</h3>';
	   	$sql = 'Update '.$CONFIG->prefix.'uli_player set birthday = "'.$player_new->birthday.'" where ID = '.$player['ID'].'';    
		$wpdb->query($sql);     
         }

 if ($player_new->verein != $player['verein'] AND $player_new->verein != 0){
          	echo '<h3>Verein repariert: '.$player['name'].'</h3>';
	   	$sql = 'Update '.$CONFIG->prefix.'uli_player set verein = "'.$player_new->verein.'" where ID = '.$player['ID'].'';    
		$wpdb->query($sql);     
         }

 if ($player_new->groesse != $player['groesse'] AND $player_new->groesse != 0){
          	echo '<h3>Groesse repariert: '.$player['name'].'</h3>';
	   	$sql = 'Update '.$CONFIG->prefix.'uli_player set groesse = "'.$player_new->groesse.'" where ID = '.$player['ID'].'';    
		$wpdb->query($sql);     
         }

 if ($player_new->gewicht != $player['gewicht'] AND $player_new->gewicht != 0){
          	echo '<h3>gewicht repariert: '.$player['name'].'</h3>';
	   	$sql = 'Update '.$CONFIG->prefix.'uli_player set gewicht = "'.$player_new->gewicht.'" where ID = '.$player['ID'].'';    
		$wpdb->query($sql);     
         }

 if ($player_new->nationalitaet != $player['nationalitaet'] AND $player_new->nationalitaet){
          	echo '<h3>nationalitaet repariert: '.$player['name'].'</h3>';
	   	$sql = 'Update '.$CONFIG->prefix.'uli_player set nationalitaet = "'.$player_new->nationalitaet.'" where ID = '.$player['ID'].'';    
		$wpdb->query($sql);     
         }

   }} 


}

?>