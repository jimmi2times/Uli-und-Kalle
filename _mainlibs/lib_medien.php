<?php


function get_news($limit, $sticky = NULL) {
	global $option;
	if ($option['leagueID']){
		if (!$sticky){
			$cond[] = array("col" => "status", "value" => 0, "func" => ">");
		} else {
			$cond[] = array("col" => "status", "value" => 2);
		}
		$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
		// Die alten weglassen
		$cond[] = array("col" => "timestamp", "value" => 1432928068, "func" => ">");
		$order[] = array("col" => "timestamp", "sort" => "DESC");
		$news = uli_get_results('journal_articles', $cond, NULL, $order, $limit);
		if ($news){return $news;}
		else {return False;}
	}
}



/*
 * Subject
 * 1 = player
 * 2 = Verlaengerung gescheitert
 * 3 = mit fremden Spieler verhandelt und einig geworden
 * 4 = auf fremden Spieler geboten
 *
 *	Sort
 *
 * Tonalit�t
 * 1 = normal
 * 2 = lieb
 * 3 = b�se
 *
 */
function textbausteine(){

	// Spielerinfo
	$a[1][1][1][] = '[[PLAYER]] spielt seit [[TRANSFERDATE]] bei [[ULINAME1]]. ';
	$a[1][1][1][] = 'Seit [[TRANSFERDATE]] tr&auml;gt [[PLAYER]] die Farben von [[ULINAME1]]. ';
	$a[1][1][1][] = 'Am [[TRANSFERDATE]] wurde [[PLAYER]] bei [[ULINAME1]] vorgestellt. ';

	$a[1][2][1][] = '[[PLAYER]] kostete [[ABLOESE]] und verdient gesch&auml;tzt [[SALARY]]. ';
	$a[1][2][1][] = '[[ABLOESE]] war [[PLAYER]] [[ULINAME1]] damals wert. ';

	$a[1][3][1][] = 'In [[GAMES]] Spielen erzielte [[PLAYER]] [[SCORE]] Punkte. ';
	$a[1][3][1][] = '[[SCORE]] Punkte aus [[GAMES]] Spielen konnte er verbuchen. ';


	$a[2][1][1][] = '[[ULINAME1]] und [[PLAYER]] sind sich nicht &uuml;ber eine vorzeitige Vertragsverl&auml;ngerung einig geworden. ';
	$a[2][1][1][] = 'Eigentlich wollte man schnell verl&auml;ngern. Daraus wurde nichts. ';
	$a[2][1][1][] = 'Gespr&auml;che fanden statt. Zu einer Unterschrift kam es nicht. ';

	$a[2][2][1][] = 'Nun ist es v&ouml;llig offen, wie es jetzt weitergeht. ';
	$a[2][2][1][] = 'Eventuell werden sich beide Seiten nach dem n&auml;chsten Spiel noch einmal zusammensetzen. ';
	$a[2][2][1][] = '[[PLAYER]] sagte die eigentlich festeingeplante Vertragsunterzeichung kurzfristig ab und bat um mehr Bedenkzeit. ';

	$a[2][3][1][] = '[[ULINAME1]] m&ouml;chte [[PLAYER]] gerne halten, will daf&uuml;r aber nicht das Gehaltsgef&uuml;ge zerst&ouml;ren. ';
	$a[2][3][1][] = '[[PLAYER]] betonte, dass er gerne f&uuml;r [[ULINAME1]] spiele, aber sich ein wenig mehr monet&auml;re Wertsch&auml;tzung w&uuml;nsche.';
	$a[2][3][1][] = 'Beide Seiten sind guter Dinge, dass die Vertragsverl&auml;ngerung beim n&auml;chsten Treffen klappt. ';

	$a[3][1][1][] = 'Unter einem guten Stern standen die Gespr&auml;che zwischen [[PLAYER]] und [[ULINAME2]]. ';
	$a[3][1][1][] = 'Schnell einig wurden sich [[ULINAME2]] und [[PLAYER]]. ';
	$a[3][1][1][] = 'Dem Angebot von [[ULINAME2]] konnte [[PLAYER]] nicht widerstehen. ';

	$a[3][2][1][] = 'Die gem&uuml;tliche Athmosph&auml;re beim Italiener mag eine Teil dazu beigetragen haben. ';
	$a[3][2][1][] = 'Beide Seiten meinten danach, dass sie sehr gut zusammen passen w&uuml;rden. ';
	$a[3][2][1][] = 'Das geheime Treffen war auch der Grund f&uuml;r das unentschuldigte Fernbleiben vom Training. ';

	$a[3][3][1][] = '&quot;[[ULINAME2]] ist mein Traumverein, ich hoffe [[ULINAME1]] legt mir keine Steine in den Weg&quot;, sagte [[PLAYER]] gegen&uuml;ber unserer Zeitung. ';
	$a[3][3][1][] = 'Jetzt wird erwartet, dass [[ULIMANAGERNAME2]] sich bei [[ULIMANAGERNAME1]] mit einem Abl&ouml;segebot meldet. ';
	$a[3][3][1][] = 'Fraglich allerdings, ob [[ULIMANAGERNAME1]] [[PLAYER]] so einfach ziehen l&auml;sst. ';
	
	$a[4][1][1][] = 'Die Fans organisieren den Widerstand.';
	$a[4][1][2][] = 'Beide Seiten freuen sich auf die Partnerschaft.';
	$a[4][1][3][] = '[[ULIMANAGERNAME1]]: "Sieht schei&szlig;e aus, aber ich brauche die Kohle."';
	$a[4][1][4][] = '[[ULIMANAGERNAME1]]: "Unser Topf hat einen Deckel gefunden. Ich bin ger&uuml;hrt."';

	$a[5][1][1][] = 'Hohn und Spott prasselt auf [[ULIMANAGERNAME1]] ein.';
	$a[5][1][2][] = 'Wieder ein St&uuml;ck Tradition verschwunden.';
	$a[5][1][3][] = '[[ULIMANAGERNAME1]]: "Wir m&uuml;ssen alles tun um an neue Gelder zu kommen."';
	$a[5][1][4][] = '[[ULIMANAGERNAME1]]: "Ein Meilenstein der Sportvermarktung."';
	
	return $a;
}


function textbausteine_headlines(){
	$a[2][2][] = '[[ULINAME1]] w&uuml;rde [[PLAYER]] gerne halten';
	$a[2][2][] = '[[ULIMANAGERNAME1]] wei&szlig;, was er an [[PLAYER]] hat.';
	$a[2][2][] = '[[PLAYER]] und [[ULINAME1]]: Doch noch kein Abschluss';
	$a[2][2][] = 'Zwischen [[PLAYER]] und [[ULINAME1]] geht es noch um Details';
	$a[2][2][] = '[[ULINAME1]] will [[PLAYER]] halten, aber nicht zu jedem Preis';
	$a[2][2][] = '[[ULIMANAGERNAME1]]: &quot;Sprechen in den n&auml;chsten Wochen noch einmal mit [[PLAYER]]&quot;';
	$a[2][2][] = '[[ULINAME1]] ist trotzdem erste Option';
	$a[2][2][] = '[[PLAYER]] und [[ULINAME1]]: Einigung in Sicht ';
	$a[2][2][] = 'Konstruktive Gespr&auml;che. ';
	$a[2][2][] = '[[PLAYER]] ist zuversichtlich';
	$a[2][1][] = '[[PLAYER]] und [[ULINAME1]] kommen zu keiner Einigung';
	$a[2][1][] = '[[PLAYER]] fordert deutlich mehr Gehalt';
	$a[2][1][] = 'Endet die Zusammenarbeit?';
	$a[2][1][] = '[[PLAYER]] und [[ULINAME1]] trennen Welten';
	$a[2][1][] = '[[PLAYER]] hat auch andere Angebote';
	$a[2][1][] = 'Vertrauen gest&ouml;rt ';
	$a[2][1][] = 'Geht [[PLAYER]] jetzt?';
	$a[2][1][] = 'Die Zukunft von [[PLAYER]] ist offen.';
	$a[2][1][] = 'Spielt [[PLAYER]] jetzt auch f&uuml;r andere vor?';
	$a[2][1][] = 'Die Fans hoffen, dass [[PLAYER]] bleibt.';
	$a[2][3][] = '[[PLAYER]] empfindet Angebot als Provokation ';
	$a[2][3][] = '[[PLAYER]]: &quot;Ich spiele in den Planungen von [[ULIMANAGERNAME1]] scheinbar keine Rolle.&quot;';
	$a[2][3][] = '[[ULIMANAGERNAME1]]: &quot;[[PLAYER]] ist unversch&auml;mt&quot;';
	$a[2][3][] = '[[PLAYER]] sieht Leistungen nicht honoriert';
	$a[2][3][] = '[[PLAYER]] ist von [[ULIMANAGERNAME1]] pers&ouml;nlich entt&auml;uscht';
	$a[2][3][] = '[[PLAYER]] ist unzufrieden mit der Gesamtsituation.&quot;';
	$a[2][3][] = 'Das Tischtuch ist zerschnitten.';
	$a[2][3][] = '[[ULIMANAGERNAME1]] fordert [[PLAYER]] zu Vernunft auf';
	$a[2][3][] = 'Zwischen [[PLAYER]] und [[ULINAME1]] knallt es';
	$a[2][3][] = '[[PLAYER]]: Dann gehe ich halt ins Ausland!';


	$a[3][1][] = '[[PLAYER]]: Ich will zu [[ULINAME2]]';
	$a[3][1][] = '[[PLAYER]]: [[ULINAME2]] ist mein Traumverein';
	$a[3][1][] = '[[PLAYER]]: Mit [[ULIMANAGERNAME1]] kann man nicht arbeiten.';
	$a[3][1][] = '[[ULIMANAGERNAME2]] und [[PLAYER]] am Flughafen Hannover gesehen.';
	$a[3][1][] = '[[ULIMANAGERNAME1]] tobt: [[PLAYER]] will zu [[ULINAME2]]';
	$a[3][1][] = 'Gute Gespr&auml;che zwischen [[PLAYER]] und [[ULINAME2]]';
	$a[3][1][] = '[[ULIMANAGERNAME1]] geschockt. [[PLAYER]] will weg.';
	$a[3][1][] = '[[ULIMANAGERNAME1]]: Dann soll die Sau doch gehen.';
	$a[3][1][] = '[[ULIMANAGERNAME1]]: Dann will ich aber richtig Kohle sehen.';

	
	$a[4][1][] = '[[ULINAME1]]: Vertrag mit [[SPONSOR]] unterschrieben.';
	$a[4][1][] = '[[ULINAME1]] und [[SPONSOR]] sind sich einig.';
	$a[4][1][] = 'Wappen von [[Sponsor]] ziert die Brust der Trikots von [[ULINAME1]]';

	$a[5][1][] = '[[ULINAME1]] nennt sein Stadion jetzt [[STADIONNAME]]';
	$a[5][1][] = '[[ULINAME1]] verkauft Namensrechte: Das traditionsreiche Stadion hei&szlig; jetzt [[STADIONNAME]]';
	$a[5][1][] = 'Zu Heimspielen demn&auml;chst ins "[[STADIONNAME]]"';
	
	return $a;
}




function create_gossip($type, $playerID = NULL, $leagueID, $uli1 = '', $uli2 = '', $published = NULL){
	global $option;
	
	if ($playerID){
		$player = get_player_infos($playerID, $leagueID, array('all'));
	}
	if (!$uli1){
		$uli1 = $player['uliID'];
	}

	$textbausteine = textbausteine();
	$headlines = textbausteine_headlines();

	// Der Randomizer um Ger�chte zu streuen
	// Mit einer 20% Wahrscheinlichkeit wird die uli2 ID manipuliert
	if ($uli2){
		if (rand(1,5) == 1){
			$allulis = get_ulis($option['leagueID']);	
			foreach ($allulis as $ulis){
				$uliIDs[] = $ulis['ID'];
			}
			$newuli2 = $uliIDs[rand(0, (count($uliIDs) - 1))];
			if ($newuli2 == $player['uliID']){
				$newuli2 = $uli2;
			} 
		$uli2 = $newuli2;	
		}
	}
	
	
	// Daten fuer Platzhalterbausteine
	$ulidata1 = get_uli($uli1);
	$ulimanagername1 = get_user_name($ulidata1['userID']);
	$ulidata2 = get_uli($uli2);
	$ulimanagername2 = get_user_name($ulidata2['userID']);

	// gescheiterte Verhandlung
	if ($type == 2){
		if ($player['uliID'] == $uli1){
			// 3 -> grosse Differenz zwischen Angebot und Forderung (50%)
			// 3 -> kurze Vertragslaufzeit (unter 3 Monate)
			// 2 -> Differenz weniger als 10%
			// sonst 1
			$tonalitaet = 1;
			if ($player['contractend'] < (mktime() + 3600*24*90)){
				$tonalitaet = 3;
			}
			$negotiation = get_running_negotiation($playerID, $uli1);
			if (($negotiation['salary'] * 2) < $negotiation['claim']){
				$tonalitaet = 3;
			}
			if (($negotiation['salary'] * 1.1) > $negotiation['claim']){
				$tonalitaet = 2;
			}
			$rand = rand(0, (count($headlines[$type][$tonalitaet])-1));
			$articleheadline = $headlines[$type][$tonalitaet][$rand];


			// Tonaltit�t ersteinmal 1 bei den Texten
			$tonalitaet = 1;
			$rand = rand(0, (count($textbausteine[$type][1][$tonalitaet])-1));
			$articletext .= $textbausteine[$type][1][$tonalitaet][$rand];
			$rand = rand(0, (count($textbausteine[$type][2][$tonalitaet])-1));
			$articletext .= $textbausteine[$type][2][$tonalitaet][$rand];
			$rand = rand(0, (count($textbausteine[$type][3][$tonalitaet])-1));
			$articletext .= $textbausteine[$type][3][$tonalitaet][$rand];

		}
		else {


		}
	}

	// Erfolgreicher Vertragsabschluss mit einem anderen UliTeam
	if ($type == 3){
		if ($player['uliID'] != $uli2){
			// Tonaltit�t ersteinmal 1 bei den Texten
				
			$tonalitaet = 1;
			$rand = rand(0, (count($headlines[$type][$tonalitaet])-1));
			$articleheadline = $headlines[$type][$tonalitaet][$rand];
			$rand = rand(0, (count($textbausteine[$type][1][$tonalitaet])-1));
			$articletext .= $textbausteine[$type][1][$tonalitaet][$rand];
			$rand = rand(0, (count($textbausteine[$type][2][$tonalitaet])-1));
			$articletext .= $textbausteine[$type][2][$tonalitaet][$rand];
			$rand = rand(0, (count($textbausteine[$type][3][$tonalitaet])-1));
			$articletext .= $textbausteine[$type][3][$tonalitaet][$rand];
		}

	}

	
	// Sponsoring Deal unterschrieben oder Stadionnamen verkauft
	if ($type == 4 OR $type == 5)	{
			$tonalitaet = 1;
			$rand = rand(0, (count($headlines[$type][$tonalitaet])-1));
			$articleheadline = $headlines[$type][$tonalitaet][$rand];
			$rand = rand(0, (count($textbausteine[$type][1][$tonalitaet])-1));
			$articletext .= $textbausteine[$type][1][$tonalitaet][$rand];
	}
	


	if ($articleheadline){
		// Spielerinfobausteine
		// TODO in eigene Funktion, braucht man ja immer, oder an den Schluss der ganzen Funktion

		if ($player){
			$rand = rand(0, (count($textbausteine[1][1][1])-1));
			$articleplayer .= $textbausteine[1][1][1][$rand];
			$rand = rand(0, (count($textbausteine[1][2][1])-1));
			$articleplayer .= ' '.$textbausteine[1][2][1][$rand];
			$rand = rand(0, (count($textbausteine[1][3][1])-1));
			$articleplayer .= ' '.$textbausteine[1][3][1][$rand];
		}
		
		// Jetzt die Platzhalter ersetzen
		$placeholders['ULINAME1'] = $ulidata1['uliname'];
		$placeholders['ULIMANAGERNAME1'] = $ulimanagername1;
		$placeholders['ULINAME2'] = $ulidata2['uliname'];
		$placeholders['ULIMANAGERNAME2'] = $ulimanagername2;
		$placeholders['PLAYER'] = $player['name'];
		$placeholders['TRANSFERDATE'] = uli_date($player['transferdetails'][0]['time']);
		$placeholders['ABLOESE'] = uli_money($player['transferdetails'][0]['sum']);
		$estimatedsalary = get_estimated_value($player['salary'], $playerID);
		$placeholders['SALARY'] = uli_money($estimatedsalary['minvalue']).' - '.uli_money($estimatedsalary['maxvalue']);
		$leaguegames = get_player_league_games($playerID, $uli1);
		$placeholders['SCORE'] = $leaguegames['points'];
		$placeholders['GAMES'] = $leaguegames['games'];
		$placeholders['SPONSOR'] = get_current_sponsor_name($option['uliID']);
		$placeholders['STADIONNAME'] = get_current_stadium_name($option['uliID']);
		
		

		$articleheadline = replace_news_placeholder($articleheadline, $placeholders);
		$articletext = replace_news_placeholder($articletext, $placeholders);
		$articleplayer = replace_news_placeholder($articleplayer, $placeholders);

		// Egal was es ist, die generierte Nachricht wird in der Datenbank als Entwurf gespeichert
		
		if ($player){
			$values[] = array("col" => "text", "value" => '<div class="maintext">'.$articletext.'</div><div class="playertext">'.$articleplayer.'</div>');
		} else {
			$values[] = array("col" => "text", "value" => $articletext);
		}
		$values[] = array("col" => "headline", "value" => $articleheadline);
		$values[] = array("col" => "playerID", "value" => $playerID);
		
		// bei bestimmten Nachrichten wird es gleich zu einer News
		if ($published == 1){
			$values[] = array("col" => "status", "value" => 1);
		} else {
			$values[] = array("col" => "status", "value" => 0);
		}
		$values[] = array("col" => "timestamp", "value" => mktime());
		$values[] = array("col" => "leagueID", "value" => $option['leagueID']);
		uli_insert_record('journal_articles', $values);

	}




}


function replace_news_placeholder($text, $placeholders){
	foreach ($placeholders as $key => $placeholder){
		$search = '[['.$key.']]';
		$text = str_replace($search, $placeholder, $text);
	}
	return $text;
}


function get_current_sponsor_name($uliID){
	global $option;

	$cond[] = array("col" => "status", "value" => 2);
	$cond[] = array("col" => "year", "value" => $option['currentchildyear']);
	$cond[] = array("col" => "team_id", "value" => $uliID);
	$id = uli_get_var("sponsoring", $cond, "sponsor_id");
	unset ($cond);
	$cond[] = array("col" => "ID", "value" => $id);
	$name = uli_get_var("sponsors", $cond, "name");
	
	if ($name){
		return $name;
	}
}

function get_current_stadium_name($uliID){
	global $option;
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$name = uli_get_var("stadium", $cond, "name");
	if ($name){
		return $name;
	}
}


?>