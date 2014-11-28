<?php

class cacheCMS{

	protected $menuefile, $sitefile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
	}

	public function cache_menue($id, $name, $link, $niveau){
		if(!is_object($this->menuefile)){
			$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
			$this->menuefile->write_kimb_new( 'time' , time() );
		}
		$fileid = $this->menuefile->next_kimb_id();
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'link' , $link );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'name' , $name );
		$this->menuefile->write_kimb_id( $fileid , 'add' , 'niveau' , $niveau );

		return true;
	}
	public function cache_site($id, $inhalt){
		if(!is_object($this->sitefile)){
			$this->sitefile = new KIMBdbf('/cache/site_'.$id.'.kimb');
			$this->sitefile->write_kimb_new( 'time' , time() );
		}
		$this->sitefile->write_kimb_new( 'inhalt' , $inhalt );
		
		return true;
	}

	public function get_cached_menue($id){
		if(!is_object($this->menuefile)){
			$this->menuefile = new KIMBdbf('/cache/menue_'.$id.'.kimb');
		}
		$time = $this->menuefile->read_kimb_one( 'time' );
		if( time()-$time <= '172800' ){
			$fileid = '1';
			$i = '0';
			while($read[$i]['name'] != ''){
				$read[$i] = $this->menuefile->read_kimb_id( $fileid );
				$i++;
				$fileid++;
			}
			return $read;
		}
		return false;
	}

	public function get_cached_site($id){
		if(!is_object($this->sitefile)){
			$this->sitefile = new KIMBdbf('/cache/site_'.$id.'.kimb');
		}
		$time = $this->sitefile->read_kimb_one( 'time' );
		if( time()-$time <= '172800' ){
			return $this->sitefile->read_kimb_all( 'inhalt' );
		}
		return false;
		
	}

}

?>
