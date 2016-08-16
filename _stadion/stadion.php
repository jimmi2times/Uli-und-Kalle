<?php
/*
 * Created on 16.05.2009
 * Stats
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "stadion", "sub" => "stadion");
uli_header(array('lib_stadion'));



/* Parameter */
$year = $_REQUEST['year']; settype($year, INT);
if(!$year){$year = $option['currentyear'];}

$view = $_REQUEST['view']; 
if (!$view){$view = 'singlevalues';}

/* ************************************************** */


/* Ausgabe des Containers für Messages */
echo '<div id="container">';
echo '</div>';
?>

<script>
// coming soon
$('a.comingsoon').live('click', function() {

	
	var returnBox = $(".firstrow");
	returnBox.html("<h3>Eine zuk&uuml;nftige Aufgabe f&uuml;r den ambitionierten Manager.</h3>");
	return false;
	});


</script>
<?php 
$stadium = get_stadium($option['uliID']);

if ($stadium['infra']){
	foreach ($stadium['infra'] as $infra){
		$sum = $infra['sum'] + $sum;
	}
}

$sum = $sum + ($stadium['sitzplaetze'] / 5000 * 8000000) + ($stadium['stehplaetze'] / 8000 * 6000000) - 8000000;
?>

<div class = "stadium">

	<div class = "firstrow ulibox">


		<h3>Dein Stadion. Die Heimat Deiner Fans. 
		<?php echo number_format($stadium['sitzplaetze'], 0, '','.');?> Sitzpl&auml;tze.
		<?php echo number_format($stadium['stehplaetze'], 0, '','.');?> Stehpl&auml;tze.
		Ingesamt schon <?php echo uli_money($sum);?> investiert.
		</h3>
	
	</div>

	<div class = "clear"></div>

	<a href="seats.php"><div class = "secondrow box ulibox seats"><h3>Pl&auml;tze</h3></div></a>
	<a href="infra.php"><div class = "secondrow box ulibox infra"><h3>Infrastruktur</h3></div></a>
	<a href="stadionname.php"><div class = "secondrow box ulibox name"><h3>Name</h3></div></a>
	<a class = "comingsoon" id = "fans" href="#"><div class = "secondrow box ulibox fans"><h3>Fans</h3></div></a>
	<a class = "comingsoon" id = "condition"  href="#"><div class = "secondrow box ulibox condition"><h3>Zustand</h3></div></a>
	<a class = "comingsoon" id = "stats" href="#"><div class = "secondrow box ulibox stats"><h3>Statistiken</h3></div></a>

	<div class = "clear"></div>

</div>


<?php 


/* Footer */
uli_footer();
?>
