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

//Rechte für diese Seite??
if( check_felogin_login( '---session---', '---allgsiteid---', true ) ){
	
	//CSS für System
	$sitecontent->add_html_header('<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/addondata/daten/explorer.dev.css" media="all">');
	//jQuery und Hash werden benötigt
	$sitecontent->add_html_header('<!-- jQuery UI -->');
	//Verschlüsselung
	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/sjcl.min.js"></script>');
	//JS für System
	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/explorer.dev.js"></script>');
	
	//HTML Gerüst für System
	$sitecontent->add_site_content('<div id="addon_daten_main" class="addon_daten_main">Bitte aktivieren Sie JavaScript für die Datenverwaltung!</div>');
	
	
}
else{
	//Fehler
	$sitecontent->echo_error( 'Die Dateiverwaltung ist nur mit einem Login via Felogin erreichbar!', '403' );
}

/*
Management der Userrechte (Speicherplatz, nur lesen)
JavaScript Verschlüsselung von Dateien und Tabellen möglich (https://bitwiseshiftleft.github.io/sjcl/)
Verwendung von Felogin
JSON für Tabellen
AJAX gestützt

Seite per Felogin geschützt, lädt per AJAX Liste der Gruppen
Gruppen mit Tabellen und Dateien
Gruppe für alle User
Gruppe für bestimmte User
*/


?>
