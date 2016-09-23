<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2016 by KIMB-technologies.eu
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
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//Systemkonfigurationsdatei
//	TeilerPlus Werte unter dem Teiler "uid"
//	sind die SeitenID der Seiten mit Umfragen.
$ucfile = new KIMBdbf( 'addon/survey__conf.kimb' );

//Umfrage auf dieser Seite ??
if( $ucfile->read_kimb_search_teilpl( 'uid', $allgsiteid ) ){

	//UID extrahieren
	$uid = $allgsiteid;

	//CSS für System
	$sitecontent->add_html_header('<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/addondata/survey/main.min.css" media="all">');
	//jQuery UI wird benötigt
	$sitecontent->add_html_header('<!-- jQuery --><!-- PrismJS -->');
	//Cart JS
	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/survey/chart.min.js"></script>');
	//Vars
	$sitecontent->add_html_header('<script language="javascript">var addsur = { su :"'.$allgsysconf['siteurl'].'", uid : "'.$uid.'",
	zugaus : "notallowed", zugriff: "notallowed", username : "'.( isset( $_SESSION['felogin']['name'] ) ? $_SESSION['felogin']['name'] : '' ).'" };</script>');
	//	Hier werden die Zugriffsrechte erstmal auf nicht erlaubt gesetzt.
	//	Beim Seitenaufruf werden diese immer zuerst per AJAX geprüft!
	//	Setzen hier nicht dynamisch möglich, da Probleme mit FullHTMLCache.

	//JS für System
	//	Struktur und Fragen
	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/survey/main.min.js"></script>');
	//	Auswertung
	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/survey/stat.min.js"></script>');
		
	//HTML Gerüst für System
	$sitecontent->add_site_content('<div id="addon_survey_main" class="addon_survey_main">Bitte aktivieren Sie JavaScript für das Umfragesystem!</div>');
}
unset( $ucfile, $ufile );

?>