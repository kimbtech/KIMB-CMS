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

//Diese Datei beherbergt die Backend Klasse für die Add-on Konfiguration.
//Hier werden die Add-on Dateien conf_more.php & conf_less.php verwendet.

defined('KIMB_CMS') or die('No clean Request');

class BEaddconf{
	
	//Klasse init	
	protected $allgsysconf, $sitecontent;
	
	public function __construct( $allgsysconf, $sitecontent ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
	}
	
	//Add-on Namen aus NameID herausfinden
	//	Jedes Add-on hat eine add-on.ini in seinem Verzeichnis mit allen wichtigen Informationen über das Add-on
	public function get_addon_name( $addon ){ 
		//ist das Add-on (die INI-Datei) vorhanden?
		if( is_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' ) ){
	
			//INI Datei parsen und Namen herausfinden
			$ini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);
			return $ini['about']['name'];
		}
		else{
			return $addon;
		}
	} 
	
	//Ist die BE Conf Datei des Add-ons okay?
	protected function be_conffile_okay( $addon, $way ){
		
		//Hat das Add-on überhaupt diese Konfigurationsoberfläche?
		if( is_file( __DIR__.'/../../addons/'.$addon.'/conf_'.$way.'.php' ) ){
				
			//BE Conf Datei Zeilenanzahl prüfen (bei alten Add-ons fehlt die Datei nicht)
			if( count( file( __DIR__.'/../../addons/'.$addon.'/conf_'.$way.'.php' ) ) > 35 ){
				//Datei okay
				return true;
			}
			
		}
		
		//Datei nicht okay
		return false;
	}
	
	//Liste der Add-ons erstellen
	//	Rückgabe => Tabelle
	//	$way => more, less (je nach Zugriffsrecht)
	public function make_addon_list( $way ){
		//in der Methode normal auf den Seiteninhalt zugreifen
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		//Tabellendesign
		$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		
		//Tabelle mit den Links und der Add-on NameID erstellen 
		$ret = '<table width="100%"><tr> <th>Add-on</th> </tr>';

		//Alle installierten Add-ons durchgehen
		$addons = listaddons();
		foreach( $addons as $addon ){
			
			//BE Conf okay?
			//Rechte des Users okay?
			if( $this->be_conffile_okay( $addon, $way ) && check_backend_permission( 'a', $addon ) ){

				$ret .= '<tr>';
				$ret .= '<td>';
				$ret .= '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo='.$way.'&amp;addon='.$addon.'">'.$this->get_addon_name( $addon ).'</a>';
				$ret .= '</td>';
				$ret .= '</tr>';

				$liste = 'yes';
			}

		}
		$ret .= '</table>';

		//wenn Tabelle leer, wohl nichts installiert
		if( $liste != 'yes' ){
			$ret =  'Es wurden keine Add-ons gefunden!' ;
		}
		
		return $ret;
	}
	
	//Konfiguration less laden oder Liste zeigen
	public function make_less(){
		//in der Methode normal auf den Seiteninhalt & die Systemkonfiguration zugreifen
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		//wenn kein Add-on gewähle Liste zeigen
		if( isset( $_GET['addon'] ) ){
			//Konfigurationsdialog
			
			//Rechte okay?
			if( check_backend_permission( 'a', $_GET['addon'] ) ) {
				
				$addonname = $this->get_addon_name( $_GET['addon'] );
		
				$sitecontent->add_site_content('<h2>Add-on "'.$addonname.'" nutzen</h2>');
				$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less">&larr; Alle Add-ons</a>');
				
				if(
					//wenn auch Rechte für more, Link dorthin zeigen
					check_backend_login( 'fourteen' , 'more', false) &&
					//Datei more überhaupt vorhanden?
					$this->be_conffile_okay( $_GET['addon'], 'more' )
				){
					$sitecontent->add_site_content('<a style="position:absolute; right:12px;" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon='.$_GET['addon'].'">Zur Add-on Konfiguration &rarr;</a>');
				}
				$sitecontent->add_site_content('<hr />');
				
				//Meldung wenn Add-on nicht aktivert
				if( check_addon( $_GET['addon'] ) == array( true, false ) ){
					$sitecontent->echo_error( 'Dieses Add-on ist aktuell nicht aktivert!', 'unknown', 'Add-on nicht aktiv' );
				}
		
				//Add-on Konfigurationsdatei laden
				if( file_exists(__DIR__.'/../../addons/'.$_GET['addon'].'/conf_less.php') ){
					require_once( __DIR__.'/../../addons/'.$_GET['addon'].'/conf_less.php' );
				}
				else{
					$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
				}
			}
			else{
				$sitecontent->echo_error( 'Sie haben keine Rechte um auf dieses Add-ons zuzugreifen!' , 'unknown');	
			}
		}
		else{
			//Add-on Liste
			$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
			$sitecontent->add_site_content( $this->make_addon_list( 'less' ) );
		}	
		
		return;
	}
	
	//Konfiguration more laden oder Liste zeigen
	public function make_more(){
		//in der Methode normal auf den Seiteninhalt & die Systemkonfiguration zugreifen
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		//wenn kein Add-on gewähle Liste zeigen
		if( isset( $_GET['addon'] ) ){
			//Konfigurationsdialog
			
			//Rechte okay?
			if( check_backend_permission( 'a', $_GET['addon'] ) ) {
			
				$addonname = $this->get_addon_name( $_GET['addon'] );

				$sitecontent->add_site_content('<h2>Add-on "'.$addonname.'" konfigurieren</h2>');
				$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more">&larr; Alle Add-ons</a>');
				
				if(
					//wenn auch Rechte für less, Link dorthin zeigen
					check_backend_login( 'thirteen' , 'less', false) &&
					//Datei more überhaupt vorhanden?
					$this->be_conffile_okay( $_GET['addon'], 'less' )
				){
					$sitecontent->add_site_content('<a style="position:absolute; right:12px;" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon='.$_GET['addon'].'">Zur Add-on Nutzung &rarr;</a>');
				}
				
				$sitecontent->add_site_content('<hr />');
		
				//Meldung wenn Add-on nicht aktivert
				if( check_addon( $_GET['addon'] ) == array( true, false ) ){
					$sitecontent->echo_error( 'Dieses Add-on ist aktuell nicht aktivert!', 'unknown', 'Add-on nicht aktiv' );
				}
		
				//Add-on Konfigurationsdatei laden
				if( file_exists(__DIR__.'/../../addons/'.$_GET['addon'].'/conf_more.php') ){
					require_once( __DIR__.'/../../addons/'.$_GET['addon'].'/conf_more.php' );
				}
				else{
					$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
				}
			}
			else{
				$sitecontent->echo_error( 'Sie haben keine Rechte um auf dieses Add-ons zuzugreifen!' , 'unknown');	
			}
		}
		else{
			//Add-on Liste
			$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
			$sitecontent->add_site_content( $this->make_addon_list( 'more' ) );
		}
		
		return;
	}
		
}
?>
