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

//Laden der ID Datei, wenn nicht schon geschehen
if(!is_object( $idfile )){
	$idfile = new KIMBdbf('menue/allids.kimb');
}

//Überprüfung der RequestID und MenüID (muss zueinader passen, Add-on könnte rumgefuscht haben)
if($allgmenueid == $idfile->read_kimb_id($_GET['id'], 'menueid')){
	$allgrequestid = $_GET['id'];
}
else{
	//passt nicht zueinander, also etwas passendes erstellen
	$allgrequestid = $idfile->search_kimb_xxxid( $allgmenueid , 'menueid' );
}

//wenn Sprache aus, Standardsprache
if( $allgsysconf['lang'] == 'off' ){
	$requestlang['id'] = 0;
}

//Cache aktiviert?
if( $allgsysconf['cache'] == 'on' ){
	//versuchen den Cache zu laden
	if( $sitecache->load_cached_menue($allgmenueid, $requestlang['id'] ) ){
		//wenn erfolgreich, Menueerstellung fertig und merken
		$menuecache = 'loaded';
	}
}

//Menü erstellen, wenn nicht schon mit Cache geschehen
if( $menuecache != 'loaded'){

	//Sprache an und nicht Standardsprache?
	if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
		//Die Menünamen der Standardsprache und der gewählten Sprache laden
		//	wenn nicht übersetzt auf Standardsprache zurückfallen
		$menuenames = new KIMBdbf('menue/menue_names_lang_'.$requestlang['id'].'.kimb');
		$menuenameslangst = new KIMBdbf('menue/menue_names.kimb');
	}
	else{
		//Standardsprache reicht für beides!
		$menuenames = new KIMBdbf('menue/menue_names.kimb');
		$menuenameslangst = $menuenames;
	}

	//Breadcumb Array noch nicht fertig
	$breadarrfertig = 'nok';
	//Menüerstellung per Funktion
	//	Menü wird automatisch an Ausgabe gegeben
	//	Menü wird automatisch gecached
	gen_menue( $allgrequestid );

	//Breadcrumb aus Array erstellen
	$breadcrumblinks = '<div id="breadcrumb" >';

	//Das Nivau mitzählen (das Breadcrumb Array enthält nur bis zum 'maxniv' richtige Daten) und wenn
	//erreicht Schleife abbrechen
	$niveau = 1;
	while( $breadcrumbarr['maxniv'] >= $niveau ){
		//Breadcrumb erstellen
		//	jeweils Pfeil und dann Link mit Menünamen
		$breadcrumblinks .= ' &rarr; ';
		$breadcrumblinks .= '<a href="'.$breadcrumbarr[$niveau]['link'].'">'.$breadcrumbarr[$niveau]['name'].'</a>';

		$niveau++;
	}
	//feddig
	$breadcrumblinks .= '</div>';

	//wenn Cache geladen
	if( is_object($sitecache) ){
		//Breadcrumb im Cache speichern
		if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
			//Sprachen mit SprachID ablegen
			$sitecache->cache_addon( $allgmenueid , $breadcrumblinks , 'breadcrumb-'.$requestlang['id'] );
		}
		else{
			//Standardsprache ohne ID
			$sitecache->cache_addon( $allgmenueid , $breadcrumblinks , 'breadcrumb' );
		}
	}

}
else{
	//MenueCache geladen, Menü ist also fertig, nur Breadcrumb nicht
	//	Breadcrumb aus Cache laden
	if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
		//Sprachen mit SprachID lesen
		$breadcrumblinks = $sitecache->get_cached_addon( $allgmenueid , 'breadcrumb-'.$requestlang['id'] );
	}
	else{
		//Standardsprache ohne ID lesen
		$breadcrumblinks = $sitecache->get_cached_addon( $allgmenueid , 'breadcrumb' );
	}
	//Ausgabe des Caches als Array, nur ersten Teil verweden (Tag nur einmal vorhanden)
	$breadcrumblinks = $breadcrumblinks[0];
}

//Breadcrumb der Seite hinzufügen!!
$sitecontent->add_site_content( $breadcrumblinks );

?>
