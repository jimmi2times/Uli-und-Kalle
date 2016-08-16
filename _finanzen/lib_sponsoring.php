<?php
/*
 * Created on 27.7.13
 *
 * Library fuer die Sponsoring Verhandlungen
 */

/* Bindet die Sprachdatei ein */
include('lang_finanzen.php');


function print_sponsoring(){
	global $option;
	$uliID = $option['uliID'];

	$sponsoringOffers = get_sponsoring_offers($uliID);

	$signed = FALSE;
	if ($sponsoringOffers){
		foreach ($sponsoringOffers as $contract){
			if ($contract['status'] == 2){
				$signed = TRUE;
				$offer = $contract;
			}
		}
	}


	if ($signed){
		$content .=  get_sponsor_pic($offer['sponsor_id']);
		$content .=  '<br/>';
		$content .=  '<b>'.$sponsors[$offer['sponsor_id']]['name'].'</b>';
		$content .=  '<br/>';
		$content .=  '<br/>';
		$content .=  uli_money($offer['base']).' | Halbjahr';
		$content .=  '<br/>';
		$content .=  uli_money($offer['per_point']).' | Punkt';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_rank1']).' | 1. Platz Spieltag';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_rank2']).' | 2. Platz Spieltag';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_rank3']).' | 3. Platz Spieltag';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_championship']).' | Meisterpr&auml;mie';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_top5']).' | Pr&auml;mie f&uuml;r einen Platz unter den ersten 5';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_audience']).' | Pr&auml;mie f&uuml;r einen Zuschauerschnitt &uuml;ber 40.000';

		$content .=  '<br/>';
		$content .=  '<br/>';
		$html .= uli_box("Dein Sponsor f&uuml;r diese Halbserie", $content);
	}
	else {

	//print_r($sponsoringOffers);
	$count = 3;
	if ($sponsoringOffers){
		$count = 3 - count($sponsoringOffers);
	}
	//echo $count;

	if (count($sponsoringOffers) < 3){
		$sponsoringOffers = generate_sponsoring_offers($uliID, $count, $sponsoringOffers);
	}

	$sponsors = get_sponsors();

	foreach ($sponsoringOffers as $offer){
		$content .= '<div style="float:left; width: 30%; padding: 5px;">';
		$content .=  '<form action="?action=sign&year='.$offer['year'].'" method = "POST">';
		$content .=  get_sponsor_pic($offer['sponsor_id']);
		$content .=  '<br/>';
		$content .=  '<b>'.$sponsors[$offer['sponsor_id']]['name'].'</b>';
		$content .=  '<br/>';
		$content .=  '<br/>';
		$content .=  uli_money($offer['base']).' | Halbjahr';
		$content .=  '<br/>';
		$content .=  uli_money($offer['per_point']).' | Punkt';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_rank1']).' | 1. Platz Spieltag';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_rank2']).' | 2. Platz Spieltag';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_rank3']).' | 3. Platz Spieltag';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_championship']).' | Meisterpr&auml;mie';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_top5']).' | Pr&auml;mie f&uuml;r einen Platz unter den ersten 5';
		$content .=  '<br/>';
		$content .=  uli_money($offer['extra_audience']).' | Pr&auml;mie f&uuml;r einen Zuschauerschnitt &uuml;ber 40.000';

		$content .=  '<br/>';
		$content .=  '<br/>';
		$content .=  'Angebot g&uuml;ltig bis <b>'.uli_date($offer['end']).'</b>';

		$content .=  '<br/>';
		$content .=  '<form action="?action=sign&year='.$year.'" method = "POST">';
		$content .=  '<input type="hidden" name = "contract" value="'.$offer['id'].'">';
		$content .=  '<input type="submit" value="Unterschreiben" ';
		$content .=  ' >';
		$content .=  '</form>';
		$content .=  '<br/>';
		$content .=  '</div>';
		}


		// $html .=  '<img src="'.$CONFIG->wwwroot.'/theme/uli/graphics/transp.gif" width=400 height=1/>';
		$html .= uli_box("Finde den Sponsor Deines Vertrauens", $content);
	}
	return $html;

}


function generate_sponsoring_offers($uliID, $count, $offers){
	global $option;

	//echo $count;

	$TeamRanking = get_TR($uliID);
	$exclude[] = 26;
	if ($offers){
		foreach ($offers as $thisoffer){
			$exclude[] = $thisoffer['sponsor_id'];
		}
	}

		// TODO Check dass kein Sponsorname doppelt auftaucht
		$totalNumsNeeded = $count;
		$randoms = array();
		while (count($randoms) < $totalNumsNeeded) {
		    $random = rand(1, 25);
		    if (!in_array($random, $randoms) AND !in_array($random, $exclude)) {
		        $randoms[] = $random;
		    }
		}

		$sponsors = get_sponsors();
		for ($x = 1; $x<=$count; $x++){

		//echo 'huhuhu<br><br>';

		$sponsContract['sponsor_id'] = $randoms[$x-1];

		//print_r($option);
		//$stadionName['end'] = $stadionName['years'] * 365 * 24 * 60 * 60 + mktime(); // Laufzeit 1 - 2 Jahre
		$sponsContract['end'] = mktime() + (86400 * rand(3,14)); // Angebot gilt 3 bis 14 Tage
		$sponsContract['team_id'] = $option['uliID'];
		$sponsContract['status'] = 1; // Status 1 = Angebot // 2 unterschrieben
		$sponsContract['year'] = $option['currentchildyear']; // Status 1 = Angebot // 2 unterschrieben
		$sponsContract['year'] = $option['currentchildyear']; // Status 1 = Angebot // 2 unterschrieben



		// Zufallsfaktor ist sehr hoch
		$sponsContract['base'] = $TeamRanking['TR_gesamt'] * rand(1000000,15000000) / 100;
		$sponsContract['per_point'] = rand(2000,5000);
		$sponsContract['extra_rank1'] = rand(750000,2000000);
		$sponsContract['extra_rank2'] = rand(500000,1500000);
		$sponsContract['extra_rank3'] = rand(250000,1000000);
		$sponsContract['extra_audience'] = $TeamRanking['TR_gesamt'] * rand(2000000,12000000) / 100;
		$sponsContract['extra_championship'] = $TeamRanking['TR_gesamt'] * rand(7500000,20000000) / 100;
		$sponsContract['extra_top5'] = $TeamRanking['TR_gesamt'] * rand(5000000,15000000) / 100;

		settype($sponsContract['base'], INT);
		settype($sponsContract['per_point'], INT);
		settype($sponsContract['extra_rank1'], INT);
		settype($sponsContract['extra_rank2'], INT);
		settype($sponsContract['extra_rank3'], INT);
		settype($sponsContract['extra_audience'], INT);
		settype($sponsContract['extra_championship'], INT);
		settype($sponsContract['extra_top5'], INT);

		$value = array();
		$sponsContract['id'] = '';
		foreach ($sponsContract as $key => $thisSpons){
			$value[] = array("col" => $key, "value" => $thisSpons);
		}

		$sponsContract['id'] = uli_insert_record('sponsoring', $value);
		$offers[] = $sponsContract;

		$exclude[] = $sponsContract['sponsor_id'];
	}
	return $offers;
}


function get_sponsors(){
	$result = uli_get_results("sponsors");

	foreach ($result as $entry){
		$sponsors[$entry['ID']] = $entry;

	}

	return $sponsors;
}

function get_spons_contract($ID) {
	$cond[] = array("col" => "id", "value" => $ID);
	$result = uli_get_row("sponsoring", $cond);
	if ($result){
		return $result;
	}
	else {
		return FALSE;
	}
}

function get_sponsoring_offers($uliID, $archiv = '') {
	global $option;

	$cond[] = array("col" => "year", "value" => $option['currentchildyear']);
	$cond[] = array("col" => "team_id", "value" => $uliID);
	if (!$archiv){
		$cond[] = array("col" => "end", "value" => mktime(), "func" => ">");
	}
	$result = uli_get_results("sponsoring", $cond);

	//print_r($result);

	if ($result){
		return $result;
	}
	else {
		return FALSE;
	}
}


function get_sponsor_pic($ID){
global $option;

$pic[1] = "zound_zero_b";
$pic[2] = "malmoe_FF_b";
$pic[3] = "minh_fashion_b";
$pic[4] = "sanssouci_doener_b";
$pic[5] = "kik_b";
$pic[6] = "t-online_b";
$pic[7] = "deutsche_bahn_b";
$pic[8] = "rag_b";
$pic[9] = "gazprom_b";
$pic[10] = "kyocera_b";
$pic[11] = "fly_emirates_b";
$pic[12] = "bwin_b";
$pic[13] = "avenzia_b";
$pic[14] = "dws_b";
$pic[15] = "envia_b";
$pic[16] = "mister_u_lady_jeans_b";
$pic[17] = "debitel_b";
$pic[18] = "dbv_winterthur_b";
$pic[19] = "vw_b";
$pic[20] = "krombacher_b";
$pic[21] = "fraport_b";
$pic[22] = "tui_b";
$pic[23] = "citibank_b";
$pic[24] = "kaufland_b";
$pic[25] = "bar_gelb_b";



$html .= '<img src="'.$option['uliroot'].'/theme/graphics/sponsors/'.$pic[$ID].'.png" border="1">';
return $html;
}

?>
