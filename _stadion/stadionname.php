<?php
/*
 *
 *
 * Sitze: 16 Blšcke a 5.000 PlŠtze
 *
 *
 *
 *
 *
 *
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "stadion", "sub" => "stadiumName");
uli_header(array('lib_stadion'));



/* Parameter */
$year = $_REQUEST['year']; settype($year, INT);
if(!$year){$year = $option['currentyear'];}

$view = $_REQUEST['view'];
if (!$view){$view = 'singlevalues';}
$action = $_REQUEST['action'];



/* ************************************************** */

?>

<script>
$('#stadiumform').live('submit', function() {

	//alert('hallo');
	var returnBox = $("#container");
	var stadiumname = $("#stadionname").attr("value");
	//alert(stadiumname);
	$.ajax({
		type: "POST", url: "ajax_seats.php", data: "action=changename&name="+stadiumname,
		complete: function(data){
			returnBox.html(data.responseText);
		}
	 });	
	
	return false;
	});



$('.acceptoffer').live('submit', function() {
	var returnBox = $("#container");
	var contractId = $(".contract").data("contract");
	//alert(contractId);
	$.ajax({
		type: "POST", url: "ajax_seats.php", data: "action=acceptoffer&contractId="+contractId,
		complete: function(data){
			returnBox.html(data.responseText);
		}
	 });	
	
	return false;
	});

</script>


<?php 



/* Ausgabe des Containers für Messages */
echo '<div id="container">';
echo '</div>';






?>
<div class="LeftColumn"><?php 
echo uli_box("Dein Stadion", "Du kannst Deinem Stadion einen kreativen Namen geben. Oder Dich auf das Angebot windiger Investoren, die Dir einen - nicht immer attraktiven - Namen aufschwatzen wollen, einlassen. <br/><br/>Damit man hier auch was lernt. Die Allianz zahlte f&uuml;r die gleichnamige Arena 90 Millionen f&uuml;r 15 Jahren, macht 6 Mio im Jahr. Daran - und an dem Stellenwert Deines Teams - orientieren sich die Angebote.");
?></div>

<div class="RightColumnLarge"><?php 
$stadium = get_stadium($option['uliID']);
$stadiumName = get_stadion_name($option['uliID']);


if (!$stadiumName){
	$stadiumName = generate_stadion_name_offer($option['uliID']);
}


if ($stadiumName){
	// hier kann immer nur ein aktueller Eintrag stehen
	$name = $stadiumName[0];

	// Angebot steht
	if ($name['status'] == 1){
		// Stadionname kann eingebene und geaendert werden
		$html = '<form action = "?" METHOD = "POST" id ="stadiumform">';
		$html .= '<input type = text size = 80 id = "stadionname" value = "'.$stadium[0]['name'].'">';
		$html .= '<input type = submit value = "Name &auml;ndern">';
		$html .= '</form>';
		echo uli_box("Dein Name", $html, NULL, 'stadiumName');

		// Angebot wird angezeigt


		$html = '';

		if ($name['years'] == 1){
			$years = '1 Jahr';
		}
		if ($name['years'] == 2){
			$years = '2 Jahre';
		}
		//print_r($stadium);
		// Bild aussuchen
		//echo $stadium[0]['ID'];
		$stadiumID = $stadium[0]['ID'];
		settype($stadiumID, INT);
		$faktor = $stadiumID % 10;
		$html .= '<div class="stadiumname_bild">';
		if ($faktor >= 0){
			$html .= '<img src="../theme/graphics/stadium/manager1.jpg" alt="(c) Bigbug21 - http://commons.wikimedia.org/wiki/User:Bigbug21">';
		}
		if ($faktor >= 3){
			$html .= '<img src="../theme/graphics/stadium/manager2.jpg" alt="(c) originally posted to Flickr as Josef Ackermann - World Economic Forum Annual Meeting Davos 2008, Sandstein, http://commons.wikimedia.org/wiki/User:Sandstein">';
		}
		if ($faktor >= 5){
			$html .= '<img src="../theme/graphics/stadium/manager3.jpg" alt="(c) http://commons.wikimedia.org/wiki/User:Eurobas - Eurobas">';
		}
		if ($faktor >= 8){
			$html .= '<img src="../theme/graphics/stadium/manager4.jpg" alt="(c) Juan Alberto PŽrez http://www.flickr.com/photos/38384810@N02/">';
		}
		$html .= '</div>';
		$html .= '<div class = "stadiumname_angebot">';
		
		$html .= 'Dieser freundliche Herr macht Dir folgendes Angebot: <br><br>';
		
		$html .= 'Nenne Deine Stadion f&uuml;r <b>'.$years.'</b> doch einfach <b>'.$name['sponsor'].'</b><br><br>';
		$html .= 'Du bekommst dann einmalig <b>'.uli_money($name['sum']).'</b> ausgezahlt.<br/><br/>';
		$html .= 'Der F&uuml;ller liegt bereit.<br/><br/>';

		$html .= '<div class="contractMessage">';
		$html .= '<form class = "acceptoffer" action = "?action=acceptoffer" METHOD = "POST" id ="name_contract_form-'.$name['ID'].'">';
		$html .= '<input data-contract = "'.$name['ID'].'" class = "contract" type = submit value = "Unterschreiben">';
		$html .= '</form>';		
		$html .= '</div>';
		
		
		$html .= '</div>';

		echo uli_box("Das unmoralische Angebot", $html, NULL, "stadiumContract");
			

	}

	// Unterschriebener Vertrag
	if ($name['status'] == 2){
		// Name kann nicht geaendert werden
		$html = '<form action = "?" METHOD = "POST" id ="stadiumform">';
		$html .= '<input type = text size = 80 readonly id = "stadionname" value = "'.$stadium[0]['name'].'">';
		$html .= '</form>';
		echo uli_box("Dein Name", $html, NULL, 'stadiumName');

		$html = '';

		if ($name['years'] == 1){
			$years = '1 Jahr';
		}
		if ($name['years'] == 2){
			$years = '2 Jahre';
		}
		//print_r($stadium);
		// Bild aussuchen
		//echo $stadium[0]['ID'];
		$stadiumID = $stadium[0]['ID'];
		settype($stadiumID, INT);
		$faktor = $stadiumID % 10;
		$html .= '<div class = "stadiumname_angebot">';
		
		
		$html .= 'Deine Stadion hei&szlig;t f&uuml;r <b>'.$years.'</b>: <b>'.$name['sponsor'].'</b><br><br>';
		$html .= 'Du hast daf&uuml;r einmalig <b>'.uli_money($name['sum']).'</b> bekommen.<br/><br/>';
		$html .= 'Erl&ouml;st bist Du wieder am '.uli_date(($name['end'])).'<br/>';
		
		
		$html .= '</div>';

		echo uli_box("Diesen Vertrag hast Du mal unterschrieben.", $html);
	}

}

?></div>









<?php


/* Footer */
uli_footer();
?>
