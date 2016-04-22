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

//Dies ist die allgemeine Cache Klasse des CMS,
//hier wird das Menue gecached, auch Add-ons können einen Cache anlegen.
//Die Seiteninhalte werden nicht gecached, denn das Erstellen benötigt, im Gegensatz zum Menü, wenig Leistung
//Welche Datei gelesen wird ist da egal.
//	Verwendung:
//		Die Klasse wird automatisch vom CMS als $sitecache erstellt, sofern der Cache aktiviert ist.
//		Add-on können so zugreifen:
//			Boolean = cache_addon( ID , INHALT , NAME);
//			Array = get_cached_addon( ID , NAME );
//				ID -> RequestID oder andere ID
//				INHALT -> zu speichernder Inhalt
//				NAME -> Name des Add-on 

class cacheCMS{

	//Klasse init
	protected $menuefile, $addonfile, $sitefile, $sitecontent, $allgsysconf, $menue, $addon;

	public function __construct($allgsysconf, $sitecontent){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
	}

	//Menuepunkte cachen
	public function cache_menue($id, $name, $link, $niveau, $clicked, $requid, $langid = 0 ){
		//beim ersten Aufruf eine Menuecachedatei laden
		if(!is_object($this->menuefile)){
			//Datei der Sprache anpassen
			if( $langid != 0 ){
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'_lang_'.$langid.'.kimb');	
			}
			else{
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
			}
		}
		//alte Datei löschen, wenn vorhanden
		if( !isset( $this->menue )){
			$this->menuefile->delete_kimb_file( );
			$this->menuefile->write_kimb_new( 'time' , time() );
			$this->menue = 'yes';
		}
		//Menuedaten speichern
		$fileid = $this->menuefile->next_kimb_id();
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'link' , $link );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'name' , $name );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'niveau' , $niveau );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'clicked' , $clicked );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'requid' , $requid );

		return true;
	}
	//Menue aus Cache laden
	public function load_cached_menue($id, $langid = 0){
		//beim ersten Aufruf eine Menuecachedatei laden
		if(!is_object($this->menuefile)){	
			if( $langid != 0 ){
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'_lang_'.$langid.'.kimb');	
			}
			else{
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
			}
		}
		//Zeit überprüfen (Cache sollte nicht zu alt sein!)
		$time = $this->menuefile->read_kimb_one( 'time' );
		if(( time()-$time <= $this->allgsysconf['cachelifetime'] || $this->allgsysconf['cachelifetime'] == 'always' ) && $time != ''){
			
			//in einer Schleife alle Daten lesen und dem Menue hinzufügen
			$fileid = '1';
			while( true ){

				$name = $this->menuefile->read_kimb_id( $fileid , 'name');
				$link = $this->menuefile->read_kimb_id( $fileid , 'link');
				$niveau = $this->menuefile->read_kimb_id( $fileid , 'niveau');
				$clicked = $this->menuefile->read_kimb_id( $fileid , 'clicked');
				$requid = $this->menuefile->read_kimb_id( $fileid , 'requid');

				//wenn fertig verlassen
				if( $name == '' ){
					break;
				}

				$this->sitecontent->add_menue_one_entry($name, $link, $niveau, $clicked, $requid );

				$fileid++;
			}
			return true;
		}
		return false;
	}

	//Add-on cachen
	public function cache_addon( $id , $inhalt , $name = 'unknown'){
		//nach Datei gucken
		if(!is_object($this->addonfile)){
			//Datei laden
			$this->addonfile = new KIMBdbf('/cache/addon_'.$id.'.kimb');
		}
		//alte Datei löschen, wenn vorhanden
		if( !isset( $this->addon )){
			$this->addonfile->write_kimb_one( 'time' , time() );
			$this->addon = 'yes';
		}
		//Cache füllen
		$this->addonfile->write_kimb_one( 'inhalt-'.$name , $inhalt );
		
		return true;
	}

	//aus Cache lesen
	public function get_cached_addon( $id , $name = 'unknown' ){
		//nach Datei gucken
		if(!is_object($this->addonfile)){
			$this->addonfile = new KIMBdbf('/cache/addon_'.$id.'.kimb');
		}
		//Alter des Caches überprüfen
		$time = $this->addonfile->read_kimb_one( 'time' );
		if(( time()-$time <= $this->allgsysconf['cachelifetime'] || $this->allgsysconf['cachelifetime'] == 'always' ) && $time != '' ){
			//Cache lesen und zurückgeben
			return $this->addonfile->read_kimb_all( 'inhalt-'.$name );
		}
		return false;
		
	}
	
	//Seite in den Cache legen
	//	sinvoll, da das Parsen von Markdown Leistung braucht!!
	public function cache_site( $id, $langid, $seite, $trans ){
		//beim ersten Aufruf eine Seitencachedatei laden
		if(!is_object($this->sitefile)){	
			if( $langid != 0 ){
				$this->sitefile = new KIMBdbf('/cache/site_'.$id.'_lang_'.$langid.'.kimb');	
			}
			else{
				$this->sitefile = new KIMBdbf('/cache/site_'.$id.'.kimb');
			}
		}
		//alte Datei löschen
		$this->sitefile->delete_kimb_file( );
		$this->sitefile->write_kimb_one( 'time' , time() );
		
		//Arrays zu Strings 
		$seite = json_encode( $seite );
		$trans = json_encode( $trans );
		
		//Daten speichern
		$this->sitefile->write_kimb_one( 'seite' , $seite );
		$this->sitefile->write_kimb_one( 'trans' , $trans );
		
		return true;
	}
	
	//Seite aus Cache laden
	public function load_cached_site($id, $langid = 0){
		//beim ersten Aufruf eine Seitencachedatei laden
		if(!is_object($this->sitefile)){	
			if( $langid != 0 ){
				$this->sitefile = new KIMBdbf('/cache/site_'.$id.'_lang_'.$langid.'.kimb');	
			}
			else{
				$this->sitefile = new KIMBdbf('/cache/site_'.$id.'.kimb');
			}
		}

		//Alter des Caches überprüfen
		$time = $this->sitefile->read_kimb_one( 'time' );
		if(( time()-$time <= $this->allgsysconf['cachelifetime'] || $this->allgsysconf['cachelifetime'] == 'always' ) && $time != '' ){
			//Cache okay
			//	auslesen
			
			//Daten aus dbf lesen
			$seite= $this->sitefile->read_kimb_one( 'seite' );
			$trans = $this->sitefile->read_kimb_one( 'trans' );			
			
			//Arrays zu Strings 
			$seite = json_decode( $seite, true );
			$trans = json_decode( $trans, true );
					
			//Seite gleich hinzufügen
			$this->sitecontent->add_site($seite, $trans );
		
			return true;
		}
		return false;
	}
	
	//Cache einer Seite löschen
	//	$id => SiteID, deren Cache geleert werden soll
	//	$lang => Sprachversion, die gelöscht werden soll
	public function del_cache_site( $id, $lang = 0 ){
		
		//Cachedateinamen je nach Sprache wählen
		if( $langid != 0 ){
			$file = '/cache/site_'.$id.'_lang_'.$langid.'.kimb';	
		}
		else{
			$file = '/cache/site_'.$id.'.kimb';
		}
		
		//Datei vorhanden?
		if( check_for_kimb_file( $file ) ){
			//löschen versuchen
			return delete_kimb_datei( $file );
		}
		else{
			return false;
		}
	}

}

?>
