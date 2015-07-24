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

//Cron aktiviert?
if( $html_out_konf->read_kimb_one( 'cr' ) == 'on' ){
	
	//dbf für cr lesen
	$html_out_cr = new KIMBdbf( 'addon/code_terminal__cr.kimb' );
	
	//Code lesen
	$code = $html_out_cr->read_kimb_one( 'code' );
	
	//Code gegeben?
	if( !empty( $code ) ){
		//ausführen
		eval( $code );
	}
}

//alle Varibalen löschen
unset($html_out_konf, $html_out_cr, $code);

?>
