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

//Cronzugriff auf Add-ons ermöglichen

//$output als Array für JSON Ausgabe anlegen
//Startzeit hinzufügen
$output['allgstart'] = time();

//Add-on Datei laden, wenn nicht schon getan
if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

//Add-on Wunschdaten lesen
//	Stellen, Rechte des Users und Reihenfolge vom Add-on gewünscht
$cronfile = new KIMBdbf('addon/wish/cron_age.kimb');

//nacheinander alle Add-on mit Cron-Datei durcharbeiten
foreach( $addoninclude->read_kimb_all_teilpl( 'cron' ) as $cron ){

	//Add-on Wunsch ID suchen	
	$id = $cronfile->search_kimb_xxxid( $cron , 'addon' );
	
	//ID/Add-on vorhanden
	if( $id != false ){
		
		//Zeit basiert auf Zahlen/ Unix Timestamp/ Sekunden
		
		//Mindestalter für erneute Ausführung lesen
		//	nicht alle Crons müssen oft ausgeführt werden
		$minage = $cronfile->read_kimb_id( $id, 'minage' );
		//Zeitpunkt der letzten Ausführung lesen
		$lastaufruf[$cron] = $cronfile->read_kimb_id( $id, 'lastaufruf' );
		
		//Ist die letzte Ausführung plus das Mindestalter kleiner als der aktuelle Timestamp ?
		//	Ist das Add-on wieder auszuführen?
		if( $lastaufruf[$cron] + $minage < time() ){
			
			//Das Add-on in das Array der auszuführenden Crons hinzufügen 
			$cronmake[] = $cron;
			
			//Den letzten Aufruf anpassen 
			$cronfile->write_kimb_id( $id , 'add' , 'lastaufruf' , time() );
		}
	}
}

//Anzahl der durchgeführten Add-on Crons zählen 
$i = 0;

//Add-on Crons durchführen
foreach ( $cronmake as $name ){

	//$Infos Array für Ausgaben des jeweiligen Add-ons
	$infos = array();
	
	//Startzeitpunkt hinzufügen
	$infos['start'] = time();
	//letzten Aufruf hinzufügen (daher oben in Array gespeichert)
	$infos['lastaufruf'] = $lastaufruf[$name];
	//Add-on Name hinzufügen
	$infos['name'] = $name;

	//Add-on Cron ausführen
	//	weitere Ausgaben über $infos[] erwünscht
	//	andere Ausgaben möglichst nicht!!
	require_once(__DIR__.'/'.$name.'/include_cron.php');

	//Endzeit hinzufügen
	$infos['end'] = time();

	//Infos der aktuellen Cron Ausführung in Cron Ausgabe Array schreiben (mehrdimensionales Array)
	$output[] = $infos;
	
	//Anzahl der durchgeführten Add-on Crons erhöhen
	$i++;
}

//Anzahl der durchgeführten Add-on Crons ausgeben
$output['doneaddons'] = $i;
//Endzeit ausgeben
$output['allgend'] = time();

//Array $output als JSON ausgeben, einfache maschinelle Verarbeitung der Ausgaben möglich
echo json_encode( $output );

//beenden
die;

?>
