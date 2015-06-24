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

defined('KIMB_CMS') or die('No clean Request');

//Sofern Seite gefunden, nach Seitendatei suchen und lesen
if( $allgerr != '404' ){
	if( check_for_kimb_file( '/site/site_'.$allgsiteid.'.kimb' ) ){
		$sitefile = new KIMBdbf( '/site/site_'.$allgsiteid.'.kimb' );
	}
	else{
		//keine Seitendatei -> Fehler
		$sitecontent->echo_error( $allgsys_trans['make_content']['err01'] , '404' );
		$allgerr = '404';
	}
}

//bei Fehler 403 keine Seite anzeigen (Fehler wird von Add-ons oder per Aufruf von index.php?id=err403 erzeugt)
if( $allgerr == '403' ){
	$sitecontent->echo_error( $allgsys_trans['make_content']['err02'] , '403' );
}
//es sollten keine Fehler gesetzt sein und natülich muss die Seitendatei geladen sein
elseif( is_object( $sitefile ) && !isset( $allgerr ) ){
	//Seitendaten lesen und ausgeben
	
	//Bei mehrsprachigen Seiten, die richige Version wählen
	if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
		
		//Die Seiteninhalt werden ja nach Sprache mit einem anderen Tag abgespeichert.
		//Hier wird der zu lesende Tag mit Hilfe der SprachID definiert
		$dbftag['title'] = 'title-'.$requestlang['id'];
		$dbftag['keywords'] = 'keywords-'.$requestlang['id'];
		$dbftag['description'] = 'description-'.$requestlang['id'];
		$dbftag['inhalt'] = 'inhalt-'.$requestlang['id'];
		$dbftag['footer'] = 'footer-'.$requestlang['id'];
		
		//Nicht jede Seite ist in alle Sprachen übersetzt, sollte dies nicht der Fall sein, wird eine Meldung angezeit und die Seite in der Standardsprache geladen 
		if( empty($sitefile->read_kimb_one( $dbftag['title'] )) && empty($sitefile->read_kimb_one( $dbftag['inhalt'] )) ){
			//Meldung
			$sitecontent->echo_error( $allgsys_trans['make_content']['err03'], 'unknown', $allgsys_trans['make_content']['err04'] );
			//normale Tags nutzen
			$normtags = true;
		}
	}
	else{
		//normsle Tags nutzen
		$normtags = true;
	}
	
	if( $normtags ){
		//normale Tags laden
		$dbftag['title'] = 'title';
		$dbftag['keywords'] = 'keywords';
		$dbftag['description'] = 'description';
		$dbftag['inhalt'] = 'inhalt';
		$dbftag['footer'] = 'footer';	
	}
		
	//alle Seiteninhalte lesen und in Array speichern
	//header, time, made_user und ID haben keine Sprache
	$seite['title'] = $sitefile->read_kimb_one( $dbftag['title'] );
	$seite['header'] = $sitefile->read_kimb_one( 'header' );
	$seite['keywords'] = $sitefile->read_kimb_one( $dbftag['keywords'] );
	$seite['description'] = $sitefile->read_kimb_one( $dbftag['description'] );
	$seite['inhalt'] = $sitefile->read_kimb_one( $dbftag['inhalt'] );
	$seite['time'] = $sitefile->read_kimb_one( 'time' );
	$seite['made_user'] = $sitefile->read_kimb_one( 'made_user' );
	$seite['footer'] = $sitefile->read_kimb_one( $dbftag['footer'] );
	$seite['req_id'] = $_GET['id'];

	//das Array mit den Seiteninhalten zur Ausgabe geben
	$sitecontent->add_site($seite, $allgsys_trans['output']);

}
//wenn noch kein Fehler vorhanden ist und man hier landet, ist irgendwas beim laden der Seiteninhalt falsch gelaufen
//	User informieren
elseif( !isset( $allgerr ) ){
	$sitecontent->echo_error( $allgsys_trans['make_content']['err05'] );
	$allgerr = 'unknown';
}
?>