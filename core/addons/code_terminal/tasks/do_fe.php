<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies.eu
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
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

$sitecontent->add_site_content( '<h3>Frontend</h3>' );

//Status
$oo = $html_out_konf->read_kimb_one( 'fe' );
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
$html_out_fe = new KIMBdbf( 'addon/code_terminal__fe.kimb' );

//Auswahl ob first oder second
//	gilt nur für Ausgaben, hier aber schon nötig für Formular URL

//Trennung
$sitecontent->add_site_content( '<br /><br />' );

//für Wahlbuttons speichern
$addonurlo = $addonurl;

//First gewählt?
if( isset( $_GET['first'] ) ){
	//Vars anpassen
	$part = 'first';
	$addonurl = $addonurl.'&amp;first';
	
	//gewählten Button disable
	$sitecontent->add_html_header( '<script> $(function() { $( "a#firstssecond.first" ).button({ disabled: true }); }); </script>');
}
//Second gewählt? 
elseif( isset( $_GET['second'] ) ){
	//Vars anpassen
	$part = 'second';
	$addonurl = $addonurl.'&amp;second';
	
	//gewählten Button disable
	$sitecontent->add_html_header( '<script> $(function() { $( "a#firstssecond.second" ).button({ disabled: true }); }); </script>');
}
//keine Wahl?
else{
	//Wahl verlangen
	$sitecontent->echo_message( 'Bitte wählen Sie welche Texte Sie bearbeiten wollen.', 'Auswahl erforderlich!' );
	$part = false;
	$sitecontent->add_site_content( '<br /><br />' );
}

//Wahlbuttons
//	JS
$sitecontent->add_html_header( '<script> $(function() { $( "a#firstssecond" ).button(); }); </script>');
//	HTML
$sitecontent->add_site_content( '<center>' );
$sitecontent->add_site_content('<a href="'.$addonurlo.'&amp;first" title="Ausgabe vor dem Seiteninhalt" id="firstssecond" class="first" >Obere Ausgabe</a>');
$sitecontent->add_site_content('<a href="'.$addonurlo.'&amp;second" title="Ausgabe nach dem Seiteninhalt" id="firstssecond" class="second" >Untere Ausgabe</a>');
$sitecontent->add_site_content( '</center>' );

//Trennung
$sitecontent->add_site_content( '<br />' );

if( $part != false ){
	//Formular beginnen
	$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');
	
	//Wish
	$sitecontent->add_site_content( '<h4>Einbindung</h4>' );
	$sitecontent->add_site_content( 'Bitte wählen Sie wo die Eingaben ausgegeben werden sollen:<br />' );
	
	//alle Teile
	$wishdo = array( 'reihen', 'ids', 'error' );
	
	//Übergabe?
	if( isset( $_POST['send'] ) ){
		//noch nichts gemacht
		$do = false;
		
		//alle Werte für wishes durchgehen
		foreach( $wishdo as $wish ){
			//Wert darf nicht leer sein!
			if( empty( $_POST[$wish] ) ){
				$error[] = true;	
			}
			else{
				$error[] = false;
			}
			
			//aktuellen Wert lesen
			$dbfval = $html_out_fe->read_kimb_one( $wish );
			//aktueller Wert anders als Übergabe?
			if( $_POST[$wish] != $dbfval){
				
				//dbf anpassen
				$html_out_fe->write_kimb_one( $wish, $_POST[$wish] );
					
				//etwas geändert
				$do = true;
			}
			
			//wish ids gewählt?
			if( $wish == 'ids' && $_POST[$wish] != 'a' ){
				//wish Wert anpassen, ID mit r versehen
				$_POST[$wish] = 'r'.$_POST[$wish];
			}
		}
		
		if( !$error[0] && !$error[1] && !$error[2] && $do ){
			//Add-on API wish
			$a = new ADDonAPI( 'code_terminal' );
			$a->set_fe( $_POST['reihen'], $_POST['ids'], $_POST['error'] );
			
			//Meldung
			$sitecontent->echo_message( 'Die FE Einbindung wurde im CMS registriert.', 'Einbindung' );
		}
		elseif( $do ){
			//Meldung
			$sitecontent->echo_message( '<b>Bitte füllen Sie alle Felder unter Einbindung!</b>', 'Einbindung' );
		}
		
		//wenn Meldungen vorhanden
		if( $do ){
			//ausgeben
			$sitecontent->echo_message( 'Die Werte wurden angepasst!', 'Einbindung' );
		}
	}
	
	//aktelle Werte lesen
	foreach( $wishdo as $wish ){
		$$wish = $html_out_fe->read_kimb_one( $wish );
	}
	
	//vorne oder hinten Dropdown
	$sitecontent->add_html_header('<script>$(function(){ $( "[name=reihen]" ).val( "'.$reihen.'" ); }); </script>');
	$sitecontent->add_site_content( '<select name="reihen" title="Stelle innerhalb der Add-ons."><option value="vorn">Vorne</option><option value="hinten">Hinten</option></select>' );
	
	//Auswahl IDs
	$sitecontent->add_html_header('<script>$(function(){ $( "[name=ids]" ).append( "<option value=\'a\'>Alle</option>" ); $( "[name=ids]" ).val( "'.$ids.'" ); }); </script>');
	$sitecontent->add_site_content( '<span title="Auf welcher Seite des Frontends soll ausgegeben werden?">'.id_dropdown( 'ids', 'requid' ).'</span>' );
	
	//Auswahl Fehler
	$sitecontent->add_html_header('<script>$(function(){ $( "[name=error]" ).val( "'.$error.'" );  $( "option" ).tooltip( { position: { my: "left+15 center", at: "right center" } } ); }); </script>');
	$sitecontent->add_site_content( '<select name="error" title="Wann soll ausgegeben werden?">
		<option value="no" title="Immer ausgeben, sofern keine Fehler.">Keine Fehler</option>
		<option value="all" title="Immer ausgeben, auch bei Fehlern">Immer</option>
		<option value="403" title="Nur bei Fehler 403 ausgeben">Fehler 403</option>
		<option value="404" title="Nur bei Fehler 404 ausgeben">Fehler 404</option>
	</select>' );
	
	//Ausgaben
	//	first und second beachten!!!
	
	//mögliche Felder in der dbf und Übertragungen
	$names = array( 'sitecont' => 'Seiteninhalt', 'area_class' => 'CSS Class', 'area_cont' => 'Add-on Area Inhalt', 'header' => 'HTML-Header' );
	//Übergabe?
	if( isset( $_POST['send'] ) ){
		//alle Möglichkeiten durchgehen
		foreach( $names as $tag => $name ){
			
			//bei allen, außer header first/second an den dbf Tag ran
			if( $tag != 'header' ){
				$dbftag = $part.'_'.$tag;
			}
			else{
				$dbftag = $tag;
			}
			
			//aktuellen Wert lesen
			$dbfval = $html_out_fe->read_kimb_one( $dbftag );
			//aktueller Wert anders als Übergabe?
			if( $_POST[$tag] != $dbfval){
					
				//dbf anpassen
				$html_out_fe->write_kimb_one( $dbftag, $_POST[$tag] );
					
				//Medlung vorbereiten
				$message .= '"'.$name.'" wurde angepasst!<br />';
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
		
		//bei allen, außer header first/second an den dbf Tag ran
		if( $tag != 'header' ){
			$dbftag = $part.'_'.$tag;
		}
		else{
			$dbftag = $tag;
		}
		
		//in Variable nach Tagnamen lesen
		$$tag = $html_out_fe->read_kimb_one( $dbftag );
	}

	//Hinweis
	$sitecontent->add_site_content( '<br /><br />' );
	$sitecontent->echo_message( 'Wenn Sie eine der Ausgabestellen nicht nutzen wollen, lassen Sie das Feld einfach leer!', 'Hinweis');
	$sitecontent->add_site_content( '<br /><br />' );
	
	//Sitecontent
	$sitecontent->add_site_content( '<h4>Normaler Seiteninhalt</h4>' );
	//TinyMCE
	$arr['small'] = '#sitecont';
	add_tiny( false, true, $arr );
	$sitecontent->add_site_content('<textarea name="sitecont" id="sitecont" style="width:99%; height:200px;">'.htmlspecialchars( $sitecont, ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea><br />');
	$sitecontent->add_site_content('<button onclick="tinychange( \'sitecont\' ); return false;">Editor I/O</button>');
	
	//Add-on Area
	$sitecontent->add_site_content( '<h4>Add-on Area</h4>' );
	$sitecontent->add_site_content('CSS Class: <input type="text" name="area_class" value="'.htmlspecialchars( $area_class, ENT_COMPAT | ENT_HTML401,'UTF-8').'"><br />');
	//TinyMCE
	$arr['small'] = '#area_cont';
	add_tiny( false, true, $arr );
	$sitecontent->add_site_content('<textarea name="area_cont" id="area_cont" style="width:99%; height:200px;">'.htmlspecialchars( $area_cont, ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea><br />&uarr; Inhalt');
	$sitecontent->add_site_content('<button onclick="tinychange( \'area_cont\' ); return false;">Editor I/O</button>');
	
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
				if( $html_out_fe->read_kimb_one( $part.'_code' ) !=  $_POST['exec_code'] ){
					
					//Code speichern
					$html_out_fe->write_kimb_one( $part.'_code', $_POST['exec_code'] );
						
					//Medlung
					$sitecontent->echo_message( 'Der PHP-Code wurde angepasst');
				}
			}
			
			//Code aus dbf lesen
			$code = $html_out_fe->read_kimb_one( $part.'_code' );
			if(empty($code)){
				//wenn leer, Beispiel
				$code = '<?php'."\r\n\r\n".'?>';
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
	
	//Hinweis
	$sitecontent->add_site_content( '<br /><br />' );
	$sitecontent->echo_message( 'Sofern Ihr Code fehlerhaft ist, kann das Backend des CMS unbrauchbar werden. Bitte testen Sie PHP-Code im Terminal und HTML-Code z.B. mit Firebug!', 'Wichtig');
}
?>
