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

//	$return -> Eingabe des Users
//	Rückgabe: HTML-Code
function make_guestbook_html( $cont, $name ){

	//Name
	$name = strip_tags ( $name );

	//Inhalt
	$cont = nl2br( htmlentities ( $cont ) );
	
	$codiert = array( '&lt;b&gt;' , '&lt;/b&gt;' , '&lt;u&gt;' , '&lt;/u&gt;' , '&lt;i&gt;' , '&lt;/i&gt;' , '&lt;center&gt;' , '&lt;/center&gt;', '<br />', 'https://' );
	$unkodiert = array( '<b>' , '</b>' , '<u>' , '</u>' , '<i>' , '</i>' , '<center>' , '</center>', ' <br />', 'http://' );
	
	//alles doofe weg
	$cont = str_replace( $codiert , $unkodiert , $cont );
	
	$cont = $cont.' ';
	
	//URL zu Link
	$pos = strpos( $cont, 'http://');
	while(  $pos !== false ){
		
		$leer = strpos( $cont, ' ', $pos );
		
		$len = $leer - $pos;
		$url = substr( $cont , $pos , $len );
		
		$array = explode( $url, $cont, 2);
		
		$cont = $array[0].'<a href="'.$url.'" target="_blank">'.$url.'</a>'.$array[1];
			
		//nächste
		$off = strlen( $array[0].'<a href="'.$url.'" target="_blank">'.$url.'</a>' ) ;
		$pos = strpos( $cont, 'http://', $off);
	}

	return array( $cont, $name);
}

?>
