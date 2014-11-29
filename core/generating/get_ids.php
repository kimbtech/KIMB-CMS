<?php

//mache aus Links oder RequestID => MenueID und SiteID

if( isset($_GET['url']) ){

	// URL => RequestID

	$urlteile = explode( '/' , $_GET['url'] );

	$i = '0';
	if($urlteile[$i] == ''){
		$i++;
	}

	$file = new KIMBdbf('/url/first.kimb');
	$ok = $file->search_kimb_xxxid( $urlteile[$i] , 'path' );
	if( $ok != false){
		$nextid = $file->read_kimb_id( $ok , 'nextid' );
		$i++;
		if( is_numeric( $nextid ) && $nextid != '' && $urlteile[$i] != '' ){
			while( 5 == 5 ){
				$file = new KIMBdbf('/url/nextid_'.$nextid.'.kimb');
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
						}
						if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
							$sitecontent->echo_error( 'Fehlerhafte RequestURL !' );
							$_GET['id'] = '1';
						}
						break;
					}
				}
				else{
					$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
					$err = 404;
					break;
				}
			}		
		}
		else{
			$_GET['id'] = $file->read_kimb_id( $ok , 'requestid' );
			if( $urlteile[$i] != '' ){
				$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
			}
			if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
				$sitecontent->echo_error( 'Fehlerhafte RequestURL !' );
				$_GET['id'] = '1';
			}
		}
	}
	else{
		$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
		$err = 404;
	}
	
}
elseif( isset($_GET['id']) ){

	// RequestID => weiter gehts ...

	if( !is_numeric($_GET['id']) ){
		$sitecontent->echo_error( 'Fehlerhafte RequestID !' );

		$_GET['id'] = '1';

	}
}
else{

	$_GET['id'] = '1'; // Startseite

}

// get MenueID

echo '<br />RequestID: '.$_GET['id'];

// get SiteID




?>
