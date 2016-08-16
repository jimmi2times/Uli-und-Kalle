<?php
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

global $option;


/* Header */
$page = array("main" => "start", "sub" => "optionen", "name" => "Optionen");
uli_header(array('lib_communication'));


echo '<div class="LeftColumn">';
echo "\n";
echo '</div>';
echo "\n\n";


echo '<div class="RightColumnLarge">';
echo "\n";
echo '<div id="communicate">';
echo "\n";
echo '<h2>Die Einrichtungssoftware f&uuml;r Dein B&uuml;ro ist unterwegs. <br/>Dann kannst Du hier loslegen.</h2>';
echo '</div>';
echo "\n";
echo '</div>';
echo "\n";



/* Footer */
uli_footer();
?>