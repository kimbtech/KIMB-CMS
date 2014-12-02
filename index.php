<?php
define("KIMB_CMS", "Clean Request");

defined('KIMB_CMS') or die('No clean Request');

//Klassen und Funktionen
require_once(__DIR__.'/core/oop/all_oop.php');
//Konfiguration laden
require_once(__DIR__.'/core/conf/conf.php');

//System initialisiert!

//Menue und Site IDs aus Request 
require_once(__DIR__.'/core/generating/get_ids.php');
//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_first.php');
//Menuestruktur erstellen
require_once(__DIR__.'/core/generating/make_menue.php');
//Inhalt erstellen
require_once(__DIR__.'/core/generating/make_content.php');
//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_second.php');


echo $sitecontent->output_complete_site();
?>
