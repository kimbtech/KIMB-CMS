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

class BEsites{
	
	protected $allgsysconf, $sitecontent;
	
	public function __construct( $allgsysconf, $sitecontent, $tabelle = true ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		
		if( is_object( $this->sitecontent ) && $tabelle ){
			$this->sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		}
	}
	
	public function make_site_new_dbf( $POST ){
			$allgsysconf = $this->allgsysconf;
			$sitecontent = $this->sitecontent;
		
			$i=1;
			while( 5 == 5 ){
				if( !check_for_kimb_file( '/site/site_'.$i.'.kimb') && !check_for_kimb_file( '/site/site_'.$i.'_deak.kimb') ){
					break;
				}
				$i++;
			}
	
			$sitef = new KIMBdbf( '/site/site_'.$i.'.kimb' );
	
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
			
			if( $easyfile->read_kimb_one( 'oo' ) == 'on'  ){
				if( $POST['menue'] != 'none' ||  $POST['untermenue'] != 'none' ){
					
					//Daten testen
					if(  $POST['menue'] != 'none' ){
						if(  $POST['menue'] == 'first' || is_numeric( $POST['menue'] )  ){
							if( $easyfile->read_kimb_search_teilpl( 'same' , $POST['menue'] )  ){
								$tested = true;
								$file = $POST['menue'];	
							}
						}			
					}
					elseif( $POST['untermenue'] != 'none' ){
						$array = explode( '||', $POST['untermenue'] );
						if( is_numeric( $array[0] ) && ( $array[1] == 'first' || is_numeric( $array[1] ) ) ){
							if( $easyfile->read_kimb_search_teilpl( 'deeper' , $POST['untermenue'] )  ){
								$tested = true;
								$requid = $array[0];
								$file = $array[1];
							}
						}
					}
					
					//Menue machen
					if( $tested ){
											
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
						
						$bemenue = new BEmenue( $allgsysconf, $sitecontent );
						
						$return = $bemenue->make_menue_new_dbf( $GET, $POST, $status );
						
						//Untermenu nur einmal, dann als Menü
						if( $POST['untermenue'] != 'none' ){
							$easyfile->write_kimb_teilpl( 'deeper' , $POST['untermenue'], 'del');
							
							$easyfile->write_kimb_teilpl( 'same' , $return['file'], 'add');
						}
					}
				}
			}
			
			return $i;	
	}
	
	
	public function make_site_new(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>Neue Seite</h2>');
	
		add_tiny( true, true);
	
		if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){
			
			$i = $this->make_site_new_dbf( $_POST );
			
			open_url('/kimb-cms-backend/sites.php?todo=edit&id='.$i);
			die;
		}
	
		//Easy Menue
		
		$easyfile = new KIMBdbf( 'backend/easy_menue.kimb' );
		
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new" method="post"><br />');
		
		if( $easyfile->read_kimb_one( 'oo' ) == 'on'  ){
		
			$menuearray =  make_menue_array_helper();
		
			$selectmen = '<select name="menue"> <option value="none" selected="selected"></option>';
			$selectunte .= '<select name="untermenue"> <option value="none" selected="selected"></option>';
		
			foreach( $menuearray as $menue ){
				
				if( empty( $menue['nextid'] ) ){
					$deeper = $menue['requid'].'||'.$menue['fileid'];
					
					if( $easyfile->read_kimb_search_teilpl( 'deeper' , $deeper )  ){
						$selectunte .= '<option value="'.$deeper.'">'.$menue['menuname'].'</option>';
						$done['deeper'] = true;
					}
				}
				if( !in_array( $menue['fileid'], $filearr ) ){
					$same = $menue['fileid'];
					
					if( $easyfile->read_kimb_search_teilpl( 'same' , $same)  ){			
						$selectmen .= '<option value="'.$same.'">'.$menue['menuname'].'</option>';
						$done['same'] = true;
					}
				}
				$filearr[] = $menue['fileid'];
				
			}
			
			$selectmen .= '</select>';
			$selectunte .= '</select>';
			
			if( !$done['deeper'] ){
				$selectunte = '<b>Keine Freigaben</b>';
			}
			if( !$done['same'] ){
				$selectmen = '<b>Keine Freigaben</b>';
			}
			
			$achtung = '<br />Der Pfad und der Name des Menüs werden auf Basis des Seitentitels erstellt!';
			if( $easyfile->read_kimb_one( 'stat' ) == 'off' ){
				$achtung .= '<br /><i>Achtung, nach der Erstellung muss das neue Menü noch aktiviert werden!</i>!';
			}
			
			$this->jsobject->for_site_new( $selectmen, $selectunte, $achtung );
		}
		
		//Seitenerstellung
		$sitecontent->add_site_content('<input type="text" value="Titel" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
		$sitecontent->add_site_content('<textarea name="header" style="width:74%; height:50px;"></textarea><i>HTML Header </i><br />');
		$sitecontent->add_site_content('<input type="text" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
		$sitecontent->add_site_content('<textarea name="description" style="width:74%; height:50px;"></textarea> <i>Description</i> <br />');
		$sitecontent->add_site_content('<textarea name="inhalt" id="inhalt" style="width:99%; height:300px;">&lt;h1&gt;Titel&lt;/h1&gt;</textarea> <i>Inhalt &uarr;</i> <button onclick="tinychange( \'inhalt\' ); return false;">Editor I/O</button> <br />');
		$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%; height:75px;"></textarea> <i>Footer &uarr;</i> <button onclick="tinychange( \'footer\' ); return false;">Editor I/O</button> <br />');
		$sitecontent->add_site_content('<input type="submit" value="Erstellen"></form>');	

	}
	
	public function make_site_list(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>Liste aller Seiten</h2>');
		
		$this->jsobject->for_site_list();
		
		$sites = scan_kimb_dir('site/');

		$sitecontent->add_site_content('<span><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new"><span class="ui-icon ui-icon-plus" style="display:inline-block;" title="Eine neue Seite erstellen."></span></a>');
		$sitecontent->add_site_content('<input type="text" class="search" onkeydown="if(event.keyCode == 13){ search(); }" ><button onclick="search();" title="Nach Seitenamen suchen ( genauer Seitenname nötig ).">Suchen</button></span><hr />');
		$sitecontent->add_site_content('<table width="100%"><tr><th width="40px;" >ID</th><th>Name</th><th width="20px;">Status</th><th width="20px;">Löschen</th></tr>');
	
		$idfile = new KIMBdbf('menue/allids.kimb');
	
		foreach ( $sites as $site ){
			if( $site != 'langfile.kimb'){
				$sitef = new KIMBdbf('site/'.$site);
				$id = preg_replace("/[^0-9]/","", $site);
				$title = $sitef->read_kimb_one('title');
				$name = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$id.'" title="Seite bearbeiten.">'.$title.'</a>';
				if ( strpos( $site , 'deak' ) !== false ){
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-close" title="Diese Seite ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern )"></span></a>';
				}
				else{
					$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-check" title="Diese Seite ist zu Zeit aktiviert, also sichtbar. ( click -> ändern )"></span></a>';
				}
				$del = '<span onclick="var delet = del( '.$id.' ); delet();"><span class="ui-icon ui-icon-trash" title="Diese Seite löschen."></span></span>';
				$zugeor = $idfile->search_kimb_xxxid( $id , 'siteid' );
				if( $zugeor == false ){
					$status .= '<span class="ui-icon ui-icon-alert" title="Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!"></span>';
				}
				$sitecontent->add_site_content('<tr><td>'.$id.'</td><td id="'.$title.'">'.$name.'</td><td>'.$status.'</td><td>'.$del.'</td></tr>');
		
				$liste = 'yes';
			}
		}
		$sitecontent->add_site_content('</table>');
	
		if( $liste != 'yes' ){
			$sitecontent->echo_error( 'Es wurden keine Seiten gefunden!' );
		}
	}
	
	public function make_site_edit(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>Seite bearbeiten</h2>');
		
		if( !is_object( $sitef ) ){
			if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
				$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'.kimb' );
			}
			elseif( check_for_kimb_file( '/site/site_'.$_GET['id'].'_deak.kimb' ) ){
				$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'_deak.kimb' );
			}
			else{
				$sitecontent->echo_error('Die Seite wurde nicht gefunden' , '404');
				$sitecontent->output_complete_site();
				die;
			}
		}
		
		if( $allgsysconf['lang'] == 'on' && $_GET['langid'] != 0 && is_numeric( $_GET['langid'] ) ){
			$dbftag['title'] = 'title-'.$_GET['langid'];
			$dbftag['keywords'] = 'keywords-'.$_GET['langid'];
			$dbftag['description'] = 'description-'.$_GET['langid'];
			$dbftag['inhalt'] = 'inhalt-'.$_GET['langid'];
			$dbftag['footer'] = 'footer-'.$_GET['langid'];
			
			if( empty( $sitef->read_kimb_one( $dbftag['title'] ) ) && empty( $sitef->read_kimb_one( $dbftag['inhalt'] ) ) ){
				$sitef->write_kimb_one( $dbftag['title'] , 'Title' );
				$sitef->write_kimb_one( $dbftag['keywords'] , '' );
				$sitef->write_kimb_one( $dbftag['description'] , '' );
				$sitef->write_kimb_one( $dbftag['inhalt'] , '<h1>Inhalt</h1>' );
				$sitef->write_kimb_one( $dbftag['footer'] , '' );				
			}
		}
		else{
			$dbftag['title'] = 'title';
			$dbftag['keywords'] = 'keywords';
			$dbftag['description'] = 'description';
			$dbftag['inhalt'] = 'inhalt';
			$dbftag['footer'] = 'footer';
			
			$_GET['langid'] = 0;				
		}
		
		if( $allgsysconf['lang'] == 'on'){
			make_lang_dropdown( '"'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&id='.$_GET['id'].'&langid=" + val', $_GET['langid'] );
		}
	
		$this->jsobject->for_site_edit();
		add_tiny( true, true);
	
		if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){
	
			$sitef->write_kimb_replace( $dbftag['title'] , $_POST['title'] );
			$sitef->write_kimb_replace( 'header' , $_POST['header'] );
			$sitef->write_kimb_replace( $dbftag['keywords'] , $_POST['keywords'] );
			$sitef->write_kimb_replace( $dbftag['description'] , $_POST['description'] );
			$sitef->write_kimb_replace( $dbftag['inhalt'] , $_POST['inhalt'] );
			$sitef->write_kimb_replace( $dbftag['footer'] , $_POST['footer'] );
			$sitef->write_kimb_replace( 'time' , time() );
			$sitef->write_kimb_replace( 'made_user' , $_SESSION['name'] );
	
		}
		
		$seite['title'] = $sitef->read_kimb_one( $dbftag['title'] );
		$seite['header'] = $sitef->read_kimb_one( 'header' );
		$seite['keywords'] = $sitef->read_kimb_one( $dbftag['keywords'] );
		$seite['description'] = $sitef->read_kimb_one( $dbftag['description'] );
		$seite['inhalt'] = $sitef->read_kimb_one( $dbftag['inhalt'] );
		$seite['footer'] = $sitef->read_kimb_one( $dbftag['footer'] );
		$seite['time'] = $sitef->read_kimb_one( 'time' );
		$seite['time'] = date( "d.m.Y \u\m H:i" , $seite['time'] );
	
		$idfile = new KIMBdbf('menue/allids.kimb');
		$id = $idfile->search_kimb_xxxid( $_GET['id'] , 'siteid' );
	
		if( $id == false ){
			$sitecontent->echo_message( 'Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!' );
		}
	
		$sitecontent->add_site_content('<span onclick="var delet = del( '.$_GET['id'].' ); delet();"><span class="ui-icon ui-icon-trash" style="display:inline-block;" title="Diese Seite löschen."></span></span>');
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$id.'" target="_blank"><span class="ui-icon ui-icon-newwin" style="display:inline-block;" title="Diese Seite anschauen."></span></a>');
		
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$_GET['id'].'&amp;langid='.$_GET['langid'].'" method="post"><br />');
		$sitecontent->add_site_content('<input type="text" value="'.$seite['title'].'" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
		$sitecontent->add_site_content('<div style="position:relative;" ><textarea name="header" style="width:74%; height:50px;">'.htmlentities( $seite['header'] ).'</textarea><span style="position:absolute; top:0; left:75.5%;"> <i>HTML Header</i>');
			$sitecontent->add_site_content('<br /> <select id="libs">');
			$sitecontent->add_site_content('<option value=""></option>');
			$sitecontent->add_site_content('<option value="&lt;!-- jQuery --&gt;">jQuery</option>');
			$sitecontent->add_site_content('<option value="&lt;!-- jQuery UI --&gt;">jQuery UI</option>');
			$sitecontent->add_site_content('<option value="&lt;!-- nicEdit --&gt;">nicEdit</option>');
			$sitecontent->add_site_content('<option value="&lt;!-- TinyMCE --&gt;">TinyMCE</option>');
			$sitecontent->add_site_content('<option value="&lt;!-- Hash --&gt;">Hash</option>');
			$sitecontent->add_site_content('</select><span class="ui-icon ui-icon-info" style="display:inline-block;" title="Fügen Sie Ihrer Seite ganz einfach eine JavaScript-Bibilothek hinzu." ></span></span></div>');
		$sitecontent->add_site_content('<input type="text" value="'.$seite['keywords'].'" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
		$sitecontent->add_site_content('<div style="position:relative;" ><textarea name="description" style="width:74%; height:50px;">'.$seite['description'].'</textarea><span style="position:absolute; top:0; left:75.5%;"> <i>Description</i></span></div>');
		$sitecontent->add_site_content('<textarea name="inhalt" id="inhalt" style="width:99%; height:300px;">'.$seite['inhalt'].'</textarea> <i>Inhalt &uarr;</i> <button onclick="tinychange( \'inhalt\' ); return false;">Editor I/O</button> <br />');
		$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%; height:75px;">'.$seite['footer'].'</textarea> <i>Footer &uarr;</i> <button onclick="tinychange( \'footer\' ); return false;">Editor I/O</button> <br />');
		$sitecontent->add_site_content('<input type="text" readonly="readonly" value="'.$seite['time'].'" name="time" style="width:74%;"> <i>Zuletzt geändert</i><br />');
		$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');
		
	}
	
	public function make_site_deakch( $id ){
		if( check_for_kimb_file( '/site/site_'.$id.'.kimb' ) ){
			rename_kimbdbf( '/site/site_'.$id.'.kimb' , '/site/site_'.$id.'_deak.kimb' );
		}
		elseif( !check_for_kimb_file('/site/site_'.$id.'.kimb') && check_for_kimb_file( '/site/site_'.$id.'_deak.kimb' )  ){
			rename_kimbdbf( '/site/site_'.$id.'_deak.kimb' , '/site/site_'.$id.'.kimb' );
		}
		else{
			return false;
		}
		
		return true;
	}
	
	public function make_site_del( $id ){
		if( check_for_kimb_file( '/site/site_'.$id.'.kimb' ) ){
			return delete_kimb_datei( '/site/site_'.$id.'.kimb' );				
		}
		return false;
	}
}
?>
