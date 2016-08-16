<?php
/*
 * Created on 18.03.2009
 * Hier stehen alle Funktionen, die mit Geld zu tun haben und immer ben�tigt werden
 */



/* Jegliche Art von Geldberechnung */

/**
 * F�hrt Transaktionen durch
Tabelle uli_finances
�bergeben wird die Summe, die Runde, Die Art der Transaktion, plus/minus, addieren oder neu

1. Schreiben des Rundenwertes
2. Update des Jahreswertes/Einzel durch Addition
3. Update der Jahreseinnahmen/Ausgaben durch Addition
4. Update des Kontostandes
 
Gesamtpr�fung �ber Admin
 1 = Geh&auml;lter
 2 = Zuschauereinnahmen
 3 =  Stehpl&auml;tze Zuschauer
 4 =  Sitzpl&auml;tze Zuschauer
 5 = Zinsen (0) (echte Kredite)
 6 = Pr&auml;mien 
 7 = Saldo (0) / Year
 8 = Einnahmen Gesamt (0)
 9 = Ausgaben Gesamt (0)
 10 = Transfereinnahmen (0)
 11 = Transferausgaben (0)
 12 = Stadionbau (0)
 13 = Dispozinsen
 14 = Kontostand ohne Jahr
 15 = Merchandising
 16 = TV Einnahmen Jahresbetrag
 17 = TV Einnahmen Erfolgsabh�ngig
 18 = Sponsoring Gesamt (0)
 19 = Sponsoring Base
 20 = Sponsoring pro Punkt (0)
 21 = Sponsoring pr�mie
 22 = Sponsoring Audience (0)
 23 = Sponsoring Meister (0)
 24 = Sponsoring Top 5 (0)
 25 = Praemie fuer gutes Wirtschaften (0)
 26 = Einnahmen Verkauf Stadionname (Year)
 27 = Einnamen Catering
 
 * @param int $uliID
 * @param int $value welcher Wert 
 * @param int $sum Betrag
 *  
 * */

function calculate_money($value, $sum, $uliID, $round, $year='', $action='', $type=''){
global $option;
// echo 'calculate money';
if (!$year){$year = $option['currentyear'];}
if (!$type){
	if ($value == "25" OR $value == "2" OR $value == "6" OR $value == "10" OR $value == "15" OR $value == "16" OR $value == "17" OR $value == "18" OR $value == "19" OR $value == "20" OR $value == "21" OR $value == "22" OR $value == "23" OR $value == "24" OR $value == "26" OR $value == "27"){$type = "income";}
	if ($value == "1" OR $value == "5" OR $value == "11" OR $value == "12" OR $value == "13"){$type =	"outgoings";}
}
if (!$action){
	if ($round != 0){$action = 'new';}
	if ($round == 0){$action = 'add';}
}

$old_sum = get_value_bank($value, $round, $year, $uliID);
if ($action == "new"){$new_sum = $sum;}
if ($action == "add"){$new_sum = $sum + $old_sum;}	
$difference = $new_sum - $old_sum; 

if (!get_value_bank($value, $round, $year, $uliID))
	{insert_single_value_finance($value, $new_sum, $uliID, $round, $year);}
else{update_single_value_finance($value, $new_sum, $uliID, $round, $year);}		

if ($round != 0 AND $difference != 0)
	{
	// Updaten der Jahreswerte des Values
	$old_year_sum = get_value_bank($value, 0, $year, $uliID);
	$new_year_sum = $old_year_sum + $difference;
	if (!get_value_bank($value, 0, $year, $uliID))
		{insert_single_value_finance($value, $new_year_sum, $uliID, 0, $year);}
	else{update_single_value_finance($value, $new_year_sum, $uliID, 0, $year);}				
	}

if ($difference != 0)
	{
	// Updaten der Jahreswerte Einnahmen/Ausgaben
	if ($type == "income"){
		$new_einnahmen = get_value_bank(8, 0, $year, $uliID) + $difference;
		if (!get_value_bank(8, 0, $year, $uliID))
			{insert_single_value_finance(8, $new_einnahmen, $uliID, 0, $year);}
		else {update_single_value_finance(8, $new_einnahmen, $uliID, 0, $year);}
		}
	if ($type == "outgoings"){
		$new_ausgaben = get_value_bank(9, 0, $year, $uliID) + $difference;
		if (!get_value_bank(9, 0, $year, $uliID))
			{insert_single_value_finance(9, $new_ausgaben, $uliID, 0, $year);}
		else {update_single_value_finance(9, $new_ausgaben, $uliID, 0, $year);}						
		$difference = -1*$difference;
		}
	// Saldo
	$new_saldo = get_value_bank(7, 0, $year, $uliID) + $difference;
	if (!get_value_bank(7, 0, $year, $uliID))
		{insert_single_value_finance(7, $new_saldo, $uliID, 0, $year);}
	else {update_single_value_finance(7, $new_saldo, $uliID, 0, $year);}					
	// Updaten des Gesamtkontostandes
	}
$new_kontostand = get_value_bank(14, 0, 0, $uliID) + $difference;		
if (!get_value_bank(14, 0, 0, $uliID))
	{insert_single_value_finance(14, $new_kontostand, $uliID, 0, 0);}
else {update_single_value_finance(14, $new_kontostand, $uliID, 0, 0);}	
}
 
 
 
/** 
 * schreibt einen Eintrag in die seit 2009 neue Tabelle Kontoauszug
 * 11.06.09
 */
function write_kontoauszug($type, $sum, $uliID, $time) {
$value[]= array("col" => "type", "value" => $type);
$value[]= array("col" => "sum", "value" => $sum);
$value[]= array("col" => "uliID", "value" => $uliID);
$value[]= array("col" => "timestamp", "value" => $time);	
uli_insert_record('kontoauszug', $value);
} 
 

/* Geh�lter */

/**
 * Berechnet zum Zeitpunkt jetzt die Geh�lter eines UliTeams
 * 22.03.09
 */
function calculate_gehaelter($uliID) {
$cond[] = array ("col" => "uliID", "value" => $uliID);
$cond[] = array ("col" => "archived", "value" => 0);
$result = uli_get_var('kredite',$cond, 'SUM(sum)');
if ($result){return $result;}
else {return FALSE;}
}




/* Kredite */
/**
 * checkt ob die kredite abgelaufen sind
 * wenn ein Kredit abgelaufen ist, wird er zur�ckgezahlt
 * Bei jedem Aufruf
 * 18.03.09
 * 
 */ 
function check_credits() {
$timestamp = mktime();
$cond[] = array ("col" => "end", "value" => $timestamp, "func" => "<");
$cond[] = array ("col" => "paid", "value" => 0);
$result = uli_get_results('kredite',$cond);
//print_r($result);
if($result){
	foreach ($result as $kredit){
	pay_back_kredit($kredit);
	}}
}

/**
 * zahlt einen kredit zur�ck
 * berechnet die zinsen
 * aktualisiert alle finanzfelder
 * 22.03.09
 * �BERARBEITEN WENN DIE LEUTE SICH GEGENSEITIG KREDITE GEBEN K�NNEN
 */
function pay_back_kredit($kredit) {
global $option;
/* Berechnung der Zinsen */
$timestamp = mktime();
$duration = $timestamp - $kredit['start'];
$days = $duration / 60 / 60 / 24; settype($days, "INT");
$zinsen = $kredit['sum'] * $kredit['percent'] / 100 * $days / 365; settype($zinsen, "INT");
$uliID = $kredit['toklub'];
/* Zinsen abziehen */
calculate_money(5, $zinsen, $uliID, 0, $option['currentyear'], $action='add', $type='outgoings');
/* kredit abziehen */
$new_kontostand = get_value_bank(14, 0, 0, $uliID) - $kredit['sum'];		
if (!get_value_bank(14, 0, 0, $uliID))
	{insert_single_value_finance(14, $new_kontostand, $uliID, 0, 0);}
else {update_single_value_finance(14, $new_kontostand, $uliID, 0, 0);}	
/* Kredit als bezahlt setzen */
$cond[] = array("col" => "ID", "value" => $kredit['ID']);	
$value[] = array("col" => "paid", "value" => 1);
uli_update_record('kredite', $cond, $value);
}

/**
 * Kreditbelastung eines Klubs
 * Es werden alle aktiven Kredite zusammengerechnet
 * wird auch beim Bietrahmen ben�tigt
 * 21.03.09
 */
function get_all_kredite($uliID) {
global $option;
$cond[] = array("col" => "toklub", "value" => $uliID);		
$cond[] = array("col" => "paid", "value" => 0);	
$result = uli_get_var('kredite', $cond, 'SUM(sum)');
if ($result){return $result;}
else {return FALSE;}
}
/** 
 * holt den Kreditrahmen
 *  Wird nach Teamranking berechnet
 *  TR Zahl mal 1 Mio
 *  
 */
function get_kreditrahmen($uliID) {
$TR = get_TR($uliID);	
$kreditrahmen = $TR['TR_gesamt'] * 1500000;
return $kreditrahmen;
}


/* Grundfunktionen */
/** 
 * Holt einen einzelnen Wert aus der Finance Tabelle 
 * Liefert den Wert zur�ck
 * 21.03.09
 */
function get_value_bank($type, $round, $year, $uliID) {
$cond[] = array("col" => "uliID", "value" => $uliID);		
$cond[] = array("col" => "type", "value" => $type);	
$cond[] = array("col" => "round", "value" => $round);	
$cond[] = array("col" => "year", "value" => $year);	
$result = uli_get_var('finances', $cond, 'sum');
if ($result){return $result;}
else {return FALSE;}
}

/**
 * Aktualisiert einen einzelnen Wert aus der Finanztabelle
 * 22.03.09
 */
function update_single_value_finance($type, $sum, $uliID, $round, $year) {
$cond[] = array("col" => "uliID", "value" => $uliID);		
$cond[] = array("col" => "type", "value" => $type);	
$cond[] = array("col" => "round", "value" => $round);	
$cond[] = array("col" => "year", "value" => $year);	
$value[] = array("col" => "sum", "value" => $sum);
if(uli_update_record('finances', $cond, $value)){return TRUE;}
else {return FALSE;}
}

/** Tr�gt einen Wert in die Finanztabelle ein
 * 22.03.09
 */
function insert_single_value_finance($type, $sum, $uliID, $round, $year) {
$value[] = array("col" => "sum", "value" => $sum);
$value[] = array("col" => "uliID", "value" => $uliID);
$value[] = array("col" => "type", "value" => $type);
$value[] = array("col" => "round", "value" => $round);
$value[] = array("col" => "year", "value" => $year);
if(uli_insert_record('finances', $value)){return TRUE;}
else {return FALSE;}
}

?>
