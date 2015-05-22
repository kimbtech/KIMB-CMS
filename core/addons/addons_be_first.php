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

//Add-ons BE first (oben) Einbindung

//Add-on Datei laden, wenn nicht schon getan
if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

//Alle Add-ons auslesen, die BE first wollen
$all = $addoninclude->read_kimb_all_teilpl( 'be_first' );

//Add-on Wunschdaten lesen
//	Stellen, Rechte des Users und Reihenfolge vom Add-on gewünscht
$addonwish = new KIMBdbf('addon/wish/be_all.kimb');

//alles prüfen Reihenfolge && Rechte && Sites

//leeres Array für Add-ons die später geladen werden sollen
$includes = array();
//url für Stelle (Seite) 
$url = get_req_url();

//Alle BE first Add-ons nacheinander Wünsche prüfen 
foreach( $all as $add ){

	//ID des Add-on Wunsches lesen
	$id = $addonwish->search_kimb_xxxid( $add , 'addon' );

	//Add-on gefunden?
	if( $id != false ){

		//1. Rechte
		//Rechte lesen
		$re = $addonwish->read_kimb_id( $id, 'recht' );
		//erstmal nicht laden
		$recht = false;

		//Rechte String lesen
		//	$rechte => more,less,one,six
		//aufteiolen und nacheinader prüfen
		foreach( explode( ',' , $re ) as $res ){
			//more gewünscht und hat User more?
			if( $res == 'more' && $_SESSION['permission'] == 'more' ){
				$recht = true;
			}
			//less gewünscht und hat User less?
			elseif( $res == 'less' && $_SESSION['permission'] == 'less' ){
				$recht = true;
			}
			//andere Rechte gewünscht und diese vorhanden ?
			elseif( check_backend_login( $res , 'none', false ) ){
				$recht = true;
			}
			//keine Rechtewünsche, also einbinden
			elseif( $res == 'no' ){
				$recht = true;
			}

			//eingebunden, dann weiter zu 2.
			if( $recht ){
				break;
			}
		}

		//2. Sites (Stellen)
		//Stellen String lesen
		//	XXX => XXX.php
		$si = $addonwish->read_kimb_id( $id, 'site' );
		//alle, dann einbinden
		if( $si == 'all' ){
			$site = true;
		}
		//aktuelle Seite, dann einbinden
		elseif( substr( $url , '-'.strlen( 'kimb-cms-backend/'.$si.'.php' ) ) == 'kimb-cms-backend/'.$si.'.php' ){
			$site = true;
		}
		//passt dann wohl nicht
		else{
			$site = false;
		}

		//Seite und Recht okay?
		if( $site && $recht ){
			//3. Reihenfolge
			
			//Reihenfolge String lesen
			//	vorn oder hinten
			$wi = $addonwish->read_kimb_id( $id, 'stelle' );
			//vorne, dann vorne an das Include Array anfügen
			if( $wi == 'vorn' ){
				array_unshift( $includes , $add );
			}
			//hinten, also hinten an das Include Array anfügen
			elseif( $wi == 'hinten' ){
				$includes[] = $add;
			}
		}
	}

}

//alles aus den Include Array ausführen
foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_be_first.php');

}

//Include Array sichern für BE unten (second)
//	first und second gleiche Wünsche
$besecondincludesaddons = $includes;

?>
