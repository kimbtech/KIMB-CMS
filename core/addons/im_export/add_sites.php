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

//Hinweis
$sitecontent->echo_message( 'Erstellen Sie vor jedem Import ein Backup um einem Datenverlust vorzubeugen!!', 'Wichtig' );
//Trennung
$sitecontent->add_site_content( '<br /><hr /><br />' );

//Diese Datei fügt dem CMS Seiten aus einer Exportdatei hinzu!

//Datei gewählt?
if( isset( $_POST['file'] ) && ( !empty( $_FILES['exportfile']['name'] ) || !empty( $_POST['exportarchiv'] ) ) ){

	//Wurde eine Datei auf dem Server gewählt
	if( !empty( $_POST['exportarchiv'] ) ){
		//keine Punkte (sonst wäre ./../../ möglich!)
		if( strpos($_POST['exportarchiev'], '..') !== false ){
			echo 'Do not hack me!';
			die;
		}
		//Die Datei auf dem Server als hochgeladene ausgeben
		$_FILES["exportfile"]["tmp_name"] = __DIR__.'/exporte/'.$_POST['exportarchiv'].'.zip';
	}

	//Namen für Dateiordner
	$_SESSION['importfile'] = mt_rand();

	//Dateiordner Pfad
	$fileroot = __DIR__.'/temp/'.$_SESSION['importfile'].'/';

	//Datei als Zip nach Dateiordner entpacken
	$zip = new ZipArchive;
	if ( $zip->open($_FILES["exportfile"]["tmp_name"]) === TRUE ) {
		$zip->extractTo( $fileroot );
		$zip->close();
	}
	else{
		//Fehlermeldung wenns nicht klappt
		$sitecontent->echo_error('Konnte Exportdatei nicht verarbeiten!');
		$sitecontent->output_complete_site();
		die;
	}
	
	//JSON Infodatei lesen
	$jsoninfo = json_decode( file_get_contents( $fileroot.'/info.jkimb' ) , true );

	//Infos ausgeben
	$sitecontent->add_site_content( '<b>Über diese Exportdatei:</b><ul>' );
	$sitecontent->add_site_content( '<li>Exportsystem: '.$jsoninfo['allg']['sysname'].'</li>' );
	//Dateialter
	$sitecontent->add_site_content( '<li>Exportzeit: '.date( 'd.m.Y H:i' , $jsoninfo['allg']['time'] ).'</li>' );
	$sitecontent->add_site_content( '<li>Inhalte:<ul>' );
		//alles was gemacht wurde aus Array 
		foreach( $jsoninfo['done'] as $done ){
			$sitecontent->add_site_content( '<li>'.$done.'</li>' );
		}
		$sitecontent->add_site_content( '</ul></li>' );
	$sitecontent->add_site_content( '</ul>' );

	//Die Datei muss Seiten enthalten, denn hier werden nur Seiten hinzugefügt!
	if( !in_array( 'sites' , $jsoninfo['done'] ) ) {
		//Fehlermeldung wenn keine Seiten in Datei
		$sitecontent->echo_error(' Die Exportdatei enhält keine Seiten! ');
		$sitecontent->output_complete_site();
		die;
	}

	//Versionsvergeleich der CMS
	if( $jsoninfo['allg']['sysver'] != $allgsysconf['build'] ){
		$sitecontent->echo_message( 'Die Versionen dieses Systems und des Exportsystems stimmen nicht überein, dies kann zu Problemem führen, wenn Sie fortfahren.','Wichtig' );
	}

	//Button um Import zu starten
	$sitecontent->add_site_content( '<a href="'.$addonurl.'&amp;weiter='.$_SESSION['importfile'].'"><button>Import starten</button></a>' );
	//Hinweis
	$sitecontent->add_site_content( '<br />Mit dem Klick auf "Import starten" fügen Sie diesem CMS weitere Seiten hinzu. Die Seiten werden nicht automatisch einem Menü zugeordnet.' );
}
//soll der Importvorgang ausgeführt werden
elseif( $_GET['weiter'] == $_SESSION['importfile'] && !empty( $_GET['weiter'] ) ){

	//Dateiordner
	$fileroot = __DIR__.'/temp/'.$_SESSION['importfile'].'/';

	//gibt es den?
	if( is_dir( $fileroot ) ){

		//gibt es dort den Odner sites (hier sind die Seiten drin)
		if( is_dir( $fileroot.'/sites/' ) ){

			//alle Seitendateien auflisten
			$sitesex = scandir( $fileroot.'/sites/' );
			//Anzahl der hinzuzufügenden Seiten herausfinden 
			$sitesexin = count( $sitesex );

			//aktuelle SeitenID des CMS herausfinden
			$i = 1;
			while( true ){
				//gibt es eine Seite für die ID
				//	aktiviert oder deaktiviert
				if( !check_for_kimb_file( '/site/site_'.$i.'.kimb') && !check_for_kimb_file( '/site/site_'.$i.'_deak.kimb') ){
					//wenn keine Seite gefunden, freie ID merken
					$newids[] = $i;
				}
				//reichen die gefundenen freien IDs?
				if( $sitesexin <= count( $newids ) ){
					//wenn ja, Schleife verlassen
					break;
				}
				//nächste ID prüfen
				$i++;
			}

			//die Seitendateien nach und nach importieren
			//	Array $newids Key auf 0
			$ii = 0;
			//alle durchgehen
			foreach( $sitesex as $file ){

				//die Dateien index.kimb und langfile.kimb sollen nicht importiert werden!
				//auch . und .. ignorieren
				if( $file != '..' && $file != '.' && $file != 'index.kimb' &&  $file != 'langfile.kimb' ){
					//Datei soll importiert werden
					
					//Seitendatei aus Exportdatei an richtige Stelle mit richtiger ID für CMS speichern
					copy( $fileroot.'/sites/'.$file , __DIR__.'/../../oop/kimb-data/site/site_'.$newids[$ii].'.kimb' );
				
					//für die nächste Seite brauchenm wir die nächste freie ID
					$ii++;

				}

			}

			//Medlung wenn alles okay
			$sitecontent->echo_message(' Seiten erfolgreich importiert! ');
			//Hinweis
			$sitecontent->echo_message(' Die Seiten werden nicht automatisch einem Menü zugeordnet! ');
			//Zurück
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=im_export"><button>&larr; Zurück</button></a>');

			//entpackte Exportdatei löschen
			rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
		}
		else{
			//keine Seiten -> Fehler
			$sitecontent->echo_error(' Keine Seiten in der Exportdatei! ');
		}
	}
	else{
		//keine Datei -> Fehler
		$sitecontent->echo_error(' Keine Exportdatei! ');
	}
}
else{
	//Datei für Import wählen
	$sitecontent->add_site_content('Fügen Sie Seiten Ihrem CMS hinzu.');

	//Formular für Upload
	$sitecontent->add_site_content('<form action="'.$addonurl.'" enctype="multipart/form-data" method="post">');

	//Button für Upload
	$sitecontent->add_site_content('<input name="exportfile" type="file" />');
	$sitecontent->add_site_content('Exportdatei hochladen <span style="display:inline-block;" title="Bitte wählen Sie eine KIMB-CMS Export-Datei (*.kimbex)" class="ui-icon ui-icon-info"></span><br />');
	
	//alle Exporte auf dem Server laden
	$exps = scandir( __DIR__.'/exporte/', SCANDIR_SORT_DESCENDING );
	//ein Dropdown beginnen, erste Auswahl leer
	$dropdown = '<option value=""></option>';
	//alle anderen Exportdateien dem Dropdown anfügen
	foreach( $exps as $exp ){
				
		//keine . und ..
		if( $exp != '.' && $exp != '..'){
			
			//Timestamp aus Dateinamen herausfinden		
			$time = substr( $exp, 16, 10);
				
			//Datei dem Dropdown anfügen (Name: Zeit der Erstellung; Value: Dateiname)
			$dropdown .= '<option value="'.substr($exp, 0, -4 ).'">'.date( 'd.m.Y - H:i', $time).'</option>';
				
			//das Dropdown wurde gefüllt
			$liste = true;
		}	
	}
	//wenn Dropdown gefüllt
	if( $liste ){
		//Dropdown anzeigen
		$sitecontent->add_site_content('oder aus der Liste wählen');
		$sitecontent->add_site_content('<select name="exportarchiv">');
		$sitecontent->add_site_content( $dropdown );
		$sitecontent->add_site_content('</select>');
		
		//User soll entweder eine Exportdatei hochladen oder eine vom Server wählen
	}
	
	//Rest der Formulars
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="hidden" value="file" name="file">');
	//Submit Button
	$sitecontent->add_site_content('<input type="submit" value="Import starten" title="Bevor es los geht, erhalten Sie zuerst eine Übersicht über die Datei!">');
	$sitecontent->add_site_content('</form>');

	//wenn User schon eine Exportdatei hochgeladen hat (warum auch immer), diese löschen
	if( isset( $_SESSION['importfile'] ) ){
		rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
	}

}


?>
