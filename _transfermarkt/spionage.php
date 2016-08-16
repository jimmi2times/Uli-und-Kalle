<?php
/*
 * Created on 14.04.2009
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');
session_start();

/* Header */
$page = array("main" => "transfermarkt", "sub" => "spionage");
uli_header(array('lib_transfermarkt'));


/* Immer auf der ganzen Seite ben�tigte Variablen zum Manager */
$allbets     = get_sum_ulibets($option['uliID']); 
$guthaben    = get_value_bank(14, 0, 0, $option['uliID']);
$kredite     = get_all_kredite($option['uliID']);	
$kreditrahmen = get_kreditrahmen($option['uliID']);
$vermoegen   = $guthaben + $kreditrahmen - $allbets - $kredite;	

// Das ist fuer die namenssuche, die hat noch kein ajax
if ($_REQUEST['action'] == "search"){
	$string = $_POST['name'];
	
	$searchresult = execute_search('', '', '', $string);
}



?>
<script>
$(document).ready(function(){
	// Ausfuehren der Suche
	// Wie machen wir das? Es sollte vielleicht jedesmal nachgeladen werden
	// Es wird eine Suche angestossen, dafuer muss ueberprueft werden, welche Elemente angeklickt sind
	// Und diese irgendwie uebergeben werden
	$('form#searchbox').change(
		    function() {
				// Hier ueberpruefen, was alles an ist.
				var pos = '';
				var age = '';
				if ($('#pos-1').is(':checked')){ var pos = pos + ',1'; }
				if ($('#pos-2').is(':checked')){ var pos = pos + ',2'; }
				if ($('#pos-3').is(':checked')){ var pos = pos + ',3'; }
				if ($('#pos-4').is(':checked')){ var pos = pos + ',4'; }
				if ($('#pos-5').is(':checked')){ var pos = pos + ',5'; }
				if ($('#pos-6').is(':checked')){ var pos = pos + ',6'; }
				if ($('#pos-7').is(':checked')){ var pos = pos + ',7'; }

				if ($('#age-1').is(':checked')){ var age = age + ',1'; }
				if ($('#age-2').is(':checked')){ var age = age + ',2'; }
				if ($('#age-3').is(':checked')){ var age = age + ',3'; }
				if ($('#age-4').is(':checked')){ var age = age + ',4'; }

				var uliID = $('#uliteamsearch').attr("value");

				var messageList = $("#search");
					//we deactivate submit button while sending
					//$(".formauction").attr({ disabled:true });
					//$(".formauction").blur();
					//send the post to php
					$.ajax({
						type: "POST", url: "ajax_bet.php", data: "action=executesearch&pos=" + pos + "&age=" + age + "&uliID=" + uliID,
						complete: function(data){
							messageList.html(data.responseText);
							//reactivate the send button
							//$(".formauction").attr({ disabled:false });
						}
					 });
				return false;
		});	
});

//Verhandeln
$('.contractplayer').live('click', function() {
	var negotiationBox = $("#negotiation-" + this.id);
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=contractplayer&playerID=" + this.id,
		complete: function(data){
		negotiationBox.html(data.responseText);
		}
	 });
	 return FALSE;
});

//das eigentliche vertragsverhandeln
$(".negotiationform").live({ submit: function(){
	var playerID = this.id;
	var salaryform = $("#salary-" + playerID);
	var salary = salaryform.attr("value");
	var unformated = $(salaryform).autoNumeric('get')
	var salary = unformated;
	
	var lengthform = $("#length-" + playerID);
	var length = lengthform.attr("value");
	var messageList = $("#negotiation-content-" + playerID);
	//deactivate submit button while sending
	$(".formauction").attr({ disabled:true });
	$(".formauction").blur();
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=negotiate&playerID=" + playerID + "&salary=" + salary + "&uliID=" + <?php echo $option['uliID']; ?> + "&leagueID=" + <?php echo $option['leagueID']; ?> + "&length=" + length,
		complete: function(data){
			messageList.html(data.responseText);
			//reactivate the send button
			$(".formauction").attr({ disabled:false });
		}
	 });
return false;
}
});


//das eigentliche vertragsverhandeln
$(".takeoverform").live({ submit: function(){
	var playerID = this.id;
	var offerform = $("#offer-" + playerID);
	var offer = offerform.attr("value");
	var unformated = $(offerform).autoNumeric('get')
	var offer = unformated;	
	var messageList = $("#negotiation-content-" + playerID);

	//deactivate submit button while sending
	$(".formauction").attr({ disabled:true });
	$(".formauction").blur();
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=takeover&playerID=" + playerID + "&offer=" + offer + "&uliID=" + <?php echo $option['uliID']; ?> + "&leagueID=" + <?php echo $option['leagueID']; ?>,
		complete: function(data){
			messageList.html(data.responseText);
			//reactivate the send button
			$(".formauction").attr({ disabled:false });
		}
	 });
return false;
}
});


</script>


<?php 



echo '<div id="container">';
echo '</div>';

echo '<div class="LeftColumn">';
echo "\n";
echo uli_box(HeadlineSearch, InfoTextSearch); 
echo "\n";
$searchbox = print_search_box();
echo uli_box(SearchBox, $searchbox); 
echo "\n";
echo '</div>';
echo "\n\n";


echo '<div class="RightColumnLarge">';
echo "\n";
	/*
	echo '<div class="filter">';
	$sortnames = array(position, PlayerName, age, jerseynumber, Salary, marktwert, contractend);
	$sort = array('position', 'name', 'age', 'jerseynumber', 'salary', 'marktwert', 'contractend');
	echo print_sortierung($sort, 'SortKader', 'kader', 'kader', $sortnames);
	echo '</div>';
	echo "\n\n";
	*/
	echo '<div id="search">';
	echo "\n";
	echo print_search_result($searchresult);
	echo '</div>';
	echo "\n";
echo '</div>';
echo "\n";

/* Footer */
uli_footer();
?>
