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

//Klassen Autoload
require_once( __DIR__.'/core/oop/all_oop.php' );

//Konfiguration
$conffile = new KIMBdbf('config.kimb');
$allgsysconf = $conffile->read_kimb_id( '001' );

//Funktionen laden
require_once(__DIR__.'/core/conf/funktionen.php');

//session, Fehleranzeige, Robots-Header, Content, Codierung
SYS_INIT( $allgsysconf['robots'], 'application/json' );

//System initialisiert!

//Stimmt der Key?
if( $_GET['key'] == $allgsysconf['cronkey'] ){

	//Key stimmt -> Add-ons ausführen
	require_once( __DIR__.'/core/addons/addons_cron.php' );

	//beenden
	die;

}

//Key wohl falsch!
echo( 'Fehlerhafter Cronzugriff!' );
die;
?>
