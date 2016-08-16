<?php
/*
 * Created on 08.06.2009
 * TV
 * 
 * TODO 
 * Sprachdateien
 * Textbausteine
 * 
 * 
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "finanzen", "sub" => "tv");
uli_header(array('lib_tv'));


/* Aktionen und Ansichten */
$action = $_REQUEST['action'];$action = strip_tags($action);
$contract = $_POST['contract'];settype($contract, INT);
/* ACHTUNG: hier wird mit Halbjahren gearbeitet */
$year = $_REQUEST['year'];settype($year, INT);
if (!$year) {$year = $option['currentchildyear'];}


/* ************************************************** */

/* Ausgabe des Containers für Messages */
echo '<div id="container">';
echo '</div>';

/* Ausgabe der Seite */
echo '<div class="LeftColumn">';
	echo "\n";
	echo uli_box(Info, InfoTextTV); 
	echo "\n";

	echo "\n";
	echo print_child_year_menue($year, NULL, $startyear = 3);
	echo "\n";
	
echo '</div>';
echo "\n\n";

echo '<div class="RightColumnLarge">';
echo "\n";

	echo '<div id="tv">';
	echo "\n";
	 	echo print_tv_negotiation($year, $action, $contract);
	echo '</div>';
	echo "\n";
echo '</div>';
echo "\n";


/* Footer */
uli_footer();
?>