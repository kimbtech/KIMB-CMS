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

define("KIMB_CMS", "Clean Request");

//Diese Datei ist Teil des Backends, sie wird direkt aufgerufen.

//Konfiguration & Klassen & Funktionen laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Diese Datei stellt unter Other den Teil "Easy Menü Konfiguration" bereit

//Rechte prüfen 
check_backend_login( 'twentytwo' , 'more');

//Ein (i) anzeigen
$sitecontent->add_site_content('<h2>Easy Menue <span class="ui-icon ui-icon-info" title="Erlauben Sie Usern, welche nur Seiten erstellen können, zu einer Seite ein Menue zu erstellen!" style="display:inline-block;"></span></h2>');

//dbf laden
$easyfile = new KIMBdbf( 'backend/easy_menue.kimb' );

//Änderungen speichern?
if( !empty( $_POST['oo'] ) || !empty( $_POST['stat'] ) ){
	
	//Status der neuen Menüs und EasyMenü on/off speichern
	
	//Sachen zu tun (on/off, Status)
	$dos = array( 'oo' => 'Easy Menue on/off', 'stat' => 'Der Status des neuen Menues' );
	//beides durchgehen
	foreach( $dos as $do => $name ){
		//wurde was geändert
		if( $easyfile->read_kimb_one( $do ) != $_POST[$do] ){
			//wenn ja, neu schreiben
			$easyfile->write_kimb_one( $do, $_POST[$do] );
			
			//Meldung ausgeben
			$sitecontent->echo_message( $name.' wurde angepasst.' );
		}
	}
	
	//Menuewahl speichern
	
	//Sachen zu tun (Menüpunkt, Untermenü)
	$dos = array( 'same', 'deeper' );
	//machen
	foreach( $dos as $do ){
		//Die Einstellungen werden per TeilPlus in der dbf gespeichert, erstmal alles entfernen
		$easyfile->write_kimb_teilpl_del_all( $do );
		//jeweils für  Menüpunkt und Untermenü wird ein Array übergeben, dieses auslesen
		foreach( $_POST[$do] as $wert ){
			if( $do == 'deeper' ){
				//bei einem Untermenü sieht die Speicherung und Übergabe so aus: "ResuestID||NextID"
				//	Teilung der Werte zur Prüfung 
				$array = explode( '||', $wert );
			}
			//Bei einem Menüpunkt wird nur die NextID angegeben
			
			//Prüfung der Werte
			if( ( ( $wert == 'first' || is_numeric( $wert ) ) && $do == 'same' )
				|| ( $do == 'deeper' && is_numeric( $array[0] ) && ( $array[1] == 'first' || is_numeric( $array[1] ) ) )
			 ){
				 //wenn okay, hinzufügen
				 $easyfile->write_kimb_teilpl($do, $wert, 'add');
			}
		}
	}
}

//Status und on/off laden

//Standard ist off und Status auf on  
$checked = array( 'ooon' => '', 'oooff' => 'checked="checked"', 'staton' => 'checked="checked"', 'statoff' => '' );
//Sachen zu tun
$dos = array( 'oo', 'stat' );
foreach( $dos as $do ){
	//lesen des Wertes aus der dbf
	$wert = $easyfile->read_kimb_one( $do );
	if( $wert == 'on' ){
		//wenn on, dann dieses Feld auf checked setzen
		$checked[$do.'on'] = 'checked="checked"';
		$checked[$do.'off'] = '';
	}
	elseif( $wert == 'off' ){
		//wenn off, dann das andere Feld auf checked setzen
		$checked[$do.'off'] = 'checked="checked"';
		$checked[$do.'on'] = '';
	}
	//weder on noch off -> Standard
}

//Menuewahl laden

//beide Möglichkeiten durchgehen
$dos = array( 'same', 'deeper' );
foreach( $dos as $do ){
	foreach( $easyfile->read_kimb_all_teilpl( $do ) as $wert ){
		//für jeden Wert als Key das Value checked ins Array setzen
		$checked[$do][$wert] = 'checked="checked"';
	} 
}

//Formular beginnen
$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_easymenue.php" method="post">');

//on/off
$sitecontent->add_site_content('<br /><h4>De-/ Aktivieren</h4>');
//Array mit checked richtig ausgeben
$sitecontent->add_site_content('<input type="radio" name="oo" value="on" '.$checked['ooon'].'> <span style="display:inline-block;" title="Easy Menue aktivieren!" class="ui-icon ui-icon-check"></span>');
$sitecontent->add_site_content('<input type="radio" name="oo" value="off" '.$checked['oooff'].'> <span style="display:inline-block;" title="Easy Menue deaktivieren!" class="ui-icon ui-icon-closethick"></span>');

//Status
$sitecontent->add_site_content('<br /><h4>Status des neuen Menues</h4>');
//Array mit checked richtig ausgeben
$sitecontent->add_site_content('<input type="radio" name="stat" value="on" '.$checked['staton'].'> <span style="display:inline-block;" title="Das neue Menue auf den Status &apos;on&apos; setzen!" class="ui-icon ui-icon-check"></span>');
$sitecontent->add_site_content('<input type="radio" name="stat" value="off" '.$checked['statoff'].'> <span style="display:inline-block;" title="Das neue Menue auf den Status &apos;off&apos; setzen!" class="ui-icon ui-icon-closethick"></span>');

//Tabelle für Menüwahl beginnen
$sitecontent->add_site_content('<br /><h4>Erlaubte Menues</h4><table>');
$sitecontent->add_site_content('<th></th> <th>Menü <b title="Menü auf diesem Niveau erlauben">*</b></th> <th>Untermenü <b title="Untermenü erlauben ( Ein Untermenü kann nur einmal erstellt werden, danach wird es automatisch als Menü gelistet. )">*</b></th>');

//Menüarray erstellen
$menuearray =  make_menue_array_helper();

//Menüarray durchgehen
foreach( $menuearray as $menue ){
	
	//erstmal die Ausgabe vorn letztem Durchgang weg
	$out = array();
	
	//Das Niveau des Menüs zur Veranschaulichung anzeigen
	$out['niveau'] = str_repeat( '==>' , $menue['niveau'] );
	//Den Menünamen anzeigen
	$out['name'] = $menue['menuname'];
	//wenn Menü noch kein Untermenü hat, kann dieses per EasyMenue erstellbar gemacht werden
	if( empty( $menue['nextid'] ) ){
		//Checkbox für Untermenü anzeigen
		//	checked aus dem oben erzeugten Array mit Value der Checkbox lesen 
		$out['deeper'] = '<input type="checkbox" name="deeper[]" value="'.$menue['requid'].'||'.$menue['fileid'].'" '.$checked['deeper'][$menue['requid'].'||'.$menue['fileid']].'>';
	}
	//jede URL-Datei soll nur einmal mit EasyMenü auswählbar sein (mehrfach macht keinen Sinn)
	//	Array $filearr enthält alle durchgegangenen Dateien, nur wenn neue Datei neue Checkbox
	if( !in_array( $menue['fileid'], $filearr ) ){
		//Checkbox für Menüpunkte erstellen
		//	checked aus dem oben erzeugten Array mit Value der Checkbox lesen
		$out['same'] = '<input type="checkbox" name="same[]" value="'.$menue['fileid'].'" '.$checked['same'][$menue['fileid']].'>';
	}
	//URL-Datei dem Array anfügen
	$filearr[] = $menue['fileid'];
		
	//Tabellenzeile ausgeben
	$sitecontent->add_site_content('<tr><td>'.$out['niveau'].' <i>'.$out['name'].'</i></td><td>'.$out['same'].'</td><td>'.$out['deeper'].'</td></tr>' );
	
}

$sitecontent->add_site_content('</table>');

//Speichern Button
$sitecontent->add_site_content('<input type="submit" value="Speichern"></form>');


//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
