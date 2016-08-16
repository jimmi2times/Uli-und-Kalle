<?php


require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');
include('lib_stadion.php');

global $option;

// allgemein
$uliID = $option['uliID'];
if ($_POST['action'] == "acceptoffer"){
	$contractId = $_POST['contractId'];
	$stadiumName = get_stadion_name($uliID);
	$contract = $stadiumName[0];
	// check
	if ($contract['ID'] == $contractId){
		calculate_money(26, $contract['sum'], $uliID, 0, $option['currentyear'], 'new', 'income');	
		$value[] = array("col" => "name", "value" => $contract['sponsor']); 
		$cond[] = array("col" => "uliID", "value" => $uliID); 
		uli_update_record("stadium", $cond, $value);	
		unset($value);
		unset($cond);
		$value[] = array("col" => "status", "value" => 2); 
		$value[] = array("col" => "signDate", "value" => mktime()); 
		$value[] = array("col" => "end", "value" => (mktime() + ($contract['years'] * 365 * 24 * 3600))); 
		
		
		$cond[] = array("col" => "ID", "value" => $contractId); 
		uli_update_record("stadium_name", $cond, $value);	
	
		create_gossip(5, NULL, $option['leagueID'], $uliID, NULL, $published = 1);		
		
		$html = '<form action = "?" METHOD = "POST" id ="stadiumform">';
		$html .= '<input type = text size = 80 readonly id = "stadionname" value = "'.$contract['sponsor'].'">';
		$html .= '</form>';

		// Felder aktualisieren
		echo '<script>';
		echo "$('.stadiumName .content').html('".$html."');";
		echo "$('.stadiumContract .contractMessage').html('<b>Das Geld ist &uuml;berwiesen, dein Stadion umbenannt.</b>');";
		echo '</script>';
		
	}
}



if ($_POST['action'] == "changename"){
	$stadiumname = $_POST['name'];
	
	$value[] = array("col" => "name", "value" => $stadiumname); 
	$cond[] = array("col" => "uliID", "value" => $uliID); 
	uli_update_record("stadium", $cond, $value);
	// echo 'OK.';
}


if ($_POST['action'] == "seats"){
	$block = $_POST['block'];
	$stadium = get_stadium($uliID);

	// checken, ob der gewuenschte Block schon Plaetze hat
	$info = FALSE;
	if ($stadium){
		if ($stadium['seats']){
			foreach ($stadium['seats'] as $seat){
				if ($block == $seat['block']){
					$info = TRUE;
					$blockinfo = $seat;
				}
			}
		}
	}

	if ($info){
		if ($blockinfo['type_of_seats'] == 1){
			$typeofseats = 'Sitzpl&auml;tze';
		}
		if ($blockinfo['type_of_seats'] == 2){
			$typeofseats = 'Stehpl&auml;tze';
		}
		$text = 'Dieser Block hat '.number_format($blockinfo['seats'], 0, '','.').' '.$typeofseats.' und wurde am '.uli_date($blockinfo['built']).' von flei&szlig;igen Arbeitern errichtet.';
		echo uli_box('Block '.$blockinfo['block'], $text);
	}
	else {
		$text = 'Diesen Block ausbauen.';
		$text .= '<br><button id="sitz-'.$block.'">Sitzpl&auml;tze</button> (5.000 = 8 Mio Euro)';
		$text .= '<br><button id="steh-'.$block.'">Stehpl&auml;tze</button> (8.000 = 6 Mio Euro)';
		$text .= '<br><i>(Klicken l&ouml;st den Auftrag aus)</i>';
		
		
		echo uli_box('Mein Stadion soll sch&ouml;ner werden', $text);
	}
}

if ($_POST['action'] == "build"){

	$what = $_POST['what'];

	$build = explode("-", $what);
	$block = $build[1];
	$type_of_seats = $build[0];
	if ($type_of_seats == "sitz"){
		$seats = 5000;
		$money = 8000000;
		$type = 1;
	}
	if ($type_of_seats == "steh"){
		$seats = 8000;
		$money = 6000000;
		$type = 2;
	}


	$seat['uliID'] = $uliID;
	$seat['block'] = $block;
	$seat['seats'] = $seats;
	$seat['type_of_seats'] = $type;
	$seat['built'] = mktime();
	foreach($seat as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	$ID = uli_insert_record('stadium_seats', $values);

	// Geld abziehen
	if ($ID){
		calculate_money(12, $money, $uliID, 0, $option['currentyear'], 'add', 'outgoings');
	}

	echo uli_box('Alles klar.', 'Bestellt. Bezahlt. Fertig.');

	if (in_array($seat['block'], array('A1', 'A2', 'A3', 'A4'))){
		$border = 'border-bottom-color';
	}
	if (in_array($seat['block'], array('B1', 'B2', 'B3', 'B4'))){
		$border = 'border-top-color';
	}
	if (in_array($seat['block'], array('C1', 'C2', 'C3', 'C4'))){
		$border = 'border-right-color';
	}
	if (in_array($seat['block'], array('D1', 'D2', 'D3', 'D4'))){
		$border = 'border-left-color';
	}

	if ($seat['type_of_seats'] == 1){
		$color = '#6571ba';
		$sitzplaetze = $sitzeplatze + $seat['seats'];
	}
	if ($seat['type_of_seats'] == 2){
		$color = '#0c1969';
		$stehplaetze = $stehplaetze + $seat['seats'];
			
	}

	// Block anmalen
	echo '<script>';
	echo '
		$(".stadium .tribune-'.$seat['block'].'").css("'.$border.'", "'.$color.'");
		$(".stadium .tribune-'.$seat['block'].'").css("opacity", "1");
		
		';
	echo '</script>';

}


if ($_POST['action'] == "buildinfra"){ 
	$what = $_POST['what'];

	$stadium = get_stadium($uliID);
	$prices = get_infra_prices();
	
	$infra['uliID'] = $uliID;
	$infra['type'] = $what;
	$infra['sum'] = $prices[$what];
	$infra['built'] = mktime();
	foreach($infra as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	
	//print_r($values);
	$ID = uli_insert_record('stadium_infra', $values);	
	// Geld abziehen
	if ($ID){
		calculate_money(12, $infra['sum'], $uliID, 0, $option['currentyear'], 'add', 'outgoings');
	}	
	
	if ($what == "bier" OR $what == "bratwurst" OR $what == "merchandising"){
		$bier = 0;
		$bratwurst = 0;
		$merchandising = 0;
		if ($stadium['infra']){
		foreach ($stadium['infra'] as $infra){
			if ($infra['type'] == "bier"){$bier = $bier + 1;}
			if ($infra['type'] == "bratwurst"){$bratwurst = merchandising + 1;}
			if ($infra['type'] == "merchandising"){$merchandising = $merchandising + 1;}
			}
		}		

		if ($what == "bratwurst"){
			?>
			<span>Du hast <?php echo $bratwurst+1; ?> Bratwurstbude<?php if ($bratwurst != 0){?>n<?php }?>. </span>
			<button class = "infrabutton" id = "bratwurst">Neuen Stand kaufen (500.000)</button> 	
			<?php 
		}
		if ($what == "bier"){
			?>
			<span>Du hast <?php echo $bier+1; ?> Bierbude<?php if ($bier != 0){?>n<?php }?>. </span>
			<button class = "infrabutton" id = "bier">Neuen Stand kaufen (500.000)</button> 	
			<?php 
		}
		if ($what == "merchandising"){
			?>
			<span>Du hast <?php echo $merchandising+1; ?> Merchandising-Shop<?php if ($merchandising != 0){?>s<?php }?>.</span> 
			<button class = "infrabutton" id = "merchandising">Neuen Shop kaufen (1 Mio)</button> 	
			<?php 
		}
		
		
	}
	else {
		echo '<span>Bestellt. Bezahlt. Gebaut.</span>';
		echo '<script>';
		echo '
			$(".infrastruktur .'.$what.'").removeClass("'.$what.'").addClass("'.$what.'_aktiv");
			';
		echo '</script>';	
		//echo $what;
	}
}



?>