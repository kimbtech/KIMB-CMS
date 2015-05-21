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

class BEaddconf{
	
	protected $allgsysconf, $sitecontent;
	
	public function __construct( $allgsysconf, $sitecontent ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
	}
	
	public function get_addon_name( $addon ){ 
		if( is_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' ) ){
	
			$ini = parse_ini_file( __DIR__.'/../../addons/'.$addon.'/add-on.ini' , true);
			return $ini['about']['name'];
		}
		else{
			return $addon;
		}
	} 
	
	public function make_addon_list( $way ){
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		
		$ret = '<table width="100%"><tr> <th>Add-on</th> </tr>';

		$addons = listaddons();
		foreach( $addons as $addon ){
			
			$ret .= '<tr> <td><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo='.$way.'&amp;addon='.$addon.'">'.$addon.'</a></td> </tr>';

			$liste = 'yes';

		}
		$ret .= '</table>';

		if( $liste != 'yes' ){
			$ret =  'Es wurden keine Add-ons gefunden!' ;
		}
		
		return $ret;
	}
	
	public function make_less(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		if( isset( $_GET['addon'] ) ){
			
			$addonname = $this->get_addon_name( $_GET['addon'] );
	
			$sitecontent->add_site_content('<h2>Add-on "'.$addonname.'" nutzen</h2>');
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less">&larr; Alle Add-ons</a>');
			if( check_backend_login( 'fourteen' , 'more', false) ){
				$sitecontent->add_site_content('<a style="position:absolute; right:12px;" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon='.$_GET['addon'].'">Zur Add-on Konfiguration &rarr;</a>');
			}
			$sitecontent->add_site_content('<hr />');
	
			if( file_exists(__DIR__.'/../../addons/'.$_GET['addon'].'/conf_less.php') ){
				require_once( __DIR__.'/../../addons/'.$_GET['addon'].'/conf_less.php' );
			}
			else{
				$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
			}
		}
		else{
			$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
			$sitecontent->add_site_content( $this->make_addon_list( 'less' ) );
		}	
		
		return;
	}
	
	public function make_more(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		
		if( isset( $_GET['addon'] ) ){
			
			$addonname = $this->get_addon_name( $_GET['addon'] );

			$sitecontent->add_site_content('<h2>Add-on "'.$addonname.'" konfigurieren</h2>');
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more">&larr; Alle Add-ons</a>');
			if( check_backend_login( 'thirteen' , 'less', false) ){
				$sitecontent->add_site_content('<a style="position:absolute; right:12px;" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon='.$_GET['addon'].'">Zur Add-on Nutzung &rarr;</a><hr />');
			}
	
			if( file_exists(__DIR__.'/../../addons/'.$_GET['addon'].'/conf_more.php') ){
				require_once( __DIR__.'/../../addons/'.$_GET['addon'].'/conf_more.php' );
			}
			else{
				$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
			}
		}
		else{
			$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
			$sitecontent->add_site_content( $this->make_addon_list( 'more' ) );
		}
		
		return;
	}
		
}
?>
