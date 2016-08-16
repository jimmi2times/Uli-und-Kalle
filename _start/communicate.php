<?php
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

global $option;

$uliID = $option['uliID'];
$action = $_REQUEST['action'];
$view = $_REQUEST['view'];



/* Header */
$page = array("main" => "start", "sub" => "communicator", "name" => "Communicator");
uli_header(array('lib_communication'));

// ueberprüfen und Schreiben der Nachricht.
if ($action == "send"){
	$form = $_POST;
	$form['time'] = mktime();
	$form['text'] = nl2br(strip_tags($form['text']));
	$form['subject'] = strip_tags($form['subject']);
	settype($form['receiver'], INT);
	settype($form['sender'], INT);

	// "normale" Nachricht
	if ($form['receiver'] != 1) {
		write_message($form);
	}
	// Rundschreiben
	if ($form['receiver'] == 1){
		$form['subject'] = '[Rundschreiben] '.$form['subject'];
		$league_members = get_ulis($option['leagueID']);
		if ($league_members){
			foreach($league_members as $league_member){
				if ($league_member['ID'] != $form['sender']){
					$form['receiver'] = $league_member['ID'];
					$form['del_sender'] = 1;
					$form['del_receiver'] = 0;
					write_message($form);
				}
				else {
					$form['receiver'] = $league_member['ID'];
					$form['del_sender'] = 0;
					$form['del_receiver'] = 1;
					write_message($form);
				}
			}
		}
	}
}


?>
<script>
$(document).ready(function(){
	
	$(".message").click(
		    function() {
			    $(".messagecontent").hide();
			    $("#messagecontent-"+this.id).show();

				$.ajax({
					type: "POST", url: "ajax_communication.php", data: "action=read&id=" + this.id,
					complete: function(data){
					$("#hiddencontainer").html(data.responseText);
					}
				 });
			    
		});	

	$(".delete").click(
		    function() {
				$.ajax({
					type: "POST", url: "ajax_communication.php", data: "action=delete&id=" + this.id,
					complete: function(data){
					$("#hiddencontainer").html(data.responseText);
					}
				 });				
		});	
	$(".reply").click(
		    function() {
				$.ajax({
					type: "POST", url: "ajax_communication.php", data: "action=reply&id=" + this.id,
					complete: function(data){
					$("#container").html(data.responseText);
					}
				 });			
				 $("#container").dialog();
						 	
		});	

	$(".newmessage").click(
		    function() {
				$.ajax({
					type: "POST", url: "ajax_communication.php", data: "action=newmessage",
					complete: function(data){
					$("#container").html(data.responseText);
					}
				 });			
				 $("#container").dialog();
						 	
		});	

});
</script>
<?

echo '<div id="container" title="'.NewMessage.'">';
echo '</div>';

echo '<div id="hiddencontainer" title="'.NewMessage.'">';
echo '</div>';


echo '<div class="LeftColumn">';
echo "\n";
echo uli_box(HeadlineCommunicator, InfoTextCommunicate);
echo "\n";
$menubox = '<a href="?">'.Inbox.'</a><br />
<a href="#" class="newmessage">'.WriteMessage.'</a><br />
<a href="?view=sent">'.SentBox.'</a><br />';	

echo uli_box(Menu, $menubox);
echo "\n";
echo '</div>';
echo "\n\n";


echo '<div class="RightColumnLarge">';
echo "\n";
echo '<div id="communicate">';
echo "\n";
echo print_mailbox($uliID, $view);
echo '</div>';
echo "\n";
echo '</div>';
echo "\n";



//</div>
//<div id="container" style="margin-left: 200px;top: 100px;"></div>
//<div id="rightcolumn"><?php
//if (!$view OR $view == "inbox") {
//	echo print_inbox($uliID);
//	}
//if ($view == "sent") {
//	//print_sent_messages($uliID);
//	}
?>

<?php

/* Footer */
uli_footer();
?>