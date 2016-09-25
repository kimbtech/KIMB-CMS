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

//Überschift
$sitecontent->add_site_content( '<h3>Terminal</h3>' );

//Code übergeben?
if( isset( $_POST['exec_code'] ) ){

	//Fehler anmachen
	ini_set( 'display_errors', 1 );
	ini_set( 'display_startup_errors', 1);
	error_reporting( E_ALL & ~E_NOTICE );
	
	if( !isset( $_POST['htmlcont'] ) ){
		//Ausgabe als Text
		header( 'Content-Type: text/plain' );
	}
	
	//CMS Root path
	define("__CMSDIR__", __DIR__.'/../../../../');

	//PHP Tags müssen für eval erst geschlossen werden
	$code = $code = ' ?> '.$_POST['exec_code'].' <?php ';

	//Code ausführen
	eval( $code );

	//alles beenden
	die;	
}
else{
	//iframe in dem Code ausgeführt wird
	$sitecontent->add_site_content( '<iframe name="frame" srcdoc="Keine Daten" style="width:100%; height:150px;" scrolling="yes" style="background-color:#000;"></iframe>' );
	
	//Trennung
	$sitecontent->add_site_content( '<br /><hr /><br />' );
	
	$sitecontent->add_site_content( '<b>__CMSDIR__</b> zeigt auf das Root-Verzeichnis des CMS.<br /><br />');

	//Codeeingabe
	$sitecontent->add_site_content( '<form action="'.$addonurl.'" method="post" target="frame">');
	//	Editor
	make_code_terminal_phpedi( "phpcodearea" );
	//	Textarea
	$sitecontent->add_site_content( '<textarea id="phpcodearea" name="exec_code">&lt;?php'."\r\n\r\n\r\n".'?&gt;</textarea>');
	$sitecontent->add_site_content( '<br />' );
	//	HTML oder Text
	$sitecontent->add_site_content( '<input type="checkbox" value="html" name="htmlcont"> Ausgabe als HTML rendern');
	$sitecontent->add_site_content( '<br /><br />' );
	//	Button
	$sitecontent->add_site_content( '<input type="submit" value="Ausführen">');
	$sitecontent->add_site_content( '</form>');
}
?>
