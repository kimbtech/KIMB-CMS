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

//Medlung
$infos[] = 'Die Sitemap wird erstellt!';

//Sitemap beginnen
$map = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://w3.org/1999/xhtml">'."\r\n";

//Menuearray	
$array = make_menue_array_helper();

if( $allgsysconf['lang'] == 'on' ){
	//Sprachen
	//	Sprachdatei lesen
	$langfile = new KIMBdbf( 'site/langfile.kimb' );
	//	als Array laden
	$langs = $langfile->read_kimb_id_all();
	//	nur Tags und IDs
	$alllangs = array();
	foreach( $langs as $id => $data ){
		$alllangs[$id] = $data['tag']; 
	}
}
else{
	//keine Sprache
	$alllangs = array( 
		0 => ''
	 );
}

//Array durchgehen
foreach( $array as $val ){
	
	//nur aktivierte Menüpunkte	
	if ( $val['status'] == 'on' ){
		
		//URL herausfinden
		$url = make_path_outof_reqid( $val['requid'] );
		//Seite des Menüpunktes laden	
		$file = new KIMBdbf( 'site/site_'.$val['siteid'].'.kimb' );
		//Änderungszeitpunkt lesen
		$time = date( 'Y-m-d' , $file->read_kimb_one( 'time' ) );

		//alles der Map anfügen
		$map .= "\t".'<url>'."\r\n";
		//	Sprache
		foreach( $alllangs as $id => $tag ){
			//URL machen
			if( $allgsysconf['urlrewrite'] == 'on' ){
				//Tag vorne in URL
				$sprurl = $allgsysconf['siteurl']. ( !empty( $tag ) ? '/'.$tag : '' ).$url;
			}
			else{
				//Tag als ID anhängen
				$sprurl = $allgsysconf['siteurl'].$url.'&amp;langid='.$id;
			}

			//Standardsprache ?
			if( $id == 0 ){
				$map .= "\t\t".'<loc>'.$sprurl.'</loc>'."\r\n";
			}
			//Alternativsprache
			else{
				//nur Anfügen, wenn Übersetzung existiert!
				if( !empty( $file->read_kimb_one( 'title-'.$id ) ) ){
					$map .= "\t\t".'<xhtml:link rel="alternate" hreflang="'.$tag.'" href="'.$sprurl.'" />'."\r\n";
				}
			}
		}
		$map .= "\t\t".'<lastmod>'.$time.'</lastmod>'."\r\n";
		$map .= "\t".'</url>'."\r\n";
	}
}

//Map beenden
$map .= '</urlset> '."\r\n";

//Meldung
$infos[] = 'Die Sitemap wurde erstellt!';

//Sitemap speichern & Medlung
$file = fopen( __DIR__.'/../../../sitemap.xml' , 'w+' );
if( fwrite( $file , $map ) && !empty( $map ) ){
	$infos[] = 'Die Sitemap wurde gespeichert!';
}
else{
	$infos[] = 'Die Sitemap konnte nicht gespeichert werden!';
}
fclose( $file );

?>