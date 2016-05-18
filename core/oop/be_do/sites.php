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

//Diese Datei beherbergt die Backend Klasse für die Seitenerstellung.

defined('KIMB_CMS') or die('No clean Request');

class BEsites{
	
	//Klasse init.
	protected $allgsysconf, $sitecontent;
	
	public function __construct( $allgsysconf, $sitecontent, $tabelle = true ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		
		if( is_object( $this->sitecontent ) && $tabelle ){
			$this->sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		}
	}
	
	//Seite neu erstellen
	public function make_site_new_dbf( $POST ){
			$allgsysconf = $this->allgsysconf;
			$sitecontent = $this->sitecontent;
			
			//den URL-Placeholder einsetzen
			$POST['inhalt'] = str_replace( $allgsysconf['siteurl'],  '<!--SYS-SITEURL-->', $POST['inhalt'] );
			$POST['footer'] = str_replace( $allgsysconf['siteurl'],  '<!--SYS-SITEURL-->', $POST['footer'] );
			$POST['header'] = str_replace( $allgsysconf['siteurl'],  '<!--SYS-SITEURL-->', $POST['header'] );
		
			//nach einer leeren Seitendatei/ID suchen
			$i=1;
			while( true ){
				if( !check_for_kimb_file( '/site/site_'.$i.'.kimb') && !check_for_kimb_file( '/site/site_'.$i.'_deak.kimb') ){
					break;
				}
				$i++;
			}
	
			//Seitendatei erstellen
			$sitef = new KIMBdbf( '/site/site_'.$i.'.kimb' );
	
			//übergebene Inhalte eintragen
			$sitef->write_kimb_new( 'title' , $POST['title'] );
			$sitef->write_kimb_new( 'header' , $POST['header'] );
			$sitef->write_kimb_new( 'keywords' , $POST['keywords'] );
			$sitef->write_kimb_new( 'description' , $POST['description'] );
			$sitef->write_kimb_new( 'inhalt' , $POST['inhalt'] );
			$sitef->write_kimb_new( 'footer' , $POST['footer'] );
			$sitef->write_kimb_new( 'time' , time() );
			$sitef->write_kimb_new( 'made_user' , $_SESSION['name'] );
					
			//Easy Menue
			$easyfile = new KIMBdbf( 'backend/easy_menue.kimb' );
			
			//überhaupt aktiviert?
			if( $easyfile->read_kimb_one( 'oo' ) == 'on'  ){
				//vom User gewünscht
				if( $POST['menue'] != 'none' ||  $POST['untermenue'] != 'none' ){
					
					//Daten testen
					if(  $POST['menue'] != 'none' ){
						//valide?
						if(  $POST['menue'] == 'first' || is_numeric( $POST['menue'] )  ){
							//erlaubt?
							if( $easyfile->read_kimb_search_teilpl( 'same' , $POST['menue'] )  ){
								$tested = true;
								$file = $POST['menue'];	
							}
						}			
					}
					elseif( $POST['untermenue'] != 'none' ){
						$array = explode( '||', $POST['untermenue'] );
						//valide?
						if( is_numeric( $array[0] ) && ( $array[1] == 'first' || is_numeric( $array[1] ) ) ){
							//erlaubt?
							if( $easyfile->read_kimb_search_teilpl( 'deeper' , $POST['untermenue'] )  ){
								$tested = true;
								$requid = $array[0];
								$file = $array[1];
							}
						}
					}
					
					//Menue machen
					if( $tested ){
						
						//alle Date sammeln/ vorbereiten				
						$GET['file'] = $file;
						if(  $POST['menue'] != 'none' ){
							$GET['niveau'] = 'same';
						}
						elseif( $POST['untermenue'] != 'none' ){
							$GET['niveau'] = 'deeper';
							$GET['requid'] = $requid;
						}
						$POST['name'] = $POST['title'];
						$POST['siteid'] = $i;
						$status = $easyfile->read_kimb_one( 'stat' );
						
						//mit der Backend Menü Klasse Menü erstellen
						$bemenue = new BEmenue( $allgsysconf, $sitecontent );
						
						$return = $bemenue->make_menue_new_dbf( $GET, $POST, $status );
						
						//Untermenu nur einmal, dann als Menü bei Easy Menü
						if( $POST['untermenue'] != 'none' ){
							$easyfile->write_kimb_teilpl( 'deeper' , $POST['untermenue'], 'del');
							
							$easyfile->write_kimb_teilpl( 'same' , $return['file'], 'add');
						}
					}
				}
			}
			//okay, SiteID zurück
			return $i;	
	}
	
	//Seite neu erstellen, HTML
	public function make_site_new(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>Neue Seite</h2>');
	
		//TinyMCE
		add_tiny( true, true);
	
		//Daten übergaben
		if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){
			
			//Seite erstellen
			$i = $this->make_site_new_dbf( $_POST );
			
			//Seite bearbeiten öffnen
			open_url('/kimb-cms-backend/sites.php?todo=edit&id='.$i);
			die;
		}
	
		//Easy Menue
		//Datei lesen
		$easyfile = new KIMBdbf( 'backend/easy_menue.kimb' );
		
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new" method="post"><br />');
		
		//Easy Menue aktiviert
		if( $easyfile->read_kimb_one( 'oo' ) == 'on'  ){
		
			//Menüarray machen
			$menuearray =  make_menue_array_helper();
		
			//Dropdowns starten
			$selectmen = '<select name="menue"> <option value="none" selected="selected"></option>';
			$selectunte .= '<select name="untermenue"> <option value="none" selected="selected"></option>';
		
			//alle Menüpunkte durchgehen
			foreach( $menuearray as $menue ){
				
				//Untermenü möglich?
				if( empty( $menue['nextid'] ) ){
					$deeper = $menue['requid'].'||'.$menue['fileid'];
					
					//erlaubt?
					if( $easyfile->read_kimb_search_teilpl( 'deeper' , $deeper )  ){
						//wenn ja, dann gleich ins Dropdown
						$selectunte .= '<option value="'.$deeper.'">'.$menue['menuname'].'</option>';
						$done['deeper'] = true;
					}
				}
				//URL-Datei das erste mal?
				if( !in_array( $menue['fileid'], $filearr ) ){
					$same = $menue['fileid'];
					
					//Menü erlaubt?
					if( $easyfile->read_kimb_search_teilpl( 'same' , $same)  ){	
						//wenn ja, dann gleich ins Dropdown	
						$selectmen .= '<option value="'.$same.'">'.$menue['menuname'].'</option>';
						$done['same'] = true;
					}
				}
				//alle schon gemachten Dateien speichern
				$filearr[] = $menue['fileid'];
				
			}
			
			//Dropdowns beenden
			$selectmen .= '</select>';
			$selectunte .= '</select>';
			
			//Keine Elemente in den Dropdowns?
			if( !$done['deeper'] ){
				//Fehlermeldung
				$selectunte = '<b>Keine Freigaben</b>';
			}
			if( !$done['same'] ){
				//Fehlermeldung
				$selectmen = '<b>Keine Freigaben</b>';
			}
			
			//Hinweise
			$achtung = '<br />Der Pfad und der Name des Menüs werden auf Basis des Seitentitels erstellt!';
			if( $easyfile->read_kimb_one( 'stat' ) == 'off' ){
				$achtung .= '<br /><i>Achtung, nach der Erstellung muss das neue Menü noch aktiviert werden!</i>!';
			}
			
			//JavaScript für EasyMenue
			$this->jsobject->for_site_new( $selectmen, $selectunte, $achtung );
		}
		
		//Seitenerstellung Formular
		$sitecontent->add_site_content('<input type="text" value="Titel" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
		$sitecontent->add_site_content('<textarea name="header" style="width:74%; height:50px;"></textarea><i>HTML Header </i><br />');
		$sitecontent->add_site_content('<input type="text" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
		$sitecontent->add_site_content('<textarea name="description" style="width:74%; height:50px;"></textarea> <i>Description</i> <br />');
		$sitecontent->add_site_content('<textarea name="inhalt" id="inhalt" style="width:99%; height:300px;">&lt;h1&gt;Titel&lt;/h1&gt;</textarea> <i>Inhalt &uarr;</i><br />');
		$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%; height:75px;"></textarea> <i>Footer &uarr;</i><br />');
		$sitecontent->add_site_content('<input type="submit" value="Erstellen"></form>');	

	}
	
	//Seiten auflisten
	public function make_site_list(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>Liste aller Seiten</h2>');
		
		//JavaScript
		$this->jsobject->for_site_list();
		
		//alle Seiten lesen
		$sites = scan_kimb_dir('site/');

		//Tabelle und Link zum Seite erstellen; Suchbox
		$sitecontent->add_site_content('<span><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new"><span class="ui-icon ui-icon-plus" style="display:inline-block;" title="Eine neue Seite erstellen."></span></a>');
		$sitecontent->add_site_content('<input type="text" class="search" onkeydown="if(event.keyCode == 13){ search(); }" ><button onclick="search();" title="Nach Seitenamen suchen ( genauer Seitenname nötig ).">Suchen</button></span><hr />');
		$sitecontent->add_site_content('<table width="100%"><tr><th width="40px;" >ID</th><th>Name</th><th width="20px;">Status</th><th width="20px;">Löschen</th></tr>');
	
		//IDfile lesen
		$idfile = new KIMBdbf('menue/allids.kimb');
	
		//alle Seiten durchgehen
		foreach ( $sites as $site ){
			//nicht die Sprachdatei?
			if( $site != 'langfile.kimb'){
				//Seitendatei lesen
				$sitef = new KIMBdbf('site/'.$site);
				//SeitenID herausfinden
				$id = preg_replace("/[^0-9]/","", $site);
				//Seitentitel
				$title = $sitef->read_kimb_one('title');
				//Link zum Seite bearbeiten
				$name = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$id.'" title="Seite bearbeiten.">'.$title.'</a>';
				//Seiten Status anzeigen -> Link zum ändern
				if ( strpos( $site , 'deak' ) !== false ){
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-close" title="Diese Seite ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern )"></span></a>';
				}
				else{
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-check" title="Diese Seite ist zu Zeit aktiviert, also sichtbar. ( click -> ändern )"></span></a>';
				}
				//Löschen Button anzeigen
				$del = '<span onclick="delete_site( '.$id.' );"><span class="ui-icon ui-icon-trash" title="Diese Seite löschen."></span></span>';
				//Ist die Seite einem Menü zugeordned?
				$zugeor = $idfile->search_kimb_xxxid( $id , 'siteid' );
				if( $zugeor == false ){
					//nein, dann Hinweis ausgeben					
					$status .= '<span class="ui-icon ui-icon-alert" title="Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!"></span>';
				}
				//Tabellenzeile anzeigen
				$sitecontent->add_site_content('<tr><td>'.$id.'</td><td id="'.$title.'">'.$name.'</td><td>'.$status.'</td><td>'.$del.'</td></tr>');
		
				$liste = 'yes';
			}
		}
		$sitecontent->add_site_content('</table>');
	
		//Hinweis, wenn keine Seiten vorhanden
		if( $liste != 'yes' ){
			$sitecontent->echo_error( 'Es wurden keine Seiten gefunden!' );
		}
	}
	
	//Seiten bearbeiten
	public function make_site_edit(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>Seite bearbeiten</h2>');
		
		//Seite braucht erstmal keinen besodneren Vorschaulink
		$prevtoken = false;
		
		//Wenn Seitendatei nicht geladen, laden dieser
		if( !is_object( $sitef ) ){
			//Seite aktiviert?
			if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
				$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'.kimb' );
			}
			//Seite deaktiviert?
			elseif( check_for_kimb_file( '/site/site_'.$_GET['id'].'_deak.kimb' ) ){
				$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'_deak.kimb' );
				
				$prevtoken = true;
			}
			//Seite nicht vorhanden -> Fehler
			else{
				$sitecontent->echo_error('Die Seite wurde nicht gefunden' , '404');
				$sitecontent->output_complete_site();
				die;
			}
		}
		
		//Sprache
		if( $allgsysconf['lang'] == 'on' && $_GET['langid'] != 0 && is_numeric( $_GET['langid'] ) ){
			//andere Tags wenn nicht Standardsprache
			$dbftag['title'] = 'title-'.$_GET['langid'];
			$dbftag['keywords'] = 'keywords-'.$_GET['langid'];
			$dbftag['description'] = 'description-'.$_GET['langid'];
			$dbftag['inhalt'] = 'inhalt-'.$_GET['langid'];
			$dbftag['footer'] = 'footer-'.$_GET['langid'];
			
			//wurde diese Sprache schon geladen?
			if( empty( $sitef->read_kimb_one( $dbftag['title'] ) ) && empty( $sitef->read_kimb_one( $dbftag['inhalt'] ) ) ){
				//wenn nicht die Sprachdatei vorbereiten
				$sitef->write_kimb_one( $dbftag['title'] , 'Title' );
				$sitef->write_kimb_one( $dbftag['keywords'] , '' );
				$sitef->write_kimb_one( $dbftag['description'] , '' );
				$sitef->write_kimb_one( $dbftag['inhalt'] , '<h1>Inhalt</h1>' );
				$sitef->write_kimb_one( $dbftag['footer'] , '' );				
			}
		}
		else{
			//Standardtags
			$dbftag['title'] = 'title';
			$dbftag['keywords'] = 'keywords';
			$dbftag['description'] = 'description';
			$dbftag['inhalt'] = 'inhalt';
			$dbftag['footer'] = 'footer';
			
			$_GET['langid'] = 0;				
		}
		
		//wenn Sprache aktiviert, Dropdown für Sprachwahl
		if( $allgsysconf['lang'] == 'on'){
			make_lang_dropdown( '"'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&id='.$_GET['id'].'&langid=" + val', $_GET['langid'] );
		}
	
		//JavaScript
		$this->jsobject->for_site_edit();	
	
		//wurden Daten übermittelt?
		if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){
			
			//den URL-Placeholder einsetzen
			$_POST['inhalt'] = str_replace( $allgsysconf['siteurl'],  '<!--SYS-SITEURL-->', $_POST['inhalt'] );
			$_POST['footer'] = str_replace( $allgsysconf['siteurl'],  '<!--SYS-SITEURL-->', $_POST['footer'] );
			$_POST['header'] = str_replace( $allgsysconf['siteurl'],  '<!--SYS-SITEURL-->', $_POST['header'] );
	
			//Daten in die dbf schreiben
			$sitef->write_kimb_replace( $dbftag['title'] , $_POST['title'] );
			$sitef->write_kimb_replace( 'header' , $_POST['header'] );
			$sitef->write_kimb_replace( $dbftag['keywords'] , $_POST['keywords'] );
			$sitef->write_kimb_replace( $dbftag['description'] , $_POST['description'] );
			$sitef->write_kimb_replace( $dbftag['inhalt'] , $_POST['inhalt'] );
			$sitef->write_kimb_replace( $dbftag['footer'] , $_POST['footer'] );
			$sitef->write_kimb_replace( 'time' , time() );
			$sitef->write_kimb_replace( 'made_user' , $_SESSION['name'] );
			if( !empty( $_POST['markdown'] ) ){
				$sitef->write_kimb_one( 'markdown', $_POST['markdown'] );
			}
			
			
			//Seiten Cache leeren gewünscht?
			if( $allgsysconf['cache'] == 'on' && isset( $_POST['delcache'] ) ){
				//Cache OBJ machen
				$ca = new cacheCMS($allgsysconf, $sitecontent);
				//Cache leeren (nur diese Seite und aktuelle Sprache)
				$ca->del_cache_site( $_GET['id'] , $_GET['langid'] );
				//OBJ wieder weg
				unset( $ca );
			}
	
		}
		//Übersetzung löschen gewünscht
		//	auch nicht Standardsprache?
		elseif( isset( $_GET['deletelang'] ) && $_GET['langid'] != 0 ){
			
			//Alle Tags dieser Sprache löschen
			$sitef->write_kimb_delete( $dbftag['title'] );
			$sitef->write_kimb_delete( $dbftag['keywords'] );
			$sitef->write_kimb_delete( $dbftag['description']  );
			$sitef->write_kimb_delete( $dbftag['inhalt'] );
			$sitef->write_kimb_delete( $dbftag['footer'] );

			//Standardsprache öffnen
			open_url('/kimb-cms-backend/sites.php?todo=edit&id='.$_GET['id']);
			die;
		}
		
		//alle Daten lesen
		$seite['title'] = $sitef->read_kimb_one( $dbftag['title'] );
		$seite['header'] = $sitef->read_kimb_one( 'header' );
		$seite['keywords'] = $sitef->read_kimb_one( $dbftag['keywords'] );
		$seite['description'] = $sitef->read_kimb_one( $dbftag['description'] );
		$seite['inhalt'] = $sitef->read_kimb_one( $dbftag['inhalt'] );
		$seite['footer'] = $sitef->read_kimb_one( $dbftag['footer'] );
		$seite['time'] = $sitef->read_kimb_one( 'time' );
		$seite['time'] = date( "d.m.Y \u\m H:i" , $seite['time'] );
		$seite['markdown'] = $sitef->read_kimb_one( 'markdown' );
	
		//Überprüfung ob Seite einen Menüpunkt hat, wenn nicht Hinweis
		$idfile = new KIMBdbf('menue/allids.kimb');
		$id = $idfile->search_kimb_xxxid( $_GET['id'] , 'siteid' );
	
		if( $id == false ){
			$sitecontent->echo_message( 'Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!' );
			$prevtoken = true;
		}
		
		//Editoren laden (Footer, Inhalt)
		// MD beachten
		if(
			( $allgsysconf['markdown'] == 'custom' && $seite['markdown'] == 'on' ) ||
			( $allgsysconf['markdown'] == 'on' )
		){
			$mdhere = true;
		}
		else{
			$mdhere = false;
		}
		add_content_editor( 'inhalt', $mdhere );
		add_content_editor( 'footer', $mdhere );
	
		//Löschen Button
		//	bei Standardsprache ganze Seite
		if( $_GET['langid'] == 0 ){
			$sitecontent->add_site_content('<span onclick="delete_all( '.$_GET['id'].' );"><span class="ui-icon ui-icon-trash" style="display:inline-block;" title="Diese ganze Seite löschen."></span></span>');
		}
		//	sonst nur Überstzung
		else {
			$sitecontent->add_site_content('<span onclick="delete_lang( '.$_GET['id'].', '.$_GET['langid'].' ); "><span class="ui-icon ui-icon-trash" style="display:inline-block;" title="Diese Übersetzung der Seite löschen."></span></span>');
		}
		
		//Vorschau Buttons
		//	Seite normal veröffentlicht?
		//		Link nutzen
		if(  !$prevtoken ){
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$id.'&langid='.$_GET['langid'].'" target="_blank"><span class="ui-icon ui-icon-newwin" style="display:inline-block;" title="Diese Seite anschauen."></span></a>');
		}
		//	Seite noch nicht veröffentlicht?
		else{
			//Seitenvorschaulink machen
			
			//Array in Session erstellen, welches die Vorschaucodes verwaltet
			if( !is_array( $_SESSION['sites_preview'] ) ){
				$_SESSION['sites_preview'] = array();
			}
			//schon ein Vorschaucode für diese Seite im Array?
			if( !isset( $_SESSION['sites_preview'][$_GET['id']] ) || empty( $_SESSION['sites_preview'][$_GET['id']] ) ){
				//Code machen
				$prevcode = makepassw( 20, '', 'numaz' );
				//ins Array
				$_SESSION['sites_preview'][$_GET['id']] = $prevcode;
			}
			else {
				//wenn Code schon erstellt auslesen
				$prevcode = $_SESSION['sites_preview'][$_GET['id']];
			}
			//Hinweise zur Vorschau
			//	Link
			$sitecontent->echo_message( 'Die Vorschau funktioniert nur, wenn der FullHTMLCache deaktiviert oder gerade geleert worden ist!<br />Außderdem sind die Menüs/ Add-ons in der Vorschau wie auf der Startseite.<br /><a href="'.$allgsysconf['siteurl'].'/index.php?siteprev='.$prevcode.'&amp;id=1&langid='.$_GET['langid'].'" target="_blank"><span class="ui-icon ui-icon-newwin" style="display:inline-block;" title="Diese Seite anschauen."></span></a>', 'Vorschau' );
		}
		
		//Eingabefelder
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$_GET['id'].'&amp;langid='.$_GET['langid'].'" method="post"><br />');
		$sitecontent->add_site_content('<input type="text" value="'.$seite['title'].'" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
		$sitecontent->add_site_content('<div style="position:relative;" ><textarea name="header" style="width:74%; height:50px;">'.htmlentities( $seite['header'], ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea><span style="position:absolute; top:0; left: calc( 74% + 16px );"> <i>HTML Header</i>');
			//JavaScript Bibilotheken einfach wählen für den Header
			$sitecontent->add_site_content('<br /> <select id="libs">');
			$sitecontent->add_site_content('<option value=""></option>');
			//alle Bibliotheken aus KlassenArray laden
			$out = new system_output( $allgsysconf );
			foreach( array_keys( $out->jsapicodes ) as $lib ){
				$sitecontent->add_site_content('<option value="'.htmlspecialchars( $lib ).'">'.substr( $lib, 5, ( strlen( $lib ) - 9 )  ).'</option>');	
			}
			unset( $out );
			$sitecontent->add_site_content('</select><span class="ui-icon ui-icon-info" style="display:inline-block;" title="Fügen Sie Ihrer Seite ganz einfach eine JavaScript-Bibilothek hinzu." ></span></span></div>');
		$sitecontent->add_site_content('<input type="text" value="'.$seite['keywords'].'" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
		$sitecontent->add_site_content('<div style="position:relative;" ><textarea name="description" style="width:74%; height:50px;">'.$seite['description'].'</textarea><span style="position:absolute; top:0; left: calc( 74% + 16px );"> <i>Description</i></span></div>');
		$sitecontent->add_site_content('<textarea name="inhalt" id="inhalt" style="width:99%; height:300px;">'.$seite['inhalt'].'</textarea> <i>Inhalt &uarr;</i> <br />');
		$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%; height:75px;">'.$seite['footer'].'</textarea> <i>Footer &uarr;</i> <br />');
		$sitecontent->add_site_content('<input type="text" readonly="readonly" value="'.$seite['time'].'" name="time" style="width:74%;"> <i>Zuletzt geändert</i><br />');
		//MD seitenspezifisch?
		if( $allgsysconf['markdown'] == 'custom' ){
			//erstmal nichts gewählt
			$md = array( 'on' => '', 'off' => '' );
			//on oder off gewählt
			if( $seite['markdown'] == 'on' || $seite['markdown'] == 'off' ){
				//passend auf checked setzen
				$md[$seite['markdown']] = ' checked="checked"';
			}
			//Auswahlbuttons ausgeben
			$sitecontent->add_site_content('<input type="radio" value="on" name="markdown"'.$md['on'].'>Aktiviert <input type="radio" value="off" name="markdown" '.$md['off'].'> Deaktiviert <i>Den Inhalt und den Footer dieser Seite als Markdown rendern?</i><br />');
		}
		//Seiten Cache leeren automatisch ermöglichen
		if( $allgsysconf['cache'] == 'on' ){
			$sitecontent->add_site_content('<input type="checkbox" value="on" name="delcache" checked="checked"> Cache dieser Seite leeren?<br />');
		}
		$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');
		$sitecontent->echo_message('<p>Wenn Sie auf eine Seite verweisen und dabei auf Nummer sicher gehen wollen, dass die Links auch bei einer Veränderung der Menüpfade noch gültig sind, können Sie für den Link den Platzhalter "<b>&lt;!--URLoutofID=123--&gt;</b>" verwenden. Setzen Sie für "123" einfach die RequestID<b title="Die RequestIDs finden Sie in der Tabelle unter Menue -> Auflisten">*</b> der Seite/ des Menüpunktes ein und der Rest erfolgt automatisch.</p>', 'Tipp');
		
	}
	
	//Seite Status ändern
	public function make_site_deakch( $id ){
		//Seite aktiviert?
		if( check_for_kimb_file( '/site/site_'.$id.'.kimb' ) ){
			//deaktivieren
			rename_kimbdbf( '/site/site_'.$id.'.kimb' , '/site/site_'.$id.'_deak.kimb' );
		}
		//Seite deaktiviert?
		elseif( !check_for_kimb_file('/site/site_'.$id.'.kimb') && check_for_kimb_file( '/site/site_'.$id.'_deak.kimb' )  ){
			//aktivieren
			rename_kimbdbf( '/site/site_'.$id.'_deak.kimb' , '/site/site_'.$id.'.kimb' );
		}
		else{
			return false;
		}
		
		return true;
	}
	
	//Seite löschen
	public function make_site_del( $id ){
		//wenn vorhanden, dann löschen
		if( check_for_kimb_file( '/site/site_'.$id.'.kimb' ) ){
			return delete_kimb_datei( '/site/site_'.$id.'.kimb' );				
		}
		return false;
	}
}
?>
