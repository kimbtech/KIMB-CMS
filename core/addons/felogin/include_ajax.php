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

//User schon vergeben Abfrage
if(  isset( $_GET['user'] ) ){ 

	//Userdatei lesen
	$userfile = new KIMBdbf('addon/felogin__user.kimb');

	//Username säubern
	$_GET['user'] = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $_GET['user'] ) );

	//nach User suchen
	$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );

	//OK oder nicht OK antworten
	if( $id == false ){
		echo 'ok';
	}
	else {
		echo 'nok';
	}

	die;
}
//Captcha Code prüfen
elseif(  isset( $_GET['captcha_code'] ) ){ 

	//OK oder nicht OK antworten
	if( check_captcha() ){
		echo 'ok';
	}
	else {
		echo 'nok';
	}

	die;
}
//E-Mail-Adresse validieren
elseif(  isset( $_GET['mail'] ) ){

	//E-Mail-Adresse von der Syntax her valide?
	//Sprache für E-Mail Inhalt gewählt?
	//Captcha Code gesetzt (nur wenn Captcha angezeigt, will User auch Account erstellen und nur dann hat er einen Grund E-Mail-Adressen zu prüfen)
	//	ODER
	//Username gesetzt (nur wenn eingeloggt, hat er einen Grund seine E-Mail-Adresse zu ändern)
	if( filter_var( $_GET['mail'] , FILTER_VALIDATE_EMAIL) && ( $_GET['lang'] == 'de' ||  $_GET['lang'] == 'en' ) && ( !empty( $_SESSION['captcha_code'] ) || !empty( $_SESSION['felogin']['user'] ) ) ){
		//alles okay!
		
		//Die Anzahl an zu versenden E-Mails pro User (IP) sind beschränkt, sonst wäre diese Funktion das beste Tool für Spamming
		
		//Datei mit Mail-Log laden
		$aufruffile = new KIMBdbf( 'addon/felogin__mailaufr.kimb' );
		//nach IP des Users suchen
		$ip = $aufruffile->search_kimb_xxxid( $_SERVER['REMOTE_ADDR'] , 'ip' );

		//IP gefunden?
		if( $ip != false ){
			//Zeitpunkt der letzten Mail lesen
			$uhr = $aufruffile->read_kimb_id( $ip , 'uhr' );
			
			//Anzahl der Mails größer als 4 und letzte Mail nicht länger als 1 Tag her
			if( $aufruffile->read_kimb_id( $ip , 'time' ) >= '4' && time() - $uhr <= '84400' ){
				//soviele Versuche sind nicht erlaubt!
				echo 'nok';
				die;
			}
			$id = $ip;

			//also nicht zu viele Versuche oder schon lange her
			
			//wenn der Wert alt ist, kann er weg
			if( time() - $uhr > '84400' ){
				$aufruffile->write_kimb_id( $id , 'del' );
			}
		}
		//IP nicht gefunden
		else{
			//neue ID herausfinden
			$id = $aufruffile->next_kimb_id();
		}

		//Code für E-Mail erstellen
		$code = makepassw( 75, '', 'numaz' );
		//Code und E-Mail-Adresse in Session speichern
		$_SESSION["mailcode"] = $code;
		$_SESSION["email"] = $_GET['mail'];
		
		//Ini Datei für Text der Mail laden (de oder en)
		$mailtext = parse_ini_file ( __DIR__.'/lang_'.$_GET['lang'].'.ini' , true );
		//Vorlage für Text laden
		$mailtext = $mailtext['mailtext']['code'];
		
		//Die Vorlage enthält Platzhalter, diese müssen ersetzt werden
		$s = array( '%sitename%' , '%br%', '%code%' );
		$r = array( $allgsysconf['sitename'], "\r\n", $code );
		$inhalt = str_replace( $s , $r , $mailtext );
		
		//Mail senden
		if( send_mail( $_GET['mail'] , $inhalt ) ){

			//OK
			echo 'ok';

			//noch keine Aufrufe in der dbf
			if( !$aufruffile->read_kimb_id( $id , 'time' ) ){
				//Anzahl der Mails also 1
				$aufruffile->write_kimb_id( $id , 'add' , 'time' , 1 );
			}
			else{
				//Anzahl um einen erhöhen
				$time = $aufruffile->read_kimb_id( $id , 'time' ) + 1;
				$aufruffile->write_kimb_id( $id , 'add' , 'time' , $time );
			}
			//IP und Zeitpunkt setzen
			$aufruffile->write_kimb_id( $id , 'add' , 'ip' , $_SERVER['REMOTE_ADDR'] );
			$aufruffile->write_kimb_id( $id , 'add' , 'uhr' , time() );
		}
		//Fehler beim Mailversand
		else{
			echo 'nok';
		}
	}
	//Fehlerhafter Aufruf
	else{
		echo 'nok';
	}

	die;
}
//neues Passwort von "Passwort vergessen?" aktivieren
elseif( is_numeric( $_GET['newpassak'] ) && !empty($_GET['code']) ){
	
	//Übergaben schöner
	//	ID des Users
	$id = $_GET['newpassak'];
	//	Code um neues Passwort zu aktiveren
	$code = $_GET['code'];
	
	//Userdatei lesen
	$userfile = new KIMBdbf('addon/felogin__user.kimb');
	
	//Ist der Code korrekt?
	if( $userfile->read_kimb_id( $id , 'setnewcode' ) == $code ){

		//neues Passwort lesen (hier schon sha1(); )
		$newpass = $userfile->read_kimb_id( $id , 'newpassw' );
		//neues Passwort zu Passwort machen
		$userfile->write_kimb_id( $id , 'add' , 'passw' , $newpass );

		//Werte für neues Passowrt und Code aus dbf löschen
		$userfile->write_kimb_id( $id , 'del', 'newpassw' );
		$userfile->write_kimb_id( $id , 'del', 'setnewcode' );

		//normale Homepage aufrufen
		open_url( '' );
	}
	else{
		//Fehlermeldung wenn Code falsch
		echo 'Fehlerhafter Code oder User';		
	}
	die;	
}
//Mailcode testen
elseif(  isset( $_GET['code'] ) ){
	
	//Test und OK/ Not OK	
	if( $_GET['code'] == $_SESSION['mailcode'] ){
		echo 'ok';
	}
	else {
		echo 'nok';
	}

	die;
}
//neuen User hinzufügen (aus Backend)
elseif( isset( $_GET['newuser'] ) ){
	//Rechte prüfen
	check_backend_login( 'thirteen' );

	//dbf laden
	$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
	//RequestID für Loginsachen lesen 
	$felogin['requid'] = $felogin['conf']->read_kimb_one( 'requid' );

	//per Session dem User das registrieren neuer User erlauben
	$_SESSION['registerokay'] = 'yes';

	//Seite dafür öffnen
	open_url( '/index.php?id='.$felogin['requid'].'&langid=0&register' );
	die;

}
//User bearbeiten (aus Backend)
elseif( isset( $_GET['settings'] ) ){
	//Rechte prüfen
	check_backend_login( 'thirteen' );

	//dbf laden
	$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
	//RequestID für Loginsachen lesen
	$felogin['requid'] = $felogin['conf']->read_kimb_one( 'requid' );

	//Loginokay in Session setzen
	$_SESSION['felogin']['loginokay'] = $felogin['conf']->read_kimb_one( 'loginokay' );
	//Username in Session setzen
	$_SESSION['felogin']['user'] = $_GET['settings'];
	
	//User ändern für Admins
	$_SESSION['felogin_uch_okay'] = 'yes';

	//Namen des Admin (nur zur Schönheit)
	$_SESSION['felogin']['name'] = 'BE-Admin';

	//Seite zum User barbeiten öffnen
	open_url( '/index.php?id='.$felogin['requid'].'&langid=0&settings' );
	die;

}
	
?>
