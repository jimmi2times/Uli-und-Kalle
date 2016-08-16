<?php
/*
 * Created on 05.01.2008
 *
 * Library fuer Communication
 *
 *
 */

/* Bindet die Sprachdatei ein */
include('lang_start.php');

/*
 * zeigt den Posteingang an
 */
function print_mailbox($uliID, $view = ''){
	global $option;

	$messages = get_messages($uliID, $view);


	// Es werden alle Ulinamen eingelesen
	$uliname = get_all_uli_names($option['leagueID']);
	$uliname[0] = SystemMessage;
	
	if ($messages){
		foreach ($messages as $message){
			$style = '';
			if ($message['view_receiver'] == 0 AND !$view){
				$style = 'unread';
			}

			$html .= '<div class="message '.$style.' message-'.$message['ID'].'" id="'.$message['ID'].'">';
			$html .= '<div class="head">';
			$html .= date("d.m.y",$message['time']);
			$html .= ' | ';

			if (!$view){
				$html .= $uliname[$message['sender']];
			}
			if ($view == "sent"){
				$html .= To.": ".$uliname[$message['receiver']];
			}

			$html .= ' | ';
			$html .= $message['subject'];
			$html .= '</div>';
			$html .= '<div class="messagecontent" id="messagecontent-'.$message['ID'].'">';
			$html .= '<div class="text">';
			$html .= $message['text'];
			$html .= '</div>';
			$html .= '<div class="func">';

			if (!$view){
				$html .= '<div class="reply" id="'.$message['ID'].'"><a href="#">'.Reply.'</a></div>';
			}
			$html .= '<div class="delete" id="'.$message['ID'].'"><a href="#">'.Delete.'</a></div>';

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';


		}
	}
	else {
		//uli_tabelle_content(3,array('','Keine Nachrichten',''));
		// Keine Nachrichten
	}
	//uli_tabelle_end();


	return $html;
}


/*
 * holt alle (nicht geloeschten nachrichten eines Ulis)
 * 22.07.2011
 */
function get_messages($uliID, $view){
	if (!$view){
		$cond[] = array("col" => "receiver", "value" => $uliID);
		$cond[] = array("col" => "del_receiver", "value" => "1", "func" => "!=");
	}
	if ($view == "sent"){
		$cond[] = array("col" => "sender", "value" => $uliID);
		$cond[] = array("col" => "del_sender", "value" => "1", "func" => "!=");
	}
	$order[] = array("col" => "time", "sort" => "DESC");
	$result = uli_get_results('messages', $cond, NULL, $order);
	if ($result){return $result;}
	else {return FALSE;}
}


/*
 * holt message nach ID
 *
 * 22.07.2011
 */
function get_message($ID){
	$cond[] = array("col" => "ID", "value" => $ID);
	$result = uli_get_row('messages', $cond);
	if ($result){return $result;}
	else {return FALSE;}
}





function print_form_new_message($message = NULL){
	global $option;
	$uliID = $option['uliID'];
	$leagueID = $option['leagueID'];
	$league_members = get_ulis($leagueID);

	// Wenn vorhanden, die alte Message auswerten
	if ($message){
		$text = "//".OldMessage.": ".uli_date($message['time'])."\n".str_replace("<br />", "\n", $message['text']);
		$subject = "Re: ".$message['subject'];
	}

	$html 	.=	'<form action = "?action=send" method = "POST">';
	$html	.=	'<select name="receiver">';
	$html 	.=	'<option value="">Empf&auml;nger ausw&auml;hlen</option>';
	$html 	.=	'<option value="1">Rundschreiben</option>';

	if ($league_members){
		foreach ($league_members as $league_member){
			$html 	.=	'<option value='.$league_member['ID'].' ';
			if ($message['sender'] == $league_member['ID']){$html .= 'selected = "selected"';}
			$html 	.=	'>';
			$html	.=	$league_member['uliname'];
			$html	.=	'</option>';
		}}
		$html	.=	'</select>';
		$html	.=	'<br/>';
		$html	.=	'<br/>';
		$html	.=	'<input size = 38 type = "text" name = "subject" value = "'.$subject.'">';
		$html	.=	'<br/>';
		$html	.=	'<br/>';
		$html	.=	'<textarea cols = 40 rows = 5 name="text">'.$text.'</textarea>';

		$html .= '<input type="hidden" name = "sender" value="'.$uliID.'">';
		$html .= '<input type="submit" value="Nachricht senden">';
		$html .='</form>';


		return $html;

}

?>
