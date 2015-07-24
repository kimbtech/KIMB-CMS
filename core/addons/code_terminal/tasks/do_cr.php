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

$sitecontent->add_site_content( '<h3>Cron</h3>' );

//dbf für fc lesen
$html_out_cr = new KIMBdbf( 'addon/code_terminal__cr.kimb' );

//Code übergeben?
if( isset( $_POST['exec_code'])){
	
	//bei eval gibt es keine <?php, weg damit
	$_POST['exec_code'] = str_replace( array( '<?php', '?>' ), '', $_POST['exec_code']); 
	
	//ist der übergebene Code anders als der in der dbf?
	if( $html_out_cr->read_kimb_one( 'code' ) !=  $_POST['exec_code'] ){
		//Übergabe darf nicht leer sein!
		if(  !empty( $_POST['exec_code'] ) ){
		
			//Code speichern
			$html_out_cr->write_kimb_one( 'code', $_POST['exec_code'] );
			
			//Medlung
			$sitecontent->echo_message( 'Der PHP-Code wurde angepasst');
		}
	}
}
//einen Wert für wish übergeben?
if( isset( $_POST['wish'] ) ){
	//hat sich der Wert geändert?
	if( $html_out_cr->read_kimb_one( 'wish' ) !=  $_POST['wish'] ){
		//Wert muss nummerisch sein
		if( is_numeric( $_POST['wish'] ) ){
			
			//Wert in dbf speichern
			$html_out_cr->write_kimb_one( 'wish', $_POST['wish'] );
			
			//Wert der API mitteilen
			$a = new ADDonAPI( 'code_terminal' );
			$a->set_cron( $_POST['wish'] );
			
			//Medlung
			$sitecontent->echo_message( 'Die Zeit wurde angepasst');
		}
	}	
}

//aktuelle Daten lesen
//	Status
$oo = $html_out_konf->read_kimb_one( 'cr' );
//	jQuery Icons HTML Code
if( $oo == 'on' ){
	$status = '<span style="display:inline-block;" title="Aktiviert" class="ui-icon ui-icon-check"></span>';
}
else{
	$status = '<span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>';
}
//	PHP-Code
$code = $html_out_cr->read_kimb_one( 'code' );
if(empty($code)){
	//wenn leer, Beispiel
	$code = '<?php'."\r\n\r\n".'$infos[\'ip\'] = $_SERVER[\'REMOTE_ADDR\'];'."\r\n\r\n".'?>';
}
else{
	$code = '<?php'.$code.'?>';
}
//API-Wish Wert
$wish = $html_out_cr->read_kimb_one( 'wish' );

//Hinweis
$sitecontent->add_site_content( 'Der hier angegebene Code wird bei jedem Aufruf der Cron Funktion des CMS geladen und ausgeführt.' );
$sitecontent->echo_message( 'Bitte geben Sie nichts aus, alle Ausgaben kommen in das Array $infos und werden automatisch als JSON ausgegeben!', 'Wichtig');
$sitecontent->add_site_content( '<br /><br />' );
//Status
$sitecontent->add_site_content( 'Aktueller Status: '.$status.' <a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon=code_terminal" target="_blank">ändern</a>' );
$sitecontent->add_site_content( '<br /><br />' );
//Eingaben
$sitecontent->add_site_content( '<form action="'.$addonurl.'" method="post">');
//	Add-on Wish
$sitecontent->add_site_content( '<input type="number" name="wish" value="'.$wish.'" placeholder="3600 &#8793; 1 h"><b title="Geben Sie die Zeit in Sekunden an, die mindestens zwischen zwei Ausführungen ablaufen soll. (Der tatsächliche Abstand hängt von der Anzahl der Aufrufe der cron.php ab!)">*</b>' );
$sitecontent->add_site_content( '<br /><br />' );
//	Editor
$sitecontent->add_site_content( '<textarea id="phpcodearea" name="exec_code">'.htmlspecialchars( $code,ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea>');
$sitecontent->add_site_content( '<br />' );
//	Button
$sitecontent->add_site_content( '<input type="submit" value="Speichern">');
$sitecontent->add_site_content( '</form>');

$sitecontent->echo_message( 'Sofern Ihr Code einen "Error" produziert, wird die Cron Funktion unbrauchbar. Bitte testen Sie den Code im Terminal!', 'Wichtig');

?>