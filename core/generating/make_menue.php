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
		//$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?url=/'.$path.'/' , '1', $clicked);
		$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/'.$path.'/' , '1', $clicked);
		if(is_object($sitecache)){
			//$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/index.php?url=/'.$path.'/', '1', $clicked);
			$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/'.$path.'/', '1', $clicked);
		}
		$ii++;
	}	
/*
	$i = '0';
	if($urlteile[$i] == ''){
		$i++;
	}
	$ok = $file->search_kimb_xxxid( $urlteile[$i] , 'path' );
	if( $ok != false){
		$nextid = $file->read_kimb_id( $ok , 'nextid' );
		if( $allgerr == '' && !$file->search_kimb_xxxid( $allgrequestid , 'requestid' ) ){
			$return = godeeper_menue($allgrequestid, $nextid, $menuenames, $urlteile, $i, $allgmenueid);
			$file = $return['file'];
			$niveau = $return['niveau'];
		}
		else{
			$niveau = 2;
			$file = new KIMBdbf('url/nextid_'.$nextid.'.kimb');

		}
		
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
			//$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?url=/'.$urlteile[$i].'/'.$path.'/' , $niveau, $clicked);
			$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/'.$urlteile[$i].'/'.$path.'/' , $niveau, $clicked);
			if(is_object($sitecache)){
				//$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/index.php?url=/'.$urlteile[$i].'/'.$path.'/', $niveau, $clicked);
				$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/'.$urlteile[$i].'/'.$path.'/', $niveau, $clicked);
			}
			$ii++;
		}
	}*/

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

/*
	$ok = $file->search_kimb_xxxid( $allgrequestid , 'requestid' );
	$okk = $ok;
	if( $ok == false ){
		$i = 1;
		while( 5 == 5 ){
			$nextid = $file->read_kimb_id($i, 'nextid');
			if( $nextid != '' ){
				$fileone = new KIMBdbf('url/nextid_'.$nextid.'.kimb');
				$filenextone = 'url/nextid_'.$nextid.'.kimb';
				$ione = 1;
				$ok = $fileone->search_kimb_xxxid( $allgrequestid , 'requestid' );
				if( $ok == false ){
					while( 5 == 5 ){
						$nextid = $fileone->read_kimb_id($ione, 'nextid');
						if( $nextid != '' ){
							$filetwo = new KIMBdbf('url/nextid_'.$nextid.'.kimb');
							$filenexttwo = 'url/nextid_'.$nextid.'.kimb';
							$ok = $filetwo->search_kimb_xxxid( $allgrequestid , 'requestid' );
							if( $ok == false ){
								break;
							}
							else{
								$wayfile[0] = $filenextone;
								$wayfile[1] = $filenexttwo;
								break;
							}
						}
						elseif( $fileone->read_kimb_id($ione, 'requestid') == '' ){
							break;
						}
						$ione++;
					}
				}
				else{
					$wayfile[0] = $filenextone; 
					break;
				}
				if( $wayfile[1] != '' ){
					break;
				}
			}
			elseif( $file->read_kimb_id($i, 'requestid') == '' ){
				break;
			}
			elseif( $wayfile[0] != ''){
				break;
			}
			$i++;
		}
	}
	
	if( $okk != false){
		$nextid = $file->read_kimb_id( $okk , 'nextid' );
		$niveau = 2;
		$file = new KIMBdbf('url/nextid_'.$nextid.'.kimb');
	}
	else{
		$ok = $fileone->search_kimb_xxxid( $allgrequestid , 'requestid' );
		$nextid = $fileone->read_kimb_id($ok, 'nextid' );
		$file = new KIMBdbf('url/nextid_'.$nextid.'.kimb');
		$niveau = 3;
	}

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
		$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau , $clicked);
		if(is_object($sitecache)){
			$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau , $clicked);
		}
		$ii++;
	}


	$niveau = 2;
	foreach( $wayfile as $way ){
		$file = new KIMBdbf($way);
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
			$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau, $clicked);
			if(is_object($sitecache)){
				$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau , $clicked);
			}
			$ii++;
		}
		$niveau++;
	}*/
}

?>
