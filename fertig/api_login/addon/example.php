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
//	auch auf anderen Seiten auf dem gleichen 
//	Server zu verwenden.
//
//	**
//		Es wird die gleiche PHP-Session verwendet!
//	**

//Session beginnen
ini_set('session.use_only_cookies', 1);
session_name ("KIMBCMS");
session_start();

//Funktion um Login zu testen und als Rückgabe Infobox
function cmsfelogin(){
	//Daten des CMS - Konfiguration
	//	URL zu 403
	$url403 = <<[[url403]]>>;
	//	Link zum CMS (Link "Home"")
	$homelink = <<[[homelink]]>>;
	//	Loginokay von felogin
	$loginokay = <<[[loginokay]]>>;
	//	erlaubte Gruppen als Array
	$allowgr = array( <<[[allowgr]]>> );
	//	erlaubte User als Array
	$allowusr = array( <<[[allowusr]]>> );
	
	//Logout ?
	if( $_POST['logout'] == 'yes' ){
		//auf aktuellem Server ausloggen
		
		//Session leeren
		session_unset();
		//Session zerstören
		session_destroy();
		//Session neu aufesetzen
		//	jetzt ist alles, was mit dem User zu tun hatte weg
		session_start();
		
		//Damit das Logout auch auf dem Server des CMS durchgeführt wird hier ein HTML Formular
		$output = '<!DOCTYPE html>';
		$output .= '<html><head>';
		$output .= '<title>Logout</title>';
		$output .= '</head><body>';
		$output .= '<h1>Sie wurden ausgeloggt</h1>';
		//Formular zum "$homelink"
		$output .= '<form method="post" name="Logoutform" action="'.$homelink.'"><input type="hidden" value="yes" name="logout"><input type="submit" value="Logout"></form>';
		//Das Formular kann manuell per Klick abgesendet werden, wird aber auch per JavaScript abgesendet
		$output .= '<script>document.Logoutform.submit();</script>';
		$output .= '</body></html>';

		//Ausgabe und fertig
		echo $output;
		die;
		
	}

	//Login prüfen
	//	allgemein
	if( $loginokay == $_SESSION['felogin']['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

		//Gruppe des Users erlaubt
		if( in_array( $_SESSION['felogin']['gruppe'], $allowgr ) ){
			//JA!
			$loggedin = true;
		}
		//User erlaubt
		elseif( in_array( $_SESSION['felogin']['user'], $allowusr ) ){
			//JA!
			$loggedin = true;
		}
		//beides nicht erlaubt
		else{
			//NEIN!
			$loggedin = false;
		}

	}
	//auch allgemein nicht
	else{
		//NEIN!
		$loggedin = false;
	}
	
	//wenn nicht eingeloggt, dann Error 403
	if( $loggedin == false ){
		//HEADER
		header( 'HTTP/1.0 403 Forbidden' );
		//Ist eine 403 URL gegeben?
		if( !empty( $url403 ) ){
			//403 URL aufrufen
			header( 'Location: '.$url403 );
		}
		else{
			//sonst einen kleine Fehlerseite ausgeben
			$output = '<!DOCTYPE html>';
			$output .= '<html><head>';
			$output .= '<title>Error 403</title>';
			$output .= '</head><body>';
			$output .= '<h1>Error 403 - Kein Zugriff!</h1>';
			$output .= '<h3>Bitte loggen Sie sich ein!</h3>';
			$output .= '<a href="'.$homelink.'" target="_blank" >Login</a>';
			$output .= '</body></html>';
		}
		//Ausgabe und beenden
		echo $output;
		die;
	}

	//dann wohl eingeloggt!
	
	//HTML-Code für kleine Infobox
	$output = '<!-- KIMB CMS API Login - Anfang -->';
	//Position
	$output .= '<div style="position:absolute; right:2px; top:2px; border-radius:15px; border:solid 2px #888; min-width:100px; min-height:100px; padding:20px; background-color:#ccc;">';
	//Begrüßung
	$output .= 'Hallo '.$_SESSION['felogin']['name'].',<br />';
	$output .= 'Sie sind eingeloggt!<br />';
	$output .= '<a href="'.$homelink.'" target="_blank" >Home</a>';
	//Logout
	$output .= '<form method="post" action=""><input type="hidden" value="yes" name="logout"><input type="submit" value="Logout"></form>';
	$output .= '</div>';
	$output .= '<!-- KIMB CMS API Login - Ende -->';

	//Rückgabe
	return $output;

}

//Ausführen und ausgeben

//echo cmsfelogin();
?>
