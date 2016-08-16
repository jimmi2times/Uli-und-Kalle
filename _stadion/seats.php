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
$page = array("main" => "stadion", "sub" => "seats");
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

//Angebot annehmen
$('.stadium a').live('click', function() {

	//alert(this.id);
	var returnBox = $(".baustelle");
	$.ajax({
		type: "POST", url: "ajax_seats.php", data: "action=seats&block="+this.id,
		complete: function(data){
			returnBox.html(data.responseText);
		}
	 });	
	 return false;
	});


$('.baustelle button').live('click', function() {

	//alert(this.id);
	var returnBox = $(".baustelle");
	$.ajax({
		type: "POST", url: "ajax_seats.php", data: "action=build&what="+this.id,
		complete: function(data){
			returnBox.html(data.responseText);
		}
	 });	
	 return false;

});



</script>




<style type = "text/css">

.stadium {
width: 650px;
float: left;
}

.bauabteilung {
	width: 280px;
	float: left;
	margin-top: 25px;
}

.blockB .tribune {
	border-top: 30px solid #000;
	border-left: 25px solid transparent;
	border-right: 25px solid transparent;
	margin-top: 2px;
	opacity: 0.4;
}	

.blockB .tribune:hover {
	border-top: 30px solid #7fbe47;
	border-left: 25px solid transparent;
	border-right: 25px solid transparent;
}

.blockA .tribune {
	border-bottom: 30px solid #000;
	border-left: 25px solid transparent;
	border-right: 25px solid transparent;
	margin-top: 2px;
	opacity: 0.4;
}	

.blockA .tribune:hover {
	border-bottom: 30px solid #7fbe47;
	border-left: 25px solid transparent;
	border-right: 25px solid transparent;

}

.blockD .tribune {
	border-left: 24px solid #000;
	border-bottom: 29px solid transparent;
	border-top: 29px solid transparent;
	float: left;
	margin-left: 2px;
	opacity: 0.4;
}	

.blockD .tribune:hover {
	border-left: 24px solid #7fbe47;
	border-bottom: 29px solid transparent;
	border-top: 29px solid transparent;
}	

.blockD {
	margin-top: -125px;
	margin-left: 22px
}

.blockC .tribune {
	border-right: 24px solid #000;
	border-bottom: 29px solid transparent;
	border-top: 29px solid transparent;
	float: left;
	margin-left: 2px;
	opacity: 0.4;
}	

.blockC .tribune:hover {
	border-right: 24px solid #7fbe47;
	border-bottom: 29px solid transparent;
	border-top: 29px solid transparent;
}	


.blockC {
	margin-top: -400px;
	margin-left: 522px
}


.D4, .C4 {
	height: 405px;
	margin-top: 2px;
}

.D3, .C3 {
	height: 340px;
	margin-top: 35px;
}

.D2, .C2 {
	height: 277px;
	margin-top: 67px;
}

.D1, .C1 {
	height: 213px;
	margin-top: 98px;
}



.B4, .A4 {
	width: 550px;
}

.B3, .A3 {
	width: 498px;
	margin-left: 26px;
}

.B2, .A2 {
	width: 446px;
	margin-left: 52px;
}

.B1, .A1 {
	width: 394px;
	margin-left: 78px;
}


.blockB {
	width: 700px;
	margin-top: 25px;
	margin-left: 25px;
}

.blockA {
	width: 700px;
	margin-top: 405px;
	margin-left: 25px;
}

.field {
	width: 390px;
	height: 200px;
	background: url('../theme/graphics/stadium/stadion_feld.png');
	margin-left: 130px;
	margin-top: 135px;
	
}

.baustelle button {
	width: 80px;
}


</style>

<?php 

$stadium = get_stadium($option['uliID']);

//print_r($stadium);

if ($stadium){
	if ($stadium['seats']){
		$seats = $stadium['seats'];
			// colors
			echo '<style type = "text/css">';
			foreach ($seats as $seat){
								
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
					$sitzplaetze = $sitzplaetze + $seat['seats'];
				}
				if ($seat['type_of_seats'] == 2){
					$color = '#0c1969';
					$stehplaetze = $stehplaetze + $seat['seats'];
					
				}
				
				echo '.stadium .tribune-'.$seat['block'].' {'.$border.': '.$color.'; opacity: 1;}';	
				
			
			}
			echo '</style>';
	
	}	

}



// print_r($stadium);

?>



<div class = "stadium">
	<div class = "blockB">
		<a id = "B4" href="#"><div class = "tribune B4 tribune-B4"></div></a>
		<a id = "B3" href="#"><div class = "tribune B3 tribune-B3"></div></a>
		<a id = "B2" href="#"><div class = "tribune B2 tribune-B2"></div></a>
		<a id = "B1" href="#"><div class = "tribune B1 tribune-B1"></div></a>
	</div>

	<div class = "blockD">
		<a id = "D4" href="#"><div class = "tribune D4 tribune-D4"></div></a>
		<a id = "D3" href="#"><div class = "tribune D3 tribune-D3"></div></a>
		<a id = "D2" href="#"><div class = "tribune D2 tribune-D2"></div></a>
		<a id = "D1" href="#"><div class = "tribune D1 tribune-D1"></div></a>
	</div>


	<div class ="field">
	</div>

	<div class = "blockC">
		<a id = "C1" href="#"><div class = "tribune C1 tribune-C1"></div></a>
		<a id = "C2" href="#"><div class = "tribune C2 tribune-C2"></div></a>
		<a id = "C3" href="#"><div class = "tribune C3 tribune-C3"></div></a>
		<a id = "C4" href="#"><div class = "tribune C4 tribune-C4"></div></a>
	</div>

	<div class = "blockA">

		<a id = "A1" href="#"><div class = "tribune A1 tribune-A1"></div></a>
		<a id = "A2" href="#"><div class = "tribune A2 tribune-A2"></div></a>
		<a id = "A3" href="#"><div class = "tribune A3 tribune-A3"></div></a>
		<a id = "A4" href="#"><div class = "tribune A4 tribune-A4"></div></a>
	
	</div>


</div>

<div class = "bauabteilung">
<?php 
echo uli_box('Dein Stadion', '<p>Dein Stadion hat im Moment '.number_format($sitzplaetze + $stehplaetze, 0, '','.').' Pl&auml;tze. Klicke auf die Bl&ouml;cke um Details zu erfahren oder um die Bagger loszuschicken.</p>'); 

?>
	<div class = "baustelle">
		
	</div>


</div>

<?php 


/* Footer */
uli_footer();
?>
