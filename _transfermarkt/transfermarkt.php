<?php
/*
 * Created on 14.04.2009
 *
 * Transfermarkt:
 * Sowohl Liste als auch laufende Auktionen
 *
 * 06.05.09
 *
 * TODO:
 * Reload Knopf fuer die Auktionen (einzeln)
 * in den letzten 10 Minuten vor Ende einer Auktion automatischer Reload
 * Dann kein Reload Knopf
 *
 *
 * Linke Spalte - Worauf wird geboten, Spielraum
 * Aktuelle Zeit
 * Transfermarkt startet nur, wenn:
 *  - gen�gend Mitspieler (LeagueSettings)
 *  - Er in den globalen Einstellungen ge�ffnet wurde (Dann sind die Auktionen an und die Liste aus.)
 *
 * �berlegen ob die Gebote auf Spieler in anderen Teams auch mit einbezogen werden m�ssen.
 *
 * Ausf�hrliches Testen
 *
 *
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "transfermarkt", "sub" => "transfermarkt");
uli_header(array('lib_transfermarkt'));

/* Immer auf der ganzen Seite benoetigte Variablen zum Manager */
$allbets     = get_sum_ulibets($option['uliID']);
$guthaben    = get_value_bank(14, 0, 0, $option['uliID']);
$kredite     = get_all_kredite($option['uliID']);
$kreditrahmen = get_kreditrahmen($option['uliID']);
$vermoegen   = $guthaben + $kreditrahmen - $allbets - $kredite;

/* Aktionen */

/* Die Seite wird ausgegeben */

/* Linke Seite */
/* Info */
/* Optionen */

/* Hauptspalte */
/* Filter */


?>
<script
	language="javascript" type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/clock/jquery.jclock.js"></script>

  <script type="text/javascript">
    $(function($) {
      $('.jclock').jclock();
    });
  </script>


<script type="text/javascript">
//on submit event
$(".betauction").live({ submit: function(){
var auctionid = this.id;
var sumfield = "#sum-auction" + auctionid;
var sumauction = $(sumfield);
var sum = sumauction.attr("value");


var unformated = $(sumfield).autoNumeric('get')
var sum = unformated;
//alert(unformated);


var playerfield = "#playerID" + auctionid;
var playerauction = $(playerfield);
var playerID = playerauction.attr("value");

var messageList = $("#bet-auctionID-" + auctionid);
	//we deactivate submit button while sending
	$(".formauction").attr({ disabled:true });
	$(".formauction").blur();
	//send the post to shoutbox.php
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=submitbet&auctionID=" + auctionid + "&sum=" + sum + "&uliID=" + <?php echo $option['uliID']; ?> + "&leagueID=" + <?php echo $option['leagueID']; ?> + "&playerID=" + playerID,
		complete: function(data){
			messageList.html(data.responseText);
			//updateShoutbox();
			//reactivate the send button
			$(".formauction").attr({ disabled:false });
		}
	 });
//we prevent the refresh of the page after submitting the form
return false;
}
});


$(document).ready(function(){
	// Transferliste nachladen oder ausblenden
	$('input#TransListCheckBox').change(
		    function() {
		        if ($(this).is(':checked')) {
					var messageList = $("#translist");
					//we deactivate submit button while sending
					$(".formauction").attr({ disabled:true });
					$(".formauction").blur();
					//send the post to shoutbox.php
					$.ajax({
						type: "POST", url: "ajax_bet.php", data: "action=loadtranslist",
						complete: function(data){
							messageList.html(data.responseText);
							//reactivate the send button
							$(".formauction").attr({ disabled:false });
						}
					 });
				return false;
			    } else {
			    	$("#translist").html("");
			    }
		});

	// Filtercheckboxen
	$('input.filtercheckbox').change(
		    function() {
				var filter = "." + this.id;
		    	if ($(this).is(':checked')) {
				    if ($(filter).hasClass('agehide')){$(filter).removeClass('agehide');$(filter).addClass('ageshow');}
				    if ($(filter).hasClass('teamhide')){$(filter).removeClass('teamhide');$(filter).addClass('teamshow');}
				    if ($(filter).hasClass('positionhide')){$(filter).removeClass('positionhide');$(filter).addClass('positionshow');}
			    } else {
				    if ($(filter).hasClass('ageshow')){$(filter).removeClass('ageshow');$(filter).addClass('agehide');}
				    if ($(filter).hasClass('teamshow')){$(filter).removeClass('teamshow');$(filter).addClass('teamhide');}
				    if ($(filter).hasClass('positionshow')){$(filter).removeClass('positionshow');$(filter).addClass('positionhide');}
			    }
		});

	// Filterselect
	$('select.selectfilter').change(
		    function() {

				var filter = "." + $(this).val();
				if (filter == ".all") {
					if ($(".auction").hasClass('teamhide')){$(".auction").removeClass('teamhide');$(".auction").addClass('teamshow');}
				}
				else {
				if ($(".auction").hasClass('teamshow')){$(".auction").removeClass('teamshow');$(".auction").addClass('teamhide');}
				if ($(filter).hasClass('teamhide')){$(filter).removeClass('teamhide');$(filter).addClass('teamshow');}
				}
			});



});



jQuery(function($) {
    $('.auctionBet').autoNumeric('init');
});



</script>



<?php

echo '<div id="container">';
echo '</div>';

echo '<div class="LeftColumn">';
echo "\n";
echo uli_box(YourBets, print_my_bets($option['uliID'], $option['leagueID']));

echo "\n";
//echo uli_box(YourPlayers, 'Info, welche Spieler auf der Liste stehen und auf wen geboten wird');
echo uli_box('Auktionen', 'Ca. 30% aller Auktionen (per Zufallsgenerator) sind verdeckt. D.h. Ihr gebt ein Gebot ein und wenn das das H&ouml;chstgebot ist, bekommt Ihr den Spieler und bezahlt genau diese Summe. Kein hektisches Bieten mehr kurz vor Schluss, sondern ein wohl bedachtes und zeitzonenunabh&auml;ngiges Bieten f&uuml;r den gereiften Manager. Alle anderen Auktionen laufen wie bei eBay. F5 oder CMD+R ist nicht notwendig. In den letzten 10 Minuten einer Auktion aktualisiert sich die Auktion von Zauberhand ohne Reload der Seite.');


// Wer ist aktiv
// Es wird das Plugin-Loginlogs ausgelesen
$table_name = $wpdb->prefix . "loginlog";
$cur_time = $wpdb->get_results("SELECT UNIX_TIMESTAMP('".current_time('mysql')."') as timestamp FROM ".$table_name);
$cur_time = $cur_time[0]->timestamp;
$query = "SELECT username,time,UNIX_TIMESTAMP(time) as timestamp,UNIX_TIMESTAMP(active) as activestamp,IP FROM ".$table_name." WHERE success='1' ORDER BY active DESC";
$results = $wpdb->get_results($query);
//print_r($results);
if ($results){
	foreach ($results as $result){
		//echo $result->username.' '.$result->activestamp.' '.mktime().'<br>';
		if ($result->activestamp < (mktime() - 3600)){
			break;
		}
		// Check wie das Team heißt und ob es aus der richtigen Liga kommt
		$query = "SELECT ID FROM ".$wpdb->prefix."users WHERE user_login = '".$result->username."'";
		$userID = $wpdb->get_var($query);
		$uli = get_uli_userID($userID);
		if ($uli['leagueID'] == $option['leagueID']){
			$activeUlis[] = $uli['uliname'];
		}
	}
	//print_r($activeUlis);
	$html = implode($activeUlis, " | ");
	echo uli_box('In der letzten Stunde im Büro', $html);
}

echo "\n";
echo '</div>';
echo "\n\n";

echo '<div class="RightColumnLarge">';
echo "\n";
	echo '<div class="filter">';
	echo print_filter_positions();
	echo print_filter_transferliste();
	echo '<br/>';
	echo print_filter_age();
	echo print_filter_team();
	echo '</div>';
	echo "\n\n";

	echo '<div id="transfermarkt">';
	echo "\n";
	echo print_transfermarkt();
	echo '</div>';
	echo "\n";
echo '</div>';
echo "\n";

?>

<script>

</script>
<?php

/* Footer */
uli_footer();
?>
