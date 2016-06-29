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

//URL zu Add-on Konfiguration
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=blog';

//Add-on nötig Datei laden
require_once( __DIR__.'/required_addons.php' );

//wenn Rechte für less, Link für neuen Artikel erstellen anzeigen
if( check_backend_login( 'thirteen' , 'less', false) ){
	$sitecontent->add_site_content( '<center><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon=blog&amp;new"><button><h3>Artikel schreiben</h3></button></a></center>' );
}

$sitecontent->add_site_content( '<h3>Allgemeine Einstellungen</h3>' );

//Konf Datei laden
$cffile = new KIMBdbf( 'addon/blog__conf.kimb' );

//Hauptseite und Blogartikelmenü
if(
		!empty( $_POST['hauptseite'] )
	&&
		!empty( $_POST['menue'] )
){

	//überhaupt verändert?
	if( $cffile->read_kimb_one( 'hauptseite' ) != $_POST['hauptseite'] ){
		//Seiten IDs Array
		$sites = array_column( list_sites_array(), 'id'  );
		//Seite korrekt
		if( in_array( $_POST['hauptseite'], $sites ) ){
			if( $cffile->write_kimb_one( 'hauptseite', $_POST['hauptseite'] ) ){
				$omessage .= 'Hauptseite übernommen!<br />';
			}
			else{
				$emessage .= 'Konnte Hauptseite nicht speichern!<br />';
			}
		}
		else{
			$emessage .= 'Keine korrekte Hauptseite angegeben!<br />';
		}
	}

	//überhaupt verändert?
	if( $cffile->read_kimb_one( 'menue' ) != $_POST['menue'] ){
		//okay?
		if(
			//ID Nummer (nicht Haupmenü oder was falsches)
			is_numeric( $_POST['menue'] )
			&&
			//KIMB-Datei für Menü vorhaden?
			check_for_kimb_file( 'url/nextid_'.$_POST['menue'].'.kimb' )
		){
			if( $cffile->write_kimb_one( 'menue', $_POST['menue'] ) ){
				$omessage .= 'Blogartikelmenü übernommen!<br />';
			}
			else{
				$emessage .= 'Konnte Blogartikelmenü nicht speichern!<br />';
			}
		}
		else{
			$emessage .= 'Kein korrektes Blogartikelmenü angegeben!<br />';
		}
	}

	//überhaupt verändert?
	if( $cffile->read_kimb_one( 'addonarea' ) != $_POST['addonarea'] ){
		//okay?
		if(
			//nur on oder off okay
			$_POST['addonarea'] == 'on'
			||
			$_POST['addonarea'] == 'off'
		){
			//schreiben
			if( $cffile->write_kimb_one( 'addonarea', $_POST['addonarea'] ) ){
				$omessage .= 'Add-on Area Status übernommen!<br />';
			}
			else{
				$emessage .= 'Konnte Add-on Area Status nicht speichern!<br />';
			}
		}
		else{
			$emessage .= 'Keinen korrekten Add-on Area Status angegeben!<br />';
		}
	}



	//Fehler oder OK Meldung machen
	if( !empty( $omessage ) ){
		$sitecontent->echo_message( $omessage );
	}
	if( !empty( $emessage ) ){
		$sitecontent->echo_error( $emessage );
	}
}

//Form Felder wie in Konf setzen
$sitecontent->add_html_header('<script>$(function(){
	$( "select[name=hauptseite]" ).val( '.$cffile->read_kimb_one( 'hauptseite' ).' );
	$( "select[name=menue]" ).val( '.$cffile->read_kimb_one( 'menue' ).' );
	$( "input[name=addonarea][value='.$cffile->read_kimb_one( 'addonarea' ).']" ).prop( "checked", true );
});</script>');

//Formular für Einstellungen
$sitecontent->add_site_content( '<form action="'.$addonurl.'" method="post">' );

$sitecontent->add_site_content( '<table>' );

$sitecontent->add_site_content( '<tr>' );
$sitecontent->add_site_content( '<td>Hauptseite:</td>' );
$sitecontent->add_site_content( '<td>'.id_dropdown( 'hauptseite' , 'siteid' ).'</td>' );
$sitecontent->add_site_content( '<td>Seite auf der die Übersicht aktueller Artikel angezeit wird<br /><i>(Startseite)</i></td>' );
$sitecontent->add_site_content( '</tr>' );

$sitecontent->add_site_content( '<tr>' );
$sitecontent->add_site_content( '<td>Blogartikelmenü:</td>' );
$sitecontent->add_site_content( '<td>'.id_dropdown( 'menue' , 'fileid' ).'</td>' );
$sitecontent->add_site_content( '<td>Menüebene in der alle Blogartikel erscheinen sollen<br /><i>(muss ein Untermenü und schon vorhanden sein)</i></td>' );
$sitecontent->add_site_content( '</tr>' );

$sitecontent->add_site_content( '<tr>' );
$sitecontent->add_site_content( '<td>Add-on Area Status:</td>' );
$sitecontent->add_site_content( '<td><input type="radio" name="addonarea" value="on"> Aktiviert<br />' );
$sitecontent->add_site_content( '<input type="radio" name="addonarea" value="off"> Deaktivert</td>' );
$sitecontent->add_site_content( '<td>Nach Keywords passende Artikel und neuste Artikel neben den Artikeln zeigen</td>' );
$sitecontent->add_site_content( '</tr>' );

$sitecontent->add_site_content( '</table>' );

$sitecontent->add_site_content( '<input type="submit" value="Speichern">' );
$sitecontent->add_site_content( '</form>' );

?>