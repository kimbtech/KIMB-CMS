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
//www.KIMB-technologies.eu
//www.bitbucket.org/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//Größe eines Ordners bestimmen
//	$folder => Pfad des Ordners
//	$bytes => true (Rückgabe in Bytes) / false (KB/MB/GB)
//	Rückgabe: INT (Bytes)/ String (21 KB/ 22 MB/ 23 GB)/ false
function dir_size_comp( $folder, $bytes = false ){
	//größe machen
	$size = make_filesize_rec( $folder );

	//Fehler?
	if( $size == false || !is_numeric( $size ) ){
		return false;
	}

	//Bytes ausgeben?
	if( $bytes ){
		//INT ausgeben
		return $size;
	}
	else{
		//in KB/MB/GB umrechnen

		//	Endungen
		$end = array( 'B', 'KB', 'MB', 'GB', 'TB');

		//	solange einen Schritt größer gehen, bis nicht mehr weiter möglich
		$i = 0;
		while( $size > 1024 ){
			//Zahl einen Schritt größer machen
			$size = round( $size / 1024 , 3 );
			$i++;
		}

		//Ausgabe
		return $size.' '.$end[$i];
	}

}

//Helper Funktion für "dir_size_comp();"
function make_filesize_rec( $folder ){

	//Größen INT
	$size = (int) 0;

	//Ornder übergeben? 
	if( is_dir( $folder ) ){

		//Ordner einlesen
		foreach (scandir( $folder) as $file) {
			//nur Ordner und Dateien bearbeiten
			if( $file != '..' && $file != '.' ){
				//Datei?
				if( is_file( $folder.'/'.$file ) ){
					//Größe bestimmen
					$bytes = filesize( $folder.'/'.$file );
					//wenn okay, zu Gesamtgröße addieren
					if( $bytes != false ){
						$size += $bytes;
					}
				}
				elseif( is_dir( $folder.'/'.$file ) ){
					//Größe bestimmen
					//	diese Funktion verschachtelt
					$bytes = make_filesize_rec( $folder.'/'.$file );
					//wenn okay, zu Gesamtgröße addieren
					if( $bytes != false ){
						$size += $bytes;
					}
				}
			}
		}

		//Größe ausgeben
		return $size;
	}
	//Fehler
	else{
		return false;
	}
}

?>