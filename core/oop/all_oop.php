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

function autoload_classes( $class ){
	$classarray = array(
		'ADDonAPI' => 'addon_api',
		'cacheCMS' => 'cache',
		'KIMBdbf' => 'kimbdbf',
		'system_output' => 'output',
		'backend_output' => 'output_backend',
		'JSforBE' => 'be_do/js_dialog',
		'BEmenue' => 'be_do/menue',
		'BEsites' => 'be_do/sites',
		'BEsyseinst' => 'be_do/syseinst',
		'BEuser' => 'be_do/user',
		'BEaddconf' => 'be_do/addon_conf',
		'BEaddinst' => 'be_do/addon_inst'
	);
	
	require_once( __DIR__.'/'.$classarray[$class].'.php' );
}

spl_autoload_register('autoload_classes');

?>
