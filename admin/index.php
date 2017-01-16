<?php
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');


// Admin fuer Spieler aller Art

// Transfers
// wie Transfermarkt?
// Was muss fuer Spieler gemanaged werden
// Transfer | Position | Fuss | KickerID | Geburtstag | Stern (erledigt) | Groesse/Gewicht | diverse Checks | Berechnungen neu anstossen





// was sollte ueberprueft werden
// 1. welche Spieler sind nicht in einem Kader und haben keine 0er Auktion (ist da)
// 2. Welche Spieler stehen in der Kicker-Import-XML und sind nicht im Spiel
// --> Sind diese Spieler in der player-Datenbank und haben kein Team (999)
// --> muessen sie neu eingegeben werden




/* Header */
$page = array("main" => "start1", "sub" => "start1");
uli_header(array("lib_admin"));
?>

<script type="text/javascript">
// Livesearch
$(document).ready(function() {
	$("#newplayer").keyup(function()
		{
		var playernameinput = $(this).val();
		var dataString = 'newplayername='+ playernameinput;
		if(playernameinput.length > 3)
			{
			$.ajax({
			type: "POST",
			url: "ajax_admin.php",
			data: dataString,
			beforeSend:  function() {
			$('input#newplayer').addClass('loading');
			},
			success: function(server_response)
			{
			$('#searchresultdata').html(server_response).show();
			// $('span#title').html(playernameinput);

			if ($('input#newplayer').hasClass("loading")) {
			 	$("input#newplayer").removeClass("loading");
			 }
			}
		});
		}return false;
	});
});

// Neuen Spieler eingeben
$("#insertnewplayer").live({ click: function(){
	//alert('hu');
	var newteam = $("#newplayerteam").attr("value");
	var playername = $("#newplayer").attr("value");
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=insertnewplayer&playername=" + playername + "&team=" + newteam,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		});
	return false;
	}
});

// Spieler aus der live-search reaktivieren
$(".playertoreactivate").live({ click: function(){
	var ligateamnew = $("#newplayerteam").attr("value");
	var playerid = this.id;
	var sum = 0;
	var externold = 'Archiv';
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=admintrade&playerID=" + playerid + "&sum=" + sum + "&externold=" + externold + "&ligateamnew=" + ligateamnew,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		 });
	}
});


//positionen aendern
$(".positions select").live({ change: function(){
var positionplayer = this.id;
var newposition = $("#" + this.id).attr("value");

	//send the post to shoutbox.php
	$.ajax({
		type: "POST", url: "ajax_admin.php", data: "action=changeplayer&positionplayer=" + positionplayer + "&newposition=" + newposition,
		complete: function(data){
		// Erst einmal Container. spaeter schoener
			$("#container").html(data.responseText);
		}
	 });
}
});

// Neuen Status berechnen
$(".statuscheck").live({ click: function(){
	var playerID = this.id;
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=calculatestatus&playerID=" + playerID,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		 });
	}
});

// Neuen MW berechnen
$(".mwcheck").live({ click: function(){
	var playerID = this.id;
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=calculatemarktwert&playerID=" + playerID,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		 });
	}
});

//admintrade
$(".admintrade").live({ submit: function(){
var playerid = this.id;
var sum = $("#sum-auction" + playerid).attr("value");
var externnew = $("#transfer-extern" + playerid).attr("value");
var ligateamnew = $("#ligateamnew" + playerid).attr("value");
	$.ajax({
		type: "POST", url: "ajax_admin.php", data: "action=admintrade&playerID=" + playerid + "&sum=" + sum + "&externnew=" + externnew + "&ligateamnew=" + ligateamnew,
		complete: function(data){
			// Erst einmal Container. spaeter schoener
			$("#container").html(data.responseText);
		}
	 });
return false;
}
});


$(".deleteplayerleague").live({ click: function(){
	var playerleagueID = this.id;
	//send the post to shoutbox.php
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=deleteplayerleague&playerleagueID=" + playerleagueID,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		 });
	return false;
	}
	});


$(".deletecontract").live({ click: function(){
	var contractid = this.id;
	//send the post to shoutbox.php
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=deletecontract&contractid=" + contractid,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		 });
	//we prevent the refresh of the page after submitting the form
	return false;
	}
	});

// filter
$(document).ready(function(){
	// Filtercheckboxen
	$('input.filtercheckbox').change(
		    function() {
				var filter = "." + this.id;
		    	if ($(this).is(':checked')) {
				    if ($(filter).hasClass('positionhide')){$(filter).removeClass('positionhide');$(filter).addClass('positionshow');}
			    } else {
				    if ($(filter).hasClass('positionshow')){$(filter).removeClass('positionshow');$(filter).addClass('positionhide');}
			    }
		});
});


$("a.tm_id").live({ click: function(){
	var tm_id = this.id;
	//send the post to shoutbox.php
	//alert(tm_id);
	var playerID = $(this).data("playerid");
	//alert(playerID);
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=set_tm_id&tm_id=" + tm_id + "&playerID=" + playerID,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		 });
	//we prevent the refresh of the page after submitting the form
	return false;
	}
	});

$(".form_tm_id").live({ submit: function(){
	var tm_id = this.id;
	//send the post to shoutbox.php
	//alert(tm_id);
	var playerID = $("#playerid-" + tm_id).attr("value");
	//alert(playerID);
		$.ajax({
			type: "POST", url: "ajax_admin.php", data: "action=set_tm_id&tm_id=" + tm_id + "&playerID=" + playerID,
			complete: function(data){
				// Erst einmal Container. spaeter schoener
				$("#container").html(data.responseText);
			}
		 });
	//we prevent the refresh of the page after submitting the form
	return false;
	}
	});


$(document).ready(function(){
	$('input.Blickfeld').change(
		    function() {if ($(this).is(':checked')) {var star = 4;}
		        else {var star = 0;  }
				var playerid = this.id;var messageList = $("#container");
				$.ajax({
					type: "POST", url: "ajax_admin.php", data: "action=changestars&star=" + star + "&playerID=" + playerid,
					complete: function(data){
						messageList.html(data.responseText);
					}
				 });
		});
	$('input.WeitererKreis').change(
		    function() {if ($(this).is(':checked')) {var star = 3;}
		        else {var star = 0;  }
				var playerid = this.id;var messageList = $("#container");
				$.ajax({
					type: "POST", url: "ajax_admin.php", data: "action=changestars&star=" + star + "&playerID=" + playerid,
					complete: function(data){
						messageList.html(data.responseText);
					}
				 });
		});
	$('input.IK').change(
		    function() {if ($(this).is(':checked')) {var star = 2;}
		        else {var star = 0;  }
				var playerid = this.id;var messageList = $("#container");
				$.ajax({
					type: "POST", url: "ajax_admin.php", data: "action=changestars&star=" + star + "&playerID=" + playerid,
					complete: function(data){
						messageList.html(data.responseText);
					}
				 });
		});
	$('input.WK').change(
		    function() {if ($(this).is(':checked')) {var star = 1;}
		        else {var star = 0;  }
				var playerid = this.id;var messageList = $("#container");
				$.ajax({
					type: "POST", url: "ajax_admin.php", data: "action=changestars&star=" + star + "&playerID=" + playerid,
					complete: function(data){
						messageList.html(data.responseText);
					}
				 });
		});
})

$(document).ready(function(){
	$(".selectfilter").change(
		    function() {
		    	var teamview = $(this).val();
		    	var param = "?teamview=" + teamview + "&view=editplayer";
				window.location.search = param;
		});
});
</script>



<?php
$view = $_REQUEST['view'];
$teamview = $_REQUEST['teamview'];
$teamview = str_replace("team", "", $teamview);
$action = $_REQUEST['action'];
$year = $_REQUEST['year'];
global $option;







echo '<div id="container">';
echo '</div>';

/* Ausgabe der Seite */
echo '<div class="LeftColumn">';
echo "\n";
echo print_admin_player_menue();
echo "\n";
echo print_admin_menu_calculate_rounds();

//echo uli_box("TODO", "Was muss hier noch hin? Ausrechnen pro Spieltag. Finanzen pro Spieltag. Neu eingeben von Spielern (bzw. Suchen von Spielern). Einladen der xml Datei vom Kicker. (Idealerweise anschieben des Skriptes)");
//echo uli_box("TODO2", "Aendern der Namen von Spielern. UliKlubs loeschen. Abstieg von Bundesligateams. Vielleicht doch in zwei Seiten organisieren? Einmal berechnungen. Einmal Checks und Datenbankpflege.");


echo '</div>';
echo "\n\n";

echo '<div class="RightColumnLarge">';
echo "\n";
echo '<div id="stats">';
echo "\n";


?>

<?php


/*
 * Neue Liga anlegen
 * wird eigentlich durch die checks erledigt
 */


/*
 * Check Smile
 */
if ($action == "checksmiles"){
	$cond[] = array("col" => "playerID", "value" => 0);
	$results = uli_get_results("player_league_smile", $cond);
	if ($results){
		foreach($results as $auction){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $auction['ID']);
			uli_delete_record('player_league_smile', $cond);
		}
	}
	unset($cond);
	$cond[] = array("col" => "team", "value" => 999, "func" => "!=");
	$results = uli_get_results("player", $cond);
	$leagues = get_leagues();
	if ($results){
		foreach($results as $entry){
			unset($cond);
			if ($leagues){
				foreach ($leagues as $league){
					unset($cond);
					$cond[] = array("col" => "playerID", "value" => $entry['ID']);
					$cond[] = array("col" => "leagueID", "value" => $league['ID']);
					$player = uli_get_results("player_league_smile", $cond);
					if (!$player){
					$playerID = $entry['ID'];
					// Datensaetze in der player_league_smile_tabelle anlegen
					unset ($value);
					//echo $playerID;

					echo update_smile($playerID, $league['ID'], NULL, 50, 1, $option['currentyear']);
					}
				}
			}
		}
	}
}

/*
 * wer ist denn aktiv?
 */


if ($action == "activeulis"){
	//print_r($option);
	$leagues = get_leagues();
	if ($leagues){
		foreach ($leagues as $league){
			$ulis = get_ulis($league['ID']);
			if ($ulis){
				foreach ($ulis as $uli){
					echo $uli['uliname']. ' ('.$uli['ID'].')';

					// Check, ob es im aktuellen Jahr Punkte gab
					unset($cond);
					$cond[] = array("col" => "uliID", "value" => $uli['ID']);
					$cond[] = array("col" => "year", "value" => $option['currentyear']);
					$cond[] = array("col" => "round", "value" => 0);
					$score = uli_get_row("results", $cond);
					echo '<br/>';
					if ($score['score'] < 1){echo '<b>';}
					echo $score['score'].' Punkte';
					if ($score['score'] < 1){echo '</b>';}

					echo '<br/>';
					echo '<br/>';
				}
			}
		}
	}
}


// Schaut nach, ob dieses Team aktiv ist
if ($action == "checkuli"){
	$uliID = $_REQUEST['uliID'];
	$uli = get_uli($uliID);
	echo '<h3>'.$uli['uliname'].'</h3>';

	// Gibt es spieler
	$players = get_user_team($uliID);
	echo count($players).' Spieler<br>';

	// Wie viel Geld
	echo uli_money(get_value_bank(14, 0,0, $uliID)).' Euro auf dem Konto<br/>';


}

// Vorstufe vom Verein löschen, wenn es geht, kommt es dann in das richtige rein
if ($action == "sellteams"){
	$uliID = $_REQUEST['uliID'];
	$uli = get_uli($uliID);
	// Alle Verträge auf Ende = 0 setzen

echo $uliID;

	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "history", "value" => 0);
	$values[] = array("col" => "end", "value" => (time()-10000));
	uli_update_record('player_contracts', $cond, $values);

	check_contracts();
	check_contracts();
	check_contracts();
	check_contracts();

	$cond = array();

}


if ($action == "deleteuli"){
	$uliID = $_REQUEST['uliID'];

	$uli = get_uli($uliID);

	echo '<h1>DELETE</h1>';





	// Was muss gelöscht werden
 	$cond[] = array("col" => "uliID", "value" => $uliID);

 	// finances
 	uli_delete_record('finances', $cond);

 	// kredite
 	uli_delete_record('kredite', $cond);

 	// soldtrikots
 	uli_delete_record('merch_soldtrikots', $cond);

 	// player_contract_negotiations
 	uli_delete_record('player_contract_negotiations', $cond);

 	// results
 	uli_delete_record('results', $cond);


 	// stadium
 	uli_delete_record('stadium', $cond);

 	// stadium_infra
 	uli_delete_record('stadium_infra', $cond);

 	// stadium_name
 	uli_delete_record('stadium_name', $cond);

 	// stadium_seats
 	uli_delete_record('stadium_seats', $cond);

 	// finances
 	uli_delete_record('finances', $cond);


 	// team_ranking
 	uli_delete_record('team_ranking', $cond);
 	// tv_contracts
 	uli_delete_record('tv_contracts', $cond);
 	// userformation
 	uli_delete_record('userformation', $cond);
 	// userteams
 	uli_delete_record('userteams', $cond);


 	unset($cond);

  	$cond[] = array("col" => "team_id", "value" => $uliID);
 	// sponsoring
 	uli_delete_record('sponsoring', $cond);

 	unset($cond);
 	// transfers umschreiben
 	$cond[] = array("col" => "uliold", "value" => $uliID);
 	$values[] = array("col" => "uliold", "value" => NULL);
 	$values[] = array("col" => "externold", "value" => $uli['uliname']);
 	uli_update_record('transfers', $cond, $values);

 	unset($cond);
 	unset($values);

 	// transfers umschreiben
 	$cond[] = array("col" => "ulinew", "value" => $uliID);
 	$values[] = array("col" => "ulinew", "value" => NULL);
 	$values[] = array("col" => "externnew", "value" => $uli['uliname']);
 	uli_update_record('transfers', $cond, $values);

 	unset($cond);
 	unset($values);
 	$cond[] = array("col" => "ID", "value" => $uliID);
 	uli_delete_record('uli', $cond);

}


if ($action == "checks"){


	// Beim Löschen eines Uli Teams gab es einen Bug. Sitze im Stadion wurden nicht mitgelöscht
	// Wenn es Sitze gibt, die keinen "Besitzer haben, werden diese hier gelöscht"
	// Das haut nicht hin, warum nicht??????? TODO
	$results = uli_get_results("stadium_seats");
	if ($results){
		foreach ($results as $seat){
			unset($cond);
			$thisUli = array();
			//echo $seat['uliID'].'<br/>';
			$thisUli = get_uli($seat['uliID']);
			if (!$thisUli['ID']){
				$cond[] = array("col" => "ID", "value" => $seat['ID']);
				uli_delete_record('stadium_seats', $cond);
			}
		}
	}

	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	uli_delete_record('stadium_seats', $cond);


	// auktionen playerID = 0
	unset($cond);
	$cond[] = array("col" => "playerID", "value" => 0);
	$results = uli_get_results("auctions", $cond);
	if ($results){
		foreach($results as $auction){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $auction['ID']);
			uli_delete_record('auctions', $cond);
		}
	}
	// finance Einträge ohne UliID
	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	$results = uli_get_results("finances", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $entry['ID']);
			uli_delete_record('finances', $cond);
		}
	}
	// merch SoldTrikots Einträge ohne UliID
	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	$results = uli_get_results("merch_soldtrikots", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $entry['ID']);
			uli_delete_record('merch_soldtrikots', $cond);
		}
	}
	// Results ohne uliID
	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	$results = uli_get_results("results", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $entry['ID']);
			uli_delete_record('results', $cond);
		}
	}

	// Stadionname ohne uliID
	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	$results = uli_get_results("stadium_name", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $entry['ID']);
			uli_delete_record('stadium_name', $cond);
		}
	}

	// TV Verträge ohne uliID
	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	$results = uli_get_results("tv_contracts", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $entry['ID']);
			uli_delete_record('tv_contracts', $cond);
		}
	}

	// userformation ohne UliID
	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	$results = uli_get_results("userformation", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $entry['ID']);
			uli_delete_record('userformation', $cond);
		}
	}

	// userteams ohne UliID
	unset($cond);
	$cond[] = array("col" => "uliID", "value" => 0);
	$results = uli_get_results("userteams", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "ID", "value" => $entry['ID']);
			uli_delete_record('userteams', $cond);
		}
	}
	// Sponsoring ohne uliID (team_id)
	unset($cond);
	$cond[] = array("col" => "team_id", "value" => 0);
	$results = uli_get_results("sponsoring", $cond);
	if ($results){
		foreach($results as $entry){
			unset($cond);
			$cond[] = array("col" => "id", "value" => $entry['id']);
			uli_delete_record('sponsoring', $cond);
		}
	}

	// Player ohne Team != 999, der keinen Eintrag hat
	unset($cond);
	$cond[] = array("col" => "team", "value" => 999, "func" => "!=");
	$results = uli_get_results("player", $cond);
	$leagues = get_leagues();
	if ($results){
		foreach($results as $entry){
			unset($cond);
			if ($leagues){
				foreach ($leagues as $league){
					unset($cond);
					$cond[] = array("col" => "playerID", "value" => $entry['ID']);
					$cond[] = array("col" => "leagueID", "value" => $league['ID']);

					$player = uli_get_results("player_league", $cond);
					if (!$player){
					// Datensaetze in der player_league_tabelle anlegen
					unset ($value);
					$value[] = array("col" => "playerID", "value" => $entry['ID']);
					$value[] = array("col" => "leagueID", "value" => $league['ID']);
					$ID = uli_insert_record('player_league', $value);
					$playerID = $entry['ID'];
					update_smile($playerID, $league['ID'], NULL, 50, NULL, $option['currentyear']);
					}
				}
			}
		}
	}

	// Player ohne Team != 999 und ohne uliID, der keine Auktion hat
	unset($cond);
	$cond[] = array("col" => "team", "value" => 999, "func" => "!=");
	$results = uli_get_results("player", $cond);
	$leagues = get_leagues();
	if ($results){
		foreach($results as $entry){
			unset($cond);
			if ($leagues){
				foreach ($leagues as $league){
					$player = get_player_infos($entry['ID'], $league['ID']);
					if ($player['uliID'] == 0){
						unset($cond);
						$cond[] = array("col" => "history", "value" => 0);
						$cond[] = array("col" => "leagueID", "value" => $league['ID']);
						$cond[] = array("col" => "playerID", "value" => $entry['ID']);

						$auctions = uli_get_results("auctions", $cond);
						if (!$auctions){
							$auction = array();
							$auction['playerID']  = $entry['ID'];
							$auction['leagueID']  = $league['ID'];
							$auction['start']  	  = mktime();
							echo '<p>Fehlende Auktion für '.$player['name'].' in Liga '.$league['name'].' angelegt</p>';
							start_auction($auction);
						}
					}
				}
			}
		}
	}
}
// Ende Checks




/*
// Hack um Auktionen zu verdecken
$cond[] = array("col" => "history", "value" => 0);
$cond[] = array("col" => "topbet", "value" => 1, "func" => ">");
$results = uli_get_results("auctions", $cond);
if ($results){
	foreach ($results as $auction){
		$zufall = rand(1,3);
		if ($zufall == 1){
			$auction['hidden'] = 1;
			//print_r($auction);
			$bet = get_bet($auction['topbetID']);
			$auction['topbet'] = $bet['sum'];
			update_auction($auction);
		}
	}
}
*/

if ($view == "smile"){
	calculate_smile($_POST['round']);

}

if ($view == "repairtransfers"){
	repair_transfers();

}


if ($view == "check_verletzte"){
	check_verletzte();
}


if ($view == "checkfinances"){
	check_finances();

}
if ($view == "sponsyear"){
	calculate_uli_sponsoring_per_year($year);
}

if ($view == "editplayer"){
	// Filter
	$html .= '<div class="filter">';
	$html .= print_filter_positions();
	$html .= print_filter_team();
	//$html .= '<input type = "checkbox" id="OldPlayers">';
	//$html .= ' '.OldPlayers;
	$html .= "\n";
	$html .=  '</div>';
	$html .=  "\n\n";
	echo $html;

	echo edit_player($teamview);
}

if ($view == "reactivate"){
	// Filter
	$html .= '<div class="filter">';
	$html .= print_filter_positions();
	//$html .= '<input type = "checkbox" id="OldPlayers" checked = "checked">';
	//$html .= ' '.OldPlayers;
	$html .= "\n";
	$html .=  '</div>';
	$html .=  "\n\n";
	echo $html;

	echo edit_player('', 1);
}


if ($view == "editstars"){
	echo edit_stars();
}

// MArktwert
// TODO das hier ist nur fuer Liga "1"
if ($view == "marktwert"){
	calculate_marktwert_all();
}


// Check der doppelten Transfers
if ($view == "checktransfers"){
	check_double_transfers();
}

// Status der Spieler berechnen
if ($view == "status"){
	calculate_status_all_player();
}

//Check der doppelten Eintraege in der player_league_tabelle
if ($view == "checkplayerleague"){
	check_player_league_table(1);
}


if ($view == "calculateround"){
	calculate_round($_POST['round']);
}


if ($view == "merch"){
	calculate_merch($_POST['round']);
}

//Check der doppelten Eintraege in der player_league_tabelle
if ($view == "calculateteamranking"){
	$leagues = get_leagues();
	foreach ($leagues as $league){
		calculate_team_ranking($league['ID']);
	}
}

//Abstieg von Spielern
if ($view == "abstieg"){
	abstieg_players($_REQUEST['team']);
}


//Check der doppelten Eintraege in der player_league_tabelle
if ($view == "checkcontracts"){
	check_contracts_table(1);
}


//Check der doppelten Eintraege in der player_league_tabelle
if ($view == "everybodythere"){
	check_everybody(1);
}

if ($view == "calculateresults"){
	write_player_points_to_userteams($round);
	calculate_uli($round, $year);
}

if ($view == "newplayers"){
	echo print_new_player_form();
}


if ($view == "updateplayerleaguegames"){
	echo update_league_games();
}

if ($view == "calcgames"){
	echo calculate_uli_games($option['currentyear'], $leagueID = 1, $_REQUEST['round']);
}

echo '</div>';
echo "\n";
echo '</div>';
echo "\n";


uli_footer();

/** FUNC **/


?>
