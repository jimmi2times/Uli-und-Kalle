<?php

// fuer die ganzen ajax files muss irgendeine routine gebaut werden, die die ganzen libs einliest.
// diese variante ist etwas ruppig, weil bei jedem kleinen request die ganze routine (checks, etc.) durchlaufen wird.

require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

global $option;
//echo 'ahllo';

if ($_POST['action'] == "form"){
	$teamnumber = $_POST['teamnumber'];
	$uliID = $_POST['team'.$teamnumber];
	$goals = $_POST['goals'.$teamnumber];
	if ($uliID > 0){
		$players = get_user_team_sort($uliID);
		if ($players){
			for ($x = 1; $x <= $goals; $x++){
				?><select name = "scorer-<? echo $teamnumber; ?>-<?php echo $x; ?>">
				<option>Torsch&uuml;tze <?php echo $x; ?></option>
				<?php 
				foreach ($players as $player){
					//$player = get_player_infos($player['ID']);
					echo '<option value = "'.$player['playerID'].'">';
					echo $player['name'];
					echo '</option>';
				}
				?></select><br><?php 
			}
		}
	}
}





?>