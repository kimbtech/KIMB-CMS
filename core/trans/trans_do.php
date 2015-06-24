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

//Übersetzungen des Frontends laden

//Sprachwünsche des Benutzers beachten?
if( $allgsysconf['sitespr'] == 'auto' ){

	//Der Browser eines Benutzer gibt Sprachwünsche an
	$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	//es können mehrere Sprachen gewünscht werden (je früher genannt, desto lieber gesehen)
	foreach($langs as $lang){
			//durchgehen der Wünsche
				
			//Die Wünsche sehen teilweise so aus: en-US
			//	daher nur die ersten zwei Zeichen für als Lang-Tag nutzen
			$lang = substr($lang, 0, 2);
			//Sprachtags bestehen nur aus a-z, alles andere weg
			$lang = preg_replace( "/[^a-z]/" , "" , strtolower( $lang ) );
			
			//Ist die gewünscht Sprache als Übersetzung vorhanden?
			if( is_file( __DIR__.'/lang_'.$lang.'.php' ) ){
				
				//für später merken
				$allgsys_sitespr = $lang;
				//Sprache laden
				require_once( __DIR__.'/lang_'.$lang.'.php' );
				//Übersetzung gefunden
				$trans = true;
				//keine weiteren Durchgänge nötig
				break;
			}
	}

	if( !$trans ){
		//wenn keine Übersetzung gefunden, Fallback nötig
		$transfallback = true;
	}
				
}
//Feste Sprache vom Admin gewählt
elseif( strlen( $allgsysconf['sitespr'] ) == 2 ){
	
	//Sprachtags bestehen nur aus a-z, alles andere weg
	$lang = preg_replace( "/[^a-z]/" , "" , strtolower( $allgsysconf['sitespr'] ) );
	
	//Ist die gewünscht Sprache als Übersetzung vorhanden?
	if( is_file( __DIR__.'/lang_'.$lang.'.php' ) ){
		//für später merken
		$allgsys_sitespr = $lang;
		//Sprache laden
		require_once( __DIR__.'/lang_'.$lang.'.php' );
	}
	else{
		//gewünschte Übersetzung nicht gefunden, Fallback nötig
		$transfallback = true;
	}

}
//keine Sprache gewählt
else{
	//Fallback
	$transfallback = true;
}

//Fallback verwenden -> EN
if($transfallback ){
	//für später merken
	$allgsys_sitespr = 'en';
	//EN laden
	require_once( __DIR__.'/lang_en.php' );
}

//für Add-ons freihalten
$allgsys_trans['addons'] = array();
		
//Jetzt enthält $allgsys_sitespr die aktuelle Sprache
//und das Array $allgsys_trans die übersetzten Ausagaben.
?>
