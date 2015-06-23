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

//Nur mit conf-enable Datei den Konfigurator erlauben, sonst unerlaubte Konfiguration möglich
if(file_exists ('conf-enable') != 'true'){
	//User bitten den Konfigurator zu aktivieren
	echo('<title>KIMB CMS - Installation</title><link rel="shortcut icon" href="load/system/KIMB.ico" type="image/x-icon; charset=binary"><h1>Error - 403</h1>Bitte schalten Sie den Configurator frei, erstellen Sie eine leere "conf-enable" Datei im CMS-Root-Verzeichnis.<br /> Please activate the configurator, create an empty "conf-enable" file in the CMS root folder.'); die;
}

//HTML des Konfigurators 
//inkl. Warnung per JS wenn /core/ aufrufbar!
echo('
<!DOCTYPE HTML >
<html><head>
<title>KIMB CMS - Installation</title>
<link rel="shortcut icon" href="load/system/KIMB.ico" type="image/x-icon; charset=binary">
<link rel="icon" href="load/system/KIMB.ico" type="image/x-icon; charset=binary">
<style>
body { 
	background-color:#999999; 
	font-family: Ubuntu, Arial;
	color:#000000;
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
#wichtig{
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
			$( "#wichtig" ).css( "display" , "block" );
		}
	});
});
</script>
</head><body>

<div id="main">
<h1 style="border-bottom:5px solid #55dd77;" >KIMB CMS - Installation</h1>
<div style="display:none;" id="wichtig" >
	<b>Achtung:</b><br />Das Verzeichnis /core/ und seine Unterverzeichnisse sind nicht gesch&uuml;tzt! <br /> Bitte sperren Sie diese Verzeichnisse f&uuml;r jegliche Browseraufrufe!!
</div>
<br />
');

//Ganz unten bei else{} gehts los!

if($_GET['step'] == '2'){

	//Zufallsgenerator Passwortsalt
	$alles = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$laenge = '10';
	$anzahl = strlen($alles);
	$i = '1';
	$output = '';
	while($i <= $laenge){
		$stelle = mt_rand('0', $anzahl); 
		$output .= $alles{$stelle};
		$i++;
	}

	//Formular für die Konfiguration
	echo '<h2>Allgemeine Systemeinstellungen</h2>';
	echo '<form method="post" action="configurator.php?step=3" onsubmit=" if( document.getElementById(\'passw\').value != \'\' ){ document.getElementById(\'passw\').value = SHA1( \''.$output.'\' + document.getElementById(\'passw\').value );} else{ alert( \'Bitte geben Sie ein Passwort für den Administrator an!\' ); return false; } " >';
	echo '<input type="text" name="sitename" value="KIMB CMS" size="60"><br />(Name der Seite)<br /><br />';
	echo '<input type="text" name="metades" value="CMS von KIMB-technologies" size="60"><br />(Meta Seitenbeschreibung)<br /><br />';
	echo '<input type="text" name="sysadminmail" value="cmsadmin@example.com" size="60"><br />(E-Mail Adresse des Systemadministrators)<br /><br />';
	echo '<input type="radio" name="urlrew" value="off">OFF <input type="radio" name="urlrew" value="on" checked="checked">ON (Aktivieren Sie URL-Rewriting f&uuml;r das System (Dazu muss Ihr Server die .htaccess im Rootverzeichnis verwenden k&ouml;nnen oder die Variable $SERVER[REQUEST_URI] setzen.))<br /><br />';

	echo '<h2>Ersten Administrator einrichten</h2>';
	echo '<input type="text" name="user" value="admin" readonly="readonly" size="60"><br />(Username des Administrators)<br /><br />';
	echo '<input type="password" name="passhash" placeholder="123456" id="passw" size="60"><input type="hidden" name="salt" value="'.$output.'"><br />(Passwort des Administrators)<br /><br />';
	echo '<input type="text" name="name" value="Max Muster" size="60"><br />(Name des Administrators)<br /><br />';
	echo '<input type="text" name="usermail" value="max.muster@example.com" size="60"><br />(E-Mail Adresse des Administrators)<br /><hr /><hr />';

	echo '<input type="submit" value="Weiter"> <b>Alle Felder m&uuml;ssen gef&uuml;llt sein !!</b><br />';
	echo '</form>';
}

elseif($_GET['step'] == '3'){

	//Alle Felder richtig gefüllt
	if( $_POST['sitename'] == '' || $_POST['metades'] == '' || $_POST['sysadminmail'] == '' || $_POST['passhash'] == '' || $_POST['name'] == '' || $_POST['usermail'] == '' ){

		echo( '<h1 style="color:red;">Alle Felder m&uuml;ssen gef&uuml;llt sein !!</h1><br /><br />' );
		echo( '<a href="configurator.php?step=2" >Zur&uuml;ck</a>' );
		die;
	}


	//Request URL
	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$url = substr($urlg, '0', '-'.strlen(strrchr($urlg, '/')));

	//Zufallsgenerator Loginokay
	$alles = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$laenge = '50';
	$anzahl = strlen($alles);
	$i = '1';
	$output = '';
	while($i <= $laenge){
		$stelle = mt_rand('0', $anzahl); 
		$output .= $alles{$stelle};
		$i++;
	}

	//Konfigurationsteile
	//erster
	$addconf = '<[001-sitename]>'.$_POST['sitename'].'<[001-sitename]>
<[001-sitefavi]>'.$url.'/load/system/KIMB.ico<[001-sitefavi]>
<[001-loginokay]>'.$output.'<[001-loginokay]>
<[001-siteurl]>'.$url.'<[001-siteurl]>
<[001-description]>'.$_POST['metades'].'<[001-description]>
<[001-adminmail]>'.$_POST['sysadminmail'].'<[001-adminmail]>
<[001-mailvon]>cms@'.$_SERVER['HTTP_HOST'].'<[001-mailvon]>
<[001-urlrewrite]>'.$_POST['urlrew'].'<[001-urlrewrite]>';

	//Schreibe in Konfigurationsdatei
	$handle = fopen(__DIR__.'/core/oop/kimb-data/config.kimb', 'a+');
	fwrite($handle, $addconf);
	fclose($handle);

	//.htaccess für URL-Rewriting umbenennen
	if( $_POST['urlrew'] == 'on' ){
		rename( __DIR__.'/_.htaccess', __DIR__.'/.htaccess' );
	}

	//zweiter
	$adduser = '<[1-passw]>'.$_POST['passhash'].'<[1-passw]>
<[1-salt]>'.$_POST['salt'].'<[1-salt]>
<[1-name]>'.$_POST['name'].'<[1-name]>
<[1-mail]>'.$_POST['usermail'].'<[1-mail]>';

	//schreiben in Userdatei
	$handle = fopen(__DIR__.'/core/oop/kimb-data/backend/users/list.kimb', 'a+');
	fwrite($handle, $adduser);
	fclose($handle);

	//fertig anzeigen
	echo('Installation erfolgreich!<br /><br /> <a href="'.$url.'/" target="_blank"><button>Zur Seite</button></a><br />');
	echo('<a href="'.$url.'/kimb-cms-backend/" target="_blank"><button>Zum Backend</button></a><br />');
	
	echo( '<hr />' );
	echo( '<h2>KIMB-technologies Register</h2>' );
	echo( 'Registrieren Sie sich im KIMB-technologies Register und bleiben Sie auf dem Laufenden.<br />' );
	echo( '<a href="https://register.kimb-technologies.eu/" target="_blank">Zum Register</a>' );
	echo( '<hr />' );

	//Konfigurator sperren
	unlink('conf-enable');

}
else{
	
	echo '<h2>Serverprüfung</h2>';
	echo '<ul>';
	
	//PHP - Version OK?
	if (version_compare(PHP_VERSION, '5.3.0' ) >= 0 ) {
    		echo '<li class="okay">Sie verwenden PHP 5.3.0 oder neuer!</li>';
		$okay[] = 'okay';
	}
	else{
		echo '<li class="err">Dieses System wurde f&uuml;r PHP 5.3.0 und h&ouml;her entwickelt, bitte f&uuml;hren Sie ein PHP-Update durch!</li>';
		$okay[] = 'err';
	}
	
	//url fopen okay?
	if( ini_get( 'allow_url_fopen' ) ){
		$okay[] = 'okay';
		echo '<li class="okay">Ihr Server erlaubt PHP Requests per HTTP zu anderen Servern!</li>';
	}
	else{
		$okay[] = 'war';
		echo '<li class="war">Ihr Server erlaubt PHP keine Requests per HTTP zu anderen Servern!</li>';
	}

	//cURL
	if( function_exists('curl_version') ){
		$okay[] = 'okay';
		echo '<li class="okay">Ihr Server hat cURL !</li>';
	}
	else{
		$okay[] = 'war';
		echo '<li class="war">Ihrem Server fehlt cURL!</li>';
	}
	
	//PHP GD
	if (defined('GD_VERSION')) {   
		$okay[] = 'okay';
		echo '<li class="okay">Ihr Server hat PHP_GD !</li>';
	}
	else{
		$okay[] = 'war';
		echo '<li class="war">Ihrem Server fehlt PHP_GD!</li>';
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
			echo '<li class="err">"'.$folder.'" ist nicht schreibbar!</li>';
		}
	}
	
	//Hat count den richtigen Wert, dann alles okay
	if($count == count( $checkfolders ) ){
		echo '<li class="okay">Alle benötigten Verzeichnisse sind schreibbar!</li>';
		$okay[] = 'okay';
	}
	else{
		$okay[] = 'err';
	}

	echo '</ul>';

	//okay auswerten
	//wiederholen oder weiter zu Schritt 2
	if( array_search ('err' , $okay ) === false && array_search ('war' , $okay ) === false ){
		echo('<ul><li class="okay">Alle Bedingungen für das KIMB-CMS sind erfüllt!<br /><br />');
		
		echo('<a href="configurator.php?step=2"><button>Weiter</button></a></li></ul>');
	}
	elseif( array_search ('err' , $okay ) === false ){
		echo('<ul><li class="war">Die grundlegenden Bedingungen für das KIMB-CMS sind erfüllt, es könnte aber zu Problemen kommen!<br /><br />');
		
		echo('<a href="configurator.php?step=2"><button>Weiter</button></a></br />');
		echo('<a href="configurator.php"><button>Neue Systemprüfung</button></a></li></ul>');
	}
	else{
		echo('<ul><li class="err">Die grundlegenden Bedingungen für das KIMB-CMS sind nicht erfüllt!<br /><br />');
		
		echo('<a href="configurator.php"><button>Neue Systemprüfung</button></a></li></ul>');
	}

}
echo('</div></body></html>');
?>
