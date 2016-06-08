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

//Add-on URL zur vereinfachten Handhabung
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=auto_update_addons';

//dbf Datei
$updatefile = new KIMBdbf( 'addon/auto_update_addons__info.kimb' );

//Ist der Ordner /temp/ vorhanden?
if( !is_writable( __DIR__.'/temp/' ) || !is_dir( __DIR__.'/temp/' )  ){
	//wenn nicht dann erstellen
	mkdir( __DIR__.'/temp/' );
	//Rechte einstellen
	chmod( __DIR__.'/temp/' , (fileperms( __DIR__ ) & 0777));
}

//HTTP Requests müssen PHP erlaubt sein
if( !ini_get('allow_url_fopen') ) {
	//Fehlermedlung
	$sitecontent->echo_error( 'PHP muss "allow_url_fopen" erlauben!' );
}
//Temp-Ordner muss schreibbar sein!
elseif( !is_writable( __DIR__.'/temp/' ) ){
	//Fehlermeldung
	$sitecontent->echo_error( 'Der Ordner "'.__DIR__.'/temp/" muss schreibbar sein!' );
}
//Add-on installieren/ updaten
elseif( !empty( $_GET['installaddon'] ) ){
	
	//NameID aus URL
	$nameid = $_GET['installaddon'];
	//säubern
	$nameid = preg_replace( "/[^a-z_]/" , "" , $nameid );
	//Link zum Pack bekommen
	//	KIMB-API
	$addwert = json_decode( file_get_contents( 'https://api.kimb-technologies.eu/cms/addon/getcurrentversion.php?addon='.$nameid ) , true );
	
	//Link für User zur Liste
	$sitecontent->add_site_content( '<a href="'.$addonurl.'">&larr; Zurück zur Liste</a><br /><br />' );
	
	//Fehler?
	if( $addwert[0]['err'] == 'no' ){
		//URL aus Array
		 $url = $addwert[0]['link'];
		 
		 //Installdatei herunterladen
		//	Name der Installdatei
		$file = __DIR__.'/temp/'.time().'.kimbadd';
		 //	die Update-Datei auf dem Server öffnen
		$src = fopen( $url , 'r');
		//	den Ort für die Update-Datei im CMS öffnen
		$dest = fopen( $file , 'w+');
		//	Datei herunterladen
		if( !stream_copy_to_stream( $src, $dest ) ){
			//wenn Fehler -> Fehlermeldung
			$sitecontent->echo_error( 'Download des Add-ons nicht möglich!' );
		}
		else{ 
			//Add-on Datei laden, wenn nicht schon getan
			if( !isset( $addoninclude ) ){
				$addoninclude = new KIMBdbf('addon/includes.kimb');
			}
			//BE Addon Installation Klasse laden
			 $beaddinst = new BEaddinst($allgsysconf, $sitecontent, $addoninclude, false);
			 //Add-on Installieren
			 $beaddinst->install_addon( $file );
			 //Datei löschen
			 unlink( $file );
		}
	}
	else{
		//Fehlermedung, wenn API Fehler ausgibt
		$sitecontent->echo_error( 'Fehler beim Holen der Infos über das Add-on!' );
	}	
}
//Übersicht
else{
	//muss die Liste der Add-ons neu geladen werden?
	//	entweder nach 3 Tagen oder wenn User das will
	if( $updatefile->read_kimb_one('lastapijson') + 259200 < time() || isset( $_GET['getnewdata']) ){
		//JSON Liste laden
		$apijson = file_get_contents( 'https://api.kimb-technologies.eu/cms/addon/getall.php' );
		
		//Liste leer?
		if(empty( $apijson )){
			//Fehler + Fehlermeldung
			$sitecontent->echo_error( 'Es konnte keine neue Liste der Add-ons geladen werden.' );
		}
		else{
			//sonst Liste speichern
			$updatefile->write_kimb_one( 'apijson', $apijson );
			//Zeitpunkt speichern
			$updatefile->write_kimb_one( 'lastapijson', time() );
		}
	}
	
	//Liste schon definiert (wenn geade neu geladen)?
	if( empty( $apijson ) ){
		//sonst liste aus dbf laden
		$apijson = $updatefile->read_kimb_one('apijson');
	}
	
	//Liste sollte nicht leer sein
	if( !empty( $apijson ) ){
		
		//alle Add-ons als Array
		$alladdons =listaddons();
		//JSON Liste der API zu Array
		$jsondata = json_decode( $apijson, true);
		
		//CSS für Tabellen
		$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} div#reiter div.ui-widget-content a { color:#00f; }</style>');
		
		//jQuery UI Tabs (in de: Reiter)
		//	zuerst gewählter Tab nach GET[tab] (0 oder 1)
		if( empty( $_GET['tab'] ) || $_GET['tab'] != 1 && $_GET['tab'] != 0 ){
			//wenn nicht gewählt oder falsch -> 0 
			$_GET['tab'] = 0;
		}
		//JS für Tabs
		$sitecontent->add_html_header('<script> $(function() { $( "#reiter" ).tabs( { active: '.$_GET['tab'].' } ); }); </script>');
	
		//Links oben für Tabs
		$sitecontent->add_site_content( '<div id="reiter" style="overflow:hidden;">
  		<ul>
			<li><a href="#reiter_update">Updates</a></li>
			<li><a href="#reiter_inst">Installation</a></li>
		</ul>');
		
		//ersten Tab beginnen
		$sitecontent->add_site_content( '<div id="reiter_update">');
	
		//Teil mit Updates
		$sitecontent->add_site_content( '<h3>Updates</h3>' );
		
		//bisher keine gefunden
		$updates = false;
		
		//Tabelle beginnen
		$sitecontent->add_site_content( '<table>' );
		//Überschriften
		$sitecontent->add_site_content( '<tr><th>Add-on</th><th>Version installiert</th><th>Version aktuell</th><th></th></tr>' );
		//alle Add-ons des Systems durchgehen
		foreach( $alladdons as $add ){
			
			//INI des Add-ons des CMS lesen
			$ini = parse_ini_file( __DIR__.'/../'.$add.'/add-on.ini' , true);
			//Add-on Version lesen
			$addversion = $ini['inst']['addonversion'];
			
			//aktuelle Daten aus API
			//	Key in JSON Array feststellen
			$key =  array_search( $add, array_column($jsondata, 'addon') );
			//Key gefunden?
			if( $key !== false ){
				//Array des Addons extrahieren
				$apiadd = $jsondata[$key];
			}
			else{
				//kein Key, also kein Add-on aus der KIMB API, false
				$apiadd = false;
			}
	
			//sofern alle Daten verfügbar
			if( $apiadd != false ){
					
				//Add-on Versionen vergleichen
				//	Ist die aktuellste aus der API neuer als die auf dem CMS
				if( compare_cms_vers( $apiadd['version'], $addversion ) == 'newer'){
			
					//Link zur Homepage der CMS erstellen
					$hplink = '<a href="https://cmswiki.kimb-technologies.eu/addons/'.$apiadd['addon'].'" target="_blank" title="Zur Homepage des Add-ons">'.$apiadd['name'].'</a>';
					//Installiert Version
					$verinst = $addversion;
					//aktuelle Version
					$verakt = $apiadd['version'];
						
					//Add-on und CMS auf Kompatibilität testen
					if( compare_cms_vers( $apiadd['von'], $allgsysconf['build'] ) == 'newer' || compare_cms_vers( $allgsysconf['build'], $apiadd['bis'] ) == 'newer' ){
						//nicht kompatibel, Button Update auf disable setzen
						$butt = '<td title="Ihrem CMS fehlen Updates oder das Add-on ist zu alt.">Nicht kompatibel</td>';
					}
					else{
						//kompatibel, Button Update als Link zum Updatesystem
						$butt = '<td><a href="'.$addonurl.'&amp;installaddon='.$apiadd['addon'].'"><button>Update</button></a></td>';
					}
					
					//Tabellenzeile
					$sitecontent->add_site_content( '<tr><td>'.$hplink.'</td><td>'.$verinst.'</td><td>'.$verakt.'</td>'.$butt.'</tr>' );
						
					//jetzt Updates gefunden
					$updates = true;
				}
			}
		}
		//Tabelle beenden
		$sitecontent->add_site_content( '</table>' );
		
		//wurden Updates gefunden?
		if( !$updates ){
			//wenn nicht, dann Medlung
			$sitecontent->add_site_content( '<br /><i>Keine Updates verfügbar! Sehr schön :-)</i><br /><br />' );
		}
		
		//Link, damit User manuell eine neue Liste von der API downloaden kann (aktueller Tab bleibt erhalten)
		$sitecontent->add_site_content( '<a href="'.$addonurl.'&amp;getnewdata&amp;tab=0"><button>Neue Liste laden</button></a><br />' );
		
		//Tab beenden und Tab Installation
		$sitecontent->add_site_content( '</div><div id="reiter_inst">');
		
		//Überschift
		$sitecontent->add_site_content( '<h3>Installation</h3>' );
		
		//bisher keine Add-ons gefunden
		$installs = false;
		
		//Tabelle beginnen
		$sitecontent->add_site_content( '<table>' );
		//Überschriften	
		$sitecontent->add_site_content( '<tr><th>Add-on</th><th>Version</th><th></th></tr>' );
			
		//alle Add-ons aus JSON Array durchgehen 
		foreach( $jsondata as $add ){
			//Ist das Add-on noch nicht installiert?
			if( !in_array( $add['addon'], $alladdons ) ){
				
				//Link zur Homepage des Add-ons
				$hplink = '<a href="https://cmswiki.kimb-technologies.eu/addons/'.$add['addon'].'" target="_blank" title="Zur Homepage des Add-ons">'.$add['name'].'</a>';
						
				//Add-on und CMS auf KLompatibilität testen
				if( compare_cms_vers( $add['von'], $allgsysconf['build'] ) == 'newer' || compare_cms_vers( $allgsysconf['build'], $add['bis'] ) == 'newer' ){
					//nicht kompatibel, Button Install auf disable setzen
					$butt = '<td title="Ihrem CMS fehlen Updates oder das Add-on ist zu alt.">Nicht kompatibel</td>';
				}
				else{
					//kompatibel, Button Install als Link zum Installsystem
					$butt = '<td><a href="'.$addonurl.'&amp;installaddon='.$add['addon'].'"><button>Installieren</button></a></td>';
				}
					
				//Tabellenzeile
				$sitecontent->add_site_content( '<tr><td>'.$hplink.'</td><td>'.$add['version'].'</td>'.$butt.'</tr>' );
				
				//jetzt Add-ons gefunden
				$installs = true;
			}
		}
		//Tabelle beenden
		$sitecontent->add_site_content( '</table>' );
		
		//Wurden keine Add-ons gefunden?
		if( !$installs ){
			//Medlung
			$sitecontent->add_site_content( '<br /><i>Es konnten keine Add-ons für Sie gefunden werden!</i><br /><br />' );
		}
	
		//Link, damit User manuell eine neue Liste von der API downloaden kann (aktueller Tab bleibt erhalten)
		$sitecontent->add_site_content( '<a href="'.$addonurl.'&amp;getnewdata&amp;tab=1"><button>Neue Liste laden</button></a><br />' );
		
		//Tabs beenden
		$sitecontent->add_site_content( '</div></div>');
				
	}
	else{
		//es konnte keine Liste von der API gealden werden und es war auch keine gespeichert
		$sitecontent->echo_error( 'Es existiert keine Liste der Add-ons.' );
	}
}

?>
