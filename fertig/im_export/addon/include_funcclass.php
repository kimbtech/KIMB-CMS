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

//make_cms_export( array $teile );
//	array $teile => möglich: 'sites', 'menue', 'users', 'addon', 'confs','filem'
//	Rückgabe => Pfad zur Exportdatei oder 'fileerror'
//		CMS Export Datei machen
function make_cms_export( array $teile ){
	global $allgsysconf;

	//ZIP Datei erstellen
	$zip = new ZipArchive();
	//Dateiname
	$filename = __DIR__.'/exporte/export_'.date("dmY").'_'.time().'.zip';
	//Datei öffnen
	if ( $zip->open( $filename , ZIPARCHIVE::CREATE) !== TRUE ) {
		//Fehler wenn nicht klappt
		return 'fileerror';
	}

	//Ordner der dbf
	$dbfroot = __DIR__.'/../../oop/kimb-data/';

	//allgemeines zum System
	$infojson['allg'] = array( 'date' => date( 'dmY' ), 'time' => time(), 'sysver' => $allgsysconf['build'], 'sysname' => $allgsysconf['sitename'] );

	//alle Add-ons in Array
	foreach( listaddons() as $addon ){
		$infojson['addons'][] = $addon;
	}

	//Seiten machen?
	if( in_array( 'sites', $teile ) ){

		//alle Seitendateien in ZIP
		zip_r( $zip, $dbfroot.'site/', '/sites/' );

		//Seiten gemacht
		$infojson['done'][] = 'sites';
	}
	//Menue machen?
	if( in_array( 'menue', $teile ) ){

		//Menüdateien in ZIP
		zip_r( $zip, $dbfroot.'menue/', '/menue/menue/' );

		//URL-Dateien in ZIP
		zip_r( $zip, $dbfroot.'url/', '/menue/url/' );
		
		//EasyMenue Datei in ZIP
		$zip->addFile( $dbfroot.'backend/easy_menue.kimb' , '/menue/easy_menue.kimb' );

		//Menue gemacht
		$infojson['done'][] = 'menue';
	}
	//User machen?
	if( in_array( 'users', $teile ) ){

		//Userdateien in ZIP
		zip_r( $zip, $dbfroot.'/backend/users/', '/users/' );

		//User gemacht
		$infojson['done'][] = 'users';
	}
	//Add-on machen?
	if( in_array( 'addon', $teile ) ){

		//Add-on Dateien in ZIP
		zip_r( $zip, $dbfroot.'addon/', '/addon/' );

		//Includes löschen (alle Add-ons deaktivieren)
		$zip->deleteName( '/addon/includes.kimb' );

		//Add-on gemacht
		$infojson['done'][] = 'addon';
	}
	//Konfiguration machen?
	if( in_array( 'confs', $teile ) ){

		//Seitename und Description in Array
		$infojson['confs']['sitename'] = $allgsysconf['sitename'];
		$infojson['confs']['description'] = $allgsysconf['description'];

		//Datei mit 404 & 403 & Footer
		$zip->addFile( $dbfroot.'sonder.kimb' , '/confs/sonder.kimb' );

		//Konfiguration gemacht
		$infojson['done'][] = 'confs';
	}
	//Filemanager Dateien machen?
	if( in_array( 'filem', $teile ) ){

		//Filemanager Datei für sichere Dateien mit Keys in ZIP
		$zip->addFile( $dbfroot.'backend/filemanager.kimb' , '/filem/filemanager.kimb' );

		//Dateien des offenen Orders in ZIP
		zip_r( $zip, __DIR__.'/../../../load/userdata/', '/filem/open/' );

		//Dateien des sicheren Ordners in ZIP
		zip_r( $zip, __DIR__.'/../../secured/', '/filem/close/' );

		//Filemanager gemacht
		$infojson['done'][] = 'filem';	
	}

	//alle Daten zur Exportdatei sind im Array $infojson
	//dieses Array zu einer JSON-Datei machen und 
	//in die ZIP Datei
	$zip->addFromString( '/info.jkimb' , json_encode( $infojson ) );

	//ZIP Datei schließen
	$zip->close();
	
	//Dateinamen ausgeben (Pfad)
	return $filename;	
}

//ftp_push_export( $upfile, $topath , $dat );
//	$upfile => Pfad zur Datei, die hochgeladen werden soll (Name bleibt auf Server gleich) 
//	$topath => Pfad auf dem Server, in dem die Datei gespeichert werden soll (Pfad muss vorhanden sein)
//	$dat => Array( 'server' => 'Namen des Servers (URL)', 'name' => 'Username', 'pass' => 'Passwort' );
//	Rückgabe => true/ false
//		Exportdateien des CMS auf einen FTP Server laden, andere Dateien sind auch möglich, erhalten aber die Dateiendung *.kimbex
function ftp_push_export( $upfile, $topath , $dat ){
	
	//FTP-Verbindung herstellen
	$conn = ftp_connect($dat['server']);
	//auf Server einloggen
	$login = ftp_login($conn, $dat['name'], $dat['pass']);

	//Pfad für Uploaddatei bestimmen
	$remote = '/'.$topath.'/'.basename( $upfile, '.zip' ).'.kimbex';

	//hochladen (Binary Mode)
	//	Rückgabe entsprechend
	if( ftp_put($conn, $remote, $upfile, FTP_BINARY) ) {
		$ret = true;
	}
	else{
		$ret = false;
	}

	//FTP Verbindung beenden
	ftp_close($conn);
	
	//Rückgabe
	return $ret;
}

?>