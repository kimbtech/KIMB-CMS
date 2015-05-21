<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
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


define("KIMB_CMS", "Clean Request");

//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login( 'fiveteen' , 'more');

$beaddinst = new BEaddinst( $allgsysconf, $sitecontent, $addoninclude );

//Add-ons installieren und löschen

$allinclpar = $beaddinst->allinclpar;

$_GET['addon'] = preg_replace( "/[^a-z_]/" , "" , $_GET['addon'] );

if( isset( $_GET['del'] ) && !empty( $_GET['addon'] ) ){

	$beaddinst->del_addon( $_GET['addon'] );
	
}
elseif( !empty( $_FILES['userfile']['name'] ) ){

	$beaddinst->install_addon( $_FILES["userfile"]["tmp_name"] );

}
elseif( $_GET['todo'] == 'chdeak' && !empty( $_GET['addon'] ) ){

	$beaddinst->change_stat( $_GET['addon'] );
	
}
elseif( $_GET['todo'] == 'addonnews' && !empty( $_GET['addon'] ) ){

	$beaddinst->show_addon_news( $_GET['addon'] );
	
	$showlist = 'no';
}
elseif( $_GET['todo'] == 'checkall' ){

	$beaddinst->check_addon_upd();
		
}

if( !isset( $showlist ) ){
	
	$beaddinst->show_list();
	
}
//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
