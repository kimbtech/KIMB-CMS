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

//Konfiguration laden und Handhabung vereinfachen
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon=survey';

//Systemkonfigurationsdatei
$sysfile = new KIMBdbf( 'addon/survey__conf.kimb' );

//immer Tabelle
$maketable = true;

//Ergebnisse aufrufen?
if( isset( $_GET['stat'] ) && is_numeric( $_GET['stat'] ) ){
	//Ergebisse vorhanden?
	if( check_for_kimb_file( 'addon/survey__'.$_GET['stat'].'_erg.kimb' ) ){
		//auslesen
		$uid = $_GET['stat'];

		$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Zurück zur Liste</a>');

		$sitecontent->add_site_content('<h2>Statistik für Umfrage <u>'.$uid.'</u></h2>');

		//Prism JS
		$sitecontent->add_html_header('<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/system/prism/prism.min.css">'."\r\n".'<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/prism/prism.min.js"></script>');
		//CSS für System
		$sitecontent->add_html_header('<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/addondata/survey/main.min.css" media="all">');
		//Cart JS
		$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/survey/chart.min.js"></script>');
		//Vars
		$sitecontent->add_html_header('<script language="javascript">var addsur = { su :"'.$allgsysconf['siteurl'].'", uid : "'.$uid.'" };</script>');
		//JS für System
		//	Struktur
		$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/survey/bemain.min.js"></script>');
		//	Auswertung
		$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/survey/stat.min.js"></script>');
			
		//HTML Gerüst für System
		$sitecontent->add_site_content('<div id="addon_survey_main" class="addon_survey_main">Bitte aktivieren Sie JavaScript für das Umfragesystem!</div>');
		
		$maketable = false;
	}
	//keine Ergebnisse
	else{
		//norm. Tabelle
		$maketable = true;
	}
}

//Tabelle machen?
if( $maketable ){
	//alle UIDs lesen
	$uids = $sysfile->read_kimb_all_teilpl( 'uid' );
	//Array ( SeitenID => Seitenname ) erstellen
	foreach( list_sites_array() as $array ){
		$names[$array['id']] = $array['site'];
	}

	$sitecontent->add_site_content('<h2>Alle Umfragen</h2>');

	//Tabelle
	$sitecontent->add_site_content('<style>table#uliste{ width:100%; border:1px solid black; }</style>');
	$sitecontent->add_site_content('<table id="uliste">');
	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<th>ID</th>');
	$sitecontent->add_site_content('<th>Seite</th>');
	$sitecontent->add_site_content('<th>Ergebisse &amp; Auswertung</th>');
	$sitecontent->add_site_content('</tr>');
	//alle Seiten mit Umfrage durchgehen
	foreach( $uids as $uid ){
		$sitecontent->add_site_content('<tr>');
		$sitecontent->add_site_content('<td>'.$uid.'</td>');
		$sitecontent->add_site_content('<td>'.$names[$uid].'</td>');
		$sitecontent->add_site_content('<td>');
		//schon Ergebnisse?
		if( check_for_kimb_file( 'addon/survey__'.$uid.'_erg.kimb' ) ){
			$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;stat='.$uid.'"><span class="ui-icon ui-icon-image" title="Ergebnisse ansehen"></span></a>');
		}
		else{
			$sitecontent->add_site_content('<span class="ui-icon ui-icon-cancel" title="Keine Ergebnisse"></span>');
		}
		$sitecontent->add_site_content('</td>');
		$sitecontent->add_site_content('</tr>');
	}
	//leer ?
	if( $uids == array () ){
		$sitecontent->add_site_content('<tr><td colspan="3">&nbsp;</td></tr>');
		$sitecontent->add_site_content('<tr>');
		$sitecontent->add_site_content('<td colspan="3">Bisher keine Umfragen erstellt.</td>');
		$sitecontent->add_site_content('</tr>');
		$sitecontent->add_site_content('<tr><td colspan="3">&nbsp;</td></tr>');
	}
	$sitecontent->add_site_content('</table>');
	$sitecontent->add_site_content('<p>Die Umfragen können Sie im Backend in der Add-on Konfiguration bearbeiten!</p>');
}

?>