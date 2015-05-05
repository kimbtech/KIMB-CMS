<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
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

//includes addons die first wollen

//evtl siteid, menueid, err verarbeiten

if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

$all = $addoninclude->read_kimb_all_teilpl( 'fe_first' );

$addonwish = new KIMBdbf('addon/wish/fe_all.kimb');

$includes = array();

foreach( $all as $add ){

	$id = $addonwish->search_kimb_xxxid( $add , 'addon' );

	if( $id != false ){

		//IDs
		$wid = $addonwish->read_kimb_id( $id, 'ids' );
		$wids = false;
		$ebst = substr( $wid, 0, 1 );
		$wid = substr( $wid, 1);

		if( $ebst == 'a' ){
			$wids = true;
		}
		elseif( $ebst == 'r' && $_GET['id'] == $wid ){
			$wids = true;
		}
		elseif( $ebst == 's' && $allgsiteid == $wid ){
			$wids = true;
		}

		//Error
		$err = $addonwish->read_kimb_id( $id, 'error' );
		$error = false;

		if( $err == 'all' ){
			$error = true;
		}
		elseif( $err == 'no' && $allgerr != '404' && $allgerr != '403' ){
			$error = true;
		}
		elseif( $err == '404' && $allgerr == '404' ){
			$error = true;
		}
		elseif( $err == '403' && $allgerr == '403' ){
			$error = true;
		}

		if( $error && $wids ){
			//Reihenfolge
			$wi = $addonwish->read_kimb_id( $id, 'stelle' );
			if( $wi == 'vorn' ){
				array_unshift( $includes , $add );
			}
			elseif( $wi == 'hinten' ){
				$includes[] = $add;
			}
		}
	}

}

foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_fe_first.php');

}

$fesecondincludesaddons = $includes;

?>
