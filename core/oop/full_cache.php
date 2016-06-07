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

//Diese Klasse dient dazu die gesamte Ausabe des CMS für einen
//Request zu cachen.
//Diese Klasse hat keine Verbindung zu "cacheCMS".
//	Im Unterschied zu letztgenanntem Cache hat dieser
//	Cache jedoch Auswirkungen auf das gesamte
//	CMS.
//
//	Add-ons müssen für die Verwendung mit "FullHTMLCache"
//	optimiert werden oder es kann zu Fehlfunktionen kommen.

defined('KIMB_CMS') or die('No clean Request');

class FullHTMLCache{
	
	//allgemeines zum Cache
	protected $filesdir, $allgsysconf;
	
	//speziell zu diesem Aufruf
	//	SeitenID, URL Rewrite an?, Seitensprache, Cache an?, Wünsche von Add-ons fertig?, Wünsche add-ons PreSaved
	protected $requid, $rewrite, $lang, $spr, $status = null, $other = array(), $other_pre = array();
	
	//allgeines schonmal setzen
	public function __construct( $allgsysconf ){
		//Cache Pfad 
		//	!! Bei Änderungen auch in der static Methode "self::clear_cache():" ändern. !!
		$this->filesdir = __DIR__.'/full_cache/';
		//Systemkonfigurationsdaten (Cachealter)
		$this->allgsysconf = $allgsysconf;
		
		//Aufnahme beginnen
		$this->rec_start();
	}
	
	
	//Cache beginnen (Ausgabe aufnehmen oder vorhanden Cache ausgeben )
	//	$requid => Request ID der aktuellen Seite (INT/err404/err403)
	//	$rewrite => Zugriff per URL-Rewriting (true/false)
	//	$lang => ID der Sprache des Aufrufs (Inhalt)
	//	$spr => Sprache der Seitenelemente (nicht Inhalt)
	//	Rückgabe true/ false
	//		Skript wird beendet, wenn alter Cache erfolgreich geladen
	//		Cache wird durch diesen Aufruf bei true gefüllt und bei flase nicht,
	//			das Skript sollte aber trotzdem nicht unterbrochen werden.
	public function start_cache( $requid, $rewrite, $lang, $spr ){
		
		//Übergaben als Klassenvar
		$this->requid = $requid;
		$this->rewrite = $rewrite; 
		$this->lang = $lang;
		$this->spr = $spr;
		
		//Add-on Wünsche per ADDonAPI Klasse gesetzt
		//müssen jetzt geladen werden
		$this->load_addonwish_kimbdbf();
		
		//Wünsche, Mitteilungen der Add-ons passend laden
		//	Status wird evtl. auf false gesetzt, wenn Add-ons das wollen!
		$this->addon_load_set();
		
		//Status schon gesetzt?
		if( $this->status === null ){
			//Nein, Cache auf an setzen
			$this->status = true;
		}
		//Cache aus?
		elseif( $this->status == false ){
			//passenden Rückgabe machen (Seite soll normal erstellt werden, aber keine Caching)
			return false;
		}
		
		//erstmal versuchen vorhandene Cache zu laden
		if( $this->load_cache() ){
			//wenn erfolgreich Seite ausgegeben und Skript kann beendet werden
			die;
		}
		else{
			//keine Cache oder zu alt, Cache neu erstellen
			return true;
		}
	}
	
	//Cache beenden (ohne Ausgabe und Datenspeicherung)
	public function end_cache( $del = false ){
		//Seite soll wohl nicht gecached werden
		
		if( $this->status ){
			//Bisherige Inhalte löschen?
			if( $del ){
				//Buffer leeren und beenden
				ob_end_clean();	
			}
			else{
				//alle Daten aus Buffer ausgeben und diesen leeren und beenden
				ob_end_flush();
			}
		}
			
		//Cache aus machen
		$this->status = false;
	}
	
	//Cache beenden (mit Ausgabe und Speicherung)
	public function __destruct(){
		
		//Cache überhaupt an?
		if( $this->status == true ){
			
			//Daten über den Request sammeln
			$data = array(
				'id' => $this->requid,
				'rew' => $this->rewrite,
				'lang' => $this->lang,
				'spr' => $this->spr,
				'info' => array( $_POST, $_GET, $_COOKIE ),
				'time' => time(),
				'filename' => uniqid( makepassw( 2 ,'abcdefghijklmnop') )
			);
			//RawCache Dateiname
			$rawname = $data['filename'];
			//Zu JSON (mit Zeilenumbruch am Ende)
			$data = json_encode( $data )."\r\n";
			
			//Ausgabe bekommen und ausgeben
			$out = ob_get_flush();
			
			//Cache Daten speichern
			//	JSON Daten (in Datei banannt nach RequestID, Zeile für Zeile ein Cache [])
			$fi = fopen( $this->filesdir.'info_'.$this->requid.'.json', 'a+' );
			fwrite( $fi, $data );
			fclose( $fi );
			//	Ausgabe (einfach den HTML-Code direkt in eine Datei)
			$fi = fopen( $this->filesdir.$rawname, 'w+' );
			fwrite( $fi, $out );
			fclose( $fi );
		}
		else {
			//Cache nie richtig begonnen
			$this->end_cache();
		}
	}
	
	//Gesamten Cache leeren
	//	statische Methode, kann ohne Objekt aufgerufen werden
	//		"FullHTMLCache::clear_cache();"
	public static function clear_cache(){
		
		//Ordner mit Cache Dateien
		$filesdir = __DIR__.'/full_cache/';
		
		//Dateien lesen und nach und nach löschen
		foreach( scandir( $filesdir ) as $file ){
			//keine zu schützenden Datei?
			if( $file != '.' && $file != '..' && $file != 'index.txt' ){
				//löschen
				unlink( $filesdir.$file );
			}
		}
		
		return true;
	}
	
	//Add-on Mitteilung
	//	Ein Add-on kann den Cache für eine Seite (auf der es Inhalte hat) bearbeiten
	//		$requestid => RequestID bei deren Aufruf die Wünsche gelten sollen ('a' => immer)
	//		$mode => on, off 
	//			(Modus des Cache setzen => Ganz normal an, Cache komplett deaktivieren )
	//		$aufp = Array(
	//				'POST' => Array( 'attribut1', 'attribut2' ),
	//				'GET' => Array( 'attribut1' ),
	//				'COOKIE' => Array()
	//			);
	//			(Der Cache kann zwischen bestimmten Fällen unterscheiden)
	//			(Es wird ein Array übergeben, welches die Cache vorschreiben kann, was neben der URL/RequestID
	//			eines Aufrufs noch identisch sein muss, damit er die gleichen Inhalte ausspucken kann.)
	//			(Es wird für als Key POST/ GET/ COOKIE genommen und als Value das Attribut welches geleich sein soll.)
	//	=> Die Mitteilungen werden vom System erstellt, das Add-on kann sie über die Klasse "ADDonAPI" melden.
	public function addon_set( $requestid , $mode = 'on', $aufp = array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() ) ){
		//Überhaupt noch möglich/ sinnvoll Wünsche zu äußern
		//	dies muss vor $this->start_cache(); sein
		if( $this->other == array() ){
			//Überall oder für best. ID?
			if( $requestid == 'a' ){
				$pretag = 'a';
			}
			//ID eine richtige Zahl?
			elseif( is_numeric( $requestid ) ){
				$pretag = $requestid;
			}
			else{
				return false;
			}
			//Array für Infos je RestID machen
			if( !is_array( $this->other_pre[$pretag] ) ){ 
				$this->other_pre[$pretag] = array();
			}		
			
			//Cache soll aus?
			if( $mode == 'off' ){
				//Immer auf aus stellen (für diese ID)
				$this->other_pre[$pretag]['status'] = false;
			}
			else{
				//Status schon gesetzt?
				if( !isset( $this->other_pre[$pretag]['status'] ) ){
					//nein, also auf an setzen
					$this->other_pre[$pretag]['status'] = true;
				}
				
				//Daten Array lesen
				foreach( $aufp as $art => $attrs ){
					//Array IDs okay?
					if( in_array( $art, array( 'POST', 'GET', 'COOKIE' ) ) ){
						
						//Array Other Pre mit $art erweitern 
						if( !is_array( $this->other_pre[$pretag][$art] ) ){
							//anlegen
							$this->other_pre[$pretag][$art] = array();
						}
						
						//Alle wichtigen Arrtibute aus Array $attrs lesen
						foreach ($attrs as $attr) {
							//wird Attribut schon als wichtig geführt?
							if( !in_array( $attr, $this->other_pre[$pretag][$art] ) ){
								//neues Attribut anfügen
								$this->other_pre[$pretag][$art][] = $attr;
							}
						}
					}
					else {
						//Falsche Übergabevals
						return false;
					}
				}
				
				//Array erfolgeich erweitert
				return true;
				
			}
		}
		else{
			//Umsetzung der Wünsche nicht mehr möglich
			return false;
		}
	}
	
	//Other_pre enthält die Wünsche der Add-ons,
	//diese jetzt für den Cache parsen
	//	Diese Funktion wird von $this->start_cache(); ausgeführt.
	protected function addon_load_set(){
		//Allgemeine Daten (für alle Request IDs) gesetzt
		if( is_array( $this->other_pre['a'] ) && $this->other_pre['a'] != array() ){
			//Array zusammenbauen
			$this->other = array_merge_recursive( $this->other, $this->other_pre['a'] );
			
			//Status auf aus?
			if( $this->other_pre['a']['status'] == false ){
				//Cache stoppen
				$this->status = false;	
			}
		}
		
		//Daten für die aktuelle Seite gewünscht
		if( is_array( $this->other_pre[$this->requid] ) && $this->other_pre[$this->requid] != array() ){
			//Array zusammenbauen
			$this->other = array_merge_recursive( $this->other, $this->other_pre[$this->requid] );
			
			//Status auf aus?
			if( $this->other_pre[$this->requid]['status'] == false ){
				//Cache stoppen
				$this->status = false;	
			}
			
		}
		
		//Statusdaten schon verarbeitet
		unset( $this->other['status'] );
		
		//Okay, weiter geht's
		return true;
	}
	
	//Versuchen den passenden Cache für diese Seite zu laden
	protected function load_cache() {
		
		//Cache JSON Datei für RequestID vorhanden??
		if( is_file( $this->filesdir.'info_'.$this->requid.'.json' ) ){
			
			//Array für veralteten Cache Zeilen anlegen
			$outdated_rows = array();
			
			//alle Zeilen mit je einem Cached Request lesen
			$caches = file( $this->filesdir.'info_'.$this->requid.'.json' );
			
			//alle durchgehen und gucken ob passend
			foreach( $caches as $cid => $cache ){
				
				//aktuelle JSON Zeile parsen
				$data = json_decode( trim ( $cache ), true );
				
				//Daten okay?
				if( is_array( $data ) ){
					//grundsätzliche Werte okay?
					if( 
						$data['id'] == $this->requid &&
						$data['rew'] == $this->rewrite &&
						$data['lang'] == $this->lang &&
						$data['spr'] == $this->spr
					){
						//Cache Alter okay
						if( $data['time'] + $this->allgsysconf['fullcachelifetime'] >= time() ){
							
							//Other beachten
							if( $this->other != array() ){
								//Othervalues
								$othervalues = array();
								
								//GET, COOKIE, POST für unten in andere Var
								$GET = $_GET;
								$POST = $_POST;
								$COOKIE = $_COOKIE;
								
								//	alle möglichen Arten durchgehen
								foreach( array( 'POST', 'GET', 'COOKIE' ) as $arrid => $art ){
									//Attribute der art anschauen
									foreach( $this->other[$art] as $attr ){
										
										//Name der passenden globalen Var
										$varname = $art;
										
										$debug = array(
											isset( $data['info'][$arrid][$attr] ),
											isset( ${$varname}[$attr] ),
											$data['info'][$arrid][$attr],
											${$varname}[$attr],
											$_GET[$attr]
										);
										
										
										//Gewünschtes Attribut in Cache Datei gegeben und bei diesem Aufruf
										if( isset( $data['info'][$arrid][$attr] )  && isset( ${$varname}[$attr] ) ){
											//aktueller Wert auch wie in 
											if( ${$varname}[$attr] == $data['info'][$arrid][$attr] ){
												//passt (Inhalte gleich)
												$othervalues[] = true;
											}
											else{
												//passt nicht
												$othervalues[] = false;	
											}
										}
										elseif(
											//Gewünschtes Attribut in Cache Datei gegeben und nicht bei diesem Aufruf
											( isset( $data['info'][$arrid][$attr] )  && !isset( ${$varname}[$attr] ) ) ||
											//Gewünschtes Attribut nicht in Cache Datei gegeben und bei diesem Aufruf
											( !isset( $data['info'][$arrid][$attr] )  && isset( ${$varname}[$attr] ) )
										){
											//passt nicht
											$othervalues[] = false;	
										}
										else{
											//passt (beide leer)
											$othervalues[] = true;	
										}
									}
								}
								
								//Eines der Other falsch (unpassend)?
								if( in_array( false, $othervalues ) ){
									//klappt nicht
									$otherok = false;
								}
								else{
									//klappt!!
									$otherok = true;
								}
							}
							else{
								//kein Other gewünscht => Cache okay
								$otherok = true;
							}
							
							//Other wie gewünscht?
							if( $otherok ){
								//keinen weiteren Cache
								$this->end_cache(true);
								//Cache geladen
								$cacheloaded = true;
								//HTML ausgeben
								readfile( $this->filesdir.$data['filename'] );
								//forach verlassen
								break;
							}
							
						}
						else{
							//alte Caches zum Löschen vormerken!!
							$outdated_rows[] = array( $cid, $data['filename'] );
						}
					}
				}
			}
			
			//veraltete Cache Zeilen löschen
			if( $outdated_rows != array() ){
				
				//alle zu alten löschen
				foreach( $outdated_rows as $rows ){
					
					//JSON Info löschen
					//	JSON Infodatei Zeile aus Array weg 
					unset( $caches[$rows[0]] );
					
					//CacheDatei löschen
					unlink( $this->filesdir.$rows[1] );
				}
				
				//Neue Cache Daten (drüber-)speichern
				//	JSON Daten (in Datei banannt nach RequestID, Zeile für Zeile ein Cache [])
				$fi = fopen( $this->filesdir.'info_'.$this->requid.'.json', 'w+' );
				fwrite( $fi, implode('', $caches ) );
				fclose( $fi );
			}
			
			//Cache geladen ??
			if( isset( $cacheloaded ) && $cacheloaded ){
				//Cache geladen
				return true;
			}
			else{
				//kein Cache geladen
				return false;
			}
		}
		//keine Info Datei => keine Cache
		else{
			return false;	
		}
	}
	
	
	//Die Aufzeichung des Caches beginnen
	protected function rec_start(){
		
		//Output Buffering nutzen
		return ob_start();
	}
	
	//Diese Funktion lädt die von den Add-ons mit der ADDonAPI
	//geäußerten Wünsche aus der fullcache.kimb.
	protected function load_addonwish_kimbdbf(){
		
		//Datei mit Wünschen
		$fullcache = new KIMBdbf('addon/wish/fullcache.kimb');
		//Datei mit aktiven Add-ons
		$addoninclude = new KIMBdbf('addon/includes.kimb');
		
		//alle aktivierten Add-ons (auf dem FE)
		$all = $addoninclude->read_kimb_all_teilpl( 'fe' );
		
		//alle IDs in der Wunschdatei lesen
		$ids = $fullcache->read_kimb_all_teilpl( 'allidslist' );
		
		//alle Wünsche durchgehen
		foreach( $ids as $id ){
			//Datensatz lesen
			$data = $fullcache->read_kimb_id( $id );
			
			//Add-on mit diesem Datensatz auch geladen?
			if( in_array( $data['addon'], $all) ){
				
				//zu Array
				$data = json_decode( $data['array'], true );
				
				//alle Wünsche durchgehen
				foreach( $data as $k => $v){
					
					//und in FullCache Klasse/ Objekt laden
					$this->addon_set( $k , $v[0], $v[1] );
					
				}
				
			}
		}
		
		//Okay
		return true;
		
	}
}