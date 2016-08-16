<?php
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "transfermarkt", "sub" => "kader");
uli_header(array('lib_transfermarkt'));

?>
<script>
$('.saveplayer').live('click', function() {
	var playerID = this.id;
	//alert(playerID);
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=saveplayer&playerID="+playerID,
		complete: function(data){
			$("#container").html(data.responseText);
		}
	 });
	});


//Angebot annehmen
$('a.acceptoffer').live('click', function() {
	var negotiationid = this.id;
	var playerID = $("#offerplayerid-"+negotiationid).attr("value");
	var negotiationBox = $("#kader-player-" + playerID);
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=acceptoffer&negotiationid="+negotiationid,
		complete: function(data){
			negotiationBox.html(data.responseText);
		}
	 });

	
	});

//Angebot ablehnen
$('a.rejectoffer').live('click', function() {
	var negotiationid = this.id;
	var negotiationBox = $("#offer-" + this.id);
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=rejectoffer&negotiationid="+negotiationid,
		complete: function(data){
			negotiationBox.html(data.responseText);
		}
	 });
	});

//Gehalt erhoehen
$('a.raisesalary').live('click', function() {
	var playerid = this.id;
	var negotiationBox = $("#raisesalary-" + this.id);
	var salary = $("#newsalary-"+playerid).attr("value");
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=raisesalary&playerID="+playerid+"&salary="+salary,
		complete: function(data){
			negotiationBox.html(data.responseText);
		}
	 });
	});

// Trikot
$('.jerseynumberchange').live('change', function() {
	var playerID = this.id;
	var newNumber = $(this).val();
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=changejerseynumber&playerID="+playerID+"&newNumber="+newNumber,
		complete: function(data){
		$("#jerseynumberpanel").html(data.responseText);
		}
	 });
	$("#jerseynumberpanel").dialog('destroy');
	});


//Trikotnummer tauschen - Das ist das Panel.
$('a.jerseynumber').live('click', function() {
	var playerID = this.id;
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=jerseynumber&playerID="+playerID,
		complete: function(data){
			$("#jerseynumberpanel").html(data.responseText);
		}
	 });
	$("#jerseynumberpanel").dialog();
	});


//Auf die Liste setzen
$('.takehome').live('click', function() {
	var negotiationBox = $("#negotiation-" + this.id);
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=takehome&playerID=" + this.id,
		complete: function(data){
		negotiationBox.html(data.responseText);
		}
	 });
	 return FALSE;
});

//Auf die Liste setzen
$('.sellplayer').live('click', function() {
	var negotiationBox = $("#negotiation-" + this.id);
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=sellplayer&playerID=" + this.id,
		complete: function(data){
		negotiationBox.html(data.responseText);
		}
	 });
	 return FALSE;
});

//der Spieler soll auf die Transferliste
$(".PutPlayerOnList").live({ submit: function(){
	var playerID = this.id;
	var WantedSumForm = $("#WantedSum-" + playerID);
	var WantedSum = WantedSumForm.attr("value");
	var messageList = $("#negotiation-" + playerID);
	//deactivate submit button while sending
	$(".formauction").attr({ disabled:true });
	$(".formauction").blur();
	$.ajax({
		type: "POST", url: "ajax_bet.php", data: "action=puthimonlist&playerID=" + playerID + "&WantedSum=" + WantedSum,
		complete: function(data){
			messageList.html(data.responseText);
			//reactivate the send button
			$(".formauction").attr({ disabled:false });
		}
	 });
return false;
}
});


// Verhandeln
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

// Sortierung
$(document).ready(function(){
	$('.sortradio').change(
		    function() {
					$.ajax({
						type: "POST", url: "ajax_bet.php", data: "action=loadkader&sort=" + this.id,
						complete: function(data){
							$("#kader").html(data.responseText);
						}
					 });
				return false;
		});	
})


</script>
<div id="jerseynumberpanel" title="Trikotnummer" style="display:none;"></div>

<?php 

echo '<div id="container">';
echo '</div>';

echo '<div class="LeftColumn">';
echo "\n";
echo uli_box(HeadlineKader, InfoTextKader); 
echo "\n";

echo uli_box(Info, print_quickstats_kader($option['uliID'], $option['leagueID'])); 
echo "\n";
echo '</div>';
echo "\n\n";


echo '<div class="RightColumnLarge">';
echo "\n";
	echo '<div class="filter">';
	$sortnames = array(position, PlayerName, age, jerseynumber, Salary, marktwert, contractend);
	$sort = array('position', 'name', 'age', 'jerseynumber', 'salary', 'marktwert', 'contractend');
	echo print_sortierung($sort, 'SortKader', 'kader', 'kader', $sortnames);
	echo '</div>';
	echo "\n\n";
	
	echo '<div id="kader">';
	echo "\n";
	echo print_kader();
	echo '</div>';
	echo "\n";
echo '</div>';
echo "\n";

/* Footer */
uli_footer();
?>
