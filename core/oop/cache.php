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

class cacheCMS{

	protected $menuefile, $sitefile, $sitecontent, $allgsysconf, $menue, $addon;

	public function __construct($allgsysconf, $sitecontent){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
	}

	public function cache_menue($id, $name, $link, $niveau, $clicked, $requid, $langid = 0 ){
		if(!is_object($this->menuefile)){	
			if( $langid != 0 ){
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'_lang_'.$langid.'.kimb');	
			}
			else{
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
			}
		}
		if( !isset( $this->menue )){
			$this->menuefile->delete_kimb_file( );
			$this->menuefile->write_kimb_new( 'time' , time() );
			$this->menue = 'yes';
		}
		$fileid = $this->menuefile->next_kimb_id();
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'link' , $link );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'name' , $name );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'niveau' , $niveau );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'clicked' , $clicked );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'requid' , $requid );

		return true;
	}
	public function load_cached_menue($id, $langid = 0){
		if(!is_object($this->menuefile)){	
			if( $langid != 0 ){
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'_lang_'.$langid.'.kimb');	
			}
			else{
				$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
			}
		}
		$time = $this->menuefile->read_kimb_one( 'time' );
		if(( time()-$time <= $this->allgsysconf['cachelifetime'] || $this->allgsysconf['cachelifetime'] == 'always' ) && $time != ''){
			$fileid = '1';
			while( 5 == 5){

				$name = $this->menuefile->read_kimb_id( $fileid , 'name');
				$link = $this->menuefile->read_kimb_id( $fileid , 'link');
				$niveau = $this->menuefile->read_kimb_id( $fileid , 'niveau');
				$clicked = $this->menuefile->read_kimb_id( $fileid , 'clicked');
				$requid = $this->menuefile->read_kimb_id( $fileid , 'requid');

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


	public function cache_addon( $id , $inhalt , $name = 'unknown'){
		if(!is_object($this->sitefile)){
			$this->sitefile = new KIMBdbf('/cache/addon_'.$id.'.kimb');
		}
		if( !isset( $this->addon )){
			$this->sitefile->write_kimb_one( 'time' , time() );
			$this->addon = 'yes';
		}

		$this->sitefile->write_kimb_one( 'inhalt-'.$name , $inhalt );
		
		return true;
	}

	public function get_cached_addon( $id , $name = 'unknown' ){
		if(!is_object($this->sitefile)){
			$this->sitefile = new KIMBdbf('/cache/addon_'.$id.'.kimb');
		}
		$time = $this->sitefile->read_kimb_one( 'time' );
		if(( time()-$time <= $this->allgsysconf['cachelifetime'] || $this->allgsysconf['cachelifetime'] == 'always' ) && $time != '' ){
			return $this->sitefile->read_kimb_all( 'inhalt-'.$name );
		}
		return false;
		
	}

}

?>
