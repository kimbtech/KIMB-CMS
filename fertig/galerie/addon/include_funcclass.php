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

//Diese Funktion verkleinert eine Bilddatei ohne sie zu verzerren
//(basierend auf PHP_GD)
//	$img -> Pfad zum großen Bild
//	$klimg -> Pfad unter dem das kleine Bild gespeichert werden soll
//	$size -> maximale Ausdehnung in Pixel (Höhe oder Breite)
function saveklimg( $img, $klimg, $size ){
	
	//Bild laden
	$image = imagecreatefromstring( file_get_contents( $img ) );
	//aktuelle Größe heausfinden
	$width = imagesx($image);
	$height = imagesy($image);

	//breiter als hoch?
	if( $height < $width ){
		//neue Breite und Höhe bestimmen
		$heightnew = $height * $size / $width ;
		$widthnew = $size;
	}
	//höher als breit?
	elseif( $height > $width ){
		//neue Breite und Höhe bestimmen
		$widthnew = $width * $size / $height ;
		$heightnew = $size;
	}
	//sonst quadratisch
	else{
		//Breite gleich Höhe
		$widthnew = $size;
		$heightnew = $size;
	}
	
	//neues Bild in der richigen Größe erstellen
	$thumb = imagecreatetruecolor( $widthnew, $heightnew );
	//neues Bild transparent machen
	imagefill($thumb,0,0, imagecolorallocatealpha($thumb, 0, 0, 0, 127) );
	imagesavealpha($thumb, TRUE);
	//gegebenes Bild in neues einfügen (und das kleiner)
	imagecopyresampled( $thumb, $image, 0, 0, 0, 0, $widthnew, $heightnew, $width, $height);
	//Bild speichern
	imagepng( $thumb, $klimg );
	//aufräumen
	imagedestroy( $thumb );
}
?>
