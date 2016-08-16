<?php
/*
 * Created on 16.05.2009
 * Stats
 */
 

 
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "stats", "sub" => "stats");
uli_header(array('lib_stats'));

/* Parameter */
$year = $_REQUEST['year']; settype($year, INT);
if(!$year){$year = $option['currentyear'];}

$leagueID = $_REQUEST['leagueID']; settype($leagueID, INT);


$round = str_replace("round", "", $_REQUEST['round']); settype($round, INT);


$view = $_REQUEST['view']; 
if (!$view){$view = 'tabelle';}

// TODO Das noch optimieren mit diesem Tablesorter
?>
<script
	language="javascript" type="text/javascript"
	src="<?php  echo $option['uliroot']; ?>/theme/jquery/tablesorter/jquery.tablesorter.min.js"></script>

<script type="text/javascript">
      $(document).ready(function(){
        $.tablesorter.addParser({
              id: 'germandate',
              is: function(s) {
                      return false;
              },
              format: function(s) {
                var a = s.split('.');
                a[1] = a[1].replace(/^[0]+/g,"");
                return new Date(a.reverse().join("/")).getTime();
              },
              type: 'numeric'
            });
        $("#ulitable").tablesorter(

 <?php if ($view == "lasttransfers" OR $view == "mytransfers" OR $view == "toptransfers") { ?>       		
        		
                {
                headers: { 0: { sorter:'germandate' }}
        }
<?php } ?>

        );
      });
</script>

<script>
$(document).ready(function(){
	$("#year, #round, #league").change(
		    function() {
		    	var round = $('#round').attr("value");
		    	var year = $('#year').attr("value");
		    	var league = $('#league').attr("value");

		    	var param = "?view=<?php echo $view;?>&round=" + round + "&year=" + year + "&leagueID=" + league;
				window.location.search = param;
		});


	$(".showteam").click(
			function () {
				$.ajax({
					type: "POST", url: "ajax_stats.php", data: "action=printuserteam&uliID=" + this.id + "&round=<?php echo $round; ?>&year=<?php echo $year;?>",
					complete: function(data){
						$("#container").html(data.responseText);
					}
				 });
				$("#container").dialog({ height: 540, width: 480 });
			});
});
</script>

<?php 







/* ************************************************** */


/* Ausgabe des Containers f�r Messages */
echo '<div id="container">';
echo '</div>';

/* Ausgabe der Seite */
echo '<div class="LeftColumn">';
	echo print_stats_menue($year, $view); 
	echo "\n";

	//echo print_year_menue($year, $view); 
	echo "\n";
	echo "<br/>";
	echo print_my_stats_menue($year, $view); 
	echo "\n";

	//echo print_year_menue($year, $view); 
	echo "\n";
echo '</div>';
echo "\n\n";

echo '<div class="RightColumnLarge">';
echo "\n";

	echo '<div id="stats">';
	echo "\n";
	 	
	echo print_stats($view, $round, $year, $leagueID);

	//echo '<h2>26.4 Mb und 319.031 Eintr&auml;ge haben sich hier in den letzten 7 Jahren angesammelt. Demn&auml;chst gibts die dann auch als Moderationsk&auml;rtchen aufbereitet.</h2>';
	
	echo '</div>';
	echo "\n";
echo '</div>';
echo "\n";

echo '<div class="RightColumnSmall">';
	echo "\n";
	//echo uli_box('More Stats'); 
	echo "\n";
	
	//echo uli_box('More Stats #2'); 
	echo "\n";
	
	//echo uli_box('Quickstats'); 
	echo "\n";
	
	//echo uli_box('Prognose'); 
	echo "\n";
echo '</div>';
echo "\n";

/* Footer */
uli_footer();


/**
 * Gibt das Men� f�r die Jahre aus
 * Markiert das aktive Jahr
 * 22.05.09
 */
/*
function print_year_menue($year, $view){
global $option;
$uliyears = get_uli_years();
$html .= "\n";	
if ($uliyears){
	foreach ($uliyears as $uliyear){
		$active = '';
		if ($year == $uliyear['ID']){$active = 'active';}
		$html .= '<a href="?year='.$uliyear['ID'].'&amp;view='.$view.'" class="'.$active.'">'.$uliyear['name'].'</a>';	
		$html .= '<br/>';
		$html .= "\n";
	}}
$html .= "\n";
$html = uli_box(ChoseYear, $html);		
return $html;
}
*/


?>
