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

//alles aus gesichertem Array von FE fist (oben) ausführen
foreach( $fesecondincludesaddons as $name ){

	//CMS Fehlerstatus immernoch wie bei First?
	$wish = $fesecondincludesaddons_errwish[$name];
	
	//bei allem, dann einbinden
	if( $wish == 'all' ){
		$error = true;
	}
	//keine Fehler gewünscht und auch keine gegeben, dann einbinden
	elseif( $wish == 'no' && $allgerr != '404' && $allgerr != '403' ){
		$error = true;
	}
	//Fehler 404 gewünscht, dann einbinden
	elseif( $wish == '404' && $allgerr == '404' ){
		$error = true;
	}
	//Fehler 403 gewünscht, dann einbinden
	elseif( $wish == '403' && $allgerr == '403' ){
		$error = true;
	}
	else{
		//Fehlerstatus erlaubt Einbinden nicht!
		$error = false;
	}
	
	//Datei second vorhanden?
	//Und Fehlerstatus okay?
	if( $error && is_file(  __DIR__.'/'.$name.'/include_fe_second.php' ) ){
		//Datei second laden
		require_once(__DIR__.'/'.$name.'/include_fe_second.php');
	}

}

?>
