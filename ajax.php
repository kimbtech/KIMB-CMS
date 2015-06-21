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

define("KIMB_CMS", "Clean Request");

//Klassen Autoload
require_once( __DIR__.'/core/oop/all_oop.php' );

//Konfiguration
$conffile = new KIMBdbf('config.kimb');
$allgsysconf = $conffile->read_kimb_id( '001' );

//Funktionen laden
require_once(__DIR__.'/core/conf/funktionen.php');

//session, Fehleranzeige, Robots-Header, Content, Codierung
SYS_INIT( $allgsysconf['robots'] );

//System initialisiert!

if( empty( $_GET['file'] ) && empty( $_GET['addon'] ) ){

	//Weder Add-on noch systemeigen -> Fehler

	echo('Fehlerhafte Ajax-Anfrage!');
	die;

}

//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_ajax.php');

//Systemeigen
//Systemeigen
//Systemeigen

//BE Login - Salt 
if( $_GET['file'] == 'index.php' ){

	//var setzen
	if( empty( $_SESSION["loginfehler"] ) ){
		$_SESSION["loginfehler"] = 0; 
	}

	//Usernamen holen und Syntax prüfen
	$user = $_GET['user'];
	$user = preg_replace( "/[^a-z]/" , "" , strtolower( $user ) );

	//Username darf nicht leer sein
	//Nur wenn die Loginseite vorher aufgerufen wurde hat man eine Grund die Salts zu lesen
	if( !empty( $user ) && !empty($_SESSION["loginsalt"]) ){

		//BE Userdatei öffnen
		$userfile = new KIMBdbf('/backend/users/list.kimb');
		
		//Nach User suchen
		$userda = $userfile->search_kimb_xxxid( $user , 'user' );

		//Wenn User da und weniger als 6 Loginfehler
		if( $userda != false && $_SESSION["loginfehler"] <= 6 ){

			//Salt auslesen
			$salt = $userfile->read_kimb_id( $userda , 'salt' );
			
			//Wenn Salt nicht leer ausgeben
			if( !empty( $salt ) ){
				echo $salt;
			}
			else{
				//sonst irgendwas ausgeben (User soll nicht merken, dass Username falsch)
				$randsalt = true;
			}
		}
		else{
			//sonst irgendwas ausgeben (User soll nicht merken, dass Username falsch)
			$randsalt = true;
		}
	}
	else{
		//leer => Fehler (not okay)
		echo 'nok';
	}
	
	//soll irgendwas ausgegeben werden?
	if( $randsalt){
		//sonst irgendwas ausgeben (User soll nicht merken, dass Username falsch)
		//wenn zweimal der gleiche User abgefragt wird, dann muss immer das gleiche Salt angegeben werden
				
		//wurde dieser User schonmal ausgegeben?
		if( empty($_SESSION['allsalts'][$_GET['user']])){
			//nein -> neues Salt erstellen
			$randsalt = makepassw( 10, '', 'numaz' );
			//in der Session ablegen
			$_SESSION['allsalts'][$_GET['user']] = $randsalt;
			//und ausgeben
			echo $randsalt;
		}
		else{
			//User wurde schon mal abgefragt
			//Salt aus Session ausgeben
			echo $_SESSION['allsalts'][$_GET['user']];
		}
	}

	//beenden
	die;

}
//BE Menue
elseif( $_GET['file'] == 'menue.php' ){

	//Rechte prüfen
	check_backend_login('seven' , 'more');
	
	//Pfad überprüfen
	//Übergabeparameter okay?
	if( $_GET['urlfile'] == 'first' || is_numeric( $_GET['urlfile'] ) ){
		
		//in welcher URL-Datei muss gesucht werden? 
		if( $_GET['urlfile'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['urlfile'].'.kimb' );
		}
	
		//Suchbegriff reinigen
		$_GET['search'] = preg_replace("/[^0-9A-Za-z_.-]/","", $_GET['search']);
	
		//suchen und wenn nicht gefunden okay, sonst nicht okay
		if( !$file->search_kimb_xxxid( $_GET['search'] , 'path') ){
			echo 'ok';
		}
		else{
			echo 'nok';
		}
	}
	//Menüpunkt verschieben
	//Übergabeparameter richtig?
	elseif( ($_GET['fileid'] == 'first' || is_numeric( $_GET['fileid'] ) ) && ( $_GET['updo'] == 'up' || $_GET['updo'] == 'down' ) && is_numeric( $_GET['requid'] ) ){
		
		//neues Objekt der Klasse Backend Menue
		$bemenue = new BEmenue( $allgsysconf, $sitecontent );
		
		//Methode Parameter vorbereiten
		$GET = $_GET;
		
		//Ausführen und Rückmeldung
		if( $bemenue->make_menue_versch( $GET ) ){
			echo 'ok';
		}
		else{
			echo 'nok';
		}
	}
	
	//beenden
	die;
}
//BE User - Username vorhanden?
elseif( $_GET['file'] == 'user.php' && isset( $_GET['user'] ) ){

	//Rechte prüfen
	check_backend_login( 'nine' , 'more' );

	//Userdatei laden
	$userfile = new KIMBdbf('backend/users/list.kimb');

	//Username Übergabe säubern
	$_GET['user'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_GET['user'] ) );

	//name Username suchen
	$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );

	//passenden Rückgabe
	if( $id == false ){
		echo 'ok';
	}
	else {
		echo 'nok';
	}
	
	//beenden
	die;
}
//Filemanager secure Bilderzugriff
elseif( $_GET['file'] == 'other_filemanager.php' && isset( $_GET['key'] ) ){

	//Key Datei öffnen
	$keyfile = new KIMBdbf( 'backend/filemanager.kimb' );

	//nach key suchen
	$id = $keyfile->search_kimb_xxxid( $_GET['key'] , 'key' );
	
	//key vorhanden?
	if( $id != false ){
		//Datei zum Key gefunden
		
		//Dateiname lesen
		$dateiname = $keyfile->read_kimb_id( $id , 'file' );
		//Dateipfad lesen
		$datei = __DIR__.'/core/secured/'.$keyfile->read_kimb_id( $id , 'path' );

		//Datei mit richtigem Header und Dateinamen ausgeben
		header("Content-type: ". mime_content_type($datei) );
		header('Content-Disposition: filename= '.$dateiname);
		echo ( file_get_contents($datei) );
	}
	else{
		//kein Datei zum Key da -> Fehler!
		echo('Fehlerhafter Key!!');
	}
	//beenden
	die;
}

//keine Add-ons und nichts systemeigenes
echo('Fehlerhafte Ajax-Anfrage!');
die;
?>
