<?php
/*
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "lounge", 
"sub" => "lounge");
uli_header();

$view = $_REQUEST['view'];

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
	$('input.score1').change(
		function() {
			var team1 = $("select.team1").val();
			var goals1 = $(this).val();
			$.ajax({
				type: "POST", url: "ajax_lounge.php", data: "action=form&team1=" + team1 + "&goals1=" + goals1 + "&teamnumber=1",
				complete: function(data){
					$("#scorer1").html(data.responseText);
				}
			 });

		});	
	$('input.score2').change(
			function() {
				var team2 = $("select.team2").val();
				var goals2 = $(this).val();
				$.ajax({
					type: "POST", url: "ajax_lounge.php", data: "action=form&team2=" + team2 + "&goals2=" + goals2 + "&teamnumber=2",
					complete: function(data){
						$("#scorer2").html(data.responseText);
					}
				 });

			});		
});
</script>




<?php 

echo '<div id="container">';
echo '</div>';

echo '<div class="LeftColumn">';
echo "\n";


echo "\n";

echo print_menue($year, $view); 

echo "\n";
echo '</div>';
echo "\n\n";

echo '<div class="RightColumnLarge">';
	if ($view == "insertgame"){
		$ulis = get_ulis(1);
		
	//	print_r($ulis);
		foreach ($ulis as $uli){
			if (in_array($uli['ID'], array(15,16,20,101,45,17))){
				//echo $uli['uliname'];
				$loungeulis[] = $uli;
			}
		}
		?>
		<form action = "?action=insertgame" method="POST">
		<table class = "ulitable">
			<tr>
				<td>
				Teams
				</td>
				<td>
				<select class = "team1" name = "team1" id="team1">
					<option>Bitte Team 1 w&auml;hlen</option>
					<?php 
					foreach ($loungeulis as $uli){
						echo '<option value = "'.$uli['ID'].'">'.$uli['uliname'].'</option>';	
					}
					?>
				</select>
				</td>
				<td>
				<select class = "team2" name = "team2" id="team2">
					<option>Bitte Team 2 w&auml;hlen</option>
					<?php 
					foreach ($loungeulis as $uli){
						echo '<option value = "'.$uli['ID'].'">'.$uli['uliname'].'</option>';	
					}
					?>
				</select>
				</td>
			</tr>
			<style>
			input[type="number"]::-webkit-outer-spin-button { display: none; }
			</style>
			<?php 
			$items = get_items();
			foreach ($items as $item){
				?>
				<tr>
					<td><?php echo $item['desc']?></td>
					<td><input class = "<?php echo $item['name'];?>1" value = "<?php echo $game['team1'][$item['name']]; ?>" size = 3 type = "tel" name = "<?php echo $item['name'];?>1"></td>
					<td><input class = "<?php echo $item['name'];?>2" value = "<?php echo $game['team2'][$item['name']]; ?>" size = 3 type = "tel" name = "<?php echo $item['name'];?>2"></td>
				</tr>
				<?
			}
			?>
		<tr>
			<td>
			</td>
			<td id = "scorer1"></td>
			<td id = "scorer2"></td>

		</tr>
		<tr>
			<td colspan = "3">
				<input type = "submit" value = "Eingeben"></input>
			</td>
		</tr>
		</table>
		</form>
		
		<?php 
	}

	
	
	
if ($_REQUEST['action'] == "insertgame"){
	//print_r($_POST);
	// das spiel
	$form['ID'] = $_POST['gameID'];
	$form['team1'] = $_POST['team1'];
	$form['team2'] = $_POST['team2'];
	$form['score1'] = $_POST['score1'];
	$form['score2'] = $_POST['score2'];
	$form['timestamp'] = mktime();
	$gameID = update_game($form);
	unset($form);
	$items = get_items();
	foreach ($items as $item){
		$form['gameID'] = $gameID;
		$form['uliID'] 	= $_POST['team1'];
		$form['item'] = $item['name'];
		$form['value'] = $_POST[$item['name'].'1'];
		$statsID = update_stats($form);
		unset($form);
		$form['gameID'] = $gameID;
		$form['uliID'] 	= $_POST['team2'];
		$form['item'] = $item['name'];
		$form['value'] = $_POST[$item['name'].'2'];
		$statsID = update_stats($form);
		unset($form);
		//echo '<br><br>';
	}
	for ($x=1; $x<=$_POST['score1']; $x++){
		$form['playerID'] = $_POST['scorer-1-'.$x];
		$form['gameID'] = $gameID;
		$form['uliID'] 	= $_POST['team1'];
		$scorerID = update_scorer($form);
		unset($form);
		
	}
	for ($x=1; $x<=$_POST['score2']; $x++){
		$form['playerID'] = $_POST['scorer-2-'.$x];
		$form['gameID'] = $gameID;
		$form['uliID'] 	= $_POST['team2'];
		$scorerID = update_scorer($form);
		unset($form);
	}

$view = "tabelle";
}

if ($view == "tabelle" OR !$view){

	$results = uli_get_results("lounge_games");
	
	$punkte[15] = 0; 
	$punkte[16] = 0; 
	$punkte[20] = 0; 
	$punkte[17] = 0; 
	$punkte[45] = 0; 
	$punkte[101] = 0; 
	
	if ($results){
		// schauen, wer welche punkte hat
		foreach ($results as $game){
			if ($game['score1'] > $game['score2']){
				$punkte[$game['team1']] = $punkte[$game['team1']] + 3;
			}
			elseif ($game['score1'] < $game['score2']){
				$punkte[$game['team2']] = $punkte[$game['team2']] + 3;
			}
			elseif ($game['score1'] == $game['score2']){
				$punkte[$game['team1']] = $punkte[$game['team1']] + 1;
				$punkte[$game['team2']] = $punkte[$game['team2']] + 1;
			}
			else {
				$punkte[$game['team1']] = $punkte[$game['team1']] + 0;
				$punkte[$game['team2']] = $punkte[$game['team2']] + 0;
			}			
		$tore[$game['team1']] = $tore[$game['team1']] + $game['score1'];
		$tore[$game['team2']] = $tore[$game['team2']] + $game['score2'];
		$gegentore[$game['team2']] = $gegentore[$game['team2']] + $game['score1'];
		$gegentore[$game['team1']] = $gegentore[$game['team1']] + $game['score2'];
		$spiele[$game['team1']] = $spiele[$game['team1']] + 1;
		$spiele[$game['team2']] = $spiele[$game['team2']] + 1;
		}
		arsort($punkte);
	}
	$x = 1;
	foreach ($punkte as $key => $punkte){
		$uli = get_uli($key);
		$uliname = $uli['uliname'];

		$items = get_items();
		foreach($items as $item){
			$cond[] = array("col" => "item", "value" => $item['name']);
			$cond[] = array("col" => "uliID", "value" => $key);
			$result = uli_get_results('lounge_stats', $cond, array('SUM(value) as value', 'COUNT(ID) as count'));
			//print_r($result);
			if ($result[0]['count'] > 0){
				$stats[$item['name']] = round($result[0]['value'] / $result[0]['count'],2);
			}
			unset($cond);		
		}
		if ($spiele[$key] > 0){
			$durchschnittspunkte = round($punkte/$spiele[$key],2);
		}
		else {
			$durchschnittspunkte = 0;
			}
		$data[$x][] = $uliname;
		$data[$x][] = $spiele[$key];
		$data[$x][] = $tore[$key];
		$data[$x][] = $gegentore[$key];
		$data[$x][] = $stats['shots'];
		$data[$x][] = $stats['ongoal'];
		$data[$x][] = $stats['tackles'];
		$data[$x][] = $stats['fouls'];
		$data[$x][] = $stats['corners'];
		$data[$x][] = $stats['yellow'];
		$data[$x][] = $stats['red'];
		$data[$x][] = $stats['possesion'];;
		$data[$x][] = $stats['passpercent'];
		$data[$x][] = $punkte;
		$data[$x][] = $durchschnittspunkte;
		unset($stats);
		$x++;
	}
	$colh[1] = Team;
	$colh[2] = "Sp.";
	$colh[3] = "T";
	$colh[4] = "GT";
	$colh[5] = "Sch.";
	$colh[6] = "Sch./T";
	$colh[7] = "Zw";
	$colh[8] = "Fouls";
	$colh[9] = "Eck.";
	$colh[10] = "Gelb";
	$colh[11] = "Rot";
	$colh[12] = "BB";
	$colh[13] = "P%";
	$colh[14] = "Pt.";
	$colh[15] = "Pt./Sp.";

	if ($colh AND $data){
		echo $content = uli_table($colh, $data, '');
	}
}

if ($view == "games"){
	
	$ulis = get_ulis(1);
	foreach ($ulis as $uli){
		$thisulis[$uli['ID']] = $uli;
	}	

	$items = get_items();
	$order[] = array("col" => "timestamp", "sort" => "DESC");	
	$results = uli_get_results("lounge_games", NULL, NULL, $order);
	if ($results){
		foreach ($results as $game){
			// get stats
			$stats = get_stats($game['ID']);
			$headline = uli_date($game['timestamp']).' ';
			$headline .= $thisulis[$game['team1']]['uliname'].' vs. '.$thisulis[$game['team2']]['uliname'];
			$headline .= '<span style="float: right;">'.$game['score1'].' : '.$game['score2'].'</span>';
			$text = '';
			//print_r($stats);
			if ($stats){
				foreach ($stats as $stat){
					$thisstats[$stat['uliID']][$stat['item']] = $stat['value'];
				}
				//print_r($thisstats);
				foreach ($items as $item){
					$text .= '<div class ="stats">';
					$text .= '<span style = "width: 32%; float: left;">'.$thisstats[$game['team1']][$item['name']].'</span>';
					$text .= '<span style = "width: 32%; float: left;text-align: center;">'.$item['desc'].'</span>';
					$text .= '<span style = "width: 33%; float: left;text-align: right;">'.$thisstats[$game['team2']][$item['name']].'</span>';
					$text .= '</div>';
				}
			}
			
			// Torschuetzen
			$scorers = get_scorer($game['ID']);
			//print_r($scorers);
			
			if ($scorers){
				$text .= '<div class "scorer">Torsch&uuml;tzen: ';
				foreach ($scorers as $scorer){
					//echo $scorer['playerID'];
					$player = get_player_infos($scorer['playerID']);
					//print_r($player);
					$text .= $player['name'].' ('.$thisulis[$scorer['uliID']]['uliname'].') | ';
				}
				$text .= '</div>';
			}
			echo uli_box($headline, $text);
		}
	}
}

if ($view == "scorer"){
	$ulis = get_ulis(1);
	foreach ($ulis as $uli){
		$thisulis[$uli['ID']] = $uli;
	}	
	
	$scorer = uli_get_results("lounge_scorer");
	if ($scorer){
		foreach ($scorer as $scorer){
			$thisscorer[$scorer['playerID']] = $thisscorer[$scorer['playerID']] + 1;
			$thisuliplayer[$scorer['playerID']] = $scorer['uliID'];
		}
	}
	arsort($thisscorer);
	$colh[1] = Platz;
	$colh[2] = "Spieler";
	$colh[3] = "Tore";		

	$x=1;
	foreach($thisscorer as $player => $goals){
		$playerinfos = get_player_infos($player);
		$data[$x][] = $x.'.';
		$data[$x][] = $playerinfos['name'].' ('.$thisulis[$thisuliplayer[$player]]['uliname'].')';
		$data[$x][] = $goals;
		$x = $x + 1;
	}
	if ($colh AND $data){
		echo $content = uli_table($colh, $data, '');
	}
}

echo '</div>';
echo "\n";

?>

<script>

</script>
<?php 

/* Footer */
uli_footer();



function get_scorer($gameID){
	$cond[] = array("col" => "gameID", "value" => $gameID);
	$results = uli_get_results('lounge_scorer', $cond);
	return $results;
}

function get_stats($gameID){
	$cond[] = array("col" => "gameID", "value" => $gameID);
	$results = uli_get_results('lounge_stats', $cond, NULL, $order);
	return $results;
}
function update_scorer($form){
	foreach ($form as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	if ($form['ID']){
		$cond[] = array("col" => "ID", "value" => $form['ID']);
		$ID = uli_update_record('lounge_scorer', $cond, $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
	else {
		$ID = uli_insert_record('lounge_scorer', $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
}

function update_stats($form){
	foreach ($form as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	if ($form['ID']){
		$cond[] = array("col" => "ID", "value" => $form['ID']);
		$ID = uli_update_record('lounge_stats', $cond, $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
	else {
		$ID = uli_insert_record('lounge_stats', $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
}

function update_game($form){
	foreach ($form as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	if ($form['ID']){
		$cond[] = array("col" => "ID", "value" => $form['ID']);
		$ID = uli_update_record('lounge_games', $cond, $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
	else {
		$ID = uli_insert_record('lounge_games', $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
}

function get_items(){
	$items[] = array("name" => "score", "desc" => "Ergebnis");
	$items[] = array("name" => "shots", "desc" => "Sch&uuml;sse");
	$items[] = array("name" => "ongoal", "desc" => "Aufs Tor");
	$items[] = array("name" => "possesion", "desc" => "Ballbesitz");
	$items[] = array("name" => "tackles", "desc" => "Zweik&auml;mpfe");
	$items[] = array("name" => "fouls", "desc" => "Fouls");
	$items[] = array("name" => "yellow", "desc" => "Gelbe Karten");
	$items[] = array("name" => "red", "desc" => "Rote Karten");
	$items[] = array("name" => "corners", "desc" => "Ecken");
	$items[] = array("name" => "passpercent", "desc" => "% P&auml;sse");
	
	return $items;
}

function print_menue(){
	global $option;

	$SelectOptions[] = array("view" => "tabelle", "desc" => Tabelle);
	$SelectOptions[] = array("view" => "insertgame", "desc" => "Spiel eingeben");
	$SelectOptions[] = array("view" => "scorer", "desc" => "Torsch&uuml;tzen");
	$SelectOptions[] = array("view" => "games", "desc" => "Alle Spiele");
	

	$html .= "\n";
	if ($SelectOptions){
		foreach ($SelectOptions as $SelectOption){
			$active = '';
			if ($view == $SelectOption['view']){$active = 'active';}
			$html .= '<a href="?view='.$SelectOption['view'].'&amp;year='.$year.'" class="'.$active.'">'.$SelectOption['desc'].'</a>';
			$html .= '<br/>';
			$html .= "\n";
		}}
		$html .= "\n";
		$html = uli_box("Lounge Modus", $html);
		return $html;
}

?>