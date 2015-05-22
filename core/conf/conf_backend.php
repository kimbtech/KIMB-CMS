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

defined('KIMB_CMS') or die('No clean Request');

//Klassen Autoload
require_once( __DIR__.'/../oop/all_oop.php' );

//Konfiguration laden
$conffile = new KIMBdbf('config.kimb');
$allgsysconf = $conffile->read_kimb_id('001');

//session, Fehleranzeige, Robots-Header, Content, Codierung
session_name ("KIMBCMS");
session_start();
error_reporting( 0 );
header('X-Robots-Tag: none');
header('Content-Type: text/html; charset=utf-8');

//wichtige Objekte

//Seitenausgabe BE
$sitecontent = new backend_output($allgsysconf);

//Info über das CMS dem HTML-Code hinzufügen
$kimbcmsinfo = '<!--

	This site is made with KIMB-CMS!
	http://www.KIMB-technologies.eu
	https://bitbucket.org/kimbtech/kimb-cms/

	GNU General Public License v3
	http://www.gnu.org/licenses/gpl-3.0
	
	The Backend can only be used with an account!

-->';
$sitecontent->add_html_header($kimbcmsinfo);

//Allgemeine Funktionen usw. laden
require_once(__DIR__.'/funktionen.php');

//Backend Add-ons (mit zusätzlichem Schutz)
//Wenn User eingeloggt, immer die BE Add-ons laden
if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){
	require(__DIR__.'/../addons/addons_be_first.php');
}
//Wenn nicht eingeloggt, Add-ons nur auf der Login-Seite laden
elseif( substr( get_req_url() , -26 ) == 'kimb-cms-backend/index.php' || substr( get_req_url() , -16 ) == 'kimb-cms-backend' || substr( get_req_url() , -17 ) == 'kimb-cms-backend/' ){
	require(__DIR__.'/../addons/addons_be_first.php');
}
?>
