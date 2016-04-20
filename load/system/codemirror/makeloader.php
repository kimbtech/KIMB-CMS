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

//Diese Datei dient dazu die CodeMirror JS Dateien passend
//für das CMS in einer zusammenzufassen.
//
//Normalerweise muss diese Datei nicht verwendet werden,
//der CodeMirrorLoader wird im CMS Installer und in Updates
//fertig erstellt ausgeliefert.
//
//Diese Datei kann nur im Terminal ausgeführt werden!
//	=> php ./makeloader.php

//nur per Termial erlauben
if (php_sapi_name() == "cli") {
	//alle JS Dateien von CodeMirror
	$files = array(
		'/lib/codemirror.js',
		'/mode/markdown/addon_overlay.js',
		'/mode/xml/xml.js',
		'/mode/javascript/javascript.js', 
		'/mode/css/css.js',
		'/mode/htmlmixed/htmlmixed.js',
		'/mode/markdown/markdown.js',
		'/mode/markdown/gfm.js'
	);

	//CMS Loader Datei lesen
	$data = file_get_contents( __DIR__.'/codemirrorloader.premin.js' );

	//alle CodeMirror JS Dateien nacheinander einlesen
	foreach ($files as $value) {
		$co = file_get_contents( __DIR__.$value );
		$data .= $co;
	}

	//alles zusammen in CodeMirrorLoader JS einfügen
	//	Meldung
	if( file_put_contents( __DIR__.'/codemirrorloader.min.js', $data ) ){
		echo "CodeMirrorLoader JS erfolgreich erstellt!";
	}
	else{
		echo "CodeMirrorLoader JS konnte nicht erstellt werden!";
	}
}
else{
	echo "Dieses Skript muss über das Terminal ausgefuehrt werden.";
}

?>