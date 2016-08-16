<?php

/*
 * schreibt eine message in die datenbank
 * 22.07.2011
 *
 */
function write_message($message){
	foreach ($message as $key => $value){
		$values[] = array("col" => $key, "value" => $value);
	}
	if ($message['ID']){
		$cond[] = array("col" => "ID", "value" => $form['ID']);
		$ID = uli_update_record('messages', $cond, $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
	else {
		$ID = uli_insert_record('messages', $values);
		if ($ID){return $ID;} else {return FALSE;}
	}
}



/*
 * holt alle ungelesenen messages
 * 22.07.2011
 */
function get_new_messages($uliID){
	$cond[] = array("col" => "del_receiver", "value" => "1", "func" => "!=");
	$cond[] = array("col" => "view_receiver", "value" => "1", "func" => "!=");
	$cond[] = array("col" => "receiver", "value" => $uliID);
	$order[] = array("col" => "time", "sort" => "DESC");
	$result = uli_get_results('messages', $cond, NULL, $order);
	if ($result){return $result;}
	else {return FALSE;}
}

?>