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

//Diese Klasse dient dazu die Wünsche für die Add-on API eines Add-on abzuspeichern,
//die  Werte werden beim ausführen des CMS von der Add-on API verarbeitet
//Verwendung:
//	$addonapi = new AddonAPI( 'Add-on Name' );
//	Methode aufrufen und Werte übergeben
class ADDonAPI{

	//Klasse init
	protected $be, $fe, $funcclass, $addon, $cron, $fullcache;
	
	//leeres Conf Array für FullHTMLCache Wish
	public $emptyjson = '{
				"a":[
					"on",
					{
						"POST":[],
						"GET":[],
						"COOKIE":[]
					}
				]
			}',
		$emptyarray = array(
				'a' => array(
					'on',
					array(
						'POST' => array(),
						'GET' => array(),
						'COOKIE' => array()
					)
				)
			);

	public function __construct( $addon ){
		$this->addon = $addon;

		//alle Wunschdateien laden
		$this->be = new KIMBdbf('addon/wish/be_all.kimb');
		$this->fe = new KIMBdbf('addon/wish/fe_all.kimb');
		$this->funcclass = new KIMBdbf('addon/wish/funcclass_stelle.kimb');
		$this->cron = new KIMBdbf('addon/wish/cron_age.kimb');
		$this->fullcache = new KIMBdbf('addon/wish/fullcache.kimb');
	}

	//in jeder Datei hat ein Add-on einen andere ID,
	//diese wird hier bestimmt
	protected function get_addon_id( $fi ){

		//suchen
		$id = $this->$fi->search_kimb_xxxid( $this->addon , 'addon' );

		//überprüfen
		if( $id != false ){
			//ausgeben
			return $id;
		}
		else{
			//sonst neu erstellen
			$id = $this->$fi->next_kimb_id();
			if( $this->$fi->write_kimb_id( $id , 'add' , 'addon' , $this->addon ) ){
				//ausgeben
				return $id;
			}
			else{
				return false;
			}
		}
	}

	public function set_be( $reihen, $site, $rechte ){
		// Backend Wünsche speichern

		// $reihen => vorn oder hinten
		// $site => XXX.php
		// $rechte => more,less,one,six

		//Parameter überprüfen
		if( $reihen != 'vorn' && $reihen != 'hinten' ){
			return false;
		}

		//ID herausfinden
		$id = $this->get_addon_id( 'be' );

		//wenn ID okay, alles speichern
		if( is_numeric( $id ) ){
			$rstelle = $this->be->write_kimb_id( $id , 'add' , 'stelle' , $reihen );
			$rrecht = $this->be->write_kimb_id( $id , 'add' , 'recht' , $rechte );
			$rsite = $this->be->write_kimb_id( $id , 'add' , 'site' , $site );

			if( $rstelle && $rrecht && $rsite ){
				return true;
			}
		}

		return false;
	}

	public function set_fe( $reihen, $ids, $error ){
		// Frtontend Wünsche speichern

		// $reihen => vorn oder hinten
		// $id => r/s/a + ( ID )
		// $error => no/ all/ (nur) 404/ 403

		//Parameter überprüfen
		if( $reihen != 'vorn' && $reihen != 'hinten' ){
			return false;
		}

		//ID herausfinden
		$id = $this->get_addon_id( 'fe' );

		//wenn ID okay, alles speichern
		if( is_numeric( $id ) ){
			$rstelle = $this->fe->write_kimb_id( $id , 'add' , 'stelle' , $reihen );
			$rids = $this->fe->write_kimb_id( $id , 'add' , 'ids' , $ids );
			$rerror = $this->fe->write_kimb_id( $id , 'add' , 'error' , $error );

			if( $rstelle && $rids && $rerror ){
				return true;
			}
		}

		return false;
	}

	public function set_funcclass( $reihen ){
		// Funktionen und Klassen Wünsche speichern

		// $reihen => vorn oder hinten

		//Parameter überprüfen
		if( $reihen != 'vorn' && $reihen != 'hinten' ){
			return false;
		}

		//ID herausfinden
		$id = $this->get_addon_id( 'funcclass' );

		//wenn ID okay, alles speichern
		if( is_numeric( $id ) ){
			if( $this->funcclass->write_kimb_id( $id , 'add' , 'stelle' , $reihen ) ){
				return true;
			}
		}

		return false;
	}
	
	public function set_cron( $minage ){
		// Cron Aufrufabstand in Sekunden speichern

		// $minage => Sekunden zu warten zwischen Abrufen 

		//Parameter überprüfen
		if( is_numeric( $minage ) ){
			//ID herausfinden
			$id = $this->get_addon_id( 'cron' );

			//wenn ID okay, alles speichern
			if( is_numeric( $id ) ){
				if( $this->cron->write_kimb_id( $id , 'add' , 'minage' , $minage ) && $this->cron->write_kimb_id( $id , 'add' , 'lastaufruf' , '1000' ) ){
					return true;
				}
			}
		}

		return false;
	}

	public function del(){
		// Add-on Wünsche löschen

		//sollte alles okay sein
		$re = true;

		//jede Wunschdatei durchgehen
		foreach( array( 'fe', 'be', 'funcclass', 'cron', 'fullcache' ) as $fi ){
			//ID lesen
			$id = $this->get_addon_id( $fi );
			//ID testen und alles sollte okay sein
			if( is_numeric( $id ) && $re == true ){
				//ID löschen
				$re = $this->$fi->write_kimb_id( $id , 'del' );
			}
			else{
				//eins falsch, alles fehlerhaft
				$re = false;
			}
		}
		
		//Rückgabe
		return $re;
	}
	
	//für FullHTMLcache
	//	Wünsche des Add-ons hier allgemein nennen
	//		CMS (FullHTMLCache) lädt diese dann beim Seitenaufruf automatisch
	//	$do => 'set' (überschreiben), 'read' (aktuelles Array zurück)
	//	$array => array(
	//			'a' => array( $mode, array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() ) ),
	//			requID => array( $mode, array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() ) ),
	//			<< weitere IDs >>
	//		);
	//			(Es ist nicht zwingend erforderlich, dass das Array immer 'a' enthält, es kann auch nur eine ID haben.
	//			Das Array in der ID muss jedoch dem angegebenen Schema folgen:
	//				array( $mode, array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() ) )
	//			)
	//	Return true/false ($do=set), Array (do=read)
	public function full_html_cache_wish( $do = 'read' , $array = null ){
		
		//ID für Infos bei diesem Addon suchen/ erstellen
		$id = $this->get_addon_id( 'fullcache' );
	
		//nur lesen?
		if( $do == 'read' ){
			
			//Daten lesen
			$data = $this->fullcache->read_kimb_id( $id, 'array' );
			
			//keien Daten?
			if( empty( $data ) ){
				//leeres Muster ausgeben (für 'a' aktiv und auf nichts achten)
				return $this->emptyarray;
			}
			else{
				//Daten aus KIMBdbf ausgeben
				return json_decode( $data, true );
			}
		}
		//schreiben?
		elseif( $do == 'set' && is_array( $array ) && count( $array ) > 0 ){
			
			//Array Übergabe prüfen
			//	durchgehen
			foreach ($array as $key => $value) {
				//ID/ Bereich okay?
				if( $key == 'a' || is_numeric( $key ) ){
					
					//Inhalte prüfen
					if(
						//überhaupt Array
						is_array( $value ) &&
						( $value[0] == 'on' || $value[0] == 'off' ) &&
						is_array( $value[1] ) &&
						is_array( $value[1]['GET'] ) &&
						is_array( $value[1]['POST'] ) &&
						is_array( $value[1]['COOKIE'] )
					){
						//okay!! :)	
					}
					//Fehler
					else{
						return false;
					}
					
					
				}
				//Fehler
				else{
					return false;
				}
			}
			
			//wenn hier angekommen Array Übergabe okay!!
			//	Array als JSON in Infodatei ablegen!
			$this->fullcache->write_kimb_id( $id, 'add', 'array', json_encode( $array ) );
			
			//okay
			return true;
			
		}
		//keins? => Fehler
		else{
			return false;
		}
	}


}

?>
