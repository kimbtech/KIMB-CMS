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

//Diese Datei wird ausgeführt, wenn ein Backend-User die Rechte 
//zum Durchführen von Updates hat.

//HTTP Requests müssen PHP erlaubt sein
if( ini_get('allow_url_fopen') ) {

	//Update-Datei lesen
	$updatefile = new KIMBdbf( 'addon/auto_update__info.kimb' );

	//Zeit der letzten Überprüfung
	$lasttime = $updatefile->read_kimb_one( 'lasttime' );

	//noch gar keine Überprüfung?
	if( empty( $lasttime ) ){
		//Werte in dbf schreiben
		//	sehr sehr lange her
		$updatefile->write_kimb_new( 'lasttime', '100' );
		//	kein Update
		$updatefile->write_kimb_new( 'lastanswer', 'no' );
		//	keine neue Version
		$updatefile->write_kimb_new( 'newv', 'none' );
	}
	
	//alle 3 Tage testen
	if( $lasttime + 259200 < time() ){

		//aktuellen Zeitpunkt für letzte Überprüfung einsetzen
		$updatefile->write_kimb_replace( 'lasttime', time() );

		//prüfen
		require_once( __DIR__.'/check.php' );

		//Antworten in dbf schreiben
		$updatefile->write_kimb_replace( 'lastanswer', $update );
		$updatefile->write_kimb_replace( 'newv', $updatearr['newv'] );
	}
	else{
		//wenn letzte Überprüfung noch gültig, Update ja/nein auslesen
		$update = $updatefile->read_kimb_one( 'lastanswer' );
	}

	//wenn Update nötig, jQuery-UI Dialog anzeigen
	if( $update == 'yes' ){

		//JavaScript Code für Dialog
		$sitecontent->add_html_header('<script>
		$(function() {
			if ( window.sessionStorage.getItem( "cms_update" ) != "info" ){
				$( "#auto_update" ).show( "fast" );
				$( "#auto_update" ).dialog({
				resizable: false,
				height: 250,
				modal: true,
				buttons: {
					"Update": function() {
						$( this ).dialog( "close" );
						window.sessionStorage.setItem( "cms_update" , "info" );
						window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=auto_update";
						return true;
					},
					"Später": function() {
						$( this ).dialog( "close" );
						window.sessionStorage.setItem( "cms_update" , "info" );
						return false;
					}
				}
				});
			}
		});
		</script>');

		//HTML-Code für Dialog
		$sitecontent->add_site_content('<div style="display:none;"><div id="auto_update" title="CMS Update verfügbar!"><p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 50px 0;"></span>Es ist eine neue Version des KIMB-CMS verfügbar, möchten Sie das Update gleich durchführen?</p></div></div>');

	}
	
	//Datei schleßen und Änderungen übernehmen!
	unset( $updatefile );
}
?>
