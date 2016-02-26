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

//Datei mit Cron-Infos laden
$imexfile = new KIMBdbf( 'addon/im_export__cron.kimb' );

//Ist Cron aktiviert?
if( $imexfile->read_kimb_one( 'coo' ) == 'on' ){

	//Exportdatei (alles bis auf filemanager)
	$teile = array( 'sites', 'menue', 'users', 'addon', 'confs' );
	
	//Export durchführen
	$file = make_cms_export( $teile );
	
	//Fehler?
	if( $file == 'fileerror' ){
		//Fehlermeldung
		//	Alle Ausgeben in array $infos
		$infos[] = ('Die Exportdatei konnte nicht erstellt werden!');
	}
	else{
		//	Export okay
		$infos[] = ('Export erfolgreich!');
		
		//FTP Upload?
		
		//FTP-Daten lesen
		$dat = $imexfile->read_kimb_id( '21' );
		//Ordner auf Server lesen
		$topath = $imexfile->read_kimb_one( 'topath' );
		
		//erstmal ftp okay
		$ftp = true;
		//Ordner auf Server darf nicht leer sein
		if( empty( $topath ) ){
			$ftp = false;
		}
		//Server muss gegeben sein
		if( empty( $dat['server'] ) ){
			$ftp = false;
		}
		//Username muss gegeben sein 
		if( empty( $dat['name'] ) ){
			$ftp = false;
		}
		//Passwort muss gegeben sein
		if( empty( $dat['pass'] ) ){
			$ftp = false;
		}
		
		//Ist alles für FTP-Upload okay?
		if( $ftp ){
			//Upload begonnen Meldung
			$infos[] = ('Upload begonnen!');
			
			//Upload ausführen
			if( ftp_push_export( $file, $topath , $dat ) ){
				//okay Meldung
				$infos[] = ('Upload erfolgreich!');
			}
			else{
				//Fehlermeldung
				$infos[] = ('Upload fehlerhaft!');
			}
		}
		
	}
}
else{
	//Kein Export per Cron gewünscht
	$infos[] = ('Export deaktivert!');
}

?>