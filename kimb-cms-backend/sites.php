<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Seite erstellen, zuordnen

check_backend_login();

echo 'sdfdsf';

$sitecontent->output_complete_site();
?>
