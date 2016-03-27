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

//Diese Klasse ist die zentrale Ausgabeklasse des CMS Frontends.
//Alle Ausgaben werden hier zusammengefasst und zum Ende dem Theme übergeben
//Die Klasse ist im Frontend immer als $sitecontent verfügbar!

class system_output{

	//Klasse init
	protected $title, $header, $allgsysconf, $menue, $sitecontent, $addon, $footer, $sonderfile, $hidden_menu;
	public $jsapicodes = array();
	
	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->footer = $this->sonderfile->read_kimb_one('footer')."\r\n";
	
		 //einfügen von JavaScript Code für Platzhalter	
		$this->jsapicodes = array(	
			'<!-- jQuery -->' => '<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>',
			'<!-- jQuery UI -->' => '<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.css" >'."\r\n".'<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.js"></script>',
			'<!-- TinyMCE -->' => '<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/tinymce/tinymce.min.js"></script>',
			'<!-- Hash -->' => '<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/hash.js"></script>',
			'<!-- CodeMirror -->' => '<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/codemirror/codemirrorloader.min.js"></script>',
			'<!-- FontAwesome -->' => '<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/fontawesome/font-awesome.min.css">',
			'<!-- PrismJS -->' => '<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/prism/prism.min.css">'."\r\n".'<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/prism/prism.min.js"></script>'
		);
	}

	//Menüeinträge hinzufügen
	public function add_menue_one_entry($name, $link, $niveau, $clicked , $allgrequestid){
		
		//Canonical Tag wenn aktuelle URL
		if( $clicked == 'yes' ){
			$this->add_html_header( '<link rel="canonical" href="'.$link.'">' );
		}
		
		//nur nicht verborgene Menüs anzeigen
		if( !in_array( $allgrequestid, $this->hidden_menu  ) ){
		
			//Die Menüerstellung erfolgt durch eine Datei des Themes
			
			//nach Theme gucken, Fallback auf Standard
			if( isset( $this->allgsysconf['theme'] ) ){
				if( file_exists( __DIR__.'/../theme/output_menue_'.$this->allgsysconf['theme'].'.php' ) ){
					require(__DIR__.'/../theme/output_menue_'.$this->allgsysconf['theme'].'.php');
				}
				else{
					require(__DIR__.'/../theme/output_menue_norm.php');
				}
			}
			else{
				require(__DIR__.'/../theme/output_menue_norm.php');
			}
		}
	}

	//Seiteninahlt hinzufügen
	//	Übersetzung für Siteinfos
	public function add_site($content, $trans){
		//Title setzen
		$this->set_title($content['title']);
		
		//Header hinzufügen
		$this->add_html_header($content['header']);
		
		//Description und Keywords setzen, wenn nicht leer
		//	Die Description einer Seite überschreibt die Description des CMS.
		if( !empty( $content['description'] ) ){
			$this->allgsysconf['description'] = $content['description'];
		}
		if( !empty( $content['keywords'] ) ){
			$this->add_html_header('<meta name="keywords" content="'.$content['keywords'].'">');
		}
		//Seiteninhalt und Footer
		$html = $content['inhalt'];
		$this->add_footer($content['footer']);
		
		//wenn siteinfos aktiviert hinzufügen
		if( $this->allgsysconf['show_siteinfos'] == 'on' ){
			
			//Permalink über Domain des CMS laufen lassen (einfacher ID Zugriff)
			if( $this->allgsysconf['permalink_domain'] == 'off' ){
				$permaurl = $this->allgsysconf['siteurl'].'/index.php?id='.$content['req_id'];
			}
			else{
				//Permalink über andere Domain laufen lassen (Weiterleitung auf ID-Zugriff per externem Skript nötig)
				$permaurl = $this->allgsysconf['permalink_domain'].$content['req_id'];
			}
			
			$time = date( "d.m.Y" , $content['time'] );
			$schlusszeile .= '<div id="usertime">'.$trans['estv'].' '.$content['made_user'].' '.$trans['am'].' '.$time.'</div>';
			$schlusszeile .= '<div id="permalink">'.$trans['perma'].': <a href="'.$permaurl.'">'.$permaurl.'</a></div>';
			$html .= $schlusszeile."\r\n";
		}
		
		//Seiteninhalt anfügen
		$this->add_site_content( $html );
	}
	
	//Sprachinformationen für das Theme annehmen und speichern
	public function set_lang( $allglangs, $requestlang ){
		$this->allglangs = $allglangs;
		$this->requestlang = $requestlang; 
	}

	//Seiteninhalt hinzufügen
	public function add_site_content($content){
		$this->sitecontent .= $content."\r\n";
	}

	//Add-on Area hinzufügen
	public function add_addon_area($inhalt, $style = '', $cssclass = ''){
		$this->addon .= '<div id="apps" class="'.$cssclass.'" style="'.$style.'">'."\r\n";
		$this->addon .= $inhalt."\r\n";
		$this->addon .= '</div>'."\r\n";
	}

	//Footer hinzufügen
	public function add_footer($inhalt){
		$this->footer .= $inhalt."\r\n";
	}

	//HTML Header hinzufügen
	public function add_html_header($inhalt){
		$this->header .= $inhalt."\r\n";
	}

	//Seitentitel setzen
	public function set_title($title){
		$this->title = $title;
	}

	//Fehlermeldung ausgeben
	public function echo_error($message = '', $art = 'unknown', $heading =  'Error' ){
		//Fehler 404 mit Vorgabetext und eigenem und HTTP Header
		if( $art == '404' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-404');
			$this->sitecontent .= '<div id="errorbox"><h1>Error - 404</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i></div>'."\r\n";
			header("HTTP/1.0 404 Not Found");
			$this->set_title( 'Error - 404' );

		}
		//Fehler 403 mit Vorgabetext und eigenem und HTTP Header
		elseif( $art == '403' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-403');
			$this->sitecontent .= '<div id="errorbox"><h1>Error - 403</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i></div>'."\r\n";
			header('HTTP/1.0 403 Forbidden');
			$this->set_title( 'Error - 403' );
		}
		//andere Fehler ohne Vorgabetext
		//	evtl. eigene Überschrift
		else{
			$this->sitecontent .= '<div id="errorbox"><h1>'.$heading.'</h1>'.$message.'</div>'."\r\n";
			$this->set_title( 'Error' );
		}

	}

	//bestimmte Menüpunkte ausbleden
	//	Array oder einzelne INT (RequestIDs) übergeben, Menüpunkt werden dann ausgeblendet
	public function hide_menu( $array ){
		//alle IDs in Array speichern
		//Überprüfung ob ID im Array beim Hinzufügen des Menüpunktes
		if( !is_array() ){
			$array = (array) $array;
		}
		foreach( $array as $val ){
			if( $val != 1){
				$this->hidden_menu[] = $val;
			}	
		}
	}

	//abschließende Ausgabe der Seite
	public function output_complete_site( $allgsys_trans ){

		//jede JS Datei soll nur einmal dabei sein
		//	jQuery-UI benötigt jQuery
		$add = '';
		$dones = array();
		//alle JavaScript Platzhalter durchgehen
		foreach( $this->jsapicodes as $key => $code ){
			//wenn Platzhalter gefunden 
			if( strpos( $this->header , $key ) !== false ){
				//im Array $dones werden alle schon hinzugefügten abgelegt
				
				//der Loader für CodeMirror braucht die GrundURL des Systems
				if( $key == '<!-- CodeMirror -->' ){
					$add .= '<script>var codemirrorloader_siteurl = "'.$this->allgsysconf['siteurl'].'", codemirrorloader_done = false;</script>'."\r\n";
				
					//CodeMirror braucht auch jQuery
					//	wenn nicht geladen, dann hinzufügen
					if(  !in_array( '<!-- jQuery -->', $dones ) ){
						$add .= $this->jsapicodes['<!-- jQuery -->']."\r\n";
					}
				}
				
				//bei jQuery UI überprüfen ob jQuery schon geladen, wenn nicht beides hinzufügen
				if( $key == '<!-- jQuery UI -->' && !in_array( '<!-- jQuery -->', $dones ) && !in_array( '<!-- jQuery UI -->', $dones ) ){
					
					//beide Scripte hinzufügen
					$add .= $this->jsapicodes['<!-- jQuery -->']."\r\n";
					$add .= $code."\r\n";

					//beide als erledigt speichern
					$dones[] = '<!-- jQuery -->';
					$dones[] = '<!-- jQuery UI -->';
				}
				//alle anderen einfach einfügen und als erledigt markieren
				elseif( !in_array( $key, $dones ) ){
					$add .= $code."\r\n";
					$dones[] = $key;
				}
			}
		}

		//JavaScript dem Header anfügen
		$this->header = $add.$this->header;
		
		//alle Bibilothek Keywords entfernen
		$this->header = str_replace( array_keys( $this->jsapicodes ), '', $this->header );
		
		//den URL-Placeholder duch die aktuelle URL ersetzen
		$this->header = str_replace( '<!--SYS-SITEURL-->', $this->allgsysconf['siteurl'], $this->header );
		$this->footer = str_replace( '<!--SYS-SITEURL-->', $this->allgsysconf['siteurl'], $this->footer );
		$this->sitecontent = str_replace( '<!--SYS-SITEURL-->', $this->allgsysconf['siteurl'], $this->sitecontent );
		$this->addon = str_replace( '<!--SYS-SITEURL-->', $this->allgsysconf['siteurl'], $this->addon );
		//	auch bei den Sprachflaggen
		foreach( $this->allglangs as $key => $value ){
			$this->allglangs[$key]['flag'] = str_replace( '<!--SYS-SITEURL-->', $this->allgsysconf['siteurl'], $value['flag'] );
		}
		
		//URL aus Platzhalter für URL machen (<!--URLoutofID=1-->)
		$this->sitecontent = replace_urloutofid( $this->sitecontent );
		$this->footer = replace_urloutofid( $this->footer );
		$this->addon = replace_urloutofid( $this->addon );
		$this->header = replace_urloutofid( $this->header );

		//alles dem Theme übergeben
		//	wenn Theme nicht gefunden Fallback auf Standard
		if( isset( $this->allgsysconf['theme'] ) ){
			if( file_exists( __DIR__.'/../theme/output_site_'.$this->allgsysconf['theme'].'.php' ) ){
				require_once(__DIR__.'/../theme/output_site_'.$this->allgsysconf['theme'].'.php');
			}
			else{
				require_once(__DIR__.'/../theme/output_site_norm.php');
			}
		}
		else{
			require_once(__DIR__.'/../theme/output_site_norm.php');
		}
	}


}

?>
