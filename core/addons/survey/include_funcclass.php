<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2016 by KIMB-technologies
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

//Fragen aus Fragedatei lesen
//  korrekt sortiert
//	$ufile => KIMBdbf Objekt der Umfragedatei
//	Return: Array von KIMBdbf read all ID
function read_and_sort_fragen( $ufile ){
	//alle lesen
	$fragen = $ufile->read_kimb_id_all();
	//nach ID sortieren
	ksort( $fragen, SORT_NUMERIC );
	//Ausgeben
	return $fragen;
}

//Zugriffsrechte für Umfrage prüfen
//  per Session
//	$uid => Umfrage ID
//	$ufile => KIMBdbf Objekt der Umfragedatei
//	Return: Boolean
function check_surveyrights( $uid, $ufile ){
	//Zugriffsart lesen
	$zug = $ufile->read_kimb_one( 'zugriff' );
	//Auswertung
	$ausw = $ufile->read_kimb_one( 'zugaus' );

	//überhaupt Zugriff auf Seite?
	//	Felogin Check
	if(
		( check_addon( 'felogin' ) == array(true, true) && check_felogin_login( '---session---', '---allgsiteid---', true ) )
		||
		( check_addon( 'felogin' ) != array(true, true) )
	){
		//schon teilgenommen?
		if(
			!isset( $_SESSION['addon_survey'][$uid]['teilgenommen'] )
			||
			( isset( $_SESSION['addon_survey'][$uid]['teilgenommen'] ) && $_SESSION['addon_survey'][$uid]['teilgenommen'] != 'ja' )

		){
		
			//Zugriff Auswertung
			if( $ausw == 'oe' ){
				$_SESSION['addon_survey']['ausw'][$uid]['zugriff'] = 'allowed';
			}
			else{
				$_SESSION['addon_survey']['ausw'][$uid]['zugriff'] = 'notallowed';
			}

			//Teilnahme Umfrage

			//öffentlich?
			if( $zug == 'oe' ){
				$_SESSION['addon_survey'][$uid]['zugriff'] = 'allowed';
				return true;
			}
			//per Link?
			elseif( $zug == 'li' ){
				if( isset( $_SESSION['addon_survey'][$uid]['zugriff'] ) ){
					if( $_SESSION['addon_survey'][$uid]['zugriff'] == 'allowed' ){
						return true;
					}
				}
			}
			//Felogin
			elseif( $zug == 'fe' ){
				//Add-on vorhanden und aktiviert
				if( check_addon( 'felogin' ) == array(true, true) ){
					//Login okay
					if( check_felogin_login( '---session---', '---none---', true ) ){
						//Ergebnisdatei vorhanden?
						if( check_for_kimb_file( 'addon/survey__'.$uid.'_erg.kimb' ) ){
							//Datei laden
							$uefile = new KIMBdbf( 'addon/survey__'.$uid.'_erg.kimb' );

							//User schon drin?
							//	wenn nicht, dann okay
							$teilnahme = ( $uefile->read_kimb_search_teilpl( 'teilnahmeuser', $_SESSION['felogin']['user'] ) ? false : true );
						}
						else{
							//keine Ergebnisse => keien Teilnahmen
							$teilnahme = true;	
						}

						//Teilnahme okay?
						if( $teilnahme ){
							$_SESSION['addon_survey'][$uid]['zugriff'] = 'allowed';
							return true;
						}
					}
				}
			}
		}
	}
	else{
		//nicht erlaubt (Auswetung sehen)
		//	kein Zugriff auf Seite
		$_SESSION['addon_survey']['ausw'][$uid]['zugriff'] = 'notallowed';
	}

	//sonst Fehler
	$_SESSION['addon_survey'][$uid]['zugriff'] = 'notallowed';
	return false;
}

//User per Session auf teilgenommen setzen
//	$uid => UmfrageID
function survey_teilgenommen( $uid ){
	//schon teilgenommen
	$_SESSION['addon_survey'][$uid]['teilgenommen'] = 'ja';
	//nicht mehr erlaubt
	$_SESSION['addon_survey'][$uid]['zugriff'] = 'notallowed';
}

?>