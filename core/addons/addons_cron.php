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

$output['allgstart'] = time();

if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

$cronfile = new KIMBdbf('addon/wish/cron_age.kimb');

foreach( $addoninclude->read_kimb_all_teilpl( 'cron' ) as $cron ){
	
	$id = $cronfile->search_kimb_xxxid( $cron , 'addon' );
	
	if( $id != false ){
		
		$minage = $cronfile->read_kimb_id( $id, 'minage' );
		$lastaufruf[$cron] = $cronfile->read_kimb_id( $id, 'lastaufruf' );
		
		if( $lastaufruf[$cron] + $minage < time() ){
			$cronmake[] = $cron;
			
			$cronfile->write_kimb_id( $id , 'add' , 'lastaufruf' , time() );
		}
	}
}

$i = 0;

foreach ( $cronmake as $name ){

	$infos['start'] = time();
	$infos['lastaufruf'] = $lastaufruf[$name];
	$infos['name'] = $name;

	require_once(__DIR__.'/'.$name.'/include_cron.php');

	$infos['end'] = time();

	$output[] = $infos;
	
	$i++;
}

$output['doneaddons'] = $i;
$output['allgend'] = time();

echo json_encode( $output );

die;

?>
