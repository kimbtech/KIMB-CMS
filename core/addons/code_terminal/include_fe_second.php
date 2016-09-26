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

//schon von first?
if( !is_object( $html_out_konf ) ){
	//dbf für Status lesen
	$html_out_konf = new KIMBdbf( 'addon/code_terminal__conf.kimb' );
}

//FE aktiviert?
if( $html_out_konf->read_kimb_one( 'fe' ) == 'on' ){
	
	//schon von first?
	if( !is_object( $html_out_fe ) ){
		//dbf für fe lesen
		$html_out_fe = new KIMBdbf( 'addon/code_terminal__fe.kimb' );
	}
	
	//mögliche Felder in der dbf
	//	hier nur first
	$dos = array( 'second_area_class', 'second_area_cont', 'second_sitecont', 'second_code' );
	
	//alle durchgehen
	foreach( $dos as $do ){
		//Wert lesen
		$val = $html_out_fe->read_kimb_one( $do );
		//Wert darf nicht leer sein (leer => nicht ausgeben)
		if( !empty( $val ) ){
			//Seiteninhalt?
			if( $do == 'second_sitecont' ){
				//ausgeben
				$sitecontent->add_site_content( $val );
			}
			//Add-on Area Class?
			elseif( $do == 'second_area_class' ){
				//für Message speichern
				$area_class = $val;
			}
			//Add-on Area?
			elseif( $do == 'second_area_cont' ){	
				//Class leer?
				if( empty( $area_class ) ){
					//Area ohne Class
					$sitecontent->add_addon_area($val);
				}
				else{
					//Area mit Class
					$sitecontent->add_addon_area($val,'', $area_class);
				}
			}
			//Code?
			elseif( $do == 'second_code' ){
				eval( $val );
			}
		}
	}	
}

//alle Vars löschen
unset( $html_out_konf, $html_out_fe, $dos, $do, $val, $area_class );
?>