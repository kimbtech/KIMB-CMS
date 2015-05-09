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

//erstelle globale conf Variablen aus config.kimb
$conffile = new KIMBdbf('config.kimb');
$allgsysconf = $conffile->read_kimb_id('001');

//session, ...
session_name ("KIMBCMS");
session_start();
error_reporting( 0 );
header('X-Robots-Tag: '.$allgsysconf['robots']);
header('Content-Type: text/html; charset=utf-8');

//wichtige Objekte

$sitecontent = new system_output($allgsysconf);

if($allgsysconf['cache'] == 'on'){
	$sitecache = new cacheCMS($allgsysconf, $sitecontent);
}

$kimbcmsinfo = '<!--

	This site is made with KIMB-CMS!
	http://www.KIMB-technologies.eu
	https://bitbucket.org/kimbtech/kimb-cms/

	GNU General Public License v3
	http://www.gnu.org/licenses/gpl-3.0

-->';

$sitecontent->add_html_header($kimbcmsinfo);

//allgemeine Funktionen

require_once(__DIR__.'/funktionen.php');
?>
