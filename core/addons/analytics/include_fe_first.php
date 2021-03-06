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

require_once( __DIR__.'/tracking_codes.php' );

//Piwik per Bild in den Seitenquelltext
if( $analytics['conf']['anatool'] == 'pimg' ){
	$sitecontent->add_footer( $analytics['codes'] );
}
//sonst alles in den Header
else{
	$sitecontent->add_html_header( $analytics['codes'] );
}

//Piwik Noscript Code gewünscht? -> Footer
if( $analytics['conf']['anatool'] == 'p' && $analytics['toold']['pimg'] == 'on' ){
	$sitecontent->add_footer( $analytics['toold']['pimgcode'] );
}

//Speicher freigeben
unset( $analytics );
?>
