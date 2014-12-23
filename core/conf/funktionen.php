<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS
//KIMB ContentManagementSystem
//KIMB-technologies.blogspot.com
/*************************************************/
//CC BY-ND 4.0
//http://creativecommons.org/licenses/by-nd/4.0/
//http://creativecommons.org/licenses/by-nd/4.0/legalcode
/*************************************************/
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
//BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
//IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
//IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
/*************************************************/



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
	if(strpos($datei, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
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
	if(strpos($datei, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
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
			if( is_object( $sitecontent ) ){
				$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
				$sitecontent->output_complete_site();
			}
			else{
				echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
			}
			die;
		}
		return true;
	}
	else{
		if( is_object( $sitecontent ) ){
				$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
				$sitecontent->output_complete_site();
		}
		else{
			echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
		}
		die;
	}
}

//kimb datei umbenennen
function rename_kimbdbf( $datei1 , $datei2 ){
	$datei1 = preg_replace('/[\r\n]+/', '', $datei1);
	$datei1 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei1);
	$datei2 = preg_replace('/[\r\n]+/', '', $datei2);
	$datei2 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei2);

	if(strpos($datei2, "..") !== false || strpos($datei1, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}


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
				rm_r($dir.'/'.$file);
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
				zip_r($zip, $dir.'/'.$file, $base.$file.'/');
			}
		}
	}
	return true;
}

function copy_r( $dir , $dest ){

	if( !is_dir( $dest ) ){
		mkdir( $dest );
		chmod( $dest , ( fileperms( $dest.'/../' ) & 0777));
	}
	
	$files = scandir( $dir );
	foreach ($files as $file){
		if ($file == '..' || $file == '.'){
			//nichts
		}
		else{
			if ( is_file($dir.'/'.$file) ){
				copy( $dir.'/'.$file , $dest.'/'.$file );
			}
       			elseif ( is_dir($dir.'/'.$file) ){
				copy_r( $dir.'/'.$file , $dest.'/'.$file );
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
					$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].$grpath.$path , $niveau , $clicked);
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

function listaddons(){

	$files = scandir(__DIR__.'/../addons/');
	foreach ($files as $file) {
		if( $file != '.' && $file != '..' &&  is_dir(__DIR__.'/../addons/'.$file) ){
			$read[] = $file;
		}
	}
	return $read;
}

function makepassw( $laenge , $chars = '!"#%&()*+,-./:;?[\]_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' ){
	$anzahl = strlen($chars);
	$i = '1';
	$output = '';
	while($i <= $laenge){
		$stelle = mt_rand('0', $anzahl); 
		$output .= $chars{$stelle};
		$i++;
	}
	return $output;
}
?>
