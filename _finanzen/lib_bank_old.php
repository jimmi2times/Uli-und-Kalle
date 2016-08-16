
//
//// *******************************************************
//// ******** JAVASCRIPTS			  ************************
//// *******************************************************
//
///**
// * gibt den DivContainer für Irgendetwas in Tabs aus
// */
//function print_html_charts() {
//	$html .= '<div id="tabContainer"></div>';
//	$html .= "\n";
//	return $html;
//}
//
//
///**
// * Gibt das Javascript für die Jahresvergleiche aus
// * ACHTUNG JAVASCRIPT VERSTEHT SCHEINBAR KEINE BINDESTRICHE IN VARIABLENNAMEN
// */
//function print_javascript_charts_compareyears($dataSaldo, $dataPlayer, $dataTV, $dataMerch,$dataSpons, $dataElse){
//	global $option;
//	$style = 'border: {color: 0x7fbe47, size: 2},
//	font: {color: 0x000, size: 9},
//	dataTip:
//	{
//		border: {color: 0xffffff, size: 1},
//		font: {color: 0xffffff},
//		background: {color: 0x7fbe47}
//	},
//	xAxis:
//	{
//		color: 0x000
//	},
//	yAxis:
//	{
//		color: 0x000,
//		majorTicks: {color: 0x000, length: 4},
//		minorTicks: {color: 0x000, length: 2},
//		majorGridLines: {color: 0x000, size: 1}
//	}
//';
//	$balkenstyle = '{color: 0x7fbe47,size: 30}';
//
//	$fields[] = array("name" => "type", "dbfield" => "type", "type" => "x", "desc" => "");
//	$fields[] = array("name" => "sum1", "dbfield" => "sum1", "type" => "y", "desc" => "Saison 2004/05");
//	$fields[] = array("name" => "sum2", "dbfield" => "sum2", "type" => "y", "desc" => "Saison 2005/06");
//	$fields[] = array("name" => "sum3", "dbfield" => "sum3", "type" => "y", "desc" => "Saison 2006/07");
//	$fields[] = array("name" => "sum6", "dbfield" => "sum6", "type" => "y", "desc" => "Saison 2007/08");
//	$fields[] = array("name" => "sum9", "dbfield" => "sum9", "type" => "y", "desc" => "Saison 2008/09");
//
//
//
//	$SaldoArray 	 = get_data_js_chart($dataSaldo, $fields, 'sum');
//	$playerArray 	 = get_data_js_chart($dataPlayer, $fields, 'sum');
//	$TVArray	 	 = get_data_js_chart($dataTV, $fields, 'sum');
//	$MerchArray	 	 = get_data_js_chart($dataMerch, $fields, 'sum');
//	$SponsArray	 	 = get_data_js_chart($dataSpons, $fields, 'sum');
//	$ElseData	 	 = get_data_js_chart($dataElse, $fields, 'sum');
//
//	/* JavaScript Starten */
//	$html .= '<script type = "text/javascript">';
//	$html .= 'YAHOO.widget.Chart.SWFURL = "'.$option['uliroot'].'/_mainlibs/includes/yui/build/charts/assets/charts.swf";';
//
//	/* DATASOURCE */
//	$html .= print_js_chart_DataSource('Saldo', $SaldoArray['dataArrayString'], $fields);
//	$html .= print_js_chart_DataSource('Personalkosten', $playerArray['dataArrayString'], $fields);
//	$html .= print_js_chart_DataSource('TV', $TVArray['dataArrayString'], $fields);
//	$html .= print_js_chart_DataSource('Merch', $MerchArray['dataArrayString'], $fields);
//	$html .= print_js_chart_DataSource('Spons', $SponsArray['dataArrayString'], $fields);
//	$html .= print_js_chart_DataSource('Else', $ElseData['dataArrayString'], $fields);
//
//	/* TAB VIEW */
//	$html .= print_js_tabview_start();
//	$html .= print_js_chart_createTab('Saldo', 'Saldo', 'columnchartSaldo', 1);
//	$html .= print_js_chart_createTab('Personalkosten', 'Personalkosten', 'columnchartPlayer');
//	$html .= print_js_chart_createTab('TV', 'TV', 'columnchartTV');
//	$html .= print_js_chart_createTab('Merchandising', 'Merchandising', 'columnchartMerch');
//	$html .= print_js_chart_createTab('Sponsoring', 'Sponsoring', 'columnchartSpons');
//	$html .= print_js_chart_createTab('Sonstiges', 'Sonstiges', 'columnchartElse');
//	$html .= print_js_tabview_append();
//
//	/* Beginn der eigentlichen Charts */
//	/* Achsen Definitionen */
//	$html .= print_js_chart_seriesDef($balkenstyle, $fields);
//	$html .= print_js_format_text($fields);
//	$html .= print_js_chart_dataTip();
//
//	$html .= print_js_chart_createAxis('Saldo', $SaldoArray['YAchse']);
//	$html .= print_js_chart_createAxis('Personalkosten', $playerArray['YAchse']);
//	$html .= print_js_chart_createAxis('TV', $TVArray['YAchse']);
//	$html .= print_js_chart_createAxis('Merch', $MerchArray['YAchse']);
//	$html .= print_js_chart_createAxis('Spons', $SponsArray['YAchse']);
//	$html .= print_js_chart_createAxis('Else', $ElseData['YAchse']);
//
//	$html .= print_js_chart_createColumnChart('Saldo', 'columnchartSaldo', $style, $fields);
//	$html .= print_js_chart_createColumnChart('Personalkosten', 'columnchartPlayer', $style, $fields);
//	$html .= print_js_chart_createColumnChart('TV', 'columnchartTV', $style, $fields);
//	$html .= print_js_chart_createColumnChart('Merch', 'columnchartMerch', $style, $fields);
//	$html .= print_js_chart_createColumnChart('Spons', 'columnchartSpons', $style, $fields);
//	$html .= print_js_chart_createColumnChart('Else', 'columnchartElse', $style, $fields);
//
//	$html .= '</script>';
//	return $html;
//}
//
//
//
///**
// * HEADER DER TABVIEWS STYLEN
// * DAS SOLLTEN ECHTE TABS SEIN (http://developer.yahoo.com/yui/examples/charts/charts-tabview.html)
// *   tabview.css von YUI ersetzen
// * gibt das ungetüm der Charts für die Rundenstatistiken aus
// * 22.05.09
// *
// */
//function print_javascript_charts_singlevalues($dataSaldo, $dataSalaries, $dataVisitors, $dataTV, $dataSponsoring, $dataPraemien, $dataMerch, $dataDispo){
//	global $option;
//
//	?>
//
//<script>
//$(document).ready(function(){
//    var s1 = [200, 600, 700, 1000];
//    var s2 = [460, -210, 690, 820];
//    var s3 = [-260, -440, 320, 200];
//    // Can specify a custom tick Array.
//    // Ticks should match up one for each y value (category) in the series.
//    var ticks = ['May', 'June', 'July', 'August'];
//    
//    var plot1 = $.jqplot('chart-15', [s1, s2, s3], {
//        // The "seriesDefaults" option is an options object that will
//        // be applied to all series in the chart.
//        seriesDefaults:{
//            renderer:$.jqplot.BarRenderer,
//            rendererOptions: {fillToZero: true}
//        },
//        // Custom labels for the series are specified with the "label"
//        // option on the series option.  Here a series option object
//        // is specified for each series.
//        series:[
//            {label:'Hotel'},
//            {label:'Event Regristration'},
//            {label:'Airfare'}
//        ],
//        // Show the legend and put it outside the grid, but inside the
//        // plot container, shrinking the grid to accomodate the legend.
//        // A value of "outside" would not shrink the grid and allow
//        // the legend to overflow the container.
//        legend: {
//            show: true,
//            placement: 'outsideGrid'
//        },
//        axes: {
//            // Use a category axis on the x axis and use our custom ticks.
//            xaxis: {
//                renderer: $.jqplot.CategoryAxisRenderer,
//                ticks: ticks
//            },
//            // Pad the y axis just a little so bars can get close to, but
//            // not touch, the grid boundaries.  1.2 is the default padding.
//            yaxis: {
//                pad: 1.05,
//                tickOptions: {formatString: '$%d'}
//            }
//        }
//    });
//});
//  </script>
//
//	<?php
//
//
//
//	//	$style = 'border: {color: 0x7fbe47, size: 2},
//	//	font: {color: 0x000, size: 9},
//	//	dataTip:
//	//	{
//	//		border: {color: 0xffffff, size: 1},
//	//		font: {color: 0xffffff},
//	//		background: {color: 0x7fbe47}
//	//	},
//	//	xAxis:
//	//	{
//	//		color: 0x000
//	//	},
//	//	yAxis:
//	//	{
//	//		color: 0x000,
//	//		majorTicks: {color: 0x000, length: 4},
//	//		minorTicks: {color: 0x000, length: 2},
//	//		majorGridLines: {color: 0x000, size: 1}
//	//	}
//	//';
//	//	$balkenstyle = '{color: 0x7fbe47,size: 40}';
//	//
//	//	$fields[] = array("name" => "day", "dbfield" => "round", "type" => "x", "desc" => "Spieltag");
//	//	$fields[] = array("name" => "sum", "dbfield" => "sum", "type" => "y", "desc" => "Summe");
//	//
//	//	$SaldoArray 	 = get_data_js_chart($dataSaldo, $fields, 'sum');
//	//	$SalaryArray 	 = get_data_js_chart($dataSalaries, $fields, 'sum');
//	//	$VisitorArray 	 = get_data_js_chart($dataVisitors, $fields, 'sum');
//	//	$TVArray 		 = get_data_js_chart($dataTV, $fields, 'sum');
//	//	$SponsoringArray = get_data_js_chart($dataSponsoring, $fields, 'sum');
//	//	$PraemienArray 	 = get_data_js_chart($dataPraemien, $fields, 'sum');
//	//	$MerchArray 	 = get_data_js_chart($dataMerch, $fields, 'sum');
//	//	$DispoArray 	 = get_data_js_chart($dataDispo, $fields, 'sum');
//	//
//	//
//	//	/* JavaScript Starten */
//	//	$html .= '<script type = "text/javascript">';
//	//	$html .= 'YAHOO.widget.Chart.SWFURL = "'.$option['uliroot'].'/_mainlibs/includes/yui/build/charts/assets/charts.swf";';
//	//
//	//	/* DATASOURCE */
//	//	if ($SaldoArray)	{$html .= print_js_chart_DataSource('Saldo', $SaldoArray['dataArrayString'], $fields);}
//	//	if ($SalaryArray)	{$html .= print_js_chart_DataSource('Salaries', $SalaryArray['dataArrayString'], $fields);}
//	//	if ($VisitorArray)	{$html .= print_js_chart_DataSource('Visitors', $VisitorArray['dataArrayString'], $fields);}
//	//	if ($TVArray)		{$html .= print_js_chart_DataSource('TV', $TVArray['dataArrayString'], $fields);}
//	//	if ($SponsoringArray){$html .= print_js_chart_DataSource('Sponsoring', $SponsoringArray['dataArrayString'], $fields);}
//	//	if ($PraemienArray)	{$html .= print_js_chart_DataSource('Praemien', $PraemienArray['dataArrayString'], $fields);}
//	//	if ($MerchArray)	{$html .= print_js_chart_DataSource('Merch', $MerchArray['dataArrayString'], $fields);}
//	//	if ($DispoArray)	{$html .= print_js_chart_DataSource('Dispo', $DispoArray['dataArrayString'], $fields);}
//	//
//	//	/* TAB VIEW */
//	//	$html .= print_js_tabview_start();
//	//	if ($SaldoArray)	{$html .= print_js_chart_createTab('Saldo', 'Saldo', 'columnchartSaldo', 1);}
//	//	if ($SalaryArray)	{$html .= print_js_chart_createTab('Geh&auml;lter', 'Geh&auml;lter', 'columnchartSalaries');}
//	//	if ($VisitorArray)	{$html .= print_js_chart_createTab('Zuschauer', 'Zuschauer', 'columnchartVisitors');}
//	//	if ($TVArray)		{$html .= print_js_chart_createTab('TV', 'TV', 'columnchartTV');}
//	//	if ($SponsoringArray){$html .= print_js_chart_createTab('Sponsoring', 'Sponsoring', 'columnchartSponsoring');}
//	//	if ($PraemienArray)	{$html .= print_js_chart_createTab('Praemien', 'Praemien', 'columnchartPraemien');}
//	//	if ($MerchArray)	{$html .= print_js_chart_createTab('Merch', 'Merch', 'columnchartMerch');}
//	//	if ($DispoArray)	{$html .= print_js_chart_createTab('Dispo', 'Dispo', 'columnchartDispo');}
//	//	$html .= print_js_tabview_append();
//	//
//	//	/* Beginn der eigentlichen Charts */
//	//	/* Achsen Definitionen */
//	//	$html .= print_js_chart_seriesDef($balkenstyle, $fields);
//	//	$html .= print_js_format_text($fields);
//	//	$html .= print_js_chart_dataTip();
//	//
//	//	if ($SaldoArray)	{$html .= print_js_chart_createAxis('Saldo', $SaldoArray['YAchse']);}
//	//	if ($SalaryArray)	{$html .= print_js_chart_createAxis('Salaries', $SalaryArray['YAchse']);}
//	//	if ($SalaryArray)	{$html .= print_js_chart_createAxis('Visitors', $VisitorArray['YAchse']);}
//	//	if ($TVArray)		{$html .= print_js_chart_createAxis('TV', $TVArray['YAchse']);}
//	//	if ($SponsoringArray){$html .= print_js_chart_createAxis('Sponsoring', $SponsoringArray['YAchse']);}
//	//	if ($PraemienArray)	{$html .= print_js_chart_createAxis('Praemien', $PraemienArray['YAchse']);}
//	//	if ($MerchArray)	{$html .= print_js_chart_createAxis('Merch', $MerchArray['YAchse']);}
//	//	if ($DispoArray)	{$html .= print_js_chart_createAxis('Dispo', $DispoArray['YAchse']);}
//	//
//	//	if ($SaldoArray)	{$html .= print_js_chart_createColumnChart('Saldo', 'columnchartSaldo', $style, $fields);}
//	//	if ($SalaryArray)	{$html .= print_js_chart_createColumnChart('Salaries', 'columnchartSalaries', $style, $fields);}
//	//	if ($SalaryArray)	{$html .= print_js_chart_createColumnChart('Visitors', 'columnchartVisitors', $style, $fields);}
//	//	if ($TVArray)		{$html .= print_js_chart_createColumnChart('TV', 'columnchartTV', $style, $fields);}
//	//	if ($SponsoringArray){$html .= print_js_chart_createColumnChart('Sponsoring', 'columnchartSponsoring', $style, $fields);}
//	//	if ($PraemienArray)	{$html .= print_js_chart_createColumnChart('Praemien', 'columnchartPraemien', $style, $fields);}
//	//	if ($MerchArray)	{$html .= print_js_chart_createColumnChart('Merch', 'columnchartMerch', $style, $fields);}
//	//	if ($DispoArray)	{$html .= print_js_chart_createColumnChart('Dispo', 'columnchartDispo', $style, $fields);}
//	//	$html .= '</script>';
//	return $html;
//}
//
///**
// * Macht aus den Daten JS Array und legt die Y-Achse fest
// * gibt ein assoziatives Array zurueck
// * geht erst einmal nur fuer die Bank-Rundenwerte
// * Das X Feld steht immer am Anfang
// *
// * gibt FALSE zurueck, wenn dataArray leer ist
// */
//function get_data_js_chart($dataArray, $fields, $sumfield){
//	if (!$dataArray){return FALSE;}
//	foreach ($dataArray as $value){
//		$fieldstringArray = array();
//		foreach ($fields as $field){
//			$fieldstringArray[] = $field['name'].': '.$value[$field['dbfield']].'';
//			if($field['type'] == "y"){$sumArray[] = $value[$field['dbfield']];}
//		}
//		$dataArrayString[] = '{'.implode(',', $fieldstringArray).'}';
//	}
//
//	/* Festlegen der Y Achse */
//	$YAchse = min($sumArray) - min($sumArray) * 0.33;
//	if ($YAchse < 0){ $YAchse = $YAchse + $YAchse * 0.66;}
//
//	$array['dataArrayString'] = $dataArrayString;
//	$array['YAchse'] = $YAchse;
//	return $array;
//}
//
///**
// * liefert den JS teil für die TextFormatierungen der Charts
// * nur innerhalb eines ChartSkiptes zu verwenden
// * 25.05.09
// */
//function print_js_format_text($fields, $type = '') {
//	if (!$type){$type = "currency";}
//
//	foreach($fields as $field){
//		if ($field['type'] == "x"){
//			$XField = $field['name'];
//			$XDesc = $field['desc'];
//		}}
//
//
//		if ($type == "currency"){
//			$html .= '
//			//format currency for axis
//	YAHOO.example.formatCurrencyAxisLabel = function( value )
//	{return YAHOO.util.Number.format( value/1000000,{suffix : " Mio",thousandsSeparator: ".",decimalPlaces: 2});	}
//
//	//format currency for tooltip
//	YAHOO.example.formatCurrencyTooltip = function( value )
//	{return YAHOO.util.Number.format( value,{suffix : " Euro",thousandsSeparator: ".",decimalPlaces: 0});}
//';	
//		}
//
//
//		$html .= '
//		//return the formatted text
//	YAHOO.example.getDataTipText = function( item, index, series, axisField )
//	{
//		var toolTipText = series.displayName + " '.$XDesc.' " + item.'.$XField.';
//		toolTipText += "\n" + YAHOO.example.formatCurrencyTooltip( item[series[axisField]] );
//		return toolTipText;
//	}';
//		return $html;
//}
//
///**
// * liefert den JS teil für die SeriesDefinition der Charts
// * nur innerhalb eines ChartSkiptes zu verwenden
// * 25.05.09
// */
//function print_js_chart_seriesDef($balkenstyle, $fields){
//	$html .= '
//	//--- chart
//	//series definition for Column and Line Charts
//	var seriesDef =
//	[';
//	foreach ($fields as $field){
//		if ($field['type'] == "y"){
//			$yfields[] = '{displayName: "'.$field['desc'].'", yField: "'.$field['dbfield'].'",	style: '.$balkenstyle.'}';
//		}}
//		$html .= implode(',', $yfields);
//		$html .= '];';
//		return $html;
//}
//
///**
// * liefert den JS teil für den Start der TabView
// * 25.05.09
// */
//function print_js_tabview_start(){
//	$html .= '
//	//--- tabView
//	//Create a TabView
//	var tabView = new YAHOO.widget.TabView();';
//	return $html;
//}
//
///**
// * liefert den JS teil für das Anwenden der TabView
// * 25.05.09
// */
//function print_js_tabview_append(){
//	$html .= '
//	//Append TabView to its container div
//	tabView.appendTo(\'tabContainer\');';
//	return $html;
//}
//
///**
// * liefert den JS teil für das den DataTip
// * nur innerhalb eines ChartSkiptes zu verwenden
// * 25.05.09
// */
//function print_js_chart_dataTip() {
//	$html .= '//DataTip function for the Line Chart and Column Chart
//	YAHOO.example.getYAxisDataTipText = function( item, index, series )
//	{return YAHOO.example.getDataTipText(item, index, series, "yField");}';	
//	return $html;
//}
//
//
///**
// * liefert den JS teil für das Bauen eines Balkendiagrammes
// * nur innerhalb eines ChartSkiptes zu verwenden
// * 25.05.09
// */
//function print_js_chart_createColumnChart($YAHOOName, $tabID, $style, $fields){
//	foreach($fields as $field){
//		if ($field['type'] == "x"){
//			$XField = $field['name'];
//		}}
//		$html .= '
//	//Create Column Chart Dispo
//	var columnChart'.$YAHOOName.' = new YAHOO.widget.ColumnChart( "'.$tabID.'", DataSource'.$YAHOOName.',
//	{series: seriesDef,xField: "'.$XField.'",yAxis: currencyAxis'.$YAHOOName.',dataTipFunction: YAHOO.example.getYAxisDataTipText,
//		style:{'.$style.'},
//		//only needed for flash player express install
//		expressInstall: "assets/expressinstall.swf"});';
//		return $html;
//}
//
///**
// * liefert den JS teil für das Bauen der Achse
// * nur innerhalb eines ChartSkiptes zu verwenden
// * 25.05.09
// */
//function print_js_chart_createAxis($YAHOOName, $YAchse){
//	$html .= '//create a Numeric Axis
//	var currencyAxis'.$YAHOOName.' = new YAHOO.widget.NumericAxis();
//	currencyAxis'.$YAHOOName.'.minimum = '.$YAchse.';
//	currencyAxis'.$YAHOOName.'.labelFunction = YAHOO.example.formatCurrencyAxisLabel;';
//	return $html;
//}
//
//
///**
// * liefert den JS teil für das Einfügen eines Tabs
// * nur innerhalb eines ChartSkiptes zu verwenden
// * 25.05.09
// */
//function print_js_chart_createTab($label, $chartTitle, $id, $active ='') {
//	//Add a tab for the Column Chart
//	$html .= '
//	tabView.addTab( new YAHOO.widget.Tab({
//			label: \''.$label.'\',
//			content: \'<span class="chart_title">'.$chartTitle.'</span><div class="chart" id="'.$id.'"></div>\'';
//	if ($active == 1) $html .= ', active: true';
//	$html .= '}));';
//	return $html;
//}
//
//
///**
// * liefert den JS teil für die DataSource
// * nur innerhalb eines ChartSkiptes zu verwenden
// * 25.05.09
// */
//function print_js_chart_DataSource($YAHOOName, $dataStringArray, $fields){
//	//--- DataSource Sponsoring
//	foreach ($fields as $field){
//		$fieldString[] = '"'.$field['name'].'"';
//	}
//	$html .= '
//	YAHOO.'.$YAHOOName.' =
//	[';
//	/* hier werden die Werte in das Array geschrieben */
//	$html .= implode(", ", $dataStringArray);
//	$html .='
//	];
//	var DataSource'.$YAHOOName.' = new YAHOO.util.DataSource( YAHOO.'.$YAHOOName.' );
//	DataSource'.$YAHOOName.'.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
//	DataSource'.$YAHOOName.'.responseSchema = {fields: ['.implode(',', $fieldString).']};	
//	';
//	return $html;
//}


/**
 * TODO: Die Komplettübersicht über alle Statistiken. Vielleicht noch einmal überarbeiten
 * Dauert auf jeden Fall zu lange
 */

// gibt alle finanziellen werte aus
function print_bilanz_bwl($uliID){
	//	global $wpdb, $CONFIG;
	//
	//	// Check von allen Posten pro Jahr
	//	$uliyear = get_attribute('uliyears');
	//
	//	// Überprüfen der Kredite
	//	$sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_kredite '.
	//			'WHERE toklub  = '.$uliID.' '.
	//			' AND paid = 1 ';
	//	$result = $wpdb->get_results($sql,ARRAY_A);
	//	if ($result){
	//
	//	uli_tabelle_header(1, array('Kredite'));
	//	uli_tabelle_end();
	//
	//	$headers = array('Kreditsumme', 'bez. Zinsen', 'Datum','Posten');
	//	uli_tabelle_header(4, $headers, '', array(30,30,10,30));
	//
	//	$zinsen='';
	//	foreach ($result as $kredit){
	//		$years = ($kredit['end'] - $kredit['start']) / 60 / 60 / 24 / 365;
	//		$minus = number_format(($kredit['sum'] / 100 * $kredit['percent'] * $years),0, ",", ".").' Euro';
	//		$zinsen = $zinsen + $minus;
	//		$content = array(number_format($kredit['sum'],0, ",", ".").' Euro', $minus, make_date($kredit['end']), 'Kreditzinsen');
	//		uli_tabelle_content(4, $content);
	//
	//	}
	//	$zinsen_gesamt='';
	//	$uliyear = get_attribute('uliyears');
	//	for ($x=1; $x <= $uliyear; $x++) {
	//		// Kopf
	//		$year = get_attribute('uliyear'.$x);
	//		$zinsen_gesamt = $zinsen_gesamt + get_value_bank(5, 0, $year, $uliID);
	//		settype($zinsen_gesamt, INT);
	//	}
	//	$minus = number_format($zinsen_gesamt,0, ",", ".").' Euro';
	//	$content = array('<strong>---</strong>', '<strong>'.$minus.'</strong>', '<strong>'.'GESAMT'.'</strong>', '<strong>Kreditzinsen</strong>');
	//	uli_tabelle_content(4, $content);
	//	uli_tabelle_end();
	//	}
	//	// *************************************************
	//
	//
	//
	//	for ($x=1; $x <= $uliyear; $x++) {
	//		// Kopf
	//		$year = get_attribute('uliyear'.$x);
	//		$yearname = get_second_attribute('uliyear'.$x);
	//		$seasonstart = get_attribute('uliyear'.$x.'time');
	//		$seasonend = get_second_attribute('uliyear'.$x.'time');
	//		uli_tabelle_header(1, array($yearname));
	//		// Der ganze Spaß nur, wenn Saldo != 0
	//		if (get_value_bank(7, 0, $year, $uliID) == 0){
	//			uli_tabelle_content(1, array('Da warst Du noch nicht in der Bundesliga'));
	//			uli_tabelle_end();
	//			}
	//		else {
	//		uli_tabelle_end();
	//			$type = array(1,2,13,15,17,);
	//			foreach ($type as $key => $type){
	//				$value='';
	//				$value_gesamt='';
	//				$style='';
	//
	//				$typename = get_attribute('finance'.$type);
	//				$typesaldo = get_second_attribute('finance'.$type);
	//				$headers = array('Einnahmen', 'Ausgaben', 'Spieltag','Posten');
	//				uli_tabelle_header(4, $headers, '', array(30,30,10,30));
	//
	//				$thereistcontent='';
	//				for ($y = 1; $y <= 34; $y++){
	//					unset($value);
	//					unset($content);
	//					$value = get_value_bank($type, $y, $year, $uliID);
	//					settype($value, INT);
	//					$minus = '---';
	//					$plus = '---';
	//					if ($typesaldo == "minus"){$minus = number_format($value,0, ",", ".").' Euro';}
	//					if ($typesaldo == "plus"){$plus = number_format($value,0, ",", ".").' Euro';}
	//					if ($value != 0){
	//						$content = array($plus, $minus, $y, $typename);
	//						uli_tabelle_content(4, $content);
	//						$thereistcontent = TRUE;
	//						}
	//					}
	//				if (!$thereistcontent){
	//					$content = array('Keinerlei Eintr&auml;ge', '', '', $typename);
	//					uli_tabelle_content(4, $content);
	//					}
	//				$value_gesamt = get_value_bank($type, 0, $year, $uliID);
	//
	//				settype($value_gesamt, INT);
	//				$minus = '---';
	//				$plus = '---';
	//				if ($typesaldo == "minus"){$minus = number_format($value_gesamt,0, ",", ".").' Euro';}
	//				if ($typesaldo == "plus"){$plus = number_format($value_gesamt,0, ",", ".").' Euro';}
	//				$content = array('<strong>'.$plus.'</strong>', '<strong>'.$minus.'</strong>', '<strong>'.'GESAMT'.'</strong>', '<strong>'.$typename.'</strong>');
	//				uli_tabelle_content(4, $content);
	//				uli_tabelle_end();
	//				}
	//			// *************************************************
	//
	//			// Die 0 Werte
	//			$type = array(6,12,16,19);
	//			foreach ($type as $key => $type){
	//				unset($value);
	//				unset($value_gesamt);
	//				unset ($style);
	//
	//				$typename = get_attribute('finance'.$type);
	//				$typesaldo = get_second_attribute('finance'.$type);
	//				$headers = array('Einnahmen', 'Ausgaben', 'Spieltag','Posten');
	//				uli_tabelle_header(4, $headers, '', array(30,30,10,30));
	//
	//				unset($thereistcontent);
	//				unset($value);
	//				unset($content);
	//				$value = get_value_bank($type, 0, $year, $uliID);
	//				settype($value, INT);
	//				$minus = '---';
	//				$plus = '---';
	//				if ($typesaldo == "minus"){$minus = number_format($value,0, ",", ".").' Euro';}
	//				if ($typesaldo == "plus"){$plus = number_format($value,0, ",", ".").' Euro';}
	//				if ($value != 0){
	//					$content = array($plus, $minus, $y, $typename);
	//					uli_tabelle_content(4, $content);
	//					$thereistcontent = TRUE;
	//					}
	//				if (!$thereistcontent){
	//					$content = array('Keinerlei Eintr&auml;ge', '', '', $typename);
	//					uli_tabelle_content(4, $content);
	//					}
	//				$value_gesamt = get_value_bank($type, 0, $year, $uliID);
	//				settype($value_gesamt, INT);
	//				$minus = '---';
	//				$plus = '---';
	//				if ($typesaldo == "minus"){$minus = number_format($value_gesamt,0, ",", ".").' Euro';}
	//				if ($typesaldo == "plus"){$plus = number_format($value_gesamt,0, ",", ".").' Euro';}
	//				$content = array('<strong>'.$plus.'</strong>', '<strong>'.$minus.'</strong>', '<strong>'.'GESAMT'.'</strong>', '<strong>'.$typename.'</strong>');
	//				uli_tabelle_content(4, $content);
	//				uli_tabelle_end();
	//				}
	//			// *************************************************
	//
	//			// Überprüfen der Transfers einnahmen
	//			$sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_transfer '.
	//					'WHERE uliold  = '.$uliID.' '.
	//					' '.
	//					' AND time < '.$seasonend.' AND time > '.$seasonstart.'  ORDER by time asc';
	//			$result = $wpdb->get_results($sql,ARRAY_A);
	//			if ($result){
	//			$headers = array('Einnahmen', 'Ausgaben', 'Datum','Transfer');
	//			uli_tabelle_header(4, $headers, '', array(30,30,10,30));
	//
	//			$t_einnahmen='';
	//			$count='';
	//			foreach ($result as $transfer){
	//				$count = $count + 1;
	//				$t_einnahmen =  number_format(($transfer['sum']),0, ",", ".").' Euro';
	//				$playername  =  get_player_infos_single('player', 'name', $transfer['playerID']);
	//				$content = array($t_einnahmen, '---', make_date($transfer['time']), $playername);
	//				uli_tabelle_content(4, $content);
	//
	//			}}
	//			if ($count) {$average = get_value_bank(10, 0, $year, $uliID) / $count;}
	//			$average = number_format(($average),0, ",", ".").' Euro';
	//			$t_einnahmen_gesamt = number_format((get_value_bank(10, 0, $year, $uliID)),0, ",", ".").' Euro';
	//			$content = array('<strong>'.$t_einnahmen_gesamt.'</strong>', '<strong>Im Schnitt: '.$average.'</strong>', '<strong>'.'GESAMT'.'</strong>', '<strong>'.$count.' Transfers (Abg&auml;nge)</strong>');
	//			uli_tabelle_content(4, $content);
	//
	//			uli_tabelle_end();
	//
	//
	//
	//			// Überprüfen der Transfer-Einnahmen
	//			$sql = 	'SELECT * FROM '.$CONFIG->prefix.'uli_transfer '.
	//					'WHERE ulinew  = '.$uliID.' '.
	//					' '.
	//					' AND time < '.$seasonend.' AND time > '.$seasonstart.' ORDER by time asc';
	//			$result = $wpdb->get_results($sql,ARRAY_A);
	//			if ($result){
	//			$headers = array('Einnahmen', 'Ausgaben', 'Datum','Transfer');
	//			uli_tabelle_header(4, $headers, '', array(30,30,10,30));
	//
	//			unset($t_einnahmen);
	//			unset($count);
	//			foreach ($result as $transfer){
	//				$count = $count + 1;
	//				$t_ausgaben =  number_format(($transfer['sum']),0, ",", ".").' Euro';
	//				$playername  =  get_player_infos_single('player', 'name', $transfer['playerID']);
	//				$content = array('---', $t_ausgaben, make_date($transfer['time']), $playername);
	//				uli_tabelle_content(4, $content);
	//
	//			}}
	//			if ($count){$average = get_value_bank(11, 0, $year, $uliID) / $count;}
	//			$average = number_format(($average),0, ",", ".").' Euro';
	//			$t_ausgaben_gesamt = number_format((get_value_bank(11, 0, $year, $uliID)),0, ",", ".").' Euro';
	//			$content = array('<strong>'.$t_ausgaben_gesamt.'</strong>', '<strong>Im Schnitt: '.$average.'</strong>', '<strong>'.'GESAMT'.'</strong>', '<strong>'.$count.' Transfers (Zug&auml;nge)</strong>');
	//			uli_tabelle_content(4, $content);
	//
	//			uli_tabelle_end();
	//
	//
	//
	//			// ENDE Check ob user im Spiel
	//			}
	//	}

}

// *******************************************************
// ******** ENDE BANK FUNKTIONEN ************************
// *******************************************************