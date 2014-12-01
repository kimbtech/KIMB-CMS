<?php

//mache aus Links oder RequestID => MenueID und SiteID

if( isset($_SERVER['REQUEST_URI']) && $allgsysconf['urlrewrite'] == 'on' ){
	$_GET['url'] = $_SERVER['REQUEST_URI'];
}

if( isset($_GET['url']) ){

	// URL => RequestID

	$urlteile = explode( '/' , $_GET['url'] );

	$i = '0';
	if($urlteile[$i] == ''){
		$i++;
	}

	$file = new KIMBdbf('url/first.kimb');
	$ok = $file->search_kimb_xxxid( $urlteile[$i] , 'path' );
	if( $ok != false){
		$nextid = $file->read_kimb_id( $ok , 'nextid' );
		$i++;
		if( is_numeric( $nextid ) && $nextid != '' && $urlteile[$i] != '' ){
			while( 5 == 5 ){
				$file = new KIMBdbf('url/nextid_'.$nextid.'.kimb');
				$ok = $file->search_kimb_xxxid( $urlteile[$i] , 'path' );
				if( $ok != false){
					$nextid = $file->read_kimb_id( $ok , 'nextid' );
					$i++;
					if( is_numeric( $nextid ) && $nextid != '' && $urlteile[$i] != '' ){
						
					}
					else{
						$_GET['id'] = $file->read_kimb_id( $ok , 'requestid' );
						if( $urlteile[$i] != '' ){
							$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
							$allgerr = '404';
						}
						if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
							$sitecontent->echo_error( 'Fehlerhafte RequestURL !' );
							$allgerr = 'unknown';
							$_GET['id'] = '1';
						}
						break;
					}
				}
				else{
					$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
					$allgerr = '404';
					break;
				}
			}		
		}
		else{
			$_GET['id'] = $file->read_kimb_id( $ok , 'requestid' );
			if( $urlteile[$i] != '' ){
				$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
				$allgerr = '404';
			}
			if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
				$sitecontent->echo_error( 'Fehlerhafte RequestURL !' );
				$allgerr = 'unknown';
				$_GET['id'] = '1';
			}
		}
	}
	else{
		$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
		$allgerr = '404';
	}
	
}
elseif( isset($_GET['id']) ){

	// RequestID => weiter gehts ...

	if( !is_numeric($_GET['id']) ){
		$sitecontent->echo_error( 'Fehlerhafte RequestID !' );
		$allgerr = 'unknown';
		$_GET['id'] = '1';

	}
}
else{

	$_GET['id'] = '1'; // Startseite

}

// get MenueID && get SiteID

$idfile = new KIMBdbf('menue/allids.kimb');

$allgsiteid = $idfile->read_kimb_id($_GET['id'], 'siteid');

$allgmenueid = $idfile->read_kimb_id($_GET['id'], 'menueid');

if( $allgsiteid == ''  || $allgmenueid == '' || $allgsiteid == false  || $allgmenueid == false ){
	$sitecontent->echo_error( 'Fehlerhafte RequestID Zuordnung!' , '404' );
	$allgerr = '404';
}

//Weitergabe von $allgsiteid, $allgmenueid, $allgerr

?>
