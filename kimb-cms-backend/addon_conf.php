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

//Add-on Konfiguration

if(strpos( $_GET['addon'] , "..") !== false){
	echo ('Do not hack me!!');
	die;
}

$beaddconf = new BEaddconf( $allgsysconf, $sitecontent );

if( $_GET['todo'] == 'more' ){
	check_backend_login( 'fourteen' , 'more');
	
	$beaddconf->make_more();

}
elseif( $_GET['todo'] == 'less' ){

	check_backend_login( 'thirteen' );

	$beaddconf->make_less();

}
else{
	check_backend_login( 'twelve' );

	$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
