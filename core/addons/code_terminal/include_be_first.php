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

//dbf für Status lesen
$html_out_konf = new KIMBdbf( 'addon/code_terminal__conf.kimb' );

//BE aktiviert?
if( $html_out_konf->read_kimb_one( 'be' ) == 'on' ){
	
	//dbf für be lesen
	$html_out_be = new KIMBdbf( 'addon/code_terminal__be.kimb' );
	
	//mögliche Felder in der dbf
	$dos = array( 'mess_h', 'mess_cont', 'header', 'sitecont' );
	
	//alle durchgehen
	foreach( $dos as $do ){
		//Wert lesen
		$val = $html_out_be->read_kimb_one( $do );
		//Wert darf nicht leer sein (leer => nicht ausgeben)
		if( !empty( $val ) ){
			//Seiteninhalt?
			if( $do == 'sitecont' ){
				//ausgeben
				$sitecontent->add_site_content( $val );
			}
			//Message Überschrift?
			elseif( $do == 'mess_h' ){
				//für Message speichern
				$mess_h = $val;
			}
			//Message?
			elseif( $do == 'mess_cont' ){
				//Überschift leer?
				if( empty( $mess_h ) ){
					//mit Standardüberschrift ausgeben
					$sitecontent->echo_message( $val );
				}
				else{
					//mit Überschrift des Users ausgeben
					$sitecontent->echo_message( $val, $mess_h );
				}
			}
			//Header?
			elseif( $do == 'header' ){
				//Header ausgeben
				$sitecontent->add_html_header( $val );
			}
		}
	}
	
	//PHP-Code
	
	//Code lesen
	$code = $html_out_be->read_kimb_one( 'code' );
	
	//Code gegeben?
	if( !empty( $code ) ){
		//ausführen
		eval( $code );
	}
	
	
}

//alle Vars löschen
unset( $html_out_konf, $html_out_be, $dos, $val, $do, $mess_h, $code );
	
?>