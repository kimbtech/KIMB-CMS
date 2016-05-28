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

//Hinweis auf Add-on für User
if( !isset( $_GET['todo'] ) || $_GET['todo'] != 'addonnews' ){
	//nur wenn nicht Seite mit Add-on Infos
	$sitecontent->add_site_content( '<br />' );
	$sitecontent->echo_message( 'Installieren Sie neue Add-ons und Updates mit nur einem Klick.<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=auto_update_addons"><button>Los geht&apos;s</button></a>', 'Add-on: Installation &amp; Update' );
}

?>
