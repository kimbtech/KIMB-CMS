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

//URL
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=im_export';

//User könnt vergessen das Add-on zu aktivieren (das meiste befindet sich halt im Backend unter Konfiguration)
if( !function_exists( 'make_cms_export' )){
	//Medlung
	$sitecontent->echo_error( 'Dieses Add-on ist nicht aktiviert, es kann zu Fehlern kommen!' );
	//Beim Export würde die Seite weiß werden (fatal error, undefined function)
	//Cron würde nicht klappen
}

//Ist der Ordner /temp/ vorhanden?
if( !is_writable( __DIR__.'/temp/' ) || !is_dir( __DIR__.'/temp/' )  ){
	//wenn nicht dann erstellen
	mkdir( __DIR__.'/temp/' );
	//Rechte einstellen
	chmod( __DIR__.'/temp/' , (fileperms( __DIR__ ) & 0777));
}
//Ist der Ordner exporte vorhanden?
if( !is_writable( __DIR__.'/exporte/' ) || !is_dir( __DIR__.'/exporte/' )  ){
	//wenn nicht dann erstellen
	mkdir( __DIR__.'/exporte/' );
	//Rechte einstellen
	chmod( __DIR__.'/exporte/' , (fileperms( __DIR__ ) & 0777));
}

//wenn einer der Ordner nicht schreibbar ist -> Fehler
//	PHP konnt ihn auch wohl nicht erstellen
if( !is_writable( __DIR__.'/temp/' ) || !is_writable( __DIR__.'/exporte/' ) ){
	//Meldung
	$sitecontent->echo_error( 'Der Ordner "'.__DIR__.'/temp/" und "'.__DIR__.'/exporte/" muss schreibbar sein!' );
}
//User will Export durchführen?
elseif( $_GET['task'] == 'export' ){

	//Link zur Auswahl
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');
	//Überschrift
	$sitecontent->add_site_content('<h3>Export</h3>');
	//URL ändern
	$addonurl .= '&task='.$_GET['task'];
	//Datei für Export laden
	require_once( __DIR__.'/export_cms.php' );

}
//User will Import durchführen?
elseif( $_GET['task'] == 'import' ){

	//Link zur Auswahl
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');
	//Überschrift
	$sitecontent->add_site_content('<h3>Import</h3>');
	//URL ändern
	$addonurl .= '&task='.$_GET['task'];
	//Datei für Export laden
	require_once( __DIR__.'/import_cms.php' );

}
//User will Seiten hinzufügen?
elseif( $_GET['task'] == 'add' ){

	//Link zur Auswahl
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');
	//Überschrift
	$sitecontent->add_site_content('<h3>Add</h3>');
	//URL ändern
	$addonurl .= '&task='.$_GET['task'];
	//Datei für Export laden
	require_once( __DIR__.'/add_sites.php' );

}
//will der User eine alte Exportdatei herunterladen?
//	Übergaben okay?
elseif( $_GET['task'] == 'downexpfile' && !empty( $_GET['downexpfile'] ) ){
	
	//keine Punkt im Dateinamen (sonst Filesystem gefährdet)
	if( strpos($_GET['downexpfile'], '..') !== false ){
		echo 'Do not hack me!';
		die;
	}
	
	//gibt es die gewünscht Datei?
	if( is_file( __DIR__.'/exporte/'.$_GET['downexpfile'].'.zip' ) ){
		
		//Header
		header('Content-type: application/kimbex');
		header('Content-Disposition: attachment; filename= '.$_GET['downexpfile'].'.kimbex');
		//Datei ausgeben
		readfile( __DIR__.'/exporte/'.$_GET['downexpfile'].'.zip' );
		//alles beenden
		die;
		
	}
	else{
		//Datei nicht gefunden
		$sitecontent->echo_error('Die Exportdatei wurde nicht gefunden!', 'Datei weg');	
	}
}
//User will wohl nichts bestimmtes
else{
	//Auwahl was er will
	$sitecontent->add_site_content('<h3>Auswahl</h3>');
	$sitecontent->add_site_content('<br />');
	//Export
	$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;task=export"><button title="Exportieren Sie die Inhalt dieses CMS in eine Datei." >Export</button></a>');
	$sitecontent->add_site_content('<br /><br />');
	//Import
	$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;task=import"><button title="Importieren Sie eine der Exportdateien in dieses CMS, dies Überschreibt alle aktuellen Inhalte" >Import</button></a>');
	$sitecontent->add_site_content('<br /><br />');
	//Add
	$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;task=add"><button title="Fügen Sie Inhalte wie Seiten aus einer Exportdatei diesem CMS hinzu." >Add</button></a>');
	$sitecontent->add_site_content('<br />');
	
	//Exportdateien listen und zum Download anbieten
	$sitecontent->add_site_content('<h3>Exportdateien</h3>');
	
	//will User eine Datei löschen
	//	Übergaben okay?
	if( $_GET['task'] == 'delexpfile' && !empty( $_GET['delexpfile'] ) ){
	
		//keine Punkt im Dateinamen (sonst Filesystem gefährdet)
		if( strpos($_GET['delexpfile'], '..') !== false ){
			echo 'Do not hack me!';
			die;
		}
		
		//Ist die Datei vorhanden?
		if( is_file( __DIR__.'/exporte/'.$_GET['delexpfile'] ) ){
			//dann Datei löschen
			unlink( __DIR__.'/exporte/'.$_GET['delexpfile'] );
			//Medlung
			$sitecontent->echo_message('Die Exportdatei wurde gelöscht!');	
		}
		else{
			//wenn Datei nicht da, auch Meldung
			$sitecontent->echo_error('Die Exportdatei wurde nicht gefunden!', 'Datei weg');	
		}
	}
	
	//Liste für Exportdateien beginnen
	$sitecontent->add_site_content('<ul>');
	
	//Verzeichnis mit Exportdateien lesen
	$exps = scandir( __DIR__.'/exporte/', SCANDIR_SORT_DESCENDING );
	//alle Dateien durchgehen
	foreach( $exps as $exp ){
		
		// "." und ".." ignorieren
		if( $exp != '.' && $exp != '..'){
			
			//Timestamp der Datei herausfinden
			$time = substr( $exp, 16, 10);
			
			//Punkt der Liste hinzufügen
			//	Link zum Download
			//	Mülleimer um Datei zu löschen
			$sitecontent->add_site_content('<li><a href="'.$addonurl.'&amp;task=downexpfile&amp;downexpfile='.substr($exp, 0, -4 ).'">'.date( 'd.m.Y - H:i', $time).'</a><a href="'.$addonurl.'&amp;task=delexpfile&amp;delexpfile='.$exp.'"><span style="display:inline-block;" title="Exportdatei löschen" class="ui-icon ui-icon-trash"></a></li>');
			
			//Liste nicht leer
			$liste = true;
		}	
	}
	$sitecontent->add_site_content('</ul>');
	
	//wenn Liste leer,
	if( !$liste ){
		//Meldung ausgeben
		$sitecontent->echo_error( 'Es wurden keine Exporte gefunden!', 'unknown', 'Exportdateien' );
	}

	//Konfigurationsdatei für Cron-Einstellungen
	$imexfile = new KIMBdbf( 'addon/im_export__cron.kimb' );
	
	//Änderungen gesetzt?
	if( isset( $_POST['coo'] ) ){
		//alle Übergaben
		$dos = array( 'coo', 'age', 'server', 'name', 'pass', 'folder', 'topath' );
		//Werte für dbf ID 21
		$xxxids = array( 'server', 'name', 'pass' );
		
		//alle Übergaben durchgehen
		foreach( $dos as $do ){
			
			//wird ein Wert aus der dbf ID 21 gegeben?		
			if( in_array( $do, $xxxids)){
				//ist die Übergabe anders als der Wert in der dbf
				if( $_POST[$do] != $imexfile->read_kimb_id( '21', $do ) ){
					//dbf anpassen
					$imexfile->write_kimb_id( '21', 'add', $do, $_POST[$do] );
					//Meldung vorbereiten
					$message .= 'Die ftp Daten ("'.$do.'") wurden aktualisiert!<br />';
				}
			}
			//wird ein normaler Wert der dbf gegeben?
			else{
				//ist die Übergaben anders als der aktuelle Wert in der dbf
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
			}
		}
		//wenn Medlung nicht leer, ausgeben
		if( !empty($message) ){
			$sitecontent->echo_message( $message );
		}
	}
	
	//Beispielwerte
 	$age = '72';
	$dat = array( 'server' => '', 'name' => '', 'pass' => '' );
	$topath = '';
	$coo = array( '', '');
	
	//alle Werte lesen, sofern nicht leer als Value im Input Feld
	if( !empty( $aage = $imexfile->read_kimb_one( 'age' ) ) ){
		$age = $aage;
	}
	if( is_array( $ddat = $imexfile->read_kimb_id( '21' ) ) ){
		$dat = $ddat;
	}
	if( !empty( $ttopath = $imexfile->read_kimb_one( 'topath' ) ) ){
		$topath = $ttopath;
	}
	
	//On/Off Radion Button
	$ccoo = $imexfile->read_kimb_one( 'coo' );
	if( $ccoo == 'on' ){
		$coo[1] = 'checked="checked"';
	}
	elseif( $ccoo == 'off' ){
		$coo[0] = 'checked="checked"';
	}
	
	//Eingabeformular
	$sitecontent->add_site_content('<h3>Cron</h3>');
	//Hinweise
	$sitecontent->add_site_content('Das CMS kann automatisch in regelmäßigen Abständen eine Exportdatei erstellen.<b title="Der Filemanager wird nicht exportiert!">*</b><br />');
	$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post">');
	//on/off
	$sitecontent->add_site_content('<input type="radio" value="off" name="coo" '.$coo[0].'><span style="display:inline-block;" title="Cron deaktivieren" class="ui-icon ui-icon-closethick"></span>');
	$sitecontent->add_site_content('<input type="radio" value="on" name="coo" '.$coo[1].'><span style="display:inline-block;" title="Cron aktivieren" class="ui-icon ui-icon-check"></span><br /><br />');
	
	//Cron Zeit
	$sitecontent->add_site_content('<input type="text" value="'.$age.'" name="age"> Mindestabstand zwischen den Backups in Stunden. <b title="Die tatsächliche Zeit hängt von der Anzahl der Aufrufe der /cron.php ab.">*</b><br />');
	
	//ftp Daten
	$sitecontent->add_site_content('<h4>ftp Upload</h4>');
	$sitecontent->add_site_content('Das CMS kann die Exportdateien direkt auf einen ftp Server hochladen, bitte füllen Sie die Felder sofern Sie diese Funktion nutzen wollen.<br />');
	$sitecontent->add_site_content('<input type="text" value="'.$dat['server'].'" placeholder="ftp.example.com" name="server"> ftp Host<br />');
	$sitecontent->add_site_content('<input type="text" value="'.$dat['name'].'" placeholder="max" name="name"> ftp Username<br />');
	$sitecontent->add_site_content('<input type="text" value="'.$dat['pass'].'" placeholder="geheim" name="pass"> ftp Passwort<br />');
	$sitecontent->add_site_content('<input type="text" value="'.$topath.'" placeholder="cms/cms1" name="topath"> ftp Ordner <b title="In diesen Ordner auf dem Server kommen die Dateien. (Ordner muss auf Server existieren!)">*</b><br />');
	
	$sitecontent->add_site_content('<input type="submit" value="Übernehmen">');	
	$sitecontent->add_site_content('</form>');
}

?>
