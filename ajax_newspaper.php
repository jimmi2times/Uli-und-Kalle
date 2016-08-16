<?php

// fuer die ganzen ajax files muss irgendeine routine gebaut werden, die die ganzen libs einliest.
// diese variante ist etwas ruppig, weil bei jedem kleinen request die ganze routine (checks, etc.) durchlaufen wird.

require_once('../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');


global $option;

// Angebot akzeptieren
if ($_POST['action'] == "wholenewspaper"){
		echo '<div class="jour-header"></div>';
		// Holt alle News
		$news = get_news(9999999);
		
		if ($news){
			foreach($news as $aufmacher){
			// Das ist der Aufmacher
			echo '<div class="jour-article">';
				echo '<div class="jour-pic">';
				echo get_player_pic($aufmacher['playerID']);
				echo '</div>';
				echo '<div class="jour-text">';
				echo '<h3 class="jour-headline">';
				echo $aufmacher['headline'];
				echo '</h3>';
				echo uli_date($aufmacher['timestamp']).' | ';
				echo $aufmacher['text'];
				echo '</div>';
			echo '</div>';
			echo '<div class="clearer"></div>';		
			}
		}
}




?>