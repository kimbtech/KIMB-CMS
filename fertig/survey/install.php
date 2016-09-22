<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2016 by KIMB-technologies.eu
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

//erweiterter Funktionumfang
if( check_addon( 'felogin' ) == array( false, false ) || check_addon( 'felogin' ) == array( true, false ) ){
	$sitecontent->echo_message( 'Das Add-on "Frontend Login" ist nicht aktivert oder nicht installiert. Sie können mit "Frontend Login" den Funktionsumfang von "Survey - Umfrage" erweitern!' );
}

//Add-on API wish
$a = new ADDonAPI( 'survey' );
$a->set_fe( 'vorn', 'a' , 'no' );
$a->set_funcclass( 'hinten' );

?>
