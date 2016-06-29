<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2016 by KIMB-technologies
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

//URL zur Konf less
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=blog';

//Add-on nötig Datei laden
require_once( __DIR__.'/required_addons.php' );

//neuen Artikel schreiben?
if( isset( $_GET['new'] ) ){

	//Menü machen

	//Seite machen

	//Gästebuch anfügen

	// => Seite füllen Link (ext)
	// => veröffentlichen Link

}
//Seite veröffentlichen
elseif( !empty( $_GET['newpub'] ) && is_numeric( $_GET['newpub'] ) ){

	//Seite und Menü
	//	Status "on"

	//Cache leeren

}
//
else{

	//Neuen Artikel Button
	$sitecontent->add_site_content( '<center><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon=blog&amp;new"><button><h3>Artikel schreiben</h3></button></a></center>' );

}
?>