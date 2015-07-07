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

//KIMB-CMS Add-on API Login
//=======================
//	Diese Datei ermöglicht es das Login im KIMB-CMS
//	auch auf Seiten auf anderen Servern zu verwenden.
//
//	**
//	Ablauf der Verbindung:
//		Ein eingeloggter User des CMS will auf eine
//		externe Seite. Das CMS baut eine Verbindung 
//		zu dieser Datei (auf einem externen Server)
//		auf und authentifiziert sich. Dann gibt das
//		CMS die Daten der Session des Users an
//		diese Datei weiter.
//		Der User wird an diese Datei weitergeleitet
//		und gibt sich als Besitzer einer Session aus. 
//		Diese Datei erstellt dann seine Session
//		auf dem externen (diesem) Server.
//		Der User kann jetzt auf Seiten welche
//		mit der Datei "api_login.php" gesichert sind
//		zugreifen.
//	**


//Daten 
$auth = <<[[auth]]>>;
$sysurl = <<[[sysurl]]>>;

//Ist es das CMS? (stimmt der Auth-Code)
if( $_POST['auth'] == $auth ){

	//Datei zur Speicherung von Session Daten lesen
	//	13 (Datei enthält am Anfang "php die;", dies wird übersprungen)
	$cont = file_get_contents( __DIR__.'/inhalte.php', NULL, NULL, 13 );

	//Die Daten sind JSON Strings
	//	parsen
	$arr = json_decode( $cont, true );
	//	neue Daten anfügen
	$arr[] = json_decode( $_REQUEST['jsondata'], true );

	//Daten wieder speichern
	file_put_contents( __DIR__.'/inhalte.php', '<?php die; ?>'.json_encode ( $arr ) );

	//dem Server Rückmeldung geben
	echo 'taken';
	//beenden
	die;
}
//Ist es ein User? (hat er eine ID einer Session in der "inhalte.php"")
elseif( isset( $_GET['id'] ) ){

	//Session für User starten
	ini_set('session.use_only_cookies', 1);
	session_name ("KIMBCMS");
	session_start();

	//Datei zur Speicherung von Session Daten lesen
	//	13 (Datei enthält am Anfang "php die;", dies wird übersprungen)
	$cont = file_get_contents( __DIR__.'/inhalte.php', NULL, NULL, 13 );
	//JSON Daten parsen
	$arr = json_decode( $cont, true );

	//alle Daten durchgehen
	foreach( $arr as $user ){
		//passt die ID des Users zu der aktuellen Session?
		if( $user['id'] == $_GET['id'] ){
			//wenn ja, alle Sessionwerte setzen
			$_SESSION['felogin']['loginokay'] = $user['loginokay'];
			$_SESSION["ip"] = $user['ip'];
			$_SESSION["useragent"] = $user['ua'];
			$_SESSION['felogin']['gruppe'] = $user['gr'];
			$_SESSION['felogin']['user'] = $user['us'];
			$_SESSION['felogin']['name'] = $user['na'];
		}
		else{
			//wenn nicht, Daten für später aufheben
			$newarr[] = $ar;
		}
	}

	//Die Daten neu in die inhalte.php schreiben
	file_put_contents( __DIR__.'/inhalte.php', '<?php die; ?>'.json_encode ( $newarr ) );

}

//URL des Systems aufrufen
header( 'Location: '.$sysurl );
die;

?>