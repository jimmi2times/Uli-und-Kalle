<?php
/*
 * Checkt ob ein User eingeloggt ist
 * spaeter vielleicht auch das Anmelden bzw. eine Hilfeseite
 */

//echo get_currentuserinfo();


if (!is_user_logged_in()) {
	wp_redirect(get_option('home').'/wp-login.php?redirect_to='.get_option('home').'/uli');
	}

global $user_ID;
$option['userID'] 		= $user_ID;
$uli					= get_uli_userID($user_ID);
$option['uliID']		= $uli['ID'];
	

if (!$option['uliID'] AND !$_REQUEST['register'] == "now"){
	wp_redirect(get_option('home').'/uli/register.php?register=now');
}

	
?>