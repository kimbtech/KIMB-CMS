<?php

defined('KIMB_CMS') or die('No clean Request');

//email versenden
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

//browser an url weiterleiten
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

//schaauen ob kimb datei vorhanden
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

//alle kimb dateien in verzeichnis ausgeben
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

//backendlogin prüfen und error ausgeben
function check_backend_login( $permiss = 'none'){
	global $sitecontent, $allgsysconf;
	if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){
		if( $permiss == 'more' && $_SESSION['permission'] != 'more' ){
			$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
			$sitecontent->output_complete_site();
			die;
		}
		return true;
	}
	else{
		$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
		$sitecontent->output_complete_site();
		die;
	}
}

//kimb datei umbenennen
function rename_kimbdbf( $datei1 , $datei2 ){
	$datei1 = preg_replace('/[\r\n]+/', '', $datei1);
	$datei1 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei1);
	$datei2 = preg_replace('/[\r\n]+/', '', $datei2);
	$datei2 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei2);

	return rename( __DIR__.'/../oop/kimb-data/'.$datei1 , __DIR__.'/../oop/kimb-data/'.$datei2 );
}

//rekursiv leoschen
function rm_r($dir){
	$files = scandir($dir);
	foreach ($files as $file) {
		if($file == '.' || $file == '..'){
			//nichts
		}
		else{
			if(is_dir($dir.'/'.$file)){
				rmdirrec($dir.'/'.$file);
			}
			else{
				unlink($dir.'/'.$file);
			}
		}
	}
	return rmdir($dir);
}

//rekursiv zippen
function zip_r($zip, $dir, $base = '/'){
	if (!file_exists($dir)){
		return false;
	}
	$files = scandir($dir);

	foreach ($files as $file){
		if ($file == '..' || $file == '.'){
			//nichts
		}
		else{

			if (is_file($dir.'/'.$file)){
				$zip->addFile($dir.'/'.$file, $base.$file);
			}
       			elseif (is_dir($dir.'/'.$file)){
				$zip->addEmptyDir($base.$file);
				zipDir($zip, $dir.'/'.$file, $base.$file.'/');
			}
		}
	}
	return true;
}

//Menue
function gen_menue( $allgrequestid , $filename = 'url/first.kimb' , $grpath = '/' , $niveau = '1'){
	global $sitecache, $sitecontent, $menuenames, $allgsysconf, $allgmenueid;

	$file = new KIMBdbf( $filename );
	$id = 1;
	while( 5 == 5 ){
		$requid = $file->read_kimb_id( $id , 'requestid' );
		$path = $file->read_kimb_id( $id , 'path' );
		$menuname = $menuenames->read_kimb_one( $requid );
		if( $allgrequestid == $requid ){
			$clicked = 'yes';
		}
		else{
			$clicked = 'no';
		}
		if( $path == '' ){
			return true;
		}
		if( $file->read_kimb_id( $id , 'status') == 'on' ){
			if( $allgsysconf['urlrewrite'] == 'on' ){
				$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].$grpath.$path , $niveau, $clicked);
				if(is_object($sitecache)){
					$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau , $clicked);
				}
			}
			else{
				$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau, $clicked);
				if(is_object($sitecache)){
					$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau , $clicked);
				}
			}
		}
		$nextid = $file->read_kimb_id( $id , 'nextid');
		if( $nextid != '' ){
			$newniveau = $niveau + 1;
			gen_menue( $allgrequestid , 'url/nextid_'.$nextid.'.kimb' , $grpath.$path.'/' , $newniveau);
		}
		$id++;
	}
}

function make_menue_array( $filename = 'url/first.kimb' , $niveau = '1' , $fileid = 'first' , $oldfilelisti = 'none'){
	global $menuenames, $idfile, $menuearray, $fileidlist, $filelisti;

	if( !isset( $filelisti ) ){
		$filelisti = 0;
	}
	else{
		$filelisti++;
	}

	$file = new KIMBdbf( $filename );
	$id = 1;
	while( 5 == 5 ){
		$path = $file->read_kimb_id( $id , 'path' );
		$nextid = $file->read_kimb_id( $id , 'nextid' );
		$requid = $file->read_kimb_id( $id , 'requestid' );
		$status = $file->read_kimb_id( $id , 'status');
		$menuname = $menuenames->read_kimb_one( $requid );
		$siteid = $idfile->read_kimb_id( $requid , 'siteid' );
		$menueid = $idfile->read_kimb_id( $requid , 'menueid' );
		$fileidbefore = $fileidlist[$oldfilelisti];

		if( $path == '' ){
			return true;
		}

		$fileidlist[$filelisti] = $fileid;

		$menuearray[] = array( 'niveau' => $niveau, 'path' => $path, 'nextid' => $nextid , 'requid' => $requid, 'status' => $status, 'menuname' => $menuname, 'siteid' => $siteid, 'menueid' => $menueid, 'fileid' => $fileid , 'fileidbefore' => $fileidbefore );

		if( $nextid != '' ){
			$newniveau = $niveau + 1;
			make_menue_array( 'url/nextid_'.$nextid.'.kimb' , $newniveau , $nextid , $filelisti );
		}
		$id++;
	}
}

?>
