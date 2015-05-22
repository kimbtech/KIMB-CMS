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

//Add-ons FE first (oben) Einbindung

//Add-on Datei laden, wenn nicht schon getan
if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

//Alle Add-ons auslesen, die FE first wollen
$all = $addoninclude->read_kimb_all_teilpl( 'fe_first' );

//Add-on Wunschdaten lesen
//	IDs, Error und Reihenfolge vom Add-on gewünscht
$addonwish = new KIMBdbf('addon/wish/fe_all.kimb');

//alles prüfen Reihenfolge && Fehler && IDs

//leeres Array für Add-ons die später geladen werden sollen
$includes = array();

//Alle FE first Add-ons nacheinander Wünsche prüfen 
foreach( $all as $add ){

	//Add-on Wunsch ID lesen
	$id = $addonwish->search_kimb_xxxid( $add , 'addon' );

	//Wunsch ID vorhanden?
	if( $id != false ){

		//1. IDs
		//IDs String lesen
		//	$id => r/s/a + ( ID )
		$wid = $addonwish->read_kimb_id( $id, 'ids' );
		
		//erstmal nicht gewünscht
		$wids = false;
		//erste Stelle extrahieren ( a = all, s = siteID, r = requestID )
		$ebst = substr( $wid, 0, 1 );
		//ID extrahieren
		$wid = substr( $wid, 1);

		//alle IDs, dann einbinden
		if( $ebst == 'a' ){
			$wids = true;
		}
		//request ID gegeben und aktuelle Request ID = $wid, dann einbinden
		elseif( $ebst == 'r' && $_GET['id'] == $wid ){
			$wids = true;
		}
		//site ID gegeben und aktuelle Site ID = $wid, dann einbinden
		elseif( $ebst == 's' && $allgsiteid == $wid ){
			$wids = true;
		}

		//2. Error
		//Fehler Sting lesen
		//	no/ all/ (nur) 404/ 403
		$err = $addonwish->read_kimb_id( $id, 'error' );
		
		//erstmal nicht gewünscht
		$error = false;

		//bei allem, dann einbinden
		if( $err == 'all' ){
			$error = true;
		}
		//keine Fehler gewünscht und auch keine gegeben, dann einbinden
		elseif( $err == 'no' && $allgerr != '404' && $allgerr != '403' ){
			$error = true;
		}
		//Fehler 404 gewünscht, dann einbinden
		elseif( $err == '404' && $allgerr == '404' ){
			$error = true;
		}
		//Fehler 403 gewünscht, dann einbinden
		elseif( $err == '403' && $allgerr == '403' ){
			$error = true;
		}

		//Fehler und IDs passen zu Wunsch?
		if( $error && $wids ){
			//3. Reihenfolge
			//Sting lesen
			//	vorn, hinten
			$wi = $addonwish->read_kimb_id( $id, 'stelle' );
			//vorne an Include Array anhängen
			if( $wi == 'vorn' ){
				array_unshift( $includes , $add );
			}
			//hinten an Include Array anhängen
			elseif( $wi == 'hinten' ){
				$includes[] = $add;
			}
		}
	}

}

//alles im Include Array einbinden
foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_fe_first.php');

}

//Include Array für FE unten (second) sichern
$fesecondincludesaddons = $includes;

?>
