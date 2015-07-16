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

$imexfile = new KIMBdbf( 'addon/im_export__cron.kimb' );

if( $imexfile->read_kimb_one( 'coo' ) == 'on' ){

	//Exportdatei (alles bis auf filemanager)
	$teile = array( 'sites', 'menue', 'users', 'addon', 'confs' );
	
	$file = make_cms_export( $teile );
	
	if( $file == 'fileerror' ){
		$infos[] = ('Die Exportdatei konnte nicht erstellt werden!');
	}
	else{
		$infos[] = ('Export erfolgreich!');
		
		$dat = $imexfile->read_kimb_id( '21' );
		$topath = $imexfile->read_kimb_one( 'topath' );
		
		$ftp = true;
		if( empty( $topath ) ){
			$ftp = false;
		}
		if( empty( $dat['server'] ) ){
			$ftp = false;
		}
		if( empty( $dat['name'] ) ){
			$ftp = false;
		}
		if( empty( $dat['pass'] ) ){
			$ftp = false;
		}
		
		if( $ftp ){
			$infos[] = ('Upload begonnen!');
			
			if( ftp_push_export( $file, $topath , $dat ) ){
				$infos[] = ('Upload erfolgreich!');
			}
			else{
				$infos[] = ('Upload fehlerhaft!');
			}
		}
		
	}
}
else{
	$infos[] = ('Export deaktivert!');
}

?>