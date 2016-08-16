<?php
require_once('../../wp-load.php' );
require_once(ABSPATH.'/uli/_mainlibs/setup.php');


/* Header */
$page = array("main" => "start", "sub" => "start");
uli_header();

echo "Teamranking: Teamranking wird angezeigt und berechnet";

calculate_team_ranking(1);


uli_footer();
?>