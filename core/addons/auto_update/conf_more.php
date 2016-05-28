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

//Add-on URL zur vereinfachten Handhabung
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=auto_update';

//Updatedatei schon geladen?
if( !is_object( $updatefile ) ){
	$updatefile = new KIMBdbf( 'addon/auto_update__info.kimb' );
}

//Wann war die letzte Überprüfung?
$lasttime = $updatefile->read_kimb_one( 'lasttime' );
//noch gar keine Überprüfung durchgeführt?
if( empty( $lasttime ) ){
	//erstmal Werte für Check auf keine Update und lange zuvor setzen
	$updatefile->write_kimb_new( 'lasttime', '100' );
	$updatefile->write_kimb_new( 'lastanswer', 'no' );
	$updatefile->write_kimb_new( 'newv', 'none' );
}

//Ist der Ordner /temp/ vorhanden?
if( !is_writable( __DIR__.'/temp/' ) || !is_dir( __DIR__.'/temp/' )  ){
	//wenn nicht dann erstellen
	mkdir( __DIR__.'/temp/' );
	//Rechte einstellen
	chmod( __DIR__.'/temp/' , (fileperms( __DIR__ ) & 0777));
}

//HTTP Requests müssen PHP erlaubt sein
if( !ini_get('allow_url_fopen') ) {
	//Fehlermedlung
	$sitecontent->echo_error( 'PHP muss "allow_url_fopen" erlauben!' );
}
//Temp-Ordner muss schreibbar sein!
elseif( !is_writable( __DIR__.'/temp/' ) ){
	//Fehlermeldung
	$sitecontent->echo_error( 'Der Ordner "'.__DIR__.'/temp/" muss schreibbar sein!' );
}
//Will der User das Update schon duchführen
elseif( $_GET['task'] == 'update' ){

	//immer zurück zur Übersicht möglich
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Übersicht</a>');

	//Hinweis zu Gefahren von Updates
	$sitecontent->add_site_content('<h3>Update durchführen</h3>');
	$sitecontent->echo_message( 'Erstellen Sie vor jedem Update ein Backup um einem Datenverlust vorzubeugen!!<br />Add-ons werden nicht automatischt aktualisiert' );
	$sitecontent->add_site_content( '<br /><hr /><br />' );

	//URL anpassen
	$addonurl .= '&task='.$_GET['task'];

	//Update PHP einbinden
	require_once( __DIR__.'/updator.php' );

}
//User will erstmal die Übersicht sehen
else{
	//Text
	$sitecontent->add_site_content( '<h3>Übersicht</h3>');
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( 'Hier können Sie die Aktualität Ihres CMS überprüfen und wenn möglich ein Update durchführen.');
	$sitecontent->add_site_content( 'Alle 3 Tage wird bei einem Besuch des Backends automatisch eine Prüfung der Aktualität durchgeführt.');

	//Überprüfung nötig (alle 3 Tage) oder auf Wunsch des Users
	if( $lasttime + 259200 < time() || isset( $_GET['updstat'] ) ){

		//Überprüfung durchführen
		require_once( __DIR__.'/check.php' );

		//Ergebnis speichern
		//	Zeitpunkt
		$updatefile->write_kimb_replace( 'lasttime', time() );
		//	Update nötig?
		$updatefile->write_kimb_replace( 'lastanswer', $update );
		//	aktuelle Version
		$updatefile->write_kimb_replace( 'newv', $updatearr['newv'] );
		$lasttime = time();

	}
	else{
		//keine Überprüfung nötig
		
		//alles aus dbf lesen
		$update = $updatefile->read_kimb_one( 'lastanswer' );
		$updatearr['newv'] = $updatefile->read_kimb_one( 'newv' );
		$lasttime = $updatefile->read_kimb_one( 'lasttime' );
		$updatearr['sysv'] = $allgsysconf['build'];
	}

	//Übersichtstabelle
	$sitecontent->add_site_content('<ul>');
	//	CMS Version
	$sitecontent->add_site_content('<li>Version des Systems: '.$updatearr['sysv'].'</li>');
	//	aktuell verfügbare Version
	$sitecontent->add_site_content('<li>Aktuelle Version: '.$updatearr['newv'].'</li>');
	//	Zeitpunkt der Überprüfung
	$sitecontent->add_site_content('<li>Stand: '.date( 'd-m-Y H:i' , $lasttime).' <a href="'.$addonurl.'&updstat"><span class="ui-icon ui-icon-refresh" title="Status neu abfragen!" style="display:inline-block;"></span></a></li>');
	$sitecontent->add_site_content('</ul>');

	//wenn Update möglich, Link zum Udate
	if( $update == 'yes' ){
		$sitecontent->add_site_content('<br /><br />');
		$sitecontent->add_site_content('<a href="'.$addonurl.'&task=update"><button title="Führen Sie ein automatisches Update des CMS durch!" >Update starten</button></a>');
		$sitecontent->add_site_content('<br /><br />');
	}
	else{
		//kein Update verfügbar
		$sitecontent->add_site_content('<br /><br />');
		$sitecontent->add_site_content('<button disabled="disabled" onclick="return:false;">Kein Update verfügbar!</button>');
		$sitecontent->add_site_content('<br /><br />');
	}

	//alte Update Dateien aus dem Temp-Ordner löschen
	foreach( scandir( __DIR__.'/temp/' ) as $zip ){
		if( $zip != '.' && $zip != '..'){
			unlink( __DIR__.'/temp/'.$zip );
		}
	}
}

?>
