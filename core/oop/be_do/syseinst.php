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

defined('KIMB_CMS') or die('No clean Request');

//Diese Klasse ist für den Punkt Konfiguration im Backend zuständig. 

class BEsyseinst{
	
	//Klasse init.
	protected $allgsysconf, $sitecontent, $sonder,$conffile;
	
	//Array mit Erklärungen für alle Werte der Systemkonfiguration
	public $info = array(
		'sitename' => 'Name der Seite' ,
		'sitefavi' => 'URL zum favicon' ,
		'loginokay' => 'Zufälliger Code um Login zu verifizieren' ,
		'siteurl' => 'GrundURL der Seite (ohne / am Ende)' ,
		'description' => 'Allgemeiner Beschreibungs-Meta-Tag' ,
		'urlweitermeth' => 'PHP header (1) oder Meta Refresh (2) für Weiterleitungen' ,
		'adminmail' => 'E-Mail Adresse des Administrators' ,
		'robots' => 'Meta-Robots-Tag für Homepage' ,
		'mailvon' => 'E-Mail Absender des Systems' ,
		'cache' => 'on/ off des Caches (on ist empfehlenswert)' ,
		'fullcache' => 'on/ off des FullHTMLCaches (Achtung: Dieser Cache ist nicht mit allen Add-ons kompatibel, kann die Seitengeschwindigkeit aber stark erhöhen!)',
		'sitespr' => 'Sprache des Frontends (auto [an Userwahl orientieren] oder Vorgabe nach ISO 639; [nicht alle Sprachen sind verfügbar, das CMS bietet de,en,fr])' ,
		'systemversion' => 'Version des CMS' ,
		'build' => 'Genaue Version des Systems, wichtig für Updates,... (Beispiele: V1.0F-p0 -> Version 1.0 Final Patch 0 // V0.7B-p4,5 -> Version 0.7 Beta Patch 4 und 5) (entspricht GIT Tags)' ,
		'urlrewrite' => 'on / off des URL-Rewritings (on ist empfehlenswert)' ,
		'cachelifetime' => 'Lebensdauer des Caches in Sekunden oder always (always ist empfehlenswert)' ,
		'fullcachelifetime' => 'Lebensdauer des FullHTMLCaches in Sekunden (0 ist immer überschreiben!!)[259200 = 3 Tage]',
		'use_request_url' => 'Für URL-Rewriting muss der Request entweder an /index.php?url=xxx gesendert werden oder per $SERVER[REQUEST_URI] verfügbar sein. Letzteres kann hier verboten werden, da es auf manchen Server zu Problemen führen könnte. (ok/ nok)' ,
		'show_siteinfos' => 'Unten auf den Seiten anzeigen wann und von wem die Seite geändert wurde sowie den Permalink!' ,
		'theme' => 'Wählen Sie ein installiertes Thema für Ihre Seite, ohne oder mit falschem Parameter wird das Standardthema verwendet. (Dieser Wert wird automatisch bei einer Themeninstallation geändert.)',
		'cronkey' => 'Hier finden Sie den Cronkey. Diese Zeichenkette müssen Sie an die URL hängen, um den Systemcron ausführen zu können. (<cms-url>/cron.php?key=XXXX)',
		'lang' => 'Hier können Sie das CMS für mehrsprachige Seiten freischalten. (on / off) (Wird auch über Other -> Mehrsprachige Seite verändert)' ,
		'coolifetime' => 'Lebenszeit des Session Cookies in Sekunden, 0 -> bis Browser geschlossen wird',
		'smime_cert_ini_folder' => 'Ordner mit den Zertifikaten und der ini-Datei für den Versandt von unterschriebenen E-Mails (S/MIME). [absolute Pfade mit / beginnen, wenn ohne / relativ zu /core/conf/; siehe auch readme in /core/conf/pem/]',
		'smime_cert_ini_name' => 'Name der ini-Datei für den Versand von signierten E-Mails (S/MIME). [Die Datei muss im Ordner smime_cert_ini_folder liegen; z.B.: &apos;cert_cms.ini&apos;; wenn leer werden nur unsignierte E-Mails versandt]',
		'mail_log' => 'Alle vom System versandten E-Mails loggen (on/ off) [Die Logdatei wird unter /core/conf/mail.log erstellt]',
		'permalink_domain' => 'Geben Sie hier eine Domain bzw. eine Grundurl für die Permalinks an. [off/ URL](Es wird einfach die ID der Seite an den hier eingegebenen String angehängt und auf den Seiten angezeigt.;Sie müssen die Weiterleitung manuell einrichten. Siehe Beispiel im KIMB-CMS Wiki unter &apos;https://cmswiki.kimb-technologies.eu/tutorials/erste-schritte/konfiguration&apos;)',
		'overview_left' => 'Links auf der Seite einen aktuellen Auschnitt des Menüs anzeigen. (on/off) [nicht auf Mobilgeräten; nur wenn vom Theme unterstützt]',
		'markdown' => 'Soll für den Seiteninhalt und den seitenspezifischen Footer Markdown ermöglicht werden? (on[=> auf allen Seiten]/ off[=> auf keiner Seite]/ custom[=> für jede Seite einstellbar]) [MarkdownExtra von http://parsedown.org/]',
		'gns_limit' => 'Wie viele KIMB-GNS Mitteilungen sollen maximal pro Session/ User gesendet werden. (Reset durch Logout, Browserneustart)(off, leer => kein Limit)',
		'gns_extfile' => 'Pfad zur externen KIMB-GNS (https://www.kimb-technologies.eu/systeme/gns) PHP Datei (relativ zu Root [/] des CMS)(off, leer => kein GNS)'
	);
	
	public function __construct( $allgsysconf, $sitecontent,$conffile ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->conffile = $conffile;
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		
		$this->sonder = new KIMBdbf('sonder.kimb');
	}
	
	//Seitencache löschen
	public function make_syseinst_cachedel(){
		$sitecontent = $this->sitecontent;
		
		//alle Dateien im Cache Ordner auslesen		
		$caches = scan_kimb_dir('cache/');
		foreach( $caches as $cache ){
			//Datei für Datei löschen
			delete_kimb_datei( 'cache/'.$cache );
		}
		//Meldung
		$sitecontent->echo_message( 'Der Cache wurde gelöscht!', 'Systemcache' );
		
		//FullHTMLCache
		if( FullHTMLCache::clear_cache() ){
			//Meldung
			$sitecontent->echo_message( 'Der Cache wurde gelöscht!', 'FullHTMLCache' );
		}
		else{
			//Meldung
			$sitecontent->echo_error( 'Der Cache konnte nicht gelöscht werden!', 'unknown', 'FullHTMLCache' );
		}
		
		return;
	}
	
	//Konfiguration zum Anpassen zeigen
	public function make_syseinst_einst(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$info = $this->info;
		$conffile = $this->conffile;
		
		//Teil löschen?
		if ( $_GET['todo'] == 'del' && isset( $_GET['teil'] ) ){
		
			//Löschen des Teils duchführen, Meldung wenn okay
			if( $conffile->write_kimb_id( '001' , 'del' , $_GET['teil'] ) ){
				$sitecontent->echo_message( 'Der Parameter "'.$_GET['teil'].'" wurde aus der Konfiguration entfernt!' );
			}
		}
		
		//Teile verändern?
		if ( isset( $_POST['1'] ) ){
			//Daten nacheinader durchgehen 
				//	Post Werte beginned ab 1 nummeriert
				//		[Nummer] => Name des Konfigurationswertes
				//		[Nummer-wert] => Inhalt des Konfigurationswertes
			
			//alle durchgehen
			$i = 1;
			while( isset( $_POST[$i] ) ){
				//Wert verändert?
				if( $_POST[$i.'-wert'] != $allgsysconf[$_POST[$i]] ){
					//Wert anpassen
					if( $conffile->write_kimb_id( '001' , 'add' , $_POST[$i] , $_POST[$i.'-wert'] ) ){
						//Meldung, wenn Änderung erfolgreich
						$sitecontent->echo_message( 'Der Parameter "'.$_POST[$i].'" wurde in der Konfiguration geändert!' );
					}
				}
			$i++;
			}
		
			//Konfiguration neu laden
			$allgsysconf = $conffile->read_kimb_id('001');
		
		}
		
		
		$sitecontent->add_site_content('<h2>Systemkonfiguration</h2>');
		
		//JavaScript
		$this->jsobject->for_syseinst_all();
		
		//alle Namen der Konfigurationswerte lesen
		$confteile = $conffile->read_kimb_all_xxxid('001');
		
		//Eingabeformular beginnen
		$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?konf">');
		$sitecontent->add_site_content('<table width="100%" ><tr><th>Name</th><th>Wert</th><th width="20px;">Löschen</th><th width="20px;">Info</th></tr>');
		
		//für Nummerierung zählen
		$i = 1;
		//alle Teile durchgehen
		foreach( $confteile as $confteil ){
		
			//Gibt es eine Erklärung zum Teil?
			if( isset( $info[$confteil] ) ){
				//Erklärung anzeigen
				$infotab = '<span class="ui-icon ui-icon-info" title="'.$info[$confteil].'"></span>';
			}
			else{
				$infotab = '';
			}
		
			//Werte anzeigen
			
			if( $confteil == 'systemversion' || $confteil == 'build' ){
				//Bei Version und Build keine Änderung zulassen
				$sitecontent->add_site_content('<tr><td><input type="text" readonly="readonly" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" readonly="readonly" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span><span class="ui-icon ui-icon-trash" title="Löschen nicht erlaubt!"></span></span></td><td>'.$infotab.'</td></tr>');
			}
			else{
				//alle anderen normal als input
				$sitecontent->add_site_content('<tr><td><input type="text" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span onclick="var delet = del( \''.$confteil.'\' ); delet(); " style="display:inline-block;" ><span class="ui-icon ui-icon-trash" title="Diesen Wert löschen."></span></span></td><td>'.$infotab.'</td></tr>');
			}
			$i++;
		
		}
		//neuen Wert hinzufügen input
		$sitecontent->add_site_content('<tr><td><input type="text" placeholder="hinzufügen" name="'.$i.'"></td><td><input type="text" placeholder="hinzufügen" name="'.$i.'-wert"></td><td></td><td><span class="ui-icon ui-icon-info" title="Fügen Sie einen eigenen Wert in die allgemeine Konfiguration ein."></span></td></tr>');
		//Button
		$sitecontent->add_site_content('</table><input type="submit" value="Ändern"></form>');
	}

	//Footer-, Errortexte ändern
	public function make_syseinst_sonder_dbf( $POST ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$sonder = $this->sonder;
		
		//Übergabenamen den dbf Namen zuordnen
		$dos = array( 'footer' => 'footer', 'error-404' => 'err404', 'error-403' => 'err403' );
		
		//alles durchgehen
		foreach( $dos as $key => $val ){
			
			//den URL-Placeholder einsetzen
			$POST[$val] = str_replace( $allgsysconf['siteurl'],  '<!--SYS-SITEURL-->', $POST[$val] );
			
			//Text geändert?
			if( $sonder->read_kimb_one( $key ) != $POST[$val] ){
				//Änderung speichern
				if( $sonder->write_kimb_replace( $key , $POST[$val] ) ){
					//Meldung
					$sitecontent->echo_message( 'Der "'.$key.'" wurde geändert!' );
				}
			}
		}
		
		return true;
		
	}
	
	//Footer-, Errortexte ändern Eingabefelder
	public function make_syseinst_sonder(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$sonder = $this->sonder;
		
		//Daten übergeben?
		if( !empty( $_POST['footer'] ) &&!empty( $_POST['err404'] ) && !empty($_POST['err403'] ) ){
			//eintragen
			$this->make_syseinst_sonder_dbf( $_POST );
		}

		//Editoren laden (Footer, Inhalt)
		add_content_editor( 'footer' );
		add_content_editor( 'err404' );
		add_content_editor( 'err403' );
		
		//Eingabefelder
		$sitecontent->add_site_content('<h2>Error- und Footertexte</h2>');
		$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?text">');
		$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%;">'.$sonder->read_kimb_one( 'footer' ).'</textarea> <i>Footer &uarr;</i><br />');
		$sitecontent->add_site_content('<textarea name="err404" id="err404" style="width:99%;">'.$sonder->read_kimb_one( 'error-404' ).'</textarea> <i>Error 404 &uarr;</i><br />');
		$sitecontent->add_site_content('<textarea name="err403" id="err403" style="width:99%;">'.$sonder->read_kimb_one( 'error-403' ).'</textarea> <i>Error 403 &uarr;</i><br />');
		$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');
	}
	
	
}
?>
