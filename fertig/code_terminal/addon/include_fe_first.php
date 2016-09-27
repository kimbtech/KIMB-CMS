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

//dbf für fe lesen
$html_out_fe = new KIMBdbf( 'addon/code_terminal__fe.kimb' );
	
//alles auslesen
$alles = $html_out_fe->read_kimb_id_all();

//korrekte Seiten
$korsites = array( 'a', $allgsiteid );

//aktuelle Stelle
$now = 'top';
//	$now = 'bottom';

//durchgehen
foreach( $alles as $id => $vals ){
	//diesen Code nehmen?
	if( in_array( $vals['site'], $korsites )){
		
		//Werte nicht leer?
		//	cssclass ist egal
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
			&&
			(
				( isset( $vals['place'] ) && ( $vals['place'] == 'top' || $vals['place'] == 'bottom' ) )
				||
				( !isset( $vals['place'] ) )
			)


		 ){

			//Header?
			if( $vals['type'] == 'header' ){
				//ausgeben
				$sitecontent->add_html_header( $vals['code'] );
			}
			//Add-on Area?
			elseif( $vals['type'] == 'area' ){
				//Stelle okay?
				if( $vals['place'] == $now ){
					//Klasse leer?
					if( empty( $vals['cssclass'] ) ){
						//ohne Klasse ausgeben
						$sitecontent->add_addon_area( $vals['inhalt'] );
					}
					else{
						//mit Klasse ausgeben
						$sitecontent->add_addon_area( $vals['inhalt'], '', $vals['cssclass'] );
					}
				}
			}
			//Seiteninhalt?
			elseif( $vals['type'] == 'cont' ){
				//Stelle okay?
				if( $vals['place'] == $now ){
					//Header ausgeben
					$sitecontent->add_site_content( $vals['inhalt'] );
				}
			}
			//PHP?
			elseif( $vals['type'] == 'php' ){
				//Stelle okay?
				if( $vals['place'] == $now ){
					//ausführen
					eval( $vals['phpcode'] );
				}
			}
		 }
	}
}

?>