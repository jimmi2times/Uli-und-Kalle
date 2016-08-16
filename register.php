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
uli_header();

global $option;
$action = $_REQUEST['action'];


if ($action == "send") {

	$uliname = $_POST['uliname']; $uliname = strip_tags($uliname);
	$leagueID = $_POST['leagueID'];settype($leagueID, "INT");
	echo $error = check_newuli_data($uliname);


	if (!$error){
		write_uli($uliname, $leagueID);
			?>
		<div class = "founduli ulibox">
			<h3>OK, na dann mal los</h3>
			<p>Schau Dich doch mal in den einzelnen Bereichen Deines Büros um.</p>
		</div>
		<?php
	}
	else {
		unset($action);
	}
}


if ($action != "send"){

?>
<div class = "founduli ulibox">
	<h3>Herzlich Willkommen</h3>
	<?php if ($error) { echo '<p><b>'.$error.'</b></p>'; } ?>
	<p>Du bist kurz davor, in das Haifischbecken der <b>Uli und Kalle Bundesliga</b> (TM, nur echt mit Röhrenmonitor) einzutauchen.</p>
	<p>Denke Dir nun einen m&ouml;glichst hochtrabenden Namen f&uuml;r Deinen Klub aus und schicke den Antrag ab.</p>
	<p>Dann bekommst Du 50 Millionen Euro Startkapital, ein Mini-Stadion und Zutritt zu allen Bereichen des modernen Managerlebens.</p>
	<form method = "POST" action="?register=now&action=send">
	<input type="text" name="uliname" size = "40" width="40" value="Dein Teamname"></input>

	<p>Liga auswählen:</p>
	<select name="leagueID">
	<?php
	$leagues = get_leagues();
	foreach ($leagues as $league){
	$ulis = get_ulis($league['ID']);
	echo '<option value="'.$league['ID'].'">'.$league['name'].' | '.$league['infotext'].' ('.count($ulis).' Teams)</option>';
	}


	?>
	</select>

	<input type="submit" value="Ab damit!">
	</form>
</div>
<?php
}

/* Footer */
uli_footer();



function check_newuli_data($uliname) {

$error = '';
if (!uliname OR $uliname == "Dein Teamname") {$error = 'Bitte einen Vereinsnamen eintragen.';}
if (check_uliname($uliname)){$error = 'Dieser Name ist schon in Benutzung.';}


return $error;
}


/*
 * �berpr�ft ob es den Namen f�r das Team schon gibt
 * Nur f�r die Anmeldung notwendig
 * TODO in eine andere LIB
 * 15.01.09
 */
function check_uliname($uliname) {
	$cond[0]['col']	= 'uliname'; $cond[0]['value'] = $uliname;
	$var = uli_get_var('uli', $cond, 'ID');
	if ($var){return TRUE;}
	else {return FALSE;}
}



/**
 * tr�gt einen Manager neu ein.
 */
function write_uli($uliname, $leagueID) {
	global $option, $user_ID;
	$values[] = array("col" => "uliname", "value" => $uliname);
	$values[] = array("col" => "userID", "value" => $user_ID);
	$values[] = array("col" => "leagueID", "value" => $leagueID);
	$values[] = array("col" => "startyear", "value" => mktime());

	$uliID = uli_insert_record("uli", $values);

	unset($values);

	$values[] = array("col" => "name", "value" => "Karl-Liebknecht-Stadion");
	$values[] = array("col" => "uliID", "value" => $uliID);

	$ID = uli_insert_record("stadium", $values);


	unset($values);

	$values[] = array("col" => "type", "value" => 14);
	$values[] = array("col" => "round", "value" => 0);
	$values[] = array("col" => "sum", "value" => 50000000);

	$values[] = array("col" => "uliID", "value" => $uliID);

	$ID = uli_insert_record("finances", $values);
}
?>
