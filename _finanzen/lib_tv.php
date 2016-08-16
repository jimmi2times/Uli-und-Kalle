<?php
/*
 * Created on 08.06.2009
 *
 * Library f�r die TV-Funktionen
 */

/* Bindet die Sprachdatei ein */
include('lang_finanzen.php');


/**
 * Managed die Verhandlungen zu TV-Vertr�gen
 *
 * Wenn Verhandlung --> Aktion Verhandlung
 * Wenn Unterschrift --> Geld �berweisen, andere Vertr�ge l�schen, unterschreiben
 * Wenn keine Vertr�ge vorhanden --> Angebote erzeugen
 * 08.06.09
 */

function print_tv_negotiation($year, $action, $contractID) {
global $option;
$uliID = $option['uliID'];

// Holt alle aktuellen Angebote f�r einen Manager */
$offers = get_tv_offers($uliID, $year);

/* Wenn keine Angebote vorhanden sind und das Jahr offen ist, werden neue erzeugt */
if (!$offers AND $option['currentchildyear'] == $year)
	{$offers = generate_tv_offers($uliID, $year);}

if ($offers){
	foreach($offers as $offer){
		if ($offer['ID'] == $contractID){$contract = $offer;}
		if ($offer['decision'] == 1){$signedcontract = $offer;}
	}}

/* Verhandlung */
if ($action == "negotiate")
	{
	$answer = negotiate_tv_contract($contract);
	$offers = get_tv_offers($uliID, $year);
	}
/* Vertrag unterschreiben */
if ($action == "sign")
	{
	sign_tv_contract($contract);
	$offers = get_tv_offers($uliID, $year);
	foreach($offers as $offer){
		if ($offer['decision'] == 1){$signedcontract = $offer;}
		}
	}
/* Unterschriebener Vertrag wird angezeigt */
if ($signedcontract){
	$content .= $signedcontract['name'];
	$content .= "<br/>\n";
	$content .= TVGesamt.': '.uli_money($signedcontract['sum']);
	$content .= "<br/>\n";
	$content .= TVPerPoint.': '.uli_money($signedcontract['perpoint']);
	$content .= "<br/>\n";
	$content .= TVSignedContractText;

	/* Ausgabe des aktuellen Vertrages */
	$html .= uli_box(YourTVPartner, $content);

	/* TODO Stats der Einnahmen im laufenden Jahr */
	}
/* wenn wir uns im aktuellen Halbjahr befinden, wird die Verhandlung ausgegeben */
elseif ($option['currentchildyear'] == $year) {
	// Ausgabe der drei Angebote
	foreach ($offers as $offer){
		$content .= '<div style="float:left; width: 30%; padding: 5px;">';
		$content .=  '<form action="?action=negotiate&year='.$year.'" method = "POST">';
		$content .=  get_tv_pic($offer['broadcaster']);
		$content .=  '<br/>';
		$content .=  '<b>'.$offer['name'].'</b>';
		$content .=  '<br/>';
		$content .=  '<br/>';
		$content .=  uli_money($offer['sum']).'/Halbjahr';
		$content .=  '<br/>';
		$content .=  uli_money($offer['perpoint']).'/Punkt';
		$content .=  '<br/>';
		$content .=  '<br/>';
		$content .=  get_smile_pic_tv($offer['status']);
		$content .=  ' <input type="submit" value="Verhandeln" ';
		if ($offer['decision'] == 9) {$content .=  ' disabled = "disabled" ';}
		$content .=  ' >';
		$content .=  '<input type="hidden" name = "contract" value="'.$offer['ID'].'">';
		$content .=  '</form>';
		$content .=  '<form action="?action=sign&year='.$year.'" method = "POST">';
		$content .=  '<input type="hidden" name = "contract" value="'.$offer['ID'].'">';
		$content .=  '<input type="submit" value="Unterschreiben" ';
		if ($offer['decision'] == 9) {$content .=  ' disabled = "disabled" ';}
		$content .=  ' >';
		$content .=  '</form>';
		$content .=  '<br/>';
		$content .=  '</div>';
		}
	// $html .=  '<img src="'.$CONFIG->wwwroot.'/theme/uli/graphics/transp.gif" width=400 height=1/>';
	$html .= uli_box(YourNegotiation, $content);

	/* TODO hier der Punkterechner hin */
	}
elseif ($option['currentchildyear'] != $year) {
	$html .= uli_box(Attention, LongTimeAgoTV);
	}
return $html;
}

/**
 * schreibt die Ausgangswerte f�r drei Vertr�ge in die Datenbank
 * Basis ist das TR
 * 08.06.09
 * // TODO vielleicht noch einmal die Summen ueberpruefen
 * prinzipiell scheint das zu funktionieren / Juli 2011
 */
function generate_tv_offers($uliID, $year) {
/* W�hlt einen zuf�lligen Broadcaster aus */
$broadcaster_one 	= get_broadcaster(1);
$broadcaster_two	= get_broadcaster(2);
$broadcaster_three 	= get_broadcaster(3);

/* Holt das Teamranking */
$TR = get_TR($uliID);

/* Ab einem Team Ranking von 50 steigt das Angebot bis auf das Doppelte */
if ($TR['TR_gesamt'] > 50)
	{$FaktorTR = ($TR['TR_gesamt'] - 50) * 1/50 + 1;}
	else {$FaktorTR = 1;}

/* Die drei Angebote */
$broadcaster_one['sum'] = (8500000 * $FaktorTR) * rand(80,120) / 100;
$broadcaster_one['perpoint'] = (5000 * $FaktorTR) * rand(80,120) / 100;
$broadcaster_two['sum'] = (6500000 * $FaktorTR) * rand(80,120) / 100;
$broadcaster_two['perpoint'] = (15000 * $FaktorTR) * rand(80,120) / 100;
$broadcaster_three['sum'] = (4000000 * $FaktorTR) * rand(80,120) / 100;
$broadcaster_three['perpoint'] = (35000 * $FaktorTR) * rand(80,120) / 100;

$value[] = array("col" => "sum", "value" => $broadcaster_one['sum']);
$value[] = array("col" => "perpoint", "value" => $broadcaster_one['perpoint']);
$value[] = array("col" => "year", "value" => $year);
$value[] = array("col" => "uliID", "value" => $uliID);
$value[] = array("col" => "status", "value" => 10);
$value[] = array("col" => "broadcaster", "value" => $broadcaster_one['ID']);
$broadcaster_oneID = uli_insert_record('tv_contracts', $value);

unset($value);
$value[] = array("col" => "sum", "value" => $broadcaster_two['sum']);
$value[] = array("col" => "perpoint", "value" => $broadcaster_two['perpoint']);
$value[] = array("col" => "year", "value" => $year);
$value[] = array("col" => "uliID", "value" => $uliID);
$value[] = array("col" => "status", "value" => 10);
$value[] = array("col" => "broadcaster", "value" => $broadcaster_two['ID']);
$broadcaster_twoID = uli_insert_record('tv_contracts', $value);

unset($value);
$value[] = array("col" => "sum", "value" => $broadcaster_three['sum']);
$value[] = array("col" => "perpoint", "value" => $broadcaster_three['perpoint']);
$value[] = array("col" => "year", "value" => $year);
$value[] = array("col" => "uliID", "value" => $uliID);
$value[] = array("col" => "status", "value" => 10);
$value[] = array("col" => "broadcaster", "value" => $broadcaster_three['ID']);
$broadcaster_threeID = uli_insert_record('tv_contracts', $value);

$offers = get_tv_offers($uliID, $year);
return $offers;
}

/**
 * holt zuf�llig einen broadcaster nach image sortiert
 * gibt ein array mit dem Broadcaster zur�ck
 * 08.06.09
 */
function get_broadcaster($image) {
$cond[] = array("col" => "image", "value" => $image);
$result = uli_get_results('tv_broadcaster', $cond);
$count = COUNT($result);
$zufall = rand(0, $count-1);
return $result[$zufall];
}


/**
 * gibt einen Smilie zur�ck
 * 08.06.09
 */
function get_smile_pic_tv($smile) {
global $option;
$pic = '<img src="'.$option['uliroot'].'/theme/graphics/icons/smile_';
if ($smile > 7) {$pic .= 'vh.gif" ';}
elseif ($smile > 5) {$pic .= 'h.gif" ';}
elseif ($smile > 3) {$pic .= 'm.gif" ';}
elseif ($smile > -1) {$pic .= 's.gif" ';}
$pic .= 'width=15 height=15 title = "Laune deines Verhandlungspartners"/>';
return $pic;
}

/**
 * f�hrt die Verhandlungen und gibt als Antwort ein Answer-Array zur�ck
 * 08.06.09
 */
function negotiate_tv_contract($contract) {
global $option;
/* Checks ob das alles OK ist) */
if ($contract['decision'] == 1) {return FALSE;}
if ($contract['decision'] == 9) {return FALSE;}
if ($contract['uliID'] != $option['uliID']) {return FALSE;}

/* Ab einem Status unter 5 steigt die wahrscheinlichkeit des scheiterns */
if ($contract['status'] < 5) {

	if (rand(0,$contract['status']) == 0) {
		$answer['decision'] = 9; /* 9 = gescheitert */
		$value[] = array("col" => "decision", "value" => 9);
		$cond[]  = array("col" => "ID", "value" => $contract['contractID']);
		uli_update_record('tv_contracts', $cond, $value);
		return $answer;
	}}

$answer['sum'] = $contract['sum'] * rand(100,110) / 100;
$answer['perpoint'] = $contract['perpoint'] * rand(100,110)/100;
$answer['status'] = $contract['status'] - rand(1,2);
$answer['ID'] = $contract['contractID'];
$answer['decision'] = 0;
update_tv_contract($answer);
return $answer;
}


/**
 * Aktualisiert einen Eintrag in der TV Contract Tabelle
 * 08.06.09
 */
function update_tv_contract($contract) {
foreach ($contract as $key => $value){
	$values[] = array("col" => $key, "value" => $value);
	}
$cond[] = array("col" => "ID", "value" => $contract['ID']);
uli_update_record('tv_contracts', $cond, $values);
}



/**
 * unterzeichnet einen TV-Vertrag
 * �berweist die Kohle und schreibt alles �berall hin
 * l�scht alle anderen Angebote
 * 11.06.09
 */
function sign_tv_contract($contract) {
global $option;

/* Checks ob das alles OK ist) */
if ($contract['decision'] == 1) {return FALSE;}
if ($contract['decision'] == 9) {return FALSE;}
if ($contract['uliID'] != $option['uliID']) {return FALSE;}

/* schreibt den Vertrag als unterschrieben */
$cond[] = array("col" => "ID", "value" => $contract['contractID']);
$value[]= array("col" => "decision", "value" => 1);
uli_update_record('tv_contracts', $cond, $value);

unset($cond);
$cond[] = array("col" => "uliID", "value" => $contract['uliID']);
$cond[] = array("col" => "year", "value" => $contract['year']);
$cond[] = array("col" => "decision", "value" => 1, "func" => "!=");
uli_delete_record('tv_contracts', $cond);


// Das Parent Jahr holen
unset($cond);
$cond[] = array("col" => "parent", "value" => $contract['year']);
$year = uli_get_var('years', $cond, 'ID');



/* schreibt bzw aktualisiert den einmal betrag in die finance tabelle */
calculate_money(16, $contract['sum'], $contract['uliID'], 0, $year, 'add', $type='income');

/* legt einen Eintrag in der Kontoauszugstabelle an */
write_kontoauszug(16, $contract['sum'], $contract['uliID'], mktime());
}


/**
 * Holt alle Angebote f�r einen Manager und ein Jahr
 * Mit JOIN wird gleich der Name mit aus der TV_broadcasters Tabelle geholt
 * 08.06.09
 */
function get_tv_offers($uliID, $year) {
global $option;
$tablestring = 'tv_contracts tvc LEFT JOIN '.$option['prefix'].'uli_tv_broadcaster tvb ON tvb.ID = tvc.broadcaster';
$cond[] = array("col" => "year", "value" => $year);
$cond[] = array("col" => "uliID", "value" => $uliID);
$order[] = array("col" => "tvc.ID", "sort" => "ASC");
$result = uli_get_results($tablestring, $cond, array('tvc.ID AS contractID','tvc.*', 'tvb.*'), $order);
if($result){return $result;}
else {return FALSE;}
}


function get_tv_pic($ID){
global $option;
$html .= '<img src="'.$option['uliroot'].'/theme/graphics/broadcaster/'.$ID.'.jpg" border="1">';
return $html;
}
?>
