<?php

class cacheCMS{

	protected $menue, $site, $siteid, $menueid;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
	}

	public function cache_menue($id, $inhalt, $niveau){
		if( $this->menueid == ''){
			$this->menueid = $id;
		}
		if( $this->menueid != $id ){
			return false;
		} 

		if( $this->menue[$niveau] == '' ){
			$this->menue[$niveau] = time()."\n\r";
		}
		$this->menue[$niveau] .= $inhalt."\n\r";

		return true;
	}
	public function cache_site($id, $inhalt){
		if( $this->siteid == ''){
			$this->siteid = $id;
		}
		if( $this->siteid != $id ){
			return false;
		} 

		if( $this->site == '' ){
			$this->site = time()."\n\r";
		}
		$this->site .= $inhalt."\n\r";
		
		return true;
	}

	public function __destruct(){

		$i = '1';
		foreach ( $this->menue as $inhalt ){
			if($inhalt != ''){
				$handle = fopen(__DIR__.'/../cache/menue'.$id.'_niveau'.$i.'.cache', 'w+');
				fwrite($handle, $inhalt);
				fclose($handle);
			}
			$i++;
		}

		if($this->site != '' ){
			$handle = fopen(__DIR__.'/../cache/site'.$id.'.cache', 'w+');
			fwrite($handle, $this->site);
			fclose($handle);
		}
	}

	public function get_cached_menue($id){
		echo 'max';
	}

	public function get_cached_site($id){
		if( file_exists( __DIR__.'/../cache/site'.$id.'.cache' ) ){
			//zeile 1 Zeit okay ??
			//zeile für Zeile vorlesen in array (0- XX)
		}
	}

}

?>
