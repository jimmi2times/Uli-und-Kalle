<?php
require_once('../../wp-load.php' );
$_REQUEST['short'] = 1;
require_once(ABSPATH.'/uli/_mainlibs/setup.php');
include('lib_communication.php');

global $option;


// Nachricht "loeschen" (wobei hier nur auf 1 setzen in DB gemeint ist)
// Danach wird das ding ausgeblendet
if ($_POST['action'] == "delete"){
	$id = $_POST['id'];
	$cond[] = array("col" => "id", "value" => $id);
	$value[] = array ("col" => "del_receiver", "value" => "1");
	uli_update_record('messages', $cond, $value);
	$html .= '<script>';
	$html .= '$(".message-'.$id.'").hide();';
	$html .= '</script>';
	echo $html; 
}

if ($_POST['action'] == "reply"){
	$id = $_POST['id'];
	$message = get_message($id);
	$html = print_form_new_message($message);
	echo $html;
}

if ($_POST['action'] == "read"){
	$id = $_POST['id'];
	$cond[] = array("col" => "id", "value" => $id);
	$value[] = array ("col" => "view_receiver", "value" => "1");
	uli_update_record('messages', $cond, $value);
	//$html .= 'read';
	echo $html;
}


if ($_POST['action'] == "newmessage"){
	$html = print_form_new_message();
	echo $html;
}
?>