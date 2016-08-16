<?php
/* PRINT FUNKTIONEN */

/**
 * Gibt den Kopf einer Seite aus
 *
 * �bergeben wird die Seite
 * --> Hier werden die speziell f�r diese Seite ben�tigten Bibliotheken eingelesen
 * --> Hier werden die Ajax Funktionen f�r die Seite referenziert
 *
 * Dann wird die Theme-Datei gestartet
 *
 *
 */
function uli_header($libs ='', $ajaxfuncs='', $javascripts='') {
	global $option, $page;
	/* Einbinden der Libs */
	if ($libs){
		foreach ($libs as $lib){
			include($lib.'.php');
		}}
		require_once(ABSPATH.$option['ulidir'].'/theme/header.php');
}

/** Gibt den Fuss einer Seite aus
 *
 */
function uli_footer() {
	global $option;
	require_once(ABSPATH.$option['ulidir'].'/theme/footer.php');
}

/** Gibt das Men� der Kopfzeile aus
 *
 */
function print_header_menu($page) {
	global $option;
	$newMessages = '';
	if ($option['uliID']){
		$newMessages = get_new_messages($option['uliID']);
		if ($newMessages){
			$newMessageString = ' <span style="color: white">('.count($newMessages).')</span>';
		}
	}
	$menu[] = array("name" => START, "link" => "/index.php", "id" => "start");
	$menu[] = array("name" => FINANZEN, "link" => "/_finanzen/bank.php", "id" => "finanzen");
	$menu[] = array("name" => TRANSFERMARKT, "link" => "/_transfermarkt/transfermarkt.php", "id" => "transfermarkt");
	$menu[] = array("name" => AUFMPLATZ, "link" => "/_kabine/kabine.php", "id" => "kabine");
	$menu[] = array("name" => STADION, "link" => "/_stadion/stadion.php", "id" => "stadion");
	$menu[] = array("name" => STATISTIKEN, "link" => "/_stats/stats.php", "id" => "stats");
	$menu[] = array("name" => INBOX.$newMessageString, "link" => "/_start/communicate.php", "id" => "communicator");
	$menu[] = array("name" => ZUMTIP, "link" => "/../");

	echo '<ul class="menu">';
	echo "\n";
	foreach ($menu as $menu){
		$cssclass = '';
		if ($menu['id'] == $page['main']){$cssclass = 'current_';}
		echo '<li class="'.$cssclass.'page_item"><a href="'.$option['uliroot'].$menu['link'].'">'.$menu['name'].'</a></li>';
		echo "\n";
	}
	echo '</ul>';
	echo "\n\n";
}

/**
 * Gibt das Men� in der Fu�zeile aus
 */

function print_footer_menu() {
	global $option, $page;

	$menu['transfermarkt'][] = array("name" => TRANSFERLISTE, "link" => "/_transfermarkt/transfermarkt.php", "id" => "transfermarkt");
	$menu['transfermarkt'][] = array("name" => MEINKADER, "link" => "/_transfermarkt/kader.php", "id" => "kader");
	$menu['transfermarkt'][] = array("name" => SPIONAGE, "link" => "/_transfermarkt/spionage.php", "id" => "spionage");

	$menu['finanzen'][] = array("name" => BANK, "link" => "/_finanzen/bank.php", "id" => "bank");
	$menu['finanzen'][] = array("name" => FERNSEHEN, "link" => "/_finanzen/tv.php", "id" => "tv");
	$menu['finanzen'][] = array("name" => SPONSORING, "link" => "/_finanzen/sponsoring.php", "id" => "sponsoring");

	//$menu['start'][] = array("name" => , "link" => "/_start/communicate.php", "id" => "communicate");
	// $menu['start'][] = array("name" => COMMUNICATOR, "link" => "/_start/communicate.php", "id" => "communicator");
	$menu['start'][] = array("name" => OPTIONEN, "link" => "/_start/options.php", "id" => "optionen");



	if ($menu[$page['main']]){
		echo '<ul class="menu footer">';
		echo "\n";

		foreach ($menu[$page['main']] as $menu){
			$cssclass = '';
			if ($menu['id'] == $page['sub']){$cssclass = 'current_';}
			echo '<li class="'.$cssclass.'page_item"><a href="'.$option['uliroot'].$menu['link'].'">'.$menu['name'].'</a></li>';
			echo "\n";
		}
		echo '</ul>';
	}
	echo "\n\n";
}

/**
 * Gibt das Men� f�r die Jahre aus
 * Markiert das aktive Jahr
 * 22.05.09
 */
function print_year_menue($year, $view=''){
	global $option;
	$uliyears = get_uli_years();
	$html .= "\n";
	if ($uliyears){
		foreach ($uliyears as $uliyear){
			$active = '';
			if ($year == $uliyear['ID']){$active = 'active';}
			$html .= '<a href="?year='.$uliyear['ID'].'&amp;view='.$view.'" class="'.$active.'">'.$uliyear['name'].'</a>';
			$html .= '<br/>';
			$html .= "\n";
		}}
		$html .= "\n";
		$html = uli_box(ChoseYear, $html);
		return $html;
}


/**
 * Gibt das Men� f�r die Unterjahre aus
 * Markiert das aktive Jahr
 * 22.05.09
 */
function print_child_year_menue($year, $view='', $startyear){
	global $option;
	$uliyears = get_uli_child_years();
	$html .= "\n";
	if ($uliyears){
		foreach ($uliyears as $uliyear){
			if ($uliyear['ID'] >= $startyear){
				$active = '';
				if ($year == $uliyear['ID']){$active = 'active';}
				$html .= '<a href="?year='.$uliyear['ID'].'&amp;view='.$view.'" class="'.$active.'">'.$uliyear['name'].'</a>';
				$html .= '<br/>';
				$html .= "\n";
			}}}
			$html .= "\n";
			$html = uli_box(ChoseYear, $html);
			return $html;
}


/* Was muss ausgegeben werden */
/* Tabelle (sortierbar) */
/* Message (Ajax und normal) */
/* Panel */
/* div-Box */

/**
 * Die Haupttabellenfunktion
 */
function uli_table($tableHeader, $tableData, $cssClass){
	global $option;

	$html .= '<table id = "ulitable" class="ulitable '.$cssClass.'" cellspacing = "0" cellpadding = "0" >';
	$html .= "\n";
	/* Kopf */
	$html .= '<thead>';

	$html .= '<tr class="headline">';
	$html .= "\n";
	foreach ($tableHeader as $Header){
		$html .= '<th class="headline">';
		$html .= $Header.'&nbsp;';
		$html .= '</th>';
		$html .= "\n";
	}
	$html .= '</tr>';
	$html .= "\n";
	$html .= '</thead>';

	/* Data */
	$html .= '<tbody>';
	foreach ($tableData as $Row){
		$html .= '<tr class="content">';
		$html .= "\n";
		foreach ($Row as $Column){
			$html .= '<td class="content">';
			$html .= $Column.'&nbsp;';
			$html .= '</td>';
			$html .= "\n";
		}
		$html .= '</tr>';
		$html .= "\n";
	}
	$html .= '<t/body>';
	$html .= '</table>';
	$html .= "\n";
	return $html;
}








/**
 * Funktion zum initialisieren eines Panels
 */
function uli_panel(){

	return $html;
}




/**
 * gibt den Link zu einem Hilfthema �ber das Icon aus
 *
 */
function print_help_icon($helptopic){


}


/**
 * gibt ein sch�n formatiertes Datum aus einem Timestamp zur�ck
 * wenn time = 1, dann auch die Uhrzeit
 * 
 * 
 * das ist diese schei�e mit der sommer und winterzeit und der nicht exakten uhrzeit.
 */
function uli_date($timestamp, $time = '', $nobr = ''){

	date_default_timezone_set('Europe/Berlin');
	
	$datumString = date("d.m.y",$timestamp);

	if ($time == 1){
		if (!$nobr){
			$timeString .= '<br/>';
		}
		else {
			$timeString .= ' | ';
		}
		$timeString .= date("G", $timestamp).':'.date("i", $timestamp).' Uhr';
	}
	$html = $datumString.$timeString;
	return $html;
}

/**
 * Gibt eine sch�n formatierte Zahl mit Euro zur�ck
 */
function uli_money($sum){
	if ($sum != 0){
		$html = number_format($sum,0, ",", ".").' &euro;';
	}
	return $html;
}



/**
 * Eine typische Infobox
 * Header, Content, Footer
 * Klasse (wird dazu addiert)
 * Zur�ck kommt der HTML Code
 * Wenn ein Hilfethema �bergeben wird, wird das Hilfe Icon mit Link zum Help-Panel ausgegeben
 * 23.04.09
 */
function uli_box($headline='', $content='', $helptopic='', $class = '', $footer= '', $overflow = ''){

	$html.= '<div class="ulibox '.$class.'">';
	$html .= "\n";
	$html .= '<div class="headline">';
	$html .= "\n";
	$html .= '<h3>'.$headline.'</h3>';
	if ($helptopic){print_help_icon($helptopic);}
	$html .= "\n";
	$html .= '</div>';
	$html .= "\n";
	if($content){
		$html .= '<div class="content '.$overflow.'">';
		$html .= "\n";
		$html .= $content;
		$html .= "\n";
		$html .= '</div>';
	}

	$html .= "\n";
	if ($footer){
		$html .= '<div class="footer">';
		$html .= "\n";
		$html .= $footer;
		$html .= "\n";
		$html .= '</div>';
		$html .= "\n";
	}
	$html .= '</div>';
	$html .= "\n\n";
	return $html;
}

/**
 * Eine Nachricht
 *
 */
function uli_message(){

	return $html;
}

/**
 * Eine Men� mit Jahren und Runden
 */
function uli_round_menu(){

	return $html;
}


/* Formularbaukasten */

/**
 * erzeugt den html code eines formularanfangs
 * 31.03.09
 */
function uli_start_form($name='', $action='', $method='', $class='', $id = ''){
	if ($name){$namestring = 'name = "'.$name.'" ';}
	if ($class){$classstring = 'class = "'.$class.'" ';}
	if (!$method){$methodstring = 'method = "post" ';}
	else {$methodstring = 'method="'.$method.'" ';}
	if ($id){$idstring = 'id="'.$id.'" ';}
	$html = '<form '.$namestring.$methodstring.'action = "'.$action.'" '.$classstring.$idstring.'>';
	$html .= "\n";
	return $html;
}

/**
 * erzeugt das Ende eines Formulars
 * wenn kein submitvalue �bergeben wird, wird kein submitfeld ausgegeben
 * mit action kann eine javascript action �bergeben werden
 * (bsp. $action = 'onclick = "xajax_get_info()"');
 * 31.03.09
 */
function uli_end_form($submitvalue='', $class='', $action=''){
	if ($submitvalue){
		$submitstring = '<input type = "submit" value="'.$submitvalue.'" ';
		if ($class){$submitstring .= ' class = "'.$class.'" ';}
		if ($action){$submitstring .= $action;}
		$submitstring .= '/>';
	}
	$html = $submitstring;
	$html .= "\n";
	$html .= '</form>';
	$html .= "\n";
	return $html;
}


/**
 * gibt den html code eines input feldes aus
 *  mit action kann eine javascript action �bergeben werden
 * (bsp. $action = 'onclick = "xajax_get_info()"');
 * 31.03.09
 */
function uli_input($type, $name, $value = '', $class = '', $size = '', $maxsize = '', $attributes ='', $actions = ''){
	if (!$type){return FALSE;}
	if (!$name){return FALSE;}
	$typestring = 'type ="'.$type.'" ';
	$namestring = 'name ="'.$name.'" ';
	if ($value)    {$valuestring    = 'value = "'.$value.'" ';}
	if ($class)    {$classstring    = 'class = "'.$class.'" ';}
	if ($size)     {$sizestring     = 'size = "'.$size.'" ';}
	if ($maxsize)  {$maxsizestring  = 'maxlength = "'.$maxsize.'" ';}
	if ($attributes){$attributesstring = $attributes.' ';}
	if ($actions)  {$actionsstring  = $actions.' ';}
	$html = '<input '.$namestring.$typestring.$valuestring.$classstring.$sizestring.$maxsizestring.$attributesstring.$actionsstring.'/>';
	$html .= "\n";
	return $html;
}


/**
 * gibt den html code einer textarea aus
 * 31.03.09
 */
function uli_textarea($name, $value = '', $class ='', $cols ='', $rows ='', $attributes ='', $actions = ''){
	if (!$name){return FALSE;}
	$namestring = 'name ="'.$name.'" ';
	if ($class)    {$classstring    = 'class = "'.$class.'" ';}
	if ($cols)     {$colsstring     = 'cols = "'.$cols.'" ';}
	if ($rows)     {$rowsizestring  = 'rows = "'.$rows.'" ';}
	if ($attributes){$attributesstring = $attributes.' ';}
	if ($actions)  {$actionsstring  = $actions.' ';}
	$html = '<textarea '.$namestring.$classstring.$colsstring.$rowsizestring.$attributesstring.$actionsstring.'>';
	$html .= $value;
	$html .= '</textarea>';
	$html .= "\n";
	return $html;
}


?>