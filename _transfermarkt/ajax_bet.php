<?php

// fuer die ganzen ajax files muss irgendeine routine gebaut werden, die die ganzen libs einliest.
// diese variante ist etwas ruppig, weil bei jedem kleinen request die ganze routine (checks, etc.) durchlaufen wird.

require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');
include('lib_transfermarkt.php');

global $option;


if ($_POST['action'] == "saveplayer"){
	$uliID = $option['uliID'];
	$year = $option['currentyear'];
	$playerID = $_POST['playerID'];
	
	// alle spieler auf 0 setzen
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$value[] = array("col" => "save", "value" => 0);
	uli_update_record("player_league", $cond, $value);	

	// den neuen Spieler setzen
	unset($value);
	$cond[] = array("col" => "playerID", "value" => $playerID);
	$value[] = array("col" => "save", "value" => 1);
	uli_update_record("player_league", $cond, $value);	
	

		$html .= '<script>';
		$html .= '$(".playerissave").html("Spieler sch&uuml;tzen");';
		$html .= '$(".playerissave").removeClass("playerissave").addClass("saveplayer");';
		$html .= '$("#kader-player-'.$playerID.' .actions div").removeClass();';
		$html .= '$("#kader-player-'.$playerID.' .actions div").addClass("playerissave");';
		$html .= '$("#kader-player-'.$playerID.' .actions div").html("Spieler ist gesch&uuml;tzt");';
		$html .= '</script>';
		echo $html;
}




// Angebot akzeptieren
if ($_POST['action'] == "acceptoffer"){
	$negotiationID = $_POST['negotiationid'];
	echo $html = accept_offer($negotiationID, $option['uliID']);
}

// Angebot ablehnen
if ($_POST['action'] == "rejectoffer"){
	$negotiationID = $_POST['negotiationid'];
	$html = reject_offer($negotiationID, $option['uliID']);
	echo $html;
}

// Gehalt erhoehen
if ($_POST['action'] == "raisesalary"){
	$playerID = $_POST['playerID'];
	$salary = $_POST['salary'];
	$html = raise_salary($playerID, $option['uliID'], $salary);
	echo $html;
}

// feindliche uebernahme
if ($_POST['action'] == "takeover"){

	// Jetzt werden hier noch einmal die ganzen lustigen Dinge eingebunden ???
	// Performance kann sicher auch verbessert werden
	$allbets     = get_sum_ulibets($option['uliID']);
	$guthaben    = get_value_bank(14, 0, 0, $option['uliID']);
	$kredite     = get_all_kredite($option['uliID']);
	$kreditrahmen = get_kreditrahmen($option['uliID']);
	$vermoegen   = $guthaben + $kreditrahmen - $allbets - $kredite;


	$offer = $_POST['offer'];
	$uliID = $_POST['uliID'];
	$playerID = $_POST['playerID'];
	$negotiation = get_running_negotiation($playerID, $uliID);

	settype($offer, INT);

	// Erst einmal der Finanzielle Check, vorher wird das Gebot ueberhaupt nicht angenommen
	$error = check_offer_takeover($offer, $uliID);

	if ($error){
		$answer['text'] = $error;
	}

	if (!$error){
		// Abloese checken
		$answer = check_abloese($playerID, $uliID, $offer, $negotiation);
		// Verhandlung aktualisieren (Gebot und Antwort)
		$form['ID'] = $negotiation['ID'];
		$form['offer'] = $offer;
		$form['faktor'] = $answer['faktor'];
		$form['klubdecision']= $answer['answer'];
		update_negotiation($form);

		// TODO noch den Namen des Spielers und einen Schoenen Textbaustein
		$player = get_player_infos($playerID, $options['leagueID'], array('all'));

		// Falls positiv --> Transfer vollziehen
		if ($answer['answer'] == 2){
			$contract = $negotiation;
			$contract['takeover'] = 1;
			$contract['offer'] = $offer;
			trade_player($playerID, $option['leagueID'], $auction = NULL, $contract);
			$message['sender'] = 0;
			$message['receiver'] = $player['uliID'];
			$message['subject'] = Transfer;
			$message['text'] = OnePlayerWasTransferd.' '.$player['name'];
			$message['time'] = time();
			write_message($message);
		}
		// Falls negativ --> Manager benachrichtigen
		if ($answer['answer'] == 1){
			$message['sender'] = 0;
			$message['receiver'] = $player['uliID'];
			$message['subject'] = NewBet;
			$message['text'] = 'Jemand hat '.uli_money($offer).' auf '.$player['name'].' geboten.';
			$message['time'] = time();
			write_message($message);
		}
	}

	// Antwort ausgeben
	echo $answer['text'];
}


// Suche ausfuehren
if ($_POST['action'] == "executesearch"){
	$pos = $_POST['pos'];
	$age = $_POST['age'];
	$posArray = explode(",", $pos);
	unset($posArray[0]);
	$ageArray = explode(",", $age);
	unset($ageArray[0]);
	$uliID = $_POST['uliID'];
	$result = execute_search($posArray, $ageArray, $uliID);
	$html = print_search_result($result);
	echo $html;
}


// Menu zum Wechseln der Nummer ausgeben
if ($_POST['action'] == "changejerseynumber"){
	$playerID = $_POST['playerID'];
	$uliID = $option['uliID'];
	$newNumber = $_POST['newNumber'];
	$html .= change_jersey_number($playerID, $uliID, $newNumber);
	echo $html;
}



// Menu zum Wechseln der Nummer ausgeben
if ($_POST['action'] == "jerseynumber"){
	$playerID = $_POST['playerID'];
	$uliID = $option['uliID'];
	$html = print_number_select($playerID, $uliID);
	echo $html;
}

// Spieler auf die Liste setzen
if ($_POST['action'] == "takehome"){
	$playerID = $_POST['playerID'];
	$uliID = $option['uliID'];
	$html = take_player_home($playerID, $uliID);
	echo $html;
}


// Spieler auf die Liste setzen
if ($_POST['action'] == "puthimonlist"){
	$playerID = $_POST['playerID'];
	$form['WantedSum'] = $_POST['WantedSum'];
	$uliID = $option['uliID'];
	$html .= put_player_on_list($form, $playerID, $uliID);
	echo $html;
}


// hier passiert die verhandlungsaction
if ($_POST['action'] == "negotiate"){
	$playerID = $_POST['playerID'];
	if ($_POST['salary']){
		$form['salary'] = $_POST['salary'];
		$form['length'] = $_POST['length'];
	}
	else {
		$action = 'zerosalary';
	}

	$uliID = $option['uliID'];
	$html = negotiate_player($playerID, $uliID, $form, $action);
	$html .= "
	<script>
		jQuery(function($) {	
		    $('.sumInput').autoNumeric('init');    
		});
	</script>";	
	echo $html;
}


// hier wird nur das fenster aufgeklappt
if ($_POST['action'] == "contractplayer"){
	$playerID = $_POST['playerID'];
	$uliID = $option['uliID'];
	$html = negotiate_player($playerID, $uliID, $form, $action='');
	
	$html .= "
	<script>
		jQuery(function($) {	
		    $('.sumInput').autoNumeric('init');    
		});
	</script>";
	
	echo $html;
	
}

if ($_POST['action'] == "sellplayer"){
	$playerID = $_POST['playerID'];
	$player = get_player_infos($playerID);
	$html .= '<form class = "PutPlayerOnList" id="'.$playerID.'" method ="POST" onsubmit="return false">';
	$html .= WantedSumFor.$player['name'].'?';
	$html .= "\n";
	$html .= '<input class="formauction" type="text" id = "WantedSum-'.$playerID.'" name="WantedSum" size="12">';
	$html .= "\n";
	$html .= '<input class="formauction" type="submit" value="'.PutHimOnList.'">';
	$html .= "\n";
	$html .= '</form>';
	echo $html;
}


if ($_POST['action'] == "loadkader"){
	$sort = $_POST['sort'];
	$html = print_kader($sort);
	echo $html;
}


if ($_POST['action'] == "loadtranslist"){
	$html = print_transfermarkt(1);
		$html .= "
	<script>
		jQuery(function($) {	
		    $('.sumInput').autoNumeric('init');    
		});
	</script>";
	echo $html;
}


// TODO eine Nachricht, wenn die Auktion vorbei ist ...
// Verkauft an ...
// Einen Countdown ???

if ($_REQUEST['action'] == "reloadbet"){

	$auctionID = $_REQUEST['auctionID'];
	$auction = get_auction($auctionID);
	$leagueID = $option['leagueID'];
	$uliID = $option['uliID'];
	$mybet = get_bet_uliID($auctionID, $uliID);
	$sum = $mybet['sum'];

	if ($auction){
		$uliname = get_all_uli_names($leagueID);
		$newAuction .= uli_money($auction['topbet']);
		$newAuction .= ' ('.$uliname[$auction['topbetuliID']].')';
		$newAuction .= '<br>';

		if ($sum > 0){
			$newAuction .= MyTopBet.': '.uli_money($sum);
		}

		if (time() < $auction['end']){
			echo $newAuction;
			if ($auction['topbetuliID'] == $uliID)
			{$class = 'topgebot';}
			if ($auction['topbetuliID'] != $uliID)
			{$class = 'ueberboten';}
			echo '<script>';
			echo '$("#auction-'.$auctionID.'").removeClass("ueberboten topgebot");';
			echo '$("#auction-'.$auctionID.'").addClass("'.$class.'");';
			echo '</script>';
				
		}
		else {
			echo AuctionIsOver;
			echo '<script>';
			echo '$("#auction-'.$auctionID.' .betauction").html("");';
			echo '</script>';
		}

	}
}

if ($_POST['action'] == "submitbet"){

	// Jetzt werden hier noch einmal die ganzen lustigen Dinge eingebunden ???
	// Performance kann sicher auch verbessert werden
	$allbets     = get_sum_ulibets($option['uliID']);
	$guthaben    = get_value_bank(14, 0, 0, $option['uliID']);
	$kredite     = get_all_kredite($option['uliID']);
	$kreditrahmen = get_kreditrahmen($option['uliID']);
	$vermoegen   = $guthaben + $kreditrahmen - $allbets - $kredite;


	$auctionID = $_POST['auctionID'];
	$sum = $_POST['sum'];
	$playerID = $_POST['playerID'];
	$leagueID = $_POST['leagueID'];
	$uliID = $option['uliID'];

	// Gebot ueberpruefen
	$error = check_bet_auction($playerID, $sum, $auctionID, $uliID, $leagueID);

	$thisauction = get_auction($auctionID);
	//print_r($thisauction);
	//echo $error;
	/* Wenn Gebot OK, schreiben und Auktion aktualisieren */
	if($error == 'FALSE'){
		/* Eintragen/Aktualisieren */
		$betID = update_bet($sum, $auctionID, $uliID);
		$auction = calculate_auction($sum, $auctionID, $uliID, $betID);
	}
	elseif ($error == "Das ist zu wenig." AND $thisauction['hidden'] == 1){
		unset($error);
		$betID = update_bet($sum, $auctionID, $uliID);
		$auction = calculate_auction($sum, $auctionID, $uliID, $betID);		
	}


	if (!$auction){
		$auction = get_auction($auctionID);
	}

	// Auktionsfeld wird aktualisiert
	if ($auction)
	{
		$uliname = get_all_uli_names($leagueID);

		if ($auction['hidden'] == 1){
			$newAuction .= 'Verdeckte Auktion';
			$newAuction .= '<br>';				
		}
		if ($uliname[$auction['topbetuliID']] AND $auction['hidden'] == 0){
			$newAuction .= uli_money($auction['topbet']);
			$newAuction .= ' ('.$uliname[$auction['topbetuliID']].')';
			$newAuction .= '<br>';
		}
		if ($error == "FALSE"){
			$newAuction .= MyTopBet.': '.uli_money($sum);
		}
		elseif ($error) {
			$newAuction .= '<span class="message">'.$error.'</span>';
		}
		elseif ($auction['hidden'] == 1 AND !$error){
			$newAuction .= MyTopBet.': '.uli_money($sum);
		}

		if ($auction['claim'] > 0 AND !$uliname[$auction['topbetuliID']]) {
			$newAuction .= Claim.': '.uli_money($auction['claim']).'<br/>';
			$newAuction .= ' ('.$uliname[$auction['claimuliID']].')';
		}
		echo $newAuction;
	}

	/* CSS Klasse wird aktualisiert */
	if ($auction AND $error == "FALSE" AND $auction['hidden'] == 0){
		if ($auction['topbetuliID'] == $uliID)
		{$class = 'topgebot';}
		if ($auction['topbetuliID'] != $uliID)
		{$class = 'ueberboten';}
		echo '<script>';
		echo '$("#auction-'.$auctionID.'").removeClass("ueberboten topgebot");';
		echo '$("#auction-'.$auctionID.'").addClass("'.$class.'");';
		echo '</script>';
	}


	/* Message wird ausgegeben */
	if ($error != 'FALSE'){$message = $error;}
	elseif ($auction['message']){$message = $auction['message'];}



}




?>