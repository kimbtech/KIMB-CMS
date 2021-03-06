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

defined('KIMB_CMS') or die('No clean Request');

//gesichertes Include Array von first vorhanden?
//	(kann teilweise durch /core/conf/conf_backend.php fehlen)
if( is_array( $besecondincludesaddons ) ){
	
	//gesichertes Include Array von first verwenden
	//alles ausführen
	foreach( $besecondincludesaddons as $name ){
	
		//prüfen ob Datei vorhanden
		if( is_file( __DIR__.'/'.$name.'/include_be_second.php' ) ){

			require_once(__DIR__.'/'.$name.'/include_be_second.php');

		}
	}
}

?>
