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



defined('KIMB_Backend') or die('No clean Request');

class BEmenue{
	
	protected $allgsysconf, $sitecontent, $idfile, $menuenames;
	
	public function __construct( $allgsysconf, $sitecontent ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		
		$this->idfile = new KIMBdbf('menue/allids.kimb');
		$this->menuenames = new KIMBdbf('menue/menue_names.kimb');
		$this->sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
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
			
				//nötige vars
				$newm['name'] = $POST['name'];
				if( $POST['pfad'] == '' ){
					$newm['pfad'] = preg_replace("/[^0-9A-Za-z_-]/","", $POST['name']);
				}
				else{
					$newm['pfad'] = preg_replace("/[^0-9A-Za-z_-]/","", $POST['pfad']);
				}
				$newm['nextid'] = '---empty---';
				$newm['requestid'] = $idfile->next_kimb_id();
				if( isset( $POST['siteid'] ) && ( is_numeric( $POST['siteid'] ) || $POST['siteid'] == 'none' ) ){
					$newm['siteid'] = $POST['siteid'];
				}
				else{
					$newm['siteid'] = '---empty---';
				}
				$newm['menueid'] = $newm['requestid'].mt_rand( 100, 999 );
				$newm['status'] = $status;
	
				if( $GET['file'] == 'first' ){
					$file = new KIMBdbf( 'url/first.kimb' );
				}
				else{
					$file = new KIMBdbf( 'url/nextid_'.$GET['file'].'.kimb' );
				}
	
				if( $GET['niveau'] == 'deeper' && is_numeric( $GET['requid'] ) ){
					$i = 1;
					while( 5 == 5 ){
						if( !check_for_kimb_file( 'url/nextid_'.$i.'.kimb' ) ){
							break;
						}
						$i++;
					}
	
					//neue Datei und nextid eintragen				
					$oldid = $file->search_kimb_xxxid( $GET['requid'] , 'requestid');
					if( $oldid == false ){
						$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
						$sitecontent->output_complete_site();
						die;
					}
					if( empty( $file->read_kimb_id( $oldid , 'nextid' ) ) ){
						$file->write_kimb_id( $oldid , 'add' , 'nextid' , $i );
					}
					else{
						$newm['file'] = 'error';
						$newm['requestid'] = '';
						return $newm;
					}
	
					$file = new KIMBdbf( 'url/nextid_'.$i.'.kimb' );
					
					$GET['file'] = $i;
					$newm['file'] = $i;
	
				}
				elseif( $GET['niveau'] == 'deeper' ) {
					$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
					$sitecontent->output_complete_site();
					die;
				}
				$id = $file->next_kimb_id();
	
				$pfad = $newm['pfad'];
				$i = 1;
				while( $file->search_kimb_xxxid( $pfad , 'path') != false){
					$pfad = $newm['pfad'].$i;
					$i++;
				}
				$newm['pfad'] = $pfad;
	
				//url file schreiben
				$file->write_kimb_id( $id , 'add' , 'path' , $newm['pfad'] );
				$file->write_kimb_id( $id , 'add' , 'nextid' , $newm['nextid'] );
				$file->write_kimb_id( $id , 'add' , 'requestid' , $newm['requestid'] );
				$file->write_kimb_id( $id , 'add' , 'status' , $newm['status'] );
				//idfile schreiben
				$idfile->write_kimb_id( $newm['requestid'] , 'add' , 'siteid' , $newm['siteid'] );
				$idfile->write_kimb_id( $newm['requestid'] , 'add' , 'menueid' , $newm['menueid'] );
				//menuename schreiben
				$menuenames->write_kimb_new( $newm['requestid'] , $newm['name'] );
				
				if( empty( $newm['file'] ) ){
					$newm['file'] = $GET['file'];
				}
				
				return $newm;
	}
	
	public function make_menue_new(){
		//Zeigt dem User den Dialog zum Erstellen neuer Menues an
		
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$idfile = $this->idfile;
		$menuenames = $this->menuenames;
		
		$sitecontent->add_site_content('<h2>Ein neues Menue erstellen</h2>');

		if( ( is_numeric( $_GET['file'] ) || $_GET['file'] == 'first' )  && ( $_GET['niveau'] == 'same' || $_GET['niveau'] == 'deeper' ) && ( is_numeric( $_GET['requid'] ) || !isset( $_GET['requid'] ) || $_GET['requid'] == '' ) ){
	
			if( isset( $_POST['name'] ) ){
				
				$GET = $_GET;
				$POST = $_POST;
				
				$newm = $this->make_menue_new_dbf( $GET, $POST );
	
				open_url('/kimb-cms-backend/menue.php?todo=edit&file='.$newm['file'].'&reqid='.$newm['requestid']);
				die;
			}
	
			$sitecontent->add_html_header('<script>
			function makepfad() {
				var name, klein, pfad, umg;
	
				name = $( "input[name=name]" ).val();
	
				klein = name.toLowerCase();
				umg = klein.replace( / /g , "-");
				pfad = umg.replace( /[^0-9A-Za-z_-]/g, "");
	
				$( "input[name=pfad]" ).val( pfad );
	
			}
			</script>');
	
			$sitedr = '<select name="siteid"><option value="none" selected="selected">None</option>';
			$sites = scan_kimb_dir('site/');
			foreach ( $sites as $site ){
				if( $site != 'langfile.kimb'){
					$sitef = new KIMBdbf('site/'.$site);
					$id = preg_replace("/[^0-9]/","", $site);
					$title = $sitef->read_kimb_one('title');
		
					$sitedr .= '<option value="'.$id.'">'.$title.' - '.$id.'</option>';
				}
			}
			$sitedr .= '</select>';
	
			$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$_GET['file'].'&amp;niveau='.$_GET['niveau'].'&amp;requid='.$_GET['requid'].'" method="post">');
			$sitecontent->add_site_content('<input type="text" name="name" onkeyup=" makepfad(); " onchange=" makepfad(); " > <i title="Pflichtfeld">(Menuename *)</i><br />');
			$sitecontent->add_site_content('<input type="text" name="pfad" > <i title="Manuell oder automatisch aus Menuename">(Pfad)</i><br />');
			$sitecontent->add_site_content($sitedr.' <i title="Auch später über Zuordnung zu definieren">(SiteID)</i><br />');
			$sitecontent->add_site_content('<input type="submit" value="Erstellen" ><br />');
			$sitecontent->add_site_content('</form>');
	
		}
		else{
			$sitecontent->echo_message( 'Bitte wählen Sie zuerste eine Stelle über Menue -> Anpassen! <br />( Rechts in der Spalte Neu )<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list"><button>Los geht&apos;s!</button></a>' );
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
		
			if( $_POST['post'] == 'post' ){
				$i = 0;
				while( 5 == 5 ){
					if( $_POST[$i.'-site'] != $idfile->read_kimb_id( $_POST[$i] , 'siteid' ) ){
						if( $idfile->write_kimb_id( $_POST[$i] , 'add' , 'siteid' , $_POST[$i.'-site'] ) ){
							$sitecontent->echo_message( 'Die Seite '.$_POST[$i.'-site'].' wurde einem Menue zugeordnet!<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$_POST[$i].'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Die Seite mit Menue aufrufen."></span></a>' );
						}
					}
					if( $_POST[$i] == '' ){
						break;
					}
					$i++;
				}
			}
			
			$sites = scan_kimb_dir('site/');
			foreach ( $sites as $site ){
				if( $site != 'langfile.kimb'){
					$sitef = new KIMBdbf('site/'.$site);
					$id = preg_replace("/[^0-9]/","", $site);
					$title = $sitef->read_kimb_one('title');
			
					$allsites[] = array( 'site' => $title, 'id' => $id );
				}
			}
		
			$sitecontent->add_site_content('<h2>Ein Menue einer Seite zuordnen</h2>');
				
			unset( $idfile );
			$this->idfile = new KIMBdbf('menue/allids.kimb');
			$idfile = $this->idfile;
			$menuearray = make_menue_array_helper();
						
			$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect">');
			$sitecontent->add_site_content('<table width="100%"><tr> <th width="50px;"></th> <th>MenueName</th> <th>Status</th> <th>SiteID <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Wählen Sie bitte für jeden Menuepunkt eine Seite!"></span></th> </tr>');
			$i = 0;
			foreach( $menuearray as $menuear ){
		
				$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
		
				if ( $menuear['status'] == 'off' ){
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" style="display:inline-block;" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
				}
				else{
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" style="display:inline-block;" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
				}
		
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
				if( $sel == 'yes' ){
					$sitedr = '<select name="'.$i.'-site"><option value="none" >None</option>'.$sitedr;
				}
				else{
					$sitedr = '<select name="'.$i.'-site"><option value="none" selected="selected">None</option>'.$sitedr;
				}
				
				$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td>  <td>'.$menuear['menuname'].'</td> <td>'.$status.'</td> <td>'.$sitedr.'<input type="hidden" value="'.$menuear['requid'].'" name="'.$i.'"></td> </tr>');
				$i++;
		
				$liste = 'yes';
			}
			$sitecontent->add_site_content('</table>');
		
			if( $liste != 'yes' ){
				$sitecontent->echo_error( 'Es wurden keine Menues gefunden!' );
			}
		
			$sitecontent->add_site_content('<input type="hidden" value="post" name="post"><input type="submit" value="Zuordnungen ändern"></form>');	
	}
	
	
	
	
}
?>
