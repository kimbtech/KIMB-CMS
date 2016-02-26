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

//URL
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=search_sitemap';

//Formular
$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

//Konfigurationsdatei
$conffile = new KIMBdbf( 'addon/search_sitemap__conf.kimb' );

//Daten gesendet?
if( isset( $_POST['send'] ) ){

	//dbf und POST Namen
	$allnames = array( 'mapsiteid', 'searchsiteid', 'searchform', 'maxversuch', 'maxerg' );

	//Beispielwerte
	$examples = array( 'mapsiteid' => '2', 'searchsiteid' => '2', 'searchform' => 'on', 'maxversuch' => '100', 'maxerg' => '10' );

	//alle Post durchgehen
	foreach( $_POST as $name => $val ){

		//ist der Post Wert einer der gefragten?
		if( in_array( $name, $allnames ) ){

			//Ist die Übergabe leer?
			if( empty( $val ) ){
				//Beispiel verwenden
				$val = $examples[$name];
			}

			//aktuellen Wert lesen
			$old = $conffile->read_kimb_one( $name );

			//sind die Werte verschieden?
			if( $old != $val ){
				//Wert neu schreiben
				$conffile->write_kimb_one( $name , $val );
				//Medlung
				$sitecontent->echo_message( 'Der Wert "'.$name.'" wurde geändert!' );
			}		

		}

	}
} 

//Sitemap: Wahl der Seite
$sitecontent->add_site_content('<h3>Sitemap</h3>');
$sitecontent->add_html_header('<script>$(function(){ $( "[name=mapsiteid]" ).append( "<option value=\'off\'>Off</option>" ); $( "[name=mapsiteid]" ).val( "'.$conffile->read_kimb_one( 'mapsiteid' ).'" ); }); </script>');
$sitecontent->add_site_content( id_dropdown( 'mapsiteid', 'siteid' ).' <span style="display:inline-block;" title="Bitte wählen Sie eine Seite auf der die Sitemap angezeigt werden soll!" class="ui-icon ui-icon-info"></span><br />');

//Suche: Wahl der Seite
$sitecontent->add_site_content('<h3>Suche</h3>');
$sitecontent->add_html_header('<script>$(function(){ $( "[name=searchsiteid]" ).append( "<option value=\'off\'>Off</option>" ); $( "[name=searchsiteid]" ).val( "'.$conffile->read_kimb_one( 'searchsiteid' ).'" ); }); </script>');
$sitecontent->add_site_content( id_dropdown( 'searchsiteid', 'requid' ).' <span style="display:inline-block;" title="Bitte wählen Sie eine Seite auf der die Suche angezeigt werden soll!" class="ui-icon ui-icon-info"></span><br />');

//kein checked
$form = array( 'on' => ' ' , 'off' => ' ' );
//wenn an, dann an auf checked
if( $conffile->read_kimb_one( 'searchform' ) == 'on' ){
	$form['on'] = ' checked="checked" ';
}
//sonst, dann aus auf checked
else{
	$form['off'] = ' checked="checked" ';
}

//Suchkasten
$sitecontent->add_site_content('<input name="searchform" value="on" type="radio" '.$form['on'].'><span style="display:inline-block;" title="Ein Formular für die Suche auf jeder Seite anzeigen (Sollte Ihr Template schon diese Funktion beinhalten, dann müssen Sie es hier deaktivieren!)" class="ui-icon ui-icon-check"></span>' );
$sitecontent->add_site_content('<input name="searchform" value="off" type="radio" '.$form['off'].'><span style="display:inline-block;" title="Das Formular nur auf der Such-Seite anzeigen." class="ui-icon ui-icon-closethick"></span><br />' );

//Versuche und Ergebnisse Anzahlen
$sitecontent->add_site_content('<input name="maxversuch" value="'.$conffile->read_kimb_one( 'maxversuch' ).'" type="number"> Maximale Versuche<b title="Geben Sie an, wie viele Seiten der Suchalgorithmus durchsuchen soll. (Achtung: Zuserst findet ein Durchlauf mit den Menuenamen statt, erst dann wird in den Seiteninhalten gesucht. (Einen Seite => 2 Versuche))">*</b><br />' );
$sitecontent->add_site_content('<input name="maxerg" value="'.$conffile->read_kimb_one( 'maxerg' ).'" type="number"> Maximale Ergebnisse<b title="Geben Sie an, nach wie vielen Ergebnissen der Suchalgorithmus abbrechen soll.">*</b><br />' );

//Senden Button
$sitecontent->add_site_content('<br /><br /><input type="hidden" value="send" name="send"><input type="submit" value="Speichern"> </form>');
?>
