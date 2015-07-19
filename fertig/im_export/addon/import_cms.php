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

//Mit dieser Datei können Sie das Exporte in dieses CMS importieren

//Hinweis
$sitecontent->echo_message( 'Erstellen Sie vor jedem Import ein Backup um einem Datenverlust vorzubeugen!!' );
$sitecontent->add_site_content( '<br /><hr /><br />' );

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

	//Pfad für Dateiordner
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

	//Versionsvergeleich der CMS
	if( $jsoninfo['allg']['sysver'] != $allgsysconf['build'] ){
		$sitecontent->echo_message( 'Die Versionen dieses Systems und des Exportsystems stimmen nicht überein, dies kann zu Problemem führen, wenn Sie fortfahren.' );
	}

	//die Add-ons auf dem Exportsystem  mit denen auf diesem System vergleichen
	$sitecontent->add_site_content('<br />');
	$sysaddons = listaddons();
	foreach( $jsoninfo['addons'] as $addon ){
		if( !in_array( $addon, $sysaddons ) ){
			//wenn Add-on nicht da, dann Meldung ausgeben
			$sitecontent->echo_message( '<b style="color:red;" >Das Add-on "'.$addon.'" war auf dem Exportsystem vorhanden und ist auf diesem System nicht installiert.<br />Dies kann zu Problemen führen!</b>' );
		}
	}
	$sitecontent->add_site_content('<br />');
	
	//Auswahlformular (nicht alle Kategorien müssen importiert werden)
	$sitecontent->add_site_content( '<form action="'.$addonurl.'&amp;weiter='.$_SESSION['importfile'].'" method="post">' );

	//Überschrift
	$sitecontent->add_site_content( '<h4>Wählen Sie die zu importierenden Kategorien:</h4>' );
	
	//Namen der Kategorien
	$doneread = array( "sites" => 'Seiten', "menue" => 'Menüstruktur', "users" => 'Backend-User', "addon" => 'Add-on Konfiguration', "confs" => 'Systemkonfiguration' , "filem" => 'Dateien des Filemanager' );
	//evtl. JavaScript bei onlick der Kategorie
	$onclick = array( "sites" => 'onclick=" $(\'input[value=menue]\').prop(\'checked\', false); "', "menue" => 'onclick=" $(\'input[value=sites]\').prop(\'checked\', true); "', "users" => '', "addon" => '', "confs" => '' , "filem" => '' );

	//für alle Kategorien in der Exportdatei, eine Checkbox machen (Werte von Arrays oben)
	foreach( $jsoninfo['done'] as $done ){
		$sitecontent->add_site_content('<input type="checkbox" value="'.$done.'" name="todo[]" checked="checked" '.$onclick[$done].' >'.$doneread[$done].'<br />');
	}

	$sitecontent->add_site_content('<br />');
	//Submit
	$sitecontent->add_site_content('<input type="submit" value="Import starten">');
	$sitecontent->add_site_content('</form>');
	//Hinweis
	$sitecontent->add_site_content( '<br />Mit dem Klick auf "Import starten" ersetzen Sie die gewählten Daten dieses CMS mit denen der Exportdatei.' );

}
//soll ein Import ausgeführt werden?
elseif( $_GET['weiter'] == $_SESSION['importfile'] && !empty( $_GET['weiter'] ) ){

	//Ordner der entpackten Exportdatei
	$fileroot = __DIR__.'/temp/'.$_SESSION['importfile'].'/';
	//Ordner für dbf
	$dbfroot = __DIR__.'/../../oop/kimb-data/';

	//existiert der Ordner mit den entpackten Exportdateien
	if( is_dir( $fileroot ) ){

		//JSON Info lesen
		$jsoninfo = json_decode( file_get_contents( $fileroot.'/info.jkimb' ) , true );

		//Seiten in der Datei und von User gewünscht?
		if( in_array( 'sites', $_POST['todo'] ) && in_array( 'sites', $jsoninfo['done'] ) ){

			//aktuelle Daten löschen
			rm_r( $dbfroot.'site/' );

			//aus Export nehmen
			copy_r( $fileroot.'sites/', $dbfroot.'site/' );			

			//Medlung
			$sitecontent->echo_message(' Seiten importiert! ');
			$sitecontent->add_site_content('<br />');
		}
		//Menue  in der Datei und von User gewünscht?
		if( in_array( 'menue', $_POST['todo'] ) && in_array( 'menue', $jsoninfo['done'] ) ){

			//URL Dateien löschen
			rm_r( $dbfroot.'url/' );

			//Menü Dateien löschen
			rm_r( $dbfroot.'menue/' );
			
			//Easy-Menü Datei löschen
			unlink( $dbfroot.'backend/easy_menue.kimb' );

			//URL Dateien aus Export nehmen
			copy_r( $fileroot.'menue/url/', $dbfroot.'url/' );		

			//Menü Dateien aus Export nehmen
			copy_r( $fileroot.'menue/menue/', $dbfroot.'menue/' );
			
			//Easy-Menü Datei aus Export nehmen
			copy( $fileroot.'menue/easy_menue.kimb', $dbfroot.'backend/easy_menue.kimb');

			//Medlung
			$sitecontent->echo_message(' Menü importiert! ');
			$sitecontent->add_site_content('<br />');
		}
		//User in der Datei und von User gewünscht?
		if( in_array( 'users', $_POST['todo'] ) && in_array( 'users', $jsoninfo['done'] ) ){

			//User Dateien löschen
			rm_r( $dbfroot.'backend/users/' );

			//User Dateien aus Export nehmen
			copy_r( $fileroot.'users/', $dbfroot.'backend/users/' );	

			//Medlung
			$sitecontent->echo_message(' User importiert! <br /> <b style="color:red;" >Achtung, bitte stellen Sie vor Ihrem Logout sicher, dass es einen gültigen Admin (more) Account gibt!</b>');
			$sitecontent->add_site_content('<br />');
		}
		//Add-on  in der Datei und von User gewünscht?
		if( in_array( 'addon', $_POST['todo'] ) && in_array( 'addon', $jsoninfo['done'] ) ){

			//Add-on Dateien löschen
			rm_r( $dbfroot.'addon/' );

			//Add-on Dateien aus Export nehmen
			copy_r( $fileroot.'addon/', $dbfroot.'addon/' );

			//Medlung
			$sitecontent->echo_message(' Add-on Konfiguration importiert! <br /> <b style="color:red;" >Achtung, überprüfen Sie die Konfiguration möglichst noch einmal! <br />Um Probleme zu vermeiden wurden alle Add-ons deaktiviert!</b>');
			$sitecontent->add_site_content('<br />');
		}
		//Konfiguration  in der Datei und von User gewünscht?
		if( in_array( 'confs', $_POST['todo'] ) && in_array( 'confs', $jsoninfo['done'] ) ){

			//Ist die Konfigurationsdatei geladen? 
			if( !is_object( $conffile ) ){
				//wenn nicht, neu laden
				$conffile = new KIMBdbf('config.kimb');
			}

			//Werte aus dem Export in die Konfigurationsdatei setzen
			$conffile->write_kimb_id( '001' , 'add' , 'sitename' , $jsoninfo['confs']['sitename'] );
			$conffile->write_kimb_id( '001' , 'add' , 'description' , $jsoninfo['confs']['description'] );

			//Datei mit Fehler 404 & 403 & Footer aus Export laden
			copy( $fileroot.'confs/sonder.kimb' , $dbfroot.'sonder.kimb' );

			//Medlung
			$sitecontent->echo_message(' Systemkonfiguration importiert! ');
			$sitecontent->add_site_content('<br />');
		}
		//Filemanager-Dateien  in der Datei und von User gewünscht?
		if( in_array( 'filem', $_POST['todo'] ) && in_array( 'filem', $jsoninfo['done'] ) ){

			//Ordner mit offenen Dateien, um die aus Export erweitern (zwei Dateien gleichen Namens werden überschrieben)
			copy_r( $fileroot.'filem/open/',  __DIR__.'/../../../load/userdata/' );

			//Gesicherten Ordner leeren
			rm_r( __DIR__.'/../../secured/' );

			//Gesicherte Dateien aus Export
			copy_r( $fileroot.'filem/close/', __DIR__.'/../../secured/' );

			//Filemanager Datei mit Keys aus Export nehmen
			copy( $fileroot.'filem/filemanager.kimb' , $dbfroot.'backend/filemanager.kimb' );

			//Medlung
			$sitecontent->echo_message(' Filemanager importiert! ');
			$sitecontent->add_site_content('<br />');
		}

		//Zurück zur Übersicht
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=im_export"><button>&larr; Zurück</button></a>');

		//Exportdatei löschen
		rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
	}
	else{
		//Fehler wenn keine Datei
		$sitecontent->echo_error(' Keine Exportdatei! ');
	}

}
else{
	//Hinweis
	$sitecontent->echo_message( 'Mit dem Import <b><u>ersetzen</u></b> Sie alle Inhalte Ihres CMS!!' );
	$sitecontent->add_site_content( '<br /><br />' );

	//Formular beginnen
	$sitecontent->add_site_content('<form action="'.$addonurl.'" enctype="multipart/form-data" method="post">');

	//Datei auswählen (hochladen)
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
	$sitecontent->add_site_content('<input type="submit" value="Import starten">');
	$sitecontent->add_site_content('</form>');

	//wenn User schon eine Exportdatei hochgeladen hat (warum auch immer), diese löschen
	if( isset( $_SESSION['importfile'] ) ){
		rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
	}

}

?>
