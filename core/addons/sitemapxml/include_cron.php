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
$map = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\r\n";

//Menuearray	
$array = make_menue_array_helper();

//Array durchgehen
foreach( $array as $val ){
	
	//nur aktivierte Menüpunkte	
	if ( $val['status'] == 'on' ){
		//URL herausfinden
		$url = $allgsysconf['siteurl']. make_path_outof_reqid( $val['requid'] );
		//Seite des Menüpunktes laden	
		$file = new KIMBdbf( 'site/site_'.$val['siteid'].'.kimb' );
		//Änderungszeitpunkt lesen
		$time = date( 'Y-m-d' , $file->read_kimb_one( 'time' ) );

		//alles der Map anfügen
		$map .= "\t".'<url>'."\r\n";
		$map .= "\t\t".'<loc>'.$url.'</loc>'."\r\n";
		$map .= "\t\t".'<lastmod>'.$time.'</lastmod>'."\r\n";
		$map .= "\t".'</url>'."\r\n";
	}
}

//Map beenden
$map .= '</urlset> '."\r\n";

//Meldung
$infos[] = 'Die Sitemap fertig erstellt!';

//Sitemap speichern & Medlung
$file = fopen( __DIR__.'/../../../sitemap.xml' , 'w+' );
if( fwrite( $file , $map ) && !empty( $map ) ){
	$infos[] = 'Die Sitemap wurde gespeichert!';
}
else{
	$infos[] = 'Die Sitemap konnte nicht  gespeichert werden!';
}
fclose( $file );

?>