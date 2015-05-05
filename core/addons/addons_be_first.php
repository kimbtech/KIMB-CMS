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



defined('KIMB_Backend') or die('No clean Request');

//includes addons die first wollen

if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

$all = $addoninclude->read_kimb_all_teilpl( 'be_first' );

$addonwish = new KIMBdbf('addon/wish/be_all.kimb');

//Reihenfolge && Rechte && Sites
$includes = array();
$url = get_req_url();

foreach( $all as $add ){

	$id = $addonwish->search_kimb_xxxid( $add , 'addon' );

	if( $id != false ){

		//Rechte
		$re = $addonwish->read_kimb_id( $id, 'recht' );
		$recht = false;

		foreach( explode( ',' , $re ) as $res ){
			if( $res == 'more' && $_SESSION['permission'] == 'more' ){
				$recht = true;
			}
			elseif( $res == 'less' && $_SESSION['permission'] == 'less' ){
				$recht = true;
			}
			elseif( check_backend_login( $res , 'none', false ) ){
				$recht = true;
			}
			elseif( $res == 'no' ){
				$recht = true;
			}

			if( $recht ){
				break;
			}
		}

		//Sites
		$si = $addonwish->read_kimb_id( $id, 'site' );
		if( $si == 'all' ){
			$site = true;
		}
		elseif( substr( $url , '-'.strlen( 'kimb-cms-backend/'.$si.'.php' ) ) == 'kimb-cms-backend/'.$si.'.php' ){
			$site = true;
		}
		else{
			$site = false;
		}

		if( $site && $recht ){
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

//Ausführen
foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_be_first.php');

}

$besecondincludesaddons = $includes;

?>
