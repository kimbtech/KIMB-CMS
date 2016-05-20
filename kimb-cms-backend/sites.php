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

//Diese Datei stellt den Teil "Seiten" bereit

//Die entsprechende Backendklasse laden
$besites = new BEsites( $allgsysconf, $sitecontent );

//Herausfinden, was der User machen will
//	Überprüfung der Rechte und Aufruf der passenden Methode
if( $_GET['todo'] == 'new' ){
	check_backend_login('two');

	//neue Seite erstellen
	$besites->make_site_new();
}
elseif( $_GET['todo'] == 'list' ){
	check_backend_login('three');

	//Seiten auflisten
	$besites->make_site_list();

}
elseif( $_GET['todo'] == 'edit' && is_numeric( $_GET['id'] ) ){
	check_backend_login('three');

	//Seiten editieren
	$besites->make_site_edit();

}
elseif( $_GET['todo'] == 'del' && is_numeric( $_GET['id'] ) ){
	check_backend_login('three');

	//Seiten löschen

	//Methode ausführen
	if( $besites->make_site_del( $_GET['id'] ) ){
		//wenn erfolgreich auf Liste weiterleiten
		open_url('/kimb-cms-backend/sites.php?todo=list&ab='.intval( $_GET['ab']));
	}
	else{
		//sonst Fehler
		$sitecontent->echo_error('Fehler beim Löschen!');
		//ausgeben
		$sitecontent->output_complete_site();
	}
	//alles beenden
	die;
}
elseif( $_GET['todo'] == 'deakch' && is_numeric( $_GET['id'] ) ){
	check_backend_login('three');

	//Status einer Seite ändern

	//Methode ausführen
	if( $besites->make_site_deakch( $_GET['id'] ) ){
		//wenn erfolgreich auf Liste weiterleiten
		open_url('/kimb-cms-backend/sites.php?todo=list&ab='.intval( $_GET['ab']) );
	}
	else{
		//sonst Fehler
		$sitecontent->echo_error('Fehler beim Ändern des Status!');
		//ausgeben
		$sitecontent->output_complete_site();
	}
	//alles beenden
	die;
}
else{
	check_backend_login('one');

	//User will nichts bestimmtes
	//	alles was möglich ist anzeigen

	$sitecontent->add_site_content('<h2>Seiten</h2>');
	
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new"><span id="startbox" class="kompl"><b>Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Eine neue Seite erstellen.</i></span></a>');
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list"><span id="startbox" class="kompl" ><b>Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle Seiten zum Bearbeiten, De-, Aktivieren und Löschen auflisten.</i></span></a>');

	$sitecontent->add_site_content('<hr /><u>Schnellzugriffe:</u><br /><br />');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" method="get"><input type="text" name="id" placeholder="ID"><input type="hidden" value="edit" name="todo"><input type="submit" value="Los"> <span title="Geben Sie die SeitenID ein und bearbeiten Sie sofort die Inhalte!">(Seite bearbeiten)</span></form>');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" method="get"><input type="text" name="id" placeholder="ID"><input type="hidden" value="del" name="todo"><input type="submit" value="Los"> <span title="Geben Sie die SeitenID ein und löschen Sie sofort die Seite!">(Seite löschen)</span></form>');
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
