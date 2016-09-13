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

//Konfiguration laden
$search_sitemap['file'] = new KIMBdbf( 'addon/search_sitemap__conf.kimb' );

//Seite für Sitemap laden
$search_sitemap['mapsiteid'] = $search_sitemap['file']->read_kimb_one( 'mapsiteid' ); // off oder id
//Seite für Suche laden
$search_sitemap['searchsiteid'] = $search_sitemap['file']->read_kimb_one( 'searchsiteid' ); // off oder id
//Soll überall ein Suchformular angezeigt werden?
$search_sitemap['searchform'] = $search_sitemap['file']->read_kimb_one( 'searchform' ); // on oder off

//Richtige Seite für Sitemap?
//kein Fehler 403?
if( $allgsiteid == $search_sitemap['mapsiteid'] && $allgerr != '403'){

	//Map laden
	require_once( __DIR__.'/map.php' );

}

//Suchformular an?
//Suche überhaupt aktiviert?
if( $search_sitemap['searchform'] == 'on' && $search_sitemap['searchsiteid'] != 'off' ){
	//Suchformular als Add-on Area
	
	//Überschrift
	$search_sitemap['searchform'] = '<h2>'.$allgsys_trans['addons']['search_sitemap']['suche'].'</h2>';
	//Formular
	$search_sitemap['searchform'] .= '<form method="post" action="'.$allgsysconf['siteurl'].'/index.php?id='.$search_sitemap['searchsiteid'].'">';
	//Eingabefeld
	$search_sitemap['searchform'] .= '<input type="text" name="search" placeholder="'.$allgsys_trans['addons']['search_sitemap']['suchb'].'" value="'.htmlentities( $_REQUEST['search'] , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'">';
	//Button
	$search_sitemap['searchform'] .= '<input type="submit" value="'.$allgsys_trans['addons']['search_sitemap']['suchn'].'">';
	//beenden
	$search_sitemap['searchform'] .= '</form>';

	//Ausgabe
	$sitecontent->add_addon_area( $search_sitemap['searchform'] );
}

//Richtige Seite für Suche?
//kein Fehler 403?
if( $_GET['id'] == $search_sitemap['searchsiteid'] && $allgerr != '403' ){

	//Suchbegriff aus Übergabe
	$begriff = $_REQUEST['search'];
	//Suchbegriff in den Header (Zugriffe für Bots und JavaScript möglich)
	$sitecontent->add_html_header( '<!-- SEARCH::'.json_encode( $begriff ).':: -->' );
	//Suchen einbinden
	require_once( __DIR__.'/search.php' );
	//Begriff wieder weg
	unset( $begriff );
}

//alles weg
unset( $search_sitemap );
?>
