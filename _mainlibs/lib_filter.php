<?php
/*
 * Created on 24.04.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */



/**
 * holt einen Filter aus der DB
 * zerlegt den string in ein handliches array
 * NEU CHECKEN !!! wenn nur ein eintrag drinne ist, wird ein string �bergeben !!!
 * 19.05.09
 */
function get_filter($uliID, $filter, $noarray = ''){
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "type", "value" => $filter);
	$result = uli_get_row('filter', $cond);
	if(!$noarray AND $result){
		$filter = explode('|', $result['value']);
	}
	if ($noarray AND $result)
	{$filter = $result['value'];}
	if ($filter AND $result){return $filter;}
	else {return FALSE;}
}

/**
 * gibt den html-code f�r eine Sortierung-Auswahl aus
 * Die Sortierung wird per Array �bergeben
 */
function print_sortierung($sort, $type, $container, $content, $sortnames){
	global $option;
	//$sortFilterArray = get_filter($option['uliID'], $type);
	//$sortFilter = $sortFilterArray[0]; /* hier steht immer nur ein Wert drinne */

	/* Wenn es keinen Eintrag gibt, wird er hier erzeugt */
	/*
	if (!$sortFilter){
		$value[] = array("col" => "uliID", "value" => $option['uliID']);
		$value[] = array("col" => "type", "value" => $type);
		$value[] = array("col" => "value", "value" => $sort[0]);
		//uli_insert_record('filter', $value);
		$sortFilter = $sort[0];
	}
	*/
	if ($sort){
		$html .= Sort.': ';
		$html .= '<form>';
		$html .= "\n";
		$x = 0;
		foreach($sort as $sort){
			$html .= '<input type="radio" name="sort" class="sortradio" id="'.$sort.'" value="'.$sort.'" '.$checked.' '.$action.'> ';
			$html .= $sortnames[$x].' ';
			$html .= "\n";
			$x = $x + 1;
		}
		$html .= '</form>';
		$html .= "\n";
	}
	return $html;
}




/**
 * gibt die filter f�r die positionen aus
 * wenn keine Nutzereinstellung gefunden wird, wird eine angelegt
 * Standard ist ALLE AN
 * 16.04.09
 */
function print_filter_positions(){
	global $option;
	$html .= Positions.': ';
	$html .= "\n";
	for ($x=1; $x<=7; $x++){
		$html .= '<input id = "position'.$x.'" class="filtercheckbox" type = "checkbox" checked = "checked" value = "'.$x.'">';
		$html .= ' '.$option['position'.$x.'-2'];
		$html .= "\n";
	}
	return $html;
}


/**
 * gibt die checkbox zum aktivieren der transferliste aus
 * wenn keine Nutzereinstellung gefunden wird, wird eine angelegt
 * Standard ist AUS
 * 17.04.09
 */
function print_filter_transferliste(){
	global $option;
	///* Holt den aktuellen Filter */
	//$listFilter = get_filter($option['uliID'], 'ListOn');
	///* Wenn es keinen Eintrag gibt, wird er hier erzeugt */
	//if (!$listFilter){
	//	$value[] = array("col" => "uliID", "value" => $option['uliID']);
	//	$value[] = array("col" => "type", "value" => "ListOn");
	//	$value[] = array("col" => "value", "value" => "");
	//	uli_insert_record('filter', $value);
	//	$listFilter = array();
	//	}
	//	if(in_array(1, $listFilter))
	//		{$checkarray[1] = 'checked = "checked" ';}
	//	$actionarray[1] = 'onchange = "xajax_change_filter(\''.$option['uliID'].'\', \'ListOn\', \'1\', \'transfermarkt\', \'transfermarkt\')"';
	//$html .= '<form>';
	$html .= '<input type = "checkbox" id="TransListCheckBox">';
	$html .= ' '.TransListOn;
	$html .= "\n";
	//$html .= '</form>';
	return $html;
}

/** gibt die Filterboxen f�r das Alter aus
 * wenn keine Nutzereinstellung gefunden wird, wird eine angelegt
 * Standard ist alle
 * 22.04.09
 */
function print_filter_age(){
	global $option;

	for ($x=1; $x<=4; $x++){
		$html .= '<input class="filtercheckbox" type = "checkbox" value = "'.$x.'"  id = "age'.$x.'" checked = "checked">';
		$html .= ' '.$option['age'.$x.'-2'];
		$html .= "\n";
	}
	return $html;
}

/** gibt die Filter f�r das Bundesligateam aus
 * wenn keine Nutzereinstellung gefunden wird eine angelegt
 * Standard ist "alle"
 *
 *
 * ACHTUNG
 * http://bytes.com/topic/javascript/answers/479399-onclick-option-not-working-ie-safari
 * http://www.webdeveloper.com/forum/showthread.php?t=193505
 * SAFARI und IE Problem bei Onclick auf options
 *
 * ERLEDIGT
 *
 * MUSS EVENTUELL NOCH AUF ANDERE BEREICHE UEBERTRAGEN WERDEN (TRIKOTNUMMERN, etc.)
 *
 */
function print_filter_team(){
	global $option;
	$html .= '<select class="filterform selectfilter" name ="teamFilter">';
	$html .= "\n";
	$html .= '<option value="all">'.AllTeams.'</option>';
	$html .= "\n";
	$teamnames = get_all_team_names();
	foreach ($teamnames as $key => $teamname){
		if ($key != 999){
			$html .= '<option value="team'.$key.'">'.$teamname.'</option>';
			$html .= "\n";
		}
	}
	$html .= '</select>';
	$html .= "\n";
	return $html;
}





/**
 * XAJAX FUNKTION
 * wechselt die filter
 * speichert die neuen Einstellungen in der Filter Tabelle
 * l�dt den Inhalt neu
 * wenn clear = 1, dann wird der Wert nicht hinzugeschrieben sondern alle anderen vorher gel�scht
 * 18.04.09
 *
 */
function change_filter($uliID, $type, $value, $divcontainer, $content, $clear=''){
	$objResponse = new xajaxResponse();

	/* Filter geholt */
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "type", "value" => $type);
	$filter = uli_get_var('filter', $cond, 'value');

	/* Filter String wird bearbeitet */
	$newfilter = str_replace(array ("|".$value,$value."|", $value, " "), "", $filter, $count);
	if ($count == 0 AND strlen($filter) > 0){$newfilter = $filter.'|'.$value;}
	if ($count == 0 AND strlen($filter) == 0){$newfilter = $value;}

	/* Wenn clear gesetzt */
	if ($clear == 1){$newfilter = $value;}

	/* neuer Wert wird geschrieben */
	$newvalue = array();
	$newvalue[] = array("col" => "value", "value" => $newfilter);
	uli_update_record('filter', $cond, $newvalue);

	/* Inhalt wird dynamisch nachgeladen und platziert */
	if ($content == "transfermarkt"){$html = print_transfermarkt();}
	$objResponse->assign($divcontainer, 'innerHTML', $html);
	return $objResponse;
}



/**
 * XAJAX FUNKTION
 * wechselt die Sortierung
 * speichert die neuen Einstellungen in der Filter Tabelle
 * l�dt den Inhalt neu
 * 18.04.09
 *
 */
function change_sort($uliID, $type, $value, $divcontainer, $content){
	$objResponse = new xajaxResponse();
	/* neuer Wert wird geschrieben */
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "type", "value" => $type);
	$newvalue[] = array("col" => "value", "value" => $value);
	uli_update_record('filter', $cond, $newvalue);

	/* Inhalt wird dynamisch nachgeladen und platziert */
	if ($content == "kader"){$html = print_kader();}
	$objResponse->assign($divcontainer, 'innerHTML', $html);
	return $objResponse;
}


/**
 * Setzt einen Eintrag in der Filtertabelle f�r die Ansicht
 * �berpr�ft vorher ob ein Eintrag vorhanden ist.
 * 19.05.09
 */
function set_view_filter($uliID, $value, $type){
	global $option;
	/* Holt den aktuellen Filter */
	$Filter = get_filter($uliID, $type, 1);
	/* Wenn es keinen Eintrag gibt, wird er hier erzeugt */
	if (!$Filter){
		$values[] = array("col" => "uliID", "value" => $uliID);
		$values[] = array("col" => "type", "value" => $type);
		$values[] = array("col" => "value", "value" => $value);
		uli_insert_record('filter', $values);
	}
	else {
		$cond[] = array("col" => "uliID", "value" => $uliID);
		$cond[] = array("col" => "type", "value" => $type);
		$newvalue = array();
		$newvalue[] = array("col" => "value", "value" => $value);
		uli_update_record('filter', $cond, $newvalue);
	}
}


?>
