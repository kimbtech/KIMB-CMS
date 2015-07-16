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

function make_cms_export( array $teile ){
	global $allgsysconf;

	$zip = new ZipArchive();
	$filename = __DIR__.'/exporte/export_'.date("dmY").'_'.time().'.zip';
	if ( $zip->open( $filename , ZIPARCHIVE::CREATE) !== TRUE ) {
		return 'fileerror';
	}

	$dbfroot = __DIR__.'/../../oop/kimb-data/';

	//allgemeines zum System
	$infojson['allg'] = array( 'date' => date( 'dmY' ), 'time' => time(), 'sysver' => $allgsysconf['build'], 'sysname' => $allgsysconf['sitename'] );

	foreach( listaddons() as $addon ){
		$infojson['addons'][] = $addon;
	}

	//Seiten
	if( in_array( 'sites', $teile ) ){

		zip_r( $zip, $dbfroot.'site/', '/sites/' );

		$infojson['done'][] = 'sites';
	}
	//menue
	if( in_array( 'menue', $teile ) ){

		zip_r( $zip, $dbfroot.'menue/', '/menue/menue/' );

		zip_r( $zip, $dbfroot.'url/', '/menue/url/' );
		
		$zip->addFile( $dbfroot.'backend/easy_menue.kimb' , '/menue/easy_menue.kimb' );

		$infojson['done'][] = 'menue';
	}
	//User
	if( in_array( 'users', $teile ) ){

		zip_r( $zip, $dbfroot.'/backend/users/', '/users/' );

		$infojson['done'][] = 'users';
	}
	//Add-on
	if( in_array( 'addon', $teile ) ){

		zip_r( $zip, $dbfroot.'addon/', '/addon/' );

		$zip->deleteName( '/addon/includes.kimb' );

		$infojson['done'][] = 'addon';
	}
	//Konfiguration
	if( in_array( 'confs', $teile ) ){

		$infojson['confs']['sitename'] = $allgsysconf['sitename'];
		$infojson['confs']['description'] = $allgsysconf['description'];

		$zip->addFile( $dbfroot.'sonder.kimb' , '/confs/sonder.kimb' );

		$infojson['done'][] = 'confs';
	}
	//Filemanager dateien
	if( in_array( 'filem', $teile ) ){

		$zip->addFile( $dbfroot.'backend/filemanager.kimb' , '/filem/filemanager.kimb' );

		zip_r( $zip, __DIR__.'/../../../load/userdata/', '/filem/open/' );

		zip_r( $zip, __DIR__.'/../../secured/', '/filem/close/' );

		$infojson['done'][] = 'filem';	
	}


	$zip->addFromString( '/info.jkimb' , json_encode( $infojson ) );

	$zip->close();
	
	return $filename;	
}

function ftp_push_export( $upfile, $topath , $dat ){
	
	$conn = ftp_connect($dat['server']);
	$login = ftp_login($conn, $dat['name'], $dat['pass']);

	$remote = '/'.$topath.'/'.basename( $upfile, '.zip' ).'.kimbex';

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