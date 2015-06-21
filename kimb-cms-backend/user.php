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

//Diese Datei ist Teil des Backends, sie wird direkt aufgerufen.

//Konfiguration & Klassen & Funktionen laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Diese Datei stellt den Teil "Menüs" bereit

//Auch wenn einem User die Rechte zum Bearbeiten von Usern fehlen, soll er sich selbst bearbeiten können
//	dazu muss er nur "eight" haben

//erstmal müssen die Rechte nochmal geprüft werden
$checkerneut = 'yes';
//will der User sich selbst bearbeiten
if( $_GET['user'] == $_SESSION['user'] && $_GET['todo'] == 'edit' ){
	//hat er die Rechte für "eight"?
	check_backend_login( 'eight' );
	//jetzt muss nicht nochmal geprüft werden 
	$checkerneut = 'no';
}

//Die entsprechende Backendklasse laden
$beuser = new BEuser( $allgsysconf, $sitecontent );

//BE-User erstellen, bearbeiten
$userfile = new KIMBdbf('backend/users/list.kimb');

//Herausfinden, was der User will
//	Rechte prüfen und Methode aufrufen
if( $_GET['todo'] == 'new' ){
	check_backend_login( 'nine' , 'more' );
	
	//neuen User erstellen
	$beuser->make_user_new();

}
elseif( $_GET['todo'] == 'edit' && isset( $_GET['user'] ) ){
	
	//User bearbeiten
	
	//nochmal teste?
	if( $checkerneut != 'no' ){
		check_backend_login( 'ten' , 'more' );
	}

	//Methode
	$beuser->make_user_edit();
}
elseif( $_GET['todo'] == 'list'){
	check_backend_login( 'ten' , 'more' );
	
	//User auflisten
	$beuser->make_user_list();
}
else{
	check_backend_login( 'eight' , 'more' );
	
	//User will nichts bestimmtes
	//	alles was möglich ist anzeigen

	$sitecontent->add_site_content('<h2>User</h2>');

	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new"><span id="startbox"><b>Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Einen neuen User erstellen.</i></span></a>');
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=list"><span id="startbox"><b>Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle User zum Bearbeiten und Löschen auflisten.</i></span></a>');

	$sitecontent->add_site_content('<hr /><u>Schnellzugriffe:</u><br /><br />');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php" method="get"><input type="text" name="user" placeholder="Username"><input type="hidden" value="edit" name="todo"><input type="submit" value="Los"> <span title="Geben Sie Usernamen ein und bearbeiten Sie ihn sofort!">User bearbeiten</span></form>');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php" method="get"><input type="text" name="user" placeholder="Username"><input type="hidden" value="edit" name="todo"><input type="hidden" name="del"><input type="submit" value="Los"> <span title="Geben Sie einen Usernamen ein und löschen Sie ihn sofort!">User löschen</span></form>');
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
