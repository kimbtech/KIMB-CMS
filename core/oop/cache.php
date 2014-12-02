<?php

defined('KIMB_CMS') or die('No clean Request');

class cacheCMS{

	protected $menuefile, $sitefile, $sitecontent, $allgsysconf, $menue, $addon;

	public function __construct($allgsysconf, $sitecontent){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
	}

	public function cache_menue($id, $name, $link, $niveau, $clicked){
		if(!is_object($this->menuefile)){
			$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
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

		return true;
	}
	public function load_cached_menue($id){
		if(!is_object($this->menuefile)){
			$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
		}
		$time = $this->menuefile->read_kimb_one( 'time' );
		if(( time()-$time <= $this->allgsysconf['cachelifetime'] || $this->allgsysconf['cachelifetime'] == 'always' ) && $time != ''){
			$fileid = '1';
			while( 5 == 5){

				$name = $this->menuefile->read_kimb_id( $fileid , 'name');
				$link = $this->menuefile->read_kimb_id( $fileid , 'link');
				$niveau = $this->menuefile->read_kimb_id( $fileid , 'niveau');
				$clicked = $this->menuefile->read_kimb_id( $fileid , 'clicked');

				if( $name == '' ){
					break;
				}

				$this->sitecontent->add_menue_one_entry($name, $link, $niveau, $clicked );

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
			$this->sitefile->delete_kimb_file( );
			$this->sitefile->write_kimb_new( 'time' , time() );
			$this->addon = 'yes';
		}

		$this->sitefile->write_kimb_new( 'inhalt-'.$name , $inhalt );
		
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
