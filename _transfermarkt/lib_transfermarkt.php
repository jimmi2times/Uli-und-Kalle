<?php
/*
 * Created on 14.04.2009
 * Alle Funktionen fuer die Dateien im Ordner transfermarkt
 *
 *
 * Alles sicherheitshalber auf der festplatte kopiert (bevor ich die ganzen xajax aenderungen anfange
 *
 */


/* Bindet die Sprachdatei ein */
include('lang_transfermarkt.php');

/**
 * Quickstats fuer die Kaderuebersicht
 * Wieviel Spieler
 * Wieviel Gehalt pro Spieltag
 */
function print_quickstats_kader($uliID, $leagueID){
	global $option;
	// Wieviel Spieler
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "history", "value" => 0);

	$fields[] = 'SUM(salary)';
	$fields[] = 'COUNT(ID)';

	$result = uli_get_results('player_contracts', $cond, $fields, NULL, NULL, NULL, NULL, NULL);
	if ($result){
		foreach ($result as $result){
			$html .= YouVeGot.' <b>'.$result['COUNT(ID)'].' '.Players.'</b><br/>';
			$html .= "\n";
			$html .= TheyEarn.' <b>'.uli_money($result['SUM(salary)']).'</b> <br/>';
			$html .= "\n";
		}}
		return $html;
}





/**
 * Die Gebote eines Managers erfassen und als HTML ausgeben
 * �bergeben werden UliID und LeagueID
 */
function print_my_bets($uliID, $leagueID) {
	global $option, $allbets, $vermoegen, $guthaben, $kredite, $kreditrahmen;
	$cond[] = array("col" => "topbetuliID", "value" => $uliID);
	$cond[] = array("col" => "history", "value" => "0");
	$cond[] = array("col" => "hidden", "value" => "0");

	$result = uli_get_results('auctions a LEFT JOIN '.$option['prefix'].'uli_player p ON p.ID = a.playerID ', $cond);
	if($result){
		foreach($result as $bets){
			$playerlink[] = '<a class="playerinfo" id = "'.$bets['playerID'].'" href="#">'.$bets['name'].'</a>';
		}
		$html .= YouAreTheBoss.' '.count($result).' '.Auctions;
		$html .= ' ('.implode(", \n", $playerlink).')';

		$html .= ' Stand jetzt müsstest Du dafür <b>'.uli_money(get_sum_ulibets($uliID, 1)).'</b> bezahlen. (Achtung: Verborgene Auktionen werden hier nicht aufgeführt.)';


	}
	else {$html .= YouDontBet;}
	unset($cond);unset($result);unset($playerlink);

	/*
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "offer", "value" => "0", "func" => ">");
	$result = uli_get_results('negotiations n LEFT JOIN '.$option['prefix'].'uli_player p ON p.ID = n.playerID ', $cond);
	if($result){
		foreach($result as $bets){
			$playerlink[] = '<a class="playerinfo" id = "'.$bets['playerID'].'" href="#">'.$bets['name'].'</a> ('.uli_money($bets['offer']).')';
		}
		$html .= '<br/>'.FurthermoreYouBet.':<br/>';
		$html .= ''.implode("<br/>", $playerlink);
		$html .= "\n";
	}
	*/
	$bietrahmen = $guthaben + $kreditrahmen - $allbets - $kredite + 5000000;
	$html .= '<br/>';
	return $html;
}

/**
 * gibt die ganze Tabelle des Transfermarktes raus
 * Liste und running auctions
 *
 * 14.04.09
 */
function print_transfermarkt($showTranslist = ''){
	global $option;
	$html ='';
	/* Es werden alle Ulinamen eingelesen */
	$uliname = get_all_uli_names($option['leagueID']);
	/* Es werden alle Bundesligateamnamen eingelesen */
	$ligateam = get_all_team_names();

	if (!$showTranslist){
		// holt alle Auktionen, an denen der Nutzer irgendwie beteiligt ist
		$running_user_auctions = get_running_auctions_user($option['uliID']);
		if ($running_user_auctions){
			foreach ($running_user_auctions as $user_auction){
				$user_auctions[$user_auction['auctionID']] = $user_auction;}
		}

		// Erst einmal alle laufenden Auktionen
		$auctions = get_auctions($option['leagueID'], 1);
		if ($auctions){

			$headline = RunningAuctions.'<div class="jclock" style="float:right;"></div>';

			$html = uli_box($headline);
			foreach ($auctions as $auction){
				/* schaut ob der Nutzer hier mitbietet */
				$class = '';
				if ($user_auctions[$auction['auctionID']]['sum'] > 0 AND $auction['topbetuliID'] != $option['uliID']){$class = 'ueberboten';}
				if ($auction['topbetuliID'] == $option['uliID']){$class = 'topgebot';}

				if ($auction['hidden'] AND $user_auctions[$auction['auctionID']]['sum'] > 0 ){$class = 'hiddenauction';}

				/* Ausgabe der Auktion */
				$html .= print_auction($auction, $uliname, $ligateam, $user_auctions[$auction['auctionID']], $class);
				$html .= "\n\n";
			}}
			else {
				/* Es laufen keine Auktionen */
				$html = uli_box(RunningAuctions, NoRunningAuctions);
			}
	}
	$html .= '<div id="translist">';
	$html .= '</div>';
	// Die Transferliste
	if ($showTranslist == 1){
		$auctions = get_auctions($option['leagueID'], 0);

		// Sommerpause
		//unset($auctions);
		//echo '<h3>SOMMERPAUSE. ES GEHT ANFANG AUGUST WEITER. UND ES WIRD RECHTZEITIG BESCHEID GESAGT.</h3>';

		if ($auctions){
			$html .= uli_box(Translist);
			foreach ($auctions as $auction){
				/* Alle Informationen zum Spieler holen */
				// $player = get_player_infos($auction['playerID'], $tables = array('player', 'playerattributes', 'contracts', 'userplayer'), '', $option['leagueID']);
				/* Ausgabe der Auktion */
				if ($auction['name']){
					$html .= print_auction($auction, $uliname, $ligateam);

					$html .= "\n\n";
				}
			}}
			else {
				/* Es laufen keine Auktionen */
				$html .= uli_box(Translist, NoEntries);
			}

	}
	return $html;
}

/**
 * Gibt den Html-Code f�r eine Auktion zur�ck
 * Markierung der Zeilen (�berboten, H�chstbietender) durch �bergabe der CSS Klasse
 * FERTIG MACHEN
 *
 *
 */
function print_auction($auction, $uliname, $ligateam, $user_auction = '', $class = ''){
	global $option;


	/* H�chstgebot */
	if (isset($user_auction['sum'])){
		$myTopBet = $user_auction['sum'];
	}
	$player = $auction;

	$cond[] = array("col" => "playerID", "value" => $player['playerID']);
	$injured = uli_get_row("player_injured", $cond);
	if ($injured){
		$player['injury'] = TRUE;
		$player['injury_cause'] = $injured['cause'];
		$player['injury_update'] = $injured['timestamp'];
	}

	$player_age = player_age($player['birthday']);
	if ($player_age < 20){$age = 1;}
	elseif ($player_age <= 25){$age = 2;}
	elseif ($player_age <= 30){$age = 3;}
	else {$age = 4;}

	// Wenn eine Auktion nur noch 10 Minuten laeuft
	// automatischer reload
	if (($auction['end'] - time()) < 600 AND $auction['end'] > 0 AND $auction['hidden'] == 0){
		$html .= '
		<script type="text/javascript">
		   jQuery(document).ready(function() {
   				var refreshId = setInterval(function() {
     			jQuery("#bet-auctionID-'.$auction['auctionID'].'").load("ajax_bet.php?action=reloadbet&auctionID='.$auction['auctionID'].'");
   				}, 1000);
			});
 		</script>';
	}

	$html .= '<div id="auction-'.$auction['auctionID'].'" class="ageshow positionshow teamshow auction team'.$player['team'].' age'.$age.' position'.$player['hp'].' '.$class.'">';

	/* Wappenbild des Bundesligateams */
	$html .= "\n";
	$html .= '<div class="player">';
	$html .= get_ligateam_wappen($player['team'], $ligateam);
	$html .= "\n";



	$html .= '<b><span><a href="#" class="playerinfo" id = "'.$player['playerID'].'">'.$player['name'].'</a></span></b> ';
	// Alter
	$html .= '('.player_age($player['birthday']).') ';
	if ($player['star'] > 0){
		$html .= get_star_icon($player['star']).' ';
	}

	if ($player['injury']){
		$html .= get_injury_pic($player['injury_cause'].' (letztes Update: '.uli_date($player['injury_update']).')').' ';
	}

	/* Div Marker fuer die Spielerinfo */
	$html .= '<div class="marker" id="player-'.$auction['playerID'].'"></div>';

	/* Positionen und Fu� */
	$html .= '<br/>';
	$html .= $option['position'.$player['hp'].'-2'].' ';
	if ($player['np1']){$html .= $option['position'.$player['np1'].'-2'].' ';}
	if ($player['np2']){$html .= $option['position'.$player['np2'].'-2'].' ';}
	$html .= '('.$option['foot'.$player['foot'].'-2'].')';
	$html .= '</div>';

	/* aktuelles H�chstgebot */
	/* Hier umschlie�t ein DIV die Angaben damit aktualisiert werden kann */
	$html .= "\n";
	$html .= '<div class="auctionbet" id ="bet-auctionID-'.$auction['auctionID'].'">';

	/* wenn Auktion l�uft, wird das H�chstgebot angezeigt */
	if ($auction['topbet'] > 0){

		if ($auction['hidden'] == 0){
			$html .= uli_money($auction['topbet']);
			$html .= ' ('.$uliname[$auction['topbetuliID']].')';
		}
		else {
			$html .= 'Verdeckte Auktion';
		}

		if ($user_auction['sum']){
			$html .= '<br>'.MyTopBet.': '.uli_money($user_auction['sum']);
		}
	}
	/* ansonsten die Forderung des Klubs */
	elseif ($auction['claim'] > 0) {
		$html .= Claim.': '.uli_money($auction['claim']).'<br/>';
		$html .= ' ('.$uliname[$auction['claimuliID']].')';
	}
	else {
		$html .= '&nbsp;';
	}
	$html .= '</div>';
	$html .= "\n";

	/* Ende der Auktion */
	$html .= '<div class="auctionend">';
	if ($auction['end']){
		$html .= uli_date($auction['end'],1);
	}
	else {
		$html .= '&nbsp;';
	}
	$html .= '</div>';
	$html .= "\n";

	/* Gebotsformular */
	$html .= '<div class="yourbet">';

	// wenn nicht der eigene Klub
	if ($auction['claimuliID'] != $option['uliID']){
		// Gebot abgeben
		$html .= '<form class="betauction" id="'.$auction['auctionID'].'" method ="POST">';
		$html .= "\n";
		$html .= '<input type="hidden" id="playerID'.$auction['auctionID'].'" name="playerID'.$auction['auctionID'].'" size="10" value="'.$auction['playerID'].'">';
		$html .= "\n";
		$html .= '<input class="formauction auctionBet sumInput" data-a-dec="," data-a-sep="." data-m-dec="0" type="text" id="sum-auction'.$auction['auctionID'].'" name="sum-auction'.$auction['auctionID'].'" size="10">';
		$html .= "\n";
		$html .= '<input class="formauction" type="submit" value="'.SubmitBet.'">';
		$html .= "\n";
		$html .= '</form>';
	}
	else {
		$html .= AuctionYourPlayer;
	}
	$html .= '</div>';
	$html .= '</div>';
	return $html;

}

/**
 * Holt Auktionen
 * bezieht die Filter mit ein
 * kann folgende Filter verarbeiten: Alter, Position, Team
 * bezieht mit ein, ob die Auktion gestartet ist, oder nicht
 * 18.04.09
 */
function get_auctions($leagueID, $started, $posFilter = '', $ageFilter = '', $teamFilter = '') {
	global $wpdb, $option;
	$conditionArray = array();
	$tableQuery = 'SELECT a.ID as auctionID, a.*, p.* FROM '.$option['prefix'].'uli_auctions a ';
	$conditionArray[] = ' a.leagueID = '.$leagueID;
	$conditionArray[] = 'a.history = 0';

	if ($started == 0){$conditionArray[] = 'end IS NULL';}
	if ($started == 1){$conditionArray[] = 'end > 0';$conditionArray[] = 'a.end > '.time();}

	$joinString = ' LEFT JOIN '.$option['prefix'].'uli_player p ON p.ID = a.playerID ';

	//	if ($posFilter){
	//		$conditionArray[] = '(p.hp IN ('.implode(',', $posFilter).') OR p.np1 IN ('.implode(',', $posFilter).') OR p.np2 IN ('.implode(',', $posFilter).'))';
	//	}
	//
	//	if ($ageFilter) {
	//		$timestamp = time();
	//		/* R�nder der Alterklassen berechnen */
	//		/* Beispiel 20-25 Jahre: Geburtstag < heute - 20 Jahre und Geburtstag > heute - 25 Jahre */
	//		$Twentyyears = $timestamp - (20* 365.25 * 24 * 3600);
	//		$Twentyfiveyears = $timestamp - (25* 365.25 * 24 * 3600);
	//		$Thirtyyears = $timestamp - (30* 365.25 * 24 * 3600);
	//
	//		if(in_array(1, $ageFilter)){$ageArray[] = '(p.birthday > '.$Twentyyears.') ';}
	//		if(in_array(2, $ageFilter)){$ageArray[] = '(p.birthday < '.$Twentyyears.' AND p.birthday > '.$Twentyfiveyears.') ';}
	//		if(in_array(3, $ageFilter)){$ageArray[] = '(p.birthday < '.$Twentyfiveyears.' AND p.birthday > '.$Thirtyyears.') ';}
	//		if(in_array(4, $ageFilter)){$ageArray[] = '(p.birthday < '.$Thirtyyears.') ';}
	//		if ($ageArray){$conditionArray[] = ' ('.implode(' OR ', $ageArray).') ';}
	//		else {$conditionArray[] = ' p.birthday = 0 ';}
	//	}
	//	if ($teamFilter){
	//		if(!in_array(0, $teamFilter)){$conditionArray[] = ' p.team = '.implode('', $teamFilter).' ';}
	//	}

	$orderquery = ' ORDER by a.end ASC, p.team ASC, p.name ASC';

	$conditionquery = " WHERE ".implode(" AND ", $conditionArray);
	$sql = $tableQuery.$joinString.$conditionquery.$orderquery;
	$result = $wpdb->get_results($sql, ARRAY_A);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * Holt eine aktuelle Auktion eines Spielers
 * return ROW
 * 21.04.09
 */
function get_auction_player($playerID, $leagueID) {
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "history", "value" => "0");
	$result = uli_get_row('auctions', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * Holt alle Auktionen an denen ein Nutzer beteiligt ist
 * 28.04.09
 */
function get_running_auctions_user($uliID) {
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "history", "value" => "0");
	$result = uli_get_results('auctions_bets', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * Holt eine Auktion nach ID
 * 22.04.09
 */
function get_auction($ID) {
	$cond[] = array("col" => "ID", "value" => $ID);
	$result = uli_get_row('auctions', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * aktualisiert eine Auktion
 * 22.04.09
 */
function update_auction($auction) {
	$cond[]		= array("col" => "ID", "value" => $auction['ID']);
	$values[]	= array("col" => "start", "value" => $auction['start']);
	$values[]	= array("col" => "end", "value" => $auction['end']);
	$values[]	= array("col" => "topbet", "value" => $auction['topbet']);
	$values[]	= array("col" => "topbetuliID", "value" => $auction['topbetuliID']);
	$values[]	= array("col" => "topbetID", "value" => $auction['topbetID']);
	$values[]	= array("col" => "hidden", "value" => $auction['hidden']);

	uli_update_record('auctions',$cond,$values);
}


/**
 * aktualisiert eine auktion
 * schreibt die neuen top-gebote
 * 22.04.09
 *
 */
function calculate_auction($bet, $auctionID, $uliID, $betID){
	/* holt die aktuellen daten der auktion */
	$auction = get_auction($auctionID);

	// Zufallsgenerator fuer versteckte auktionen bei Start
	if ($auction['topbet'] < 1){
		$zufall = rand(1,3);
		if ($zufall == 1){
			$auction['hidden'] = 1;
		}
		else {
			$auction['hidden'] = 0;
		}
		/* Berechnen der Laufzeit einer Auktion */
		/* Zufallsfaktor. Basiswert ist 6 Tage + max 2 Tage */
		$random = rand(0, 950400);
		$auction['end'] = time() + 259200 + $random;
		$auction['start'] = time();

	}

	/* wenn noch kein h�chstgebot - 1. Gebot 100.000 und start der auktion */
	if ($auction['hidden'] == 0){
		if ($auction['topbet'] < 1){
			$auction['topbet'] = 100000;
			$auction['topbetuliID'] = $uliID;
			$auction['topbetID'] = $betID;
			/* falls es eine forderung gibt: Startwert = Claim */
			if ($auction['claim']){$auction['topbet'] = $auction['claim'];}
			$message = AuctionStarted;
		}
		/* wenn Auktion aktualsiert wird */
		$currenttopbet = get_bet($auction['topbetID']);
		if ($auction['topbet'] > 1 AND $uliID != $auction['topbetuliID']){
			/* berechnet das neue topgebot */

			/* neues gebot ist h�her */
			if ($bet > $currenttopbet['sum']) {
				if (($bet-$currenttopbet['sum']) < 100000){$auction['topbet'] = $bet;}
				else {$auction['topbet'] = $currenttopbet['sum'] + 100000;}
				if ($bet > 9999999) {
					if (($bet-$currenttopbet['sum']) < 250000){$auction['topbet'] = $bet;}
					else {$auction['topbet'] = $currenttopbet['sum'] + 250000;}
				}
				$auction['topbetuliID'] = $uliID;
				$auction['topbetID'] = $betID;
				$message = 'Dein Gebot ist das aktuelle H&ouml;chsgebot';
			}
			if ($bet < $currenttopbet['sum']) {
				if (($currenttopbet['sum']-$bet) < 100000){$auction['topbet'] = $currenttopbet['sum'];}
				else {$auction['topbet'] = $bet + 100000;}
				$auction['topbetuliID'] = $currenttopbet['uliID'];
				$auction['topbetID'] = $currenttopbet['ID'];
				$message = SorryYourAreNotTheTopBet;
			}
			if ($bet == $currenttopbet['sum']) {
				$auction['topbet'] = $currenttopbet['sum'];
				$auction['topbetuliID'] = $currenttopbet['uliID'];
				$auction['topbetID'] = $currenttopbet['ID'];
				$message = SorryYourAreNotTheTopBet;
			}
		}
		if ($auction['topbet'] > 1 AND $uliID == $currenttopbet['topbetuliID']){
			$message = YourTopBetWasSaved;
		}
		update_auction($auction);
		$auction['message'] = $message;
	}
	// Fuer eine verdeckte Auktion
	else {
		if ($auction['topbet'] < $bet){
			$auction['topbetuliID'] = $uliID;
			$auction['topbetID'] = $betID;
			$auction['topbet'] = $bet;
			update_auction($auction);
			$message = YourTopBetWasSaved;
		}
		$auction['message'] = $message;
	}
	return $auction;
}

/**
 * holt ein gebot nach uliID und auctionID
 * 21.04.09
 */
function get_bet($ID) {
	$cond[] = array("col" => "ID", "value" => $ID);
	$result = uli_get_row('auctions_bets', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * holt ein gebot nach uliID und auctionID
 * 21.04.09
 */
function get_bet_uliID($auctionID, $uliID) {
	$cond[] = array("col" => "auctionID", "value" => $auctionID);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$result = uli_get_row('auctions_bets', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * liefert die Summe aller (H�chst)gebote eines Managers (aktuell)
 * 21.04.09
 * Unterscheidung ob verdeckt oder offen möglich
 * 11.8.15
 */
function get_sum_ulibets($uliID, $hidden = ''){
	$cond[] = array("col" => "topbetuliID", "value" => $uliID);
	$cond[] = array("col" => "history", "value" => "0");
	if (isset($hidden) AND $hidden == 1){
		$cond[] = array("col" => "hidden", "value" => "0");
	}
	$result = uli_get_var('auctions', $cond, 'SUM(topbet)');
	if ($result){return $result;}
	else {return FALSE;}
}



/**
 * schreibt ein gepr�ftes gebot in die Tabelle auctions_bets
 */
function update_bet($bet, $auctionID, $uliID) {
	if ($ulibet = get_bet_uliID($auctionID, $uliID)){
		$cond[]		= array("col" => "uliID", "value" => $uliID);
		$cond[]		= array("col" => "auctionID", "value" => $auctionID);
		$values[]	= array("col" => "sum", "value" => $bet);
		uli_update_record('auctions_bets',$cond,$values);
		$ID = $ulibet['ID'];
	}
	else {
		$values[]		= array("col" => "uliID", "value" => $uliID);
		$values[]		= array("col" => "auctionID", "value" => $auctionID);
		$values[]	= array("col" => "sum", "value" => $bet);
		$ID = uli_insert_record('auctions_bets',$values);
	}
	return $ID;
}


/**
 * ueberprueft ob ein gebot abgegeben werden darf
 * @return unknown_type
 */
function check_offer_takeover($bet, $uliID, $leagueID = ''){
	global $option, $allbets, $vermoegen, $guthaben, $kredite, $kreditrahmen;
	/* Holt benoetigte Daten */
	settype($bet, INT);

	$allbets     = $allbets + $bet;
	$guthabennew = $guthaben - $bet;
	$vermoegen   = $vermoegen - $bet;
	if ($bet < 100000) {$error = nohundred;}
	if ($vermoegen   < -10000000 AND $bet > 777777) {$error = novermoegen;}
	if ($guthabennew < -5000000  AND $bet > 777777) {$error = nomoney;}
	if (!$bet) {$error = typesomething;}

	return $error;
}
/**
 * �berpr�ft ein gebot auf der Transferliste
 * liefert einen definierten Fehler oder FALSE (alles ok) zur�ck
 * Die finanziellen Werte werden in der Transfermarkt.php schon geholt, weil sie auch f�r die Anzeige in der linken Spalte immer ben�tigt werden
 *
 * 06.05.09
 */

function check_bet_auction($playerID, $bet, $auctionID, $uliID = '', $leagueID='') {
	global $option, $allbets, $vermoegen, $guthaben, $kredite, $kreditrahmen;
	if (!$leagueID){$leagueID = $option['leagueID'];}
	if (!$uliID){$uliID = $option['uliID'];}
	$timestamp = time();
	$error = 'FALSE';

	settype($bet, INT);

	/* Holt ben�tigte Daten */
	$auction     = get_auction_player($playerID, $leagueID);
	$mybet       = get_bet_uliID($auctionID, $uliID);

	$allbets     = $allbets + $bet;
	$guthabennew = $guthaben - $bet;
	$vermoegen   = $vermoegen - $bet;

	// Abfragen
	if ($auction['end'] < $timestamp AND $auction['end'] != 0) {$error = toolate;}
	if ($bet <= ($auction['topbet'] + 24999)) {$error = toolow;}
	if ($bet > 999999 AND $bet <= ($auction['topbet'] + 99999)) {$error = toolow;}
	if ($bet > 9999999 AND $bet <= ($auction['topbet'] + 249999)) {$error = toolow;}
	if ($bet < $auction['claim']) {$error = 'Weniger als gefordert.';}
	if ($bet < 100000) {$error = nohundred;}
	if ($vermoegen   < -10000000 AND $bet > 777777) {$error = novermoegen;}
	if ($guthabennew < -5000000  AND $bet > 777777) {$error = nomoney;}
	if (!$bet) {$error = typesomething;}
	if ($bet <= $mybet['sum']){$error = 'Weniger als das eigene H&ouml;chstgebot.';}

	return $error;
}



///**
// * �bergibt mit Ajax das Gebot
// */
//function submit_bet_ajax($form, $uliID, $auctionID, $playerID, $leagueID){
//	$objResponse = new xajaxResponse();
//	$sum = $form['sum-auction'.$auctionID];
//	/* Gebot �berpr�fen */
//	$error = check_bet_auction($playerID, $sum, $auctionID, $leagueID);
//
//	/* Wenn Gebot OK, schreiben und Auktion aktualisieren */
//	if($error == 'FALSE'){
//		/* Eintragen/Aktualisieren */
//		$betID = update_bet($sum, $auctionID, $uliID);
//		$auction = calculate_auction($sum, $auctionID, $uliID, $betID);
//	}
//
//	/* Auktionsfeld wird aktualisiert */
//	if ($auction)
//	{
//		$uliname = get_all_uli_names($leagueID);
//		$newAuction .= uli_money($auction['topbet']);
//		$newAuction .= ' ('.$uliname[$auction['topbetuliID']].')';
//		$newAuction .= '<br>';
//		$newAuction .= MyTopBet.': '.uli_money($sum);
//		$objResponse->assign('bet-auctionID-'.$auctionID, 'innerHTML', $newAuction);
//	}
//
//	/* CSS Klasse wird aktualisiert */
//	if ($auction){
//		if ($auction['topbetuliID'] == $uliID)
//		{$class = 'topgebot';}
//		if ($auction['topbetuliID'] != $uliID)
//		{$class = 'ueberboten';}
//		$objResponse->assign('auction-'.$auctionID, 'className', 'auction '.$class);
//	}
//
//
//	/* Message wird ausgegeben */
//	if ($error != 'FALSE'){$message = $error;}
//	elseif ($auction['message']){$message = $auction['message'];}
//
//	$objResponse->call("YAHOO.example.container.message.setHeader", "<div class='tl'></div><span>Panel #2 from Script</span><div class='tr'></div>");
//	$objResponse->call("YAHOO.example.container.message.setBody", $message);
//	$objResponse->call("YAHOO.example.container.message.render", "container");
//	$objResponse->call("YAHOO.example.container.message.show");
//	return $objResponse;
//}

/********************* PROVISORISCHE SORTIERUNG ***********************/
/*********************         KADER            ***********************/

/**
 * Gibt den eigenen Kader aus
 */
function print_kader($sort = '') {
	global $option;
	if (!$sort){$sort = 'jerseynumber';}

	$html ='';
	// Es werden alle Ulinamen eingelesen
	$uliname = get_all_uli_names($option['leagueID']);
	// Es werden alle Bundesligateamnamen eingelesen
	$ligateam = get_all_team_names();

	// Holt die Sortierung des Users
	//$sortFilterArray = get_filter($option['uliID'], 'sortKader');
	//$sortFilter = $sortFilterArray[0]; /* hier steht immer nur ein Wert drinne */

	// Holt alle Spieler des eigenen Kaders
	$userTeam = get_user_team_sort($option['uliID'], $sort);
	if ($userTeam){
		$html .= '<table>';
		foreach ($userTeam as $player){
			// Ausgabe des Spielers
			$html .= '<div class="kader-player" id="kader-player-'.$player['playerID'].'">';
			$html .= print_player_kader($player['playerID'], $uliname, $ligateam);
			$html .= '</div>';
			$html .= "\n\n";
		}}
		else {
			$html .= YouVeGotNoPlayers;
		}
		$html .= '</table>';
		return $html;
}


/**
 * gibt einen Spieler im Raster des eigenen Kaders aus
 *
 * 1. Spalte Bundesligateam|Name|Alter|Position|R�ckennummer mit Trikot|Zufriedenheit|Loyalit�t
 * 2. Spalte Vertrag|Marktwert
 * 3. Spalte Verkaufen|Verhandeln|...
 *
 */
function print_player_kader($playerID, $uliname = array(), $ligateam = array()){
	global $option;
	$player = get_player_infos($playerID, $option['leagueID'], array('contracts'));

	// Wenn ein Spieler keine Rueckennummer hat, wird sie hier vergeben
	if ($player['jerseynumber'] == 0){
		set_jersey_number($option['uliID'], $player['playerID'], '', 1);
	}


	// Schaut, ob der Spieler auf der Transferliste steht
	$auction = get_auction_player($player['playerID'], $option['leagueID']);

	// Schaut ob es angebote eines spielers gibt.
	$negotiations = get_negotiations($player['playerID'], $option['leagueID']);

	// Wappenbild des Bundesligateams
	$html .= "\n";
	$html .= '<div class="player">';
	// Trikotnummer
	$html .= '<div class="jerseynumber" id = "jerseynumber-'.$player['playerID'].'"><a class="jerseynumber" id = "'.$player['playerID'].'" href="#">'.$player['jerseynumber'].'</a></div>';
	$html .= "\n";


	// Was passiert mit Kuenstlernamen (Der Name ist immer das entscheidenden fuer kleine Listen --> das heisst da muss auch der kuenstlername stehen ...)
	$html .= '<b><span><a href="#" class="playerinfo" id = "'.$player['playerID'].'">'.$player['name'].'</a></span></b> ';
	// Alter
	$html .= '('.player_age($player['birthday']).') ';
	if ($player['captain'] == 1){
		$html .= ' <b>(Cap)</b>';
	}

	if ($player['injury']){
		$html .= ' '.get_injury_pic($player['injury_cause'].' (letztes Update: '.uli_date($player['injury_update']).')').' ';
	}

	// Div Marker fuer die Spielerinfo
	$html .= '<div class="marker" id="player-'.$player['playerID'].'"></div>';

	// Positionen und Fu�
	$html .= '<br/>';
	$html .= get_smile_icon($player['smile']).' ';
	$html .= get_ligateam_wappen($player['team'], $ligateam).' ';
	if ($player['star'] > 0){
		$html .= get_star_icon($player['star']).' ';
	}

	$html .= $option['position'.$player['hp'].'-2'].' ';
	if ($player['np1']){$html .= $option['position'.$player['np1'].'-2'].' ';}
	if ($player['np2']){$html .= $option['position'.$player['np2'].'-2'].' ';}
	$html .= '('.$option['foot'.$player['foot'].'-2'].')';
	$html .= '</div>';

	// Zweite Spalte
	$html .= '<div class="contract">';
	$html .= "\n";
	$html .= Salary.': '.uli_money($player['salary']).' ';
	if (($player['contractend'] - time()) < 1209600){$html .= '<span class="attention">';}
	$html .= '('.until.': '.uli_date($player['contractend']).')';
	if (($player['contractend'] - time()) < 1209600){$html .= '</span>';}
	$html .= '<br/>';
	$html .= Marktwert.': '.uli_money(round($player['marktwert'], -5));
	if ($auction){
		$html .= '<span class="attention">';
		$html .= '<br/>'.PlayerIsOnList.Claim.' '.uli_money($auction['claim']);
		if ($auction['end'] > 0){
			$html .= '<br/>';
			$html .= ' '.AuctionEndsOn.' '.uli_date($auction['end']);
		}
		$html .= '</span>';
	}

	$html .= '</div>';
	$html .= "\n";

	// Dritte Spalte
	$html .= '<div class="actions">';
	$html .= "\n";

	// Verkaufen
	if (!$auction){
		$html .= '<a href="#" class="sellplayer" id = "'.$player['playerID'].'">'.Sell.'</a> ';
	}
	else {
		$html .= '<a href="#" class="takehome" id = "'.$player['playerID'].'">'.BackFromList.'</a> ';
	}
	// Vertrag verlaengern
	$html .= '<a href="#" class="contractplayer" id = "'.$player['playerID'].'">'.RenewContract.'</a> ';

	$html .= '<br/>';

	if ($player['save'] == 0){
		$html .= '<a href="#"><div class="saveplayer" id="'.$player['playerID'].'">Spieler sch&uuml;tzen</div></a>';
	}
	else {
		$html .= '<div class="playerissave">Spieler ist gesch&uuml;tzt</div></a>';
	}

	// Feuern
	// TODO
	//$html .= '<a href="#" onclick="xajax_fire_player(\''.$player['playerID'].'\', \''.$player['uliID'].'\')">'.Fire.'</a> ';

	// TODO
	// Verleihen
	//$html .= '<a href="#" onclick="xajax_lent_player(\''.$player['playerID'].'\', \''.$player['uliID'].'\')">'.Lent.'</a>';


	$html .= '</div>';
	$html .= "\n";
	// Marker fuer die Verhandlungen. Diese werden dynamisch angesprochen
	$html .= '<div class="negotiation" id="negotiation-'.$player['playerID'].'" style="margin-left: 5px">';

	// Wenn es Gebote gibt, werden die hier ausgegeben
	if ($negotiations){
		foreach ($negotiations as $negotiation){
			if ($negotiation['uliID'] != $player['uliID']){
				if ($negotiation['klubdecision'] == 1) {
					$html .= '<span id="offer-'.$negotiation['ID'].'">';
					//print_r($negotiation);
					$html .= $uliname[$negotiation['uliID']].Bets.uli_money($negotiation['offer']);
					$html .= '<input id="offerplayerid-'.$negotiation['ID'].'" type="hidden" value="'.$player['playerID'].'">';
					$html .= ' <a href="#" class="acceptoffer" id = "'.$negotiation['ID'].'">'.AcceptOffer.'</a> ';
					$html .= '<a href="#" class="rejectoffer" id = "'.$negotiation['ID'].'">'.RejectOffer.'</a> ';


					$html .= '</br>';
					$html .= '</span>';
				}
				// Die Forderung nach mehr Geld
				if ($negotiation['klubdecision'] == 4) {
					if ($newClaim < $negotiation['salary']){
						$newClaim = $negotiation['salary'];
					}
				}
			}
		}
		if ($newClaim > 0 AND $newClaim > $player['salary']){
			$html .= '<span id="raisesalary-'.$player['playerID'].'">';
			$html .= PlayerWantsMoreMoney.HeWouldBeHappyAbout.uli_money($newClaim);
			$html .= '<input id="newsalary-'.$player['playerID'].'" type="hidden" value="'.$newClaim.'">';
			$html .= ' <a href="#" class="raisesalary" id = "'.$player['playerID'].'">'.RaiseSalary.'</a> ';
			$html .= '</br>';
			$html .= '</span>';
		}
	}
	$html .= '</div>';
	$html .= "\n";
	return $html;
}





/**
 * Vergibt fuer einen Spieler eine Trikotnummer
 * Wenn newnumber = 1: Nimmt automatisch als neue die niedrigste freie Nummer im Kader
 *  * Ansonsten wird die uebergebene Nummer geschrieben
 */
function set_jersey_number($uliID, $playerID, $number = '', $newnumber =''){
	if ($newnumber == 1)
	{
		// Naechst freie Rueckennummer ermitteln
		// Holt zunaechst vergebenen Rueckennummern in ein Array
		$jerseyNumbers = get_all_jersey_numbers($uliID);
		if ($jerseyNumbers){
			$i = 0;
			$newJerseyNumber = 0;
			while($newJerseyNumber == 0 AND $i < count($jerseyNumbers)){
				$firstValue = $jerseyNumbers[$i]['jerseynumber'];
				$secondValue = $jerseyNumbers[$i+1]['jerseynumber'];
				$diff = $secondValue - $firstValue;
				if ($diff != 1 AND $firstValue != 0 AND $secondValue != 0){$newJerseyNumber = $firstValue + 1;}
				if ($diff != 1 AND $firstValue != 0){$newJerseyNumber = $firstValue + 1;}
				$i++;
			}}
			// Alle Nummern sind vergeben - die naechst hoehere wird genommen
			if ($secondValue != 0 AND $newJerseyNumber == 0){$newJerseyNumber = $secondValue + 1;}
			if ($newJerseyNumber == 0){$newJerseyNumber = 1;}
			$number = $newJerseyNumber;
	}
	$cond[]		= array("col" => "uliID", "value" => $uliID);
	$cond[]		= array("col" => "playerID", "value" => $playerID);
	$values[]	= array("col" => "jerseynumber", "value" => $number);
	uli_update_record('player_league',$cond,$values);
}

/**
 * Holt alle R�ckennummern in ein Array
 * 12.05.09
 */
function get_all_jersey_numbers($uliID){
	$cond[] = array("col" => "uliID", "value" => $uliID);
	//$cond[] = array("col" => "jerseynumber", "value" => 0, "func" => "!");
	$order[]= array("col" => "jerseynumber");
	$result = uli_get_results('player_league', $cond, array('jerseynumber'), $order);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * XAJAX Funktion
 * gibt den Wechsler f�r R�ckennummern aus
 *
 */
function print_number_select($playerID, $uliID){
	$userteam = get_user_team_sort($uliID, 'jerseynumber');
	/* Kader wird nach Rueckennummern indiziert */
	if ($userteam){
		foreach ($userteam as $userteamplayer){
			$player[$userteamplayer['jerseynumber']] = $userteamplayer;
		}}
		$html .= NewNumber.': ';
		$html .= '<select class = "jerseynumberchange" id = "'.$playerID.'" name="jerseynumber">';
		/* Rueckennummern werden bis 50 angeboten */
		for ($i = 1; $i <= 50; $i++){
			$select = '';
			if ($player[$i]['playerID'] == $playerID){$select = 'selected = "selected"';}
			$html .=  '<option value="'.$i.'" '.$select.'>'.$i;
			if ($player[$i]){$html .= ' '.$player[ $i]['name'];}
			else {$html .= ' '.FreeJerseyNumber;}
			$html .= '</option>';
		}
		$html .= '</select>';
		return $html;
}


/**
 * Vergibt die neue Nummer
 * Der Spieler, der diese Nummer besa�, bekommt die neue
 */
function change_jersey_number($playerID, $uliID, $newNumber){
	$oldJerseyNumber = get_jersey_number($playerID, $uliID);
	$playerWithNewNumber = get_player_by_jersey_number($newNumber, $uliID);
	set_jersey_number($uliID, $playerID, $newNumber);
	if ($playerWithNewNumber){
		set_jersey_number($uliID, $playerWithNewNumber, $oldJerseyNumber);
	}

	$html .= '
		<script type="text/javascript">
		$("#jerseynumber-'.$playerID.' a.jerseynumber").html("'.$newNumber.'");
		$("#jerseynumber-'.$playerWithNewNumber.' a.jerseynumber").html("'.$oldJerseyNumber.'");
		</script>';
	return $html;
}


/**
 * XAJAX Funktion
 * gibt das Panel zum Verkaufen eines Spielers aus
 * 16.07.2010
 *
 */
function sell_player($playerID, $uliID, $playername){
	$objResponse = new xajaxResponse();
	$cssid = 'player-'.$playerID;

	$html .= WantedSumFor.$playername;
	$html .= '<form id="PutPlayerOnList" method ="POST" onsubmit="return false">';
	$html .= "\n";
	$html .= '<input class="formauction" type="text" name="WantedSum" size="12">';
	$html .= "\n";
	$html .= '<input class="formauction" type="submit" value="'.PutHimOnList.'" onclick="xajax_put_player_on_list(xajax.getFormValues(\'PutPlayerOnList\'), \''.$playerID.'\', \''.$uliID.'\')">';
	$html .= "\n";
	$html .= '</form>';


	$objResponse->call("YAHOO.example.container.PlayerInfo.setHeader", "<div class='tl'></div>".SellPlayer."</span><div class='tr'></div>");
	$objResponse->call("YAHOO.example.container.PlayerInfo.setBody", $html);
	$objResponse->call("YAHOO.example.container.PlayerInfo.render", $cssid);
	$objResponse->call("YAHOO.example.container.PlayerInfo.show");
	return $objResponse;
}

/**
 * packt eine Spieler auf die Transferliste
 * schreibt die Forderung, etc.
 * 16.07.2010
 *
 */
function put_player_on_list($form, $playerID, $uliID){
	global $option;
	$leagueID = $option['leagueID'];
	$player = get_player_infos($playerID, $option['leagueID']);
	$claim = $form['WantedSum'];

	// CHECK OB SPIELER WIRKLICH IM KADER
	if ($player['uliID'] == $uliID)
	{
		if ($claim > 99999) {
			// Wenn der Spieler ungluecklich ist und die Forderuung unter Marktwert, freut er sich
			if ($player['smile'] < 40 AND $claim < $player['marktwert']){
				$smile = rand (5,20);
			}
			// Wenn der Spieler gluecklich ist, findet er das doof
			if ($player['smile'] > 40){
				$smile = rand (-5,-20);
			}
			update_smile($playerID, $leagueID, $smile, NULL, NULL, $option['currentyear']);
			$write = write_offer_player($playerID, $uliID, $claim, 1, $leagueID);
		}
		else {$error = LessThan100K;}
	}
	else {
		$error = SomeThingWrongTryAgain;
	}

	if (!$write AND !$error){
		$error = SomeThingWrongTryAgain;
	}
	if ($error){
		$html .= $error;
	}
	else {
		$html .= PlayerIsOnTheList;
	}
	if (!$error){
		$span .= '<span class=\"attention\">';
		$span .= '<br/>'.PlayerIsOnList.Claim.' '.uli_money($claim);
		$span .= '</span>';
		$html .= '
		<script type="text/javascript">
		$("#kader-player-'.$playerID.' .attention").hide();
		$("#kader-player-'.$playerID.' .sellplayer").addClass("takehome");
		$("#kader-player-'.$playerID.' .sellplayer").removeClass("sellplayer");
		$("#kader-player-'.$playerID.' .takehome").html("'.BackFromList.'");
		$("#kader-player-'.$playerID.' .contract").append("'.$span.'");
		</script>';
	}

	return $html;
}

/**
 * holt eine Spieler von der Transferliste zurueck
 * 16.07.2010
 *
 */
function take_player_home($playerID, $uliID){
	global $option;
	$leagueID = $option['leagueID'];
	$player = get_player_infos($playerID, $option['leagueID']);

	// CHECK OB SPIELER WIRKLICH IM KADER
	if ($player['uliID'] == $uliID)
	{
		// check, ob eine Auktion schon laeuft
		$auction = get_auction_player($playerID, $option['leagueID']);
		if ($auction['end'] == 0 OR $auction['end'] == NULL){
			// Um das hin und herklicken zu vermeiden, muss man mindestens eine stunde warten, bevor man ihn wieder zurueckholt
			if (($auction['start'] + 3600) > time()){
				//Zum Testen ohne Zeitbeschraenkung.
				//if (($auction['start']) > time()){
				$error = WaitAtLeastAnHour;
			}
			else {
				$write = write_offer_player($playerID, $uliID, 0, 0, $leagueID);
			}
		}
		else {
			$error = AuctionIsRunning;
		}
	}
	else {
		$error = SomeThingWrongTryAgain;
	}

	if (!$write AND !$error){
		$error = SomeThingWrongTryAgain;
	}
	if ($error){
		$html .= $error;
	}
	else {
		$html .= PlayerIsBack;
	}
	// Wenn kein Error, wird mit einem JS hier der Menupunkt ausgetauscht
	if (!$error){
		$html .= '
		<script type="text/javascript">
		$("#kader-player-'.$playerID.' .attention").hide();
		$("#kader-player-'.$playerID.' .takehome").addClass("sellplayer");
		$("#kader-player-'.$playerID.' .takehome").removeClass("takehome");
		$("#kader-player-'.$playerID.' .sellplayer").html("'.Sell.'");
		</script>';
	}
	return $html;
}


/**
 * Holt die R�ckennummer eines Spielers aus einem Team
 * ben�tigt playerID und uliID
 * 14.05.09
 */
function get_jersey_number($playerID, $uliID){
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$result = uli_get_var('player_league', $cond, 'jerseynumber');
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * Holt die den Spieler mit einer bestimmten Nummer aus einem Team
 * ben�tigt R�ckennummer und uliID
 * 14.05.09
 */
function get_player_by_jersey_number($jerseynumber, $uliID){
	$cond[] = array("col" => "jerseynumber", "value" => $jerseynumber);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$result = uli_get_var('player_league', $cond, 'playerID');
	if ($result){return $result;}
	else {return FALSE;}
}


/**
 * schreibt eine neue Auktion fuer einen Spieler bzw. nimmt einen Spieler wieder von der Liste
 * return TRUE oder FALSE
 */
function write_offer_player($playerID, $uliID, $claim, $list, $leagueID='') {
	global $option;
	if (!$leagueID){$leagueID = $option['leagueID'];}
	// (Neu) auf die Liste setzen
	if ($list == 1){
		$values[]	= array("col" => "claimuliID", "value" => $uliID);
		$values[]	= array("col" => "leagueID", "value" => $leagueID);
		$values[]	= array("col" => "claim", "value" => $claim);
		$values[]	= array("col" => "playerID", "value" => $playerID);
		$values[]	= array("col" => "start", "value" => time());
		$ID = uli_insert_record('auctions',$values);
	}

	// Von der Liste nehmen (Auktion beenden)
	if ($list == 0){
		$cond[]		= array("col" => "playerID", "value" => $playerID);
		$cond[]		= array("col" => "claimuliID", "value" => $uliID);
		$cond[]		= array("col" => "leagueID", "value" => $leagueID);
		$values[]	= array("col" => "history", "value" => 1);
		$ID = uli_update_record('auctions',$cond,$values);
	}
	if ($ID){return TRUE;}
	else {return FALSE;}
}

/**
 * liefert eine Vertrag nach Spieler ID und Uli ID zurueck
 * 10.07.2011
 */
function get_contract($playerID, $uliID) {
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "history", "value" => 0);
	$result = uli_get_row('player_contracts', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}


/**
 * liefert eine verhandlungen nach id zurueck
 * benoetigt ID
 * 10.07.2011
 */
function get_negotiation($id) {
	$cond[] = array("col" => "ID", "value" => $id);
	$result = uli_get_row('player_contracts_negotiations', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * liefert alle verhandlungen zu einem spieler zurueck
 * benoetigt playerID
 * 10.07.2011
 */
function get_negotiations($playerID, $leagueID) {
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "history", "value" => 0);
	$result = uli_get_results('player_contracts_negotiations', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * liefert eine laufende verhandlung zurueck
 * benoetigt playerID und uliID
 * 18.07.2010
 */
function get_running_negotiation($playerID, $uliID) {
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "history", "value" => 0);
	$result = uli_get_row('player_contracts_negotiations', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * XAJAX Funktion
 * startet die Verhandlungsbox mit einem Spieler
 * 18.07.2010
 *
 */
//function contract_player_x($playerID, $uliID, $form, $action=''){
//	global $option;
//	$objResponse = new xajaxResponse();
//	$cssid = 'negotiation-'.$playerID;
//	$html .= '<div class="ulibox">';
//	$html .= negotiate_player($playerID, $uliID, $form, $action);
//	$html .= '</div>';
//	// Ausgabe der Box
//	$objResponse->assign($cssid, 'innerHTML', $html);
//	return $objResponse;
//}


/**
 *
 * Die grosse Verhandlungsfunktion
 * sowohl fuer verlaengerungen als auch fuer feindliche uebernahmen
 *
 * Erklaerung Status
 * startet bei 10 und zaehlt runter
 * 0 --> gescheitert
 * 20 --> erfolgreich (Das ist neu, das heisst bei erfolgreicher Verhandlung wird der Status auf 20 gesetzt)
 *
 * Was soll hier passieren?
 *
 * 1. Spricht der eigene Klub oder ist das ein feindlicher Kontakt
 * 2. Gibt es eine laufende Verhandlung oder ist das der "Erstkontakt"
 * 3. Wenn NEU und kein Angebot --> Ausgabe eines leeren Formulars
 * 4. Wenn NEU und das Formular wurde abgeschickt --> Berechnung der Gehaltsvorstellung und schreiben der "Verhandlung"
 * 5. Wenn Fortsetzung der Verhandlung, Ausgabe des letzten Standes (in Forderung und Angebot)
 *
 *
 *
 * Rueckgabe eines HTMLs je nach Status
 * 19.07.2010
 */
function negotiate_player($playerID, $uliID, $form, $action=''){
	global $option;
	$error = FALSE;
	$firstcontact = FALSE;
	$hometeam = FALSE;
	$continue = FALSE;
	$sign = FALSE;

	// Ersteinmal werden Infos zum Spieler geholt.
	$player = get_player_infos($playerID, $option['leagueID'], array('contracts'));

	// Es wird geschaut ob es eine laufende Verhandlung gibt (und geholt)
	$negotiation = get_running_negotiation($playerID, $uliID);


	// Wenn FORM und NEGOTIATION leer sind ist das der Erstkontakt
	if (!$form AND !$negoatiation){
		$firstcontact = TRUE;
	}


	// Es wird geschaut ob das der aktuelle Klub ist, der fragt
	if ($uliID == $player['uliID']){
		$hometeam = TRUE;
	}

	// Das ist eine "Fortsetzung" (Ein neuer Besuch beim Spieler mit einer existierenden Verhandlung)
	if (!$form AND $negotiation){
		$continue = TRUE;
	}

	// Hier die Ausgabe eines leeren Formulars
	if ($firstcontact OR $continue){
		// Wenn der Spieler zu unzufrieden ist und das eigene Team fragt
		if ($hometeam AND $player['smile'] < 20){
			$html = generate_negotiation_texts(6);$html .= update_neg_pic(1, $playerID);
		}
		// Man hat mit dem fremden Spieler schon erfolgreich verhandelt. Jetzt kommt die Abloese
		elseif ($negotiation['status'] == 20 AND !$hometeam){
			$html = generate_negotiation_texts(22, $negotiation['salary']);

			if ($negotiation['klubdecision'] == 0){
				$html .= print_formular_takeover($playerID, $uliID, $negotiation);
				$html .= update_neg_pic(20, $playerID);
			}
			// Es wurde schon einmal eine Abloese eingegeben
			else {
				$html .= ' '.generate_negotiation_texts(24, $negotiation['offer']);
				$html .= update_neg_pic(20, $playerID);
			}

		}
		// Es wurde gerade erst eine Verhandlung erfolgreiche beendet oder ist gescheitert
		elseif ($negotiation AND ($negotiation['status'] < 1 OR $negotiation['status'] == 20)){
			if ($negotiation['status'] < 1){$error .= generate_negotiation_texts(8);$error .= update_neg_pic(1, $playerID);}
			if ($negotiation['status'] == 20){$error .= generate_negotiation_texts(7);$error .= update_neg_pic(20, $playerID);}
			$error .= ' '.generate_negotiation_texts(14).' '.uli_date($negotiation['end']);
			$html = $error;
		}
		else {
			if ($negotiation){
				$form = $negotiation;
				// TODO ???
				// Eventuell hier ein anderer Textbaustein
				$html .= generate_negotiation_texts(0).' ';
			}
			else {
				$html .= generate_negotiation_texts(0).' ';
			}
			$html .= print_formular_negotiation($playerID, $uliID, $form);
		}
		// Das ist der allgemeine Rahmen
		if ($action != "zerosalary"){
			$newhtml .= '<div class="neg-pic" id="neg-pic-'.$playerID.'" style="background: url('.get_player_pic_url($playerID).') no-repeat;">';
			$newhtml .= '</div>';
			$newhtml .= '<div class="neg-mainbox">';
			// Headline
			$newhtml .= '<h3>'.NegotiationWith.' '.$player['name'].' </h3>';
			$newhtml .= '<div class="content" id = "negotiation-content-'.$playerID.'">';
			$newhtml .= $html;
			$newhtml .= '</div>';
			$newhtml .= '</div>';
		}
		else {
			$newhtml = $html;
		}
		// In diesem Fall hat die Funktion alles erledigt und es kann das html zurueckgegeben werden
		return $newhtml;
	}

	// Es wurde ein Angebot abgegeben, aber es existiert noch keine Verhandlung in der DB
	if (!$negotiation AND $form){
		$form['claim'] = calculate_salary(NULL, $playerID, $uliID, $option['leagueID'], $form['length']);
		$form['playerID'] = $playerID;
		$form['uliID'] = $uliID;
		// Ende dynamisch zwischen 10 und 20 Tagen
		$form['end'] = time() + rand(864000, 1728000);
		$form['leagueID'] = $option['leagueID'];
		$form['status'] = 10;
		// Hier wird die Basis Verhandlung geschrieben
		$ID = update_negotiation($form);
		$negotiation = $form;
		$negotiation['ID'] = $ID;
	}

	// Der Spieler verhandelt
	if ($negotiation AND $form) {
		$answer = check_offer($form, $negotiation,$player);
		if ($answer['answer'] == "yes"){
			$sign = TRUE;
		}
		// wenn keine Einigung wird das formular zurueckgegeben
		elseif ($answer['answer'] == "no") {
			$form['ID'] = $negotiation['ID'];
			$form['claim'] = $answer['claim'];
			$form['status']= $answer['status'];
			update_negotiation($form);
			$html .= $answer['text'];
			$html .= print_formular_negotiation($playerID, $uliID, $form);
			$html .= update_neg_pic($form['status'], $playerID);
			return $html;
		}
		// Die Verhandlungen sind gescheitert
		elseif ($answer['answer'] == "stop") {
			$form['ID'] = $negotiation['ID'];
			$form['status'] = 0;
			update_negotiation($form);
			$html .= $answer['text'];
			$html .= update_neg_pic($form['status'], $playerID);

			// Ger�cht erzeugen
			create_gossip(2, $playerID, $option['leagueID'], $uliID);
			return $html;
		}
	}

	// Wenn die Funktion hier noch nicht beendet wurde, liegt wohl ein Vertragsabschluss vor :)
	if ($sign){
		// Jetzt unterteilen in fremden Klub und eigenen Klub
		if ($uliID == $player['uliID']){
			$form['ID'] = $negotiation['ID'];
			$form['claim'] = $answer['claim'];
			$form['status']= $answer['status'];
			update_negotiation($form);
			// Beendet den alten Vertrag
			end_contract($playerID, $uliID);
			// Schreibt den neuen Vertrag
			$contract['playerID'] = $negotiation['playerID'];
			$contract['uliID'] = $negotiation['uliID'];
			$contract['length'] = $form['length'];
			$contract['salary'] = $form['salary'];
			$contract['start'] = time();
			$contract['end'] = $contract['length'] * 30 * 24 * 60 * 60 + time();
			$contract['leagueID'] = $option['leagueID'];
			$contract['history'] = 0;
			write_new_contract($contract);
			$html .= $answer['text'];

			// Neuen Marktwert berechnen und schreiben
			$marktwert = get_marktwert(NULL, $player['playerID'], $option['leagueID']);
			$value[] = array("col" => "marktwert", "value" => $marktwert);
			$cond[] = array("col" => "playerID", "value" => $playerID);
			$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
			uli_update_record("player_league", $cond, $value);

			// Die Zeile aktualisieren
			$newHtml .= Salary.': '.uli_money($contract['salary']).' ';
			$newHtml .= '('.until.': '.uli_date($contract['end']).')';
			$newHtml .= '<br/>';
			$newHtml .= Marktwert.': '.uli_money(round($marktwert, -5));

			$html .= update_neg_pic(20, $playerID);

			$html .= '
				<script type="text/javascript">
				$("#kader-player-'.$playerID.' .contract").html("'.$newHtml.'");
				</script>';
			return $html;
		}
		// Wenn es eine feindliche uebernahme ist
		if ($uliID != $player['uliID']){
			create_gossip(3, $playerID, $option['leagueID'], '', $uliID);
			$form['ID'] = $negotiation['ID'];
			$form['claim'] = $answer['claim'];
			$form['status']= $answer['status'];
			update_negotiation($form);
			// Jetzt den marktwert ausrechnen und die verhandlungs box ausgeben
			$html .= $answer['text'];
			$html .= print_formular_takeover($playerID, $uliID, $form);
			$html .= update_neg_pic(20, $playerID);
			return $html;
		}
	}
}

/**
 * Mini Funktion, die ein jquery abschiesst um ein smilie bei verhandlungen zu aendern
 */
function update_neg_pic($faktor, $playerID){
	global $option;
	if ($faktor == 20){$pic = 'vh';}
	if ($faktor < 20){$pic = 'h';}
	if ($faktor < 8){$pic = 'm';}
	if ($faktor < 4){$pic = 's';}
	if ($faktor == 0){$pic = 'stop';}
	$img = '<img src = \"'.$option['uliroot'].'/theme/graphics/icons/smile_big_'.$pic.'.png\">';
	$html .= '
				<script type="text/javascript">
				$("#neg-pic-'.$playerID.'").html("'.$img.'");
				</script>';
	return $html;
}


/**
 * checkt die abloese
 * return $answer
 * $answer['answer'] = 1 // Klubentscheidung
 * $answer['answer'] = 2 // sofortiger verkauf
 *
 */
function check_abloese($playerID, $uliID, $offer, $negotiation){
	global $options;

	$player = get_player_infos($playerID, $options['leagueID'], array('all'));
	// Wir tun so, als ob der SPieler in dem neuen Klub spielt.

	$oldUliID = $player['uliID'];
	$guthaben    = get_value_bank(14, 0, 0, $oldUliID);
	$kredite     = get_all_kredite($oldUliID);
	$vermoegen   = $guthaben - $kredite;


	$player['uliID'] = $uliID;

	// Verhandlung holen
	$player['salary'] = $negotiation['salary'];

	// Virtuellen Marktwert berechnen
	$virtualmarktwert = get_marktwert($player);

	// Was soll alles vom Praesidium gecheckt werden???
	// 1. Mehr als der virtuelle Marktwert?
	// 2. Wirtschaftliche Lage
	// 3. Zufriedenheit des Spielers


	$answer['answer'] = '1';
	$answer['text'] = generate_negotiation_texts(20);

	// Ein Versuch mit Faktoren -- Es gibt 100 Punkte zu verteilen - Ab 50 gibt es die Moeglichkeit, dass das Praesidium dem Wechsel zustimmt

	// Finanzielle Lage
	if ($vermoegen < 20000000){
		if ($vermoegen > 0){
			$faktor = $faktor + 10;
		}
		else {
			$faktor = ($vermoegen * -1 / 1000000) + $faktor;
		}
	}
	else {
		$faktor = $faktor + 0;
	}

	// Zufriedenheit des Spielers
	if ($player['smile'] < 40){
		$faktor = ($player['smile'] - 40) * -1 + $faktor;
	}

	// Angebot in Relation zum virtuellen Marktwert

	$diffprozent = ($offer - $virtualmarktwert) * 100 / $virtualmarktwert;
	if ($diffprozent > -50){
		if ($diffprozent > 80){
			$diffprozent = 80;
		}
		$faktor = $faktor + $diffprozent;
	}
	else {
		$faktor = 0;
	}


	if($player['transferdetails']){
		foreach($player['transferdetails'] as $transfer){
			for ($x = 1; $x <= 1; $x++){$player['lasttransfersum'] = $transfer['sum']; $player['lasttransfertime'] = $transfer['time'];}
		}
	}

	// 6. Wenn Spieler weniger als 3 Monate im Team, dann nur wenn hoeher als Abloese
	if ((time() - $player['lasttransfertime']) < (90*3600*24) AND $offer < $player['lasttransfersum']){
		$faktor = 0;
		$answer['text'] = generate_negotiation_texts(23);
		$answer['answer'] = '1';
	}

	// 5. Status des Spielers
	if ($player['status'] > 0){
		if ($player['status'] == 1){$faktor = $faktor - 20;}
		if ($player['status'] == 2){$faktor = $faktor - 10;}
		if ($player['status'] == 3){$faktor = $faktor + 10;}
		if ($player['status'] == 4){$faktor = $faktor + 20;}
		if ($player['status'] == 5){$faktor = $faktor + 15;}
		if ($player['status'] == 6){$faktor = $faktor - 10;}
		if ($player['status'] == 7){$faktor = $faktor - 15;}
		if ($player['status'] == 8){$faktor = $faktor + 5;}
	}


	// Holt das Team in ein Array
	$uliteam = get_user_team($oldUliID);
	if ($uliteam){
		// zaehlt die Spieler
		$numberofplayers = count($uliteam);
		// Zu viele Spieler im Kader (mehr als 25)
		if ($numberofplayers > 25){$faktor = $faktor + ((25 - $numberofplayers) * - 1);}

		foreach($uliteam as $uliteam){
			// Der ganze Aufwand wird nur betrieben, wenn es nicht derselbe Spieler ist
			if ($uliplayer['ID'] != $player['ID']){
				$uliplayer = get_player_infos($uliteam['playerID'], $uliteam['leagueID'], array('contracts'));// packt die Spieler auf der Hauptposition in ein Array
				// Hauptposition ist gleich
				if ($uliplayer['hp'] == $player['hp']){
					$betterplayers = 0;
					$worseplayers = 0;
					$worseplayersalary = 0;
					// Durchschnittswert der letzten zwei Saisons fuer den positionsgleichen Spieler
					// wird nur gezaehlt, wenn es mindestens 8 Spiele waren
					// Genauso wie weiter oben
					if ($uliplayer['scores'][$option['currentyear']]){
						$games['currentyear'] = count($uliplayer['scores'][$option['currentyear']]) - 1;
					}
					if ($uliplayer['scores'][$option['lastyear']]){
						$games['lastyear'] = count($uliplayer['scores'][$option['lastyear']]) - 1;
					}

					$scores['games'] = $games['currentyear'] + $games['lastyear'];
					$scores['scores'] = $uliplayer['scores'][$option['currentyear']][0] + $uliplayer['scores'][$option['lastyear']][0];
					if ($scores['games'] >= 8){
						$uliplayer['av_score'] = $scores['scores'] / $scores['games'];
					}
					// Es ist jemand besser
					if ($uliplayer['av_score'] > $faktor['av_score']){
						$betterplayers = $betterplayers + 1;
					}
					// Es ist jemand schlechter
					if ($uliplayer['av_score'] < $faktor['av_score']){
						$worseplayers = $worseplayers + 1;
						// Holt das aktuelle Gehalt
					}
				}
			}
		}

		// Mehr als 1 Spieler besser auf der Position
		// Dann kann man den ja leichter verkaufen
		if ($betterplayers > 1){
			$faktor = $faktor + 10;
		}
		else {
			$faktor = $faktor - 10;
		}

		// Sonderfall Es gibt einen Stammkeeper
		// Also jemand der auf der Position bessere Punkte hat
		// Dann noch einmal einen saftigen Aufschlag
		if ($player['hp'] == 1 AND $betterplayers >= 1){
			$faktor = $faktor + 20;
		}
		else {
			$faktor = $faktor - 20;
		}
	}

	//echo $faktor;

	// Wenn das Gebot unter 2 mio ist, dann niemals eine feindliche Uebernahme - zu anfaellig fuer Zufall
	if ($faktor > 50 AND $offer < 2000000){
		$faktor = 50;
	}

	// Die Knut Bremse
	if ($faktor > 50 AND $uliID == 101){
		$faktor = 50;
	}

	// wenn kapitaen
	if ($faktor > 50 AND $player['captain'] == 1){
		$faktor = 50;
	}
	// wenn geschuetzt
	if ($faktor > 50 AND $player['save'] == 1){
		$faktor = 50;
	}


	// checken, ob der typ der kapitaen ist.



	// Jetzt wird mit dem Faktor gespielt
	// Nur wenn der Faktor ueber 50 ist, besteht die Moeglichkeit des sofortigen Verkaufs
	if ($faktor > 50){
		$wahrscheinlichkeit = $faktor - 50;
		$zufall = rand(1,50);
		if ($zufall < $wahrscheinlichkeit){
			//echo 'GO';
			$answer['answer'] = '2';
			$answer['text'] = generate_negotiation_texts(21);

		}
		else {
			//echo 'NO';
			$answer['answer'] = '1';
		}
	}

	$answer['faktor'] = $faktor;
	return $answer;
}



/**
 * ueberprueft ein angebot
 * Achtung. Hier muss auf den Wechsel der Vertragslaufzeiten reagiert werden.
 * diverse Checks
 * 18.07.2010
 */
function check_offer($offer, $negotiation, $player) {
	global $option;
	$claim = $negotiation['claim'];
	$status = $negotiation ['status'];

	// Wenn sich die angebotene Laenge des Vertrags aendert
	if ($offer['length'] != $negotiation['length']) {
		if ($offer['length'] == 3)
		{
			if ($negotiation['length'] == 6){$claim = $claim * 1.1;}
			if ($negotiation['length'] == 12){$claim = $claim * 1.2;}
			if ($negotiation['length'] == 24){$claim = $claim * 1.3;}
		}
		if ($offer['length'] == 6)
		{
			if ($negotiation['length'] == 3){$claim = $claim * 0.9;}
			if ($negotiation['length'] == 12){$claim = $claim * 1.1;}
			if ($negotiation['length'] == 24){$claim = $claim * 1.2;}
		}
		if ($offer['length'] == 12)
		{
			if ($negotiation['length'] == 3){$claim = $claim * 0.8;}
			if ($negotiation['length'] == 6){$claim = $claim * 0.9;}
			if ($negotiation['length'] == 24){$claim = $claim * 1.1;}
		}
		if ($offer['length'] == 24)
		{
			if ($negotiation['length'] == 3){$claim = $claim * 0.7;}
			if ($negotiation['length'] == 6){$claim = $claim * 0.8;}
			if ($negotiation['length'] == 12){$claim = $claim * 0.9;}
		}
	}

	$dontnegotiate = FALSE;

	$end_new_contract = $offer['length'] * 30 * 24 * 60 * 60 + time();

	// noch mindestens ein jahr vertrag dann nur bei gro�er gehaltserh�hung
	if ($player['contractend'] > (time() + 365 * 24 * 60 * 60) AND $offer['salary'] < ($player['salary'] * 1.5)) {
		$answer['text'] = generate_negotiation_texts(9);
		$status = $status - 3;
		$answer['answer'] = 'no';
		$dontnegotiate = TRUE;
	}
	// noch mindestens 6 monate vertrag dann keine gehaltsk�rzung
	elseif ($player['contractend'] > (time() + 6 * 30 * 24 * 60 * 60) AND $offer['salary'] < $player['salary']) {
		$answer['text'] = generate_negotiation_texts(10);
		$status = $status - 3;
		$answer['answer'] = 'no';
		$dontnegotiate = TRUE;
	}
	// der neue vertrag ist k�rzer
	elseif ($end_new_contract < $player['contractend']) {
		$answer['text'] = generate_negotiation_texts(13);
		$status = $status - 3;
		$answer['answer'] = 'no';
		$dontnegotiate = TRUE;
	}

	// Berechnug ob Angebot angenommen wird.
	// Zufriedenheit des Spielers --> entgegenkommen
	// Loyalitaet --> entgegenkommen
	if (!$dontnegotiate){
		$smilefaktor = $player['smile'] / 100;
		$loyaltyfaktor = $player['loyalty'] / 2;
		$statusfaktor = $status / 10;
		$zufall = rand(1,10) / 5;
		$faktor = $smilefaktor + $loyaltyfaktor + $statusfaktor + $zufall;
		$claim = $claim - ($faktor * $claim / 100);  // neuer wunsch des spielers

		if ($offer['salary'] > $claim)
		{
			if ($player['uliID'] == $option['uliID']){
				$answer['text'] = generate_negotiation_texts(11);
			}
			if ($player['uliID'] != $option['uliID']){
				$answer['text'] = generate_negotiation_texts(15);
			}
			$answer['status'] = '20';
			$answer['answer'] = 'yes';
			$answer['claim'] = $claim;
			return $answer;
		}
		else {
			if ($offer['salary'] <= ($claim - $claim * 0.5))
			{
				$claimtext = $claim / 5000;
				settype($claimtext, INT);
				$claimtext = ($claimtext + 3) * 5000;
				settype($claimtext, INT);
				$claimtext = uli_money($claimtext);

				$answer['text'] = generate_negotiation_texts(1, $claimtext);
				$status = $status -3;
				$answer['answer'] = 'no';
			}
			elseif ($offer['salary'] <= ($claim - $claim * 0.4))
			{
				$claimtext = $claim / 5000;
				settype($claimtext, INT);
				$claimtext = ($claimtext + 2) * 5000;
				settype($claimtext, INT);
				$claimtext = uli_money($claimtext);

				$answer['text'] = generate_negotiation_texts(2, $claimtext);
				$status = $status -2;
				$answer['answer'] = 'no';
			}
			elseif ($offer['salary'] <= ($claim - $claim * 0.2))
			{
				$claimtext = $claim / 5000;
				settype($claimtext, INT);
				$claimtext = ($claimtext + 1) * 5000;
				settype($claimtext, INT);
				$claimtext = uli_money($claimtext);
				$answer['text'] = generate_negotiation_texts(3, $claimtext);
				$status = $status -1;
				$answer['answer'] = 'no';
			}
			elseif ($offer['salary'] <= ($claim))
			{
				$answer['text'] = generate_negotiation_texts(4);
				$status = $status -1;
				$answer['answer'] = 'no';
			}
		}
	}

	$abort = 0;
	// Ab einem Status von 5 steigt die Wahrscheinlichkeit das der Spieler das Ding beendet
	if ($status <=5 AND $offer['salary'] < $claim)
	{
		$abort = rand(1, $status);
	}
	if ($status < 1){$abort = 1;}

	// Die Verhandlungen sind gescheitert
	if ($abort == 1){
		$answer['text'] = generate_negotiation_texts(5);
		$status = '0';
		$answer['answer'] = 'stop';
		unset($smile);
		// Die zufriedenheit sinkt in bestimmten groessen ...
		if ($claim > (2*$offer['salary'])){$smile = rand(-10, -30);}
		elseif ($claim > $offer['salary']){$smile = rand(-5, -15);}
		update_smile($player['playerID'], $option['leagueID'], $smile, NULL, NULL, $option['currentyear']);
	}


	$answer['status'] = $status;
	$answer['claim'] = $claim;
	return $answer;
}
/////////////////////////////////////////

/**
 * gibt ein html fuer das Bild zum Verhandlungsstand zurueck
 * @param $status
 * @return html
 */
function get_smile_pic_negotiation($status, $special = ''){
	global $option;
	$html .= '<img src="'.$option['uliroot'].'/theme/graphics/icons/smile_big_';
	if ($status == 10){$html .= 'vh';}
	elseif ($status >7){$html .= 'h';}
	elseif ($status >4){$html .= 'm';}
	elseif ($status >0){$html .= 's';}
	elseif ($status <1){$html .= 'stop';}
	$html .= '.png">';
	return $html;
}


/**
 * Hier werden Textbausteine fuer die Verhandlungen zusammengestellt.
 * 0 - Intro
 * 1 - Noch weit auseinander
 * 2 - ein bisschen besser
 * 3 - wir naehern uns
 * 4 - Nicht mehr viel
 * 5 - Abbruch
 * 6 - Ich bin traurig und will nicht sprechen
 * 7 - Wir haben uns doch gerade erst geeinigt
 * 8 - Die Verhandlungen sind doch gerade gescheitert
 * 9 - Der Vertrag ist sehr lang, da will ich deutlich mehr Geld
 * 10 - Der Vertrag ist lang. Ich will keine Kuerzungen
 * 11 - Unterschrieben
 * 12 - Weiterverhandeln
 * 13 - Der neue Vertrag w�re k�rzer
 */
function generate_negotiation_texts($type, $claimtext = ''){

	$text[0][1] = 'Grunds&auml;tzlich bin ich an einer Verbesserung meiner Vertragssituation interessiert.';
	$text[0][2] = 'Ich bin gespannt auf Dein Angebot';
	$text[0][3] = 'Ich habe mal meinen Berater mitgebracht. Was gibt es denn?';

	$text[1][1] = 'So brauchen wir gar nicht anfangen.';
	$text[1][2] = 'Da stelle ich mir aber deutlich mehr vor.';
	$text[1][3] = 'Das ist ja wohl indiskutabel.';

	$text[2][1] = 'Na komm, Du verdienst so viel mit meinen Trikots, da kannst Du noch was raufpacken.';
	$text[2][2] = 'Mein Berater sagt, ich bin deutlich mehr wert.';
	$text[2][3] = 'Ich will noch mehr, denk mal daran, wie hoch heutzutage die Fixkosten sind.';

	$text[3][1] = 'Ich denke, das ist eine gute Gespr&auml;chsgrundlage.';
	$text[3][2] = 'Schon mal ein guter Anfang. Bestellst Du mir einen Kaffee, dann reden wir weiter.';
	$text[3][3] = 'Nicht schlecht. Aber auch noch nicht gut.';

	$text[4][1] = 'Wir sind nicht mehr weit auseinander. Geht mal schonmal einen F&uuml;ller holen und pack noch ein bisschen drauf.';
	$text[4][2] = 'Ich sehe, wir sch&auml;tzen meine Leistungen gleich ein. Ein bisschen mehr bitte noch.';
	$text[4][3] = 'Denke daran, dass ich drei Kinder habe. Packst Du noch einen Familienzuschlag drauf, dann sind wir uns einig.';

	global $option;
	$ulinames = get_all_uli_names($option['leagueID']);
	$x = 0;
	foreach ($ulinames as $key => $name){
		if ($key != $option['uliID']){
			$x = $x + 1;
			$names[$x] = $name;
		}
	}

	$text[5][1] = 'Jetzt ist aber Schluss. So kommen wir nicht zusammen. Die Jungs von '.$names[rand(1,$x)].' waren da deutlich netter.';
	$text[5][2] = 'Wei&szlig;t Du, ich glaube, das bringt erstmal nix.';
	$text[5][3] = 'Ichhabkeinelust. Diese Verhandlungen sind gescheitert.';

	$text[6][1] = 'Ich bin sehr unzufrieden hier. Deswegen m&ouml;chte ich nicht verl&auml;ngern.';
	$text[6][2] = 'Ich bin mit der Gesamtsituation unzufrieden und denke nicht, dass ich verl&auml;ngern m&ouml;chte.';
	$text[6][3] = 'Die andern sind doof. Ich will hier weg.';

	$text[7][1] = 'Die Tinte ist doch gerade erst getrocknet.';
	$text[7][2] = 'Bist Du verhandlungsgeil? Wir haben uns doch gerade erst geeinigt.';
	$text[7][3] = 'Lass mal. Ich bin mit meinem neuen Vertrag noch sehr zufrieden.';

	$text[8][1] = 'Ich bin noch sauer. Raus!';
	$text[8][2] = 'Leck mich. ';
	$text[8][3] = 'Die Verhandlungen sind doch gerade erst gescheitert, du Affe.';

	$text[9][1] = 'Ich habe doch noch so lange Vertrag. Da lasse ich nur bei deutlich mehr Geld mit mir reden.';
	$text[9][2] = 'Junge, ich hab hier noch ewig Vertrag. Ich lass mir doch nicht die Butter vom Brot nehmen.';
	$text[9][3] = 'Pffff. Ich krieg mein Gehalt auch auf der Trib&uuml;ne.';

	$text[10][1] = 'Gehalt k&uuml;rzen? Ich ruf gleich Verdi an. Ich hab noch so lange Vertrach.';
	$text[10][2] = 'Du denkst wohl, Du bist ganz clever. Mein Vertrag geht noch ne Weile, da lass ich mich nicht auf weniger Kohle ein.';
	$text[10][3] = 'Versprochen ist versprochen. Mein Vertrag l&auml;uft noch ein bisschen. Biete mir doch eine Gehaltserh&ouml;hung.';

	$text[11][1] = 'Dieser Verein war immer mein ein und alles. Ich bin sehr froh, dass wir uns einigen konnten.';
	$text[11][2] = 'Ich bin so froh, weiter in diesem tollem Verein Leistung bringen zu d&uuml;rfen.';
	$text[11][3] = 'Lass dich dr&uuml;cken. Wir werden immer gute Freunde sein.';

	$text[12][1] = 'Sch&ouml;n, dass Du mal wieder vorbei gekommen bist. Dann lass uns mal weiterverhandeln.';
	$text[12][2] = 'Mmmmh. Du hast Kuchen mitgebracht. Dann lass uns weitermachen.';
	$text[12][3] = 'Wo waren wir stehen geblieben? Ok. Na dann mal Butter bei die Fische.';

	$text[13][1] = 'Der neue Vertrag w&auml;re k&uuml;rzer als der alte. Papa sagt, bei so was muss man immer aufpassen.';
	$text[13][2] = 'Ich bin doch nicht dusselig. Der neue Vertrag w&auml;re k&uuml;rzer als der alte.';
	$text[13][3] = 'Verkauf mich doch, wenn Du mich nicht willst. Eine k&uuml;rzere Vertragslaufzeit ist sehr unsportlich.';

	$text[14][1] = 'Komm doch sp&auml;ter wieder vorbei. Vielleicht ab ';
	$text[14][2] = 'Versuchs doch wieder ab ';
	$text[14][3] = 'Ich bin nicht zu sprechen bis zum ';

	$text[15][1] = 'Wir w&auml;ren uns schon einmal einig. Aber f&uuml;r umsonst werden die mich nicht gehen lassen.';
	$text[15][2] = 'Ich w&uuml;rde ja sofort die Koffer packen. Mach das mal mit der Abl&ouml;se klar.';
	$text[15][3] = 'Ich freue mich auf die neue Herausforderung. Du wirst ja nicht knauserig sein, bei der Abl&ouml;se, oder?';


	$text[20][1] = 'Das Pr&auml;sidium vertraut da auf die Entscheidung des Managers';
	$text[20][2] = 'Dein Gebot ist angekommen. Wir werden uns bei Dir melden.';
	$text[20][3] = 'Wir werden das sorgf&auml;ltig abw&auml;gen.';

	$text[21][1] = 'Der Verein ist zu dem Schluss gekommen, dass dieser Transfer sehr sinnvoll ist. Der Spieler wird Dir sofort verkauft. ';
	$text[21][2] = 'Es war uns eine Ehre mit Dir Gesch&auml;fte machen zu d&uuml;rfen. Der Spieler ist unterwegs.';
	$text[21][3] = 'Der T&uuml;p sitzt im Zug, die Kohle haben wir mal per Lastschriftverfahren eingezogen.';

	$text[22][1] = 'Mit dem Spieler bist Du Dir schon einig. Du hattest ihm '.uli_money($claimtext).' Gehalt geboten.';
	$text[22][2] = 'Mit dem Spieler bist Du Dir schon einig. Du hattest ihm '.uli_money($claimtext).' Gehalt geboten.';
	$text[22][3] = 'Mit dem Spieler bist Du Dir schon einig. Du hattest ihm '.uli_money($claimtext).' Gehalt geboten.';

	$text[23][1] = 'Der Typ ist doch noch keine 3 Monate hier, da verkaufen wir den doch nicht f&uuml;r weniger.';
	$text[23][2] = 'Der Typ ist doch noch keine 3 Monate hier, da verkaufen wir den doch nicht f&uuml;r weniger.';
	$text[23][3] = 'Der Typ ist doch noch keine 3 Monate hier, da verkaufen wir den doch nicht f&uuml;r weniger.';

	$text[24][1] = 'Dein Angebot &uuml;ber '.uli_money($claimtext).' wird gepr&uuml;ft.';
	$text[24][2] = 'Dein Angebot &uuml;ber '.uli_money($claimtext).' wird gepr&uuml;ft.';
	$text[24][3] = 'Dein Angebot &uuml;ber '.uli_money($claimtext).' wird gepr&uuml;ft.';


	$claim[1] = 'Sch&ouml;n w&auml;ren '.$claimtext;
	$claim[2] = 'So mit '.$claimtext.' k&ouml;nnte ich mich anfreunden.';
	$claim[3] = 'Ich stelle mir so '.$claimtext.' vor.';
	$claim[4] = 'Ich denke, ich bin '.$claimtext.' wert.';
	$claim[5] = 'Jetzt mal unter uns. '.$claimtext.' sind doch fair.';
	$claim[6] = 'Ein paar Euro mehr f&uuml;r meine Familie noch.';
	$claim[7] = 'Noch ein bisschen mehr, bitte.';
	$claim[8] = 'Ich denke, jetzt sind wir auf einem sehr gutem Weg.';
	$claim[9] = 'Noch ein paar Euro mehr und Du kannst schon mal den F&uuml;ller suchen.';
	$claim[10] = 'Ein paaaaar Taler mehr f&uuml;rs Gl&uuml;ck.';

	$html .= $text[$type][rand(1,3)];


	if ($type > 0 AND $type < 4){
		$html .= $claim[rand(1,5)];
	}

	return $html;
}


/**
 * gibt das formular fuer die eingabe der abloese bei einer feindlichen uebergabe aus
 * 29.07.2010
 */
function print_formular_takeover($playerID, $uliID, $offer) {
	$html .= '<div class="negotiation-form">';
	$html .= '<form class="takeoverform" id="'.$playerID.'" method ="POST" onsubmit="return false">';
	$html .= ' <input class="formauction sumInput"  data-a-dec="," data-a-sep="." data-m-dec="0"  type = "text" id = "offer-'.$playerID.'" name = "offer" size = "10" maxlength = "10" value="'.$offer['offer'].'"> '.OfferAbloese;
	$html .= ' <input class="formauction" type = "submit" value = "'.ThisIsMyOfferAbloese.'">';
	$html .= '</form>';
	$html .= '</div>';
	return $html;
}



/**
 * gibt das formular zur verhandlung aus
 * 18.07.2010
 * geht fuer alle arten von verhandlungen (auch uebernahmen)
 * braucht das formualr
 */
function print_formular_negotiation($playerID, $uliID, $offer) {
	$html .= '<div class="negotiation-form">';
	$html .= '<form class="negotiationform" id="'.$playerID.'" method ="POST" onsubmit="return false">';
	$html .=  '<select name = "length" id = "length-'.$playerID.'">';
	$html .=  '<option value = "3" ';
	if ($offer['length'] == 3){$html .=  'selected';}
	$html .=  '>3 Monate</option>';
	$html .=  '<option value = "6" ';
	if ($offer['length'] == 6){$html .=  'selected';}
	$html .=  '>6 Monate</option>';
	$html .=  '<option value = "12" ';
	if ($offer['length'] == 12){$html .=  'selected';}
	$html .=  '>1 Jahr</option>';
	$html .=  '<option value = "24" ';
	if ($offer['length'] == 24){$html .=  'selected';}
	$html .=  '>2 Jahre</option>';
	$html .= '</select>';
	$html .= ' <input class="formauction sumInput"  data-a-dec="," data-a-sep="." data-m-dec="0"  id = "salary-'.$playerID.'" type = "text" name = "salary" size = "10" maxlength = "10" value="'.$offer['salary'].'"> '.SalaryPerDay;
	$html .= ' <input class="formauction" type = "submit" value = "'.ThisIsMyOffer.'">';
	$html .= '</form>';
	$html .= '</div>';
	return $html;
}




/**
 * gibt die Suchbox aus
 *
 * Positionen
 * UliTeams
 * Alter
 * Zufriedenheit
 * Name
 */
function print_search_box() {
	global $option;

	$html .= '<form id="searchbox" method ="POST" onsubmit="return false">';

	$html .= Positions.': <br/>';
	$html .= "\n";
	$html .= "\n";

	for ($x=1; $x<=7; $x++){
		$html .= '<input type = "checkbox" id = "pos-'.$x.'">';
		$html .= ' '.$option['position'.$x.'-2'];
		$html .= "\n";
		if ($x==4){$html .= '<br/>';}
	}
	// Alter
	$html .= '<br/>';
	$html .= age.':<br/>';
	$html .= "\n";
	$html .= "\n";
	$html .= "\n";
	for ($x=1; $x<=4; $x++){
		$html .= '<input type = "checkbox" checked = "checked" id = "age-'.$x.'">';
		$html .= ' '.$option['age'.$x.'-2'];
		$html .= '<br/>';
		$html .= "\n";
	}
	// Uli Teams
	$html .= '<br/>';
	$html .= UliTeams.':<br/>';
	$html .= "\n";

	$html .= '<select class="filterform selectfilter" id ="uliteamsearch">';
	$html .= "\n";
	$html .= '<option value="all">'.AllTeams.'</option>';
	$html .= "\n";
	// Es werden alle Ulinamen eingelesen
	$uliname = get_all_uli_names($option['leagueID']);
	if ($uliname){
		foreach ($uliname as $key => $uliname){
			$html .= '<option value="'.$key.'">'.$uliname.'</option>';
			$html .= "\n";
		}
	}
	$html .= '</select>';
	$html .= "\n";
	$html .= '</form>';

	$html .= '<br/>';
	$html .= Name.':<br/>';
	$html .= "\n";
	$html .= '<form method = "POST" action = "?action=search">';
	// Name
	// live Search

	$html .= '<input id = "livesearch" name = "name" type = "text">';
	$html .= '<input value = "Such!" type = "submit">';


	$html .= '</form>';

	// Zufriedenheit (nur unzufriedene) SP�TER TODO
	return $html;
}

/**
 * Holt nach uebergebenen Kriterien die Spieler
 *
 */
function execute_search($posFilter, $ageFilter, $uliID, $name = ''){
	global $wpdb, $option;
	$leagueID = $option['leagueID'];
	$conditionArray = array();
	$tableQuery = 'SELECT * FROM '.$option['prefix'].'uli_player p ';
	$conditionArray[] = ' p.team != 999';
	$conditionArray[] = ' pa.leagueID = '.$leagueID;



	$joinString .= ' LEFT JOIN '.$option['prefix'].'uli_player_league pa ON pa.playerID = p.ID';

	if ($posFilter){
		$conditionArray[] = '(p.hp IN ('.implode(',', $posFilter).') OR p.np1 IN ('.implode(',', $posFilter).') OR p.np2 IN ('.implode(',', $posFilter).'))';
	}

	if ($ageFilter) {
		$timestamp = time();
		/* Raender der Alterklassen berechnen */
		/* Beispiel 20-25 Jahre: Geburtstag < heute - 20 Jahre und Geburtstag > heute - 25 Jahre */
		$Twentyyears = $timestamp - (20* 365.25 * 24 * 3600);
		$Twentyfiveyears = $timestamp - (25* 365.25 * 24 * 3600);
		$Thirtyyears = $timestamp - (30* 365.25 * 24 * 3600);

		if(in_array(1, $ageFilter)){$ageArray[] = '(p.birthday > '.$Twentyyears.') ';}
		if(in_array(2, $ageFilter)){$ageArray[] = '(p.birthday < '.$Twentyyears.' AND p.birthday > '.$Twentyfiveyears.') ';}
		if(in_array(3, $ageFilter)){$ageArray[] = '(p.birthday < '.$Twentyfiveyears.' AND p.birthday > '.$Thirtyyears.') ';}
		if(in_array(4, $ageFilter)){$ageArray[] = '(p.birthday < '.$Thirtyyears.') ';}
		if ($ageArray){$conditionArray[] = ' ('.implode(' OR ', $ageArray).') ';}
		else {$conditionArray[] = ' p.birthday = 0 ';}
	}

	// Dann eine ganz andere Query - dann ist Userteam im Fokus. das geht viel schneller
	if ($uliID AND $uliID != "all"){
		$conditionArray[] = ' pa.uliID = '.$uliID.' ';
	}

	// hier nur die Namenssuche
	if ($name){
		unset($conditionArray);
		$conditionArray[] = ' p.team != 999';
		$conditionArray[] = ' pa.leagueID = '.$leagueID;
		$conditionArray[] = ' p.name LIKE "%'.$name.'%"';

	}

	$conditionquery = " WHERE ".implode(" AND ", $conditionArray);
	$sql = $tableQuery.$joinString.$conditionquery.$orderquery;
	$result = $wpdb->get_results($sql, ARRAY_A);
	if ($result){return $result;}
	else {return FALSE;}



	return $result;
}

/**
 * gibt ein suchergebnis aus
 *
 * @param $result
 * @return unknown_type
 */
function print_search_result($result){
	if (!$result){$html = SomeCriteriasPlease; return $html;}

	// Es werden alle Ulinamen eingelesen
	$uliname = get_all_uli_names($option['leagueID']);
	// Es werden alle Bundesligateamnamen eingelesen
	$ligateam = get_all_team_names();

	foreach ($result as $player){
		/* Ausgabe des Spielers */
		$html .= '<div class="kader-player" id="search-player-'.$player['playerID'].'">';
		$html .= print_player_search($player['playerID'], $uliname, $ligateam);
		$html .= '</div>';
		$html .= "\n\n";
	}

	return $html;
}


/**
 * gibt den spieler aus (nach einer suche)
 * @param $player
 * @param $uliname
 * @param $ligateam
 * @return unknown_type
 */
function print_player_search($playerID, $uliname, $ligateam){
	global $option;

	$player = get_player_infos($playerID, $option['leagueID'], array('contracts'));

	// Schaut, ob der Spieler auf der Transferliste steht
	$auction = get_auction_player($player['playerID'], $option['leagueID']);
	/* Wappenbild des Bundesligateams */
	$html .= "\n";
	$html .= '<div class="player">';
	/* Trikotnummer */
	if ($player['jerseynumber'] == 0){$player['jerseynumber'] = "x";}
	$html .= '<div class="jerseynumber"><b>'.$player['jerseynumber'].'</b></div>';
	$html .= "\n";

	$html .= '<b><a class="playerinfo" id = "'.$player['playerID'].'" href="#">'.$player['name'].'</a></b> ';
	/* Alter */
	$html .= '('.player_age($player['birthday']).') ';

	if ($player['injury']){
		$html .= ' '.get_injury_pic($player['injury_cause'].' (letztes Update: '.uli_date($player['injury_update']).')').' ';
	}
	/* Div Marker fuer die Spielerinfo */
	$html .= '<div class="marker" id="player-'.$player['playerID'].'"></div>';

	/* Positionen und Fu� */

	$html .= '<br/>';
	if ($player['smile'] < 35){
		$html .= get_smile_icon($player['smile']).' ';
	}
	$html .= get_ligateam_wappen($player['team'], $ligateam).' ';
	if ($player['star'] > 0){
		$html .= get_star_icon($player['star']).' ';
	}

	$html .= $option['position'.$player['hp'].'-2'].' ';
	if ($player['np1']){$html .= $option['position'.$player['np1'].'-2'].' ';}
	if ($player['np2']){$html .= $option['position'.$player['np2'].'-2'].' ';}
	$html .= '('.$option['foot'.$player['foot'].'-2'].')';
	$html .= '</div>';

	/* Zweite Spalte */
	$html .= '<div class="contract">';
	$html .= "\n";

	if ($player['uliID']){
		$html .= $uliname[$player['uliID']];
		$html .= '<br/>';
		$html .= Marktwert.': '.uli_money(round($player['marktwert'], -5));
	}
	else {
		$html .= NoJob;
	}
	if ($auction){
		$html .= '<span class="attention">';
		$html .= '<br/>'.ThereIsAnAuction;
		if ($auction['claim'] > 0){
			' '.Claim.' '.uli_money($auction['claim']);
		}
		if ($auction['end'] > 0){
			$html .= '<br/>';
			$html .= ' '.AuctionEndsOn.' '.uli_date($auction['end']);
		}
		$html .= '</span>';
	}


	$html .= '</div>';
	$html .= "\n";

	/* Dritte Spalte */
	$html .= '<div class="actions">';
	$html .= "\n";

	/* Verhandeln */
	if (!$auction['end'] OR $auction['end'] == 0){
		if ($player['uliID'] != $option['uliID'] AND $player['uliID']){
			// Sommerpause
			$html .= '<a href="#" class="contractplayer" id = "'.$player['playerID'].'">'.TakeOver.'</a> ';
		}
	}


	$html .= '</div>';
	$html .= "\n";
	// Marker fuer die Verhandlungen. Diese werden dynamisch angesprochen
	$html .= '<div class="negotiation" id="negotiation-'.$player['playerID'].'" style="margin-left: 5px">';
	$html .= '</div>';
	$html .= "\n";


	return $html;

}

/*
 * akzeptiert ein Angebot und tradet einen Spieler
 */
function accept_offer($ID, $uliID){
	global $option;
	// Negotiation holen
	$negotiation = get_negotiation($ID);
	$contract = $negotiation;
	// Der Spieler wird verkauft
	trade_player($contract['playerID'], $option['leagueID'], NULL, $contract);
	$html .= PlayerSold;
	return $html;
}

/**
 * Lehnt ein Angebot ab (nach ID)
 *
 */
function reject_offer($ID, $uliID = ''){
	global $option;
	// Negotiation holen
	$negotiation = get_negotiation($ID);
	// Wenn der Faktor ueber 25 ist, dann will der Spieler mehr Geld ... Das wird durch Status 4 gekennzeichnet
	if ($negotiation['faktor'] > 25){
		$form['klubdecision'] = 4;
		$form['faktor'] = 0;
	}
	// Sonst einfach nur den Status auf 3 setzen
	else {
		$form['klubdecision'] = 3;
		$form['faktor'] = 0;
		// Die Endzeit wird hochgesetzt
		$form['end'] = $negotiation['end'] + (3600*24*7);
	}
	$form['ID'] = $ID;
	update_negotiation($form);
	$html = OfferRejected;
	return $html;
}

/**
 *
 * Gehalt erhoehen
 */
function raise_salary($playerID, $uliID, $salary){
	global $option;

	// Vertrag holen
	$contract = get_contract($playerID, $uliID);

	$newcontract = $contract;
	$newcontract['salary'] = $salary;
	$newcontract['start'] = time();
	$newcontract['history'] = 0;
	unset($newcontract['ID']);

	// Alten Vertrag beenden
	end_contract($playerID, $uliID);

	// Neuen Vertrag (mit Daten des alten plus Neues Gehalt) schreiben
	write_new_contract($newcontract);

	// Jetzt alle 4er Negotiations archivieren
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$cond[] = array("col" => "klubdecision", "value" => 4);
	$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
	$cond[] = array("col" => "history", "value" => 0);
	$value[] = array("col" => "history", "value" => 1);
	uli_update_record("player_contracts_negotiations", $cond, $value);


	// Spieler wird gluecklicher
	update_smile($playerID, $option['leagueID'], $diff = rand(8,12), NULL, NULL, $option['currentyear']);


	return $html;
}





?>
