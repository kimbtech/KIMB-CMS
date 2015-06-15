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

//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//BE-Login

$htmlcode_drin = '<h2>Willkommen!</h2>';
$htmlcode_drin .= 'Hallo '.$_SESSION['name'].',<br />Sie können jetzt Ihre Homepage bearbeiten!';
$htmlcode_drin .= '<br /><br />';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php"><span id="startbox"><b>Seiten</b><br /><span class="ui-icon ui-icon-document"></span><br /><i>Seiten erstellen, löschen, bearbeiten</i></span></a>';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php"><span id="startbox"><b>Menue</b><br /><span class="ui-icon ui-icon-newwin"></span><br /><i>Menüs erstellen, löschen, bearbeiten</i></span></a>';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php"><span id="startbox"><b>User</b><br /><span class="ui-icon ui-icon-person"></span><br /><i>Backenduser erstellen, löschen, bearbeiten</i></span></a>';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php"><span id="startbox"><b>Konfiguration</b><br /><span class="ui-icon ui-icon-gear"></span><br /><i>Systemkonfiguration anpassen</i></span></a>';

if($_GET['todo'] == 'logout'){

	$loginfehler = $_SESSION["loginfehler"];
	session_unset();
	session_destroy();
	session_start();
	$_SESSION["loginfehler"] = $loginfehler;	

	$sitecontent->echo_message('Sie wurden ausgeloggt!');
}

//Loginteil

if( !empty( $_POST['user'] ) && !empty( $_POST['pass'] ) ){

	$_POST['user'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_POST['user'] ) );
	$_POST['pass'] = preg_replace( "/[^A-Z0-9a-z]/" , "" , strtolower( $_POST['pass'] ) );

	if($_SESSION["loginfehler"] == ''){ $_SESSION["loginfehler"] = 0; }

	$userfile = new KIMBdbf('/backend/users/list.kimb');
		
	$user = $_POST['user'];
	$userda = $userfile->search_kimb_xxxid( $user , 'user' );

	if($userda != false && $_SESSION["loginfehler"] <= 6 ){
 		$passhash = $_POST['pass'];
		$passpruef = sha1($userfile->read_kimb_id( $userda , 'passw' ).$_SESSION["loginsalt"]);
	
		if( $passhash == $passpruef ){
			//eingeloggt
			$sitecontent->echo_message('Sie wurden eingeloggt!');
			$sitecontent->add_site_content($htmlcode_drin);

			$_SESSION['loginokay'] = $allgsysconf['loginokay'];
			$_SESSION['name'] = $userfile->read_kimb_id( $userda , 'name' );
			$_SESSION['permission'] = $userfile->read_kimb_id( $userda , 'permiss' );
			$_SESSION['user'] = $userfile->read_kimb_id( $userda , 'user' );
			$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
			$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
			unset($_SESSION["loginsalt"]);
		}
		else{
			$_SESSION["loginfehler"]++;
			$sitecontent->echo_error( 'Ihr Passwort ist falsch!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!' , 'unknown');
		}
		
	}
	else{
		$_SESSION["loginfehler"]++;
		$sitecontent->echo_error( 'Sie haben die maximale Anzahl an Versuchen erreicht oder Ihr Username ist inkorrekt!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!' , 'unknown');		
	}
}
elseif( ( empty( $_POST['user'] ) || empty( $_POST['pass'] ) ) && ( isset( $_POST['user'] ) || isset( $_POST['pass'] ) ) ){
	$sitecontent->echo_error( 'Bitte füllen Sie die Felder!' );
} 
elseif( $_SESSION['loginokay'] == $allgsysconf['loginokay'] ){
	$sitecontent->echo_message('Sie sind eingeloggt!');
	$sitecontent->add_site_content($htmlcode_drin);
}
if( $_SESSION['loginokay'] != $allgsysconf['loginokay'] ){
	$loginsalt = makepassw( 50, '', 'numaz' );
	$_SESSION["loginsalt"] = $loginsalt;

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
