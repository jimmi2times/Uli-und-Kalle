<?php
/*
 * Created on 30.03.2009
 *
 * Die Einstellungen für jeden Benutzer
 * 30.03.09
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/ulinew/_mainlibs/setup.php');

/* Header */
$page = 'meinbuero';
uli_header(array('lib_meinbuero'), array('change_color'));

/* Aktionen */

/* Der Datensatz wird aktualisiert */
if ($_REQUEST['action'] == 'updateuli'){
	$values = $_POST;
	$cond   = array('col' => 'ID', 'value' => $option['uliID']); 
	uli_update_record('uli', $cond, $values);
}

/* Die Seite wird ausgegeben */


?>

<?
echo '<div class="einsechstel">';
echo print_uli_mainoptions();
echo '</div>';
echo "\n\n";
echo '<div class="einsechstel">';
echo print_uli_colors();



echo '</div>';
echo "\n\n";
echo '<div class="einsechstel">';
echo print_uli_mainoptions();
echo '</div>';
echo "\n\n";
echo '<div class="einsechstel">';
echo print_uli_mainoptions();
echo '</div>';
echo "\n\n";
echo '<div class="einsechstel">';
echo print_uli_mainoptions();
echo '</div>';
echo "\n\n";
echo '<div class="einsechstel">';
echo print_uli_mainoptions();
echo '</div>';
/*
print_uli_colors();
print_uli_trikots();
print_uli_manager();
print_uli_location();
print_uli_styles();
*/



uli_footer(); 



?>
