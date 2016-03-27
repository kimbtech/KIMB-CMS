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

//Konfiguration lesen
$conffile = new KIMBdbf('config.kimb');
$allgsysconf = $conffile->read_kimb_id('001');

//allgemeine Funktionen usw. laden
require_once(__DIR__.'/funktionen.php');

//session, Fehleranzeige, Robots-Header, Content, Codierung
SYS_INIT( $allgsysconf['robots'] );

//wichtige Objekte erstellen

//Seiteninhat
$sitecontent = new system_output($allgsysconf);

//wenn Cache aktiviert, cache laden
if($allgsysconf['cache'] == 'on'){
	$sitecache = new cacheCMS($allgsysconf, $sitecontent);
}

//Info über das CMS dem HTML-Code hinzufügen
$kimbcmsinfo = '<!--

	This site is made with KIMB-CMS!
	https://www.KIMB-technologies.eu
	https://bitbucket.org/kimbtech/kimb-cms/
	https://github.com/kimbtech/kimb-cms/

	GNU General Public License v3
	http://www.gnu.org/licenses/gpl-3.0

-->';

$sitecontent->add_html_header($kimbcmsinfo);

?>
