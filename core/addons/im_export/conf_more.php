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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=im_export';

if( !is_writable( __DIR__.'/temp/' ) ){
	$sitecontent->echo_error( 'Der Ordner "'.__DIR__.'/temp/" muss schreibbar sein!' );
	
	//Ordner temp & exporte testen und evtl. erstellen!!!
}
elseif( $_GET['task'] == 'export' ){

	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');

	$sitecontent->add_site_content('<h3>Export</h3>');

	$addonurl .= '&task='.$_GET['task'];

	require_once( __DIR__.'/export_cms.php' );

}
elseif( $_GET['task'] == 'import' ){

	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');

	$sitecontent->add_site_content('<h3>Import</h3>');

	$addonurl .= '&task='.$_GET['task'];

	require_once( __DIR__.'/import_cms.php' );

}
elseif( $_GET['task'] == 'add' ){

	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');

	$sitecontent->add_site_content('<h3>Add</h3>');

	$addonurl .= '&task='.$_GET['task'];

	require_once( __DIR__.'/add_sites.php' );

}
else{
	$sitecontent->add_site_content('<h3>Auswahl</h3>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&task=export"><button title="Exportieren Sie die Inhalt dieses CMS in eine Datei." >Export</button></a>');
	$sitecontent->add_site_content('<br /><br />');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&task=import"><button title="Importieren Sie eine der Exportdateien in dieses CMS, dies Überschreibt alle aktuellen Inhalte" >Import</button></a>');
	$sitecontent->add_site_content('<br /><br />');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&task=add"><button title="Fügen Sie Inhalte wie Seiten aus einer Exportdatei diesem CMS hinzu." >Add</button></a>');
	$sitecontent->add_site_content('<br />');
	
	//Liste alle Exporte!!
	
	//Einstellungen für Cron (Häufigkeit) & FTP
}

?>
