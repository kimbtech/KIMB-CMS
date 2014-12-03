<?php

defined('KIMB_Backend') or die('No clean Request');

//erstelle globale conf Variablen aus config.kimb

$conffile = new KIMBdbf('config.kimb');

$allgsysconf = $conffile->read_kimb_id('001');

//session, ...

session_start();
error_reporting('0');
header('X-Robots-Tag: none');

//wichtige Objekte

$sitecontent = new backend_output($allgsysconf);

$kimbcmsinfo = '<!--

	Diese Seite basiert auf dem KIMB-CMS!
	KIMB-technologies.blogspot.com

	CC BY-ND 4.0
	http://creativecommons.org/licenses/by-nd/4.0/

-->';

$sitecontent->add_html_header($kimbcmsinfo);

//allgemeine Funktionen

require_once(__DIR__.'/funktionen.php');
?>
