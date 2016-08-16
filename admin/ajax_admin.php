<?php

// fuer die ganzen ajax files muss irgendeine routine gebaut werden, die die ganzen libs einliest.
// diese variante ist etwas ruppig, weil bei jedem kleinen request die ganze routine (checks, etc.) durchlaufen wird.

require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');
require_once(ABSPATH.'/uli/admin/lib_admin.php');

global $option;



// ACHTUNG HIER SIND EINIGE ADMIN FUNKTIONEN DRINNE, DIE NUR FUER EINE LIGA GELTEN

if ($_POST['action'] == "set_tm_id"){
	$playerID = $_POST['playerID'];
	$tm_id = $_POST['tm_id'];
	$cond[] = array("col" => "ID", "value" => $playerID);
	$value[] = array("col" => "tm_id", "value" => $tm_id);
	uli_update_record("player", $cond, $value);
	
}

// Neuer Spieler wird eingegeben
if ($_POST['action'] == "insertnewplayer"){
	$playername = $_POST['playername'];
	$team = $_POST['team'];
	$team = str_replace("team", "", $team);
	$value[] = array("col" => "name", "value" => $playername);
	$value[] = array("col" => "team", "value" => $team);
	$playerID = uli_insert_record('player', $value);
	$leagues = get_leagues();
	if ($leagues){
		foreach ($leagues as $league){
			// Datensaetze in der player_league_tabelle anlegen
			unset ($value);
			//$value[] = array("col" => "smile", "value" => 50);
			$value[] = array("col" => "playerID", "value" => $playerID);
			$value[] = array("col" => "leagueID", "value" => $league['ID']);
			$ID = uli_insert_record('player_league', $value);
			update_smile($playerID, $league['ID'], NULL, 50, NULL, $option['currentyear']);
			
			
			// Auktionen starten
			$auction = array();
			$auction['playerID']  = $playerID;
			$auction['leagueID']  = $league['ID'];
			$auction['start']  	  = mktime();
			start_auction($auction);
		}
	}
	echo $playername.' eingegeben.';
}



if($_POST['newplayername']){
	$newplayername = $_POST['newplayername'];
	$result = check_new_player($newplayername);
	$ligateams = get_all_team_names();
	if ($result){
		//echo '<h3>Kommt mir bekannt vor: </h3>';
		foreach ($result as $player){
			echo '<p>';
			if ($player['team'] != 999){
				echo '<b>'.$player['name'].'</b>';
				echo ' ist schon bei '.$ligateams[$player['team']].'';
			}
			else {
				echo '';
				echo '<input type="submit" class="playertoreactivate" id="'.$player['ID'].'" value="'.$player['name'].' (Archiv) BITTE REAKTIVIEREN">';
			}
			echo '</p>';
		}
	}
}




// Status neu berechnen
if ($_POST['action'] == "calculatestatus"){
	$playerID = str_replace("status", "", $_POST['playerID']);
	calculate_player_status($playerID, 1);
	echo '<script>';
	echo '$("#status'.$playerID.'").html("OK");';
	echo '</script>';
}

// MW neu berechnen
if ($_POST['action'] == "calculatemarktwert"){
	$playerID = str_replace("mw", "", $_POST['playerID']);
	$marktwert = get_marktwert(NULL, $playerID, 1);
	$cond = array();
	$value = array();
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "leagueID", "value" => 1);
	$value[] = array("col" => "marktwert", "value" => $marktwert);
	uli_update_record('player_league', $cond, $value);
	echo '<script>';
	echo '$("#mw'.$playerID.'").html("OK");';
	echo '</script>';
}


// POsition aendern
if ($_POST['action'] == "changeplayer"){
	$positionplayer = $_POST['positionplayer'];
	$newposition = $_POST['newposition'];
	$newArray = explode("-", $positionplayer);
	$playerID = $newArray[1];
	$position = $newArray[0];
	
	//print_r($newposition);
	
	$cond = array();
	$value = array();
	$cond[] = array("col" => "ID", "value" => $playerID);
	$value[] = array("col" => $position, "value" => $newposition);
	$result = uli_update_record('player', $cond, $value);
}


// Sterne aendern
if ($_POST['action'] == "changestars"){
	$playerID = $_POST['playerID'];
	$star = $_POST['star'];
	$cond = array();
	$value = array();
	$cond[] = array("col" => "ID", "value" => $playerID);
	$value[] = array("col" => "star", "value" => $star);
	$result = uli_update_record('player', $cond, $value);
}

//Admintrade
if ($_POST['action'] == "admintrade"){
	$playerID = $_POST['playerID'];
	$admintrade['sum'] = $_POST['sum'];
	$admintrade['externnew'] = $_POST['externnew'];
	$admintrade['ligateamnew'] = str_replace("team", "", $_POST['ligateamnew']);
	$admintrade['externold'] = $_POST['externnew'];
	if ($admintrade['externnew'] == "externer Transfer" OR $admintrade['externnew'] == "undefined"){
		$admintrade['externnew'] = '';
	}
	if ($admintrade['sum'] == "Abl&ouml;se"){
		$admintrade['sum'] = 0;
	}
	trade_player($playerID, '', NULL, NULL, $admintrade);
	echo '<script>';
	echo '$("#editplayer-'.$playerID.' .auctionend").html("OK");';
	echo '</script>';
}


if ($_POST['action'] == "deleteplayerleague"){
	$ID = str_replace("playerleague", "", $_POST['playerleagueID']);
	$cond[] = array("col" => "ID", "value" => $ID);
	uli_delete_record('player_league', $cond);


}


if ($_POST['action'] == "deletecontract"){
	$ID = str_replace("contract", "", $_POST['contractid']);
	$cond[] = array("col" => "ID", "value" => $ID);
	uli_delete_record('player_contracts', $cond);


}

?>