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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=sitemapxml';

/*

if( $_POST[$do] != $imexfile->read_kimb_one( $do ) ){
					
	//Wert anpassen
	$imexfile->write_kimb_one( $do, $_POST[$do] );
					
	//wurde die Zeit zwischen der Aufrufe des Cronjobs bearbeitet?
	//	Stunden müssen Zahlen sein
	if( $do == 'age' && is_numeric( $_POST[$do] ) ){
		//Stunden aus Übergabe in Sekunden umrechen
		$agesec = $_POST[$do]*3600;
						
		//Add-on API wish
		$a = new ADDonAPI( 'im_export' );
		$a->set_cron( $agesec );
	}
	//Meldung vorbereiten
	$message .= 'Der Wert "'.$do.'" wurde aktualisiert!<br />';
			
}
*/

$conffile = new KIMBdbf( 'addon/sitemapxml__conf.kimb' );

if( isset( $_GET['keygen'] ) ){

	$key = makepassw( 30, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );	

	if( empty( $conffile->read_kimb_one( 'key' ) ) ){
		$conffile->write_kimb_new( 'key', $key );
	}
	else{
		$conffile->write_kimb_replace( 'key', $key );
	}
	$sitecontent->echo_message( 'Es wurde ein neuer Key erstellt!' );
}

$key = $conffile->read_kimb_one( 'key' );

$sitecontent->add_site_content( '<h3>Links</h3>' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;key='.$key.'" target="_blank" >Neue Sitemap anschauen</a><br />' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;save&amp;key='.$key.'" target="_blank" >Neue Sitemap speichern <b title="Unter der URL: '.$allgsysconf['siteurl'].'/sitemap.xml" >*</b></a><br />' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;down&amp;key='.$key.'" target="_blank" >Neue Sitemap herunterladen</a><br />' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/sitemap.xml" target="_blank" >Aktuelle Sitemap ansehen</a><br />' );
$sitecontent->add_site_content( '<br />' );
$sitecontent->add_site_content( 'Key: <div style="background-color:#ccc; padding:10px; width:90%;" >'.$key.'</div><br />' );
$sitecontent->add_site_content( 'Cron URL: <div title="Nutzen Sie diese URL um mit einem Cron-Job regelmäßig vollkommen automatisch eine neue Sitemap zu generieren." style="background-color:#ccc; padding:10px; width:90%;" >'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;save&amp;key='.$key.'</div><br />' );
$sitecontent->add_site_content( '<br />' );

$sitecontent->add_site_content( '<hr /><a href="'.$addonurl.'&amp;keygen">Neuen Key generieren</a>' );



?>