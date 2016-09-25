<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies
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

defined('KIMB_CMS') or die('No clean Request');

//URL
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=code_terminal';

//Konfigurationsdatei
$html_out_konf = new KIMBdbf( 'addon/code_terminal__conf.kimb' );

//mögliche Aufgaben (URL-Paramter)
$tasks = array( 'fe', 'be', 'fc', 'tm' );
//Aufrufe nur für Gruppe more 
$mores = array( 'fc', 'tm' );

//Funktion für PHP Editor (CodeMirror)
function make_code_terminal_phpedi( $id ){
	global $allgsysconf, $sitecontent;

	//immer CodeMirror
	$sitecontent->add_html_header( '<script>window.localStorage.setItem( "editorloader", "codemirror" );</script>' );
	//Editor
	add_content_editor( $id, true );
	//	Editor PHP Lib
	$sitecontent->add_html_header( '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/code_terminal/codemirror_clike.js"></script>' );
	$sitecontent->add_html_header( '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/code_terminal/codemirror_php.js"></script>' );
	//	PHP Modus
	$sitecontent->add_html_header( '<script>$( function () { codemirrorloader_instances["'.$id.'"].setOption("mode", "php" ); });</script>' );
}

//Ist eine Ausgabe gewählt?
//Gibt es die Ausgabe überhaupt?
if( isset( $_GET['task'] ) && in_array( $_GET['task'], $tasks ) ){
	
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Zur Übersicht</a><br /><br />');
	
	//Gibt es die Datei für die Ausgabe?
	if( is_file( __DIR__.'/tasks/do_'.$_GET['task'].'.php' ) ){
		
		//benötigt der User Rechte der Gruppe more für die Ausgabe?
		if( in_array( $_GET['task'], $mores ) ){
			//Rechte more prüfen
			check_backend_login( 'no' , 'more' );
		}
		
		//URL für Aufgabe anpassen
		$addonurl = $addonurl.'&task='.$_GET['task'];
		
		//Datei mit Aufgabe laden
		require_once( __DIR__.'/tasks/do_'.$_GET['task'].'.php' );
	}
	else{
		$sitecontent->echo_error( 'Die Konfigurationsseite wurde nicht gefunden.', 'unknown', 'URL-Fehler' );
	}
}
else{

	//Fronted Codeeingabe
	$sitecontent->add_site_content('<h4>Frontend</h4>');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;task=fe"><button>Ausgabe anpassen</button></a><br /><br />');
	
	//Backend Codeeingabe
	$sitecontent->add_site_content('<h4>Backend</h4>');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;task=be"><button>Ausgabe anpassen</button></a><br /><br />');
	
	//nur User der Gruppe 'more' erlauben
	if( check_backend_login( 'no' , 'more', false ) ){
		
		//Funcclass Codeeingabe
		$sitecontent->add_site_content('<h4>Funcclass</h4>');
		$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;task=fc"><button>PHP anpassen</button></a><br /><br />');
		
		//Terminl
		$sitecontent->add_site_content('<hr /><h4>PHP-Terminal</h4>');
		$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;task=tm"><button>PHP ausführen</button></a><br /><br />');
	}
}

?>