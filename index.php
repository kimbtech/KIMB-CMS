<?php
//Klassen und Funktionen
require_once('core/oop/all_oop.php');
//Konfiguration laden
require_once('core/conf/conf.php');

//System initialisiert!

//Menue und Site IDs aus Request 
require_once('core/generating/get_ids.php');
//Addons ermoeglichen einzugreifen
$addon = 'first';
require_once('core/generating/addon_control.php');
//Menuestruktur erstellen
require_once('core/generating/make_menue.php');
//Inhalt erstellen
require_once('core/generating/make_content.php');
//Addons ermoeglichen einzugreifen
$addon = 'second';
require_once('core/generating/addon_control.php');


echo $sitecontent->output_complete_site();
?>
