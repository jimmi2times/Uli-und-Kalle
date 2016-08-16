<?php
require_once('../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');



/*
 * globale TODOs zum Start
 *
 * Spielerverwaltung Neue Sterne
 * Startseite
 * Textbausteine
 * Vielleicht noch eine bessere Message Systematik (Transfermarkt)
 * Bank Ligenvergleich

 * Aufstellung (mit neuer Systematik Nebenpositonen)
 * ganz kurze Stats (Transfers, Manager, etc.)

 * Spieleradmin (neue Spieler, Transfers)
 *
 *
 * TODOs spaeter
 * Sponsoring
 * Aufstellung speichern
 * Optionen Manager, Bild hochladen
 * viele geile Stats
 * Finanzbereich Kontoauszug
 * Stadion
 * Zeitung
 *
 * TODO sehr spaeter
 *
 *
 *
 */


/* Header */
$page = array("main" => "start", "sub" => "start", "name" => "Startseite");
uli_header(array('_transfermarkt/lib_transfermarkt', '_start/lib_communication'));

global $option;



?>
<script>
$(document).ready(function(){

		$('#journal').show();
		$('#journal').dialog({
			  height: 430,
			  width: 480,
			  position: { 
			  my: "right bottom",
			  at: "right bottom",
			    of: $('#pos')
			  }
			});

		
		$('#twitter_uli').show();
		$('#twitter_uli').dialog({
			  height: 200,
			  width: 250,
			  position: { 
			  my: "right bottom",
			  at: "right bottom",
			    of: $('#twitter_pos')
			  }
			});
		

	$(".alleslesen").live('click', function() {
		$.ajax({
			type: "POST", url: "ajax_newspaper.php", data: "action=wholenewspaper",
			complete: function(data){
				$("#journal").html(data.responseText);
			}
		 });
		
	});


});
</script>

<style type="text/css">
#messages {
	
}

#transfers {
	
}




</style>





<?php


//the_widget("widget_reallysimpletwitterwidget");


$TR = get_TR($option['uliID']);
//print_r($TR);


if ($TR){
?>
	<div class="teamranking">
		<span style="width: <?php echo 200*$TR['TR_gesamt']/100; ?>px;">Status: <?php echo $TR['TR_gesamt']; ?>/100</span>
	</div>
<?php 
}

echo '<div id="pos" style="margin: 0px; height: 450px; width: 500px;">';
	echo '<div id="journal" style="display:none;">';
		echo '<div class="jour-header"></div>';
		// Holt alle News
		$news = get_news(1, TRUE);
		
		if ($news){
			foreach($news as $aufmacher){
			$sticky = $aufmacher['ID'];
			// Das ist der Aufmacher
			echo '<div class="jour-article">';
				//echo '<div class="jour-pic">';
				//echo get_player_pic($aufmacher['playerID']);
				//echo '</div>';
				echo '<div class="xxjour-text">';
				echo '<h3 class="jour-headline">';
				echo $aufmacher['headline'];
				echo '</h3>';
				echo uli_date($aufmacher['timestamp']).' | ';
				echo $aufmacher['text'];
				echo '</div>';
			echo '</div>';
			echo '<div class="clearer"></div>';		
			}
		}	
		$news = get_news(15);
		if ($news){
			foreach($news as $aufmacher){
			if ($sticky != $aufmacher['ID']){
				// Das ist der Aufmacher
				echo '<div class="jour-article">';
					//echo '<div class="jour-pic">';
					//echo get_player_pic($aufmacher['playerID']);
					//echo '</div>';
					echo '<div class="xxjour-text">';
					echo '<h3 class="jour-headline">';
					echo $aufmacher['headline'];
					echo '</h3>';
					echo uli_date($aufmacher['timestamp']).' | ';
					echo $aufmacher['text'];
					echo '</div>';
				echo '</div>';
				echo '<div class="clearer"></div>';		
				}
			}
		}		

		//echo '<div class="jour-header"></div>';
		// Holt die drei Top-News
		/*
		$news = get_news(2);
		
		if ($news){
			// Das ist der Aufmacher
			$aufmacher = $news[0];	
			echo '<div class="jour-article">';
				//echo '<div class="jour-pic">';
				//echo get_player_pic($aufmacher['playerID']);
				//echo '</div>';
				echo '<div class="xxjour-text">';
				echo '<h3 class="jour-headline">';
				echo $aufmacher['headline'];
				echo '</h3>';
				echo $aufmacher['text'];
				echo '</div>';
			echo '</div>';
		
			
			echo '<div class="clearer"></div>';		
			/*
			$secondarticle = $news[1];
			$thirdarticle = $news[2];
			echo '<div class="jour-article">';
				echo '<div class="xxjour-text">';
				echo '<h3 class="jour-headline">';
				echo $secondarticle['headline'];
				echo '</h3>';
				echo $secondarticle['text'];
				echo '</div>';
			echo '</div>';
			
			}
			*/
			/*
			echo '<div class="clearer"></div>';	
			echo '<div class="jour-bottom-row">';
			
				echo '<div class="jour-fc">';
				echo '<h3 class="jour-headline">';
				echo 'Immer seri&ouml;s';
				echo '</h3>';
				echo 'Die Uli & Kalle Schreiberlinge wissen alles. Und haben beste Kontakte. Und nur ganz selten sind ihre Behauptungen haltlose Spekulationen.';
				echo '</div>';
				
				echo '<div class="jour-sc">';
				echo '<h3 class="jour-headline">';
				echo 'Transfer aktuell';
				echo '</h3>';
				echo get_next_auction($uliID, $option['leagueID']);
				echo get_last_transfer($uliID, $option['leagueID']);
				echo '</div>';
				
				echo '<div class="jour-tc">';
				echo '<h3 class="jour-headline">';
				echo 'Tabelle';
				echo '</h3>';
				$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
				$cond[] = array("col" => "year", "value" => $option['currentyear']);
				$cond[] = array("col" => "round", "value" => 0);
				$uliname = get_all_uli_names($option['leagueID']);
				$order[] = array("col" => "score", "sort" => "DESC");
				$result = uli_get_results('results', $cond, NULL, $order, 5);
				if ($result){
					$x = 1;
					foreach ($result as $result){
						echo $x.'. ';
						echo round($result['score'], 2);
						echo ' '.$uliname[$result['uliID']]. '<br/>';
						$x = $x + 1;					
					}
				}
				
				echo '';
				
				echo '</div>';
			
				echo '<span class="alleslesen"><b>>> Alles lesen</b></span>';
			

			echo '</div>';	
			*/	
	echo '</div>';
echo '</div>';
	
	echo '<div id ="twitter_pos">';
		echo '<div id ="twitter_uli" title="Info">';
			echo '<div class="jour-bottom-row">';
				echo '<div class="jour-sc">';
				echo '<h3 class="jour-headline">';
				echo 'Transfer aktuell';
				echo '</h3>';
				echo get_next_auction($uliID, $option['leagueID']);
				echo get_last_transfer($uliID, $option['leagueID']);
				echo '</div>';
				
				echo '<div class="jour-tc">';
				echo '<h3 class="jour-headline">';
				if ($option['leagueID']){
					echo 'Tabelle';
					echo '</h3>';
					$cond[] = array("col" => "leagueID", "value" => $option['leagueID']);
					$cond[] = array("col" => "year", "value" => $option['currentyear']);
					$cond[] = array("col" => "round", "value" => 0);
					$uliname = get_all_uli_names($option['leagueID']);
					$order[] = array("col" => "score", "sort" => "DESC");
					$result = uli_get_results('results', $cond, NULL, $order, 5);
					if ($result){
						$x = 1;
						foreach ($result as $result){
							echo $x.'. ';
							echo round($result['score'], 2);
							echo ' '.$uliname[$result['uliID']]. '<br/>';
							$x = $x + 1;					
						}
					} else {
						echo NoTable;
					}
					echo '';
				}
				echo '</div>';
			echo '</div>';	
		echo '</div>';
	echo '</div>';
/*

?>
<div id ="twitter_pos">
<div id = "twitter_uli" title="@Uli_und_Kalle (Twitter)"  style="display:none;">
<?php 
$tw_options['username'] = "Uli_und_Kalle";
$tw_options['num'] = 5;
echo really_simple_twitter_messages($tw_options);
echo '<span class = "twitter_span"><a href="/">&nbsp;</a></span>';
echo '<i>Kurze Updates via <a href="http://twitter.com/uli_und_kalle">Twitter</a> | <a href="http://www.facebook.com/UliUndKalle">Facebook</a></i>';
?>
</div>
</div>
<?php 
*/
/* Footer */
uli_footer();


/* Transferdaten f�r Startseite */
function get_next_auction($uliID, $leagueID) {

	// Naechste Auktion
	$cond = array();
	$order = array();
	$cond[] = array("col" => "end", "value" => 0, "func" => ">");
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$cond[] = array("col" => "history", "value" => 0);
	$order[] = array("col" => "end", "sort" => "ASC");
	$auction = uli_get_row('auctions', $cond, $order);
	if ($auction) {
		$date = uli_date($auction['end'], 1, 'nobr');
		$player = get_player_infos($auction['playerID']);
		$html .= ''.NextAuction.': ';
		$html .= $date.' | <span class="playerinfo" id = "'.$player['playerID'].'">'.$player['name'].'</span> | ';
		// $html .= '<br/>';
	} else {
		$html .= NoAuctions.' *** ';
	}

	return $html;
}
	
	
function get_last_transfer($uliID, $leagueID) {
	// Letzte Feindliche Uebernahme
	// Letzter Transfer
	$cond = array();
	$order = array();
	$cond[] = array("col" => "leagueID", "value" => $leagueID);
	$order[] = array("col" => "time", "sort" => "DESC");
	$transfer = uli_get_row('transfers', $cond, $order);
	if ($transfer) {
		/* Es werden alle Ulinamen eingelesen */
		$uliname = get_all_uli_names($option['leagueID']);
		/* Es werden alle Bundesligateamnamen eingelesen */
		$ligateam = get_all_team_names();
		$date = uli_date($transfer['time']);
		$zu = $uliname[$transfer['ulinew']];
		if (!$zu){$zu = $transfer['externnew'];}
		if (!$zu){$zu = $ligateam[$player['team']];}
		$von = $uliname[$transfer['uliold']];
		if (!$von){$von = $transfer['externold'];}
		if (!$von){$von = $ligateam[$player['team']];}
		if (!$von){$von = 'unbekannt';}
		$player = get_player_infos($transfer['playerID']);
		$html .= ''.LastTransfer.': ';
		$html .= $date.' | <span class="playerinfo" id = "'.$player['playerID'].'">'.$player['name'].'</span>';
		$html .= ' | '.uli_money($transfer['sum']).' (von '.$von.' zu '.$zu.')';
	}
	return $html;
}

?>
