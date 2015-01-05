<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login( 'nineteen' , 'more');

$sitecontent->add_site_content('<h2>Userlevel Backend</h2>');

$sitecontent->output_complete_site();
?>
