<!--<?php 
/*
 * Created on 08.06.2009
 * Sponsoring
 * 
 * TODO Erst einmal blocken und ankuendigen, dass das demnaechst kommt.
 * 
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "finanzen", "sub" => "sponsoring");
uli_header(array('lib_sponsoring'));



/* Extra für den Sponsoring Bereich */
//require_once('class.dbmanager.php');
//require_once('class.sponsoringmanager.php');

/* Aktionen und Ansichten */
$action = $_REQUEST['action'];$action = strip_tags($action);
/* ACHTUNG: hier wird mit Halbjahren gearbeitet */
$year = $_REQUEST['year'];settype($year, INT);
if (!$year) {$year = $option['currentchildyear'];}




if ($action == "sign") {
	global $option;
	$contractID = $_POST['contract'];
	settype($contractID, INT);
	// check
	$contract = get_spons_contract($contractID);
	$error = FALSE;
	if ($contract['team_id'] != $option['uliID'] OR $contract['status'] != 1 OR $contract['year'] != $year){
		$error = TRUE;
	}
	if (!$error){
		// unterschreiben und ueberweisen
		$cond[] = array("col" => "id", "value" => $contractID);
		$value[]= array("col" => "status", "value" => 2);
		$value[]= array("col" => "end", "value" => 999999999999);
		
		uli_update_record('sponsoring', $cond, $value);
		calculate_money(19, $contract['base'], $contract['team_id'], 0, $option['currentyear'], 'add', $type='income');
		// News
		create_gossip(4, NULL, $option['leagueID'], $contract['team_id'], NULL, $published = 1);
		
	}
	
}




/* runtime of a contract */
define("CONTRACT_RUNTIME", 180 * 24 * 3600);


/* Ausgabe des Containers für Messages */
echo '<div id="container">';
echo '</div>';

/* Ausgabe der Seite */
echo '<div class="LeftColumn">';
	echo "\n";
	
	$infoSpons = "Du bekommst immer drei Angebote von Sponsoren. Du kannst dieses Angebot annehmen oder warten bis nach Ablauf der benannten Frist eine neue Firma um die Ecke kommt und ihr Wappen auf Deiner Brust sehen m&ouml;chte.";
	
	echo uli_box(Info, $infoSpons); 
	echo "\n";

	echo "\n";
	//echo print_child_year_menue($year, NULL, $startyear = 6);
	echo "\n";
	
echo '</div>';
echo "\n\n";

echo '<div class="RightColumnLarge">';
	echo "\n";
	//echo uli_box(Info, InfoTextSponsoring); 
	echo "\n";
	echo print_sponsoring();
	
	echo "\n";
	//echo print_child_year_menue($year, NULL, $startyear = 6);
	echo "\n";
	
echo '</div>';
echo "\n\n";

//echo '<div class="RightColumnLarge">';
//echo "\n";
//
//	echo '<div id="tv">';
//	echo "\n";
//	 	// echo print_tv_negotiation($year, $action, $contract);
//	echo '</div>';
//	echo "\n";
//echo '</div>';
//echo "\n";


/* Das muss für den Server dann in echt gemacht werden */
//$db['dbhost'] 	= DB_HOST;
//$db['dbuser'] 	= DB_USER;
//$db['dbpass'] 	= DB_PASSWORD;
//$db['dbname'] 	= DB_NAME;
//$db['prefix'] 	= 'tip_';
//
//
//try {
//	$uliID = $option['uliID'];
//	$dbManager = new DbManager($db['dbhost'], $db['dbuser'], $db['dbpass'], $db['dbname'], $db['prefix']);
//	$manager = new SponsoringManager($dbManager);
//	$thisTeam = $dbManager->getTeam($uliID);
//	$currentSeasonStart = time();
//	$currentSeasonEnd =  time() + 180 * 24 * 3600;
//
//	switch($action) {
//		case "initseason"	:	
//						if($user_ID == 1){$manager->initSeason($currentSeasonStart, $currentSeasonEnd, $year, $option['leagueID']);}
//						break;
//	
//		case "deleteseason"	:	
//						if($user_ID == 1){$manager->deleteSeason($currentSeasonStart, $currentSeasonEnd, $year);}
//						break;
//	
//		case "negotiate"	:	$sponsorId = $_GET['sponsor'];
//						if(!settype($sponsorId, "int")) {
//							throw new Exception("Given sponsor id is not an integer.");
//							break;
//						}
//						$sponsor = $dbManager->getSponsor($sponsorId);
//						$contract = $dbManager->getSponsorContract($sponsor, $thisTeam, $currentSeasonStart, $currentSeasonEnd, -1,$year);
//						if($contract) {
//							$manager->negotiateSponsorContract($contract);
//						}
//						break;
//		case "accept"	:		$sponsorId = $_GET['sponsor'];
//						if(!settype($sponsorId, "int")) {
//							throw new Exception("Given sponsor id is not an integer.");
//							break;
//						}
//						$sponsor = $dbManager->getSponsor($sponsorId);
//						$contract = $dbManager->getSponsorContract($sponsor, $thisTeam, $currentSeasonStart, $currentSeasonEnd,  -1,$year);
//						if($contract) {
//							$manager->acceptSponsorContract($contract);
//						}
//						break;
//
//		case "sign"	:		$sponsorId = $_GET['sponsor'];
//						if(!settype($sponsorId, "int")) {
//							throw new Exception("Given sponsor id is not an integer.");
//							break;
//						}
//						$sponsor = $dbManager->getSponsor($sponsorId);
//						$contract = $dbManager->getSponsorContract($sponsor, $thisTeam, $currentSeasonStart, $currentSeasonEnd, -1, $year);
//						if($contract) {
//							$manager->signSponsorContract($contract, $year);
//						}
//						$sum = $contract->getBaseAmount();
//						$uliID = $contract->getTeam()->getId();
//						$update = "finance";
//						calculate_money(19, $sum, $uliID, 0, $CONFIG->currentyear, $action='add', $type='income');
//						break;
//
//		default			:	break;
//	}
//	
//} catch(Exception $e) {
//	print($e->getMessage());
//}
//
//print("<div class=\"RightColumnLarge\">\n");
//print("<div id=\"Sponsoring\">\n");
//
//search for an existing valid contract */
//$validContracts  = $dbManager->getSponsorContracts(NULL, $thisTeam, time(), time() + CONTRACT_RUNTIME, 3, $year);
//if(sizeof($validContracts) > 0) {
//	print("<table class=\"ulitable\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n");
//	print("<tr>\n");
//	print("<th class=\"headline\">Dein Sponsorvertrag</td>\n");
//	print("</tr>\n");
//	print("<tr>\n");
//	print("<td class=\"content\">\n");
//	
//	/* create image path */
//	$sponsorName = $validContracts[0]->getSponsor()->getName();
//	$sponsorImageName = $sponsorName;
//	$sponsorImageName = str_replace(" ", "_", $sponsorImageName);
//	$sponsorImageName = str_replace("&uuml;", "ue", $sponsorImageName);
//	$sponsorImageName = str_replace("&ouml;", "oe", $sponsorImageName);
//	$sponsorImageName = str_replace("&auml;", "ae", $sponsorImageName);
//	$sponsorImageName = str_replace("&szlig;", "ss", $sponsorImageName);
//	$sponsorImageName = str_replace("&", "u", $sponsorImageName);
//
//	$logoPath = "/theme/graphics/sponsors/".strtolower($sponsorImageName)."_b.png";
//	if(file_exists($option['ulidirroot'].$logoPath)) {
//		print("<img src=\"".$option['uliroot'].$logoPath."\" alt=\"".$sponsorName."\" />\n");
//	} else {
//		print("<p><strong>".$sponsorName."</strong></p>\n");
//	}
//	print("<div class=\"clear\"></div>\n");
//	print("Dein bester Freund: <strong>".$sponsorName."</strong><br/>\n");
//	print("Vertragsstart: ".date('d. m. Y', $validContracts[0]->getStart())."<br/>\n");
//	print("Vertragsende: ".date('d. m. Y', $validContracts[0]->getEnd())."<br/>\n");
//	print("Grundbetrag: ".number_format($validContracts[0]->getBaseAmount(),0, ",", ".")." Euro<br/>");
//	print("Pro Punkt: ".number_format($validContracts[0]->getPerPoint(),0, ",", ".")." Euro<br />");
//	print("Pr&auml;mie f&uuml;r Platz 1: ".number_format($validContracts[0]->getExtraRank1(),0, ",", ".")." Euro<br/>");
//	print("Pr&auml;mie f&uuml;r Platz 2: ".number_format($validContracts[0]->getExtraRank2(),0, ",", ".")." Euro<br/>");
//	print("Pr&auml;mie f&uuml;r Platz 3: ".number_format($validContracts[0]->getExtraRank3(),0, ",", ".")." Euro<br/>");
//	print("Besucherbonus: ".number_format($validContracts[0]->getExtraAudience(),0, ",", ".")." Euro<br/>");
//	print("Meisterbonus: ".number_format($validContracts[0]->getExtraChampionship(),0, ",", ".")." Euro<br/>");
//	print("Top5-Bonus: ".number_format($validContracts[0]->getExtraTop5(),0, ",", ".")." Euro");
//	print("</td>\n");
//	print("</tr>\n");
//	print("</table>\n");
//}else {
//	/* get all contracts for this team */
//	$currentContracts = $dbManager->getSponsorContracts(NULL, $thisTeam, time(), time() + CONTRACT_RUNTIME, -1, $year);
//	if (!$currentContracts){
//		$manager->initSeason_team($currentSeasonStart, $currentSeasonEnd, $year, $uliID, $option['leagueID']);
//		}
//	$currentContracts = $dbManager->getSponsorContracts(NULL, $thisTeam, time(), time() + CONTRACT_RUNTIME, -1, $year);
//
//	print("<table class=\"ulitable\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n");
//	print("<tr>\n");
//	print("<th class=\"headline\">Sponsor</td>\n");
//	// print("<td class=\"headeruli\">Image</td>\n");
//	print("<th class=\"headline\">Angebot</td>\n");
//	print("<th class=\"headline\">Men&uuml;</td>\n");
//	print("</tr>\n");
//
//
//	foreach($currentContracts as $contract) {
//		
//		
//		//echo $contract->getKarma();
//		/* create image path for sponsor logo */
//		$sponsorName = $contract->getSponsor()->getName();
//		$sponsorImageName = $sponsorName;
//		$sponsorImageName = str_replace(" ", "_", $sponsorImageName);
//		$sponsorImageName = str_replace("&uuml;", "ue", $sponsorImageName);
//		$sponsorImageName = str_replace("&ouml;", "oe", $sponsorImageName);
//		$sponsorImageName = str_replace("&auml;", "ae", $sponsorImageName);
//		$sponsorImageName = str_replace("&szlig;", "ss", $sponsorImageName);
//		$sponsorImageName = str_replace("&", "u", $sponsorImageName);
//			$karma['value'] = $contract->getKarma();
//			$karma = get_karma_pic($karma['value'], $uliname);
//			
//		print("<tr>\n");
//		print("<td class=\"content\">\n");
//
//		$logoPath = "/theme/graphics/sponsors/".strtolower($sponsorImageName)."_S.png";
//		if(file_exists($option['ulidirroot'].$logoPath)) {
//			print("<img src=\"".$option['uliroot'].$logoPath."\" alt=\"".$sponsorName."\" />\n");
//			} else {
//				print("<p><strong>".$sponsorName."</strong></p>\n");
//			}
//
//		print("</td>\n");
//		// print("<td class=\"contentuli\">".$contract->getSponsor()->getImage()."</td>\n");
//
//		if($contract->getStatus() == 0) {
//			print("<td class=\"content\" colspan=\"2\">\n");
//			print("Jungschen, da stellen wir uns ganz andere Kaliber als Partner vor. Entweder Du warst zu schlecht oder zu langsam. ");
//			print("</td>\n");
//		}
//
//		if($contract->getStatus() == 1 OR $contract->getStatus() == 2) {
//			print("<td class=\"content\" width=\"40%\">\n");
//			print("<strong>Grundbetrag:</strong> ".number_format($contract->getBaseAmount(),0, ",", ".")." Euro <br/>");
//			print("<strong>Pro Punkt:</strong> ".number_format($contract->getPerPoint(),0, ",", ".")." Euro <br />");
//			print("<strong>Pr&auml;mie f&uuml;r Platz 1:</strong> ".number_format($contract->getExtraRank1(),0, ",", ".")." Euro <br/>");
//			print("<strong>Pr&auml;mie f&uuml;r Platz 2:</strong> ".number_format($contract->getExtraRank2(),0, ",", ".")." Euro <br/>");
//			print("<strong>Pr&auml;mie f&uuml;r Platz 3:</strong> ".number_format($contract->getExtraRank3(),0, ",", ".")." Euro <br/>");
//			print("<strong>Besucherbonus:</strong> ".number_format($contract->getExtraAudience(),0, ",", ".")." Euro <br/>");
//			print("<strong>Meisterbonus:</strong> ".number_format($contract->getExtraChampionship(),0, ",", ".")." Euro <br/>");
//			print("<strong>Top5-Bonus:</strong> ".number_format($contract->getExtraTop5(),0, ",", ".")." Euro ");
//			print("</td>\n");
//		}
//
//		if(time() >= $contract->getStart() ) {
//			if($contract->getStatus() == 1) {
//				print("<td class=\"content\">\n");
//				print($karma['pic']." <strong>".$karma['text']."</strong><br/><br/>");
//				print("<a href=\"".$PHP_SELF."?action=negotiate&sponsor=".$contract->getSponsor()->getId()."\">Hart wie die GDL verhandeln</a><br/><br/>\n");
//				print("<a href=\"".$PHP_SELF."?action=accept&sponsor=".$contract->getSponsor()->getId()."\">Einfach mal einen Kaffee trinken</a><br/>\n");
//				print("</td>\n");
//				} 
//			elseif($contract->getStatus() == 2) {
//				print("<td class=\"content\">\n");
//				print("<a href=\"".$PHP_SELF."?action=sign&sponsor=".$contract->getSponsor()->getId()."\">unterschreiben</a><br/>\n");
//				print("</td>\n");
//				}
//			} 
//		elseif($contract->getStatus() != 0) {
//			print("<td class=\"content\">\n");
//			print($karma['pic']." <strong>".$karma['text']."</strong><br/><br/>");
//			print("Komm doch mal am <strong>".date('d.m.Y \s\o\ \a\b G', $contract->getStart())." Uhr</strong> wieder vorbei.");
//			print("</td>\n");
//			}
//		print("</tr>\n");
//	}
//}
//?>
<?php 
//
//
//
//	/* Wenn Admin, dann die wichtig Funktionen */
//	if ($user_ID == 1){
//	 print("<p>\n<a href=\"".$PHP_SELF."?action=initseason\">Vertr&auml;ge f&uuml;r aktuelle Saison initialisieren</a>\n</p>\n");
//	 print("<p>\n<a href=\"".$PHP_SELF."?action=deleteseason\">Vertr&auml;ge f&uuml;r aktuelle Saison l&ouml;schen</a>\n</p>\n");
//	}
//
//
//print("</div>"); /* ID="SPONSORING" */
//print("</div>"); /* rightcolumn */


/* Footer */
uli_footer();
 


function get_karma_pic($karmavalue, $uliname) {
global $option;
if ($karmavalue < 3){$karma['pic'] 		= '<img src="'.$option['uliroot'].'/theme/graphics/icons/smile_s.gif" height="15" width="15">';}
elseif ($karmavalue < 6){$karma['pic'] 	= '<img src="'.$option['uliroot'].'/theme/graphics/icons/smile_m.gif" height="15" width="15">';}
elseif ($karmavalue <= 9){$karma['pic'] = '<img src="'.$option['uliroot'].'/theme/graphics/icons/smile_h.gif" height="15" width="15">';}
elseif ($karmavalue > 9){$karma['pic'] 	= '<img src="'.$option['uliroot'].'/theme/graphics/icons/smile_vh.gif" height="15" width="15">';}
if ($karmavalue < 1){$karma['text'] = 'Wer bist Du denn, Dich kenne ich nicht.';}
elseif ($karmavalue < 2){$karma['text'] = 'Also wir w&uuml;rden ja lieber die Bayern sponsorn.';}
elseif ($karmavalue < 3){$karma['text'] = 'Was habt Ihr denn so zu bieten?';}
elseif ($karmavalue < 4){$karma['text'] = 'Na mal sehen, reden kann man ja &uuml;ber alles.';}
elseif ($karmavalue < 5){$karma['text'] = 'Ich wei&szlig; ja nicht, ob wir im Fu&szlig;ballumfeld werben wollen.';}
elseif ($karmavalue < 6){$karma['text'] = 'Na Hauptsache, bei Euch gibt es kein Dopping.';}
elseif ($karmavalue < 7){$karma['text'] = 'Wir werden das wohlwollend pr&uuml;fen.';}
elseif ($karmavalue < 8){$karma['text'] = 'Das k&ouml;nnte eine erfolgreiche Partnerschaft werden.';}
elseif ($karmavalue <= 9){$karma['text'] = 'Ich habe als Kind schon in '.$uliname.'-Bettw&auml;sche geschlafen.';}
elseif ($karmavalue > 9){$karma['text'] = 'Nur noch letzte Details w&auml;ren zu kl&auml;ren.';}
return $karma;
}


?>-->
