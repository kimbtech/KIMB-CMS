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

$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$felogin['loginokay'] = $felogin['conf']->read_kimb_one( 'loginokay' );
$felogin['grlist'] = $felogin['conf']->read_kimb_one( 'grlist' );
$felogin['grs'] = explode( ',' , $felogin['grlist'] );
$felogin['requid'] = $felogin['conf']->read_kimb_one( 'requid' );
$felogin['selfreg'] = $felogin['conf']->read_kimb_one( 'selfreg' );

foreach( $felogin['grs'] as $felogin['gr'] ){

	$felogin['grteile'] = $felogin['conf']->read_kimb_one( $felogin['gr'] );

	$felogin['teilesite'][$felogin['gr']] = explode( ',' , $felogin['grteile'] );

	foreach($felogin['teilesite'][$felogin['gr']] as $felogin['teilsite'] ){

		$felogin['allsites'][] = $felogin['teilsite'];

	}
}

function check_felogin_login( $gruppe = '---session---', $siteid = '---allgsiteid---'  ){
	global $felogin, $allgsiteid;

	if( $gruppe == '---session---'){
			$gruppe = $_SESSION['felogin']['gruppe'];
	}
	if( $siteid == '---allgsiteid---' ){
		$siteid = $allgsiteid;
	}

	if( in_array( $allgsiteid , $felogin['allsites'] ) ){
		if( $felogin['loginokay'] == $_SESSION['felogin']['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

			if( in_array( $siteid , $felogin['teilesite'][$gruppe] ) ){
				return true;
			}
			else{
				return false;
			}

		}
		else{
			return false;
		}
	}
	else{
		return true;
	}
}

$addon_felogin_array = $felogin;
unset ( $felogin );
?>
