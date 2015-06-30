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

//Galeriedatei lesen
$galerie['file'] = new KIMBdbf( 'addon/galerie__conf.kimb' );

//hat die aktuelle RequestID eine Galerie?
$galerie['id'] = $galerie['file']->search_kimb_xxxid( $allgsiteid , 'siteid');

if( $galerie['id'] != false && !empty( $allgsiteid ) ){

	//Galerie vorhanden

	//jQuery nötig
	$sitecontent->add_html_header( '<!-- jQuery -->' );

	//alles über die Galerie lesen
	$galerie['c'] = $galerie['file']->read_kimb_id( $galerie['id'] );

	//die Anzahl an Bildern prüfen
	//	sonst Standard -> kein Limit
	if( !is_numeric( $galerie['c']['anz'] ) || $galerie['c']['anz'] <= 0 ){
		$galerie['c']['anz'] = '99999';
	}
	//gewünschte Größe der Bilder prüfen
	//	sonst Standard -> 250px
	if( !is_numeric( $galerie['c']['size'] ) || $galerie['c']['size'] <= 0 ){
		$galerie['c']['anz'] = '250';
	}

	//Galleryoverlay beginnen
	$echo = '<div id="galleryover" style="display:none;">';
		//Overlay vor dem Hintergrund
		$echo .= '<div style="position: fixed; top:0; right:0; height:100%; width:100%; background-color:#000; opacity:0.4;">';
		$echo .= '</div>';
		//Div für alle Bilder
		$echo .= '<div style="position: absolute; top:0; right:0; height:100%; width:100%; z-index:100;">';
			//Auswahlmenü des Overlays
			$echo .= '<div style="background-color:#888; margin:10px; border-radius:15px; padding:10px; border: 2px solid #fff;">';
				//Scheließen Button rechts
				$echo .= '<span style="float:right; margin-right:5px; margin-top:5px;" >';
					$echo .= '<button onclick="closeover();">'.$allgsys_trans['addons']['galerie']['schl'].'</button>';
				$echo .= '</span><center>';

				//Gallerieordner nach Bildern durchsuchen
				$scandir = scandir( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'] );

				//alle gefundenen Dateien in Ordner prüfen
				foreach( $scandir as $file ){
					//keine Punkte
					if( $file != '..' || $file != '.') {
						//Ist die Datei vorhanden?
						//Ist die Datei kein Thumbnail?
						//Ist die Datei ein Bild?
						if( is_file( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$file ) && strpos( $file , '--thumb--' ) === false && exif_imagetype( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$file ) != false ){
							//wenn alles Ja!, dann in das Array der Bilder einfügen
							$files[] = $file;
						}
					}
				}

				//Anzahl der Bilder bestimmen
				$anteile = count( $files ) - 1;

				//Galerie Zufall an?
				if( $galerie['c']['rand'] == 'on' ){

					//solange die Anzahl an Bilder nicht zu hoch
					//	mitzählen
					$i = 1;
					while ( $i <= $galerie['c']['anz'] ){

						//zufällig ein Bild wählen
						$num = mt_rand( 0 , $anteile );

						//wenn alle Bilder schon gewählt, dann die Schleife verlassen (weniger Bilder als Anzahl)
						if( count( $oldnums ) > $anteile ){
							break;
						}

						//wenn Bild nicht schon gewählt
						if( !in_array( $num , $oldnums ) ){

							//den Pfad zum Bild bestimmen
							$imggr = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num];

							//Gallery Thumb (Auswahlmenü des Overlays) erstellen
							//	Namen/Pfad
							$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

							//	wenn Thumb noch nicht erstellt -> machen
							if( !is_file( $imgkl ) ){
								saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , 100 );
							}

							//	den Link zum Thumb erstellen
							$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

							//	das Bild dem Auswahlmenü des Overlays anfügen
							$echo .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>';

							//Normal Thumb (Vorschau auf Seite) erstellen
							//	Namen/Pfad
							$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

							//	wenn Thumb noch nicht erstellt -> machen (hier gewünschte Größe verwenden)
							if( !is_file( $imgkl ) ){
								saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , $galerie['c']['size'] );
							}

							//	den Link zum Thumb erstellen
							$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

							//	das Bild der Vorschau auf der Seite hinzufügen
							$norm .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>'."\r\n";

							//Bild in Array der gewählten
							$oldnums[] = $num;
							//ein Bild mehr
							$i++;
						}
					}

				}
				//kein Zufall
				else{
					//genauso wie oben, nur ohne zufällge Auswahl
					$i = 1;
					$num = 0;
					while ( $i <= $galerie['c']['anz'] ){

						if( count( $oldnums ) > $anteile ){
							break;
						}

						$imggr = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num];

						//Gallery Thumb
						$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

						if( !is_file( $imgkl ) ){
							saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , 100 );
						}

						$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

						$echo .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>';

						//Normal Thumb
						$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

						if( !is_file( $imgkl ) ){
							saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , $galerie['c']['size'] );
						}

						$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

						$norm .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>'."\r\n";

						$oldnums[] = $num;
						$num++;
						$i++;
					}
				}

			//Auswahlmenü des Overlays beenden
			$echo .= '</center></div>';
			$echo .= '<div>';
				//großes Bild
				$echo .= '<img style="display:block; margin:auto;" boder="0" id="gallerybigimg" src="" alt="" title="" >';
	//Overlay beenden
			$echo .= '</div>';
		$echo .= '</div>';
	$echo .= '</div>';

	//JavaScript Code
	//	das gesamte Overlay wird per JS geladen, hier als JSON speichern
	$header = '<script>'."\r\n";
	$header .= "\t".'var galloverhtml = '.json_encode( $echo ).';'."\r\n";
	//	Funktionen um Overlay zu öffnen und großes Bild zu ändern
	//	sowie Overlay zu schließen
	$header .= "\t".'var openover = function( imgurl ) { 
		$( "div#galleryover" ).css( "display", "block" );
		$( "img#gallerybigimg" ).attr( "src" , imgurl );
	}
	function closeover(){
		$( "div#galleryover" ).css( "display", "none" ); 
	}
	$(function () {
		$( "body" ).append( galloverhtml );
		$( "div.imggallerydisplayhere" ).html( $( "div.imggalleryallnone" ).html() ); 
	});
	';
	$header .= '</script>';

	//alles dem Header anfügen
	$sitecontent->add_html_header( $header );

	//HTML-Code der Vorschau auf der Seite
	$gallnorm = '<center><div style="background-color:#ddd; border-radius:15px; padding:5px;">'."\r\n";
	$gallnorm .= $norm."\r\n";
	$gallnorm .= '</div></center>'."\r\n";

	//HTML-Code der Vorschau auf der Seite anfügen
	$sitecontent->add_site_content( '<div class="imggalleryallnone" style="display:none;">'."\r\n".$gallnorm.'</div>' );

	//wenn die Galerie über dem Seiteninhalt angezeit werden soll, dann hier den HTML-Code ausgeben
	if( $galerie['c']['pos'] == 'top' ){
		$sitecontent->add_site_content( '<div class="imggallerydisplayhere" style="background-color:#ddd; border-radius:15px;" >'.$allgsys_trans['addons']['galerie']['jsakt'].'</div>' );
	}
}

?>
