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

class BEaddinst{
	
	protected $allgsysconf, $sitecontent, $addoninclude, $jsobject;
	
	public $allinclpar = array( 'ajax', 'cron', 'funcclass', 'be_first', 'be_second', 'fe_first', 'fe_second' );
	
	public function __construct( $allgsysconf, $sitecontent, $addoninclude, $tabelle = true ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->addoninclude = $addoninclude;
		
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		
		if( is_object( $this->sitecontent ) && $tabelle ){
			$this->sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		}
		
		if( is_dir( __DIR__.'/../../addons/temp/' ) ){
			rm_r( __DIR__.'/../../addons/temp/' );
		}
	}
	
	public function change_stat( $addon ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$addoninclude = $this->addoninclude;
		
		foreach( $this->allinclpar as $par ){
			
			if( !$addoninclude->read_kimb_search_teilpl( $par , $addon ) ){
				if( file_exists(__DIR__.'/../../addons/'.$addon.'/include_'.$par.'.php') ){
					$addoninclude->write_kimb_teilpl( $par , $addon , 'add' );
				}
			}
			else{
				$addoninclude->write_kimb_teilpl( $par , $addon , 'del' );
			}
		}
	
		$sitecontent->echo_message( 'Der Status des Add-ons "'.$addon.'" wurde geändert!' );
		
		return;
	}
	
	public function check_addon_upd(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$addoninclude = $this->addoninclude;
		
		if( ini_get( 'allow_url_fopen' ) ){
	
			$addons = listaddons();
	
			$addoninclude->write_kimb_one( 'lastcheck', time() );
	
			$querypar = implode( ',' , $addons );
			$addwert = json_decode( file_get_contents( 'http://api.kimb-technologies.eu/cms/addon/getcurrentversion.php?addon='.$querypar ) , true );
	
			foreach( $addwert as $ver ){
	
				$addon = $ver['addon'];
	
				if( in_array( $addon, $addons ) ){
		
						$oldini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);
	
						if( compare_cms_vers( $ver['aktuell'], $oldini['inst']['addonversion'] ) == 'newer' && $ver['err'] == 'no' ){
							$addoninclude->write_kimb_id( '21' , 'add' , $addon , 'upd' );
						}
						elseif( $ver['err'] == 'no' ){
							$addoninclude->write_kimb_id( '21' , 'add' , $addon , 'noup' );
						}
						else{
							$addoninclude->write_kimb_id( '21' , 'add' , $addon , '---empty---' );
						}
	
				}
			}
	
			$sitecontent->echo_message( 'Die Aktualität der Add-ons wurde überprüft!' );
			
			return true;
		}
		else{
			$sitecontent->echo_message( 'Ihr Server erlaubt PHP keine Requests per HTTP zu anderen Servern!' );
			
			return false;
		}
	}
	
	public function show_addon_news( $addon ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		$oldini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);

		$sitecontent->add_site_content('<h2>Infos zum Add-on "'.$oldini['about']['name'].'"</h2>');
	
		$sitecontent->add_site_content('<table>');
		$sitecontent->add_site_content('<tr><td><b>Name:</b></td><td>'.$oldini['about']['name'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Version:</b></td><td>'.$oldini['inst']['addonversion'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>CMS Version (min - max):</b></td><td>'.$oldini['inst']['mincmsv'].' - '.$oldini['inst']['maxcmsv'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td colspan="2" ></td></tr>');
		$sitecontent->add_site_content('<tr><td><b>By:</b></td><td>'.$oldini['about']['by'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Lizenz:</b></td><td>'.$oldini['about']['lic'].'</td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Homepage</b></td><td><a href="'.$oldini['about']['url'].'" target="_blank">Besuchen!</a></td></tr>');
		$sitecontent->add_site_content('<tr><td><b>Upadates und Downloads</b></td><td><a href="'.$oldini['about']['updurl'].'" target="_blank">Besuchen!</a></td></tr>');
		if( !empty( $oldini['about']['infot'] ) ){
			$sitecontent->add_site_content('<tr><td><b>Information</b></td><td>'.$oldini['about']['infot'].'</td></tr>');
		}
		$sitecontent->add_site_content('</table>');
	
		$sitecontent->add_site_content('<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php">&larr; Zur Add-on Seite</a>');

		return;
		
	}
	
	public function del_addon( $addon ){
		$sitecontent = $this->sitecontent;
		$addoninclude = $this->addoninclude;
		
		if( is_dir( __DIR__.'/../../addons/'.$addon.'/' ) ){
			rm_r( __DIR__.'/../../addons/'.$addon.'/' );
		}
		if( is_dir( __DIR__.'/../../../load/addondata/'.$addon.'/' ) ){
			rm_r( __DIR__.'/../../../load/addondata/'.$addon.'/' );
		}
		foreach( $this->allinclpar as $par ){
			if ( $addoninclude->read_kimb_search_teilpl( $par , $addon ) ){
				$addoninclude->write_kimb_teilpl( $par , $addon , 'del' );
			}
		}
	
		$sitecontent->echo_message( 'Das Add-on "'.$addon.'" wurde gelöscht!' );
	}
	
	public function install_addon( $file ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		if( !mkdir( __DIR__.'/../../addons/temp/' ) ){
			$sitecontent->echo_error( 'Es konnte kein Installationsverzeichnis erstellt werden!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}
	
		if( !chmod( __DIR__.'/../../addons/temp/' , ( fileperms( __DIR__.'/../../addons/' ) & 0777) ) ){
			$sitecontent->echo_error( 'Es konnte kein Installationsverzeichnis eingerichtet werden!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}
	
		$zip = new ZipArchive;
		if ($zip->open( $file ) === TRUE) {
			$zip->extractTo( __DIR__.'/../../addons/temp/' );
			$zip->close();
		}
		else{
			$sitecontent->echo_error( 'Die Add-on Datei konnte nicht geöffnet werden!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}
	
		$addonini = parse_ini_file( __DIR__.'/../../addons/temp/add-on.ini' , true);
	
		$name = $addonini['inst']['name'];
	
		if( $name == 'temp' ){
			rm_r( __DIR__.'/../../addons/temp/' );
			$sitecontent->echo_error( 'Ein Add-on darf nicht "temp" heißen!' );
			$sitecontent->output_complete_site();
			die;
		}
	
		if( compare_cms_vers( $addonini['inst']['mincmsv'], $allgsysconf['build'] ) == 'newer' ){
			rm_r( __DIR__.'/../../addons/temp/' );
			$sitecontent->echo_error( 'Sie haben eine zu alte Version des CMS für das Add-on "'.$name.'" !' );
			$sitecontent->output_complete_site();
			die;
		}
		if( compare_cms_vers( $allgsysconf['build'], $addonini['inst']['maxcmsv'] ) == 'newer' ){
			rm_r( __DIR__.'/../../addons/temp/' );
			$sitecontent->echo_error( 'Sie haben eine zu neue Version des CMS für das Add-on "'.$name.'" !' );
			$sitecontent->output_complete_site();
			die;
		}
	
		if( is_dir( __DIR__.'/../../addons/'.$name.'/' ) ){
			
			$oldini = parse_ini_file( __DIR__.'/../../addons/'.$name.'/add-on.ini' , true);
	
			if( compare_cms_vers( $oldini['inst']['addonversion'], $addonini['inst']['addonversion'] ) == 'older' ){
				$sitecontent->echo_message( 'Das Add-on "'.$name.'" wurde mit dieser Installation aktualisiert!' );
			}
			else{
				rm_r( __DIR__.'/../../addons/temp/' );
				$sitecontent->echo_error( 'Das Add-on "'.$name.'" ist bereits installiert!' );
				$sitecontent->output_complete_site();
				die;
			}
		}
	
		copy( __DIR__.'/../../addons/temp/add-on.ini', __DIR__.'/../../addons/temp/addon/add-on.ini' );
		copy_r( __DIR__.'/../../addons/temp/addon' , __DIR__.'/../../addons/'.$name.'/' );
		copy_r( __DIR__.'/../../addons/temp/load' , __DIR__.'/../../../load/addondata/'.$name.'/' );
	
		if( file_exists( __DIR__.'/../../addons/temp/install.php' ) ){
			require( __DIR__.'/../../addons/temp/install.php' );
		}
	
		if( !rm_r( __DIR__.'/../../addons/temp/' ) ){
			$sitecontent->echo_error( 'Die Installationsdateien konnte nicht gelöscht werden!' );
			$sitecontent->output_complete_site();
			die;
		}
	
		$sitecontent->echo_message( 'Das Add-on "'.$name.'" wurde installiert!' );
	}
	
	public function show_list(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$addoninclude = $this->addoninclude;
		
		$this->jsobject->for_addon_inst();
	
		$sitecontent->add_site_content('<h2>Add-onliste</h2>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th>Name</th> <th>Status</th> <th>Updates</th> <th>Löschen</th> </tr>');
		$addons = listaddons();
	
		//Infos zu alt, dann ? anzeigen
		$lastcheck = $addoninclude->read_kimb_one( 'lastcheck' );
		if( $lastcheck + 259200 > time() ){
			$updinfos = $addoninclude->read_kimb_id( '21' );
		}
		else{
			$updinfos = array();
		}
	
		foreach( $addons as $addon ){
	
			$oldini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);
	
			$link = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=addonnews&addon='.$addon.'">'.$oldini['about']['name'].'</a>';
	
			$del = '<span onclick="var delet = del( \''.$addon.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Add-on löschen." style="display:inline-block;" ></span></span>';
	
			if ( check_addon_status( $addon, $addoninclude, $this->allinclpar) ){
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=chdeak&amp;addon='.$addon.'"><span class="ui-icon ui-icon-check" style="display:inline-block;" title="Dieses Add-on ist zu Zeit aktiviert. ( click -> ändern )"></span></a>';
			}
			else{
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=chdeak&amp;addon='.$addon.'"><span class="ui-icon ui-icon-close" style="display:inline-block;" title="Dieses Add-on ist zu Zeit deaktiviert. ( click -> ändern )"></span></a>';
			}
	
			if( empty( $updinfos[$addon] ) ){
				$upd = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=checkall"><span class="ui-icon ui-icon-help" style="display:inline-block;" title="Bitte klicken Sie für eine Abfrage! ( Wenn Sie gerade eine Abfrage gemacht haben, ist dieses Add-on nicht in der Datenbank! )"></span></a>';
			}
			elseif( $updinfos[$addon] == 'noup' ){
				$upd = '<span class="ui-icon ui-icon-check" style="display:inline-block;" title="Das Add-on ist aktuell!"></span>';
			}
			elseif( $updinfos[$addon] == 'upd' ){
				$upd = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=addonnews&addon='.$addon.'"><span class="ui-icon ui-icon-alert" style="display:inline-block;" title="Es gibt ein Update für dieses Add-on!"></span>';
			}
				
			$sitecontent->add_site_content('<tr> <td>'.$link.'</td> <td>'.$status.'</td> <td>'.$upd.'</td> <td>'.$del.'</td> </tr>');
	
			$liste = 'yes';
	
		}
		$sitecontent->add_site_content('</table>');
	
		if( $liste != 'yes' ){
			$sitecontent->echo_error( 'Es wurden keine Add-ons gefunden!' );
		}
	
	
		$sitecontent->add_site_content('<br /><br /><h2>Add-on installieren</h2>');
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php" enctype="multipart/form-data" method="post">');
		$sitecontent->add_site_content('<input name="userfile" type="file" /><br />');
		$sitecontent->add_site_content('<input type="submit" value="Installieren" title="Wählen Sie eine Add-on Datei (&apos;*.kimbadd&apos;) von Ihrem Rechner zur Installation." />');
		$sitecontent->add_site_content('</form>');
	}
		
}
?>
