<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
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

define("KIMB_CMS", "Clean Request");

//Diese Datei ist Teil des Backends, sie ist für den Login zuständig!

//Konfiguration & Klassen & Funktionen laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Den Text definieren, welcher eingeloggen Usern angezeigt werden soll
$htmlcode_drin = '<h2>Home</h2>';
$htmlcode_drin .= 'Hallo '.$_SESSION['name'].',<br />Sie können jetzt Ihre Homepage bearbeiten!';
$htmlcode_drin .= '<br /><br />';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php"><span id="startbox"><b>Seiten</b><br /><span class="ui-icon ui-icon-document"></span><br /><i>Seiten erstellen, löschen, bearbeiten</i></span></a>';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php"><span id="startbox"><b>Menue</b><br /><span class="ui-icon ui-icon-newwin"></span><br /><i>Menüs erstellen, löschen, bearbeiten</i></span></a>';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php"><span id="startbox"><b>User</b><br /><span class="ui-icon ui-icon-person"></span><br /><i>Backenduser erstellen, löschen, bearbeiten</i></span></a>';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php"><span id="startbox"><b>Konfiguration</b><br /><span class="ui-icon ui-icon-gear"></span><br /><i>Systemkonfiguration anpassen</i></span></a>';

//Teil Logout

//Will sich der User ausloggen?
if($_GET['todo'] == 'logout'){

	//Die falschen Loginversuche sollen bleiben
	$loginfehler = $_SESSION["loginfehler"];
	//Session leeren
	session_unset();
	//Session zerstören
	session_destroy();
	//Session neu aufesetzen
	//	jetzt ist alles, was mit dem User zu tun hatte weg
	session_start();
	//Wie gesagt, die falschen Loginversuche bleiben 
	$_SESSION["loginfehler"] = $loginfehler;	

	//Hinweis, dass User ausgeloggt
	$sitecontent->echo_message('Sie wurden ausgeloggt!', 'Auf Wiedersehen');
}

//Teil Login

//Wurden Logindaten übergeben
if( !empty( $_POST['user'] ) && !empty( $_POST['pass'] ) ){

	//Prüfung der Daten
	//	Usernames dürfen nur a-z enthalten
	//		Kulanz gegenüber GROSS-Schreibung
	$_POST['user'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_POST['user'] ) );
	//	Hier wird nur ein SHA1 Hash des Passworts übergeben, also können hier auch nur Zeichen von A-Z,0-9,a-z vorhanden sein  
	$_POST['pass'] = preg_replace( "/[^A-Z0-9a-z]/" , "" , strtolower( $_POST['pass'] ) );

	//Hat der User schon falsche Loginversuche?, wenn nicht dann wohl 0
	if(  empty( $_SESSION["loginfehler"] ) ){
		$_SESSION["loginfehler"] = 0; 
	}

	//Die Userdatei laden
	$userfile = new KIMBdbf('/backend/users/list.kimb');
	
	//Den übergebenen Usernamen in $user packen
	$user = $_POST['user'];
	//Den Usernamen in der Datenbank suchen
	$userda = $userfile->search_kimb_xxxid( $user , 'user' );

	//Ist der User gefunden worden?
	//Und sind weniger als 7 falsche Loginversuche festgestellt worden?
	if($userda != false && $_SESSION["loginfehler"] <= 6 ){
		//Den übergebenen Hash in $passhash packen
 		$passhash = $_POST['pass'];
		 //Den Hash aus der Datenbank lesen und zum Prüfhash weiterverarbeiten.
		 //	Beim User (Browser) wird per JavaScript die Eingabe mit den Salt aus der Datenbank gehasht, das Salt
		 //	wird per AJAX geladen. Dieser Hash wird dann vor der Übergabe mit einem Zufallscode erneut gehasht, so 
		 //	ist jeder übergebene Passworthash nur ein Mal gültig.
		 //	Auf dem Server wird der erste Schritt übersprungen (das Passwort liegt "gesalted" in der Datenbank), 
		 //	das Hashing mit dem Zufallscode ($_SESSION["loginsalt"]) findet jedoch statt.
		 //	Bei einem richtigen Passwort stimmen die Hashes überein und der User kann eingeloggt werden.
		$passpruef = sha1($userfile->read_kimb_id( $userda , 'passw' ).$_SESSION["loginsalt"]);
	
		//stimmen die Hashes überein?
		if( $passhash == $passpruef ){
			//eingeloggt
			
			//Meldung
			$sitecontent->echo_message('Sie wurden eingeloggt!', 'Willkommen');
			//Text für eingeloggte User
			$sitecontent->add_site_content($htmlcode_drin);

			//Alles Wichtige in die Session schreiben
			//	Das $loginokay ist systemspezifisch und sorgt dafür, dass die Backends mehrerer CMS Installationen auf einer Domain
			//	nicht alle mit einem Backend-Login erreichbar sind.
			$_SESSION['loginokay'] = $allgsysconf['loginokay'];
			//	Name des Users für "Hallo User XXX" oben rechts im Backend
			$_SESSION['name'] = $userfile->read_kimb_id( $userda , 'name' );
			//	Rechtegruppe des Users
			$_SESSION['permission'] = $userfile->read_kimb_id( $userda , 'permiss' );
			//	Username
			$_SESSION['user'] = $userfile->read_kimb_id( $userda , 'user' );
			//	Die Session wird an die IP des Users gebunden
			$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
			//	Die Session wird außerdem an den User-Agent gebunden,
			//	so ist das übernehmen der Session selbst im gleichen Netzwerk schwer. 
			$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
			//Der Zufallscode für den Hash des Passworts ist nicht mehr nötig -> weg damit
			unset($_SESSION["loginsalt"]);
		}
		else{
			//Das Passwort ist falsch
			
			//einen falschen Logiversuch registrieren
			$_SESSION["loginfehler"]++;
			//Meldung an den User
			$sitecontent->echo_error( 'Sie haben die maximale Anzahl an Versuchen erreicht oder Ihr Username bzw. Passwort ist inkorrekt!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!' , 'unknown', 'Falsche Logindaten');
		}
		
	}
	else{
		//Der Username ist falsch oder die Anzahl an Versuchen ist zu hoch
		
		//einen falschen Logiversuch registrieren
		$_SESSION["loginfehler"]++;
		//Meldung an User
		$sitecontent->echo_error( 'Sie haben die maximale Anzahl an Versuchen erreicht oder Ihr Username bzw. Passwort ist inkorrekt!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!' , 'unknown', 'Falsche Logindaten');
		
		//=> Die Meldungen bei falschen Eingaben unterscheiden sich nicht, dies ist so gewollt um Brute-Forcing vorzubeugen		
	}
}
//Wurde das Loginformular leer abgesendet? 
//	-> Das soll so nicht sein!
elseif( ( empty( $_POST['user'] ) || empty( $_POST['pass'] ) ) && ( isset( $_POST['user'] ) || isset( $_POST['pass'] ) ) ){
	//Medlung
	$sitecontent->echo_error( 'Bitte füllen Sie die Felder!' );
}
//Ist der User eingeloggt?
elseif( $_SESSION['loginokay'] == $allgsysconf['loginokay'] ){
	//Hier reicht eine oberflächliche Prüfung, denn es können keine Änderungen durchgeführt werden, weder Konfigurationen gesehen werden.
	$sitecontent->echo_message('Sie sind eingeloggt!', 'Willkommen');
	$sitecontent->add_site_content($htmlcode_drin);
}

//Der User ist also nicht eingeloggt?
if( $_SESSION['loginokay'] != $allgsysconf['loginokay'] ){
	//Den Zufallscode für den Passworthash erstellen und in die Session legen
	$loginsalt = makepassw( 50, '', 'numaz' );
	$_SESSION["loginsalt"] = $loginsalt;

	//Um sicher zu gehen, dass nur Browser mit aktivierten JavaScript ein Login versuchen, das Formular per JavaScript dem DOM hinzufügen
	//Außderdem die JavaScript Funktion zur Erstellung des Passworthashes ausgeben
	//Und die Funktion zum laden des Salts
	//	Während der Dauer des AJAX-Requests wird der Login-Button auf "disabled" gesetzt und eine GIF Grafik dem User angezeigt
	$sitecontent->add_html_header('<script>
	$(function() {
		$("div#login").html( \'<table><tr><td>Username:</td><td><input type="text" id="user" name="user" onkeydown="if(event.keyCode == 13){ return false; }" onchange="getsalt();" autofocus="autofocus"></td><td><img src="'.$allgsysconf['siteurl'].'/load/system/spin_load.gif" title="Loading Userdata" style="display:none;" id="loadergif" width="20" title="spin_load.gif"></td></tr><tr><td>Passwort:</td><td><input type="password" name="pass" id="pass" onkeydown="if(event.keyCode == 13){ return true; }" maxlength="32"><br /></td><td></td></tr><tr><td><input type="submit" value="Login"></td><td><img src="'.$allgsysconf['siteurl'].'/load/system/spin_load.gif" title="Loading Userdata" style="display:none;" id="loadergif" width="20" title="spin_load.gif"></td></table>\' );
	});

	function submitsys() {
		var randsalt = "'.$loginsalt.'";
		var user = $( "input#user" ).val();
		var pass = $( "input#pass" ).val();

		if( pass == "" || user == "" ){
			return false;
		}

		var salt = $( "span#passsalt" ).text().toString();

		if( salt == "none" ){
			return true;
		}
		else if( salt != "nok" ){
		
			var newpass = SHA1( SHA1( salt + pass ) + randsalt );
			newpass = newpass.toString();
			$( "input#pass" ).val( newpass );

			return true;
		}
		else{
			return true;
		}
	}

	function getsalt(){
		var user = $( "input#user" ).val();

		$( "input[type=submit]" ).attr( "disabled", "disabled" );
		$( "img#loadergif" ).css( "display", "block" );
		$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=index.php&user=" + user , function( data ) {
			$( "span#passsalt" ).text( data );
			$( "img#loadergif" ).css( "display", "none" );
			$( "input[type=submit]" ).removeAttr( "disabled" );
		});	
	}
	</script>');

	//Das Grunfgerüst des Loginformulars ausgeben
	//	Ohne JavaScript erscheint nur ein Hinweis auf die Notwendigkeit JavaScript zu aktivieren
	$sitecontent->add_site_content( '<h2>Login</h2>' );
	$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/index.php" method="post" onsubmit="return submitsys();" >' );
	$sitecontent->add_site_content( '<div id="login"> <div class="ui-widget" style="position: relative;"><div class="ui-state-highlight ui-corner-all" style="padding:10px;">');
	$sitecontent->add_site_content( '<span class="ui-icon ui-icon-info" style="position:absolute; left:20px; top:7px;"></span>');
	$sitecontent->add_site_content( '<h1>Diese Seite benötigt JavaScript!!</h1>');
	$sitecontent->add_site_content( '</div></div></div>' );
	$sitecontent->add_site_content( '</form><span id="passsalt" style="display:none;" >none</span><br /><br />' );
}
//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
