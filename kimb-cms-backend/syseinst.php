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



define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');
//BE Klassen
require_once(__DIR__.'/../core/oop/be_do/be_do_all.php');

//Systemeinstellungen in config.kimb ( eigenes Feld möglich )

$besyseinst = new BEsyseinst($allgsysconf, $sitecontent, $conffile);

if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){
	if ( $_GET['todo'] == 'purgecache' ){
		$besyseinst->make_syseinst_cachedel();
	}
}

check_backend_login( 'eleven' , 'more');


$sitecontent->add_site_content('<h2>Konfiguration Auswahl</h2>');
	
$sitecontent->add_html_header('<script> $(function() { $( "a#dohinw" ).button(); }); </script>');
	
$sitecontent->add_site_content('<center><br />
<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?konf" id="dohinw">Systemkonfiguration</a>
<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?text" id="dohinw">Error- und Footertexte</a>
<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?text&konf" id="dohinw">Gesamte Konfiguration</a>
<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?todo=purgecache" id="dohinw">Cache leeren</a>
</center><br /><hr /><br />');

if( isset( $_GET['konf'] ) &&  isset( $_GET['text'] ) ){
	$text = true;
	$konf = true;
}
elseif( isset( $_GET['text'] ) ){
	$text = true;
	$konf = false;
}
elseif( isset( $_GET['konf'] ) ){
	$text = false;
	$konf = true;
}
else{
	$sitecontent->add_site_content('Bitte wählen Sie oben einen Bereich aus, den Sie angezeigt bekommen wollen.');
}

if( $konf ){
	$besyseinst->make_syseinst_einst();
}

if( $konf && $text ){
	$sitecontent->add_site_content('<hr /><hr />');
}

if( $text ){
	$besyseinst->make_syseinst_sonder();
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
