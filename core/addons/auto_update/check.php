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

//Überprüfung der aktuellen Version mit der KIMB-API
$newver = json_decode( file_get_contents( 'https://api.kimb-technologies.eu/cms/getcurrentversion.php' ) , true );

//aktuelle Version mit der des CMS vergleichen
if( compare_cms_vers( $allgsysconf['build'] , $newver['currvers'] ) == 'older' ){
	//Update möglich -> Variablen setzen
	$update = 'yes';
	$updatearr['do'] = 'yes';
	$updatearr['sysv'] = $allgsysconf['build'];
	$updatearr['newv'] = $newver['currvers'];
}
else{
	//kein Update -> auch Variablen setzen
	$update = 'no';
	$updatearr['do'] = 'no';
	$updatearr['sysv'] = $allgsysconf['build'];
	$updatearr['newv'] = $newver['currvers'];
}

?>
