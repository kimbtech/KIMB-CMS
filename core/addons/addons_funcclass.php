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

//Funktionen und Klassen durch Add-ons definieren lassen 

//Add-on Datei laden, wenn nicht schon getan
if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

//Alle Add-ons lesen, die funcclass wollen
$all = $addoninclude->read_kimb_all_teilpl( 'funcclass' );

//Add-on Wünsche laden
$addonwish = new KIMBdbf('addon/wish/funcclass_stelle.kimb');

//leeres Array für Add-ons die später geladen werden sollen
$includes = array();

//ein Add-on nach dem anderen, nach seinen Wünschen, zu $includes hinzufügen
foreach( $all as $add ){

	//Add-on Wunsch ID lesen
	$id = $addonwish->search_kimb_xxxid( $add , 'addon' );

	//Add-on Wunsch vorhanden?
	if( $id != false ){
		//Reihenfolge Sting lesen
		//	vorn, hinten
		$wi = $addonwish->read_kimb_id( $id, 'stelle' );

		//je nach vorn oder hinten in das Array includes hinzufügen
		if( $wi == 'vorn' ){
			array_unshift( $includes , $add );
		}
		elseif( $wi == 'hinten' ){
			$includes[] = $add;
		}
	}

}

//entsprechende Dateien laden
foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_funcclass.php');

}

?>
