<?php




// TODO 
/**********************************************************/
/**********************************************************/
/**********************************************************/
/**********************************************************/
/**********************************************************/
/**********************************************************/
// Ab hier alles noch einmal checken und sortieren !!!!!!!!!
/**********************************************************/
/**********************************************************/
/**********************************************************/
/**********************************************************/
/**********************************************************/
/**********************************************************/
/**********************************************************/

///**
// * schaut ob der Spieler im Kader des Managers steht
// * return TRUE oder FALSE
// * BRAUCHT MAN DAS WIRKLICH ???????????
// */
//function check_player_team($playerID, $uliID) {
//$cond[] = array("col" => "playerID", "value" => $playerID);	
//$cond[] = array("col" => "uliID", "value" => $uliID);	
//$result = uli_get_var('userplayer', $cond, 'ID');
//if ($result){return TRUE;}
//else {return FALSE;}
//}
//
///**
// * gibt das Userteam eines Players als ID zurueck
// * BRAUCHT MAN DAS WIRKLICH ???????????
// */
//function get_player_user_team($playerID, $leagueID){
//$cond[] = array("col" => "playerID", "value" => $playerID);	
//$cond[] = array("col" => "leagueID", "value" => $leagueID);	
//$result = uli_get_var('userplayer', $cond, 'uliID');
//if ($result){return $result;}
//else {return FALSE;}	
//}

















/**
 * gibt mit Ajax die Spielerinfo aus
 */
function print_spieler_info_ajax($playerID, $leagueID){
$objResponse = new xajaxResponse();
$playerinfo = print_player_info($playerID, $leagueID);
$html .= $playerinfo['content'];
$headline .= $playerinfo['headline'];
$cssid = 'player-'.$playerID;

$objResponse->call("YAHOO.example.container.PlayerInfo.setHeader", "<div class='tl'></div><span>".$headline."</span><div class='tr'></div>");
$objResponse->call("YAHOO.example.container.PlayerInfo.setBody", $html);
$objResponse->call("YAHOO.example.container.PlayerInfo.render", $cssid);
$objResponse->call("YAHOO.example.container.PlayerInfo.show");
return $objResponse;			
}










/**
 * holt Punkte Infos zu einem Spieler
 * Wieviel Spiele (echtes Leben, Uli)
 * Durchschnittspunkte (echtes Leben, Uli)
 * 
 * BRAUCHT MAN DAS WIRKLICH ?????
 * 
 */
//function get_player_scores($playerID, $uliID){
///* schaut in den letzten beiden Saisons nach */
//
///* Holt das zweitletzte Jahr */
//$order[] = array("col" => "ID", "sort" => "DESC");
//$cond[] = array("col" => "parent", "value" => 0);
//$cond[] = array("col" => "end", "value" => mktime(), "func" => "<");
//$year = uli_get_var('years', $cond, "ID", $order);
//
///* Wie oft hat der Spieler in echt gespielt */
//unset($cond);
//$cond[] = array("col" => "year", "value" => $year, "func" => ">=");
//$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
//$cond[] = array("col" => "playerID", "value" => $playerID);
//$result = uli_get_results('playerpoints', $cond, array('sum(score)', 'count(score)'));
//if ($result){
//	foreach ($result as $result){
//		$Score['score'] = $result['sum(score)'];
//		$Score['games']= $result['count(score)'];
//	}}
//else {$Score['score'] = 0; $Score['games'] = 0;}
//
///* Wie hoft hat der Spieler in seinem Uliteam gespielt */
///* Haken. Vielleicht war er da noch gar nicht im UliTeam */
//unset($cond);
//$cond[] = array("col" => "year", "value" => $year, "func" => ">=");
//$cond[] = array("col" => "round", "value" => 0, "func" => "!=");
//$cond[] = array("col" => "playerID", "value" => $playerID);
//$cond[] = array("col" => "uliID", "value" => $uliID);
//$cond[] = array("col" => "number", "value" => 15, "func" => "!=");
//$result = uli_get_results('userteams', $cond, array('sum(points)', 'count(points)'));
//if ($result){
//	foreach ($result as $result){
//		$Score['scoreuli'] = $result['count(points)'];
//		$Score['gamesuli']= $result['sum(points)'];
//	}}
//else {$Score['score'] = 0; $Score['games'] = 0;}
//
//if ($Score['games'] > 0){$Score['AvScore'] = $Score['score'] / $Score['games'];}
//return $Score;	
//}

///**
// * Holt alle Punkte eines Jahres in ein array
// */
//function get_player_points_byyear($playerID, $year) {
//$cond[] = array("col" => "playerID", "value" => $playerID);
//$cond[] = array("col" => "year", "value" => $year);
//$order[] = array("col" => "round", "sort" => "ASC");
//$result = uli_get_results('playerpoints', $cond, NULL, $order);
//if ($result){
//	$score = array();
//	foreach ($result as $points){
//	$score[$points['round']] = $points['score'];
//	}}
//	return $score;
//}



///**
// * holt alle Transfers eines Spielers
// * 23.07.2010
// */
//function get_transfers($playerID, $leagueID = '') {
//global $option;
//if (!$leagueID){$leagueID = $option['leagueID'];}	
//$cond[] = array("col" => "playerID", "value" => $playerID);
//$cond[] = array("col" => "leagueID", "value" => $leagueID);
//$result = uli_get_results('transfer', $cond);
//if ($result){return $result;}
//else {return FALSE;}	
//}



/**
 * Gibt die Spielerinfobox als HTML aus. 
 * 23.07.2010
 * Es wird nur der Content zurueckgegeben
 * Deswegen kann diese Funktion sowohl als Panel, als auch als HTML oder in der Sidebar angesprochen werden
 * Es wird unterteilt zwischen Headline und Content
 * 
 * Was wird ausgegeben
 * 
 * Trikotnummer, Vorname Nachname, Alter, Position
 * Bild
 * Verein (echt)
 * Verein (Uli)
 * letzte Abloesesumme
 * Gehalt (nur Naeherungswert, wenn fremder Klub schaut)
 * Marktwert 
 * Punkte (echt) alle Saisons
 * Spiele (echt)
 * Transfers
 * TODO
 * Punkte/Spiele fuer die einzelnen Teams
 * 
 * 
 * 
 */
function print_player_info($playerID, $leagueID = ''){
global $option;
if (!$leagueID){$leagueID = $option['leagueID'];}	
$uliteams = get_all_uli_names($leagueID);
$ligateams = get_all_team_names();
$player = get_player_infos($playerID, array("player", "playerattributes", "contracts", "userplayer"), NULL, $leagueID);

// Name
if ($player['vorname']){
	$name .= $player['vorname'].' '.$player['nachname'];
	// Kuenstlername 
	if ($player['nachname'] != $player['name']){
		$name .= ' ('.$player['name'].')';		
		}}
else {$name = $player['name'];}

// Bild
$playerpic = get_player_pic($player['playerID']);

// Alter
$age = player_age($player['birthdayTimestamp']);

// Star
if ($player['star'] > 0){
	$staricon = get_star_icon($player['star']);}

// Position
$position .= '<b>'.$option['position'.$player['hp']].'</b>';
if ($player['np1']){$position .= '<br/>'.$option['position'.$player['np1']].' ';}
if ($player['np2']){$position .= '<br/>'.$option['position'.$player['np2']].' ';}
$position .= ' ('.$option['foot'.$player['foot']].')';

// Buli-Team
$ligateam = $ligateams[$player['buliteam']];

// Diese ganzen Sachen werden nur ausgeben, wenn der Spieler einen Besitzer hat
if ($player['userteamuliID']){
	// Trikotnummer
	if ($player['jerseynumber']){
		$jerseynumber = '<div class="jerseynumber">'.$player['jerseynumber'].'</div>';
		}

	// wenn nicht der "Besitzer" schaut, wird nur ein schaetzwert ausgegeben
	if ($player['userteamuliID'] != $option['uliID']){
		$result = get_estimated_value($player['salary']);
		$salary = 'ca. '.uli_money($result['minvalue']).' - '.uli_money($result['maxvalue']);
		}
	else {
		$salary = uli_money($player['salary']);		
	}	
$contractend = Contract.' '.until.' '.uli_date($player['end']);
$userteam = $uliteams[$player['userteamuliID']];
$marktwert = uli_money($player['marktwert']);
}	
// Der Spieler ist nicht unter Vertrag
else {
$userteam = NoJob;	
}

// HEADLINE
$headline .= '<div class="name">';
$headline .= get_ligateam_wappen($player['buliteam'], $ligateam).' ';
$headline .= $name.' ('.$age.')';
$headline .= "\n";
$headline .= '</div>';
// CONTENT
$html .= '<div class="playerinfo">';
$html .= "\n";
	$html .= '<div class="pic">';
	$html .= $playerpic;
	$html .= '</div>';
	$html .= "\n";
	$html .= '<div class="content">';
	$html .= $jerseynumber;
	$html .= $position.'<br/>';
	$html .= $userteam.'<br/>';
	$html .= '('.$contractend.')<br/>';
	if ($salary){$html .= Salary.': '.$salary.'<br/>';}
	if ($marktwert){$html .= Marktwert.': '.$marktwert.'<br/>';}
	$html .= '</div>';
	$html .= '<div class="clear"></div>';
	
	$html .= "\n";
	
	$html .= '<div class="details">';
		// Punkte
		$years = get_uli_years('DESC');
		if ($years){
			foreach ($years as $year){
			$score = array();
			$score = get_player_points_byyear($player['playerID'], $year['ID']);
			$html .= '<select>';
			$html .= '<option>'.$year['name'].': ';
			if (isset($score[0])) {$html .= $score[0].' '.Points;}
			else { $html .= ' --- ';}
			$html .= '</option>';
			for ($x=1; $x<=34; $x++) 
			{ $html .= '<option>'.$x.'. Spieltag:  ';
			if (isset($score[$x])) {$html .= '<b>'.$score[$x].'</b> '.Points.'</option>'; } 
			else { $html .= ' --- ';}
			}
			$html .= '</select><br/>';
		}}
		// Transfers
		$transfers = get_transfers($player['playerID'], $leagueID);
		if ($transfers){
			$html .= '<select>';
			$html .= '<option>'.Transfers.':</option>';
			foreach ($transfers AS $transfer)
			{
			$html .= '<option>';
			$old = '';
			$new = '';
			if($transfer['externold']) {$old = $transfer['externold'];}
			if($transfer['uliold']) {$old = $uliteams[$transfer['uliold']];}
			if (!$old){$old = NoJob;}
			if($transfer['ulinew']) {$new = $uliteams[$transfer['ulinew']];}
			if($transfer['externnew']) {$new = $transfer['externnew'];}
			$datum = date("d.m.y",$transfer['time']);
			$html .= $datum.': '.$old.' >> '.$new.' ('.uli_money($transfer['sum']).')';
			$html .= '</option>'; 
			} 
		$html .= '</select><br/>';
		}		
	$html .= '</div>';
	$html .= "\n";
$html .= '</div>';
$html .= "\n";
$playerinfo['headline'] = $headline;
$playerinfo['content'] = $html;
return $playerinfo;
}



/**
 * holt Maximal und Durchschnittsgehalt der Liga oder des Managers
 * 02.06.09
 * 
 * hoechstwahrscheinlich brauchen wir das nicht mehr
 */
/*
function get_salary_score_data($leagueID = '', $uliID = ''){
	global $option;
	//
	if ($leagueID){
		$cond[] = array("col" => "leagueID", "value" => $leagueID);	}
		if ($uliID){
			$cond[] = array("col" => "uliID", "value" => $uliID);}

			$cond[] = array("col" => "history", "value" => 0);
			$cond[] = array("col" => "salary", "value" => 0, "func" => ">");
			$result = uli_get_results('contracts', $cond);
			if ($result){
				foreach ($result as $result){
					$salarySum = $salarySum + $result['salary'];
					$salaryArray[] = $result['salary'];
				}
				$data['AvSalary'] = $salarySum / count($salaryArray);
				$data['MaxSalary'] = max($salaryArray);
				$data['MinSalary'] = min($salaryArray);
			}

			// 
			unset($cond);
			// Holt das zweitletzte Jahr
			$order[] = array("col" => "ID", "sort" => "DESC");
			$cond[] = array("col" => "parent", "value" => 0);
			$cond[] = array("col" => "end", "value" => mktime(), "func" => "<");
			$year = uli_get_var('years', $cond, "ID", $order);

			unset($cond);
			if($uliID){$tablestring = 'playerpoints p RIGHT JOIN '.$option['prefix'].'uli_userplayer u ON u.playerID = p.playerID AND u.uliID = '.$uliID.'';}
			else {$tablestring = 'playerpoints p ';}
			$cond[] = array("col" => "p.year", "value" => $year, "func" => ">=");
			$cond[] = array("col" => "p.round", "value" => 0, "func" => "!=");
			$result = uli_get_results($tablestring, $cond, array('sum(p.score)', 'count(p.score)'), NULL, NULL, 'group by p.playerID');
			if ($result){
				foreach ($result as $result){
					if ($result['count(p.score)'] > 8) // Es müssen mehr als 8 Spiele sein 
					{$scores[] = $result['sum(p.score)'] / $result['count(p.score)'];}
				}
				$data['MaxScore'] = max($scores);
				$data['MinScore'] = min($scores);

				// Durchschnitt 
				$data['AvScore'] = array_sum($scores)/count($scores);
			}

			if ($data){return $data;}
			else {return FALSE;}
}
*/

?>