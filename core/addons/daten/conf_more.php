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
if( !is_dir( __DIR__.'/userdata/group/' ) || !is_writable( __DIR__.'/userdata/group/' )){
	$sitecontent->echo_error( 'Das Verzeichnis /userdata/group/ existiert nicht oder PHP hat keine Schreibrechte!' );
}

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
$sitecontent->add_site_content('<br /><br /><form action="'.$addonurl.'" method="post">');

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

?>
