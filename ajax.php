<?php
define("KIMB_CMS", "Clean Request");

defined('KIMB_CMS') or die('No clean Request');

//Klassen und Funktionen
require_once(__DIR__.'/core/oop/kimbdbf.php');

//Konfiguration

$conffile = new KIMBdbf('config.kimb');

$allgsysconf = $conffile->read_kimb_id('001');

//session, ...

session_start();
error_reporting('0');
header('X-Robots-Tag: '.$allgsysconf['robots']);

//Funktionen laden
require_once(__DIR__.'/core/conf/funktionen.php');

//System initialisiert!

//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_ajax.php');

//Systemegeigen:
//Systemegeigen:
//Systemegeigen:

//...

?>
