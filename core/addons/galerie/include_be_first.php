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

//Diese Datei wird nur im Filemanager geladen

//aktuellen Pfad herausfinden
$path = $_GET['path']; 

//nur offene Verzeichnisse erlauben und nicht Homeverzeichnis
if( !empty( $path ) && $path != '/' && $_SESSION['secured'] == 'off' ){

	//Galeriedatei lesen
	$galerie['file'] = new KIMBdbf( 'addon/galerie__conf.kimb' );

	//den aktuellen Pfad des Filemanagers suchen
	$galerie['id'] = $galerie['file']->search_kimb_xxxid( $path , 'imgpath');

	if( $galerie['id'] == false ){
		//noch keine Galerie in diesem Verzeichnis
		if( check_backend_login( 'fourteen', 'more', false ) ){
			//Usern mit Rechten zur Erstellung neuer Galerien einen Link anzeigen
			$sitecontent->echo_message( 'Diesen Ordner als Bilderverezichnis für eine Galerie verwenden?<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=galerie&amp;path='.urlencode( $path ).'"><button>Los!</button></a>', 'Bildergalerie');
		}
	}
	else{
		//dieses Verzeichnis hat eine Galerie
		$link = '';
		if( check_backend_login( 'fourteen', 'more', false ) ){
			//Usern mit Rechten zum Bearbeiten von Galerien einen Link anzeigen
			$link = '<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=galerie&amp;id='.$galerie['id'].'"><button>Einstellungen</button></a>';
		}
		//allen Usern den Hinweis auf die Galerie zeigen
		$sitecontent->echo_message( 'Dieser Ordner ist ein Bilderverezichnis für eine Galerie!'.$link, 'Bildergalerie' );
	}
}
	
?>
