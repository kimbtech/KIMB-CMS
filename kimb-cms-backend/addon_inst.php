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

//Diese Datei ist Teil des Backends, sie wird direkt aufgerufen.

//Konfiguration & Klassen & Funktionen laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Diese Datei stellt den Teil "Add-ons installieren und löschen" bereit

//Hier sind für alle Aufgaben die gleichen Rechte erforderlich, hat der User diese?
check_backend_login( 'fiveteen' , 'more');

//Die entsprechende Backendklasse laden
//	Die Parameter sind schon vom CMS geladen
$beaddinst = new BEaddinst( $allgsysconf, $sitecontent, $addoninclude );

//Den Request Paramter reinigen
$_GET['addon'] = preg_replace( "/[^a-z_]/" , "" , $_GET['addon'] );

//Herausfinden was der User will und dann die entsprechenden Methoden aufrufen
if( isset( $_GET['del'] ) && !empty( $_GET['addon'] ) ){

	//Add-on löschen
	$beaddinst->del_addon( $_GET['addon'] );
	
}
elseif( !empty( $_FILES['userfile']['name'] ) ){

	//Add-on installieren
	$beaddinst->install_addon( $_FILES["userfile"]["tmp_name"] );

}
elseif( $_GET['todo'] == 'chdeak' && !empty( $_GET['addon'] ) ){

	//Add-on Status ändern
	$beaddinst->change_stat( $_GET['addon'] );
	
}
elseif( $_GET['todo'] == 'addonnews' && !empty( $_GET['addon'] ) ){

	//Hinweise über das Add-on zeigen
	$beaddinst->show_addon_news( $_GET['addon'] );
	
	$showlist = 'no';
}
elseif( $_GET['todo'] == 'checkall' ){

	//nach Updates suchen
	$beaddinst->check_addon_upd();
		
}

//Meistens wird die Liste mit allen Add-ons und deren Status angezeigt, hier auch der Fall?
if( !isset( $showlist ) ){
	
	$beaddinst->show_list();
	
}
//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
