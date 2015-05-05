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


if( !empty( $_GET['addon'] ) ){

	if( !isset( $addoninclude ) ){
		$addoninclude = new KIMBdbf('addon/includes.kimb');
	}

	if( $addoninclude->read_kimb_search_teilpl( 'ajax' , $search) ){

		if(strpos( $_GET['addon'] , "..") !== false){
			echo ('Do not hack me!!');
			die;
		}

		require_once(__DIR__.'/'.$_GET['addon'].'/include_ajax.php');

		die;

	}

}

?>
