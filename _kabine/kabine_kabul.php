<?php 

require_once('../../../config.php');
require_once('../../../lib/lib.php');
global $CONFIG, $user_ID, $uliID, $leagueID;
require_once($CONFIG->dirroot . '/wp-config.php' );
// check login //
if (!is_user_logged_in()){wp_redirect($CONFIG->wwwroot.'/wp-login.php?redirect_to='.$CONFIG->wwwroot.'/module/uli');}

require_once($CONFIG->dirroot . '/module/uli/_mainlibs/lib.php');
require_once($CONFIG->dirroot . '/module/uli/_kabine/lib_kabine.php');
require_once($CONFIG->dirroot . '/module/uli/_mainlibs/lib_player.php');


// Aktionen und Ansichten
$action = $_REQUEST['action'];
$round = $_REQUEST['round'];
if (!$round){$round = get_attribute("nextday");}
$userformation = $_REQUEST['formation'];
$team = trim($_POST['team']);
$position = trim($_POST['position']);

//team schreiben
if ($action == "nominateteam") 
	{
	$userformation = $_POST['userformation'];
	$player = array();
	for ($x=1; $x<=15; $x++) {
		$formular = "sp".$x;
		$player[$x] = $_POST[$formular]; 

		}
	if (check_uli_userteam(0, $player)){nominate_team(0, $player, $userformation);}
	}








////////////////////////////////
print_header_uli('kabine','kabine');

?>


<?
?><div id="leftcolumn"  style="width: 610px;padding-top: 5px;">

	<? // print_help_box('info_kabine'); ?>

	<div id="spielfeld">
<?	
		print_spielfeld(0, $userformation);
?>
		</div>
		<div id="formation">
<?
		print_formation_menu(0, $userformation);
		print_spielfeld_bank(0, $userformation);
?>
		</div>
	<?
	// runden
// echo '<form action = "?" method = "POST">';
// print_rounds_in_select($round);	
// echo '</form>';
// print_own_roster_side();


//if ($error){print_message_pic($error);}
//if($message){print_message_pic($message);}

?></div><div id="rightcolumn" style="width: 30%;">
<?PHP
// print_formation_menu($round, $userformation);

// print_spielfeld($round, $userformation);
// print_spielfeld_bank($round, $userformation);
print_help_box('info_kabine');
print_own_roster_side();
?>
</div>
 <?
print_footer_uli('kabine','kabine');
?>