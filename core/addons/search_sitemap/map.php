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

//Cache aktiviert?
if( is_object($sitecache) ){

	//Cache der Sitemap anfragen
	$menuecache = $sitecache->get_cached_addon( $search_sitemap['searchsiteid'] , 'search_sitemap' );

	//Cache vorhanden?
	if( $menuecache != false ){
		//Cache der Ausgabe anfügen
		$sitecontent->add_site_content( $menuecache[0] );
		$cacheload = 'yes';
	}
	else{
		//kein Cache
		$cacheload = 'no';
	}

}
else{
	//kein Cache
	$cacheload = 'no';
}

//wenn kein Cache: neue Sitemap erstellen
if( $cacheload == 'no' ){

	//Dateien für make_menue_array()
	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	//Menue Array machen
	make_menue_array();
	
	//Sitemap beginnen
	$menue = '<ul id="sitemap">'."\r\n";

	//alle Menüpunkte durchgehen
	foreach( $menuearray as $menuear ){

		//nur aktivierte Menüpunkte zeigen
		if ( $menuear['status'] == 'on' ){
			
			//Niveau des Punktes
			$niveau = $menuear['niveau'];
	
			//letztes Niveau gesetzt?
			if( !isset( $thisniveau ) ){
				//wenn nein, dann erster Durchgang -> los mit li
				$menue .= '<li>';
			}
			//gleiches Niveau
			elseif( $thisniveau == $niveau ){
				//ein li beenden und neues öffnen
				$menue .= '</li>'."\r\n".'<li>';
			}
			//tieferes Menü
			elseif( $thisniveau < $niveau ){
				//neues Untermenü
				
				//wie viel tiefer?
				$i = 1;
				while( $thisniveau != $niveau - $i  ){
					$i++;
				}
				
				//entsprechend oft ul öffnen
				//neues li öffnen
				$menue .= str_repeat( "\r\n".'<ul>' , $i )."\r\n".'<li>';
				//Tiefe merken
				$thisulauf = $thisulauf + $i;
			}
			//höheres Menü
			elseif( $thisniveau > $niveau ){
				//Untermenü verlassen
				
				//wie viel höher?
				$i = 1;
				while( $thisniveau != $niveau + $i  ){
					$i++;
				}
				
				//li schließen
				//entsprechend oft ul schließen
				//neues li öffnen
				$menue .= '</li>'."\r\n".str_repeat( '</ul>'."\r\n" , $i ).'<li>'."\r\n";
				$thisulauf = $thisulauf - $i;
			}

			//Link zur Seite
			$menue .=  '<a href="'.$allgsysconf['siteurl'].make_path_outof_reqid( $menuear['requid'] ).'">'.$menuear['menuname'].'</a>';
			
			//aktuelles Menü wird jetzt letztes
			$thisniveau = $niveau;
		}
	}

	//Menü beenden
	//	li schließen
	//	alle offenen ul schließen
	//	ul von ganz vorne schließen
	$menue .= '</li>'."\r\n".str_repeat( '</ul>'."\r\n" , $thisulauf ).'</ul>'."\r\n";

	//Map ausgeben
	$sitecontent->add_site_content( $menue );

	//wenn Cache aktiviert, Ausgaben cachen
	if( is_object($sitecache) ){
		$sitecache->cache_addon( $search_sitemap['searchsiteid'] , $menue , 'search_sitemap' );
	}

}


?>