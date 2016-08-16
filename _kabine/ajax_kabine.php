<?php
// hier werden die aufstellungen geprueft und die aenderungen gespeichert.

require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');
include('lib_kabine.php');

global $option;


if (!$_POST['action']){
	$playerID = $_POST['playerID'];
	$position = $_POST['position'];
	$uliID = $option['uliID'];
	$year = $option['currentyear'];
	$changeonfield = FALSE;


	// Sonderfall playerID = position
	// Verschieben auf dem Feld
	if (preg_match("/position/", $playerID)){
		$oldposition = str_replace("position", "", $playerID);
		$movingplayer = get_player_on_a_position(0, $oldposition, $uliID, $year);
		//print_R($movingplayer);
		$playerID = $movingplayer['playerID'];
		$changeonfield = TRUE;


	}


	// Der bisherige Spieler auf der Position wird geholt
	$oldPlayer = get_player_on_a_position(0, $position, $uliID, $year);

	// Der Spieler wird da reingeschrieben
	save_player_ajax($uliID, $year, 0, $position, $playerID);

	// Beim verschieben auf dem Feld
	// In diesem Fall muss dann ausgetauscht werden ...
	if ($changeonfield){
		save_player_ajax($uliID, $year, 0, $oldposition, $oldPlayer['playerID']);
	}


	$player = get_player_infos($playerID);

	// der neue Spieler sieht anders aus
	$newHtml = '';
	$newHtml .= '<div class="player_field" id = "'.$player['playerID'].'">';

	$newHtml .= get_player_pic($player['playerID']);
	$newHtml .= '<span class="jerseynumber">'.$player['jerseynumber'];
	if ($player['injury']){
		$newHtml .= ' '.get_injury_pic($player['injury_cause'].' (letztes Update: '.uli_date($player['injury_update']).')').' ';
	}
	$newHtml .= '</span>';	
	$newHtml .= '<b>'.$player['name'].'</b> ';
	$newHtml .= $option['position'.$player['hp'].'-2'].' ';
	if ($player['np1']){$newHtml .= ''.$option['position'.$player['np1'].'-2'].' ';}
	if ($player['np2']){$newHtml .= ''.$option['position'.$player['np2'].'-2'].' ';}
	$newHtml .= ' ('.$option['foot'.$player['foot'].'-2'].')';

	$newHtml .= '</div>';


	// es wird die Klasse geaendert
	$formation = get_userformation(0, $uliID, $year);
	$posArray = get_formation_position($formation, $position);

	$faktor = get_position_faktor($posArray, $player);
	if ($faktor == "1"){$class = 'playerbig_100';}
	if ($faktor == "0.5"){$class = 'playerbig_50';}
	if ($faktor == "0.25"){$class = 'playerbig_25';}
	if ($position > 11){$class = 'player_bench';};


	$html .= $newHtml;
	$html .= '<script>';
	$html .= '$("#position'.$position.'").removeClass();';
	$html .= '$("#position'.$position.'").addClass("'.$class.'");';
	$html .= '$("#bench .playerID'.$playerID.'").hide();';
	// Eine neu besetzte Position muss draggable gemacht werden
	$html .= '$(function() {$( "#position'.$position.'").draggable({ helper: "clone" });});';


	// Der alte Spieler geht auf die Bank
	if ($oldPlayer AND !$changeonfield){
		$html .= '$("#'.$oldPlayer['playerID'].'").show();';
	}

	if ($changeonfield){
		// Hier die Ajax Action show/hide
		if ($oldPlayer['playerID'] == 0){
			// TODO die Beschreibung der Position holen
			$posArray = get_formation_position($formation, $oldposition);
			$nameposition = $option['position'.$posArray['position']];
			// Ein nicht mehr besetzte Position muss undraggable gemacht werden
			$html .= '$("#position'.$oldposition.'").draggable({ disabled: true });';
			$html .= '$("#position'.$oldposition.'").html("<br/><br/>'.$nameposition.'");';
			$html .= '$("#position'.$oldposition.'").removeClass();';
			$html .= '$("#position'.$oldposition.'").addClass("slot");';
		}
		else {
			// Alter Spieler geht da hin.
			$oldPlayer = get_player_infos($oldPlayer['playerID']);
			$newHtml = '';
			$newHtml .= '<div class="player_field" id = "'.$oldPlayer['playerID'].'">';
			$newHtml .= get_player_pic($oldPlayer['playerID']);
			$newHtml .= '<span class="jerseynumber">'.$oldPlayer['jerseynumber'].'</span>';
			$newHtml .= '<b>'.$oldPlayer['name'].'</b> ';
			$newHtml .= $option['position'.$oldPlayer['hp'].'-2'].' ';
			if ($oldPlayer['np1']){$newHtml .= ''.$option['position'.$oldPlayer['np1'].'-2'].' ';}
			if ($oldPlayer['np2']){$newHtml .= ''.$option['position'.$oldPlayer['np2'].'-2'].' ';}
			$newHtml .= ' ('.$option['foot'.$oldPlayer['foot'].'-2'].')';
			$newHtml .= '</div>';
			$newHtml = str_replace('"', '\"', $newHtml);
			$html .= '$("#position'.$oldposition.'").html("'.$newHtml.'");';
			$posArray = get_formation_position($formation, $oldposition);
			$faktor = get_position_faktor($posArray, $oldPlayer);
			if ($faktor == "1"){$class = 'playerbig_100';}
			if ($faktor == "0.5"){$class = 'playerbig_50';}
			if ($faktor == "0.25"){$class = 'playerbig_25';}
			if ($position > 11){$class = 'player_bench';};
			$html .= '$("#position'.$oldposition.'").removeClass();';
			$html .= '$("#position'.$oldposition.'").addClass("'.$class.'");';
		}
	}





	$html .= '</script>';
	echo $html;
}

if ($_POST['action'] == "changeformation"){
	$uliID = $option['uliID'];
	$formation = $_POST['formation'];
	save_formation($formation, $uliID, 0, $option['currentyear']);
	//read_styles_ajax($formation);
	//print_slots($formation);
	//echo fill_slots($formation);
}


if ($_POST['action'] == "changecaptain"){
	$uliID = $option['uliID'];
	$year = $option['currentyear'];
	$slotid = $_POST['slotid'];

	$position = str_replace("captain", "", $slotid);


	// Der bisherige Spieler auf der Position wird geholt
	$oldPlayer = get_player_on_a_position(0, $position, $uliID, $year);

	// Der Spieler wird da reingeschrieben
	if ($oldPlayer['playerID'] != 0){
		// neuen Captain schreiben
		save_player_ajax($uliID, $year, 0, 15, $oldPlayer['playerID']);
		// jQuery alten Kapitän Klasse aendern
		// jQuery neuer Kapitän Klasse aendern


		$html .= '<script>';
		$html .= '$(".player_captain").removeClass("player_captain").addClass("captain");';
		$html .= '$("#'.$slotid.'").removeClass();';
		$html .= '$("#'.$slotid.'").addClass("player_captain");';
		$html .= '</script>';

		echo $html;
	}






}


?>