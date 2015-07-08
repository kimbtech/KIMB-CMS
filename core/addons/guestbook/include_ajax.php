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

if( !empty( $_POST['vorschau_name'] ) && !empty( $_POST['vorschau_cont'] ) ){
	//Vorschau
	
	$arr = make_guestbook_html( $_POST['vorschau_cont'], $_POST['vorschau_name'] );
	
	echo '<div id="guest" >'."\r\n";		
	echo '<div id="guestname" >'.$arr[1]."\r\n";
	echo '<span id="guestdate">'.date( 'd-m-Y H:i:s' ).'</span>'."\r\n";
	echo '</div>'."\r\n";
	echo $arr[0]."\r\n";
	echo '</div>'."\r\n\r\n";

	die;
}
elseif( isset( $_GET['loadadd'] ) ){
	//AJAX

	die;	
}

echo 'Falscher Zugriff auf Guestbook AJAX';

?>
