<?php
require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');

global $option;


if ($_POST['action'] == "publish"){
	$draftid = $_POST['draftid'];
	$headline = $_POST['headline'];
	$text = $_POST['text'];
	$cond[] = array("col" => "ID", "value" => $draftid);
	$values[] = array("col" => "text", "value" => $text);
	$values[] = array("col" => "headline", "value" => $headline); 	
	$values[] = array("col" => "status", "value" => 1);
	if (uli_update_record('journal_articles', $cond, $values)) {
		echo Published;
	}
}


if ($_POST['action'] == "delete"){
	$draftid = $_POST['draftid'];
	$cond[] = array("col" => "ID", "value" => $draftid);
	if (uli_delete_record('journal_articles', $cond)) {
		echo Deleted;
	}
}
?>