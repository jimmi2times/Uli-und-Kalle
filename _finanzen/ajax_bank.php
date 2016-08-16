<?php
require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');
include('lib_bank.php');

global $option;

// dynamisches Tab Laden der Matrix
if ($_REQUEST['action'] == "overview"){
	
echo get_overview_data($_REQUEST['year']);	
	
}