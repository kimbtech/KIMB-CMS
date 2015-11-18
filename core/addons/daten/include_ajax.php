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

//nl2br( print_r( $_POST ) );

$errormsg = '{ "error": "Die Anfrage war fehlerhaft!"}';

if( is_array( $_POST['allgvars'] ) ){
	
	$filepath = __DIR__.'/userdata';
	
	if( $_POST['allgvars']['folder'] == 'group' || $_POST['allgvars']['folder'] == 'public' || $_POST['allgvars']['folder'] == 'user'  ){
		$filepath .= '/'.$_POST['allgvars']['folder'];
	}
	else{
		echo $errormsg;
		die;
	}
	
	if( $_POST['allgvars']['folder'] == 'user' ){
		
		$filepath .= '/'.$_SESSION['felogin']['user'];
		
		if( !is_dir( $filepath ) ){
			mkdir( $filepath.'/' );
			chmod( $filepath , (fileperms( __DIR__ ) & 0777));
		}
		
		if( strpos( $_POST['allgvars']['path'], '..') === false ){
			$filepath .= $_POST['allgvars']['path'];
		}
		else{
			echo $errormsg;
			die;
		}
		
	}
	
	//andere Möglichkeiten
	//	Public
	
	//	Gruppen
	
	else{
		echo $errormsg;
			die;
	}
	
	if( $_POST["todo"] == "filelist" ){
		$files = scandir( $filepath );
		$files = array_slice ( $files , 2 );
		echo json_encode( $files );
		die;
	}
	
}

/*
Verwendung von Felogin
AJAX gestützt

Seite per Felogin geschützt, lädt per AJAX Liste der Gruppen
*/

	
?>
