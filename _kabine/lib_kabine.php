<?PHP
/*
 * Lib Kabine
 * hier sind die Main und die Ajax Funktionen drinne
 *
 * falls es spaeter wieder eine kabine aus kabul geben soll, muss das woanders hin
 *
 *
 * TODO die beiden neuen Systeme nachziehen
 *
 */

include("lang_kabine.php");


/**
 * loescht eine aufstellung
 * 30.07.2010
 */
function clear_formation($uliID, $year){
	$cond[] = array ("col" => "uliID", "value" => $uliID);
	$cond[] = array ("col" => "year", "value" => $year);
	$cond[] = array ("col" => "round", "value" => 0);
	$value[] = array("col" => "playerID", "value" => 0);
	uli_update_record('userteams', $cond, $value);
}

/**
 * holt alle Spieler des Kaders
 * 30.07.2010
 *
 */
function get_own_roster($uliID) {
	global $option;
	/* Baut den Join zusammen */
	$tableString  = 'player_league up ';
	$tableString .= ' LEFT JOIN '.$option['prefix'].'uli_player p ON up.playerID = p.ID ';
	/* Sortierung */
	$order[] = array("col" => "p.hp");
	$cond[] = array("col" => "up.uliID", "value" => $uliID);

	$result = uli_get_results($tableString, $cond, NULL, $order);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * holt die playerID auf einer bestimmten Position
 * ACHTUNG vereinfacht gegenŸber vorher
 * Das PlayerArray muss woanders geholt werden
 */
function get_player_on_a_position($round, $number, $uliID, $year) {
	$cond[] = array ("col" => "uliID", "value" => $uliID);
	$cond[] = array ("col" => "year", "value" => $year);
	$cond[] = array ("col" => "number", "value" => $number);
	$cond[] = array ("col" => "round", "value" => $round);
	$result = uli_get_row('userteams', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

// schaut nach ob ein spieler in der Runde gespielt hat
function did_he_play($round, $playerID, $year, $uliID) {
	$cond[] = array ("col" => "uliID", "value" => $uliID);
	$cond[] = array ("col" => "year", "value" => $year);
	$cond[] = array ("col" => "playerID", "value" => $playerID);
	$cond[] = array ("col" => "round", "value" => $round);
	$order[] = array("col" => "number", "sort" => "DESC");
	$number = uli_get_var('userteams', $cond, 'number');
	if ($number == 15){return 'C';}
	if ($number <= 11 AND $number >= 1) {return 'S';}
	if ($number >= 12 AND $number <= 14) {return 'B';}
	else {return 'X';}
}




/**
 * gibt das menŸ fŸr die formationen aus
 *
 */
function print_formation_menu($round, $userformation) {
	if (!$userformation){$userformation = 442;}

	$formations[] = array ("formation" => "442", "name" => "4-4-2");
	$formations[] = array ("formation" => "4411", "name" => "4-4-1-1");
	$formations[] = array ("formation" => "433", "name" => "4-3-3");
	$formations[] = array ("formation" => "4213", "name" => "4-3-3 (Z)");
	$formations[] = array ("formation" => "343", "name" => "3-4-3");
	$formations[] = array ("formation" => "532", "name" => "5-3-2");
	$formations[] = array ("formation" => "352", "name" => "3-5-2");
	$formations[] = array ("formation" => "451", "name" => "4-5-1");
	$formations[] = array ("formation" => "460", "name" => "4-5-1 (f9)");	
	$formations[] = array ("formation" => "4321", "name" => "4-3-3 (f9)");
	
	$content .= '<select id="formation">';
	foreach ($formations as $formation){
		$selected = "";
		if ($userformation == $formation['formation']){$selected = 'selected = "selected"';}
		$content .= '<option value="'.$formation['formation'].'" '.$selected.'>'.$formation['name'].'</option>';
	}
	$content .= '</select>';


	$content .= '<p><a href="?action=clear">'.ClearFormation.'</a></p>';
	//$content .= '<p><a href="?action=cotrainer">'.CoTrainer.'</a></p>';
	$html .= uli_box(Formation, $content, NULL, 'kabine_formation');
	return $html;
}

/**
 *
 * Das ist eigentlich ein Funktion, die irgendwo in die MainLibs koennte (oder vielleicht da schon ist)
 * Hier wird sie gebraucht um den Text von Kapitaen zu C zu aendern, oder?
 * Wird sie hier ueberhaupt gebraucht?
 *
 */
//function change_text_ajax($div, $newtext){
//	$objResponse = new xajaxResponse();
//	$objResponse->assign($div,"innerHTML", $newtext);
//	return $objResponse;
//}


/**
 * speichert den Kapitaen
 *
 * @param unknown_type $uliID
 * @param unknown_type $year
 * @param unknown_type $round
 * @param unknown_type $position
 * @return xajaxResponse
 */
function save_captain_ajax($uliID, $year, $round, $position){
	$number = str_replace("captain", "", $position);
	$player = get_player_on_a_position(0, $number, $uliID, $year);
	$cond[] = array ("col" => "uliID", "value" => $uliID);
	$cond[] = array ("col" => "year", "value" => $year);
	$cond[] = array ("col" => "round", "value" => $round);
	$cond[] = array ("col" => "number", "value" => 15);
	$value[] = array("col" => "playerID", "value" => $player['playerID']);
	uli_update_record('userteams', $cond, $value);
	$objResponse = new xajaxResponse();
	$objResponse->assign("captain","innerHTML", "(C)");
	return $objResponse;
}


/**
 * Speichert einen Spieler
 *
 * @param unknown_type $uliID
 * @param unknown_type $year
 * @param unknown_type $round
 * @param unknown_type $position
 * @param unknown_type $playerID
 * @return unknown_type
 */
function save_player_ajax($uliID, $year, $round, $position, $playerID){
	$cond[] = array ("col" => "uliID", "value" => $uliID);
	$cond[] = array ("col" => "year", "value" => $year);
	$cond[] = array ("col" => "round", "value" => $round);
	$cond[] = array ("col" => "number", "value" => $position);
	$value[] = array("col" => "playerID", "value" => $playerID);
	uli_update_record('userteams', $cond, $value);
}






/**
 * checkt beim verschieben der Spieler
 * ob sich was aendert und fuehrt das aendern der css klassen durch
 * @param unknown_type $formation
 * @param unknown_type $number_player
 * @param unknown_type $playerID
 * @param unknown_type $cssclass
 * @return unknown_type
 */
//function check_position_ajax($formation, $number_player, $playerID, $cssclass){
//
//	$number = str_replace("position", "", $number_player);
//	$player = str_replace("p", "", $playerID);
//	if($number < 12){
//		$playerinfo = get_player_infos($player, array('player'));
//		$position = get_formation_position($formation, $number);
//		$faktor = get_position_faktor($position, $playerinfo);
//
//		//$style = 'backgroundcolor: blue';
//		if ($faktor == 1){$newcssclass = "playerbig_100";}
//		if ($faktor == 0.75){$newcssclass = "playerbig_75";}
//		if ($faktor == 0.5){$newcssclass = "playerbig_50";}
//		if ($faktor == 0.25){$newcssclass = "playerbig_25";}
//
//		// Hier jetzt die Ajax-Action
//		$objResponse = new xajaxResponse();
//		//	YAHOO.util.Dom.removeClass(oDD.player, "player");
//		//  YAHOO.util.Dom.addClass(oDD.player, "playerbig");
//		$objResponse->call('YAHOO.util.Dom.removeClass', $playerID, $cssclass);
//		$objResponse->call('YAHOO.util.Dom.addClass', $playerID, $newcssclass);
//
//		//	$objResponse->call('alert', $newcssclass);
//		//	$objResponse->call('YAHOO.util.Dom.setStyle', $playerID, 'opacity', $opacity);
//	}
//	return $objResponse;
//}


/**
 * keine ahnung
 * hier gibt es zwei styles funktionen, eine sollte noch weg
 * @param unknown_type $userformation
 * @return unknown_type
 */
function read_styles_ajax($userformation) {
	?>
<style type="text/css">
<?
if    ($userformation    == "4411"){ ?> #position11 {
	left: 80px;
	top: 10px;
}

#position9 {
	left: 270px;
	top: 40px;
}

#position10 {
	left: 165px;
	top: 100px;
}

#position8 {
	left: 10px;
	top: 150px;
}

#position7 {
	left: 320px;
	top: 150px;
}

#position6 {
	left: 165px;
	top: 200px;
}

#position5 {
	left: 100px;
	top: 340px;
}

#position4 {
	left: 230px;
	top: 340px;
}

#position3 {
	left: 10px;
	top: 280px;
}

#position2 {
	left: 320px;
	top: 280px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 180px;
	top: 40px;
}

#captain9 {
	left: 370px;
	top: 70px;
}

#captain10 {
	left: 265px;
	top: 130px;
}

#captain8 {
	left: 110px;
	top: 180px;
}

#captain7 {
	left: 420px;
	top: 180px;
}

#captain6 {
	left: 265px;
	top: 230px;
}

#captain5 {
	left: 200px;
	top: 370px;
}

#captain4 {
	left: 330px;
	top: 370px;
}

#captain3 {
	left: 110px;
	top: 310px;
}

#captain2 {
	left: 420px;
	top: 310px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "442"){ ?> #position11 {
	left: 80px;
	top: 10px;
}

#position9 {
	left: 270px;
	top: 10px;
}

#position10 {
	left: 165px;
	top: 100px;
}

#position8 {
	left: 10px;
	top: 150px;
}

#position7 {
	left: 320px;
	top: 150px;
}

#position6 {
	left: 165px;
	top: 200px;
}

#position5 {
	left: 100px;
	top: 340px;
}

#position4 {
	left: 230px;
	top: 340px;
}

#position3 {
	left: 10px;
	top: 280px;
}

#position2 {
	left: 320px;
	top: 280px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 180px;
	top: 40px;
}

#captain9 {
	left: 370px;
	top: 40px;
}

#captain10 {
	left: 265px;
	top: 130px;
}

#captain8 {
	left: 110px;
	top: 180px;
}

#captain7 {
	left: 420px;
	top: 180px;
}

#captain6 {
	left: 265px;
	top: 230px;
}

#captain5 {
	left: 200px;
	top: 370px;
}

#captain4 {
	left: 330px;
	top: 370px;
}

#captain3 {
	left: 110px;
	top: 310px;
}

#captain2 {
	left: 420px;
	top: 310px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "433"){ ?> #position11 {
	left: 165px;
	top: 10px;
}

#position9 {
	left: 320px;
	top: 80px;
}

#position10 {
	left: 10px;
	top: 80px;
}

#position8 {
	left: 10px;
	top: 200px;
}

#position7 {
	left: 320px;
	top: 200px;
}

#position6 {
	left: 165px;
	top: 160px;
}

#position5 {
	left: 100px;
	top: 340px;
}

#position4 {
	left: 230px;
	top: 340px;
}

#position3 {
	left: 10px;
	top: 280px;
}

#position2 {
	left: 320px;
	top: 280px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 265px;
	top: 40px;
}

#captain9 {
	left: 420px;
	top: 110px;
}

#captain10 {
	left: 110px;
	top: 110px;
}

#captain8 {
	left: 110px;
	top: 230px;
}

#captain7 {
	left: 420px;
	top: 230px;
}

#captain6 {
	left: 265px;
	top: 190px;
}

#captain5 {
	left: 200px;
	top: 370px;
}

#captain4 {
	left: 330px;
	top: 370px;
}

#captain3 {
	left: 110px;
	top: 310px;
}

#captain2 {
	left: 420px;
	top: 310px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "4213"){ ?> #position11 {
	left: 165px;
	top: 10px;
}

#position9 {
	left: 320px;
	top: 80px;
}

#position10 {
	left: 10px;
	top: 80px;
}

#position8 {
	left: 35px;
	top: 200px;
}

#position7 {
	left: 295px;
	top: 200px;
}

#position6 {
	left: 165px;
	top: 160px;
}

#position5 {
	left: 100px;
	top: 340px;
}

#position4 {
	left: 230px;
	top: 340px;
}

#position3 {
	left: 10px;
	top: 280px;
}

#position2 {
	left: 320px;
	top: 280px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 265px;
	top: 40px;
}

#captain9 {
	left: 420px;
	top: 110px;
}

#captain10 {
	left: 110px;
	top: 110px;
}

#captain8 {
	left: 135px;
	top: 230px;
}

#captain7 {
	left: 395px;
	top: 230px;
}

#captain6 {
	left: 265px;
	top: 190px;
}

#captain5 {
	left: 200px;
	top: 370px;
}

#captain4 {
	left: 330px;
	top: 370px;
}

#captain3 {
	left: 110px;
	top: 310px;
}

#captain2 {
	left: 420px;
	top: 310px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "4321"){ ?> #position11 {
	left: 165px;
	top: 70px;
}

#position9 {
	left: 320px;
	top: 80px;
}

#position10 {
	left: 10px;
	top: 80px;
}

#position8 {
	left: 35px;
	top: 200px;
}

#position7 {
	left: 295px;
	top: 200px;
}

#position6 {
	left: 165px;
	top: 160px;
}

#position5 {
	left: 100px;
	top: 340px;
}

#position4 {
	left: 230px;
	top: 340px;
}

#position3 {
	left: 10px;
	top: 280px;
}

#position2 {
	left: 320px;
	top: 280px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 265px;
	top: 100px;
}

#captain9 {
	left: 420px;
	top: 110px;
}

#captain10 {
	left: 110px;
	top: 110px;
}

#captain8 {
	left: 135px;
	top: 230px;
}

#captain7 {
	left: 395px;
	top: 230px;
}

#captain6 {
	left: 265px;
	top: 190px;
}

#captain5 {
	left: 200px;
	top: 370px;
}

#captain4 {
	left: 330px;
	top: 370px;
}

#captain3 {
	left: 110px;
	top: 310px;
}

#captain2 {
	left: 420px;
	top: 310px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "451"){ ?> #position11 {
	left: 165px;
	top: 10px;
}

#position10 {
	left: 165px;
	top: 100px;
}

#position8 {
	left: 10px;
	top: 150px;
}

#position7 {
	left: 320px;
	top: 150px;
}

#position6 {
	left: 100px;
	top: 210px;
}

#position9 {
	left: 230px;
	top: 210px;
}

#position5 {
	left: 100px;
	top: 340px;
}

#position4 {
	left: 230px;
	top: 340px;
}

#position3 {
	left: 10px;
	top: 280px;
}

#position2 {
	left: 320px;
	top: 280px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 265px;
	top: 40px;
}

#captain10 {
	left: 265px;
	top: 130px;
}

#captain8 {
	left: 110px;
	top: 180px;
}

#captain7 {
	left: 420px;
	top: 180px;
}

#captain6 {
	left: 200px;
	top: 240px;
}

#captain9 {
	left: 330px;
	top: 240px;
}

#captain5 {
	left: 200px;
	top: 370px;
}

#captain4 {
	left: 330px;
	top: 370px;
}

#captain3 {
	left: 110px;
	top: 310px;
}

#captain2 {
	left: 420px;
	top: 310px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "460"){ ?> #position11 {
	left: 165px;
	top: 30px;
}

#position10 {
	left: 165px;
	top: 100px;
}

#position8 {
	left: 10px;
	top: 150px;
}

#position7 {
	left: 320px;
	top: 150px;
}

#position6 {
	left: 100px;
	top: 210px;
}

#position9 {
	left: 230px;
	top: 210px;
}

#position5 {
	left: 100px;
	top: 340px;
}

#position4 {
	left: 230px;
	top: 340px;
}

#position3 {
	left: 10px;
	top: 280px;
}

#position2 {
	left: 320px;
	top: 280px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 265px;
	top: 60px;
}

#captain10 {
	left: 265px;
	top: 130px;
}

#captain8 {
	left: 110px;
	top: 180px;
}

#captain7 {
	left: 420px;
	top: 180px;
}

#captain6 {
	left: 200px;
	top: 240px;
}

#captain9 {
	left: 330px;
	top: 240px;
}

#captain5 {
	left: 200px;
	top: 370px;
}

#captain4 {
	left: 330px;
	top: 370px;
}

#captain3 {
	left: 110px;
	top: 310px;
}

#captain2 {
	left: 420px;
	top: 310px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "352"){ ?> #position11 {
	left: 80px;
	top: 10px;
}

#position9 {
	left: 270px;
	top: 10px;
}

#position10 {
	left: 165px;
	top: 100px;
}

#position8 {
	left: 10px;
	top: 180px;
}

#position7 {
	left: 320px;
	top: 180px;
}

#position6 {
	left: 100px;
	top: 250px;
}

#position5 {
	left: 230px;
	top: 250px;
}

#position4 {
	left: 30px;
	top: 340px;
}

#position3 {
	left: 165px;
	top: 340px;
}

#position2 {
	left: 300px;
	top: 340px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 180px;
	top: 40px;
}

#captain9 {
	left: 370px;
	top: 40px;
}

#captain10 {
	left: 265px;
	top: 130px;
}

#captain8 {
	left: 110px;
	top: 210px;
}

#captain7 {
	left: 420px;
	top: 210px;
}

#captain6 {
	left: 200px;
	top: 280px;
}

#captain5 {
	left: 330px;
	top: 280px;
}

#captain4 {
	left: 130px;
	top: 370px;
}

#captain3 {
	left: 265px;
	top: 370px;
}

#captain2 {
	left: 400px;
	top: 370px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "532"){ ?> #position11 {
	left: 80px;
	top: 10px;
}

#position9 {
	left: 270px;
	top: 10px;
}

#position10 {
	left: 165px;
	top: 170px;
}

#position8 {
	left: 10px;
	top: 120px;
}

#position7 {
	left: 320px;
	top: 120px;
}

#position3 {
	left: 10px;
	top: 270px;
}

#position2 {
	left: 320px;
	top: 270px;
}

#position6 {
	left: 40px;
	top: 340px;
}

#position5 {
	left: 165px;
	top: 340px;
}

#position4 {
	left: 290px;
	top: 340px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 180px;
	top: 40px;
}

#captain9 {
	left: 370px;
	top: 40px;
}

#captain10 {
	left: 265px;
	top: 200px;
}

#captain8 {
	left: 110px;
	top: 150px;
}

#captain7 {
	left: 420px;
	top: 150px;
}

#captain3 {
	left: 110px;
	top: 300px;
}

#captain2 {
	left: 420px;
	top: 300px;
}

#captain6 {
	left: 140px;
	top: 370px;
}

#captain5 {
	left: 265px;
	top: 370px;
}

#captain4 {
	left: 390px;
	top: 370px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
<?
if    ($userformation    == "343"){ ?> #position11 {
	left: 165px;
	top: 10px;
}

#position9 {
	left: 320px;
	top: 80px;
}

#position10 {
	left: 10px;
	top: 80px;
}

#position6 {
	left: 165px;
	top: 150px;
}

#position8 {
	left: 10px;
	top: 200px;
}

#position7 {
	left: 320px;
	top: 200px;
}

#position5 {
	left: 165px;
	top: 250px;
}

#position4 {
	left: 30px;
	top: 340px;
}

#position3 {
	left: 165px;
	top: 340px;
}

#position2 {
	left: 300px;
	top: 340px;
}

#position1 {
	left: 165px;
	top: 410px;
}

#captain11 {
	left: 265px;
	top: 40px;
}

#captain9 {
	left: 420px;
	top: 110px;
}

#captain10 {
	left: 110px;
	top: 110px;
}

#captain6 {
	left: 265px;
	top: 180px;
}

#captain8 {
	left: 110px;
	top: 230px;
}

#captain7 {
	left: 420px;
	top: 230px;
}

#captain5 {
	left: 265px;
	top: 280px;
}

#captain4 {
	left: 130px;
	top: 370px;
}

#captain3 {
	left: 265px;
	top: 370px;
}

#captain2 {
	left: 400px;
	top: 370px;
}

#captain1 {
	left: 265px;
	top: 440px;
}

<?
}
?>
#position12 {
	left: 0px;
	top: 475px;
	height: 15px;
	width: 145px;
}

#position13 {
	left: 150px;
	top: 475px;
	height: 15px;
	width: 145px;
}

#position14 {
	left: 300px;
	top: 475px;
	height: 15px;
	width: 145px;
}

#position15 {
	left: 165px;
	top: 800px;
}
</style>
<?
}


/**
 * in abhaengigkeit der userformation
 * werden die slots fuer das spielfeld ausgegeben
 *
 * TODO
 * umbauen auf return $html
 *
 * @param unknown_type $userformation
 * @return unknown_type
 */
function print_slots($userformation) {

if ($userformation == "4411"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position10"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>



	<?	}
	if ($userformation == "4321"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position8"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position9"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position10"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position11"><br />
<br />
H&auml;ngende Spitze</div>
	<?	}
	if ($userformation == "4213"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position8"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position9"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position10"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>
	<?	}
	if ($userformation == "442"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
Sturm</div>
<div class="slot" id="position10"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>
	<?	} ?>
	<?
	if ($userformation == "433"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position10"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>
	<?	} ?>
	<?
	if ($userformation == "343"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position10"><br />
<br />
H&auml;ngende Spitze</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>
	<?	} ?>
	<?
	if ($userformation == "451"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position10"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>
	<?	} ?>
	<?
	if ($userformation == "460"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position10"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position11"><br />
<br />
H&auml;ngende Spitze</div>
	<?	} ?>	
	<?
	if ($userformation == "352"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position6"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
Sturm</div>
<div class="slot" id="position10"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>
	<?	} ?>
	<?
	if ($userformation == "532"){ ?>
<div class="slot" id="position1"><br />
<br />
Tormann</div>
<div class="slot" id="position2"><br />
<br />
Rechter Au&szlig;enverteidiger</div>
<div class="slot" id="position3"><br />
<br />
Linker Au&szlig;enverteidiger</div>
<div class="slot" id="position4"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position5"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position6"><br />
<br />
Innenverteidiger</div>
<div class="slot" id="position7"><br />
<br />
Rechter Fl&uuml;gel</div>
<div class="slot" id="position8"><br />
<br />
Linker Fl&uuml;gel</div>
<div class="slot" id="position9"><br />
<br />
Sturm</div>
<div class="slot" id="position10"><br />
<br />
Zentrales Mittelfeld</div>
<div class="slot" id="position11"><br />
<br />
Sturm</div>
	<?	} ?>

<a href="#">
<div class="captain" id="captain1">C</div>
</a>
<a href="#">
<div class="captain" id="captain2">C</div>
</a>
<a href="#">
<div class="captain" id="captain3">C</div>
</a>
<a href="#">
<div class="captain" id="captain4">C</div>
</a>
<a href="#">
<div class="captain" id="captain5">C</div>
</a>
<a href="#">
<div class="captain" id="captain6">C</div>
</a>
<a href="#">
<div class="captain" id="captain7">C</div>
</a>
<a href="#">
<div class="captain" id="captain8">C</div>
</a>
<a href="#">
<div class="captain" id="captain9">C</div>
</a>
<a href="#">
<div class="captain" id="captain10">C</div>
</a>
<a href="#">
<div class="captain" id="captain11">C</div>
</a>
<div class="slot" id="position12">Bank</div>
<div class="slot" id="position13">Bank</div>
<div class="slot" id="position14">Bank</div>
	<?

}

/**
 * belegt die slots mit jquery mit den spielern aus der db
 */
function fill_slots($formation, $uliID = '', $round = '', $year = ''){
	global $option;

	if (!$uliID){
		$uliID = $option['uliID'];
	}
	if (!$year){
		$year = $option['currentyear'];
	}
	if (!$round){
		$round = 0;
	}

	$team = get_userteam($uliID, $year, $round);
	if ($team){
		$html .= '<script>';
		foreach ($team as $playerID){
			if ($playerID['playerID'] > 0 AND $playerID['number'] != 15){
				$player = get_player_infos($playerID['playerID']);

				$newHtml = '';

				$newHtml .= '<div class="player_field" id = "'.$player['playerID'].'">';

				$newHtml .= get_player_pic($player['playerID']);
				$newHtml .= '<span class="jerseynumber">'.$player['jerseynumber'];
				if ($player['injury']){
					$newHtml .= ' '.get_injury_pic($player['injury_cause'].' (letztes Update: '.uli_date($player['injury_update']).')').' ';
				}
				$newHtml .= '</span>';
				$newHtml .= '<b>'.$player['name'].'</b> ';
				$newHtml .= '<span class="position">'.$option['position'.$player['hp'].'-2'].' ';
				if ($player['np1']){$newHtml .= ''.$option['position'.$player['np1'].'-2'].' ';}
				if ($player['np2']){$newHtml .= ''.$option['position'.$player['np2'].'-2'].' ';}
				$newHtml .= ' ('.$option['foot'.$player['foot'].'-2'].')</span>';

				$newHtml .= '</div>';

				$posArray = get_formation_position($formation, $playerID['number']);
				$faktor = get_position_faktor($posArray, $player);
				if ($faktor == "1"){$class = 'playerbig_100';}
				if ($faktor == "0.5"){$class = 'playerbig_50';}
				if ($faktor == "0.25"){$class = 'playerbig_25';}
				if ($playerID['number'] > 11){$class = 'player_bench';}

				$html .= '$("#position'.$playerID['number'].'").html(\''.$newHtml.'\');';
				$html .= '$("#position'.$playerID['number'].'").removeClass();';
				$html .= '$("#position'.$playerID['number'].'").addClass("'.$class.'");';
				$html .= '$(function() {$( "#position'.$playerID['number'].'").draggable({ helper: "clone" });});';
					
				$checkforcaptain[$playerID['playerID']] = $playerID['number'];
					
			}
			if ($playerID['playerID'] > 0 AND $playerID['number'] == 15){



				$position = $checkforcaptain[$playerID['playerID']];
				$html .= '$("#captain'.$position.'").removeClass("captain");';
				$html .= '$("#captain'.$position.'").addClass("player_captain");';
			}
		}
		$html .= '</script>';
	}
	return $html;
}



/**
 * holt die Formation des Nutzers
 * @param unknown_type $round
 * @param unknown_type $uliID
 * @param unknown_type $year
 * @return unknown_type
 */
function get_userformation($round, $uliID='', $year='') {
	global $option;
	if (!$uliID){$uliID = $option['uliID'];}
	if (!$year){$year = $option['currentyear'];}
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "round", "value" => $round);
	$cond[] = array("col" => "year", "value" => $year);
	$result = uli_get_var('userformation', $cond, 'formation');
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * holt die Aufstellung des Nutzers
 * @param unknown_type $round
 * @param unknown_type $uliID
 * @param unknown_type $year
 * @return unknown_type
 */
function get_userteam($uliID, $year, $round) {
	global $option;
	if (!$uliID){$uliID = $option['uliID'];}
	if (!$year){$year = $option['currentyear'];}
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "round", "value" => $round);
	$cond[] = array("col" => "year", "value" => $year);
	$order[] = array("col" => "number", "sort" => "ASC");
	$result = uli_get_results('userteams', $cond, NULL, $order);
	if ($result){return $result;}
	else {return FALSE;}
}




/**
 * ueberprueft ob eine 0er aufstellung fuer das jahr in der db gibt
 * wenn nicht, wird eine geschrieben
 */
function check_userteam_basic(){
	global $option;
	$uliID = $option['uliID'];
	$year = $option['currentyear'];
	$check = get_player_on_a_position(0, 1, $uliID, $year);
	if (!$check){
		for ($x=1; $x<=15; $x++){
			$value = array();
			$value[] = array("col" => "uliID", "value" => $uliID);
			$value[] = array("col" => "year", "value" => $year);
			$value[] = array("col" => "round", "value" => 0);
			$value[] = array("col" => "number", "value" => $x);
			$ID = uli_insert_record('userteams', $value);
		}
	}
}

/**
 * speichert eine formation
 * 05.08.10
 *
 * TODO eventuell in die mainlib weil das auch beim schreiben der aufschreibung pro spieltag gebraucht wirt
 */
function save_formation($formation, $uliID, $round, $year){
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "round", "value" => $round);
	$cond[] = array("col" => "year", "value" => $year);
	$ID = uli_get_var('userformation', $cond, 'ID');
	if ($ID){
		$value[] = array("col" => "formation", "value" => $formation);
		uli_update_record('userformation', $cond, $value);
	}
	else {
		$value = $cond;
		$value[] = array("col" => "formation", "value" => $formation);
		$ID = uli_insert_record('userformation', $value);
	}
}

/**
 * gibt die spieler aus, damit sie aufs feld gezogen werden koennen
 * Hier werden die "players" im YUI sinne mit den Spielern des Kaders bestückt.
 * Check, ob sie schon in der Aufstellung vorhanden sind. Wenn ja, dann invisible
 * 21.09.2010
 */
function print_kader_kabine($uliID){
	global $option;
	$players = get_own_roster($uliID);
	if ($players) {
		$count = 0;
		$count2 = 0;
		$top = 0;
		$left = 0;
		$positionname[1] = $option['position1-2'];
		$positionname[2] = $option['position2-2'];
		$positionname[3] = $option['position3-2'];
		$positionname[4] = $option['position4-2'];
		$positionname[5] = $option['position5-2'];
		$positionname[6] = $option['position6-2'];
		$positionname[7] = $option['position7-2'];

		$footname[1] = $option['foot1-2'];
		$footname[2] = $option['foot2-2'];
		$footname[3] = $option['foot3-2'];

		foreach ($players as $player) {
			$count = $count +1;
			// check, ob der fuzzi schon auf dem platz ist.
			$display = 'style = "display: none;"';
			if (did_he_play(0, $player['playerID'], $option['currentyear'], $uliID) == "X"){
				$display = '';
			}
			$cond[] = array("col" => "playerID", "value" => $player['playerID']);
			$injured = uli_get_row("player_injured", $cond);
			if ($injured){
				$player['injury'] = TRUE;
				$player['injury_cause'] = $injured['cause'];
				$player['injury_update'] = $injured['timestamp'];
			}

			$picture = get_player_pic($player['playerID']);
			$html .= '<div class="player_kabine playerID'.$player['playerID'].'" '.$display.' id="'.$player['playerID'].'">';
			$html .= "\n";
			$html .= '<span class="jerseynumber" style="background:none;">'.$player['jerseynumber'].'</span>';

			$html .= '<b>'.$player['name'].'</b> '.$positionname[$player['hp']].' '.$positionname[$player['np1']].' '.$positionname[$player['np2']].' ('.$footname[$player['foot']].')';
			if ($player['injury']){
				$html .= ' '.get_injury_pic($player['injury_cause'].' (letztes Update: '.uli_date($player['injury_update']).')').' ';
			}
			// TODO INFO
			//$html .= '<div class="kabine_info">i</div>';

			$html .= '</div> ';
			$html .= "\n";
		}}

		// Draggable
		if ($players){
			$html .= '<script>';
			$html .= "\n";
			foreach ($players as $player){
				$html .= '$(function() {$( "#bench #'.$player['playerID'].'" ).draggable({ helper: "clone" });});';
				$html .= "\n";
			}


			// Droppables
			$html .= '$( "#position1" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=1&playerID=" + playerID,complete: function(data){$("#position1").html(data.responseText);}});}});';
			$html .= '$( "#position2" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=2&playerID=" + playerID,complete: function(data){$("#position2").html(data.responseText);}});}});';
			$html .= '$( "#position3" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=3&playerID=" + playerID,complete: function(data){$("#position3").html(data.responseText);}});}});';
			$html .= '$( "#position4" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=4&playerID=" + playerID,complete: function(data){$("#position4").html(data.responseText);}});}});';
			$html .= '$( "#position5" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=5&playerID=" + playerID,complete: function(data){$("#position5").html(data.responseText);}});}});';
			$html .= '$( "#position6" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=6&playerID=" + playerID,complete: function(data){$("#position6").html(data.responseText);}});}});';
			$html .= '$( "#position7" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=7&playerID=" + playerID,complete: function(data){$("#position7").html(data.responseText);}});}});';
			$html .= '$( "#position8" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=8&playerID=" + playerID,complete: function(data){$("#position8").html(data.responseText);}});}});';
			$html .= '$( "#position9" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=9&playerID=" + playerID,complete: function(data){$("#position9").html(data.responseText);}});}});';
			$html .= '$( "#position10" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=10&playerID=" + playerID,complete: function(data){$("#position10").html(data.responseText);}});}});';
			$html .= '$( "#position11" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=11&playerID=" + playerID,complete: function(data){$("#position11").html(data.responseText);}});}});';

			$html .= '$( "#position12" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=12&playerID=" + playerID,complete: function(data){$("#position12").html(data.responseText);}});}});';
			$html .= '$( "#position13" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=13&playerID=" + playerID,complete: function(data){$("#position13").html(data.responseText);}});}});';
			$html .= '$( "#position14" ).droppable({drop: function( event, ui ) {var playerID = $(ui.draggable).attr("id");
					$.ajax({type: "POST", url: "ajax_kabine.php", data: "position=14&playerID=" + playerID,complete: function(data){$("#position14").html(data.responseText);}});}});';


			$html .= '</script>';
			$html .= "\n";
		}
		return $html;
}




/**
 * gibt eine kleine infobox aus, die dynmaisch nachgeladen werden kann
 */
function print_kabine_info_box(){
	$html .= uli_box(Info, InfoText, NULL, 'kabineinfo');
	return $html;
}


function print_kabine_info_player($playerID){
	global $option;
	$leagueID = $option['leagueID'];
	$objResponse = new xajaxResponse();
	$playerinfo = print_player_info($playerID, $leagueID);
	$html .= uli_box($playerinfo['headline'], $playerinfo['content'], NULL, 'kabineinfo');
	$objResponse->assign('container',"innerHTML", $html);
	return $objResponse;
}

?>