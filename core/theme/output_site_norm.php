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

// Diese Datei gibt das Grundgerüst für die Ausgabe
// Folgende Variablen sollten verwendet werden:
//    $this->header, $this->title, $this->menue, $this->addon, $this->sitecontent, $this->footer
//    array( $this->allgsysconf )
// Diese Datei ist Teil eines Objekts

defined('KIMB_CMS') or die('No clean Request');

//wenn mehrsprachige Seiten aktiviert HTML href-lang-Tag setzen
if( $this->allgsysconf['lang'] == 'on' ){
	//HTML Beginn
	echo '<!DOCTYPE html>'."\r\n";
	echo '<html lang="'.$this->requestlang['tag'].'">'."\r\n";
	echo "\t".'<head>'."\r\n";

	//Sprachenumschalter erstellen 
	$flagout = "\t\t\t".'<div id="lang">'."\r\n";
	$flagout .= "\t\t\t\t".'<ul>'."\r\n";
	foreach( $this->allglangs as $lang ){
		$flagout .= "\t\t\t\t\t".'<li>'."\r\n";
		$flagout .= "\t\t\t\t\t\t".'<a href="'.$lang['thissite'].'" hreflang="'.$lang['tag'].'"><img src="'.$lang['flag'].'" title="'.$lang['name'].'" alt="'.$lang['name'].'"></a>'."\r\n";
		$flagout .= "\t\t\t\t\t".'</li>'."\r\n";
		
		$headeralts .= "\t\t".'<link rel="alternate" hreflang="'.$lang['tag'].'" href="'.$lang['thissite'].'" />'."\r\n";
	}
	$flagout .= "\t\t\t\t".'</ul>'."\r\n";
	$flagout .= "\t\t\t".'</div>'."\r\n";
}
else{
	//HTML Beginn
	echo '<!DOCTYPE html>'."\r\n";
	echo '<html>'."\r\n";
	echo "\t".'<head>'."\r\n";

	$flagout = '';
}
//HTML Header der Seite beginnen
//	Titel
echo "\t\t".'<title>'.$this->allgsysconf['sitename'].' : '.$this->title.'</title>'."\r\n";
//	Icons
echo "\t\t".'<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
echo "\t\t".'<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
//	Generator
echo "\t\t".'<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\r\n";
//	Robots
echo "\t\t".'<meta name="robots" content="'.$this->allgsysconf['robots'].'">'."\r\n";
//	Description
echo "\t\t".'<meta name="description" content="'.$this->allgsysconf['description'].'">'."\r\n";
//	charset
echo "\t\t".'<meta charset="utf-8">'."\r\n";
//	CSS (font, print, screen)
echo "\t\t".'<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.min.css" media="all">'."\r\n";
echo "\t\t".'<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/design.css" media="screen">'."\r\n";
echo "\t\t".'<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/print.css" media="print">'."\r\n";
//	Touch Icon
echo "\t\t".'<link href="'.$this->allgsysconf['siteurl'].'/load/system/theme/touch_icon.png" rel="apple-touch-icon" />'."\r\n";
//JavaScript Code
//	Bei vielen Touch Geräten (alle außer iOS) wird das hover des Menüs falsch interpretiert.
//	Beim Klick auf einen Menüpunkt wird sofort der Link geöffnet, auch wenn noch ein Untermenü vorhaden ist, man kann auf
//	dem Untermenü nichts anklicken, es blitzt nur kurz auf.
//	Auf dem Desktop wird zwischen klicken und herüberfahren unterschieden.
//	Bei iOS wird erst beim zweiten Klick auf ein solches Menü der Link geöffnet, daher ist die Bedienung problemlos möglich.
//
//	Jeder Menüpunkt führt als onclick="" eine Funktion aus, welche überprüft ob es sich um ein Touch Gerät ohne iOS handelt, sofern
//	dies der Fall ist wird für jeden Menüpunkt die Anzahl der Klicks gezählt und erst beim zweiten der Link aufgerufen.
echo "\t\t".'<script>var clicks=new Array();function menueclick(id){var isTouch=(("ontouchstart" in window)||(navigator.msMaxTouchPoints>0));var iOS=(navigator.userAgent.match(/(iPad|iPhone|iPod)/g)?true:false);if(isTouch&&!iOS){if(!(id in clicks)){clicks[id]=0;}clicks[id]++;if(clicks[id]==2){return true;}else{return false;}}else{return true;}}</script>'."\r\n";
//	Viewport
echo "\t\t".'<meta name="viewport" content="width=device-width, initial-scale=1">'."\r\n";
//	Sprachversionen
echo ( !empty( $headeralts ) ? $headeralts : '' );

	//HTML Header hinzufügen
	echo "\r\n\r\n\t\t";
	//	zwei Tabs vorher einfügen
	echo preg_replace('/[\r\n]/', "\r\n\t\t", $this->header );
	echo "\r\n\r\n";

echo "\t".'</head>'."\r\n";
echo "\t".'<body>'."\r\n";
	//Die Seite beginnt
	echo "\t\t".'<div id="page">'."\r\n";

		//Header der Seite
		//	Logo und Link zur Startseite sowie Name der Seite				
		echo "\t\t\t".'<div id="header">'."\r\n";
			echo "\t\t\t\t".'<a href="'.$this->allgsysconf['siteurl'].'/">'.$this->allgsysconf['sitename']."\r\n";
			echo "\t\t\t\t".'<img src="'.$this->allgsysconf['siteurl'].'/load/system/theme/logo.png" style="border:none; float:right; height:90px;"></a>'."\r\n";
		echo "\t\t\t".'</div>'."\r\n";
		echo "\t\t\t".'<div>'."\r\n";

		//Menü
		//	ul muss geöffnet werden
		echo "\t\t\t\t".'<ul id="nav">'."\r\n\t\t\t\t\t";

			//	jeweils 4 Tabs davor, damit eingerückt wird
			echo preg_replace('/[\r\n]/', "\r\n\t\t\t\t\t", $this->menue ).'</a>'."\r\n\t\t\t\t\t";
			//schließendes li anfügen
			echo '</li>';
			//wenn nötig schließende ul anfügen
			echo str_repeat( '</ul>' , $this->ulauf );
			echo "\r\n";

			//Suchfunktion im der Menübar anzeigen
			//Ist das Add-on Suche installiert?
			if( is_dir( __DIR__.'/../addons/search_sitemap/' ) ){

				//Suche Konfiguration laden
				$search_sitemap['file'] = new KIMBdbf( 'addon/search_sitemap__conf.kimb' );
	
				//Ist die Suche aktiviert, auf welcher Seite liegt sie?
				$search_sitemap['searchsiteid'] = $search_sitemap['file']->read_kimb_one( 'searchsiteid' );
	
				//wenn die Suche aktiviert ist, HTML Form hinzufügen
				if( $search_sitemap['searchsiteid'] != 'off' && !empty( $search_sitemap['searchsiteid'] ) ){
	
					//als li das Suchfeld anfügen
					$search_str = htmlentities( $_REQUEST['search'], ENT_COMPAT | ENT_HTML401,'UTF-8' );
		
					echo "\t\t\t\t\t".'<li>'."\r\n";
					echo "\t\t\t\t\t\t".'<form method="post" action="'.$allgsysconf['siteurl'].'/index.php?id='.$search_sitemap['searchsiteid'].'">'."\r\n";
					echo "\t\t\t\t\t\t\t".'<input size="11" id="menue_search" type="text" name="search" placeholder="Suchbegriff" value="'.$search_str.'">'."\r\n";
					echo "\t\t\t\t\t\t".'</form>'."\r\n";	
					echo "\t\t\t\t\t".'</li>'."\r\n";
				}
			}

		//die Menübar beenden
		echo "\t\t\t\t".'</ul>'."\r\n";
		echo "\t\t\t".'</div>'."\r\n";

		//aktivierbar per Konfiguration
		if( $this->allgsysconf['overview_left'] == 'on' ){
			//Menue Übersicht links
			$over_menue = "\r\n".'<ul>'."\r\n";
			//noch nicht das aktuelle durch
			$clicked_durch = false;

			//augenommene Daten von menue PHP durchforsten
			foreach( $this->over_menue as $id => $data ){

				//Daten aus Array extrahieren
				$html = $data[1];
				$niv = $data[0];
				$next_niv = $this->over_menue[($id+1)][0];

				//wenn hier ID gleich ID der aktuellen Seite => aktuelles durch
				if( $id == $this->over_menue_clicked_id ){
					$clicked_durch = true;
				}

				//nur Menüpunkte des aktuellen Niveaus oder des Niveaus darunter sind interessant
				if( $this->over_menue_clicked_niv == $niv || $this->over_menue_clicked_niv == ($niv - 1) ){

					//gleiches Niveau??
					// sonst ein Niveau drunter, aber nur wenn auch ein ul dafür gemacht
					if( $this->over_menue_clicked_niv == $niv || $hadone ){
						//Element der Liste anfügen
						$over_menue .= '<li>'."\r\n";
						$over_menue .= "\t".$html;
						$over_menue .= '</li>'."\r\n";
					}

					//nächster Punkt Unterpunkt??
					//	nur unter dem aktuell geklicketen anschauen
					if( $next_niv > $niv && $id == $this->over_menue_clicked_id ){
						//ul aufmachen
						$over_menue .= '<ul>'."\r\n";

						//jetzt ein ul für Niveau darunter gemacht
						$hadone = true;
					}
					//nächster Punkt kein Untermenü mehr (und überhaupt eins offen)
					elseif( $next_niv < $niv && $hadone ){
						//ul schließen
						$over_menue .= '</ul>'."\r\n";

						//keins mehr offen
						$hadone = false;
					}
				}
				//nächtes Menü höher als das aktuell angeklickte??
				if( $next_niv < $this->over_menue_clicked_niv ){
					//ist das aktuell angeklickte schon durch??
					//wenn ja, dann ganze Liste beenden und Übersicht fertig
					if( $clicked_durch ){
						$over_menue .= "\r\n".'</ul>'."\r\n";
						break;
					}
					else{
						//sonst Liste zurücksetzen (aktuell geklickte kommt wohl noch)
						$over_menue = '<ul>'."\r\n";
					}
				}
			}

			//Übersicht als Add-on hinzufügen
			$this->add_addon_area($over_menue, '', 'overview_menue');
		}

		echo "\t\t\t".'<div id="site">'."\r\n";

		//Add-on Bereich anfügen
		if( !empty( $this->addon ) ){

				echo "\r\n\t\t\t\t";
				//	4 Tabs davor
				echo preg_replace('/[\r\n]/', "\r\n\t\t\t\t",$this->addon);
				echo "\r\n";

			//Passenden Inhalt
			echo "\t\t\t\t".'<div id="contents">'."\r\n";

				//Inhalte
				echo "\r\n\t\t\t\t\t";
				//	5 Tabs davor
				echo preg_replace('/[\r\n]/', "\r\n\t\t\t\t\t",$this->sitecontent);
				echo "\r\n";

			echo "\t\t\t\t".'</div>'."\r\n";
		}
		//keine Add-ons
		else{
			echo "\t\t\t\t".'<div id="contentm">'."\r\n";

				//Inhalte
				echo "\r\n\t\t\t\t\t";
				//	5 Tabs davor
				echo preg_replace('/[\r\n]/', "\r\n\t\t\t\t\t",$this->sitecontent);
				echo "\r\n";

			echo "\t\t\t\t".'</div>'."\r\n";
		}
		
		echo "\t\t\t".'</div>'."\r\n";

		//Sprachwahlflaggen
		echo $flagout;

		//Footer
		echo "\t\t\t".'<div id="footer" >'."\r\n";

			//Inhalte
			echo "\r\n\t\t\t\t";
			//	5 Tabs davor
			echo preg_replace('/[\r\n]/', "\r\n\t\t\t\t",$this->footer);
			echo "\r\n";

		echo "\t\t\t".'</div>'."\r\n";
	echo "\t\t".'</div>'."\r\n";

	//JavaScript Code
	//	Der Breadcrumb sowie die "siteinfos" (unterer Rand des Inhaltes) sollen keinen Inhalt überdecken.
	//	Absolute Abstände des Inhalts vom Rand können hier nicht angegeben werden, durch das responsive Design variieren diese Werte.
	//	Der Code sorgt dafür, dass der padding (Abstabd) des Inhaltes oben und unten ausreichen ist, sodass nichts verdeckt wird.
	echo "\t\t".'<script>function resize_cont(){if(document.getElementById("contentm")!=null){var cont=document.getElementById("contentm");}else{var cont=document.getElementById("contents");}cont.style.paddingTop=document.getElementById("breadcrumb").clientHeight+5+"px";if(document.getElementById("usertime")!=null){cont.style.paddingBottom=document.getElementById("usertime").clientHeight+5+"px";}if(document.getElementById("permalink")!=null){cont.style.paddingBottom=document.getElementById("permalink").clientHeight+5+"px";}}resize_cont();window.onresize=function(event){resize_cont();};</script>'."\r\n";
echo "\t".'</body>'."\r\n";
echo '</html>'."\r\n";

?>
