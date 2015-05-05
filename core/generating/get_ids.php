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

//mache aus Links oder RequestID => MenueID und SiteID

if( isset($_SERVER['REQUEST_URI']) && $allgsysconf['urlrewrite'] == 'on' && !isset($_GET['id']) && $allgsysconf['use_request_url'] == 'ok'){
	$_GET['url'] = $_SERVER['REQUEST_URI'];
}

if( isset($_GET['url']) && $allgsysconf['urlrewrite'] == 'on' && !isset($_GET['id']) ){

	// alte URL zu neuer

	$oldurlfile = new KIMBdbf( 'menue/oldurl.kimb' );

	$oldurl = $oldurlfile->search_kimb_xxxid( $_GET['url'] , 'url' );

	if( $oldurl != false ){
		$newrequid = $oldurlfile->read_kimb_id( $oldurl , 'id' );
		open_url( make_path_outof_reqid( $newrequid ), 'insystem', 301 );
	}

	// URL => RequestID

	$urlteile = explode( '/' , $_GET['url'] );

	$i = '0';
	if($urlteile[$i] == ''){
		$i++;
	}

	$pos = strpos( $allgsysconf['siteurl'] , '/' , 8 );
	$wegteil = substr( $allgsysconf['siteurl'] , $pos + 1 );
	$wegteile = explode( '/' , $wegteil );
	foreach( $wegteile as $teil ){
		if( $urlteile[$i] == $teil ){
			$i++;
		}
		else{
			break;
		}
	}
	
	if( $allgsysconf['lang'] == 'on'  && $urlteile[$i] != 'index.php'){
		$langfile = new KIMBdbf( 'site/langfile.kimb' );
		
		$langnull = $langfile->read_kimb_id( '0', 'tag' );
		$langid = (int) '0';
		if( empty( $urlteile[$i] ) ){
			$langid = false;
			$done = true;
		}
		elseif( $langnull != $urlteile[$i] ){
			$langid = $langfile->search_kimb_xxxid( $urlteile[$i] , 'tag' );
			$done = true;
		}
		
		if( $langid == false && $done ){
			$opennew = true;
			if( strlen( $urlteile[$i] ) == 2 ){
				$file = new KIMBdbf('url/first.kimb');
				if( $file->search_kimb_xxxid( $urlteile[$i] , 'path' ) == false ){
					$iplus = true;	
				}
			}
		}
		elseif( is_numeric( $langid ) ){
			$requestlang = $langfile->read_kimb_id( $langid );
			$requestlang['id'] = $langid; 
			if( $requestlang['status'] == 'off' ){
				$opennew = true;
				$iplus = true;
			}
		}
		
		if( $opennew ){
			
			$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach($langs as $lang){
				$lang = substr($lang, 0, 2);
				$langnull = $langfile->read_kimb_id( '0', 'tag' );
				if( $langnull == $lang ){
					$id = (int) '0';
					$okay = true;
				}
				else{
					$id = $langfile->search_kimb_xxxid( $lang , 'tag' );
					$okay = false;					
				}

				if( $id != false || $okay ){ 
					$langarr = $langfile->read_kimb_id( $id );
					if( $langarr['status'] == 'on' ){
						$url = '/'.$lang;
						break;
					}
				}
			}
			
			if( empty( $url ) ){
				$url = '/'.$langfile->read_kimb_id( '0', 'tag' );
			}
			
			if( $iplus ){
				$i++;
			}
			
			while( !empty( $urlteile[$i] ) ){
				$url .= '/'.$urlteile[$i];
				$i++;
			}
			
			open_url( $url );
			
			die;
		}
		else{
			$i++;
			
			$ii = $i;
			while( !empty( $urlteile[$ii] ) ){
				$url .= '/'.$urlteile[$ii];
				$ii++;
			}
			
			foreach( $langfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){
				$vals = $langfile->read_kimb_id( $id );
				if( $vals['status'] == 'on' ){
					$vals['thissite'] = $allgsysconf['siteurl'].'/'.$vals['tag'].$url;
					$allglangs[] = $vals;
				}
			}
		}
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
							$_GET['id'] = '0';
						}
						if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
							$sitecontent->echo_error( 'Fehlerhafte RequestURL !', '404' );
							$allgerr = '404';
							$_GET['id'] = '0';
						}
						break;
					}
				}
				else{
					$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
					$allgerr = '404';
					$_GET['id'] = '0';
					break;
				}
			}		
		}
		else{
			$_GET['id'] = $file->read_kimb_id( $ok , 'requestid' );
			if( $urlteile[$i] != '' ){
				$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
				$allgerr = '404';
				$_GET['id'] = '0';
			}
			if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
				$_GET['id'] = '1';
			}
		}
	}
	elseif( $urlteile[$i] == 'index.php' ){
		$_GET['id'] = '1';
		$idreq = true;
	}
	else{
		$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
		$allgerr = '404';
	}
	
}
elseif( isset($_GET['id']) ){
	
	$idreq = true;

	// RequestID => weiter gehts ...

	if( !is_numeric($_GET['id']) ){
		$sitecontent->echo_error( 'Fehlerhafte RequestID !' );
		$allgerr = 'unknown';
		$_GET['id'] = '1';
	}
}
else{

	$_GET['id'] = '1'; // Startseite
	
	$idreq = true;

}

if( $allgsysconf['lang'] == 'on' ){
	if( $idreq ){
		
		$langfile = new KIMBdbf( 'site/langfile.kimb' );
		
		if( is_numeric( $_SESSION['lang']['id'] ) && !is_numeric( $_GET['langid'] ) ){
			$_GET['langid'] = $_SESSION['lang']['id'];
		}
		
		if( is_numeric( $_GET['langid'] ) ){
			
			$requestlang = $langfile->read_kimb_id( $_GET['langid'] );
			$requestlang['id'] = $_GET['langid']; 
			if( $requestlang['status'] == 'off' ){
				$nolangidset = true;
			}
		}
		
		if( $nolangidset || !is_numeric( $_GET['langid'] ) ){
			$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach($langs as $lang){
				$lang = substr($lang, 0, 2);
				$langnull = $langfile->read_kimb_id( '0', 'tag' );
				if( $langnull == $lang ){
					$id = (int) '0';
					$okay = true;
				}
				else{
					$id = $langfile->search_kimb_xxxid( $lang , 'tag' );
					$okay = false;					
				}

				if( $id != false || $okay ){ 
					$langarr = $langfile->read_kimb_id( $id );
					if( $langarr['status'] == 'on' ){
						$langid = $id;
						break;
					}
				}
			}
			
			open_url( '/index.php?id='.$_GET['id'].'&langid='.$langid );
			
			die;
		}	
		
			foreach( $langfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){
				$vals = $langfile->read_kimb_id( $id );
				if( $vals['status'] == 'on' ){
					$vals['thissite'] = $allgsysconf['siteurl'].'/index.php?id='.$_GET['id'].'&langid='.$id;
					$allglangs[] = $vals;
				}
			}
	}
	
	header( 'Content-Language: '.$requestlang['tag'] );
	
	$sitecontent->set_lang( $allglangs, $requestlang );
	
	$_SESSION['lang'] = $requestlang;
}

if( $allgerr != '404' ){
	// get MenueID && get SiteID
	
	$idfile = new KIMBdbf('menue/allids.kimb');
	
	$allgsiteid = $idfile->read_kimb_id($_GET['id'], 'siteid');
	
	$allgmenueid = $idfile->read_kimb_id($_GET['id'], 'menueid');
	
	if( $allgsiteid == ''  || $allgmenueid == '' || $allgsiteid == false  || $allgmenueid == false ){
		$sitecontent->echo_error( 'Fehlerhafte RequestID Zuordnung!' , '404' );
		$allgerr = '404';
	}
	
	//Weitergabe von $allgsiteid, $allgmenueid, $allgerr
}
else{
	$allgsiteid = 0;
	$allgmenueid = 0;
	$_GET['id'] = '0';
}

?>
