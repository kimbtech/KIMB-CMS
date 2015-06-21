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

//Diese Datei stellt den Teil "Menüs" bereit

//Die entsprechende Backendklasse laden
$bemenue = new BEmenue( $allgsysconf, $sitecontent );

//Herausfinden, was der User machen will
//	Überprüfung der Rechte und Aufruf der passenden Methode
if( $_GET['todo'] == 'new' ){
	check_backend_login('five' , 'more');
	
	//neuer Menüpunkt
	$bemenue->make_menue_new();
	
}
elseif( $_GET['todo'] == 'connect' ){
	check_backend_login( 'six' );

	//Menüpunkte Seiten zuordnen
	$bemenue->make_menue_connect();

}
elseif( $_GET['todo'] == 'list' ){
	check_backend_login('seven' , 'more');

	//Menü auflisten
	$bemenue->make_menue_list();
	
}
elseif( $_GET['todo'] == 'edit' ){
	check_backend_login('seven' , 'more');

	//Menüpunkt bearbeiten
	$bemenue->make_menue_edit();
}
elseif( $_GET['todo'] == 'del' ){
	check_backend_login('seven' , 'more');

	//Menü löschen
	
	//hier überprüft die Methode den Request nicht, daher geschieht dies hier:
	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) && ( $_GET['fileidbefore'] == 'first' || is_numeric( $_GET['fileidbefore'] ) || $_GET['fileidbefore'] == '' ) ){
		
		//Paramter vorbereiten
		$GET = $_GET;
		
		//Methode ausführen
		if( $bemenue->make_menue_del_dbf( $GET) ){
			//wenn okay Menüliste anzeigen
			open_url('/kimb-cms-backend/menue.php?todo=list');
			die;
		}
	}
	
	//Fehler anzeigen, wenn geschehen
	$sitecontent->add_site_content('<h2>Ein Menue löschen</h2>');
	$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	
}
elseif( $_GET['todo'] == 'deakch' ){
	check_backend_login( 'four' );
	
	//Menü Status ändern

	//hier überprüft die Methode den Request nicht, daher geschieht dies hier:
	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) ){
	
		//Paramter vorbereiten	
		$GET = $_GET;
		
		//Methode ausführen
		if( $bemenue->make_menue_deakch_dbf( $GET) ){
			
			//wenn User die Rechte hat die Menüliste zu sehen diese anzeigen
			if( check_backend_login( 'seven', 'more', false ) ){
				open_url('/kimb-cms-backend/menue.php?todo=list');
				die;
			}
			else{
				//sonst zur Seite Menü zuordnen gehen
				open_url('/kimb-cms-backend/menue.php?todo=connect');
				die;
			}
		}
	}
	
	//Fehler anzeigen, wenn geschehen
	$sitecontent->add_site_content('<h2>Einen Menuestatus verändern</h2>');
	$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');

}
else{
	check_backend_login('four' , 'more');

	//User will nichts bestimmtes
	//	alles was möglich ist anzeigen

	$sitecontent->add_site_content('<h2>Startseite Menue</h2>');

	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new"><span id="startbox"><b>Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Eine neues Menue erstellen.</i></span></a>');
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect"><span id="startbox"><b>Zuordnen</b><br /><span class="ui-icon ui-icon-arrowthick-2-e-w"></span><br /><i>Die Menues einer Seite zuordnen und de-, aktivieren.</i></span></a>');
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list"><span id="startbox"><b>Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle Menues zum Bearbeiten, De-, Aktivieren und Löschen auflisten.</i></span></a>');
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
