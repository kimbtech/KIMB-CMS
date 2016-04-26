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
	//	SeitenID, URL Rewrite an?, Seitensprache, Cache an?, Wünsche von Add-ons?
	protected $requid, $rewrite, $lang, $status = null, $other = array();
	
	//allgeines schonmal setzen
	public function __construct( $allgsysconf ){
		//Cache Pfad 
		$this->filesdir = __DIR__.'/full_cache/';
		//Systemkonfigurationsdaten (Cachealter)
		$this->allgsysconf = $allgsysconf;
		
		//Aufnahme beginnen
		$this->rec_start();
	}
	
	
	//Cache beginnen (Ausgabe aufnehmen oder vorhanden Cache ausgeben )
	//	$requid => Request ID der aktuellen Seite (INT/err404/err403)
	//	$rewrite => Zugriff per URL-Rewriting (true/false)
	//	$lang => ID der Sprache des Aufrufs 
	//	Rückgabe true/ false
	//		Skript wird beendet, wenn alter Cache erfolgreich geladen
	//		Cache wird durch diesen Aufruf bei true gefüllt und bei flase nicht,
	//			das Skript sollte aber trotzdem nicht unterbrochen werden.
	public function start_cache( $requid, $rewrite, $lang ){
		
		//Übergaben als Klassenvar
		$this->requid = $requid;
		$this->rewrite = $rewrite; 
		$this->lang = $lang;
		
		//Status schon gesetzt?
		if( $this->status == null ){
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
	public function end_cache(){
		//Seite soll wohl nicht gecached werden
		
		//Cache aus machen
		$this->status = false;
		//alle Daten aus Buffer ausgeben und diesen leeren
		ob_end_flush();
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
	//	Ein Add-on kann den Cache für eine Seite (auf der es Inhalte hat bearbeiten)
	//		$mode => on, off 
	//			(Modus des Cache setzen => Ganz normal an, Cache komplett deaktivieren )
	//		$aufp = Array(
	//				'POST' => ,
	//				'GET' =>,
	//				'COOKIE' =>
	//			);
	//			(Der Cache kann zwischen bestimmten Fällen unterscheiden)
	//			(Es wird ein Array übergeben, welches die Cache vorschreiben kann, was neben der URL/RequestID
	//			eines Aufrufs noch identisch sein muss, damit er die gleichen Inhalte ausspucken kann.)
	//			(Es wird für als Key POST/ GET/ COOKIE genommen und als Value das Attribut welches geleich sein soll.)
	public function addon_set( $mode = 'on', $aufp = array() ){
		//Cache soll aus?
		if( $mode == 'off' ){
			//gleich aus machen
			$this->end_cache();
		}
		else{
			//Daten lesen
			
			//alle möglichen Datenarten durchgehen
			foreach( array( 'POST', 'GET', 'COOKIE' ) as $art ){
				//aktuelle Datenart im Array wichtig?
				if( isset( $aufp[$art] ) ){
					// gewünschtes POST Attribut merken
					$other[$art][] = $aufp[$art];
				}
			}
			
		}
	}
	
	//Versuchen den passenden Cache für diese Seite zu laden
	protected function load_cache() {
		
		//Other beachten
		//Zeit beachten (wenn zu alt, JSON Zeile löschen!! und CacheDatei)
		
		return false;
	}
	
	
	//Die Aufzeichung des Caches beginnen
	protected function rec_start(){
		
		//Output Buffering nutzen
		return ob_start();
	}
}