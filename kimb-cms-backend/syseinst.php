<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Systemeinstellungen in config.kimb ( eigenes Feld möglich )


$sitecontent->output_complete_site();
?>
