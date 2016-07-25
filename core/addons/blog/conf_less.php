<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2016 by KIMB-technologies
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

//URL zur Konf less
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=blog';

//Add-on nötig Datei laden
require_once( __DIR__.'/required_addons.php' );

//Konf Datei laden
$cffile = new KIMBdbf( 'addon/blog__conf.kimb' );
//	Werte lesen
$menue = $cffile->read_kimb_one( 'menue' );
//	Werte prüfen
if( empty( $menue ) || !is_numeric( $menue ) ){
	$sitecontent->echo_error( 'Die Konfiguration des Add-ons ist fehlerhaft! (menue)' );
}
else{

	//Seiten Klasse laden
	$o_site = new BEsites($allgsysconf, $sitecontent, false );

	//neuen Artikel schreiben?
	if( isset( $_GET['new'] ) && !empty( $_POST['titel'] ) ){

		//Seite machen
		//=> Klasse oben geladen!
		//	Vars vorbreiten
		$POST = array(
			//Inhalte
			'title' => $_POST['titel'], 
			'inhalt' => '<h1>'.$_POST['titel'].'</h1>',
			//kein EasyMenü
			'menue' => 'none',
			'untermenue' => 'none',
			//Rest leer
			'header' => '',
			'keywords' => '',
			'description' => '',
			'footer' => ''
		);
		//machen
		$sit = $o_site->make_site_new_dbf( $POST );
		//	gleich deaktivern
		$o_site->make_site_deakch( $sit );

		//Menü machen
		//	Menü Klasse laden
		$o_menue = new BEmenue($allgsysconf, $sitecontent, false );
		//	Vars vorbereiten
		$GET = array( 
			//Menü des Blogteils wählen
			'file' => $menue,
			//geleiche Ebene
			'niveau' => 'same',
			//unwichtig, da geleiche Ebene
			'requid' => '1'
		);
		$POST = array( 
			//nach Eingabe
			'name' => $_POST['titel'],
			//passende SeitID
			'siteid' => $sit
		);
		//machen
		$men = $o_menue->make_menue_new_dbf( $GET, $POST, 'off' );

		//Gästebuch anfügen
		//	konf Datei laden
		$guestfile = new KIMBdbf( 'addon/guestbook__conf.kimb' );
		//	neue Seite anfügen
		$guestfile->write_kimb_teilpl( 'siteid' , $sit , 'add' );
		/* Ich weiß, dies ist nicht gerade schön, einfach die Konfiguration abzuändern, aber es funktioniert. */

		//FullHTMLCache auf dieser Seite aus
		//	aktuelle Daten lesen
		$data = $api->full_html_cache_wish( 'read' );
		if( is_array( $data['a'] ) ){
			$data = array();
		} 	
		//	für neue Seite auch komplett deaktivieren
		$data[$men['requestid']] = array(
			'off', array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() )
		);
		//	neues Array an API übergeben
		$api->full_html_cache_wish( 'set', $data ); 			

		//Ausgabe
		$sitecontent->echo_message( 'Es wurde ein neuer Blogartikel erstellt!', 'Erfolgeich');
		// => Seite füllen Link (ext)
		$sitecontent->add_site_content( '<p><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$sit.'" target="_blank">Artikel schreiben</a></p>' );
		// => veröffentlichen Link
		$sitecontent->add_site_content( '<p><a href="'.$addonurl.'&amp;newpub='.$sit.'">Artikel veröffentlichen</a></p>' );

	}
	//Seite veröffentlichen
	elseif( !empty( $_GET['newpub'] ) && is_numeric( $_GET['newpub'] ) ){

		//Seitenstatus auf aktiviert setzen!
		if( $o_site->make_site_deakch( $_GET['newpub'] ) ){
			//Ausgabe
			$sitecontent->echo_message( 'Der neue Blogartikel wurde veröffentlicht!', 'Erfolgeich');
			
			//Backend System Klasse laden
			$besys = new BEsyseinst( $allgsysconf, $sitecontent, new KIMBdbf( 'config.kimb' ) );
			//alle Caches leeren
			$besys->make_syseinst_cachedel();
		}
		else{
			//Ausgabe
			$sitecontent->echo_error( 'Der neue Blogartikel konnte nicht veröffentlicht werden!' );
			// => veröffentlichen Link
			$sitecontent->add_site_content( '<p><a href="'.$addonurl.'&amp;newpub='.$_GET['newpub'].'">Artikel veröffentlichen</a></p>' );
		}

	}
	//
	else{

		//Neuen Artikel Button
		$sitecontent->add_site_content( '<h3>Artikel schreiben</h3>');
		$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon=blog&amp;new" method="POST">' );
		$sitecontent->add_site_content( '<p><input type="text" name="titel" placeholder="Titel"></p>');
		$sitecontent->add_site_content( '<p><input type="submit" value="Anfangen"></p>');
		$sitecontent->add_site_content( '</form>');

	}
}
?>