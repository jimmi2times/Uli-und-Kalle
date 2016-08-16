<?php
/*
 * Created on 16.05.2009
 * BANK
 */

require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');


/* Header */
$page = array("main" => "finanzen", "sub" => "bank", "name" => "Bank");
uli_header(array('lib_bank'));
?>


<script
	language="javascript" type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/jquery.jqplot.min.js"></script>
<script
	type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script
	type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>

<script
	type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/src/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script
	type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/src/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script
	type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/src/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>

<script
	type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/plugins/jqplot.pointLabels.min.js"></script>
<link
	rel="stylesheet" type="text/css"
	href="<?php  echo $option['uliroot']; ?>/theme/jquery/jqplot/jquery.jqplot.css" />


<script>
// Das ist fuer die Tabs in der Matrix jquery UI
$(function() {
	$( "#tabs" ).tabs({
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html(
					"Loading" );
			}
		}
	});
});
</script>


<?php

/* Immer auf der ganzen Seite benoetigte Variablen zum Manager */
$guthaben    = get_value_bank(14, 0, 0, $option['uliID']);
$kredite     = get_all_kredite($option['uliID']);
$kreditrahmen = get_kreditrahmen($option['uliID']);
$vermoegen   = $guthaben - $kredite;


// Parameter
if ($_REQUEST['year']){
	$year = $_REQUEST['year']; settype($year, INT);
}
if ($_POST['year']){
	$year = $_POST['year']; settype($year, INT);
}
if(!$year){$year = $option['currentyear'];}

$view = $_REQUEST['view'];strip_tags($view);
if (!$view){$view = 'overview';}

if ($_POST['type']){
	$type = $_POST['type'];
}

$action = $_REQUEST['action'];strip_tags($action);

// TODO Kreditrahmen

// Nimmt den Kredit auf
if ($action == "kreditaufnehmen"){
	$sum = $_POST['sum'];settype($sum, INT);
	$percent = $_POST['percent']; settype($percent, INT);
	if ($sum <= 0){$error = NoRealCredit;}
	if ($sum >= ($kreditrahmen - $kredite)){$error = YouDontGetSoMuchMoney;}
	if (!$error){
		write_credit($sum, $percent, $option['uliID'], $guthaben);
		$message = TheMonesIsYours;
		$guthaben = $guthaben + $sum;
		$kredite = $kredite + $sum;
	}
	else {$message = $error;}
}


/* ************************************************** */


// Ausgabe des Containers fuer Messages
echo '<div id="container">';
echo '</div>';

/* Ausgabe der Seite */
echo '<div class="LeftColumnSmall">';
echo "\n";
echo uli_box(CurrentMoney, print_vermoegen($guthaben, $vermoegen, $kredite));
echo "\n";

echo print_finance_menue($year, $view);
echo "\n";

//echo print_year_menue($year, $view);
echo "\n";

echo "\n";
echo '</div>';
echo "\n\n";

echo '<div class="CenterColumn">';
echo "\n";

echo '<div id="bank">';
echo "\n";
echo print_bank($view, $year, $type);
echo '</div>';
echo "\n";
echo '</div>';
echo "\n";

echo '<div class="RightColumnSmall">';
echo "\n";
$creditsHtml = print_kreditabteilung($kredite, $kreditrahmen, $view, $year, $action, $message);
echo uli_box(Credits, $creditsHtml);
echo "\n";

$runningCreditsHtml = print_running_credits();
echo uli_box(RunningCredits, $runningCreditsHtml);
echo "\n";

$saldoHtml = print_day_saldo();
if ($saldoHtml){
	echo uli_box(SaldoPerDay, $saldoHtml, '', '', '', 'overflow');
	echo "\n";
}
/*
 echo uli_box('Prognose');
 echo "\n";
 */

echo '</div>';
echo "\n";

/* Footer */
uli_footer();


/**
 * Gibt ein Formular zum Wechseln und dynamischen Nachladen der Menuepunkte der Bank aus
 * 19.05.09
 *
 */
function print_finance_menue($year, $view){
	global $option;

	$SelectOptions[] = array("view" => "overview", "desc" => Overview);
	$SelectOptions[] = array("view" => "compareyears", "desc" => CompareYears);
	$SelectOptions[] = array("view" => "singlevalues", "desc" => SingleValues);
	//$SelectOptions[] = array("view" => "compareleague", "desc" => CompareLeague);

	$html .= "\n";
	if ($SelectOptions){
		foreach ($SelectOptions as $SelectOption){
			$active = '';
			if ($view == $SelectOption['view']){$active = 'active';}
			$html .= '<a href="?view='.$SelectOption['view'].'&amp;year='.$year.'" class="'.$active.'">'.$SelectOption['desc'].'</a>';
			$html .= '<br/>';
			$html .= "\n";
		}}
		$html .= '</select>';
		$html .= '</form>';
		$html .= "\n";
		$html = uli_box(ChoseViewBank, $html);
		return $html;
}
?>
