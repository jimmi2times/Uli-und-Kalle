<?php 
// fuer die ganzen ajax files muss irgendeine routine gebaut werden, die die ganzen libs einliest.
// diese variante ist etwas ruppig, weil bei jedem kleinen request die ganze routine (checks, etc.) durchlaufen wird.

require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');


if ($_POST['action'] == "printplayerinfo"){
	$html = print_player_info($_POST['playerID']);
	echo $html['content'];
}

?>