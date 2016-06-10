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

//Konfiguration laden und Handhabung vereinfachen
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=daten';

//Verzeichnisrechte
if( !is_dir( __DIR__.'/userdata/public/' ) || !is_writable( __DIR__.'/userdata/public/' )){
	$sitecontent->echo_error( 'Das Verzeichnis /userdata/public/ existiert nicht oder PHP hat keine Schreibrechte!' );
}
if( !is_dir( __DIR__.'/userdata/user/' ) || !is_writable( __DIR__.'/userdata/user/' )){
	$sitecontent->echo_error( 'Das Verzeichnis /userdata/user/ existiert nicht oder PHP hat keine Schreibrechte!' );
}

//Systemkonfigurationsdatei
$sysfile = new KIMBdbf( 'addon/daten__conf.kimb' );

//Add-on Wish
//	wir die Seiten ID übergeben?
if( is_numeric( $_POST['id'] ) && !empty( $_POST['id'] ) ){

	//aktuelle Seite lesen
	$siteid = $sysfile->read_kimb_one( 'siteid' );
	//Seiten ID verändert?
	if( $_POST['id'] != $siteid ){
		//Seiten ID überschreiben
		$sysfile->write_kimb_one( 'siteid' , $_POST['id'] );
		
		//die neue Seiten ID der Add-on API mitteilen
		$a = new ADDonAPI( 'daten' );
		$a->set_fe( 'hinten', 's'.$_POST['id'] , 'no' );

		//Meldung
		$sitecontent->echo_message( 'Die SiteID wurde geändert!' );
	}

}

//Formular
$sitecontent->add_site_content('<h2>Seite für Dateiverwaltung</h2>');
$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post">');

//SiteID lesen
$siteid = $sysfile->read_kimb_one( 'siteid' );
if( empty( $siteid )){
	//leer -> keine
	$siteid = 'none';
}
//im Dropdown mit SiteIDs Akteulle wählen
$sitecontent->add_html_header('<script>$(function(){ $( "select[name=id]" ).val( \''.$siteid.'\' ); }); </script>');
//Dropdown und Infotext
$sitecontent->add_site_content(id_dropdown( 'id', 'siteid' ).' (SiteID <b title="Bitte geben Sie hier die Seite an, auf welcher die Dateiverwaltung erscheinen soll.">*</b>)<br />Die angegebene Seite muss mit Felogin geschützt werden!<br />');

$sitecontent->add_site_content('<br /><input type="submit" value="Speichern"></form>');

//Userordner löschen
$sitecontent->add_site_content('<h2>Nutzer der Dateiverwaltung</h2>');

//Ordner zu löschen?
if( !empty( $_GET['del'] ) ){
	
	//Username == Ordnername
	$user = $_GET['del'];
	//Namen bereinigen
	$user = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $user ) );
	
	//Löschen versuchen
	if( rm_r( __DIR__.'/userdata/user/'.$user ) ){
		//Medlung
		$sitecontent->echo_message( 'Die Daten des Users wurden gelöscht!' );
	}
	else{
		//Medlung
		$sitecontent->echo_error( 'Die Daten des Users konnten nicht gelöscht werden!' );
	}	
}

//alle Userordner als Tabelle laden
$sitecontent->add_html_header('<style>table, tr, td, th { border: 1px solid black; border-collapse:collapse; padding:2px;}</style>');
$sitecontent->add_site_content('<table>');
$sitecontent->add_site_content('<tr>
		<th>Username</th>
		<th>Ordnergröße</th>
		<th>Löschen</th>
		</tr>');
foreach( scandir( __DIR__.'/userdata/user/' ) as $file ){

	if( $file != '.' && $file != '..' ){
		//Ordnergröße
		$size = dir_size_comp( __DIR__.'/userdata/user/'.$file );

		$sitecontent->add_site_content('<tr>
		<td>'.$file.'</td>
		<td>'.( ( $size == false || $size <= 0 ) ? '0 B' : $size  ).'</td>
		<td><a href="'.$addonurl.'&amp;del='.$file.'"><span class="ui-icon ui-icon-trash" style="display:inline-block;" title="Dateien des Users löschen. (Der User kann trotzdem neue Dateien anlegen.)"></span></a></td>
		</tr>');	
	}
}

//Ordnergröße
$size = dir_size_comp( __DIR__.'/userdata/public/' );
$sitecontent->add_site_content('
		<tr>
			<td colspan="3"></td>
		</tr>
		<tr>
		<th colspan="2">Gemeinsames Verzeichnis aller User</th>
		<td>'.( ( $size == false || $size <= 0 ) ? '0 B' : $size  ).'</td>
		</tr>');
$sitecontent->add_site_content('</table>');

?>