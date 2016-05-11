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

//Diese Datei beherbergt die Backend Klasse für die Add-on Installation.
//Hier werden Add-ons installiert, de-, aktiviert und überprüft.

defined('KIMB_CMS') or die('No clean Request');

class BEaddinst{
	//Klasse init
	protected $allgsysconf, $sitecontent, $addoninclude, $jsobject;
	
	//Add-on API Einbindungsstellen
	public static $allinclpar = array( 'ajax', 'cron', 'funcclass', 'be', 'fe');
	
	public function __construct( $allgsysconf, $sitecontent, $addoninclude, $tabelle = true ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->addoninclude = $addoninclude;
		
		//Diese Klasse gibt alle benötigten JavaScript Codes im Backend aus
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		
		if( is_object( $this->sitecontent ) && $tabelle ){
			$this->sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		}
		
		//der Temp-Ordner sollte nach jeder Installation verschwinden, wenn dies schief geht, hier löschen!
		if( is_dir( __DIR__.'/../../addons/temp/' ) ){
			rm_r( __DIR__.'/../../addons/temp/' );
		}
	}
	
	//Status eines Add-on ändern
	//	$addon => NameID
	public function change_stat( $addon ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$addoninclude = $this->addoninclude;
		
		//alle Add-on API Einbindungsstellen aktivieren oder deaktivieren
		//	jede Stelle einzeln testen (wenn in der Liste, Addon aktiviert)
		foreach( self::$allinclpar as $par ){
			
			//Add-on in Liste?
			if( !$addoninclude->read_kimb_search_teilpl( $par , $addon ) ){
				//Add-on Datei für API vorhanden
				
				//eine oder zwei Dateien möglich?
				if( $par == 'be' || $par == 'fe' ){
					//eine der beiden Dateien vorhanden?
					if(
						file_exists(__DIR__.'/../../addons/'.$addon.'/include_'.$par.'_first.php') ||
						file_exists(__DIR__.'/../../addons/'.$addon.'/include_'.$par.'_second.php')
					){
						$fileokay = true;
					}
					else {
						$fileokay = false;
					}
				}
				else{
					//Nur eine Datei muss vorhanden sein!
					$fileokay = file_exists(__DIR__.'/../../addons/'.$addon.'/include_'.$par.'.php');
				}
				
				//Dateien okay, diese Einbindungsstelle laden?
				if( $fileokay ){
					//aktivieren
					$addoninclude->write_kimb_teilpl( $par , $addon , 'add' );
				}
			}
			else{
				//deaktivieren
				$addoninclude->write_kimb_teilpl( $par , $addon , 'del' );
			}
		}
	
		//Medlung
		$sitecontent->echo_message( 'Der Status des Add-ons "'.$addon.'" wurde geändert!' );
		
		return;
	}
	
	//für alle Add-ons Updates suchen (über die KIMB-technolgies CMS API) 
	public function check_addon_upd(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$addoninclude = $this->addoninclude;
		
		//HTTP request für PHP möglich?
		if( ini_get( 'allow_url_fopen' ) ){
	
			//addons listen
			$addons = listaddons();
	
			//Zeitpunkt des Tests speichern
			$addoninclude->write_kimb_one( 'lastcheck', time() );
	
			//die API will eine mit Komma getrennt Liste aller NameIDs der Add-ons und gibt einen JSON-String zurück	
			$querypar = implode( ',' , $addons );
			//Abfrage und JSON -> Array
			$addwert = json_decode( file_get_contents( 'https://api.kimb-technologies.eu/cms/addon/getcurrentversion.php?addon='.$querypar ) , true );
	
			//JSON-Array duchgehen
			foreach( $addwert as $ver ){
	
				//Add-on Name des JSON lesen
				$addon = $ver['addon'];
	
				//ist das Add-on aus JSON auch auf dem CMS?
				if( in_array( $addon, $addons ) ){
		
						//INI des Add-ons des CMS lesen
						$oldini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);
	
						//Add-on Versionen vergleichen und auf Fehler der API hören (Drittanbieter Add-ons)
						if( compare_cms_vers( $ver['aktuell'], $oldini['inst']['addonversion'] ) == 'newer' && $ver['err'] == 'no' ){
							//neuere Version vorhanden -> dbf
							$addoninclude->write_kimb_id( '21' , 'add' , $addon , 'upd' );
						}
						elseif( $ver['err'] == 'no' ){
							//keine neuere Version vorhanden -> dbf
							$addoninclude->write_kimb_id( '21' , 'add' , $addon , 'noup' );
						}
						else{
							//klappte wohl nicht -> dbf
							$addoninclude->write_kimb_id( '21' , 'add' , $addon , '---empty---' );
						}
	
				}
			}
	
			//Meldung
			$sitecontent->echo_message( 'Die Aktualität der Add-ons wurde überprüft!');
			
			return true;
		}
		else{
			//Medlung
			$sitecontent->echo_message( 'Ihr Server erlaubt PHP keine Requests per HTTP zu anderen Servern!' );
			
			return false;
		}
	}
	
	//Informationen über ein Add-on anzeigen
	//	$addon -> NameID
	public function show_addon_news( $addon ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		//INI lesen
		$oldini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);

		//Alle Informationen in Tabelle darstellen
		$sitecontent->add_site_content('<h2>Infos zum Add-on "'.$oldini['about']['name'].'"</h2>');
	
		$sitecontent->add_site_content('<table>');
		$sitecontent->add_site_content('<tr><td><b>Name:</b></td><td>'.$oldini['about']['name'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Version:</b></td><td>'.$oldini['inst']['addonversion'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>CMS Version (min - max):</b></td><td>'.$oldini['inst']['mincmsv'].' - '.$oldini['inst']['maxcmsv'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td colspan="2" ></td></tr>');
		$sitecontent->add_site_content('<tr><td><b>By:</b></td><td>'.$oldini['about']['by'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Lizenz:</b></td><td>'.$oldini['about']['lic'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Homepage</b></td><td><a href="'.$oldini['about']['url'].'" target="_blank">Besuchen!</a></td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Upadates und Downloads</b></td><td><a href="'.$oldini['about']['updurl'].'" target="_blank">Besuchen!</a></td></tr>');
		if( !empty( $oldini['about']['infot'] ) ){
			$sitecontent->add_site_content('<tr><td><b>Information</b></td><td>'.$oldini['about']['infot'].'</td></tr>');
		}
		$sitecontent->add_site_content('</table>');
	
		$sitecontent->add_site_content('<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php">&larr; Zur Add-on Seite</a>');

		return;
		
	}
	
	//Add-on löschen
	//	$addon => NameID
	public function del_addon( $addon ){
		$sitecontent = $this->sitecontent;
		$addoninclude = $this->addoninclude;
		
		//Add-on PHP Ordner löschen
		if( is_dir( __DIR__.'/../../addons/'.$addon.'/' ) ){
			rm_r( __DIR__.'/../../addons/'.$addon.'/' );
		}
		//Add-on load Ordner löschen
		if( is_dir( __DIR__.'/../../../load/addondata/'.$addon.'/' ) ){
			rm_r( __DIR__.'/../../../load/addondata/'.$addon.'/' );
		}
		//alle API Einbindungsstellen deaktivieren
		foreach( self::$allinclpar as $par ){
			if ( $addoninclude->read_kimb_search_teilpl( $par , $addon ) ){
				$addoninclude->write_kimb_teilpl( $par , $addon , 'del' );
			}
		}
		
		//Add-on Wish leeren
		$wish = new ADDonAPI( $addon );
		$wish->del();
		
		//Meldung
		$sitecontent->echo_message( 'Das Add-on "'.$addon.'" wurde gelöscht!' );
	}
	
	//Add-on installieren
	//	$file => Pfad zu einer Add-on Datei (*.kimbadd) auf dem Server
	public function install_addon( $file ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		//Im folgenden werden nacheinader viele Schritte duchgeführt und per IF überprüft,
		//in Fehlerfall wird eine Fehlermeldung ausgegeben und das Skript abgebrochen!
		
		//das Temp Verzeichnis erstellen
		if( !mkdir( __DIR__.'/../../addons/temp/' ) ){
			$sitecontent->echo_error( 'Es konnte kein Installationsverzeichnis erstellt werden!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}
		//Rechte des Temp Verzeichnisses anpassen
		if( !chmod( __DIR__.'/../../addons/temp/' , ( fileperms( __DIR__.'/../../addons/' ) & 0777) ) ){
			$sitecontent->echo_error( 'Es konnte kein Installationsverzeichnis eingerichtet werden!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}
	
		//Eine *.kimbadd Datei ist eine ZIP Datei mit dem PHP und load Ordner sowie der INI und der install.php Datei
		
		//Entpacken der Datei in den Temp Ordner 
		$zip = new ZipArchive;
		if ($zip->open( $file ) === TRUE) {
			$zip->extractTo( __DIR__.'/../../addons/temp/' );
			$zip->close();
		}
		else{
			$sitecontent->echo_error( 'Die Add-on Datei konnte nicht geöffnet werden!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}
	
		//lesen der INI Datei
		$addonini = parse_ini_file( __DIR__.'/../../addons/temp/add-on.ini' , true);
	
		//Namen extrahieren
		$name = $addonini['inst']['name'];
	
		//Namen überprüfen
		if( $name == 'temp' ){
			rm_r( __DIR__.'/../../addons/temp/' );
			$sitecontent->echo_error( 'Ein Add-on darf nicht "temp" heißen!' );
			$sitecontent->output_complete_site();
			die;
		}
	
		//Add-on und CMS Versionen auf kompatibilität testen
		if( compare_cms_vers( $addonini['inst']['mincmsv'], $allgsysconf['build'] ) == 'newer' ){
			rm_r( __DIR__.'/../../addons/temp/' );
			$sitecontent->echo_error( 'Sie haben eine zu alte Version des CMS für das Add-on "'.$name.'" !' );
			$sitecontent->output_complete_site();
			die;
		}
		//Add-on und CMS Versionen auf kompatibilität testen
		if( compare_cms_vers( $allgsysconf['build'], $addonini['inst']['maxcmsv'] ) == 'newer' ){
			rm_r( __DIR__.'/../../addons/temp/' );
			$sitecontent->echo_error( 'Sie haben eine zu neue Version des CMS für das Add-on "'.$name.'" !' );
			$sitecontent->output_complete_site();
			die;
		}
	
		//Ist das Add-on schon installiert
		if( is_dir( __DIR__.'/../../addons/'.$name.'/' ) ){
			
			//INI Datei der Installation lesen
			$oldini = parse_ini_file( __DIR__.'/../../addons/'.$name.'/add-on.ini' , true);
	
			//neues und altes Add-on anhand der Versionen prüfen
			//	Update?
			if( compare_cms_vers( $oldini['inst']['addonversion'], $addonini['inst']['addonversion'] ) == 'older' ){
				$sitecontent->echo_message( 'Das Add-on "'.$name.'" wurde mit dieser Installation aktualisiert!' );
				
				//ein Update!
				$addonupdate = true;
			}
			else{
				rm_r( __DIR__.'/../../addons/temp/' );
				$sitecontent->echo_error( 'Das Add-on "'.$name.'" ist bereits installiert!' );
				$sitecontent->output_complete_site();
				die;
			}
		}
	
		//Add-on installieren
		
		//INI Datei in den PHP Dateien Ordner des Add-on packen
		copy( __DIR__.'/../../addons/temp/add-on.ini', __DIR__.'/../../addons/temp/addon/add-on.ini' );
		//Die PHP Dateien an den richigen Ort verschieben
		copy_r( __DIR__.'/../../addons/temp/addon' , __DIR__.'/../../addons/'.$name.'/' );
		//Die load Dateien an den richigen Ort verschieben
		copy_r( __DIR__.'/../../addons/temp/load' , __DIR__.'/../../../load/addondata/'.$name.'/' );
	
		//einige Add-ons haben noch eine install.php, wenn vorhanden ausführen
		if( file_exists( __DIR__.'/../../addons/temp/install.php' ) ){
			require( __DIR__.'/../../addons/temp/install.php' );
		}
	
		//Temp Ordner löschen
		if( !rm_r( __DIR__.'/../../addons/temp/' ) ){
			$sitecontent->echo_error( 'Die Installationsdateien konnte nicht gelöscht werden!' );
			$sitecontent->output_complete_site();
			die;
		}
	
		//Meldung, wenn alles OK
		$sitecontent->echo_message( 'Das Add-on "'.$name.'" wurde installiert!' );
		
		//Update gemacht?
		if( isset( $addonupdate ) && $addonupdate ){
				//Jetzt nochmal neu nach Updates suchen
				$this->check_addon_upd();
		}
	}
	
	//Liste mit allen Add-ons anzeigen (inkl. Link für Updatecheck, Löchen, Informationen und Status)
	public function show_list(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$addoninclude = $this->addoninclude;
		
		//JS Code laden
		$this->jsobject->for_addon_inst();
	
		//HTML beginnen
		$sitecontent->add_site_content('<h2>Add-onliste</h2>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th>Name</th> <th>Status</th> <th>Updates</th> <th>Löschen</th> </tr>');
		//alle Add-ons in Array
		$addons = listaddons();
	
		//Updateinfos zu alt (älter als drei Tage) ?
		$lastcheck = $addoninclude->read_kimb_one( 'lastcheck' );
		if( $lastcheck + 259200 > time() ){
			//Infos okay -> in Array laden
			$updinfos = $addoninclude->read_kimb_id( '21' );
		}
		else{
			//Infos zu alt -> leeres Array
			$updinfos = array();
		}
	
		//für jedes Add-on Tabellenzeile anlegen
		foreach( $addons as $addon ){
	
			//INI lesen
			$oldini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);
	
			//Link zu Infos erzeugen
			$link = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=addonnews&addon='.$addon.'">'.$oldini['about']['name'].'</a>';
	
			//JS für Löschdialog
			$del = '<span onclick="var delet = del( \''.$addon.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Add-on löschen." style="display:inline-block;" ></span></span>';
	
			//je nach Status X oder V anzeigen
			//	Link um Status zu ändern
			if ( check_addon_status( $addon, $addoninclude ) ){
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=chdeak&amp;addon='.$addon.'"><span class="ui-icon ui-icon-check" style="display:inline-block;" title="Dieses Add-on ist zu Zeit aktiviert. ( click -> ändern )"></span></a>';
			}
			else{
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=chdeak&amp;addon='.$addon.'"><span class="ui-icon ui-icon-close" style="display:inline-block;" title="Dieses Add-on ist zu Zeit deaktiviert. ( click -> ändern )"></span></a>';
			}
	
			//Updateinfos lesen
			if( empty( $updinfos[$addon] ) ){
				//Leer -> keine Infos oder zu alt
				$upd = '<span class="ui-icon ui-icon-help" style="display:inline-block;" title="Keine aktuellen Daten! (Bitte prüfen Sie mit dem Button unten nach Updates!) [Sollten Sie gerade eine Abfrage gemacht haben, ist dieses Add-on nicht in der Datenbank!]"></span>';
			}
			elseif( $updinfos[$addon] == 'noup' ){
				//kein Update V
				$upd = '<span class="ui-icon ui-icon-check" style="display:inline-block;" title="Das Add-on ist aktuell!"></span>';
			}
			elseif( $updinfos[$addon] == 'upd' ){
				//Update verfügbar !
				$upd = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=addonnews&addon='.$addon.'"><span class="ui-icon ui-icon-alert" style="display:inline-block;" title="Es gibt ein Update für dieses Add-on!"></span>';
			}
			
			//Tabellenzeile hinzufügen
			$sitecontent->add_site_content('<tr> <td>'.$link.'</td> <td>'.$status.'</td> <td>'.$upd.'</td> <td>'.$del.'</td> </tr>');
	
			$liste = 'yes';
	
		}
		$sitecontent->add_site_content('</table>');
	
		//wenn keine Liste, keine Add-ons -> Meldung
		if( $liste != 'yes' ){
			$sitecontent->echo_error( 'Es wurden keine Add-ons gefunden!' );
		}
		
		//Nach Updates prüfen Button
		$sitecontent->add_site_content( '<p><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=checkall"><button>Add-ons auf Updates prüfen</button></a></p>' );
	
		//HTML Form um Add-ons für die Installation hoch zu laden
		$sitecontent->add_site_content('<br /><br /><h2>Add-on installieren</h2>');
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php" enctype="multipart/form-data" method="post">');
		$sitecontent->add_site_content('<input name="userfile" type="file" /><br />');
		$sitecontent->add_site_content('<input type="submit" value="Installieren" title="Wählen Sie eine Add-on Datei (&apos;*.kimbadd&apos;) von Ihrem Rechner zur Installation." />');
		$sitecontent->add_site_content('</form>');
	}
		
}
?>
