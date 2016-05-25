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

//Fehler, Inhalt, Codierung
error_reporting( 0 );
header('Content-Type: text/html; charset=utf-8');
define("KIMB_CMS", "Clean Request");

//Klassen laden
require_once __DIR__ . '/core/oop/all_oop.php';
//Funktionen laden
require_once __DIR__ . '/core/conf/funktionen.php';

//Nur mit conf-enable Datei den Konfigurator erlauben, sonst unerlaubte Konfiguration möglich
if( !file_exists ('conf-enable') ){
	//User bitten den Konfigurator zu aktivieren
	echo('<!DOCTYPE html>
<html>
	<head>
		<title>KIMB-CMS - Installation</title>
		<link rel="shortcut icon" href="load/system/KIMB.ico" type="image/x-icon; charset=binary">
		<link rel="icon" href="load/system/KIMB.ico" type="image/x-icon; charset=binary">
	</head>
	<body>
		<h1>Error - 403</h1>
		Bitte schalten Sie den Konfigurator frei,
		erstellen Sie eine leere "conf-enable" Datei
		im CMS-Root-Verzeichnis.
	</body>
</html>');
	die;
}

//HTML des Konfigurators 
//inkl. Warnung per JS wenn bestimmte Verzeichnisse aufrufbar!
echo('
<!DOCTYPE HTML >
<html>
	<head>
		<title>KIMB-CMS - Installation</title>
		<link rel="shortcut icon" href="load/system/KIMB.ico" type="image/x-icon; charset=binary">
		<link rel="icon" href="load/system/KIMB.ico" type="image/x-icon; charset=binary">
		<link rel="stylesheet" href="load/system/theme/fonts.min.css" type="text/css">
		<style>
			body { 
				background-color:#999999; 
				font-family: Ubuntu, sans-serif;
				color:#000000;
			}
			input[readonly=readonly]{
				background-color:#ccc;
				border-radius:4px;
				padding:2px;
			}
			input, button{
				font-family: Ubuntu,sans-serif;
				font-size:14px;
				color:#000000;
			}
			button[disabled=disabled]{
				color:#cccccc;
			}
			#main {
			  	width:800px;
				margin:auto;
				text-align:left;
			  	background-color:#ffffff;
				border: 5px solid #55dd77;
				border-radius:20px;
				padding:20px;
			}
			.wichtig{
				background-color:#ff0000;
				color:#ffffff;
				border-radius:10px;
				padding:30px;
				border:solid 2px orange;

			}
			ul{
				list-style-type:none;
			}
			ul li{
				padding: 5px;
				margin:5px;
				border-radius:15px;
			}
			.err{
				background-color:red;
			}
			.war{
				background-color:orange;
			}
			.okay{
				background-color:lightgreen;
			}
		</style>
		<script language="javascript" src="load/system/jquery/jquery.min.js"></script>
		<script language="javascript" src="load/system/hash.js"></script>
		<script>
			$(function() {
				var inhaltfile = "No clean Request";

				$.get( "core/conf/funktionen.php", function( data ) {
					if( data == inhaltfile){
						$( "div.wichtig" ).css( "display" , "block" );
					}
				});
			});
			
			function submit_mainform(){
				
				var salt = $( "input#salt" ).val();
				
				var passw1 = $( "input#passw1" ).val();
				var passw2 = $( "input#passw2" ).val();

				if( passw1 != passw2 ){
					alert( "Bitte geben Sie zwei identische Passwörter an!" );
					return false;
				}
				
				if( passw1 == "" || passw2 == "" ){
					alert( "Das Passwort darf nicht leer sein!" );
					return false;
				}
				
				var hash = CryptoJS.SHA1( salt + passw1 ).toString();
				
				$( "input#passw1" ).val( "123456" );
				$( "input#passw1" ).val( "123456" );
				
				$( "input#pass" ).val( hash );
			
				return true;
			}
			
			function nouserbutt() {
				if( $( "input#nouser" ).prop("checked") ){
					$( "div#nouser" ).hide();
				}
				else{
					$( "div#nouser" ).show();
				}
			}
		</script>
	</head>
	<body>

		<div id="main">
			<h1 style="border-bottom:5px solid #55dd77;">KIMB-CMS - Installation</h1>
			<div style="display:none;" class="wichtig" >
				<b>Achtung:</b>
				<br />
				Das Verzeichnis /core/ ist nicht gesch&uuml;tzt!
				<br />
				Bitte sperren Sie dieses Verzeichnis f&uuml;r jegliche Browseraufrufe!
			</div>
			<br />
');

//Ganz unten bei else{} gehts los!

if($_GET['step'] == '2'){

	//Zufallsgenerator Passwortsalt
	$output = makepassw( 10, '', 'numaz' );

	//Formular für die Konfiguration
	echo "\r\n\t\t\t".'<form method="post" action="configurator.php?step=3" onsubmit="return submit_mainform();" >';
	
	echo "\r\n\t\t\t".'<div id="nouser">';
	
	//	Pers
	echo "\r\n\t\t\t".'<h2>Personalisierung</h2>';
	echo "\r\n\t\t\t\t".'<input type="text" name="sitename" value="KIMB-CMS" size="60"><br /><small style="padding-left:5em;">(Name der Seite)</small><br /><br />';
	echo "\r\n\t\t\t\t".'<input type="text" name="metades" value="CMS von KIMB-technologies" size="60"><br /><small style="padding-left:5em;">(Meta Seitenbeschreibung)</small><br /><br />';
	echo "\r\n\t\t\t\t".'<input type="text" name="sysadminmail" value="webmaster@'.$_SERVER['HTTP_HOST'].'" size="60"><br /><small style="padding-left:5em;">(E-Mail Adresse des Systemadministrators)</small><br /><br />';
	
	//	Konf
	echo "\r\n\t\t\t".'<h2>Allgemeine Systemeinstellungen</h2>';
	echo "\r\n\t\t\t\t".'URL-Rewriting: <input type="radio" name="urlrew" value="on" checked="checked">Aktiviert'; 
	echo "\r\n\t\t\t\t".'<input type="radio" name="urlrew" value="off">Deaktivert<br />';
	echo "\r\n\t\t\t\t\t".'<small style="padding-left:5em;">(.htaccess (Apache Server) oder  $SERVER[REQUEST_URI] (andere Server))</small><br /><br />';
	
	echo "\r\n\t\t\t\t".'FullHTMLCache: <input type="radio" name="fullcache" value="on">Aktiviert'; 
	echo "\r\n\t\t\t\t".'<input type="radio" name="fullcache" value="off" checked="checked">Deaktivert<br />';
	echo "\r\n\t\t\t\t\t".'<small style="padding-left:5em;">(Leistungsstarker Cache, kann aber mit Add-ons Probleme bereiten)</small><br /><br />';
	
	echo "\r\n\t\t\t\t".'Menüübersicht: <input type="radio" name="overview_left" value="on" checked="checked">Aktiviert'; 
	echo "\r\n\t\t\t\t".'<input type="radio" name="overview_left" value="off">Deaktivert<br />';
	echo "\r\n\t\t\t\t\t".'<small style="padding-left:5em;">(Zu jeder Seite neben dem Hauptmenü eine Übersicht der aktuellen Menüpunkte ziegen.)</small><br /><br />';
	
	echo "\r\n\t\t\t\t".'Markdown: <input type="radio" name="markdown" value="on">Alle Seiten';
	echo "\r\n\t\t\t\t".'<input type="radio" name="markdown" value="custom" checked="checked">Seitenspezifisch'; 
	echo "\r\n\t\t\t\t".'<input type="radio" name="markdown" value="off">Deaktivert<br />';
	echo "\r\n\t\t\t\t\t".'<small style="padding-left:5em;">(Seiteninhalte und -footer mit Markdown erstellen.)</small><br /><br />';
	
	echo "\r\n\t\t\t\t".'Mehrsprachige Seiten: <input type="radio" name="lang" value="on" checked="checked">Aktiviert';
	echo "\r\n\t\t\t\t".'<input type="radio" name="lang" value="off">Deaktivert<br />';
	echo "\r\n\t\t\t\t\t".'<small style="padding-left:5em;">(Seiteninhalte in verschiedenen Sprachen bereitstellen.)</small></small><br /><br />';
	
	echo "\r\n\t\t\t".'</div>';

	//	User
	echo "\r\n\t\t\t".'<h2>Administrator einrichten</h2>';
	
	echo "\r\n\t\t\t\t".'Nur Administrator <input type="checkbox" name="nouser" id="nouser" value="useronly" onclick="nouserbutt();"><br /><small style="padding-left:5em;">(Ist dieses CMS schon installiert und Sie wollen nur die Backenduser zurücksetzen? [Passwort vergessen])</small><br /><br />';
	
	echo "\r\n\t\t\t\t".'<input type="text" name="user" value="admin" readonly="readonly" size="60"><br /><small style="padding-left:5em;">(Username des Administrators)</small><br /><br />';
	
	echo "\r\n\t\t\t\t".'<input type="password" id="passw1" placeholder="123456" size="60"><br /><small style="padding-left:5em;">(Passwort des Administrators)</small><br /><br />';
	echo "\r\n\t\t\t\t".'<input type="password" id="passw2" placeholder="123456" size="60"><br /><small style="padding-left:5em;">(Passwort wiederholen)</small><br /><br />';
	
	echo "\r\n\t\t\t\t".'<input type="hidden" name="salt" id="salt" value="'.$output.'">';
	echo "\r\n\t\t\t\t".'<input type="hidden" name="pass" id="pass">';
	
	echo "\r\n\t\t\t\t".'<input type="text" name="name" value="Administrator" size="60"><br /><small style="padding-left:5em;">(Name des Administrators)</small><br /><br />';
	echo "\r\n\t\t\t\t".'<input type="text" name="usermail" value="admin@'.$_SERVER['HTTP_HOST'].'" size="60"><br /><small style="padding-left:5em;">(E-Mail Adresse des Administrators)</small><br /><br />';
	
	echo "\r\n\t\t\t".'<input type="submit" value="Weiter"><br />';
	echo "\r\n\t\t\t".'<b>Alle Felder m&uuml;ssen gef&uuml;llt sein!</b><br />';
	echo "\r\n\t\t\t".'<i>Alle Einstellungen können später im Backend angepasst werden!</i>';
	echo '</form>';
}

elseif($_GET['step'] == '3'){
	
	//evtl. nur BE User?
	if( isset( $_POST['nouser'] ) && $_POST['nouser'] == 'useronly' ){
		$useronly = true;
	}
	else{
		$useronly = false;
	}

	//POST Values und Typen definieren
	$postvalues = array(
		'sitename' => 'text',
		'metades' => 'text',
		'sysadminmail' => 'mail',
		'urlrew' => 'oo',
		'fullcache' => 'oo',
		'overview_left' => 'oo',
		'markdown' => 'oco',
		'lang' => 'oo',
		'user'=> 'text',
		'salt'=> 'text',
		'pass'=> 'text',
		'name'=> 'text',
		'usermail'=> 'mail'
	);
	$postokays = array();
	
	if( $useronly ){
		//Übergabewünsche Array anpassen
		$postvalues = array_slice( $postvalues, 8 );
	}
	
	//alle Post testen
	foreach ($postvalues as $key => $type) {
		//lesen
		$value = $_POST[$key];
		
		//gefüllt?
		if( !empty( $value ) ){
			//je nach Typ
			if( $type == 'text' ){
				//okay, solange gefüllt
				$ok = true;
			}
			elseif( $type == 'mail' ){
				//Mail Syntax Check
				if( filter_var($value, FILTER_VALIDATE_EMAIL) ){
					$ok = true;
				}
				else{
					$ok = false;
				}
				
			}
			elseif( $type == 'oo' ){
				//on oder off?
				if( $value == 'on' || $value == 'off' ){
					$ok = true;
				}
				else{
					$ok = false;
				}
			}
			elseif( $type == 'oco' ){
				//on, custom oder off?
				if( $value == 'on' || $value == 'off' || $value = 'custom' ){
					$ok = true;
				}
				else{
					$ok = false;
				}
			}
			else{
				$ok = false;
			}
		}
		else{
			$ok = false;
		}
		
		//Checkergebins merken
		$postokays[] = $ok;
	}

	//Alle Felder richtig gefüllt
	if( in_array( false, $postokays ) ){

		//Fehlermeldung
		echo( '<h1 style="color:red;">Alle Felder m&uuml;ssen korrekt gef&uuml;llt sein!!</h1><br /><br />' );
		
		echo( '<a href="configurator.php?step=2" >Zur&uuml;ck</a>' );
		
		//Ende
		echo( "\r\n\t\t".'</div>');
		echo( "\r\n\t".'</div></body>');
		echo( "\r\n".'</div></html>');
		die;
	}

	//Werte für config.kimb
	//	URL
	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$siteurl = substr($urlg, '0', '-'.strlen(strrchr($urlg, '/')));
	//	Favicon
	$sitefavi = $siteurl.'/load/system/KIMB.ico';
	//	Sysabsender
	$mailvon = 'cms@'.$_SERVER['HTTP_HOST'];
	
	//nicht nur User?
	if( !$useronly ){
		//schreiben
		$cofile = new KIMBdbf( 'config.kimb' );
		$cofile->write_kimb_id( '001', 'add', 'sitefavi', $sitefavi );
		$cofile->write_kimb_id( '001', 'add', 'loginokay', makepassw( 100, null, 'numaz' ) );
		$cofile->write_kimb_id( '001', 'add', 'siteurl', $siteurl );
		$cofile->write_kimb_id( '001', 'add', 'mailvon', $mailvon );
		$cofile->write_kimb_id( '001', 'add', 'cronkey', makepassw( 75, null, 'numaz' ) );
		
		$cofile->write_kimb_id( '001', 'add', 'sitename', $_POST['sitename'] );
		$cofile->write_kimb_id( '001', 'add', 'description', $_POST['metades'] );
		$cofile->write_kimb_id( '001', 'add', 'adminmail', $_POST['sysadminmail'] );
		
		
		$cofile->write_kimb_id( '001', 'add', 'fullcache', $_POST['fullcache'] );
		$cofile->write_kimb_id( '001', 'add', 'overview_left', $_POST['overview_left'] );
		$cofile->write_kimb_id( '001', 'add', 'markdown', $_POST['markdown'] );
		$cofile->write_kimb_id( '001', 'add', 'lang', $_POST['lang'] );
		$cofile->write_kimb_id( '001', 'add', 'urlrewrite', $_POST['urlrew'] );
		//	.htaccess für URL-Rewriting umbenennen
		if( $_POST['urlrew'] == 'on' ){
			rename( __DIR__.'/_.htaccess', __DIR__.'/.htaccess' );
		}
		unset( $cofile );
	}

	// BE User KIMBdbf
	$bufile = new KIMBdbf( '/backend/users/list.kimb' );
	//	aufräumen
	$bufile->delete_kimb_file();
	$bufile->write_kimb_id( '1', 'add', 'user', 'admin' );
	$bufile->write_kimb_id( '1', 'add', 'permiss', 'more' );
	$bufile->write_kimb_id( '1', 'add', 'passw', $_POST['pass'] );
	$bufile->write_kimb_id( '1', 'add', 'salt', $_POST['salt'] );
	$bufile->write_kimb_id( '1', 'add', 'name', $_POST['name'] );
	$bufile->write_kimb_id( '1', 'add', 'mail', $_POST['usermail'] );
	unset( $bufile );

	//fertig anzeigen
	echo( "\r\n\t\t\t".'<h2>Installation erfolgreich!</h2>
			<br />
			<a href="'.$siteurl.'/" target="_blank"><button>Zur <b>Seite</b></button></a>
			<br />
			<a href="'.$siteurl.'/kimb-cms-backend/" target="_blank"><button>Zum <b>Backend</b></button></a>
			<br />');
	
	echo( "\r\n\t\t\t".'<hr />' );
	echo( "\r\n\t\t\t".'<h2>KIMB-technologies Register</h2>' );
	echo( "\r\n\t\t\t".'Registrieren Sie sich im KIMB-technologies Register und bleiben Sie auf dem Laufenden.<br />' );
	echo( "\r\n\t\t\t".'<a href="https://register.kimb-technologies.eu/" target="_blank">Zum Register</a>' );
	echo( "\r\n\t\t\t".'<hr />' );
	echo( "\r\n\t\t\t".'<h2>KIMB-CMS Wiki</h2>' );
	echo( "\r\n\t\t\t".'Anleitungen und Informarionen zum KIMB-CMS:<br />' );
	echo( "\r\n\t\t\t".'<ul>
				<li>
					<a href="https://cmswiki.kimb-technologies.eu/home/" target="_blank">Wiki <strong>Startseite</strong></a>
				</li>
				<li>
					<a href="https://cmswiki.kimb-technologies.eu/tutorials/installation/" target="_blank">Wiki <strong>Installation</strong></a>
				</li>
				<li>
					<a href="https://cmswiki.kimb-technologies.eu/tutorials/erste-schritte/" target="_blank">Wiki <strong>Erste Schritte</strong></a>
				</li>
				<li>
					<a href="https://cmswiki.kimb-technologies.eu/fragen/" target="_blank">Wiki <strong>Fragen</strong></a>
				</li>
				<li>
					<a href="https://cmswiki.kimb-technologies.eu/addons/" target="_blank">Wiki <strong>Add-ons</strong></a>
				</li>');
	echo( "\r\n\t\t\t".'</ul>');
	echo( "\r\n\t\t\t".'<hr />' );
		
	//Konfigurator sperren
	unlink('conf-enable');
}
else{
	
	echo "\r\n\t\t\t".'<h2>Serverprüfung</h2>';
	echo "\r\n\t\t\t".'<ul>';
	
	//PHP - Version OK?
	if (version_compare(PHP_VERSION, '7.0.0' ) >= 0 ) {
    		echo "\r\n\t\t\t\t".'<li class="okay">Sie verwenden PHP 7</li>';
		$okay[] = 'okay';
	}
	elseif (version_compare(PHP_VERSION, '5.5.0' ) >= 0 ) {
    		echo "\r\n\t\t\t\t".'<li class="war">Sie verwenden PHP 5.5.0, aber noch nicht das neue PHP 7</li>';
		$okay[] = 'war';
	}
	else{
		echo "\r\n\t\t\t\t".'<li class="err">Dieses System wurde f&uuml;r PHP 5.5.0 und h&ouml;her entwickelt, bitte f&uuml;hren Sie ein PHP-Update durch!</li>';
		$okay[] = 'err';
	}
	
	//url fopen okay?
	if( ini_get( 'allow_url_fopen' ) ){
		$okay[] = 'okay';
		echo "\r\n\t\t\t\t".'<li class="okay">Ihr Server erlaubt PHP Requests per HTTP zu anderen Servern!</li>';
	}
	else{
		$okay[] = 'war';
		echo "\r\n\t\t\t\t".'<li class="war">Ihr Server erlaubt PHP keine Requests per HTTP zu anderen Servern!</li>';
	}

	//cURL
	if( function_exists('curl_version') ){
		$okay[] = 'okay';
		echo "\r\n\t\t\t\t".'<li class="okay">Ihr Server hat cURL!</li>';
	}
	else{
		$okay[] = 'war';
		echo "\r\n\t\t\t\t".'<li class="war">Ihrem Server fehlt cURL!</li>';
	}
	
	//PHP GD
	if (defined('GD_VERSION')) {   
		$okay[] = 'okay';
		echo "\r\n\t\t\t\t".'<li class="okay">Ihr Server hat PHP_GD!</li>';
	}
	else{
		$okay[] = 'war';
		echo "\r\n\t\t\t\t".'<li class="war">Ihrem Server fehlt PHP_GD!</li>';
	}
	//nötige schreibbare Verzeichnisse und Dateien
	$checkfolders = array(
		'core/oop/kimb-data',
		'core/oop/kimb-data/index.kimb',
		'core/oop/kimb-data/addon',
		'core/oop/kimb-data/addon/index.kimb',
		'core/oop/kimb-data/addon/wish',
		'core/oop/kimb-data/addon/wish/index.kimb',
		'core/oop/kimb-data/cache',
		'core/oop/kimb-data/cache/index.kimb',
		'core/oop/kimb-data/menue',
		'core/oop/kimb-data/menue/index.kimb',
		'core/oop/kimb-data/site',
		'core/oop/kimb-data/site/index.kimb',
		'core/oop/kimb-data/url',
		'core/oop/kimb-data/url/index.kimb',
		'core/oop/kimb-data/backend',
		'core/oop/kimb-data/backend/index.kimb',
		'core/oop/kimb-data/backend/users',
		'core/oop/kimb-data/backend/users/index.kimb',
		'core/oop/full_cache/',
		'core/oop/full_cache/index.txt',
		'core/addons',
		'core/secured',
		'core/secured/logo.png',
		'core/theme',
		'core/theme/output_site_norm.php',
		'load/addondata',
		'load/userdata',
		'load/system/theme',
		'load/system/theme/design.css',
		'conf-enable'
	 );

	//alle Verzeichnisse testen und Fehler bzw. $count++
	$count = 0;
	foreach( $checkfolders as $folder ){

		if( is_writable( __DIR__.'/'.$folder ) ){
			$count++;
		}
		else{
			echo "\r\n\t\t\t\t".'<li class="err">"'.$folder.'" ist nicht schreibbar!</li>';
		}
	}
	
	//Hat count den richtigen Wert, dann alles okay
	if($count == count( $checkfolders ) ){
		echo "\r\n\t\t\t\t".'<li class="okay">Alle benötigten Verzeichnisse sind schreibbar!</li>';
		$okay[] = 'okay';
	}
	else{
		$okay[] = 'err';
	}

	echo "\r\n\t\t\t".'</ul>';

	//okay auswerten
	//wiederholen oder weiter zu Schritt 2
	if( array_search ('err' , $okay ) === false && array_search ('war' , $okay ) === false ){
		echo( "\r\n\t\t\t".'<ul>
				<li class="okay">
					Alle Bedingungen für das KIMB-CMS sind erfüllt!
					<br />
					<br />');
		
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php?step=2">
						<button>Weiter</button>
					</a>
				</li>
			</ul>');
	}
	elseif( array_search ('err' , $okay ) === false ){
		echo( "\r\n\t\t\t".'<ul>
					<li class="war">
						Die grundlegenden Bedingungen für das KIMB-CMS sind erfüllt, es könnte aber zu Problemen kommen!
					<br />
					<br />');
		
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php?step=2">
						<button>Weiter</button>
					</a>
					</br />');
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php">
						<button>Neue Systemprüfung</button>
					</a>
				</li>
			</ul>');
	}
	else{
		echo( "\r\n\t\t\t".'<ul>
				<li class="err">
					Die grundlegenden Bedingungen für das KIMB-CMS sind nicht erfüllt!
					<br />
					<br />');
		
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php">
						<button>Neue Systemprüfung</button>
					</a>
				</li>
			</ul>');
	}


}
echo( "\r\n\t\t".'</div>
	</body>
</html>');
?>
