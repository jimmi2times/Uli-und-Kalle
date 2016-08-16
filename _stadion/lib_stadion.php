<?php
/*
 *
 * Das neue tolle Stadion
 *
 * 2012
 *
 *
 *
 */

/*
 * holt einen oder mehrere eintraege aus der stadionname tabelle
 */
function get_stadion_name($uliID, $archiv = '') {
	$cond[] = array("col" => "uliID", "value" => $uliID);
	if (!$archiv){
		$cond[] = array("col" => "end", "value" => mktime(), "func" => ">");
	}
	$result = uli_get_results("stadium_name", $cond);
	if ($result){
		return $result;
	}
	else {
		return FALSE;
	}
}





/**
 * generiert ein angebot fuer einen stadionnamen
 */


function generate_stadion_name_offer($uliID){
	global $option;
	//$sponsor = array("Butzitown", "Allianz-Arena", "Signal-Iduna-Park", "HSH Nordbank Arena", "O2 World", "Wellblechpalast", "ChickenD&ouml;nerPalace");

	$textbaustein[] = "ist der beste Klub der Welt";
	$textbaustein[] = "&uuml;ber alles";
	$textbaustein[] = "forever number one";


	$ulis = get_ulis($option['leagueID']);
	if ($ulis){
		foreach ($ulis as $uli){
			//echo $uli['uliname'];
			if ($uli['ID'] != $uliID){
				$sponsor[] = $uli['uliname'].' '.$textbaustein[rand(0,2)];
			}
		}
	}
	$sponsor[] = "Allianz-Arena";
	$sponsor[] = "Signal-Iduna-Park";
	$sponsor[] = "HSH-Nordbank-Arena";
	$sponsor[] = "Trolli-Arena";
	$sponsor[] = "Veltins-Arena";
	$sponsor[] = "Dem gro&szlig;en MV ihm sein Stadion";
	$sponsor[] = "ChickenD&ouml;nerPalace";
	$sponsor[] = "O2 World";
	$sponsor[] = "Wellblechpalast";



	$TeamRanking = get_TR($uliID);
	$stadionName['sponsor'] = $sponsor[rand(0,(count($sponsor)-1))]; // Name des Namenssponsors
	//$stadionName['start'] = mktime();
	$stadionName['years'] = rand(1,2);
	//$stadionName['end'] = $stadionName['years'] * 365 * 24 * 60 * 60 + mktime(); // Laufzeit 1 - 2 Jahre
	$stadionName['end'] = mktime() + (86400 * rand(14,40)); // Angebot gilt 14 bis 40 Tage
	$stadionName['uliID'] = $option['uliID'];
	$stadionName['status'] = 1; // Status 1 = Angebot // 2 unterschrieben


	// Summe fuerr den Verkauf der Namensrechte
	// Maximalwert (100% TR) 8 Mio pro Jahr
	// Zufallsfaktor ist sehr hoch
	$stadionName['sum'] = $TeamRanking['TR_gesamt'] * rand(3000000,20000000) / 100 * $stadionName['years'];
	settype($stadionName['sum'], INT);

	//print_r($stadionName);

	foreach ($stadionName as $key => $thisStadionName){
		$value[] = array("col" => $key, "value" => $thisStadionName);
	}
	uli_insert_record('stadium_name', $value);

	$name[] = $stadionName;

	return $name;
}





/**
 * holt das ganze Stadion
 *
 *
 *
 */
function get_stadium($uliID){
	global $option;




	$cond[] = array("col" => "uliID", "value" => $uliID);
	$stadium = uli_get_results('stadium', $cond);

	unset($cond);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$seats = uli_get_results('stadium_seats', $cond);
	// wenn es noch keine Pl�tze gibt, dann wird die erste Reihe angelegt
	if (!$seats){
		$seat['uliID'] = $uliID;
		$seat['block'] = 'A1';
		$seat['seats'] = 5000;
		$seat['type_of_seats'] = '1';
		$seat['built'] = mktime();
		foreach($seat as $key => $value){
			$values[] = array("col" => $key, "value" => $value);
		}
		$ID = uli_insert_record('stadium_seats', $values);
		//uli_insert_record('stadium_seats', $values);
		$seats[] = $seat;
	}

	$stadium['seats'] = $seats;

	foreach ($seats as $seat){
		if ($seat['type_of_seats'] == 1){
			$sitzplaetze = $sitzplaetze + $seat['seats'];
		}
		if ($seat['type_of_seats'] == 2){
			$stehplaetze = $stehplaetze + $seat['seats'];
		}

	}
	$stadium['sitzplaetze'] = $sitzplaetze;
	$stadium['stehplaetze'] = $stehplaetze;


	unset($cond);
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$infra = uli_get_results('stadium_infra', $cond);



	if ($infra){
		$stadium['infra'] = $infra;
	}

	if ($stadium){
		return $stadium;
	}
	else {
		return FALSE;
	}
}




function get_infra_prices() {

	$prices['bratwurst'] 		= 500000;
	$prices['bier'] 			= 500000;
	$prices['merchandising'] 	= 1000000;
	$prices['fanprojekt'] 		= 2000000;
	$prices['museum'] 			= 2000000;
	$prices['kinderland'] 		= 1000000;
	$prices['videowuerfel'] 	= 5000000;
	$prices['soundsystem'] 		= 2500000;
	$prices['sbahn'] 			= 10000000;
	$prices['bushaltestelle'] 	= 1000000;
	$prices['autobahn'] 		= 25000000;
	$prices['architektur'] 		= 10000000;

	return $prices;
}


?>
