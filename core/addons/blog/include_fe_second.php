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
//Allesseite
$as = $cffile->read_kimb_one( 'allesseite' );
//Menü
$men = $cffile->read_kimb_one( 'menue' );

//Add-on richtig konfiguriert?
if( !empty( $hs ) && !empty( $men ) && is_numeric( $men ) ){

	//CSS Blog
	$sitecontent->add_html_header( '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/addondata/blog/main.dev.css" media="all">' );

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

	//aktuelle Seite auch Blogseite??
	$siteids = array_column( $blogsum, 'requid' );
	//	testen
	$is_blogsite = in_array( $_GET['id'], $siteids );
	//Array-Key holen
	if( $is_blogsite ){
		$int_blogsite = array_search( $_GET['id'], $siteids );
	}

	//Add-on Area
	if( $cffile->read_kimb_one( 'addonarea' ) == 'on'  ){

		//aktuelle Seite auch Blogseite??
		if( $is_blogsite ){

			//aktuelle Artikel - wie auf Startseite 
			$html = '<h2>Aktuelle Artikel</h2>';
			$html .= '<ul>';
			foreach ( array_slice( $blogsum, 0, 3 ) as $artikel ) {
				$html .= '<li>';
				$html .= '<a href="'.$allgsysconf['siteurl'].make_path_outof_reqid( $artikel['requid'] ).'">'.$artikel['title'].'</a>';
				$html .= '</li>';
			}
			$html .= '</ul>';

			$sitecontent->add_addon_area( $html, '', 'blog' );

			// aehnliche Artikel (Tags)
			//	Array
			$blogaeh = array();
			//	Tags auslesen
			$tagsarray = array_column( array_slice( $blogsum, 0, 100 ), 'tags');
			$thistags = $blogsum[$int_blogsite]['tags'];
			//	alles Tags durchgehen
			foreach( $tagsarray as $key => $tags ){
				//leer?
				//aktueller Artikel ?
				if(
					$tags != array()
					&&
					$key != $int_blogsite
				){
					//Anzahl der Übereinstimmungen bestimmen
					$aehn = count( array_intersect( $thistags, $tags ) );
					//mindesten eine !
					if( $aehn > 0 ){
						$blogaeh[$key] = $aehn;
					}
				}
			}

			//Trefferanzahl absteigend
			arsort( $blogaeh, SORT_NUMERIC );
			//Ausgabe
			$html = '<h2>Ähnliche Artikel</h2>';
			$html .= '<ul>';
			$i = 1;
			foreach ( $blogaeh as $key => $aehn ) {
				//Anzhal einschränken
				if( $i > 4 ){
					break;
				}

				$html .= '<li>';
				$html .= '<a href="'.$allgsysconf['siteurl'].make_path_outof_reqid( $blogsum[$key]['requid'] ).'">'.$blogsum[$key]['title'].'</a>';
				$html .= '</li>';

				$i++;
			}
			$html .= '</ul>';

			//überhaupt was gefunden??
			if( $i > 1 ){
				$sitecontent->add_addon_area( $html, '', 'blog' );
			}
		}		
	}

	//Blogseite?
	if( $is_blogsite ){

		$sitecontent->add_site_content( '<div class="blogseite navigation">' );

		//Tags
		if( $blogsum[$int_blogsite]['tags'] != array() ){
			$sitecontent->add_site_content( '<div class="blogseite tags">' );
			$sitecontent->add_site_content( 'Tags: ' );

			$tags = '';
			foreach( $blogsum[$int_blogsite]['tags'] as $tag ){
				$tags .= '<span class="tags">'.$tag.'</span>';	
			}
			$sitecontent->add_site_content( $tags );

			$sitecontent->add_site_content( '</div>' );
		}

		//voriger Artikel vorhanden?
		if( isset( $blogsum[($int_blogsite - 1 )] ) ){
			$sitecontent->add_site_content( '<a class="left" href="'.$allgsysconf['siteurl'].make_path_outof_reqid( $blogsum[($int_blogsite - 1 )]['requid'] ).'">&larr; Vorheriger Artikel</a>' );
		}
		else{
			$sitecontent->add_site_content( '<span class="deak left">&larr; Vorheriger Artikel</span>' );
		}
		//nächster Artikel vorhanden?
		if( isset( $blogsum[($int_blogsite + 1 )] ) ){
			$sitecontent->add_site_content( '<a class="right" href="'.$allgsysconf['siteurl'].make_path_outof_reqid( $blogsum[($int_blogsite + 1 )]['requid'] ).'">Nächster Artikel &rarr;</a>' );
		}
		else{
			$sitecontent->add_site_content( '<span class="deak right">Nächster Artikel &rarr;</span>' );
		}

		$sitecontent->add_site_content( '</div>' );

	}

	//Haupseite oder "Allesseite"??
	if( $hs == $allgsiteid || $as == $allgsiteid  ){

		//Seite mit aktuellen Artikeln machen
		$sitecontent->add_site_content( '<div class="hauptseite blog">' );
		$sitecontent->add_site_content( '<dl>' );

		//mehr anzeigen?
		if( $as == $allgsiteid ){
			//Anzhal per GET gesetzt??
			if(
				isset( $_GET['anzahl'] ) &&
				is_numeric( $_GET['anzahl'] ) &&
				$_GET['anzahl'] > 0
			){
				//wenn Zahl, dann nehmnen
				$anzahl = $_GET['anzahl'];
			}
			else{
				//sonst, 100 fest
				$anzahl = 100;
			}
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
				$tags = '';
				foreach( $artikel['tags'] as $tag ){
					$tags .= '<span class="tags">'.$tag.'</span>';	
				}
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
		if( $hs == $allgsiteid ){
			$sitecontent->add_site_content( '<div class="navigation">' );
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].make_path_outof_reqid( $as ).'"><button>Mehr anzeigen</button></a>' );
			$sitecontent->add_site_content( '</div>' );
		}
		else{
			//Allesseite!
			$sitecontent->add_site_content( '<div class="navigation">' );
			$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/index.php" method="GET">' );
			$sitecontent->add_site_content( '<input type="hidden" name="id" value="'.$as.'">' );
			$sitecontent->add_site_content( 'Maximale Artikelanzahl: <input type="number" step="10" min="0" name="anzahl" value="'.$anzahl.'">' );
			$sitecontent->add_site_content( '<br />' );
			$sitecontent->add_site_content( '<input type="submit" value="Anzahl ändern">' );
			$sitecontent->add_site_content( '</form>' );
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].make_path_outof_reqid( $hs ).'"><button>Haupseite</button></a>' );
			$sitecontent->add_site_content( '</div>' );
		}

		$sitecontent->add_site_content( '</div>' );
	}
}

?>
