<?
/**
 * Stand 29.05.09
 * Das ist noch nicht fertig
 *
 * Eine reine Admin Funktion (bis auf get_TR)
 * Dauert lange
 * Wird nur beim Auswerten und eigentlich nicht zwischendurch angetriggert
 *
 * Es fehlt dabei noch das "Fan Potential"
 *
 * Sind es vielleicht zu viele wirtschaftliche Kriterien? (3)
 *
 * Sollte das Teamranking permanent abgespeichert werden?
 * Ja. Mit Timestamp und "archived"
 *
 *
 */

/**
 * holt das TeamRanking eines Teams
 * 02.06.09
 */
function get_TR($uliID){
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$result = uli_get_row('team_ranking', $cond);
	if($result)
		{return $result;}
	else {
		$result['TR_gesamt'] = 50;
		return $result;
	}
}

/**
 * holt das hoechste und das niedrigste aktuelle Teamranking einer Liga
 * 25.04.11
 */
function get_topdown_TR($leagueID){
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "archived", "value" => 0);
	$order[] = array("col" => "TR_gesamt", "sort" => "DESC");
	$result = uli_get_results('team_ranking', $cond, NULL, $order);

	if ($result){
		$x = 1;
		foreach ($result as $result){
			if ($x == 1){$TR['top'] = $result['TR_gesamt'];}
			$TR['down'] = $result['TR_gesamt'];
			$x++;
		}
	}
	if($TR){return $TR;}
	else {return FALSE;}
}



/**
 * Berechnet das komplette Teamranking einer ganzen Liga
 */
function calculate_team_ranking($leagueID){
	global $option;

	/* Marktwert Kader */
	$TR_Marktwert = TR_marktwert($leagueID);

	/* Ewige Tabelle */
	$TR_EwigeTabelle = TR_Ewige_Tabelle($leagueID);

	/* Umsatz/Einnahmen Gesamt (8) */
	$TR_Umsatz = TR_get_umsatz($leagueID);

	/* Gewinn/Saldo (7) */
	$TR_Gewinn = TR_get_gewinn($leagueID);

	/* Vermoegen (14 - Kredite) */
	$TR_Vermoegen = TR_get_vermoegen($leagueID);

	/* Stadion */
	$TR_Stadium = TR_Stadium($leagueID);

	/* Fan Potential */

	/* Player Points */
	$TR_Player_Points = TR_Player_Points($leagueID);



	/* Aktualisieren der Werte in der DB */
	$ulis = get_ulis($leagueID);
	foreach($ulis as $uli){
		//echo $uli['uliname'].': '.$TR_EwigeTabelle[$uli['ID']].' - '.$TR_Umsatz[$uli['ID']].' - '.$TR_Gewinn[$uli['ID']].' - '.$TR_Stadium[$uli['ID']].' - '.$TR_Marktwert[$uli['ID']].' - '.$TR_Vermoegen[$uli['ID']];
		$gesamt = ($TR_EwigeTabelle[$uli['ID']] + $TR_Umsatz[$uli['ID']] + $TR_Gewinn[$uli['ID']] + $TR_Stadium[$uli['ID']] + $TR_Marktwert[$uli['ID']] + $TR_Vermoegen[$uli['ID']] + $TR_Player_Points[$uli['ID']]) / 7;
		$TR['uliID'] = $uli['ID'];
		$TR['TR_gesamt'] = $gesamt;
		$TR['leagueID'] = $leagueID;
		$TR['TR_umsatz'] = $TR_Umsatz[$uli['ID']];
		$TR['TR_ewigetabelle'] = $TR_EwigeTabelle[$uli['ID']];
		$TR['TR_gewinn'] = $TR_Gewinn[$uli['ID']];
		$TR['TR_stadion'] = $TR_Stadium[$uli['ID']];
		$TR['TR_marktwert'] = $TR_Marktwert[$uli['ID']];
		$TR['TR_vermoegen'] = $TR_Vermoegen[$uli['ID']];
		$TR['TR_player_points'] = $TR_Player_Points[$uli['ID']];
		$TR['TR_fans'] = 0; /* $TR_Fans[$uli['ID']]; */

		update_TR($TR);

		//echo $uli['ID'].': '.$TR['TR_stadion'].'<br/>';

	}
	return TRUE;
}


/**
 * Aktualisiert einen TeamRanking Eintrag bzw. legt ihn neu an
 * 02.06.09
 *
 *
 *
 *
 */
function update_TR($TR){
	global $option;
	$value[] = array("col" => "archived", "value" => "1");
	$cond[] = array("col" => "archived", "value" => "0");
	$cond[] = array("col" => "uliID", "value" => $TR['uliID']);


	// provisorisch erst einmal immer die alten loeschen
	//uli_update_record('team_ranking', $cond, $value, $TR);
	uli_delete_record('team_ranking', $cond);

	unset($cond);
	unset($value);

	foreach ($TR as $key => $TR){
		$value[] = array("col" => $key, "value" => $TR);
	}

	$value[] = array("col" => "timestamp", "value" => mktime());
	if (uli_insert_record('team_ranking', $value)){return TRUE;}
	else {return FALSE;}
}



/**
 * Holt die Teamranking der Werte f�r den Marktwert des Kaders
 *
 */
function TR_marktwert($leagueID){
	global $option;
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "uliID", "value" => 0, "func" => "!=");
	$result = uli_get_results(player_league, $cond, array('SUM(marktwert)', 'uliID'), NULL, NULL, 'group by uliID');
	if ($result){
		foreach ($result as $marktwert){
			$size[$marktwert['uliID']] = $marktwert['SUM(marktwert)'];
		}
		$minSize = min($size);
		$maxSize = max($size);
		foreach ($result as $marktwert){
			$TR_Marktwert[$marktwert['uliID']] =  ($size[$marktwert['uliID']] - $minSize) * 100 / ($maxSize - $minSize);
			settype($TR_Marktwert[$marktwert['uliID']], INT);
		}
	}
	if ($TR_Marktwert){return $TR_Marktwert;}
	else {return FALSE;}
}

/**
 * gibt das TR f�r das Verm�gen zur�ck
 * Verm�gen = Geld auf Konto minus laufende Kredite
 */
function TR_get_vermoegen($leagueID){
	global $option;

	$tableString  = 'finances f ';
	$tableString .= ' LEFT JOIN '.$option['prefix'].'uli u ON u.ID = f.uliID';
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "year", "value" => 0);
	$cond[] = array("col" => "type", "value" => 14);
	$cond[] = array("col" => "u.leagueID", "value" => $leagueID);
	$result = uli_get_results($tableString, $cond, array('SUM(sum)', 'uliID'), NULL, NULL, 'GROUP by uliID');
	if ($result){
		foreach ($result as $umsatz){
			$size[$umsatz['uliID']] = $umsatz['SUM(sum)'];
		}
		unset($cond);
		$tableString  = 'kredite k ';
		$tableString .= ' LEFT JOIN '.$option['prefix'].'uli u ON u.ID = k.toklub';
		$cond[] = array("col" => "paid", "value" => 0);
		$cond[] = array("col" => "u.leagueID", "value" => $leagueID);
		$result2 = uli_get_results($tableString, $cond, array('SUM(sum)', 'toklub'), NULL, NULL, 'GROUP by toklub');

		if ($result2){
			foreach ($result2 as $kredite){
				$size[$kredite['toklub']] = $size[$kredite['toklub']] - $kredite['SUM(sum)'];
			}}
			$minSize = min($size);
			$maxSize = max($size);

			foreach ($result as $umsatz){
				$TR_Vermoegen[$umsatz['uliID']] =  ($size[$umsatz['uliID']] - $minSize) * 100 / ($maxSize - $minSize);
				settype($TR_Vermoegen[$umsatz['uliID']], INT);
			}
	}
	if ($TR_Vermoegen){return $TR_Vermoegen;}
	else {return FALSE;}
}


/**
 * Gibt fuer eine Liga die Teamranking-Werte fuer den Umsatz aus
 * Es zaehlen die letzten beiden Jahre
 */
function TR_get_umsatz($leagueID){
	global $option;

	/* Holt das zweitletzte Jahr */
	$order[] = array("col" => "ID", "sort" => "DESC");
	$cond[] = array("col" => "parent", "value" => 0);
	$cond[] = array("col" => "end", "value" => mktime(), "func" => "<");
	$year = uli_get_var('years', $cond, "ID", $order);

	unset($cond);
	$tableString  = 'finances f ';
	$tableString .= ' LEFT JOIN '.$option['prefix'].'uli u ON u.ID = f.uliID';
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "type", "value" => 8);
	$cond[] = array("col" => "year", "value" => $year, "func" => ">=");
	$cond[] = array("col" => "u.leagueID", "value" => $leagueID);
	$result = uli_get_results($tableString, $cond, array('SUM(sum)', 'uliID'), NULL, NULL, 'GROUP by uliID');

	if ($result){
		foreach ($result as $umsatz){
			$size[$umsatz['uliID']] = $umsatz['SUM(sum)'];
		}
		$minSize = min($size);
		$maxSize = max($size);
		foreach ($result as $umsatz){
			$TR_Umsatz[$umsatz['uliID']] =  ($size[$umsatz['uliID']] - $minSize) * 100 / ($maxSize - $minSize);
			settype($TR_Umsatz[$umsatz['uliID']], INT);
		}
	}
	if ($TR_Umsatz){return $TR_Umsatz;}
	else {return FALSE;}
}

/**
 * Gibt fuer eine Liga die Teamranking-Werte fuer den Gewinn aus
 * Es zaehlen die letzten beiden Jahre
 */
function TR_get_gewinn($leagueID){
	global $option;

	/* Holt das zweitletzte Jahr */
	$order[] = array("col" => "ID", "sort" => "DESC");
	$cond[] = array("col" => "parent", "value" => 0);
	$cond[] = array("col" => "end", "value" => mktime(), "func" => "<");
	$year = uli_get_var('years', $cond, "ID", $order);

	unset($cond);
	$tableString  = 'finances f ';
	$tableString .= ' LEFT JOIN '.$option['prefix'].'uli u ON u.ID = f.uliID';
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "type", "value" => 7);
	$cond[] = array("col" => "year", "value" => $year, "func" => ">=");
	$cond[] = array("col" => "u.leagueID", "value" => $leagueID);
	$result = uli_get_results($tableString, $cond, array('SUM(sum)', 'uliID'), NULL, NULL, 'GROUP by uliID');
	if ($result){
		foreach ($result as $gewinn){
			$size[$gewinn['uliID']] = $gewinn['SUM(sum)'];
		}
		$minSize = min($size);
		$maxSize = max($size);
		foreach ($result as $gewinn){
			$TR_Gewinn[$gewinn['uliID']] =  ($size[$gewinn['uliID']] - $minSize) * 100 / ($maxSize - $minSize);
			settype($TR_Gewinn[$gewinn['uliID']], INT);
		}
	}
	if ($TR_Gewinn){return $TR_Gewinn;}
	else {return FALSE;}
}




/**
 * Berechnet die TR Werte fuers Stadion
 * Im Moment nur die Groesse um da einen Wert zu haben
 */
function TR_Stadium($leagueID){
	global $option;
	$ulis = get_ulis($leagueID);


	if ($ulis){
		foreach ($ulis as $uli){
			$sum = 0;
			$stadium = get_stadium($uli['ID']);
			if ($stadium['infra']){
				foreach ($stadium['infra'] as $infra){
					$sum = $infra['sum'] + $sum;
				}
			}
			$sum = $sum + ($stadium['sitzplaetze'] / 5000 * 8000000) + ($stadium['stehplaetze'] / 8000 * 6000000) - 8000000;
				
			$stadium_value[$uli['ID']] = $sum;
				
			//$size[$stadium['uliID']] = $stadium['sitz'] + $stadium['steh'];
		}
		$minSize = min($stadium_value);
		$maxSize = max($stadium_value);
		foreach ($ulis as $uli){
			$TR_Stadium[$uli['ID']] =  ($stadium_value[$uli['ID']] - $minSize) * 100 / ($maxSize - $minSize);
			//$TR_Stadium[$stadium['uliID']] =  50;
				
			settype($TR_Stadium[$uli['ID']], INT);
		}
	}
	if ($TR_Stadium){return $TR_Stadium;}
	else {return FALSE;}
}



/**
 * liefert das TR f�r die ewige Tabelle
 */
function TR_Ewige_Tabelle($leagueID){
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$results = uli_get_results('results', $cond, array('SUM(score)', 'uliID'), NULL, NULL, 'GROUP by uliID');
	if($results){
		foreach ($results as $result){
			$score = $score + $result['SUM(score)'];
			$uliScore[$result['uliID']] = $result['SUM(score)'];
		}
		$maxScore = max($uliScore);
		$minScore = min($uliScore);

		foreach ($uliScore as $key => $result){
			$TR_EwigeTabelle[$key] = ($result - $minScore) * 100 / ($maxScore - $minScore);
			settype($TR_EwigeTabelle[$key], INT);
		}
	}
	if ($TR_EwigeTabelle){return $TR_EwigeTabelle;}
	else {return FALSE;}
}


/**
 * liefert das TR fuer die Spieler Leistung
 * gezaehlt werden die Durchschnittspunkte pro Jahr aller Spieler (Summe)
 */
function TR_Player_Points($leagueID){
	global $option;
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "uliID", "value" => 0, "func" => "!=");
	$result = uli_get_results(player_league, $cond);
	if ($result){
		foreach($result as $player){
			// Jetzt die Durchschnitt Punkte des Spielers berechnen
			$cond = array();
			$cond[] = array("col" => "playerID", "value" => $player['playerID']);
			$cond[] = array("col" => "round", "value" => 0);
			$scores = uli_get_results('player_points', $cond, array('SUM(score)', 'COUNT(ID)', 'playerID'));
			if ($scores){
				foreach ($scores as $scores){
					if ($scores['COUNT(ID)'] > 0){
						$score = $scores['SUM(score)']/$scores['COUNT(ID)'];
					}
				}
			}
			$avScore[$player['uliID']] = $avScore[$player['uliID']] + $score;
		}
	}
	if ($avScore){
		$minSize = min($avScore);
		$maxSize = max($avScore);
		foreach ($avScore as $key => $avScore){
			$TR_Player_Points[$key] = ($avScore - $minSize) * 100 / ($maxSize - $minSize);
			settype($TR_Player_Points[$key], INT);
		}
	}
	if ($TR_Player_Points){return $TR_Player_Points;}
	else {return FALSE;}
}
?>