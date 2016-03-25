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

// Diese Datei wird für jedes Menue ausgeführt
// Folgende Variablen sind definiert:
//    $name, $niveau, $clicked, $link
// Diese Datei ist Teil eines Objekts
// Das fertige Menue wird, je nach Theme über $this->menue ausgegeben

defined('KIMB_CMS') or die('No clean Request');

//jedem Menüpunkt eine ID geben, damit das Menü per Touch gut zu bedienen ist 
if( !isset( $this->menuenumid ) ){
	$this->menuenumid = 0;
}
$this->menuenumid++;

//eine verschachtelte ul, li Tabelle bauen

//schon Durchgang vorher?
if( !isset( $this->niveau ) ){
	//die erste li öffnen
	$this->menue .= '<li>'."\r\n";

}
//Niveau gleich?
elseif( $this->niveau == $niveau ){
	//li beenden und neues öffnen
	$this->menue .= '</a></li><li>'."\r\n";
}
//altes Niveau kleiner?
elseif( $this->niveau < $niveau ){
	//altes Niveau kleiner, also tiefer rein
	
	//die Tiefe feststellen und entsprechend viele ul öffnen 
	$i = 1;
	while( $this->niveau != $niveau - $i  ){
		$i++;
	}
	//Zeichen im Menü als Hinweis auf Untermenü
	$this->menue .= '&raquo;</a>'.str_repeat( '<ul>' , $i ).'<li>'."\r\n";

	//Menütiefe mitzählen
	$this->ulauf = $this->ulauf + $i;

}
//altes Niveau größer?
elseif( $this->niveau > $niveau ){
	//altes Niveau größer, also wieder raus
	
	//wie oft raus feststellen und entsprechend viele ul schließen
	$i = 1;
	while( $this->niveau != $niveau + $i  ){
		$i++;
	}
	$this->menue .= '</a></li>'.str_repeat( '</ul>' , $i ).'<li>'."\r\n";

	//Menütiefe mitzählen
	$this->ulauf = $this->ulauf - $i;
}

//Den Menüpunkt der Ausgabe anfügen
//	wenn dieser 'clicked' ist (also der Menüpunkt der aktuellen Seite) die id #liclicked anfügen
if( $clicked == 'yes' ){
	$this->menue .=  '<a id="liclicked" href="'.$link.'" onclick="return menueclick('.$this->menuenumid.');" >'.$name."\r\n";
	//für Menüausschnitt links aufnehmen
	$this->over_menue[$this->menuenumid] = array( $niveau, '<a id="liclicked" href="'.$link.'">'.$name.'</a>' );
	$this->over_menue_clicked_id = $this->menuenumid;
	$this->over_menue_clicked_niv = $niveau;
}
else{
	//für Menüausschnitt links aufnehmen
	$this->menue .=  '<a href="'.$link.'" onclick="return menueclick('.$this->menuenumid.');" >'.$name."\r\n";
	$this->over_menue[$this->menuenumid] = array( $niveau, '<a href="'.$link.'">'.$name.'</a>' );
}

$this->niveau = $niveau;

?>
