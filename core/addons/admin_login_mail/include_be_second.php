<?php
/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies
/*************************************************/
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License version 3
//published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.
/*************************************************/
//www.KIMB-technologies.eu
//www.bitbucket.org/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//schon Anzahl der falschen Versuche vorhanden
if( !isset( $_SESSION['admin_login_mail']['datengesanz'] ) ){
	//wenn nicht, dann auf null setzen
	$_SESSION['admin_login_mail']['datengesanz'] = 0;
}

//Username und Passwort gegeben?
if( isset($_POST['user']) && isset($_POST['pass']) ){
	//nicht eingeloggt (diese Prüfung findet nach dem Login statt, wenn User eingeloggt hier false)
	if( $_SESSION['loginokay'] != $allgsysconf['loginokay'] ){
		//falsche Loginversuche um einen erhöhen
		$_SESSION['admin_login_mail']['datengesanz']++;
	}
}

//schon 3 falsche Loginversuchen?
if( $_SESSION['admin_login_mail']['datengesanz'] == 3 ){
		
	//Text für Mail an Admin erstellen
	$text = 'Hallo Admin,'."\n\r".'es wurden am Backend gerade 3 fehlerhafte Loginversuche festgestellt!'."\n\r\n\r";
	$text .= 'Zeit:'.date("d.m.Y \u\m H:i:s")."\n\r".'IP:'.$_SERVER['REMOTE_ADDR']."\n\r".'Useragent: '.$_SERVER['HTTP_USER_AGENT']."\n\r";

	//Mail absenden
	send_mail( $allgsysconf['adminmail'] , $text );

	//jetzt wieder die falschen Versuche von vorne zählen
	$_SESSION['admin_login_mail']['datengesanz'] = 0;
}

//User eingeloggt und Daten übergeben (nur beim ersten Aufruf der index.php Mail senden!)
if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && isset($_POST['user']) && isset($_POST['pass']) ){

	//Text für Mail vorbereiten
	$text = 'Hallo '.$_SESSION['name'].','."\n\r".'Ihre Logindaten wurden gerade fuer ein Login im Backend verwendet!'."\n\r\n\r";
	$text .= 'Zeit:'.date("d.m.Y \u\m H:i:s")."\n\r".'IP:'.$_SERVER['REMOTE_ADDR']."\n\r".'Useragent: '.$_SERVER['HTTP_USER_AGENT']."\n\r";

	//Userdatei laden, wenn nicht schon getan
	if( !is_object( $userfile ) ){
		$userfile = new KIMBdbf('backend/users/list.kimb');
	}

	//ID des Users herausfinden
	$id = $userfile->search_kimb_xxxid( $_SESSION['user'] , 'user' );		
	if( $id != false ){
		//mit der ID die E-Mail Adresse auslesen
		$maila = $userfile->read_kimb_id( $id , 'mail' );
		//Mail senden
		send_mail( $maila , $text );
	}
}

?>
