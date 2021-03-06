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

//Soll das Update gemacht werden?
//	Parameter "los"" enthält den Namen der Update-Datei, dieser ist eine Zufallszahl
if( !empty( $_GET['los'] ) && is_file( __DIR__.'/temp/'.$_GET['los'].'.zip' ) ){

	//Update-Datei öffnen
	$zip = new ZipArchive;
	if( $zip->open( __DIR__.'/temp/'.$_GET['los'].'.zip' ) === TRUE ){
		//wenn geöffnet in CMS Root entpacken
		$zip->extractTo( __DIR__.'/../../../' );
		$zip->close();

		//Update-datei löschen
		unlink( __DIR__.'/temp/'.$_GET['los'].'.zip' );

		//gibt es eine Update.php?
		if( is_file( __DIR__.'/../../../update.php' ) ){
			//diese ausführen
			require_once( __DIR__.'/../../../update.php' );
			//und löschen
			unlink( __DIR__.'/../../../update.php' );
		}

		//Meldungen
		$sitecontent->echo_message( 'Das Update wurde erfolgreich beendet!', 'Super!!' );
		$sitecontent->echo_message( 'Es wurden evtl. Add-ons deaktiviert oder auf das Standardtheme gewechselt!', 'Add-ons &amp; Themes' );
		$sitecontent->echo_message( 'Bitte überprüfen Sie, ob alles noch zuverlässig funktioniert.', '<em>Prüfen?</em>' );

		//Die letzte Überprüfung ist vielleicht noch jung, aber nach dem Update ungültig.
		//	Zeitpunkt ändern 
		$updatefile->write_kimb_replace( 'lasttime', '100' );
		
		//Build anpassen
		$conffile = new KIMBdbf('config.kimb');
		$conffile->write_kimb_id( '001', 'add', 'build', $_SESSION['update_zu'] );
		//neu laden
		$allgsysconf = $conffile->read_kimb_id('001');

	}
	else {
		//Fehlermedlung
		$sitecontent->echo_error( 'Die Datei konnte nicht entpackt werden!' );
	}
}
else{

	//noch keine Datei vorhanden
	
	//über die KIMB-API nach einer Update-Datei suchen
	$updinf = json_decode( file_get_contents( 'https://api.kimb-technologies.eu/cms/getupdatelink.php?v='.$allgsysconf['build'] ) , true );

	//keine Fehler bei der API?
	if( $updinf['err'] == 'no' ){

		//und ein Link übergeben?
		if( $updinf['link'] != 'none' ){

			//eine Zufallszahl für den Namen erstellen
			$ufile = mt_rand();

			//die Update-Datei auf dem Server öffnen
			$src = fopen( $updinf['link'] , 'r');
			//den Ort für die Update-Datei im CMS öffnen
			$dest = fopen( __DIR__.'/temp/'.$ufile.'.zip' , 'w+');
			//Datei herunterladen
			if( !stream_copy_to_stream( $src, $dest ) ){
				//wenn Fehler -> Fehlermeldung
				$sitecontent->echo_error( 'Download des Updates nicht möglich!' );
			}
			else{
				//sonst Zusammenfassung des Updates
				//Warnhinweise
				$sitecontent->add_site_content('<h3>Zusammenfassung:</h3>');
				$sitecontent->add_site_content('<table>');
				$sitecontent->add_site_content('<tr><th>Update von:</th><th>Update zu:</th></tr>');
				$sitecontent->add_site_content('<tr><td>'.$updinf['von'].'</td><td>'.$updinf['zu'].'</td></tr>');
				$sitecontent->add_site_content('<tr> <td></td> <td></td> </tr>');
				$sitecontent->add_site_content('<tr> <th>Infos zum Update: </th><td></td></tr>');
				$sitecontent->add_site_content('<tr> <td colspan="2"><div style="background-color:gray; border-radius:10px; padding:5px; color:white;" >'.nl2br( $updinf['hinw'] ).'</div></td></tr>');
				$sitecontent->add_site_content('</table>');
				$sitecontent->add_site_content('<div style="background-color:yellow; padding:20px; border-radius:15px; text-align:center;">');
				$sitecontent->add_site_content('<p><b style="color:red;">Ein Update birgt ein gewisses Risiko (z.B. CMS nicht mehr funktionstüchtig), bitte halten Sie ein Backup für den Fehlerfall bereit!</b></p>');
				$sitecontent->add_site_content('<a href="'.$addonurl.'&los='.$ufile.'"><button>OK, trotzdem weiter!</button></a>');				
				$sitecontent->add_site_content('</div>');
				
				$_SESSION['update_zu'] = $updinf['zu'];
			}

		}
		//weitere Fehlermeldungen
		else{
			$sitecontent->echo_error( 'Es ist ein Fehler aufgetreten!' );
			$sitecontent->echo_message( '<b>Fehlermedlung</b>: Kein passendes Update gefunden!' );
			$sitecontent->echo_message( '<b>Lösungsansatz</b>: Das Update muss manuell installiert werden!' );
		}

	}
	else{
		$sitecontent->echo_error( 'Es ist ein Fehler aufgetreten!' );
		$sitecontent->echo_message( '<b>Fehlermedlung</b>: '.$updinf['userinfo'] );
		$sitecontent->echo_message( '<b>Lösungsansatz</b>: '.htmlentities( $updinf['idea'] , ENT_COMPAT | ENT_HTML401,'UTF-8' ) );
	}
}
?>
