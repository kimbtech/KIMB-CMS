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


//Konf Datei laden
$cffile = new KIMBdbf( 'addon/blog__conf.kimb' );

//Haupseite
$hs = $cffile->read_kimb_one( 'hauptseite' );
//Menü
$men = $cffile->read_kimb_one( 'hauptseite' );

//Add-on richtig konfiguriert?
if( !empty( $hs ) && !empty( $men ) && is_numeric( $men ) ){

	//Alle Blogartikel (Seite mit Blog bestimmen)
	$seiten = make_menue_array_helper($men , false );
	//Seiten durchgehen, alles wichtige holen
	//	für Daten
		$blogsum = array();
	foreach ($seiten as $site ) {
		//Seite verfügbar?
		if( check_for_kimb_file( 'site/site_'.$site['siteid'].'.kimb' ) ){
			$dbf = new KIMBdbf( 'site/site_'.$site['siteid'].'.kimb' );

			//wichtiges lesen
			$zeit = $dbf->read_kimb_one( 'time' );
			$user = $dbf->read_kimb_one( 'made_user' );
			$title = $dbf->read_kimb_one( 'title' );
			//	Tags!!
			$key = array_map(
				"trim",
				array_filter(
					explode(
						',',
						$dbf->read_kimb_one( 'keywords' )
					),
					function ($va) {
						return !empty( $va );
					}
				)
			);
			$desc = $dbf->read_kimb_one( 'description' );

			$blogsum[] = array(
				'time' => $zeit,
				'author' => $user,
				'title' => $title,
				'tags' => $key,
				'about' => $desc,
				'requid' => $site['requid']
			);
		}

		//chronologisch absteigend
		array_multisort( $blogsum, SORT_DESC, SORT_NUMERIC,
			array_column( $blogsum, 'time' ), SORT_DESC, SORT_NUMERIC
		);
	}

	//Add-on Area
	if( $cffile->read_kimb_one( 'addonarea' ) == 'on'  ){
		//aktuelle Seite auch Blogseite??		
	}

	//Haupseite??
	if( $hs == $allgsiteid ){
		$sitecontent->add_site_content( '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/addondata/blog/main.dev.css" media="all">' );

		//Seite mit aktuellen Artikeln machen
		$sitecontent->add_site_content( '<div class="hauptseite blog">' );
		$sitecontent->add_site_content( '<dl>' );

		//mehr anzeigen?
		if( isset( $_POST['mehr'] )){
			$anzahl = 100;
		}
		//voreingesetllte Anzahl
		else{
			//immer eine maximale Anzahl an Artikeln zeigen
			$anzahl = $cffile->read_kimb_one( 'aktuelle_menge' );
			if( empty( $anzahl ) ){
				$anzahl = 6;
			}
		}
		$blogover = array_slice( $blogsum, 0, $anzahl );

		foreach ($blogover as $artikel ) {

			//Link machen
			$link = $allgsysconf['siteurl'].make_path_outof_reqid( $artikel['requid'] );

			$sitecontent->add_site_content( '<dt class="title">' );
			$sitecontent->add_site_content( '<a href="'.$link.'">' );
			$sitecontent->add_site_content( $artikel['title'] );
			$sitecontent->add_site_content( '</a></dt>' );

			$sitecontent->add_site_content( '<dd class="about">' );
			$sitecontent->add_site_content( $artikel['about'] );
			$sitecontent->add_site_content( '</dd>' );	

			$sitecontent->add_site_content( '<dd>' );
			if( $artikel['tags'] != array() ){
				$sitecontent->add_site_content( '<span class="tags">' );
				$tags =  implode( '</span><span class="tags">', $artikel['tags'] );
				$tags = substr( $tags, 0, -19 );
				$sitecontent->add_site_content( $tags );
			}
			$sitecontent->add_site_content( '</dd>' );	

			$sitecontent->add_site_content( '<dd><span class="time">' );
			$sitecontent->add_site_content( date( 'd.m.Y H:i:s', $artikel['time'] ) );
			$sitecontent->add_site_content( '</span></dd>' );

			$sitecontent->add_site_content( '<dd><span class="link">' );
			$sitecontent->add_site_content( '<a href="'.$link.'">Zum Artikel &rarr;</a>' );
			$sitecontent->add_site_content( '</span></dd>' );
		}

		$sitecontent->add_site_content( '</dl>' );
		
		//mehr Button ?
		if( !isset( $_POST['mehr'] )){
			$sitecontent->add_site_content( '<form action="" method="post"><input type="submit" name="mehr" value="Mehr anzeigen"></form>' );
		}

		$sitecontent->add_site_content( '</div>' );
	}
}

?>
