<?php

defined('KIMB_CMS') or die('No clean Request');

function send_mail($to, $inhalt){
	global $allgsysconf;
	if( $inhalt == '' || $to == ''){
		return false;
	}

	if(mail($to, 'Nachricht von: '.$allgsysconf['sitename'], $inhalt, 'From: '.$allgsysconf['sitename'].' <'.$allgsysconf['mailvon'].'>')){
		return true;
	}
	else{
		return false;
	}
}


function open_url($url, $area = 'insystem'){
	global $allgsysconf;

	if( $area == 'insystem'){
		$url = $allgsysconf['siteurl'].$url;
	}

	if($allgsysconf['urlweitermeth'] == '1'){
		header('Location: '.$url);
		die;
	}
	elseif($allgsysconf['urlweitermeth'] == '2'){
		echo('<meta http-equiv="Refresh" content="0; URL='.$url.'">');
		die;
	}
}

function check_for_kimb_file($datei){
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	if(file_exists(__DIR__.'/../oop/kimb-data/'.$datei)){
		return true;
	}
	else{
		return false;
	}
}

function scan_kimb_dir($datei){
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$files = scandir(__DIR__.'/../oop/kimb-data/'.$datei);
	$i = 0;
	foreach ( $files as $file ){
		if( $file != '.' && $file != '..' && $file != 'index.kimb' ){
			$return[$i] .= $file;
			$i++;
		}
	}
	return $return;
}

function godeeper_menue($allgrequestid, $nextidg , $menuenames , $urlteile, $i , $allgmenueid , $niveau = '3'){
	global $sitecontent, $sitecache;
	$file = new KIMBdbf('url/nextid_'.$nextidg.'.kimb');
	$i1 = $i+1;
	$ok = $file->search_kimb_xxxid( $urlteile[$i1] , 'path' );
	if( $ok != false){
		$nextid = $file->read_kimb_id( $ok , 'nextid' );
		$file = new KIMBdbf('url/nextid_'.$nextid.'.kimb');
		$ii = '1';
		$urlt .= $urlteile[$i].'/'.$urlteile[$i+1];
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
			//$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?url=/'.$urlt.'/'.$path.'/' , $niveau , $clicked);
			$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/'.$urlt.'/'.$path.'/' , $niveau , $clicked);
			if(is_object($sitecache)){
				//$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/index.php?url=/'.$urlt.'/'.$path.'/' , $niveau , $clicked);
				$sitecache->cache_menue($allgmenueid, $menuname, $allgsysconf['siteurl'].'/'.$urlt.'/'.$path.'/' , $niveau , $clicked);
			}
			$ii++;
		}
		$return['file'] = new KIMBdbf('url/nextid_'.$nextidg.'.kimb');
		$return['niveau'] = $niveau-1;
		return $return;
	}
	else{
		return false;
	}


}

?>
