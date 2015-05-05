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

//Add-on Konfiguration

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if(strpos( $_GET['addon'] , "..") !== false){
	echo ('Do not hack me!!');
	die;
}

if( isset( $_GET['addon'] ) ){

	if( is_file( __DIR__.'/../core/addons/'.$_GET['addon'].'/add-on.ini' ) ){

		$ini = parse_ini_file( __DIR__.'/../core/addons/'.$_GET['addon'].'/add-on.ini' , true);
		$addonname = $ini['about']['name'];
	}
	else{
		$addonname = $_GET['addon'];
	}
}

if( $_GET['todo'] == 'more' ){
	check_backend_login( 'fourteen' , 'more');
	
	if( isset( $_GET['addon'] ) ){

		$sitecontent->add_site_content('<h2>Add-on "'.$addonname.'" konfigurieren</h2>');
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more">&larr; Alle Add-ons</a>');
		$sitecontent->add_site_content('<a style="position:absolute; right:12px;" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon='.$_GET['addon'].'">Zur Add-on Nutzung &rarr;</a><hr />');

		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/conf_more.php') ){
			require_once( __DIR__.'/../core/addons/'.$_GET['addon'].'/conf_more.php' );
		}
		else{
			$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
		}
	}
	else{
		$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th>Add-on</th> </tr>');

		$addons = listaddons();
		foreach( $addons as $addon ){
			
			$sitecontent->add_site_content('<tr> <td><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon='.$addon.'">'.$addon.'</a></td> </tr>');

			$liste = 'yes';
		}
		$sitecontent->add_site_content('</table>');

		if( $liste != 'yes' ){
			$sitecontent->echo_error( 'Es wurden keine Add-ons gefunden!' );
		}
	}


}
elseif( $_GET['todo'] == 'less' ){

	check_backend_login( 'thirteen' );

	if( isset( $_GET['addon'] ) ){

		$sitecontent->add_site_content('<h2>Add-on "'.$addonname.'" nutzen</h2>');
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less">&larr; Alle Add-ons</a>');
		if( check_backend_login( 'fourteen' , 'more', false) ){
			$sitecontent->add_site_content('<a style="position:absolute; right:12px;" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon='.$_GET['addon'].'">Zur Add-on Konfiguration &rarr;</a>');
		}
		$sitecontent->add_site_content('<hr />');

		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/conf_less.php') ){
			require_once( __DIR__.'/../core/addons/'.$_GET['addon'].'/conf_less.php' );
		}
		else{
			$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
		}
	}
	else{
		$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th>Add-on</th> </tr>');

		$addons = listaddons();
		foreach( $addons as $addon ){
			
			$sitecontent->add_site_content('<tr> <td><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon='.$addon.'">'.$addon.'</a></td> </tr>');

			$liste = 'yes';

		}
		$sitecontent->add_site_content('</table>');

		if( $liste != 'yes' ){
			$sitecontent->echo_error( 'Es wurden keine Add-ons gefunden!' );
		}
	}

}
else{
	check_backend_login( 'twelve' );

	$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
