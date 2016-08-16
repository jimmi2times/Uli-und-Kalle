<?php
/*
 * 
 * 
 * was soll gebaut werden koennen
 * 
 * bratwurst (n-mal)
 * bier (n-mal)
 * kinderland
 * museum
 * merchandising (klein, mittel, gro§)
 * autobahnzubringer
 * bushaltestelle 
 * s-bahn-anschluss
 * fanprojekt
 * geiler videowuerfel
 * fettes soundsystem
 * fancy architektur 
 * 
 * 
 */
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

/* Header */
$page = array("main" => "stadion", "sub" => "infra");
uli_header(array('lib_stadion'));



/* Parameter */
$year = $_REQUEST['year']; settype($year, INT);
if(!$year){$year = $option['currentyear'];}

$view = $_REQUEST['view']; 
if (!$view){$view = 'singlevalues';}

/* ************************************************** */


/* Ausgabe des Containers für Messages */
echo '<div id="container">';
echo '</div>';
?>



<script>

// etwas bauen
$('.infrabox button').live('click', function() {

	//alert(this.id);
	var returnBox = $(".infrastruktur ." + this.id);
	$.ajax({
		type: "POST", url: "ajax_seats.php", data: "action=buildinfra&what="+this.id,
		complete: function(data){
			returnBox.html(data.responseText);
		}
	 });	
	 return false;
	});




</script>





<?php 

$stadium = get_stadium($option['uliID']);

//print_r($stadium);

$bier = 0;
$bratwurst = 0;
$merchandising = 0;
$fanprojekt = FALSE;
$museum = FALSE;
$kinderland = FALSE;
$videowuerfel = FALSE;
$soundsystem = FALSE;
$sbahn = FALSE;
$bushaltestelle = FALSE;
$autobahn = FALSE;
$architektur = FALSE;


if ($stadium['infra']){
	
	foreach ($stadium['infra'] as $infra){
		if ($infra['type'] == "bier"){$bier = $bier + 1;}
		if ($infra['type'] == "bratwurst"){$bratwurst = $bratwurst + 1;}
		if ($infra['type'] == "merchandising"){$merchandising = $merchandising + 1;}
		if ($infra['type'] == "fanprojekt"){$fanprojekt = TRUE; $fanprojektclass = "_aktiv";$fanprojekttime = uli_date($infra['built']);}
		if ($infra['type'] == "museum"){$museum = TRUE;$museumclass = "_aktiv";$museumtime = uli_date($infra['built']);}
		if ($infra['type'] == "kinderland"){$kinderland = TRUE;$kinderlandclass = "_aktiv";$kinderlandtime = uli_date($infra['built']);}
		if ($infra['type'] == "videowuerfel"){$videowuerfel = TRUE;$videowuerfelclass = "_aktiv";$videowuerfeltime = uli_date($infra['built']);}
		if ($infra['type'] == "soundsystem"){$soundsystem = TRUE;$soundsystemclass = "_aktiv";$soundsystemtime = uli_date($infra['built']);}
		if ($infra['type'] == "sbahn"){$sbahn = TRUE;$sbahnclass = "_aktiv";$sbahntime = uli_date($infra['built']);}
		if ($infra['type'] == "bushaltestelle"){$bushaltestelle = TRUE;$bushaltestelleclass = "_aktiv";$bushaltestelletime = uli_date($infra['built']);}
		if ($infra['type'] == "autobahn"){$autobahn = TRUE;$autobahnclass = "_aktiv";$autobahntime = uli_date($infra['built']);}
		if ($infra['type'] == "architektur"){$architektur = TRUE;$architekturclass = "_aktiv";$architekturtime = uli_date($infra['built']);}
		
	
	
	
		}
	}



?>
<div class = "infrastruktur">
		<div class = "infrabox bratwurst ulibox">
			<span>Du hast <?php echo $bratwurst; ?> Bratwurstbude<?php if ($bratwurst != 1){?>n<?php }?>. </span>
			<button class = "infrabutton" id = "bratwurst">Neuen Stand kaufen (500.000)</button> 		
		</div>
		<div class = "infrabox bier ulibox">
			<span>Du hast <?php echo $bier; ?> Bierbude<?php if ($bier != 1){?>n<?php }?>. </span>
			<button class = "infrabutton" id = "bier">Neuen Stand kaufen (500.000)</button> 	
		</div>

		<div class = "infrabox merchandising ulibox">
			<span>Du hast <?php echo $merchandising; ?> Merchandising-Shop<?php if ($merchandising != 1){?>s<?php }?>.</span> 
			<button class = "infrabutton" id = "merchandising">Neuen Shop kaufen (1 Mio)</button> 		
		</div>

		<div class = "infrabox fanprojekt<?php echo $fanprojektclass; ?> ulibox">
			<?php if ($fanprojekt) { ?>
				<span>Fanprojekt (seit <?php echo $fanprojekttime; ?>)</span>
			<?php } else { ?>
				<button class = "infrabutton" id = "fanprojekt">Fanprojekt (2 Mio)</button>
			<?php } ?>
		</div>
		
		
		<div class = "clear"></div>

		<div class = "infrabox museum<?php echo $museumclass; ?> ulibox">
			<?php if ($museum) { ?>
				<span>Museum (seit <?php echo $museumtime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "museum">Museum (2 Mio)</button>
			<?php } ?>
		</div>
		<div class = "infrabox kinderland<?php echo $kinderlandclass; ?> ulibox">
			<?php if ($kinderland) { ?>
				<span>Kinderland (seit <?php echo $kinderlandtime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "kinderland">Kinderland (1 Mio)</button>
			<?php } ?>
		</div>
		<div class = "infrabox videowuerfel<?php echo $videowuerfelclass; ?> ulibox">
			<?php if ($videowuerfel) { ?>
				<span>Videow&uuml;rfel (seit <?php echo $videowuerfeltime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "videowuerfel">Fetter Videow&uuml;rfel (5 Mio)</button>
			<?php } ?>
		</div>
		<div class = "infrabox soundsystem<?php echo $soundsystemclass; ?> ulibox">
			<?php if ($soundsystem) { ?>
				<span>Soundsystem (seit <?php echo $soundsystemtime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "soundsystem">Geiles Soundsystem (2,5 Mio)</button>
			<?php } ?>
		</div>
		<div class = "clear"></div>

		<div class = "infrabox sbahn<?php echo $sbahnclass; ?> ulibox">
			<?php if ($sbahn) { ?>
				<span>S-Bahn-Haltestelle (seit <?php echo $sbahntime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "sbahn">S-Bahn-Haltestelle (10 Mio)</button>
			<?php } ?>
		</div>
		<div class = "infrabox bushaltestelle<?php echo $bushaltestelleclass; ?> ulibox">
			<?php if ($bushaltestelle) { ?>
				<span>Bushaltestelle (seit <?php echo $bushaltestelletime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "bushaltestelle">Bushaltstelle (1 Mio)</button>
			<?php } ?>
		</div>
		<div class = "infrabox autobahn<?php echo $autobahnclass; ?> ulibox">
			<?php if ($autobahn) { ?>
				<span>Autobahnzubringer (seit <?php echo $autobahntime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "autobahn">Autobahnzubringer (25 Mio)</button>
			<?php } ?>
		</div>
		<div class = "infrabox architektur<?php echo $architekturclass; ?> ulibox">
			<?php if ($architektur) { ?>
				<span>Fancy Architektur (seit <?php echo $architekturtime; ?>)</span>
			
			<?php } else { ?>
				<button class = "infrabutton" id = "architektur">Fancy Architektur (10 Mio)</button>
			<?php } ?>
		</div>
	</div>
</div>


<?php 


/* Footer */
uli_footer();
?>
