<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies.eu
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
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//dbf für be lesen
$html_out_be = new KIMBdbf( 'addon/code_terminal__be.kimb' );
	
//aktuelle Seite bestimmen
//	var
$path;
//	RegExp
preg_match( '/(?<=\/kimb-cms-backend\/)[a-z\_]*(?<!\.php)/', get_req_url() , $path );
//	nur das ganze
$path = $path[0];
// evtl. index.php?
//	ohne Dateinamen aufgerufen
if( empty( $path ) ){
	$path = 'index';
}

//alles auslesen
$alles = $html_out_be->read_kimb_id_all();

//korrekte Seiten
$korsites = array( 'all', $path );

//durchgehen
foreach( $alles as $id => $vals ){
	//diesen Code nehmen?
	if( in_array( $vals['site'], $korsites )){
		
		//Werte nicht leer?
		//	Heading ist egal
		if( 
			(
				( isset( $vals['code'] ) && !empty( $vals['code'] ) )
				||
				( !isset( $vals['code'] ) )
			)
			&&
			(
				( isset( $vals['inhalt'] ) && !empty( $vals['inhalt'] ) )
				||
				( !isset( $vals['inhalt'] ) )
			)
			&&
			(
				( isset( $vals['phpcode'] ) && !empty( $vals['phpcode'] ) )
				||
				( !isset( $vals['phpcode'] ) )
			)


		 ){

			//Header?
			if( $vals['type'] == 'header' ){
				//ausgeben
				$sitecontent->add_html_header( $vals['code'] );
			}
			//Message?
			elseif( $vals['type'] == 'mess' ){
				//Überschift leer?
				if( empty( $vals['heading'] ) ){
					//mit Standardüberschrift ausgeben
					$sitecontent->echo_message( $vals['inhalt'] );
				}
				else{
					//mit Überschrift des Users ausgeben
					$sitecontent->echo_message( $vals['inhalt'],  $vals['heading'] );
				}
			}
			//Seiteninhalt?
			elseif( $vals['type'] == 'cont' ){
				//Header ausgeben
				$sitecontent->add_site_content( $vals['inhalt'] );
			}
			//PHP?
			elseif( $vals['type'] == 'php' ){
				//ausführen
				eval( $vals['phpcode'] );
			}
		 }
	}
}
	
?>