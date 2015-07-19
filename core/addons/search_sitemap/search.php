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

//Suchformular
$sitecontent->add_site_content( '<form method="post" action="">' );
//Suchbegriff
$sitecontent->add_site_content( '<input type="text" name="search" placeholder="'.$allgsys_trans['addons']['search_sitemap']['suchb'].'" value="'.htmlentities( $begriff ).'">' );
//Button
$sitecontent->add_site_content( '<input type="submit" value="'.$allgsys_trans['addons']['search_sitemap']['suchn'].'">' );
$sitecontent->add_site_content( '</form>' );

//Ist ein Suchbegriff gegeben
if( !empty( $begriff ) ){

	//keine Ergbnisse
	$anzahl = 0;

	//für make_menue_array() Dateien laden
	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	//Array des Menüs erstellen
	make_menue_array();

	//wichtiges aus Array in anderes Array
	foreach( $menuearray as $arr ){
		$seachlist[] = array( 'siteid' => $arr['siteid'], 'requestid' => $arr['requid'], 'menuename' => $arr['menuname'] );
	}

	//Sprache aktiviert?
	//	es sollen alle Sprchen durchsucht werden
	if($allgsysconf['lang'] == 'on' ){
	
		//Sprachdatei lesen
		$langfile = new KIMBdbf( 'site/langfile.kimb' );
		
		//alle Sprachen in Array
		$alllang = $langfile->read_kimb_all_teilpl('allidslist');
		//Standardsprache (0), weg
		foreach( $alllang as $la ){
			if( $la != 0){
				$alllangs[] = $la;
			}	
		}
		
		//Menünamen Dateien für alle Sprachen laden
		foreach( $alllangs as $id ){
			$menuenames_lang[$id] = new KIMBdbf('menue/menue_names_lang_'.$id.'.kimb');
		}
		
		//Sprache verwenden
		$lang = true;
	}

	//Maximale Versuche lesen
	$maxveruch = $search_sitemap['file']->read_kimb_one( 'maxversuch' );
	//Maximale Ergebnisse lesen
	$maxerg = $search_sitemap['file']->read_kimb_one( 'maxerg' );

	//erst nach Menünamen suchen, dann nach Seiteninhalten
	$parts = array( 'name', 'content' );
	//Array mit gefundenen Seiten
	$foundsites = array();

	//Parts durchgehen
	foreach( $parts as $part ){
		//alle Menüpunkte durchgehen
		foreach( $seachlist as $teil ){
			//Ist die Seite schon in den Ergebnissen?
			if( !in_array( $teil['requestid'], $foundsites ) ){
				//nein!
				
				//nach Menünamen
				if( $part == 'name' ){
					//Sprache an?
					if( $lang ){
						//Standardsprache in Arrays (werden später durchsucht)
						$strings[] = $teil['menuename'];
						$sitenames[] = $teil['menuename'];
						
						//andere Sprachen
						foreach( $alllangs as $id ){
							//Namen lesen
							$langname = $menuenames_lang[$id]->read_kimb_one( $teil['requestid'] );
							if( !empty( $langname ) ){
								//wenn Name nicht leer, auch diesen zum Durchsuchen hinzufügen
								$strings[] = $langname;
								$sitenames[] = $langname;
							} 
						}
					}
					else{
						//keine Sprache -> keine Arrays
						$string = $teil['menuename'];
						$sitename = $teil['menuename'];
					}
					//Infos über die Seite
					$requid = $teil['requestid'];
					$sid = $teil['siteid'];
				}
				//nach Seiteninhalt
				elseif( $part == 'content' ){
					//Inhalte laden
					$file = new KIMBdbf( 'site/site_'.$teil['siteid'].'.kimb' );
					//Seiteninfo
					$requid = $teil['requestid'];
					
					//Sprache verwenden?				
					if( $lang ){
						//Standardsprache
						$inh = $file->read_kimb_one( 'inhalt' );
						//alles in Arrays (werden später durchsucht)
						$inhalte[] = $inh;
						$strings[] = strip_tags( $inh );
						$sitenames[] = $teil['menuename'];
						
						//weitere Sprachen durchgehen
						foreach( $alllangs as $id ){
							//Inhalt lesen
							$inh = $file->read_kimb_one( 'inhalt-'.$id );
							//Inhalt sollte nicht leer sein!
							if( !empty( $inh ) ){
								//alles in Arrays (werden später durchsucht)
								$inhalte[] = $inh;
								$strings[] = strip_tags( $inh );
								$sitenames[] = $menuenames_lang[$id]->read_kimb_one( $teil['requestid'] );
							} 
						}
					}
					else{
						//keine Sprache -> keine Arrays
						$inhalt = $file->read_kimb_one( 'inhalt' );
						$string = strip_tags( $inhalt );
						$sitename = $teil['menuename'];
					}
				}

				//in Arrays zu suchen?
				if( is_array( $strings ) ){
					//im Array Strings befinden sich die zu durchsuchenden Inhalte
					foreach( $strings as $key => $str ){
						
						//Suchen
						$posi = stripos ( $str , $begriff );
						
						//nach Seiteninhalt?
						if( $part == 'content' ){
							//wenn ja, den Inhalt entsprechend der Sprache setzen
							$inhalt = $inhalte[$key];	
						}
						
						//Seitennamen entsprechend der Sprache setzen
						$sitename = $sitenames[$key];
						
						//wurde überhaupt was gefunden?
						if( $posi !== false ){
							//wenn ja, verlassen
							break;	
						}
					}
				}
				else{
					//kein Array, also einfach suchen
					$posi = stripos ( $string , $begriff );
				}

				//wurde was gefunden?
				if( $posi !== false ){

					//bei der Suche nach Menünamen gibt es keine Inhalte
					//hier der Fall?
					if( !isset( $inhalt ) ){
						//Inhalte der Seite lesen
						$file = new KIMBdbf( 'site/site_'.$sid.'.kimb' );
						$inhalt = $file->read_kimb_one( 'inhalt' );
					}
					
					//Inhalt für Ausgaben anpassen (kein HTML)
					$inhalt = strip_tags( $inhalt );
					//Suche nach Inhalt?
					if( $part == 'content' ){
						//in den Resultaten soll der Suchbegriff fett sein!
						
						//25 Zeichen vor der Fundstelle mit der Ausgabe beginnen
						$von = $posi - 25;
						//dann 25 Zeichen ausgeben
						$len = 25;
						//Länge des Suchbegriffs herausfinden
						$lenght = strlen( $begriff );
						//Ist die Startposition negativ?
						if( $von < 0 ){
							//wenn ja, dann die Anzahl der vor der Fundstelle auszugebenden Zeichen anpassen
							$len = 25 + $von;
							//ab dem ersten Zeichen ausgeben
							$von = 0;
						}
						//den vorderen Teil (vor der Fundstelle herausschneiden)
						$vorne = substr( $inhalt, $von, $len );
						//den hinteren Teil (nach der Fundstelle herausscheiden) [maximal 120 Zeichen]
						$hinten = substr( $inhalt, $von + $len + $lenght, 120 );					
						
						//die Ausgabe des Resultats zusammenbauen
						//	(vor der Funstelle, fetter Suchbegriff, nach der Funstelle)
						$inhalt = $vorne.'<b>'.$begriff.'</b>'.$hinten;
					}
					else{
						//wenn nach Menünamen, dann ohne Fettes die ersten 150 Zeichen ausgeben
						$inhalt = substr( $inhalt, 0, 150 );		
					}
					
					//Punkt der Liste hinzufügen
					$resultate .= '<li style="margin:5px; background-color:#ddd;">';
					//Menüname
					$resultate .= '<b><u><a href="'.$allgsysconf['siteurl'].'/index.php?id='.$requid.'" target="_blank">'.$sitename.'</a></u></b><br />';
					//Vorschau (Ausgabe des Resultats)
					$resultate .= $inhalt;
					$resultate .= '</li>';

					//Array mit gefundenen Seiten um diese erweitern
					$foundsites[] = $teil['requestid'];

					//Anzahl der Ergebnisse und Versuche erhöhen
					$anzahl++;
					$versuch++;
				}
				else{
					//nichts gefunden
					
					//Anzahl der Versuche erhöhen
					$versuch++;
				}

				//Varibalen für nächsten Durchgang leeren 
				unset($inhalt, $strings,$sting,$inhalte,$sitenames);
			}

			//Anzahl der maximalen Ergebnisse erreicht?
			if( $anzahl >= $maxerg ){
			
				//wenn ja, dann verlassen
				$anzahl .= ' +';
				$break = 'yes';
				break;
			}
			//Anzahl der maximalen Versuche erreicht?
			elseif( $versuch >= $maxveruch ){
				
				//wenn ja, dann verlassen
				$break = 'yes';
				break;
			}
		}

		//oben verlassen?
		if( isset( $break ) ){
			//hier auch!
			break;
		}
	}

	//Ergebnisse darstellen
	
	//Überschrift
	$sitecontent->add_site_content( '<h2>'.$allgsys_trans['addons']['search_sitemap']['res'].'</h2>' );
	$sitecontent->add_site_content( '<hr />' );
	//Anzahl der Ergebnisse
	$sitecontent->add_site_content( $allgsys_trans['addons']['search_sitemap']['anz'].': '.$anzahl );
	$sitecontent->add_site_content( '<br /><br />' );
	$sitecontent->add_site_content( '<ul>' );
	//Liste der Resultate
	$sitecontent->add_site_content( $resultate );
	$sitecontent->add_site_content( '</ul>' );
	$sitecontent->add_site_content( '<hr />' );
}

?>