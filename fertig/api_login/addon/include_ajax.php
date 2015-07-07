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

//Daten des Addons laden
$file = new KIMBdbf( 'addon/api_login__dest.kimb' );
//Daten von Felogin laden
$feconf = new KIMBdbf( 'addon/felogin__conf.kimb'  );
//Loginokay bestimmen
$loginokay = $feconf->read_kimb_one( 'loginokay' );

//Ist der User eingeloggt?
if( $loginokay == $_SESSION['felogin']['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

	//Hat er eine externe Seite gewählt?
	//	Ist es auch eine nummerische ID?
	if( !empty( $_GET['id'] ) && is_numeric( $_GET['id'] ) ){
		//URL zur externen Seite lesen
		$read = $file->read_kimb_one( 'dest'.$_GET['id'] );
		//sollte nicht leer sein und auch nicht none
		if( !empty( $read ) && $read != 'none' ){
			
			//ID machen, um User der Session zuzuordnen
			$id = makepassw( 100 , '', 'numaz' );

			//alle wichtigen Daten aus der Session lesen
			$postarr['loginokay'] = $_SESSION['felogin']['loginokay'];
			$postarr['ip'] = $_SERVER['REMOTE_ADDR'];
			$postarr['ua'] = $_SERVER['HTTP_USER_AGENT'];
			$postarr['gr'] = $_SESSION['felogin']['gruppe'];
			$postarr['us'] = $_SESSION['felogin']['user'];
			$postarr['na'] = $_SESSION['felogin']['name'];
			$postarr['id'] = $id;
			
			//und zusammen mit der ID zu JSON machen
			$jsondata = json_encode( $postarr );
			$openurl = $read.'?id='.$id;

			//den Code zur authentifizierung des CMS auf dem externen Server lesen
			$auth = $file->read_kimb_one( 'auth' );
			
			//Unirest für die Verbindung laden
			require_once( __DIR__.'/unirest/unirest_loader.php' );

			//JSON Header
			$headers = array("Accept" => "application/json");
			//POST Daten
			//	Auth-Code und JSON
			$body = array( 'auth' => $auth, 'jsondata' => $jsondata );
			//	Verbindung aufbauen
			$response = Unirest\Request::post( $read , $headers, $body);

			//Ist die Antwort okay?
			if( $response->body == 'taken' ){
				//User zum externen Server leiten (mit ID für seine Session)
				open_url( $openurl, 'outsystem' );
				die;
			}
			else{
				//Fehler wenn Server falsch antwortet
				echo( 'Error No/Wrong Destination Answer' );
			}
		}
		else{
			//Fehler wenn URL nicht gefunden
			echo( 'Error Destination Not Found' );
		}
	}
	else{
		//Fehler wenn keine ID gegeben
		echo( 'Error ID Not Set' );
	}
}
else{
	//Fehler wenn User nicht eingeloggt
	echo( 'Not Logged In!' );
}
	
die;

?>