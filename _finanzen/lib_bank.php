<?php
/*
 * Created on 24.03.2009
 *
 *Alle Funktionen, die nur in der Bank Abteilung gebraucht werden
 */

/* Bindet die Sprachdatei ein */
include('lang_finanzen.php');

function get_overview_data($year){
	global $option;
	$values = get_year_values_bank($option['uliID'], $year);
	/* Gibt eine Tabelle aus */
	if ($values){
		foreach ($values as $value){
			/* ausgeblendet werden Saldo (7) und Einnahmen/Ausgaben Gesamt (8/9) */
			if (in_array($value['type'], array(7,8,9))) {
				if ($value['type'] == 7){$saldo = $value['sum'];}
				if ($value['type'] == 8){$einnahmen = $value['sum'];}
				if ($value['type'] == 9){$ausgaben = $value['sum'];}
			}
			else {
				/* Check ob Wert positiv oder negativ */
				if ($option['finance'.$value['type'].'-2'] == "plus")
				{$tableData[] = array(uli_money($value['sum']), '', $option['finance'.$value['type']]);}
				if ($option['finance'.$value['type'].'-2'] == "minus")
				{$tableData[] = array('', uli_money($value['sum']), $option['finance'.$value['type']]);}
			}
		}
	}
	$tableData[] = array('<b>'.uli_money($einnahmen).'</b>', '<b>'.uli_money($ausgaben).'</b>', '<b>'.Gesamt.'</b>');
	$tableHeader = array(Income, Outgoings, Type);

	$data .= uli_table($tableHeader, $tableData, 'bank');

	return $data;
}


/**
 * gibt ALLE Sichten der Bank aus
 *
 */
function print_bank($view = '', $year = '', $type = ''){
	global $option;

	if (!$view){$view = "overview";}
	if (!$year){$year = $option['currentyear'];}

	// Jahresbilanz (Tabelle)
	// in UI Tabs
	// Data wird in einer anderen Funktion geholt
	if ($view == "overview"){
		// Werte nach Jahr holen
		$data = get_overview_data($year);
		$html .= '<div>';
		$html .= '<div id="tabs">';
		$html .= '<ul>';
		$uliyears = get_uli_years('DESC');
		if ($uliyears){
			foreach ($uliyears as $uliyear){
				if ($uliyear['ID'] != $option['currentyear']){
					$html .= '<li><a href="ajax_bank.php?action=overview&year='.$uliyear['ID'].'">'.$uliyear['name'].'</a></li>';
				}
				else {
					$html .= '<li><a href="#tabs-'.$uliyear['ID'].'">'.$uliyear['name'].'</a></li>';
				}
			}
		}
		$html .= "\n";
		$html .= '</ul>';
		$html .= '<div id="tabs-'.$option['currentyear'].'">';
		$html .= $data;
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
	}


	/* Jahresvergleich (graph) */
	/* Es werden alle Managerjahre (ab Startseason) in Balken nebeneinander gestellt.
	 * - Zinsen (Kreditzinsen, Dispozinsen) AB ZINSEN
	 * - Stadionbau AB 2005
	 */


	// Vergleich Bilanz/Durchschnitt Liga (Graph)
	// Was wird verglichen ?
	// 1. Einnahmen/Ausgaben/Saldo
	// 2. Gehaelter/Transein/Transaus
	// 3. TV/Merch/Praemien/Sponsoring
	// 4. Kreditzinsen/Dispozinsen
	if ($view == "compareyears"){
		$html .= '<div class="filter">';
		$Values[1]['values'] = array(7,8,9);
		$Values[2]['values'] = array(1,10,11);
		$Values[3]['values'] = array(15,16,17,19,20,21,22,23,24);
		$Values[4]['values'] = array(5,13);
		$Values[1]['name'] = OverviewGraph;
		$Values[2]['name'] = Personal;
		$Values[3]['name'] = Income;
		$Values[4]['name'] = SomethingElse;


		$html .= '<form action="?view=compareyears" method = "POST">';

		$html .= '<select id ="singlevalues" name = "type">';
		foreach ($Values as $key => $Value){
			if ($type == $key){$checked = 'selected = "selected"';}
			else {$checked = '';}
			$html .= '<option '.$checked.' value="'.$key.'">'.$Value['name'].'</option>';
		}
		$html .= '</select>';

		$html .= '<input type="submit" value="'.Change.'">';

		$html .= '</form>';
		$html .= '</div>';

		// Wenn nichts definiert wird, fangen wir mit dem Saldo an
		if (!$type){$type = 1;}

		// Alle Daten werden geholt
		$data = array();
		$uliyears = get_uli_years();
		if($uliyears){
			foreach($uliyears as $uliyear){
				$data[] = get_year_values_bank($option['uliID'], $uliyear['ID']);
			}
		}

		$html .= print_compare_chart($data, $type, $Values);

		$html .= '<div class="ulibox">';
		// Das ist der Container
		$html .= '<div id = "chart">';
		$html .= '</div>';
		$html .= '</div>';


	}
	///*		/* Daten holen */
	//		$uliyears = get_uli_years();
	//		foreach ($uliyears as $uliyear){
	//			$values = get_year_values_bank($option['uliID'], $uliyear['ID']);
	//			if ($values){
	//				foreach ($values as $value){
	//					/* Saldo */
	//					if ($value['type'] == 7)
	//					{$dataSaldo[0]['type'] = '"Saldo"';$dataSaldo[0]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 8)
	//					{$dataSaldo[1]['type'] = '"Einnahmen"';$dataSaldo[1]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 9)
	//					{$dataSaldo[2]['type'] = '"Ausgaben"';$dataSaldo[2]['sum'.$uliyear['ID']] = $value['sum'];}
	//
	//					/* Personalkosten */
	//					if ($value['type'] == 1)
	//					{$dataPlayer[0]['type'] = '"Gehaelter"';$dataPlayer[0]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 10)
	//					{$dataPlayer[1]['type'] = '"Transfereinnahmen"';$dataPlayer[1]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 11)
	//					{$dataPlayer[2]['type'] = '"Transferausgaben"';$dataPlayer[2]['sum'.$uliyear['ID']] = $value['sum'];}
	//					/* TV */
	//					if ($value['type'] == 16)
	//					{$dataTV[1]['type'] = '"Grundbetrag"';$dataTV[1]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 17)
	//					{$dataTV[2]['type'] = '"Punktgelder"';$dataTV[2]['sum'.$uliyear['ID']] = $value['sum'];}
	//					$dataTV[0]['type'] = '"Gesamteinnahmen"';$dataTV[0]['sum'.$uliyear['ID']] = $dataTV[2]['sum'.$uliyear['ID']] + $dataTV[1]['sum'.$uliyear['ID']];
	//					if(!$dataTV[1]['sum'.$uliyear['ID']]){$dataTV[1]['sum'.$uliyear['ID']] = 0;}
	//					if(!$dataTV[2]['sum'.$uliyear['ID']]){$dataTV[2]['sum'.$uliyear['ID']] = 0;}
	//
	//					/* Merch */
	//					if ($value['type'] == 15)
	//					{$dataMerch[0]['type'] = '"Merchandising"';$dataMerch[0]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if(!$dataMerch[0]['sum'.$uliyear['ID']]){$dataMerch[0]['sum'.$uliyear['ID']] = 0;}
	//
	//					/* Sponsoring */
	//					$dataSpons[0]['type'] = '"Gesamteinnahmen"';$dataSpons[0]['sum'.$uliyear['ID']] = $dataSpons[2]['sum'.$uliyear['ID']] + $dataSpons[1]['sum'.$uliyear['ID']];
	//					if ($value['type'] == 19)
	//					{$dataSpons[1]['type'] = '"Grundbetrag"';$dataSpons[1]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 20){$sumSponsPraem[$uliyear['ID']] = $sumSponsPraem[$uliyear['ID']] + $value['sum'];}
	//					if ($value['type'] == 21){$sumSponsPraem[$uliyear['ID']] = $sumSponsPraem[$uliyear['ID']] + $value['sum'];}
	//					if ($value['type'] == 22){$sumSponsPraem[$uliyear['ID']] = $sumSponsPraem[$uliyear['ID']] + $value['sum'];}
	//					if ($value['type'] == 23){$sumSponsPraem[$uliyear['ID']] = $sumSponsPraem[$uliyear['ID']] + $value['sum'];}
	//					if ($value['type'] == 24){$sumSponsPraem[$uliyear['ID']] = $sumSponsPraem[$uliyear['ID']] + $value['sum'];}
	//					$dataSpons[2]['type'] = '"Praemien"';$dataSpons[2]['sum'.$uliyear['ID']] = $sumSponsPraem[$uliyear['ID']] + 0;
	//					$dataSpons[0]['type'] = '"Gesamteinnahmen"';$dataSpons[0]['sum'.$uliyear['ID']] = $dataSpons[2]['sum'.$uliyear['ID']] + $dataSpons[1]['sum'.$uliyear['ID']];
	//					if(!$dataSpons[1]['sum'.$uliyear['ID']]){$dataSpons[1]['sum'.$uliyear['ID']] = 0;}
	//
	//					/* Sonstiges */
	//					if ($value['type'] == 12)
	//					{$dataElse[0]['type'] = '"Stadionbau"';$dataElse[0]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 5)
	//					{$dataElse[1]['type'] = '"Kreditzinsen"';$dataElse[1]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if ($value['type'] == 13)
	//					{$dataElse[2]['type'] = '"Dispozinsen"';$dataElse[2]['sum'.$uliyear['ID']] = $value['sum'];}
	//					if(!$dataElse[0]['sum'.$uliyear['ID']]){$dataElse[0]['sum'.$uliyear['ID']] = 0;}
	//					if(!$dataElse[1]['sum'.$uliyear['ID']]){$dataElse[1]['sum'.$uliyear['ID']] = 0;}
	//					if(!$dataElse[2]['sum'.$uliyear['ID']]){$dataElse[2]['sum'.$uliyear['ID']] = 0;}
	//				}
	//			}*/
	//		}
	//
	//
	//		$html .= print_html_charts();
	//		$html .= print_javascript_charts_compareyears($dataSaldo, $dataPlayer, $dataTV, $dataMerch, $dataSpons, $dataElse);
	//	}

	/* Rundenwerte (Graph )*/
	if ($view == "singlevalues"){

		// in der Kopfzeile stehen zwei Select Menues, die Jahr und Wert umschalten
		$html .= '<div class="filter">';
		$singleValues = array(7,1,2,17,20,21,15,13);

		$html .= '<form action="?view=singlevalues" method = "POST">';

		$html .= '<select id ="singlevalues" name = "type">';
		foreach ($singleValues as $singleValue){
			if ($type == $singleValue){$checked = 'selected = "selected"';}
			else {$checked = '';}
			$html .= '<option '.$checked.' value="'.$singleValue.'">'.$option['finance'.$singleValue].'</option>';
		}
		$html .= '</select>';

		// Jahre
		$uliyears = get_uli_years('DESC');
		if ($uliyears){
			$html .= '<select id ="years" name = "year">';
			foreach ($uliyears as $uliyear){
				if ($year == $uliyear['ID']){$checked = 'selected = "selected"';}
				else {$checked = '';}
				$html .= '<option '.$checked.' value="'.$uliyear['ID'].'">'.$uliyear['name'].'</option>';
			}
			$html .= '</select>';
		}

		$html .= '<input type="submit" value="'.Change.'">';

		$html .= '</form>';
		$html .= '</div>';

		// Wenn nichts definiert wird, fangen wir mit dem Saldo an
		if (!$type){$type = 7;}

		// Alle Daten werden geholt
		$data = get_round_values($type, $option['uliID'], $year);
		$html .= print_chart($data, $type);

		$html .= '<div class="ulibox">';
		// Das ist der Container
		$html .= '<div id = "chart">';
		$html .= '</div>';
		$html .= '</div>';

	}
	return $html;
}

/**
 * gibt das jqplot skript fuer die charts aus
 * @param unknown_type $data
 * @param unknown_type $value
 * @return unknown_type
 */
function print_compare_chart($alldata, $value, $allValues){
	global $option;
	$finances = $allValues[$value]['values'];
	if ($alldata){
		foreach ($alldata as $key => $yeardata){
			foreach ($yeardata as $data){
				if (in_array($data['type'], $finances)){
					if ($value != 3){
						$chartdata[$data['type']][$data['year']] = $data['sum'];
					}
					// Mergen
					else {
						// Merch
						if ($data['type'] == 15){
							$chartdata[$data['type']][$data['year']] = $data['sum'];
						}
						// TV
						if (in_array($data['type'], array(16,17))){
							$chartdata['TV-Einnahmen'][$data['year']] = $chartdata['TV-Einnahmen'][$data['year']] + $data['sum'];
						}
						// Sponsoring
						if (in_array($data['type'], array(19,20,21,22,23,24))){
							$chartdata['Sponsoring'][$data['year']] = $chartdata['Sponsoring'][$data['year']] + $data['sum'];
						}
					}
				}
			}
		}
	}

	$years = get_uli_years();
	$x = 1;
	foreach ($years as $year){

		foreach ($chartdata as $key => $data){
			if (!$chartdata[$key][$year['ID']]){
				$chartdata[$key][$year['ID']] = 0;
			}
		}
		$roundstring .= "'".$year['name']."'";
		if ($x < count($years)){
			$roundstring .= ',';
		}
		$x = $x +1;
	}

	if($chartdata){
		$x = 1;
		foreach($chartdata as $key => $chart){

			$y = 1;
			$attr .= 's'.$x;
			if ($option['finance'.$key]){
				$label .= "{label:'".$option['finance'.$key]."'}";
			}
			else {
				$label .= "{label:'".$key."'}";
			}
			if ($x < count($chartdata)){
				$attr .= ',';
				$label .= ',';
			}

			$datastring .= 'var s'.$x.' = [';
			foreach ($years as $year){
				$datastring .= $chart[$year['ID']];
				if ($y < count($chart)){
					$datastring .= ',';
				}
				$y = $y + 1;
			}
			$datastring .= '];';


			$x = $x + 1;
		}
	}

	// Jetzt faengt das Javascript an
	?>
<script>
$(document).ready(function(){
	<? echo $datastring; ?>
    // Can specify a custom tick Array.
    // Ticks should match up one for each y value (category) in the series.
    var ticks = [<?php echo $roundstring; ?>];

    var plot1 = $.jqplot('chart', [<?php echo $attr; ?>], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true},
            shadow: true,   // show shadow or not.
	        shadowAngle: 80,    // angle (degrees) of the shadow, clockwise from x axis.
	        shadowOffset: 1.1, // offset from the line of the shadow.
	        shadowDepth: 1,     // Number of strokes to make when drawing shadow.  Each
	                            // stroke offset by shadowOffset from the last.
	        shadowAlpha: 0.05,   // Opacity of the shadow.

        },

		// Color
        seriesColors: ["#7fbe47", "#e80000", "#5b7444"],
        negativeSeriesColors: ["#7fbe47", "#e80000", "#5b7444"],

        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
        series:[
            <? echo $label; ?>
        ],
        // Show the legend and put it outside the grid, but inside the
        // plot container, shrinking the grid to accomodate the legend.
        // A value of "outside" would not shrink the grid and allow
        // the legend to overflow the container.
        legend: {
            show: true,
            xxplacement: 'outsideGrid'
        },

        axesDefaults: {
            tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
            tickOptions: {
              angle: -30,
              fontSize: '6pt'
            }
        },

        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
	            renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
                pad: 1.05,
                tickOptions: {formatString: 'Euro %d'}
            }
        }
    });
});
</script>
            <?
            return $html;
}


/**
 * gibt das jqplot skript fuer die charts aus
 * @param unknown_type $data
 * @param unknown_type $value
 * @return unknown_type
 */
function print_chart($alldata, $value){
	global $option;

	if ($alldata){
		foreach ($alldata as $data){
			$datastring .= $data['sum'];
			$roundstring .= $data['round'];
			if ($data['round'] < count($alldata)){
				$datastring .= ',';
				$roundstring .= ',';
			}
		}
	}
	?>
<script>
$(document).ready(function(){
	var s1 = [<? echo $datastring; ?>];
    // Can specify a custom tick Array.
    // Ticks should match up one for each y value (category) in the series.
    var ticks = [<?php echo $roundstring; ?>];

    var plot1 = $.jqplot('chart', [s1], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true},
            shadow: true,   // show shadow or not.
	        shadowAngle: 80,    // angle (degrees) of the shadow, clockwise from x axis.
	        shadowOffset: 1.1, // offset from the line of the shadow.
	        shadowDepth: 1,     // Number of strokes to make when drawing shadow.  Each
	                            // stroke offset by shadowOffset from the last.
	        shadowAlpha: 0.05,   // Opacity of the shadow.

        },

		// Color
        seriesColors: ["#7fbe47"],
        negativeSeriesColors: ["#e80000"],

        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
        series:[
            {label:'<? echo $option['finance'.$value]; ?>'}
        ],
        // Show the legend and put it outside the grid, but inside the
        // plot container, shrinking the grid to accomodate the legend.
        // A value of "outside" would not shrink the grid and allow
        // the legend to overflow the container.
        legend: {
            show: true,
            xxplacement: 'outsideGrid'
        },
        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
                pad: 1.05,
                tickOptions: {formatString: 'Euro %d'}
            }
        }
    });
});
</script>
	<?
	return $html;
}


/**
 * gibt das Html fuer die Rundenwerte aus
 */
function print_day_saldo(){
	global $option;
	$dataSaldo 	= get_round_values(7, $option['uliID'], $option['currentyear']);
	//print_r($dataSaldo);

	if ($dataSaldo){
		foreach ($dataSaldo as $Saldo){
			if ($Saldo['sum'] < 0){$CssClass = 'ColorRed';}
			else {$CssClass = '';}
			$html .= $Saldo['round'].': <span class="'.$CssClass.'">'.uli_money($Saldo['sum']).'</span><br/>';
			$html .= "\n";
		}}
		if ($html){return $html;}
		else {return FALSE;}
}


/**
 * Gibt HTML f�r das Verm�gen aus
 * TODO: kennzeichnen von plus und minus
 * 17.05.09
 */
function print_vermoegen($guthaben, $vermoegen, $kredite){
	$html = Kontostand.': <b>'.uli_money($guthaben).'</b><br/>';
	$html .= Vermoegen.': <b>'.uli_money($vermoegen).'</b><br/>';
	if ($kredite > 0){
		$html .= RunningCredits.': '.uli_money($kredite).'';
	}
	return $html;
}


/**
 * gibt das HTML f�r die Kreditabteilung aus
 * 08.07.09
 */
function print_kreditabteilung($kredite, $kreditrahmen, $view, $year, $action, $message = '') {
	global $option;
	if ($message){$html .= '<b>'.$message.'</b><br/>';$html .= "\n";}

	$CreditLimit = $kreditrahmen-$kredite;
	if ($CreditLimit < 0){
		$CreditLimit = 0;
	}

	$html .= YourCreditLimit.' '.uli_money($CreditLimit);
	$html .= "\n";
	$kreditrahmen = $kreditrahmen - $kredite;
	if ($kreditrahmen < 0){$html .= YouDontGetMoreMoney;}
	else {
		$html .= '<form action="?action=kreditaufnehmen&view='.$view.'&year='.$year.'" method="POST">';
		$html .= "\n";
		$html .= '<input type = "text" size="10" name="sum"/> Summe';
		$html .= "\n";
		$html .= '<select name = "percent">';
		$html .= '<option value="15">6 Monate/15% p.a.</option>';
		$html .= '<option value="20">3 Monate/20% p.a.</option>';
		$html .= '<option value="25">1 Monate/25% p.a.</option>';
		$html .= '</select>';
		$html .= "\n";
		$html .= ' <input type = "submit" value="'.INeedTheMoney.'" />';
		$html .= "\n";
		$html .= '</form>';
		$html .= "\n";
	}
	return $html;
}

/**
 * gibt das HTML f�r alle laufenden kredite aus
 * 09.07.09
 */
function print_running_credits(){
	global $option;
	$uliID = $option['uliID'];
	$kredite = get_credits($uliID);
	if ($kredite){
		foreach ($kredite as $kredit){
			$html .= uli_money($kredit['sum']);
			$html .= ' ('.uli_date($kredit['end']).')';
			$html .= '<br/>';
			$html .= "\n";
		}}
		else {$html .= NoRunningCredits;}
		return $html;
}


/**
 * Holt alle Kredite eines Klubs in ein Array
 * 09.07.09
 */
function get_credits($uliID) {
	$cond[] = array("col" => "toklub", "value" => $uliID);
	$cond[] = array("col" => "paid", "value" => 0);
	$order[]= array("col" => "end", "sort" => "DESC");
	$result = uli_get_results('kredite', $cond, NULL, $order);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * schreibt einen kredit und berechnet das neue Guthaben
 * 09.07.09
 *
 */
function write_credit($sum, $percent, $uliID, $guthaben) {
	$timestamp = mktime();
	if ($percent == 15) {$end = $timestamp + 15552000;}
	if ($percent == 20) {$end = $timestamp + 7776000;}
	if ($percent == 25) {$end = $timestamp + 2592000;}
	$value[] = array("col" => "toklub", "value" => $uliID);
	$value[] = array("col" => "start", "value" => $timestamp);
	$value[] = array("col" => "end", "value" => $end);
	$value[] = array("col" => "percent", "value" => $percent);
	$value[] = array("col" => "sum", "value" => $sum);
	uli_insert_record('kredite', $value);

	unset($value);
	$newguthaben = $guthaben + $sum;
	$cond[] = array("col" => "uliID", "value" => $uliID);
	$cond[] = array("col" => "year", "value" => 0);
	$cond[] = array("col" => "round", "value" => 0);
	$cond[] = array("col" => "type", "value" => 14);
	$value[] = array("col" => "sum", "value" => $newguthaben);
	uli_update_record('finances', $cond, $value);
}

/**
 * Holt alle finanziellen Jahreswerte eines Managers
 * 22.05.09
 */
function get_year_values_bank($uliID, $year){
	$cond[] = array ("col" => "uliID", "value" => $uliID);
	$cond[] = array ("col" => "year", "value" => $year);
	$cond[] = array ("col" => "round", "value" => 0);
	$order[] = array ("col" => "type");
	$result = uli_get_results('finances',$cond, NULL, $order);
	if ($result){return $result;}
	else {return FALSE;}
}

/**
 * liefert ein Array mit allen Rundenwerten eines Jahres
 * 22.05.09
 */
function get_round_values($type, $uliID, $year){
	$cond[] = array ("col" => "uliID", "value" => $uliID);
	$cond[] = array ("col" => "year", "value" => $year);
	$cond[] = array ("col" => "round", "value" => 0, "func" => "!=");
	$cond[] = array ("col" => "type", "value" => $type);
	$order[] = array ("col" => "round");
	$result = uli_get_results('finances',$cond, NULL, $order);
	if ($result){return $result;}
	else {return FALSE;}
}



?>
