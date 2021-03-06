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


define("KIMB_CMS", "Clean Request");

//Klassen Autoload & Konfiguration & Funktionen laden
require_once(__DIR__.'/core/conf/conf.php');

//System initialisiert!

//Konfigurator laufengelassen?
if( empty( $allgsysconf['siteurl'] ) ){
	open_url( 'configurator.php' );
}

//FE Translation
require_once(__DIR__.'/core/trans/trans_do.php');
//Menue und Site IDs aus Request 
require_once(__DIR__.'/core/generating/get_ids.php');
//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_fe_first.php');
//Menuestruktur erstellen
require_once(__DIR__.'/core/generating/make_menue.php');
//Inhalt erstellen
require_once(__DIR__.'/core/generating/make_content.php');
//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_fe_second.php');

//Seite ausgeben
$sitecontent->output_complete_site( $allgsys_trans );

//Die Rechtschreibfehler in den Kommentaren sind nicht beabsichtigt,
//ich bin nunmal mit der Syntax von Computersprachen vertrauter als 
//mit der Syntax der deutschen Sprache.
//Über Hinweise zu Fehlern, besonders in den Ausgaben des CMS, freue ich mich.
//	https://www.kimb-technologies.eu/kontakt
?>
