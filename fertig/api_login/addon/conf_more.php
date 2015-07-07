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

//Add-on Handling
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=api_login';

//Infotext
$sitecontent->add_site_content( 'Generieren Sie hier einen Datei, die es ermöglicht den Login des CMS auch auf anderen Seiten zu nutzen.<br />' );
$sitecontent->add_site_content( '<b>Die Systeme (CMS und zu sichernde Seite) müssen hier <u>nicht</u> auf dem gleichen Server und der gleichen Domain liegen.</b><br />' );

//dbf des Add-ons öffnen
$file = new KIMBdbf( 'addon/api_login__dest.kimb' );

//cURL vorhanden?
if( !function_exists('curl_version') ){
	//Fehlermedlung
	$sitecontent->echo_error( 'Diese Funktion benötigt cURL!','' , 'Abhängigkeit' );
}
else{
	//Aufgabe machen?
	
	//externe Seite löschen?
	if( is_numeric( $_GET['del'] ) ){

		//Wert für Seite lesen
		$read = $file->read_kimb_one( 'dest'.$_GET['del'] );
		//Seite vorhanden?
		if( !empty( $read ) ){
			//Seite löschen
			$file->write_kimb_replace( 'dest'.$_GET['del'], 'none' );
			//Medlung
			$sitecontent->echo_message( 'Das externe System "'.$_GET['del'].'" wurde gelöscht!' );
		}

	}
	//Datei herunterladen?
	elseif( is_numeric( $_GET['file'] ) ){

		//Datei mit Platzhaltern laden
		$dat = file_get_contents( __DIR__.'/example_dest.php' );

		//Werte für Datei aus dbf lesen
		//	Auth Code damit CMS sich auf externer Seite authentifizieren kann
		$auth = $file->read_kimb_one( 'auth' );
		//	URL der externen Seite
		$dest = $file->read_kimb_one( 'dest'.$_GET['file'] );
		$dest = substr( $dest , '0', '-'.strlen(strrchr( $dest , '/')));

		//Platzhalter und Ersetzung
		$search = array( '<<[[auth]]>>', '<<[[sysurl]]>>' );
		$replace = array( "'".$auth."'", "'".$dest."'" );

		//Download HTTP Header setzen
		header("Content-Type: application/force-download");
		header("Content-type: text/php");
		header('Content-Disposition: attachment; filename= api_login_ext.php');

		//Datei mit Ersetzungen ausgeben
		echo str_replace( $search, $replace, $dat );
		//beenden
		die;

	}
	//neues externes System?
	elseif( isset( $_POST['url'] ) ){

		//In der dbf beginnen die Daten mit dem Index 1 
		$i = 1;
		//noch nicht fertig
		$fertig = 'no';
		//Schleife solange noch nicht fertig
		while( $fertig != 'yes' ){	
			//Wert für aktuelle ID/Index lesen
			$read = $file->read_kimb_one( 'dest'.$i );
			//Ist der Wert leer?
			if( empty( $read ) ){
				//neu schreiben
				$file->write_kimb_new( 'dest'.$i, $_POST['url'] );
				//jetzt fertig
				$fertig = 'yes';
				//Meldung
				$sitecontent->echo_message( 'Das externe System "'.$i.'" wurde hinzugefügt!' );
			}
			//Ist der Wert schon mal gelöscht worden (löschen setzt Wert auf none)
			elseif( $read == 'none' ){
				//none mit neum Wert ersetzen
				$file->write_kimb_replace( 'dest'.$i, $_POST['url'] );
				//jetzt fertig
				$fertig = 'yes';
				//Medlung
				$sitecontent->echo_message( 'Das externe System "'.$i.'" wurde hinzugefügt!' );
			}
			//nächste ID/Index probieren
			$i++;
		}

	}

	//JavaScript Code Löschen Dialog
	$sitecontent->add_html_header('<script>
	var del = function( id ) {
		$( "#del-api_login_ext" ).show( "fast" );
		$( "#del-api_login_ext" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$addonurl.'&del=" + id;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	</script>');

	//Auth Code setzten wenn nicht gesetzt
	if( empty( $file->read_kimb_one( 'auth' ) ) ){
		$file->write_kimb_new( 'auth', makepassw( 100 , '', 'numaz' ) );
	}

	//externe Seiten Listen
	$sitecontent->add_site_content( '<h3>Liste von externen Systemen/Seiten</h3>' );

	//Formular beginnen
	$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post">');
	//Tabelle beginnen
	$sitecontent->add_site_content('<table width="100%"><tr> <th>Link <span class="ui-icon ui-icon-info" title="Diesem Link müssen User zu den entsprechenden anderen Systemen folgen."></span></th> <th>Externes Ziel</th> <th width="20px;"></th> </tr>');

	//damit Schleife startet nicht leer
	$read = 'test';
	//los geht's mit ID/Index 1
	$i = 1;
	//Schleife bis keine Werte mehr
	while( !empty( $read ) ){
		//Wert für ID/Index lesen	
		$read = $file->read_kimb_one( 'dest'.$i );
		//Wert darf nicht leer oder none sein
		if( !empty( $read ) && $read != 'none' ){
			//Löschen Button
			$del = '<span onclick="var delet = del( \''.$i.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses externe System löschen."></span></span>';
			//Link um Datei für Seite anzuzeigen
			$del .= '<a href="'.$addonurl.'&amp;file='.$i.'"><span class="ui-icon ui-icon-document" title="Die Datei für das externe System herunterladen."></span></a>';
			//Tabellenzeile
			$sitecontent->add_site_content('<tr> <td>'.$allgsysconf['siteurl'].'/ajax.php?addon=api_login&amp;id='.$i.'</td> <td>'.$read.'</td> <td>'.$del.'</td> </tr>');
		}
		//nächste ID/Index 
		$i++;
		
	}
	//neue Seite (URL) hinzufügen
	$sitecontent->add_site_content('<tr> <td>Hinzufügen:</td> <td><input type="url" style="width: calc( 100% - 3px );" name="url" placeholder="Ziel URL (http://xxx.xx/api_login_ext.php)"></td> <td><input type="submit" value="Neu"></td> </tr>');
	$sitecontent->add_site_content('</table></form>');

	//HTML Code für Löschen Dialog
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-api_login_ext" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie dieses externe System wirklich löschen?</p></div></div>');

	//Nutzungshinweise
	$sitecontent->add_site_content( '<br /><h3>Nutzung</h3>' );

	$sitecontent->add_site_content( '<ol>' );
	$sitecontent->add_site_content( '<li>Fügen Sie oben einen Link auf den externen Server, mit dem zu sichernden System, zu einer Datei "api_login_ext.php" hinzu.</li>' );
	$sitecontent->add_site_content( '<li>Laden Sie über das kleine Icon rechts in der Tabelle die Datei "api_login_ext.php" herunter.</li>' );
	$sitecontent->add_site_content( '<li>Packen Sie die Datei "api_login_ext.php" auf Ihren Server, so dass sie über den in 1. angegebenen Link zu erreichen ist.<br /><i>Die Datei muss die Rechte haben eine "inhalte.php" im gleichen Verzeichnis zu erstellen.</i></li>' );
	$sitecontent->add_site_content( '<li>Passen Sie evtl. die Variable "$sysurl" an.<br /><i>Unter dieser URL sollte sich das zu sichernde System befinden.</i></li>' );
	$sitecontent->add_site_content( '<li>Gehen Sie nach Nutzung -> api_login (Link oben rechts) und sichern Sie Ihr System wie angegeben.</li>' );
	$sitecontent->add_site_content( '<li>Über den in der Tabelle angezeigten Link können eingeloggte User des CMS die externe Seite erreichen.</li>' );
	$sitecontent->add_site_content( '</ol>' );
}

?>
