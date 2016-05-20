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

//Diese Datei beherbergt die Backend Klasse für die Menüerstellung.

class BEmenue{
	
	//Klasse init
	protected $allgsysconf, $sitecontent, $idfile, $menuenames;
	
	public function __construct( $allgsysconf, $sitecontent, $tabelle = true ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		
		//wichtige KIMBdbf laden
		$this->idfile = new KIMBdbf('menue/allids.kimb');
		$this->menuenames = new KIMBdbf('menue/menue_names.kimb');
		if( is_object( $this->sitecontent ) && $tabelle ){
			//CSS für Tabellen im BE, bei ajax.php nicht nötig
			$this->sitecontent->add_html_header('<style>td:not( [class="nobor"] ) { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		}
	}
	
	public function make_menue_new_dbf( $GET, $POST, $status = 'on' ){
		//erstellt neue Menues
		//benötigt
		//	$GET['file'], $GET['niveau'], $GET['requid']
		//	$POST['name'], $POST['pfad'] (kann leer sein, dann aus Name),$POST['siteid'] (kann leer sein, dann keine Seite)
		//	[ $status ]
		//Rückgabe
		//	Array $newm ( name, pfad, nextid, requestid,status,siteid,menueid )
		
				$allgsysconf = $this->allgsysconf;
				$sitecontent = $this->sitecontent;
				$idfile = $this->idfile;
				$menuenames = $this->menuenames;
			
				//Alle übergebenen Werte überprüfen und säubern
				$newm['name'] = $POST['name'];
				//den Pfad wenn leer aus Namen erstellen
				if( $POST['pfad'] == '' ){
					$prepfad = $POST['name'];
					$prepfad = trim( $prepfad );
					$prepfad = mb_strtolower( $prepfad );
					$prepfad = str_replace(
							array( ' ', 'ä', 'ö', 'ü', 'ß' ),
							array( '-', 'ae', 'oe', 'ue', 'ss' ),
							$prepfad
						);
					$POST['pfad'] = $prepfad;
				}
				
				//Pfad unpassende Zeichen raus
				$newm['pfad'] = preg_replace("/[^0-9A-Za-z_.-]/","", $POST['pfad']);
				
				//noch keine NextID
				$newm['nextid'] = '---empty---';
				//RequestID feststellen (erste leere ID in der IDfile)
				$newm['requestid'] = $idfile->next_kimb_id();
				if( isset( $POST['siteid'] ) && ( is_numeric( $POST['siteid'] ) || $POST['siteid'] == 'none' ) ){
					$newm['siteid'] = $POST['siteid'];
				}
				else{
					$newm['siteid'] = '---empty---';
				}
				//MenüID erstellen
				$newm['menueid'] = $newm['requestid'].gen_zufallszahl(10, 99);
				//Status wie gewünscht einstellen
				$newm['status'] = $status;
	
				//richitge URL-Datei öffnen
				if( $GET['file'] == 'first' ){
					$file = new KIMBdbf( 'url/first.kimb' );
				}
				else{
					$file = new KIMBdbf( 'url/nextid_'.$GET['file'].'.kimb' );
				}
	
				//Untermenü?
				if( $GET['niveau'] == 'deeper' && is_numeric( $GET['requid'] ) ){
					//freie NextID suchen
					$i = 1;
					while( true ){
						if( !check_for_kimb_file( 'url/nextid_'.$i.'.kimb' ) ){
							break;
						}
						$i++;
					}
	
					//Übergestellten Menüpunkt des neuen Untermenüs feststellen				
					$oldid = $file->search_kimb_xxxid( $GET['requid'] , 'requestid');
					if( $oldid == false ){
						//gibt es nicht -> Fehler
						$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
						$sitecontent->output_complete_site();
						die;
					}
					//Übergestellter Menüpunkt sollte bisher kein Untermenü haben 
					if( empty( $file->read_kimb_id( $oldid , 'nextid' ) ) ){
						//hat er nicht -> NextID eintragen
						$file->write_kimb_id( $oldid , 'add' , 'nextid' , $i );
					}
					else{
						//hat er -> Fehler
						$newm['file'] = 'error';
						$newm['requestid'] = '';
						return $newm;
					}
	
					//Datei des neuen Untermenüs laden
					$file = new KIMBdbf( 'url/nextid_'.$i.'.kimb' );
					
					//Daten anpassen
					$GET['file'] = $i;
					$newm['file'] = $i;
	
				}
				elseif( $GET['niveau'] == 'deeper' ) {
					//Untermenü erstellen benötigt eine RequestID ohne -> Fehler
					$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
					$sitecontent->output_complete_site();
					die;
				}
				//ID des Menüpunktes in der URL-Datei feststellen
				$id = $file->next_kimb_id();
	
				//Pfad auf vorhandensein prüfen
				//	wenn schon vergeben -> hinten Index ansetzen
				$pfad = $newm['pfad'];
				$i = 1;
				while( $file->search_kimb_xxxid( $pfad , 'path') != false){
					$pfad = $newm['pfad'].$i;
					$i++;
				}
				$newm['pfad'] = $pfad;
	
				//URL-Datei mit Inhalten füllen
				$file->write_kimb_id( $id , 'add' , 'path' , $newm['pfad'] );
				$file->write_kimb_id( $id , 'add' , 'nextid' , $newm['nextid'] );
				$file->write_kimb_id( $id , 'add' , 'requestid' , $newm['requestid'] );
				$file->write_kimb_id( $id , 'add' , 'status' , $newm['status'] );
				//IDfile mit den neuen IDs füllen
				$idfile->write_kimb_id( $newm['requestid'] , 'add' , 'siteid' , $newm['siteid'] );
				$idfile->write_kimb_id( $newm['requestid'] , 'add' , 'menueid' , $newm['menueid'] );
				//den Menünamen notieren
				$menuenames->write_kimb_new( $newm['requestid'] , $newm['name'] );
				
				if( empty( $newm['file'] ) ){
					//NextID in Rückgabe setzen wenn nötig
					$newm['file'] = $GET['file'];
				}
				
				//alle Werte zurückgeben
				return $newm;
	}
	

	public function make_menue_new(){
		//Zeigt dem User den Dialog zum Erstellen neuer Menues an
		
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$idfile = $this->idfile;
		$menuenames = $this->menuenames;
		
		$sitecontent->add_site_content('<h2>Ein neues Menü erstellen</h2>');

		//Überprüfung des Aufrufs (Wahl der Stelle durch Link aus Menüliste)
		if( ( is_numeric( $_GET['file'] ) || $_GET['file'] == 'first' )  && ( $_GET['niveau'] == 'same' || $_GET['niveau'] == 'deeper' ) && ( is_numeric( $_GET['requid'] ) || !isset( $_GET['requid'] ) || $_GET['requid'] == '' ) ){
	
			//Daten zur Menüerstellung übertragen?
			if( !empty( $_POST['name'] ) ){
				
				//alle vorbereiten
				$GET = $_GET;
				$POST = $_POST;
				
				//neues Menü erstellen
				$newm = $this->make_menue_new_dbf( $GET, $POST );
	
				//Menü bearbeiten aufrufen
				open_url('/kimb-cms-backend/menue.php?todo=edit&file='.$newm['file'].'&reqid='.$newm['requestid']);
				die;
			}
	
			//JavaScript
			$this->jsobject->for_menue_new();
	
			//Erstellung eines Dropdowns mit allen Seiten
			$sitedr = '<select name="siteid"><option value="none" selected="selected">None</option>';
			$sites = list_sites_array();
			foreach ( $sites as $site ){
				$sitedr .= '<option value="'.$site['id'].'">'.$site['site'].' - '.$site['id'].'</option>';
			}
			$sitedr .= '</select>';
	
			//Eingabeformular
			$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$_GET['file'].'&amp;niveau='.$_GET['niveau'].'&amp;requid='.$_GET['requid'].'" method="post" onsubmit="return checksubmit();">');
			$sitecontent->add_site_content('<input type="text" name="name" onkeyup=" makepfad(); " onchange=" makepfad(); " > <i title="Pflichtfeld">(Menuename *)</i><br />');
			$sitecontent->add_site_content('<input type="text" name="pfad" onkeyup="checkpath();" > <i title="Manuell oder automatisch aus Menuename" id="pfad">(Pfad)</i><br />');
			$sitecontent->add_site_content($sitedr.' <i title="Auch später über Zuordnung zu definieren">(SiteID)</i><br />');
			$sitecontent->add_site_content('<input type="submit" value="Erstellen" ><br />');
			$sitecontent->add_site_content('<input type="hidden" value="ok" name="check">');
			$sitecontent->add_site_content('</form>');
	
		}
		else{
			//wenn Aufruf ohne Wahl einer Stelle Hinweis -> Link zu erstellen eines Menüs auf Grundebene (www.cms.com/)
			$sitecontent->echo_message( 'Bitte wählen Sie zuerste eine Stelle über Menue -> Anpassen! <br />(Rechts in der Spalte Neu)<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list"><button>Los geht&apos;s!</button></a>' );
			$sitecontent->add_site_content('<br /><br />');
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&file=first&niveau=same"><button>ODER direkt neues Menue auf Grundebene erstellen!</button></a>');
		}
	}
	
	public function make_menue_connect(){
			//Zeigt dem User den Dialog zum Verbinden von Menues mit Seiten
		
			$allgsysconf = $this->allgsysconf;
			$sitecontent = $this->sitecontent;
			$idfile = $this->idfile;
			$menuenames = $this->menuenames;
		
			//wurden Daten zur Eintragung gesendet?
			if( $_POST['post'] == 'post' ){
				//Daten nacheinader durchgehen 
				//	Post Werte beginned ab 0 nummeriert
				//		[Nummer] => RequestID
				//		[Nummer-site] => SiteID
				$i = 0;
				while( true ){
					//verändert?
					if( $_POST[$i.'-site'] != $idfile->read_kimb_id( $_POST[$i] , 'siteid' ) ){
						//Eintragung erfolgreich?
						if( $idfile->write_kimb_id( $_POST[$i] , 'add' , 'siteid' , $_POST[$i.'-site'] ) ){
							//Hinweis
							$sitecontent->echo_message( 'Die Seite '.$_POST[$i.'-site'].' wurde einem Menue zugeordnet!<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$_POST[$i].'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Die Seite mit Menue aufrufen."></span></a>' );
						}
					}
					//alle bearbeitet?
					if( $_POST[$i] == '' ){
						//raus
						break;
					}
					$i++;
				}
			}
			
			//Seitenarray
			$allsites = list_sites_array();
		
			$sitecontent->add_site_content('<h2>Ein Menü einer Seite zuordnen</h2>');
			
			//Menünavigation
			$mennav = $this->menue_navigation( 'connect' );
			$array_start = $mennav[0];
			$array_deeper =  $mennav[1];
			$array_deeper_str  = $mennav[2];
			$array_deeper_str_geg  = $mennav[3];
			
			//IDfile neu laden für make_menue_array_helper(); 
			unset( $idfile );
			$this->idfile = new KIMBdbf('menue/allids.kimb');
			$idfile = $this->idfile;
			//Menue Array machen
			$menuearray = make_menue_array_helper( $array_start, $array_deeper );
					
			//Tabelle beginnen
			$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect&amp;start='.$array_start.'&amp;deeper='.$array_deeper_str.'">');
			$sitecontent->add_site_content('<table width="100%"><tr> <th width="50px;"></th> <th>MenueName</th> <th>Status</th> <th>SiteID <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Wählen Sie bitte für jeden Menuepunkt eine Seite!"></span></th> </tr>');
			//Nummerierung
			$i = 0;
			//jeden Menüpunkt der Tabelle anfügen
			foreach( $menuearray as $menuear ){
		
				//Niveau verdeutlichen
				$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
		
				//Status mit Link zum ändern
				if ( $menuear['status'] == 'off' ){
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" style="display:inline-block;" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
				}
				else{
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" style="display:inline-block;" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
				}
		
				//Dropdown für jeden Menüpunk neu (checked)
				$sitedr = '';
				$sel = 'no';
				foreach( $allsites as $alls ){
					if( $alls['id'] == $menuear['siteid'] ){
						$sitedr .= '<option value="'.$alls['id'].'" selected="selected">'.$alls['site'].' - '.$alls['id'].'</option>';
						$sel = 'yes';
					}
					else{
						$sitedr .= '<option value="'.$alls['id'].'">'.$alls['site'].' - '.$alls['id'].'</option>';
					}
				}
				$sitedr .= '</select>';
				//none wählen wenn keine Seite verbunden
				if( $sel == 'yes' ){
					$sitedr = '<select name="'.$i.'-site"><option value="none" >None</option>'.$sitedr;
				}
				else{
					$sitedr = '<select name="'.$i.'-site"><option value="none" selected="selected">None</option>'.$sitedr;
				}
				
				//Ausgabe der Tabellenzeile
				$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td>  <td>'.$menuear['menuname'].'</td> <td>'.$status.'</td> <td>'.$sitedr.'<input type="hidden" value="'.$menuear['requid'].'" name="'.$i.'"></td> </tr>');
				//Nummerierung
				$i++;
				
				//Untermenü öffnen (wenn Liste ohne Untermenüs gewählt)
				if( !$array_deeper ){
					if( !empty( $menuear['nextid'] ) ){
						$sitecontent->add_site_content('<tr> <td class="nobor"></td> <td colspan="2" class="nobor"><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect&amp;start='.$menuear['nextid'].'&amp;deeper=false" title="Das hier gelegene Untermenü anzeigen."><span class="ui-icon ui-icon-arrowreturnthick-1-e" style="display:inline-block;"></span> Untermenü</a></td> </tr>');
					}	
				}
		
				$liste = 'yes';
			}
			$sitecontent->add_site_content('</table>');
		
			//wenn leer Hinweis anzeigen
			if( $liste != 'yes' ){
				$sitecontent->echo_error( 'Es wurden keine Menues gefunden!' );
			}
		
			//Form absenden Button
			$sitecontent->add_site_content('<input type="hidden" value="post" name="post"><input type="submit" value="Zuordnungen ändern"></form>');	
	}
	
	//Menü Liste erstellen
	public function make_menue_list(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$idfile = $this->idfile;
		$menuenames = $this->menuenames;
		
		$sitecontent->add_site_content('<h2>Menüs auflisten</h2>');

		//JavaScript
		$this->jsobject->for_menue_list();
	
		//Menünavigation
		$mennav = $this->menue_navigation( 'list' );
		$array_start = $mennav[0];
		$array_deeper =  $mennav[1];
		$array_deeper_str  = $mennav[2];
		$array_deeper_str_geg  = $mennav[3];
	
		//IDfile neu laden für make_menue_array_helper();
		unset( $idfile );
		$this->idfile = new KIMBdbf('menue/allids.kimb');
		$idfile = $this->idfile;
		$menuearray = make_menue_array_helper( $array_start, $array_deeper );
		
		//Tabelle beginnen
		$sitecontent->add_site_content('<table width="100%"><tr> <th title="Jedes Menü hat eine Tiefe, ein Niveau. (ein ==> ist eine Tiefe tiefer) ">Niveau<span class="ui-icon ui-icon-info" title="Relativ zum ersten Menüpunkt dieser Ansicht, nicht zum Hauptmenü!" style="display:inline-block;"></small></th> <th></th> <th title="Dieser Name wird Besuchern im Frontend angezeigt">Menue Name</th> <th title="Pfad-Teil des Menues für URL-Rewriting">Pfad</th> <th title="ID für Aufruf /index.php?id=XXX">RequestID</th> <th>Status</th> <th title="ID der zugeordnenten Seite">SiteID</th> <th title="ID des Menüs ( Systemintern )">MenueID</th> <th>Löschen</th> <th>Neu</th> </tr>');
		
		//Link zum direkten Bearbeiten? 
		//	nur wenn Rechte dazu
		$dis_sitelink = check_backend_login( 'three', 'less', false );
		
		//Menüpunkt duchgehen
		foreach( $menuearray as $menuear ){
	
			//Niveau
			$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
			//Status mit Link zum ändern
			if ( $menuear['status'] == 'off' ){
				$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
			}
			else{
				$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
			}
			//RequestID zeigen und Link zur Seitenansicht
			$requid = $menuear['requid'].'<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$menuear['requid'].'" target="_blank"><span class="ui-icon ui-icon-newwin" style="display:inline-block;" title="Diese Seite aufrufen."></span></a>';
			
			//SiteID Link
			if( $dis_sitelink ){
				$siteid_and_link  = $menuear['siteid'].'<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&id='.$menuear['siteid'].'"><span class="ui-icon ui-icon-pencil" style="display:inline-block;" title="Den Inhalt dieser Seite bearbeiten."></span></a>';
			}
			else{
				$siteid_and_link = $menuear['siteid'];	
			}
			
			$menuename = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'" title="Dieses Menue bearbeiten." >'.$menuear['menuname'].'</a>';
	
			//NextID vorhanden?
			if( $menuear['nextid'] == ''){
				//löschen möglich	
				$del = '<span onclick="del( \''.$menuear['fileid'].'\' , '.$menuear['requid'].' , \''.$menuear['fileidbefore'].'\' );"><span class="ui-icon ui-icon-trash" title="Dieses Menue löschen."></span></span>';
			}
			else{
				//Löschen erst wenn kein Untermenü mehr
				$del = '<span onclick="delimp();"><span class="ui-icon ui-icon-trash" style="display:inline-block;" title="Dieses Menue löschen."></span></span>';
			}
			//neuen Menüpunkt erstellen Link
			$newmenue = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$menuear['fileid'].'&amp;niveau=same" ><span class="ui-icon ui-icon-plusthick" title="Auf diesem Niveau ein weiteres Menue erstellen."></span></a>';
			if( $menuear['nextid'] == ''){
				//kein Untermenü, erstellen Link
				$newmenue .= '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$menuear['fileid'].'&amp;niveau=deeper&amp;requid='.$menuear['requid'].'"><span class="ui-icon ui-icon-arrow-1-se" title="Unter diesem Menue ein Untermenue erstellen."></span></a>';
			}
	
			//JavaScript Buttons zum verschieben
			$versch = '<span onclick=" updown( \''.$menuear['fileid'].'\' , \'up\' , '.$menuear['requid'].' );"><span class="ui-icon ui-icon-arrowthick-1-n" title="Dieses Menue nach oben schieben."></span></span>';
			$versch .= '<span onclick="updown( \''.$menuear['fileid'].'\' , \'down\' , '.$menuear['requid'].' );"><span class="ui-icon ui-icon-arrowthick-1-s" title="Dieses Menue nach unten schieben."></span></span>';
	
			//Tabellenzeile ausgeben
			$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td> <td>'.$versch.'</td> <td>'.$menuename.'</td> <td>'.breakable( $menuear['path'], 2 ).'</td> <td>'.$requid.'</td> <td>'.$menuear['status'].'</td> <td>'.$siteid_and_link.'</td> <td>'.breakable( $menuear['menueid'], 2 ).'</td> <td>'.$del.'</td> <td>'.$newmenue.'</td> </tr>');
			
			//Untermenü öffnen (wenn Liste ohne Untermenüs gewählt)
			if( !$array_deeper ){
				if( !empty( $menuear['nextid'] ) ){
					$sitecontent->add_site_content('<tr> <td class="nobor"></td> <td colspan="9" class="nobor"><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list&amp;start='.$menuear['nextid'].'&amp;deeper=false" title="Das hier gelegene Untermenü anzeigen."><span class="ui-icon ui-icon-arrowreturnthick-1-e" style="display:inline-block;"></span> Untermenü</a></td> </tr>');
				}	
			}
	
			$liste = 'yes';
		}
		$sitecontent->add_site_content('</table>');
	
		//leer -> Hinweis
		if( $liste != 'yes' ){
			$sitecontent->echo_error( 'Es wurden keine Menues gefunden!' );
		}
	
	}
	
	//Menü Listen Navigation machen und params für meneu_array_helper
	protected function menue_navigation( $todo ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//kein GET start und deeper definiert?
		//	evtl. Info in Session?
		if( empty( $_GET['start'] ) && empty( $_GET['deeper']) ){
			if( !empty( $_SESSION['menueview']['start'] ) && !empty( $_SESSION['menueview']['deeper'] ) ){
				$_GET['start'] = $_SESSION['menueview']['start'];
				$_GET['deeper'] = $_SESSION['menueview']['deeper'];
			}
		}
		
		//Tiefe der Menüliste und Beginn aus GET
		//	Beginn gegeben und Zahl?
		if( !empty( $_GET['start'] ) && is_numeric( $_GET['start'] ) && $_GET['start'] >= 0 ){
			$array_start = $_GET['start'];
		}
		//	sonst bei 0 anfangen
		else{
			$array_start = 0;
		}
		//	Tiefer gehen definiert
		if( !empty( $_GET['deeper'] ) &&
			//an oder aus?
			( $_GET['deeper'] == 'true' || $_GET['deeper'] == 'false' )
		){
			//an merken
			if( $_GET['deeper'] == 'true' ){
				$array_deeper = true;
				$array_deeper_str = 'true';
				$array_deeper_str_geg = 'false';
			}
			//aus merken
			else {
				 $array_deeper = false;
				 $array_deeper_str = 'false';
				 $array_deeper_str_geg = 'true';
			}
		}
		//nicht gegeben => aus
		else{
			$array_deeper = false;
			$array_deeper_str = 'false';
			$array_deeper_str_geg = 'true';
		}
		
		//In der Session ablegen
		$_SESSION['menueview']['start'] = $array_start;
		$_SESSION['menueview']['deeper'] = $array_deeper_str ;
		
		//Menüauflistung verändern Buttons
		$sitecontent->add_site_content('<p></p>');
		$sitecontent->add_site_content('<center>');
		
		//Hinweistext zu Ansicht
		$sitecontent->add_site_content('<b> '.(($array_start != 0 ) ? 'Menü "'.$array_start.'"' : 'Hauptmenü' ).' '.( ( $array_deeper ) ? 'mit' : 'ohne' ).' Untermenüs</b><br />');
		
		//Menü zurück (deaktivert sich bei 0)
		if( ( $array_start - 1 ) >= 0 ){
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo='.$todo.'&amp;start='.( $array_start - 1 ).'&amp;deeper='.$array_deeper_str.'">&larr; Menü zurück &larr;</a>');
		}
		else{
			$sitecontent->add_site_content('<a style="color:#ccc;">&larr; Menü zurück &larr;</a>');
		}
		//Hauptmenü
		$sitecontent->add_site_content(' &nbsp;&nbsp;&nbsp; ');
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo='.$todo.'&amp;start=0&amp;deeper='.$array_deeper_str.'">&uarr; Hauptmenü &uarr;</a>');
		$sitecontent->add_site_content(' &nbsp;&nbsp;&nbsp; ');
		//Untermenüs on/off
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo='.$todo.'&amp;start='.$array_start.'&amp;deeper='.$array_deeper_str_geg.'">&orarr; '.( ( $array_deeper ) ? 'ohne' : 'mit' ).' Untermenüs &olarr;</a>');
		$sitecontent->add_site_content(' &nbsp;&nbsp;&nbsp; ');
		//Menü vor
		//	Anzahl bestimmen
		$mens = scan_kimb_dir( 'url/' );
		$menuescount = 0;
		foreach ($mens as $value) {
			if( preg_match( '/nextid_([0-9]*).kimb/', $value ) ){
				$menuescount++;
			}
		}
		//	noch ein Menü weiter möglich?
		if( ( $array_start + 1 ) <= $menuescount ){
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo='.$todo.'&amp;start='.( $array_start + 1 ).'&amp;deeper='.$array_deeper_str.'">&rarr; Menü vor &rarr;</a>');
		}
		else{
			$sitecontent->add_site_content('<a style="color:#ccc;">&rarr; Menü vor &rarr;</a>');
		}
		
		$sitecontent->add_site_content('</center>');
		$sitecontent->add_site_content('<p></p>');
		
		return array(
			$array_start,
			$array_deeper,
			$array_deeper_str,
			$array_deeper_str_geg
		);
		
	}
	
	//Menü editieren
	public function make_menue_edit(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$idfile = $this->idfile;
		$menuenames = $this->menuenames;
		
		$sitecontent->add_site_content('<h2>Ein Menue bearbeiten</h2>');
		
		//Aufruf des Menüpunktes richtig?
		if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) ){
			//URL-Datei lesen
			if( $_GET['file'] == 'first' ){
				$file = new KIMBdbf( 'url/first.kimb' );
			}
			else{
				$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
			}
			//ID in der URL-Datei suchen
			$id = $file->search_kimb_xxxid( $_GET['reqid'] , 'requestid');
			if( $id  != false ){
				//Menüpunkt vorhanden
				
				//mehrsprachige Seite?
				//bestimmte Sprachdatei für Menünamen gewünscht?
				if( $allgsysconf['lang'] == 'on' && $_GET['langid'] != 0 && is_numeric( $_GET['langid'] ) ){
					//laden
					$menuenames = new KIMBdbf('menue/menue_names_lang_'.$_GET['langid'].'.kimb');
				}
				else{
					//Standardsprache
					$_GET['langid'] = 0;
				}
				
				//Name oder Pfad übergeben -> eintragen
				if( isset( $_POST['name'] ) && isset( $_POST['pfad'] ) ){
					//Pfad validieren
					$_POST['pfad'] = preg_replace("/[^0-9A-Za-z_.-]/","", $_POST['pfad']);
					//Pfad schon vorhaden?
					$ok = $file->search_kimb_xxxid( $_POST['pfad'] , 'path');
					if( $ok == false || $ok == $id ){
						//unverändert oder nicht vergeben
						
						//Änderung nötig?
						if( $file->read_kimb_id( $id , 'path') != $_POST['pfad'] ){
							//ändern und Hinweis
							$file->write_kimb_id( $id , 'add' , 'path' , $_POST['pfad'] );
							$sitecontent->echo_message( 'Der Pfad wurde angepasst!' );
						}
					}
					//Name übergeben und auch zu ändern? 
					if( !empty( $_POST['name'] ) && $menuenames->read_kimb_one( $_GET['reqid'] ) != $_POST['name'] ){
						//machen und Hinweis
						$menuenames->write_kimb_replace( $_GET['reqid'] , $_POST['name'] );
						$sitecontent->echo_message( 'Der Name wurde angepasst!' );
					}
				}
	
				//aktuellen Menüpath für JS setzen (unverändert erkennbar)
				$sitecontent->add_html_header('<script>var normpath = "'.$file->read_kimb_id( $id , 'path').'" </script>');
				
				//JavaScript
				$this->jsobject->for_menue_edit( $_GET['file'] );
	
				//Formular beginnen
				$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&amp;file='.$_GET['file'].'&amp;reqid='.$_GET['reqid'].'&amp;langid='.$_GET['langid'].'" method="post" onsubmit="if( document.getElementById(\'check\').value == \'nok\' ){ return false; } ">');
				
				//Sprachauswahl wenn nötig
				if( $allgsysconf['lang'] == 'on'){
					make_lang_dropdown( '"'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&file='.$_GET['file'].'&reqid='.$_GET['reqid'].'&langid=" + val', $_GET['langid'] );
				}
				$sitecontent->add_site_content('<input type="text" value="'.$menuenames->read_kimb_one( $_GET['reqid'] ).'" name="name" > <i title="Name des Menues der im Frontend angezeigt wird." >(Menuename)</i><br />');
				
				$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'path').'" name="pfad" id="pfad" onkeyup="pfadreplace();" onchange="checkpath();" > <i id="pfadtext" title="Ein Menuepfad besteht aus Buchstaben, Zahlen, &apos;_&apos; und &apos;-&apos;.">(Menuepfad)</i><br />');
				$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'status').'" name="status" readonly="readonly"> <i title="Veränderbar auf Seite Auflisten sowie Zuordnung." >(Status)</i><br />');
				$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'requestid').'" name="requid" readonly="readonly"> <i title="Automatisch bei Erstellung des Menue generiert.">(RequestID)</i><br />');
				$sitecontent->add_site_content('<input type="text" value="'.$idfile->read_kimb_id( $_GET['reqid'] , 'siteid' ).'" name="siteid" readonly="readonly"> <i title="Veränderbar auf Seite Zuordnen." >(SiteID)</i><br />');
				$sitecontent->add_site_content('<input type="hidden" value="ok" id="check">');
				$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
				$sitecontent->add_site_content('</form>');
	
			}
		}
		else{
			//Fehler ausgeben, wenn Anfrage fehlerhaft
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
		}
		
		
	}
	
	//Menü löschen
	public function make_menue_del_dbf( $GET){
		
		$idfile = $this->idfile;
		$menuenames = $this->menuenames;
		
		//richtige URL-Datei lesen
		if( $GET['file'] == 'first' ){
			$file_name = 'url/first.kimb';
		}
		else{
			$file_name = 'url/nextid_'.$GET['file'].'.kimb';
		}
		$file = new KIMBdbf( $file_name );
		//ID in der URL-Datei feststellen
		$id = $file->search_kimb_xxxid( $GET['reqid'] , 'requestid');
		//NextID lesen
		$nextid = $file->read_kimb_id( $id , 'nextid');
		//NextID leer?
		//ID gefunden?
		if( $id  != false && $nextid == ''){
			//Alle Menüpunkte der URL-Datei lesen
			
			$wid = 1;
			while( true ){
				//aber nur wenn nicht das zu löschende Menü
				if( $wid != $id ){
					$wpath = $file->read_kimb_id( $wid , 'path' );
					$wnextid = $file->read_kimb_id( $wid , 'nextid' );
					$wrequid = $file->read_kimb_id( $wid , 'requestid' );
					$wstatus = $file->read_kimb_id( $wid , 'status');
				}
				if( $wpath == '' && $wid != $id ){
					//keine Inhalte mehr -> alles gelesen
					break;
				}
				//gelesenes speichern
				if( $wid != $id ){
					//NextID leer -> auch für dbf leer
					if( $wnextid == '' ){
						$wnextid = '---empty---';
					}
					//in Array sichern
					$newmenuefile[] = array( 'path' => $wpath, 'nextid' => $wnextid , 'requid' => $wrequid, 'status' => $wstatus );
				}
				$wid++;
			}
			
			//URL-Datei neu schreiben
			
			$inhalt = 'none';
			$i = 1;
			//erstmal Datei löschen
			$file->delete_kimb_file();
			//	komplett
			unset( $file );
			
			//neu laden
			$file = new KIMBdbf( $file_name );
			
			//alles eben gespeichertes wieder in die neue Datei einfügen
			foreach( $newmenuefile as $newmenue ){
				$file->write_kimb_id( $i , 'add' , 'path' , $newmenue['path'] );
				$file->write_kimb_id( $i , 'add' , 'nextid' , $newmenue['nextid'] );
				$file->write_kimb_id( $i , 'add' , 'requestid' , $newmenue['requid'] );
				$file->write_kimb_id( $i , 'add' , 'status' , $newmenue['status'] );
				$inhalt = 'something';
				$i++;
			}
			//wurde etwas hinzugefügt?
			if( $inhalt == 'none' && $GET['file'] != 'first'){
				//nein, also wurde ein ganzes Untermenü gelöscht
				//URL-Datei welche NextID auf gerade gelöschtes Untermenü enthält öffnen
				if( $GET['fileidbefore'] == 'first' ){
					$filebef = new KIMBdbf( 'url/first.kimb' );
				}
				else{
					$filebef = new KIMBdbf( 'url/nextid_'.$GET['fileidbefore'].'.kimb' );
				}
				//ID, welche NextID enthält, in der URL-Datei suchen
				$befid = $filebef->search_kimb_xxxid( $GET['file'] , 'nextid');
				if( $befid  != false ){
					//gefunden, setzen wir die NextID auf ""
					$filebef->write_kimb_id( $befid , 'add' , 'nextid' , '---empty---' );
				}				
			}
			//ganz zum Schluss noch den Menünamen löschen
			//	wenn bei anderen Sprachen der Name beleibt, kann dieser auch bei erneuter Vergabe der RequestID überschrieben werden 
			$menuenames->write_kimb_id( $GET['reqid'] , 'del' );
			//aus der ID file austragen
			$idfile->write_kimb_id( $GET['reqid'] , 'del' );
			
			return true;

		}
		else{
			return false;
		}
	}
	
	//Menüpunkt Status ändern
	public function make_menue_deakch_dbf( $GET ){
		//URL-Datei lesen
		if( $GET['file'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$GET['file'].'.kimb' );
		}
		//ID des Menüpunktes in der URL-Datei suchen
		$id = $file->search_kimb_xxxid( $GET['reqid'] , 'requestid');
		if( $id  != false ){
			//wenn gefunden erstmal aktuellen Status herausfinden
			$status = $file->read_kimb_id( $id , 'status' );
			//aktiviert? -> das muss deaktiviert werden
			if( $status == 'on' ){
				$file->write_kimb_id( $id , 'add' , 'status' , 'off' );
				$stat = 'off';
			}
			//deaktiviert? -> das muss aktiviert werden
			else{
				$file->write_kimb_id( $id , 'add' , 'status' , 'on' );
				$stat = 'on';
			}

			return true;

		}
		else{
			return false;
		}
	}
	
	//Menüpunkt verschieben
	public function make_menue_versch( $GET ){
		
			//URL-Datei lesen
			if( $GET['fileid'] == 'first' ){
				$file_name = 'url/first.kimb';
			}
			else{
				$file_name =  'url/nextid_'.$GET['fileid'].'.kimb';
			}
			$file = new KIMBdbf( $file_name );
			//ID des Menüpunktes feststellen
			$id = $file->search_kimb_xxxid( $GET['requid'] , 'requestid');
			if( $id  != false ){
				//Verschiebung auf Stelle vor eins gewünscht?
				if( $id == 1 && $GET['updo'] == 'up' ){
					//Fehler
					echo 'nok';
					die;
				}
				//Verschiebung auf Stelle unter der Letzten gewünscht?
				if( $file->read_kimb_id( $id+1 , 'path' ) == '' && $GET['updo'] == 'down' ){
					//Fehler
					echo 'nok';
					die;
				}
	
				//ganze URL-Datei lesen
				$wid = 1;
				while( true ){
					$wpath = $file->read_kimb_id( $wid , 'path' );
					$wnextid = $file->read_kimb_id( $wid , 'nextid' );
					$wrequid = $file->read_kimb_id( $wid , 'requestid' );
					$wstatus = $file->read_kimb_id( $wid , 'status');

					if( $wnextid == '' ){
						//NextID auch in KIMBdbf bei Eintrgaung auf "" setzen
						$wnextid = '---empty---';
					}
					
					//keine Werte mehr?
					if( $wpath == '' ){
						//alles gelesen
						break;
					}
	
					//in ein Array speichern
					$newmenuefile[$wid] = array( 'path' => $wpath, 'nextid' => $wnextid , 'requid' => $wrequid, 'status' => $wstatus );
					
					$wid++;
				}
	
				//anhand von Verschieberichtung neue ID innerhalb der URL-Datei bestimmen
				if( $GET['updo'] == 'down' ){
					$newid = $id+1;
				}
				if( $GET['updo'] == 'up' ){
					$newid = $id-1;
				}
	
				//alles wieder in neue URL-Datei schreiben
				$i = 1;
				//erstmal die URL-Datei leeren
				$file->delete_kimb_file();
				//	komplett
				unset( $file );
				
				//und neu laden
				$file = new KIMBdbf( $file_name );
				
				while( true ){
					
					//zu schreibenden ID in der neuen URL-Datei entsrpricht der neuen ID des Eintrags?
					if( $newid == $i ){
	
						//gespeicherte Daten lesen und hier in die URL-Datei einfügen
						$data = $newmenuefile[$id];
	
						$file->write_kimb_id( $i , 'add' , 'path' , $data['path'] );
						$file->write_kimb_id( $i , 'add' , 'nextid' , $data['nextid'] );
						$file->write_kimb_id( $i , 'add' , 'requestid' , $data['requid'] );
						$file->write_kimb_id( $i , 'add' , 'status' , $data['status'] );
	
						//nächste ID innerhalb der Datei
						$i++;
					}
	
					//gespeicherte Daten lesen
					$data = $newmenuefile[$i];
	
					//den zu verschiebenden Datensatz überspringen
					if( $data['requid'] == $GET['requid'] && $GET['updo'] == 'down' ){
						$data = $newmenuefile[$i+1];
					}
					if( $data['requid'] == $GET['requid'] && $GET['updo'] == 'up' ){
						$data = $newmenuefile[$i-1];
					}

					//keine Daten mehr 
					if( !isset( $data['path'] ) ){
						//fertig -> verlassen
						break;
					}
	
					//Menüpunkt hinzufügen	
					$file->write_kimb_id( $i , 'add' , 'path' , $data['path'] );
					$file->write_kimb_id( $i , 'add' , 'nextid' , $data['nextid'] );
					$file->write_kimb_id( $i , 'add' , 'requestid' , $data['requid'] );
					$file->write_kimb_id( $i , 'add' , 'status' , $data['status'] );
	
					$i++;				
				}
				return true;
			}
			else{
				return false;
			}
	}
	
	//next file finished
	//let's go and get a coffe :-)
}
?>
