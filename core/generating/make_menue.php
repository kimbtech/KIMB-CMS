<?php

defined('KIMB_CMS') or die('No clean Request');

if(!is_object( $idfile )){
	$idfile = new KIMBdbf('menue/allids.kimb');
}

if($allgmenueid == $idfile->read_kimb_id($_GET['id'], 'menueid')){
	$allgrequestid = $_GET['id'];
}
else{
	$allgrequestid = search_kimb_xxxid( $allgmenueid , 'menueid' );
}

if( $allgsysconf['cache'] == 'on' ){
	if( $sitecache->load_cached_menue($allgmenueid) ){
		$menuecache = 'loaded';
	}
}

if( $allgsysconf['urlrewrite'] == 'on' && $menuecache != 'loaded' && isset($_GET['url']) ){

	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	$file = new KIMBdbf('url/first.kimb');
	$ii = '1';
	while( 5 == 5){
		$path = $file->read_kimb_id($ii, 'path');
		$requid = $file->read_kimb_id($ii, 'requestid');
		if( $allgrequestid == $requid ){
			$clicked = 'yes';
		}
		else{
			$clicked = 'no';
		}
		$menuname = $menuenames->read_kimb_one( $requid );

		if( $path == '' ){
			break;
		}
		$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/'.$path.'/' , '1', $clicked);
		if(is_object($sitecache)){
			$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/'.$path.'/', '1', $clicked);
		}
		$ii++;
	}	

	$return = gen_menue_id( $allgrequestid , $file , $menuenames );

	if( $return == 'none' ){
		$file = new KIMBdbf('url/first.kimb');
		$id = $file->search_kimb_xxxid( $allgrequestid , 'requestid' );
		$nextid = $file->read_kimb_id( $id , 'nextid');
		if( $nextid != false ){
			$filenos[] = $nextid;
		}
	}

	foreach( $wayfile as $fileno ){
		if( $fileno != '' ){
			if( $first != 'done' ){
				$file = new KIMBdbf('url/nextid_'.$fileno.'.kimb');
				$id = $file->search_kimb_xxxid( $allgrequestid , 'requestid' );
				$nextid = $file->read_kimb_id( $id , 'nextid');
				if( $nextid != false ){
					$filenos[] = $nextid;
				}
				$first = 'done';
				$filenos[] = $fileno;
			}
			else{
				$filenos[] = $fileno;
			}
		}
	}
	$niveau = count($filenos) +1 ;
	
	$filenos = array_reverse( $filenos );

	foreach( $filenos as $fileno ){
		if( $fileno != '' ){
			$file = new KIMBdbf('url/nextid_'.$fileno.'.kimb');
			$ii = '1';
			while( 5 == 5){
				$path = $file->read_kimb_id($ii, 'path');
				$requid = $file->read_kimb_id($ii, 'requestid');
				if( $allgrequestid == $requid ){
					$clicked = 'yes';
				}
				else{
					$clicked = 'no';
				}
				$menuname = $menuenames->read_kimb_one( $requid );

				if( $path == '' ){
					break;
				}
				if( $file->read_kimb_id( $ii , 'status') == 'on' ){
					$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/'.$path , $niveau, $clicked);  //Path nötig !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					if(is_object($sitecache)){
						$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].'/'.$path , $niveau , $clicked);  //Path nötig !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					}
				}
				$ii++;
			}
			$niveau = $niveau-1;
		}
	}

}
elseif( $menuecache != 'loaded'){
	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	$file = new KIMBdbf('url/first.kimb');
	$ii = '1';
	while( 5 == 5){
		$path = $file->read_kimb_id($ii, 'path');
		$requid = $file->read_kimb_id($ii, 'requestid');
		if( $allgrequestid == $requid ){
			$clicked = 'yes';
		}
		else{
			$clicked = 'no';
		}
		$menuname = $menuenames->read_kimb_one( $requid );

		if( $path == '' ){
			break;
		}
		if( $file->read_kimb_id( $ii , 'status') == 'on' ){
			$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , '1', $clicked);
			if(is_object($sitecache)){
				$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , '1', $clicked);
			}
		}
		$ii++;
	}

	$return = gen_menue_id( $allgrequestid , $file , $menuenames );

	if( $return == 'none' ){
		$file = new KIMBdbf('url/first.kimb');
		$id = $file->search_kimb_xxxid( $allgrequestid , 'requestid' );
		$nextid = $file->read_kimb_id( $id , 'nextid');
		if( $nextid != false ){
			$filenos[] = $nextid;
		}
	}

	foreach( $wayfile as $fileno ){
		if( $fileno != '' ){
			if( $first != 'done' ){
				$file = new KIMBdbf('url/nextid_'.$fileno.'.kimb');
				$id = $file->search_kimb_xxxid( $allgrequestid , 'requestid' );
				$nextid = $file->read_kimb_id( $id , 'nextid');
				if( $nextid != false ){
					$filenos[] = $nextid;
				}
				$first = 'done';
				$filenos[] = $fileno;
			}
			else{
				$filenos[] = $fileno;
			}
		}
	}
	$niveau = count($filenos) +1 ;
	foreach( $filenos as $fileno ){
		if( $fileno != '' ){
			$file = new KIMBdbf('url/nextid_'.$fileno.'.kimb');
			$ii = '1';
			while( 5 == 5){
				$path = $file->read_kimb_id($ii, 'path');
				$requid = $file->read_kimb_id($ii, 'requestid');
				if( $allgrequestid == $requid ){
					$clicked = 'yes';
				}
				else{
					$clicked = 'no';
				}
				$menuname = $menuenames->read_kimb_one( $requid );

				if( $path == '' ){
					break;
				}
				if( $file->read_kimb_id( $ii , 'status') == 'on' ){
					$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau, $clicked);
					if(is_object($sitecache)){
						$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau , $clicked);
					}
				}
				$ii++;
			}
			$niveau = $niveau-1;
		}
	}
}

?>
