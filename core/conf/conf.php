<?php

defined('KIMB_CMS') or die('No clean Request');

//erstelle globale conf Variablen aus config.kimb

$conffile = new KIMBdbf('config.kimb');

$allgsysconf = $conffile->read_kimb_id('001');

//session, ...

session_start();
error_reporting('0');
header('X-Robots-Tag: '.$allgsysconf['robots']);

//wichtige Objekte

$sitecontent = new system_output($allgsysconf);

if($allgsysconf['cache'] == 'on'){
	$sitecache = new cacheCMS($allgsysconf, $sitecontent);
}

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
