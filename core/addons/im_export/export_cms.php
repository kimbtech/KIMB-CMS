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

if( isset( $_POST['send'] ) ){

	$file = make_cms_export( $_POST['teile'] );
	
	if( $file == 'fileerror' ){
		$sitecontent->echo_error('Die Exportdatei konnte nicht erstellt werden!');
	}
	else{
		header('Content-type: application/kimbex');
		header('Content-Disposition: attachment; filename= export_'.date("dmY").'_'.time().'.kimbex');
		readfile( $file );
		die;
	}
}
else{

	$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post">');

	$sitecontent->add_site_content('<input type="checkbox" value="sites" name="teile[]" onclick=" $(\'input[value=menue]\').prop(\'checked\', false); ">');
	$sitecontent->add_site_content('Seiten <span style="display:inline-block;" title="Alle Seiteninhalte exportieren." class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="menue" name="teile[]" onclick=" $(\'input[value=sites]\').prop(\'checked\', true); " >');
	$sitecontent->add_site_content('Menüstruktur <span style="display:inline-block;" title="Die gesamte Menüstruktur und die Zuordnungen zu Seiten exportieren. (Seiten müssen auch exportiert werden.)" class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="users" name="teile[]">');
	$sitecontent->add_site_content('User <span style="display:inline-block;" title="Alle Backend-User und Userlevel exportieren." class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="addon" name="teile[]">');
	$sitecontent->add_site_content('Add-on Konfiguration <span style="display:inline-block;" title="Die Konfiguration aller Add-ons exportieren. (Achtung: Die Add-ons selber werden nicht exportiert, sie müssen auf dem Import-System vorhanden sein!)" class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="confs" name="teile[]">');
	$sitecontent->add_site_content('Systemkonfiguration <span style="display:inline-block;" title="Die Systemkonfiguration und die Fehlertexte exportieren. (Es werden nur inhaltliche Werte exportiert, z.B. der Seitenname und die Description.) " class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="filem" name="teile[]">');
	$sitecontent->add_site_content('Filemanager <span style="display:inline-block;" title="Sichern Sie alle Dateien aus dem Filemanager. (Dies kann große Exportdateien verursachen!)" class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');

	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="hidden" value="send" name="send">');
	$sitecontent->add_site_content('<input type="submit" value="Exportieren">');
	$sitecontent->add_site_content('</form>');

}

?>