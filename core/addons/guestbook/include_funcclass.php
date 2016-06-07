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

//Diese Funktion reinigt die Eingaben für das Gästebuch 
//	$cont -> übergebene Mitteilung
//		URLs werden zu Links, nur einige HTML-Tags erlaubt
//	$name -> übergebener Name
//	Rückgabe: array( $cont, $name )  [beide gesäubert]
function make_guestbook_html( $cont, $name ){

	//Name
	$name = strip_tags ( $name );

	//Inhalt
	$cont = nl2br( htmlentities ( $cont , ENT_COMPAT | ENT_HTML401,'UTF-8' ) );
	
	//einige htmlentities wieder zurück
	//	alle br erhalten ein Lesezeichen davor, nur so werden URLs am Zeilenende sicher erkannt
	//	https:// wird zu http://
	$codiert = array( '&lt;b&gt;' , '&lt;/b&gt;' , '&lt;u&gt;' , '&lt;/u&gt;' , '&lt;i&gt;' , '&lt;/i&gt;' , '&lt;center&gt;' , '&lt;/center&gt;', '<br />', 'https://' );
	$unkodiert = array( '<b>' , '</b>' , '<u>' , '</u>' , '<i>' , '</i>' , '<center>' , '</center>', ' <br />', 'http://' );
	
	//Ersetzungen durchführen
	$cont = str_replace( $codiert , $unkodiert , $cont );
	
	//eine URL am Ende muss von einem Leerzeichen beendet werden, dieses kommt hier dran
	$cont = $cont.' ';
	
	//URL zu Link
	
	//erstes http:// finden
	$pos = strpos( $cont, 'http://');
	//solange noch http://'s gefunden:
	while(  $pos !== false ){
		
		//Leerzeichen am Ende der URL suchen
		$leer = strpos( $cont, ' ', $pos );
		
		//Länge der URL herausfinden
		$len = $leer - $pos;
		//URL herausfinden
		$url = substr( $cont , $pos , $len );
		
		//vor und nach der URL in einem Array aufteilen (maximal zwei Teile)
		$array = explode( $url, $cont, 2);
		
		//Inhalt neu zusammensetzen
		$cont = $array[0].'<a href="'.$url.'" target="_blank">'.$url.'</a>'.$array[1];
			
		//nächste URL
		//	nach der URL neu suchen
		$off = strlen( $array[0].'<a href="'.$url.'" target="_blank">'.$url.'</a>' ) ;
		//	suchen
		$pos = strpos( $cont, 'http://', $off);
	}

	//als Array zurückgeben
	return array( $cont, $name);
}

//Allg CSS Code
function get_guestbook_css( $be = false ){
	//immer
	$css = 'div.guestname{ position:relative; border-bottom:solid 1px #000000; font-weight:bold; }';
	$css .= 'span.guestdate{ font-weight:normal; position:absolute; right:0px; }';
	$css .= 'div.guest, div.answer{ border:solid 1px #000000; border-radius:15px; background-color:#dddddd; padding:10px; margin:5px;}';
	$css .= 'div.answer{ margin-left:80px; }';
	$css .= 'div.guestcomment{ font-style:italic; font-size:0.9em; padding:4px; border-radius:2px; background-color:#FFD182; margin:4px; margin-left: 40px;}';
	$css .= 'div.guestcomment span{ font-weight:bold; font-align:center; display: block; }';
	
	//nur für BE Übersicht
	if( $be ){
		$css .= 'div.guestinfo{ position:relative; border-top:solid 1px #000000; font-weight:bold; text-align:center; }';
		$css .= 'span.guestlinks{ font-weight:normal; position:absolute; left:0px;}';
		$css .= 'span.guestrechts{ font-weight:normal; position:absolute; right:0px;}';
	}
	
	return $css;
}

?>
