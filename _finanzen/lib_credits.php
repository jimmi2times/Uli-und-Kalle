<?php
/*
 * Created on 24.03.2009
 *
 * Hier stehen die Funktionen nur für Kredite
 */


/** 
 * TODO: Komplett überarbeiten mit der Möglichkeit sich gegeneinander Kredite zu geben
 * Gibt den Kreditbereich aus
 * 
 */

function print_kreditabteilung() {
//global $user_ID, $wpdb, $CONFIG;
//$uliID = get_uliID($user_ID);
//$kreditrahmen = get_kreditrahmen($uliID);
//$laufendekredite = get_all_kredite($uliID);
//$kreditrahmen = $kreditrahmen - $laufendekredite;
//if ($kreditrahmen < 0){$kreditrahmen = 0;}
//
//$header = array('Kreditabteilung');
//uli_tabelle_header(1, $header);
//
//	$content='';
//	$content = array();
//	$content[] = 'Dein Kreditrahmen: '.number_format($kreditrahmen,0, ",", ".").' Euro';
//	uli_tabelle_content(1, $content);
//
//	unset($content);
//	$content = array();
//	$content[] = '<form action="?action=kreditaufnehmen&view=kredite" method="POST">'.
//				'<input type = "hidden" name="percent" value = "15"/>'.
//				'<input type = "text" size="10" name="sum"/> 15 Prozent p.A. Laufzeit 6 Monate'.
//				' <input type = "submit" value="ich brauch das geld" />'.
//				'</form>';
//	uli_tabelle_content(1, $content);
//
//	unset($content);
//	$content = array();
//	$content[] = '<form action="?action=kreditaufnehmen&view=kredite" method="POST">'.
//				'<input type = "hidden" name="percent" value = "20"/>'.
//				'<input type = "text" size="10" name="sum"/> 20 Prozent p.A. Laufzeit 3 Monate'.
//				' <input type = "submit" value="ich brauch das geld" />'.
//				'</form>';
//	uli_tabelle_content(1, $content);
//	unset($content);
//	$content = array();
//	$content[] = '<form action="?action=kreditaufnehmen&view=kredite" method="POST">'.
//				'<input type = "hidden" name="percent" value = "25"/>'.
//				'<input type = "text" size="10" name="sum"/> 25 Prozent p.A. Laufzeit 1 Monate'.
//				' <input type = "submit" value="ich brauch das geld" />'.
//				'</form>';
//	uli_tabelle_content(1, $content);
//	uli_tabelle_end();
}



/**
 * TODO: Überarbeiten je nach dem, was dort gewollt ist
 * Hier werden die laufenden Kredite ausgegeben
 */

function print_running_credits() {
//global $user_ID, $wpdb, $CONFIG;
//$uliID = get_uliID($user_ID);
//$sql = 'SELECT * FROM '.$CONFIG->prefix.'uli_kredite '.
//		' WHERE toklub = "'.$uliID.'" '.
//		' AND paid = 0 order by end';
//$result = $wpdb->get_results($sql,ARRAY_A);
//uli_tabelle_header(2,array('laufende Kredite', 'Ablauf'));
//if ($result){
//	foreach($result as $kredit){
//		$content='';
//		$content = array();
//		$content[] = number_format($kredit['sum'],0,",",".").' '.get_string('currency');
//		$content[] = date('d.m.y', $kredit['end']);
//		uli_tabelle_content(2, $content);
//		}
//	}
//	else {
//		$content='';
//		$content = array();
//		$content[] = 'Gottseidank schuldenfrei';
//		$content[] = '';
//		uli_tabelle_content(2, $content);		
//		}
//uli_tabelle_end();
	
}

?>
