<?php
/*
 * Mannschaftsaufstellung
 * 08.07.09
 *
 *
 * 21.09.2010
 *
 * Was soll sich �ndern
 *
 * Stats f�r die Spieler
 * - vor allem Verein und Punkte in den letzten Spielen
 * - Zufriedenheit
 * - die neue supergeile trikotnummer muss auch zu sehen sein
 *
 * die rueckennummer einbauen (sowohl in der aufstellung als auch auf der tribuene)
 * und auf klick auf die nummer kommen die infos
 *
 * Co-Trainer (* optional) - stellt auf jeder Position die Punktbesten auf
 *
 *
 * TODO
 * Die Spielre auf dem Feld muessen auch bewegt werden koennen
 * Info Button
 * Rueckennummern
 * Co Trainer
 * Systeme
 * Bank muss befuellt werden koennen
 * Kapitaen
 *
 *
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "kabine", "sub" => "kabine");
uli_header(array('lib_kabine'));

global $option;
$uliID = $option['uliID'];

//print_R($_POST);

/* Aktionen */
$action = $_REQUEST['action'];strip_tags($action);
$newformation = $_REQUEST['formation'];strip_tags($formation);


if ($action == "clear"){
	for ($x=1; $x<=15; $x++){
		clear_formation($uliID, $option['currentyear']);
	}
}


// ueberprueft ob es einen 0er eintrag in der db gibt, der benoetigt wird um die basisaufstellung zu schreiben
check_userteam_basic();

// holt die Formation
// wenn keine formation gesetzt ist, gilt 4-4-2
$formation= get_userformation(0, $uliID, $option['currentyear']);

//echo $formation;
if (!$formation)
	{$formation = "442"; save_formation($formation, $uliID, 0, $option['currentyear']);}

//print_r($option);

?>

<script>
$(document).ready(function(){
	$("#formation").change(
		    function() {
					//alert('hu');
		    	var formation = $('#formation').prop("value");
					//alert(formation);
				$.ajax({
					type: "POST", url: "ajax_kabine.php", data: "action=changeformation&formation=" + formation,
					complete: function(data){
					$("#container").html(data.responseText);
			    	location.reload();
					}
				});
		});






});
</script>

<script>
$( "div" ).on( "click", ".captain", function() {
	var slotid = this.id;
	$.ajax({
		type: "POST", url: "ajax_kabine.php", data: "action=changecaptain&slotid="+slotid,
		complete: function(data){
			$("#container2").html(data.responseText);
		}
	 });
	});
</script>


<script>
$( "div" ).on( "click", ".player_kabine", function() {
	var playerID = this.id;
	$.ajax({
		type: "POST", url: "<?php echo $option['uliroot']; ?>/_mainlibs/ajax_global.php", data: "action=printplayerinfo&playerID="+playerID,
		complete: function(data){
			$(".kabineinfo .content").html(data.responseText);
		}
	 });
	});

</script>

<script>
$( "div" ).on( "click", ".player_field", function() {
	var playerID = this.id;
	$.ajax({
		type: "POST", url: "<?php echo $option['uliroot']; ?>/_mainlibs/ajax_global.php", data: "action=printplayerinfo&playerID="+playerID,
		complete: function(data){
			$(".kabineinfo .content").html(data.responseText);
		}
	 });
	});
</script>

<?php



read_styles_ajax($formation);
//print_script_kabine($formation);

echo '<div id="container2"></div>';

echo '<div id="workarea">';
echo "\n";
	echo '<div id ="spielfeld">';
		echo "\n";
		print_slots($formation);
		echo fill_slots($formation);
		echo "\n";
	echo '</div>';
	echo "\n";
	echo '<div id="bench">';
		echo "\n";
		echo '<div class="player_kabine" style="color: #fff;"><b>'.YourTeam.'</b></div>';
		echo "\n";
		echo print_kader_kabine($uliID);
		echo "\n";
		//echo '<div class="player_kabine" id="captain">Kapitano</div>';
		echo "\n";
	echo '</div>';
	echo "\n";
	echo '<div id="control">';
	echo "\n";
		echo print_formation_menu(0, $formation);
	echo "\n";
		echo '<div id="container">';
		echo print_kabine_info_box();
		echo '</div>';

	echo '</div>';
	echo "\n";
echo '</div>';
/* Footer */
uli_footer();
?>
