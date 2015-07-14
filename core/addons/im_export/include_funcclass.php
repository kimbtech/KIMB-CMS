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

function make_cms_export( array $dos ){

	//.................

	return $filepath;	
}

function ftp_push_export( $upfile, $topath , $dat ){
	
	$conn = ftp_connect($dat['server']);
	$login = ftp_login($conn, $dat['name'], $dat['pass']);

	$remote = '/'.$topath.'/'.basename( $upfile );

	if( ftp_put($conn, $remote, $upfile, FTP_BINARY) ) {
		$ret = true;
	}
	else{
		$ret = false;
	}

	ftp_close($conn);
	
	return $ret;
}

?>