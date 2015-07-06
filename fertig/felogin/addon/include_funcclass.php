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

//Wichtige Werte lesen
//	dbf laden
$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
//	Loginokay
$felogin['loginokay'] = $felogin['conf']->read_kimb_one( 'loginokay' );
//	alle Gruppen
$felogin['grlist'] = $felogin['conf']->read_kimb_one( 'grlist' );
//	alle Gruppen Array
$felogin['grs'] = explode( ',' , $felogin['grlist'] );
//	RequestID zur Seite für Loginsachen
$felogin['requid'] = $felogin['conf']->read_kimb_one( 'requid' );
//	selbstregistrierung on/off
$felogin['selfreg'] = $felogin['conf']->read_kimb_one( 'selfreg' );

//Array mit allen von felogin geschützten Seiten erstellen
//Arrays mit Seite der einzelnen Gruppen erstellen

//Gruppen durchgehen 
foreach( $felogin['grs'] as $felogin['gr'] ){

	//Seiten der Gruppe lesen
	$felogin['grteile'] = $felogin['conf']->read_kimb_one( $felogin['gr'] );
	//als Array bereitstellen (Seite der einzelnen Gruppen)
	$felogin['teilesite'][$felogin['gr']] = explode( ',' , $felogin['grteile'] );

	//das Array mit allen Seiten füttern
	foreach($felogin['teilesite'][$felogin['gr']] as $felogin['teilsite'] ){

		//nur hinzufügen wenn nicht schon im Array
		if( !in_array( $felogin['teilsite'] , $felogin['allsites']) ){
			$felogin['allsites'][] = $felogin['teilsite'];
		}

	}
}

//Funktion um Rechte des eines eingeloggten Users zu prüfen
//	Rückgabe: true/false (Seite erlaubt/nicht erlaubt)
//	$gruppe => Gruppe des zu prüfenden Users (---session--- -> aktueller User)
//	$siteid => Seite welche geprüft werden soll (---allgsiteid-- -> aktuelle Seite)  
function check_felogin_login( $gruppe = '---session---', $siteid = '---allgsiteid---'  ){
	global $felogin, $allgsiteid;

	//Werte für Vorgabeparamter setzen
	if( $gruppe == '---session---'){
			$gruppe = $_SESSION['felogin']['gruppe'];
	}
	if( $siteid == '---allgsiteid---' ){
		$siteid = $allgsiteid;
	}

	//Seite von Felogin geschützt?
	if( in_array( $allgsiteid , $felogin['allsites'] ) ){
		//User richtig eingeloggt?
		if( $felogin['loginokay'] == $_SESSION['felogin']['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

			//gewünscht Seite in der gewünscht Gruppe erlaubt?
			if( in_array( $siteid , $felogin['teilesite'][$gruppe] ) ){
				//Rechte da!
				return true;
			}
			else{
				//keine Rechte
				return false;
			}

		}
		else{
			//wenn nicht dann Fehler
			return false;
		}
	}
	else{
		//nicht geschützt, also darf jeder User
		return true;
	}
}

//Daten für später sichern
$addon_felogin_array = $felogin;
unset ( $felogin );
?>
