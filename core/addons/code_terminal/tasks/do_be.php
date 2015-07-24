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

//Überschrift
$sitecontent->add_site_content( '<h3>Backend</h3>' );

//Status
$oo = $html_out_konf->read_kimb_one( 'be' );
//	jQuery Icons HTML Code
if( $oo == 'on' ){
	$status = '<span style="display:inline-block;" title="Aktiviert" class="ui-icon ui-icon-check"></span>';
}
else{
	$status = '<span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>';
}
//Status
$sitecontent->add_site_content( 'Aktueller Status: '.$status.' <a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon=code_terminal" target="_blank">ändern</a>' );

//dbf für be lesen
$html_out_be = new KIMBdbf( 'addon/code_terminal__be.kimb' );

//Formular beginnen
$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

//Wish
$sitecontent->add_site_content( '<h4>Einbindung</h4>' );
$sitecontent->add_site_content( 'Bitte wählen Sie wo die Eingaben ausgegeben werden sollen:<br />' );
$wishdo = array( 'reihen', 'site', 'rechte' );

//Übergabe?
if( isset( $_POST['send'] ) ){
	//noch nichts gemacht
	$do = false;
	
	//alle Werte für wishes durchgehen
	foreach( $wishdo as $wish ){
		//Wert darf nicht leer sein!
		if( !empty( $_POST[$wish] ) ){
			//aktuellen Wert lesen
			$dbfval = $html_out_be->read_kimb_one( $wish );
			//aktueller Wert anders als Übergabe?
			if( $_POST[$wish] != $dbfval){
				
				//API
				
				//dbf anpassen
				$html_out_be->write_kimb_one( $wish, $_POST[$wish] );
				
				//etwas geändert
				$do = true;
			}
		}
	}
	
	//wenn Medlungen vorhanden
	if( $do ){
		//ausgeben
		$sitecontent->echo_message( 'Die Werte wurden angepasst!', 'Einbindung' );
	}
}

foreach( $wishdo as $wish ){
	$$wish = $html_out_be->read_kimb_one( $wish );
}

$sitecontent->add_html_header('<script>$(function(){ $( "[name=reihen]" ).val( "'.$reihen.'" ); }); </script>');
$sitecontent->add_site_content( '<select name="reihen" title="Nur innerhalb der Add-ons."><option value="vorn">Vorne</option><option value="hinten">Hinten</option></select>' );

$sitesopt = '<option value="all">Überall</option>
<option value="index">Home/Login</option>
<option value="sites">Seiten</option>
<option value="menue">Menue</option>
<option value="user">User</option>
<option value="syseinst">Konfiguration</option>
<option value="addon_conf">Add-on Einstellungen</option>
<option value="addon_inst">Add-on Installation</option>
<option value="other_filemanager">Filemanager</option>
<option value="other_themes">Themes</option>
<option value="other_level">Userlevel Backend</option>
<option value="other_umzug">Umzug</option>
<option value="other_lang">Mehrsprachige Seite</option>
<option value="other_easymenue">Easy Menue</option>';
$sitecontent->add_html_header('<script>$(function(){ $( "[name=site]" ).val( "'.$site.'" ); }); </script>');
$sitecontent->add_site_content( '<select name="site" title="Auf welcher Seite des Backends soll ausgegeben werden?">'.$sitesopt.'</select>' );

$sitecontent->add_site_content('<input type="text" size="20" name="rechte" value="'.$rechte.'"><br />');

//mögliche Felder in der dbf und Übertragungen
$names = array( 'sitecont' => 'Seiteninhalt', 'mess_h' => 'Meldung Überschrift', 'mess_cont' => 'Inhalt der Meldung', 'header' => 'HTML-Header' );

//Übergabe?
if( isset( $_POST['send'] ) ){
	//alle Möglichkeiten durchgehen
	foreach( $names as $tag => $name ){
		//Übergabe darf nicht leer sein
		if( !empty( $_POST[$tag] ) ){
			//aktuellen Wert lesen
			$dbfval = $html_out_be->read_kimb_one( $tag );
			//aktueller Wert anders als Übergabe?
			if( $_POST[$tag] != $dbfval){
				
				//dbf anpassen
				$html_out_be->write_kimb_one( $tag, $_POST[$tag] );
				
				//Medlung vorbereiten
				$message .= '"'.$name.'" wurde angepasst!<br />';
			}
		}
	}
	
	//wenn Medlungen vorhanden
	if( !empty( $message ) ){
		//ausgeben
		$sitecontent->echo_message( $message );
	}
}

//alle Werte lesen
foreach( $names as $tag => $name ){
	$$tag = $html_out_be->read_kimb_one( $tag );
}

//Sitecontent
$sitecontent->add_site_content( '<h4>Normaler Seiteninhalt</h4>' );
//TinyMCE
$arr['small'] = '#sitecont';
add_tiny( false, true, $arr );
$sitecontent->add_site_content('<textarea name="sitecont" id="sitecont" >'.htmlspecialchars( $sitecont, ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea>');

//Message
$sitecontent->add_site_content( '<h4>Medlung</h4>' );
$sitecontent->add_site_content('Überschrift: <input type="text" name="mess_h" value="'.htmlspecialchars( $mess_h, ENT_COMPAT | ENT_HTML401,'UTF-8').'"><br />');
//TinyMCE
$arr['small'] = '#mess_cont';
add_tiny( false, true, $arr );
$sitecontent->add_site_content('<textarea name="mess_cont" id="mess_cont">'.htmlspecialchars( $mess_cont, ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea><br />&uarr; Inhalt');

//Header
$sitecontent->add_site_content( '<h4>HTML Header</h4>' );
$sitecontent->add_site_content('<textarea name="header" id="htmlcodearea">'.htmlspecialchars( $header, ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea>');

//PHP
//nur User der Gruppe 'more' erlauben
	if( check_backend_login( 'no' , 'more', false ) ){
		$sitecontent->add_site_content( '<h4>PHP-Code</h4>' );
		
		//Code übergeben?
		if( isset( $_POST['exec_code'])){
			
			//bei eval gibt es keine <?php, weg damit
			$_POST['exec_code'] = str_replace( array( '<?php', '?>' ), '', $_POST['exec_code']); 
			
			//ist der übergebene Code anders als der in der dbf?
			if( $html_out_be->read_kimb_one( 'code' ) !=  $_POST['exec_code'] ){
				//Übergabe darf nicht leer sein!
				if(  !empty( $_POST['exec_code'] ) ){
				
					//Code speichern
					$html_out_be->write_kimb_one( 'code', $_POST['exec_code'] );
					
					//Medlung
					$sitecontent->echo_message( 'Der PHP-Code wurde angepasst');
				}
			}
		}
		
		//Code aus dbf lesen
		$code = $html_out_be->read_kimb_one( 'code' );
		if(empty($code)){
			//wenn leer, Beispiel
			$code = '<?php'."\r\n\r\n".'echo $_SERVER[\'REMOTE_ADDR\'];'."\r\n\r\n".'?>';
		}
		else{
			$code = '<?php'.$code.'?>';
		}
		
		//Eingabe
		$sitecontent->add_site_content( '<textarea id="phpcodearea" name="exec_code">'.htmlspecialchars( $code, ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea>');		
	}

//Button
$sitecontent->add_site_content( '<input type="hidden" name="send" value="yes">');
$sitecontent->add_site_content( '<input type="submit" value="Speichern">');
$sitecontent->add_site_content( '</form>');
?>
