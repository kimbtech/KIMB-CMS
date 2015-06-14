<?php
	
/*************************************************/
//KIMB CMS New Theme
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
	
//Diese Datei gleicht der "output_menue_norm.php", Kommentare finden Sie dort!

defined('KIMB_CMS') or die('No clean Request');

if( !isset( $this->menuenumid ) ){
	$this->menuenumid = 0;
}
$this->menuenumid ++;

if( !isset( $this->niveau ) ){
	$this->menue .= '<li>'."\r\n";
}
elseif( $this->niveau == $niveau ){
	$this->menue .= '</li><li>'."\r\n";
}
elseif( $this->niveau < $niveau ){
	$i = 1;
	while( $this->niveau != $niveau - $i  ){
		$i++;
	}
	$this->menue .= str_repeat( '<ul>' , $i ).'<li>'."\r\n";
	$this->ulauf = $this->ulauf + $i;
}
elseif( $this->niveau > $niveau ){
	$i = 1;
	while( $this->niveau != $niveau + $i  ){
		$i++;
	}
	$this->menue .= '</li>'.str_repeat( '</ul>' , $i ).'<li>'."\r\n";
	$this->ulauf = $this->ulauf - $i;
}

if( $clicked == 'yes' ){
	$this->menue .=  '<a id="liclicked" href="'.$link.'" onclick=" return menueclick( '.$this->menuenumid.' ); ">'.$name.'</a>'."\r\n";
}
else{
	$this->menue .=  '<a href="'.$link.'" onclick=" return menueclick( '.$this->menuenumid.' ); ">'.$name.'</a>'."\r\n";
}

$this->niveau = $niveau;

?>
