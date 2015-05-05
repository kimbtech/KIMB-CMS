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

if(!is_object( $idfile )){
	$idfile = new KIMBdbf('menue/allids.kimb');
}

if($allgmenueid == $idfile->read_kimb_id($_GET['id'], 'menueid')){
	$allgrequestid = $_GET['id'];
}
else{
	$allgrequestid = $idfile->search_kimb_xxxid( $allgmenueid , 'menueid' );
}

if( $allgsysconf['lang'] == 'off' ){
	$requestlang['id'] = 0;
}

if( $allgsysconf['cache'] == 'on' ){
	if( $sitecache->load_cached_menue($allgmenueid, $requestlang['id'] ) ){
		$menuecache = 'loaded';
	}
}

if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
	$menuenames = new KIMBdbf('menue/menue_names_lang_'.$requestlang['id'].'.kimb');
	$menuenameslangst = new KIMBdbf('menue/menue_names.kimb');
}
else{
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	$menuenameslangst = $menuenames;
}


if( $menuecache != 'loaded'){

	$breadarrfertig = 'nok';
	
	gen_menue( $allgrequestid );

	$breadcrumblinks = '<div id="breadcrumb" >';

	$niveau = 1;
	while( $breadcrumbarr['maxniv'] >= $niveau ){

		$breadcrumblinks .= ' &rarr; ';
		$breadcrumblinks .= '<a href="'.$breadcrumbarr[$niveau]['link'].'">'.$breadcrumbarr[$niveau]['name'].'</a>';

		$niveau++;
	}
	$breadcrumblinks .= '</div>';

	if( is_object($sitecache) ){
		if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
			$sitecache->cache_addon( $allgmenueid , $breadcrumblinks , 'breadcrumb-'.$requestlang['id'] );
		}
		else{
			$sitecache->cache_addon( $allgmenueid , $breadcrumblinks , 'breadcrumb' );
		}
	}

}
else{
	if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
		$breadcrumblinks = $sitecache->get_cached_addon( $allgmenueid , 'breadcrumb-'.$requestlang['id'] );
	}
	else{
		$breadcrumblinks = $sitecache->get_cached_addon( $allgmenueid , 'breadcrumb' );
	}

	$breadcrumblinks = $breadcrumblinks[0];
}

$sitecontent->add_site_content( $breadcrumblinks );

?>
