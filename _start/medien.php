<?php
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');



/**
 * Wie funktioniert die Zeitung
 *
 * Artikel wird generiert
 * Redakteur schaltet frei und modifiziert (zumindest am Anfang)
 *
 *
 * Wann wird ein Artikel generiert
 * 1. Gebot auf einen Spieler, Gehalt Ÿber 100.000 und/oder Ablšse Ÿber 10 Mio
 * 2. Gescheiterte VertragsverlŠngerung/Erfolgreiche VerlŠngerung
 * 3. Top-Leistung
 * 4. Elf des Tages
 * 5. Transfer Ÿber 10 Mio
 *
 *
 * Es wird bei der Aktion selber ein GerŸcht erzeugt
 * Oder der Redakteur triggert das extra an.
 * Die vorbereiteten GerŸchte werden als Draft gespeichert
 * Mit Klick kšnnen Sie veršffentlicht werden
 *
 * FRAGE
 * Sollen die Textbausteine in der DB gespeichert werden? NEIN
 * Platzhaltersyntax = [[ULINAME1]] [[PLAYERNAME]] [[ULIMANAGERNAME1]]
 *
 *
 *  Wie ist ein textbaustein gekennzeichnet
 *
 *  Subject (1-x, Spielerbeschreibung, Vertrag verlaengert), Zitat (TRUE, FALSE), Tonalitaet (1-3)
 *
 *
 *
 *
 *
 */


// Spieler
global $option;
/* Header */
$page = array("main" => "start", "sub" => "Media", "name" => "Media");
uli_header(array('../_transfermarkt/lib_transfermarkt'));


?>
<script>
$(document).ready(function(){
	$("input.publishdraft").click(
		    function() {
				var draftid = this.id;
				var headline = $("#headline-"+draftid).attr("value");
				var text = $("#text-"+draftid).attr("value");
				$.ajax({
					type: "POST", url: "ajax_media.php", data: "action=publish&draftid=" + draftid + "&text="+ text + "&headline=" + headline ,
					complete: function(data){
					$("#draft-"+draftid).html(data.responseText);
					}
				 });
				return false;
		});
	$("input.deletedraft").click(
		    function() {
				var draftid = this.id;
				$.ajax({
					type: "POST", url: "ajax_media.php", data: "action=delete&draftid=" + draftid,
					complete: function(data){
					$("#draft-"+draftid).html(data.responseText);
					}
				 });
				return false;
		});	
		
});
</script>


<?php




echo '<div id="container" title="'.NewMessage.'">';
echo '</div>';

echo '<div id="hiddencontainer" title="'.NewMessage.'">';
echo '</div>';


echo '<div class="LeftColumn">';
echo "\n";
echo uli_box(HeadlineCommunicator, Zeitungsgenerator);
echo "\n";
echo '</div>';
echo "\n\n";


echo '<div class="RightColumnLarge">';
echo "\n";
echo '<div id="communicate">';
echo "\n";




// Hier jetzt die redaktionelle Auswahl hin.
echo print_drafts();



if ($_REQUEST['creategossip'] == "2"){

	//holt testweise alle gescheiterten verhandlungen
	$sql = 	'SELECT * '.
		' FROM tip_uli_player_contracts_negotiations '.
		' WHERE  status  = 0 AND history = 0';
	$result = $wpdb->get_results($sql, ARRAY_A);

	foreach ($result as $player){
		create_gossip(2, $player['playerID'], $player['leagueID'], $player['uliID']);
	}
}

if ($_REQUEST['creategossip'] == "3"){
	//holt testweise alle gescheiterten verhandlungen
	$sql = 	'SELECT * '.
		' FROM tip_uli_player_contracts_negotiations '.
		' WHERE  status  = 20 AND history = 0';
	$result = $wpdb->get_results($sql, ARRAY_A);

	foreach ($result as $player){
		create_gossip(3, $player['playerID'], $player['leagueID'], '', $player['uliID']);
	}
}

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


function print_drafts(){
	$cond[] = array("col" => "status", "value" => 0);
	$drafts = uli_get_results('journal_articles', $cond);
	if ($drafts){
		foreach ($drafts as $draft){
			// Jetzt das Formular
			$html .= '<div id = "draft-'.$draft['ID'].'">';
			$html .= '<form id="draftform-'.$draft['ID'].'" method = "POST" action="#">';
			$html .= '<input size = 100 type="text" id="headline-'.$draft['ID'].'" value="'.$draft['headline'].'">';
			$html .= '<br/>';

			$html .= '<textarea cols= 50 rows = 10 id="text-'.$draft['ID'].'">'.$draft['text'].'</textarea>';
			$html .= '<br/>';

			$html .= '<input type="submit" class="publishdraft" id="'.$draft['ID'].'" value="'.Publish.'">';
			$html .= '<input type="submit" class="deletedraft" id="'.$draft['ID'].'" value="'.Delete.'">';
			$html .= '</form>';
			$html .= '<br/>';
			$html .= '<br/>';
			$html .= '</div>';

		}
	}
	return $html;
}






?>