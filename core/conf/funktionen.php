<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS
//KIMB ContentManagementSystem
//www.KIMB-technologies.eu
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
	$datei = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei );
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
function justnum( $str ) { return preg_replace( "/[^0-9]/" , "" , $str ); }

function scan_kimb_dir($datei){
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei );
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
	
	$returnref = array_map( 'justnum' , $return );
	array_multisort( $returnref, $return);

	return $return;
}

//request URL
function get_requ_url(){
	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	return $urlg;
}

//backendlogin prüfen und error ausgeben
function check_backend_login( $number , $permiss = 'none', $die = true ){
	global $sitecontent, $allgsysconf;

	if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

		if( $_SESSION['permission'] == 'more' || $_SESSION['permission'] == 'less' ){
			if( $permiss == 'more' && $_SESSION['permission'] != 'more' ){
				if( $die ){
					if( is_object( $sitecontent ) ){
						$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
						$sitecontent->output_complete_site();
					}
					else{
						echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
					}
					die;
				}
				else{
					return false;
				}
			}
			return true;
		}
		else{
			$levellist = new KIMBdbf( 'backend/users/level.kimb' );
			$permissteile = $levellist->read_kimb_one( $_SESSION['permission'] );
			if( !empty( $permissteile ) ){
					$permissteile = explode( ',' , $permissteile );
					if( !in_array( $number , $permissteile ) ){
						if( $die ){
							if( is_object( $sitecontent ) ){
								$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
								$sitecontent->output_complete_site();
							}
							else{
								echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
							}
							die;
						}
						else{
							return false;
						}
					}
					else{
						return true;
					}

			}
			else{
				if( $die ){
					if( is_object( $sitecontent ) ){
							$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
							$sitecontent->output_complete_site();
					}
					else{
						echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
					}
					die;
				}
				else{
					return false;
				}
			}
		}
	}
	else{
		if( $die ){
			if( is_object( $sitecontent ) ){
					$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
					$sitecontent->output_complete_site();
			}
			else{
				echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
			}
			die;
		}
		else{
			return false;
		}
	}
}

//kimb datei umbenennen
function rename_kimbdbf( $datei1 , $datei2 ){
	$datei1 = preg_replace('/[\r\n]+/', '', $datei1);
	$datei1 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei1);
	$datei1 = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei1 );

	$datei2 = preg_replace('/[\r\n]+/', '', $datei2);
	$datei2 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei2);
	$datei2 = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei2 );

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
	global $sitecache, $sitecontent, $menuenames, $allgsysconf, $allgmenueid, $breadcrumbarr, $breadarrfertig;

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

				if( $breadarrfertig == 'nok' ){
					$breadcrumbarr[$niveau]['link'] = $allgsysconf['siteurl'].$grpath.$path; 
					$breadcrumbarr[$niveau]['name'] = $menuname;

					if( $clicked == 'yes' ){
						$breadarrfertig = 'ok';
						$breadcrumbarr['maxniv'] = $niveau;
					}
				}

				if(is_object($sitecache)){
					$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].$grpath.$path , $niveau , $clicked);
				}
			}
			else{
				$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau, $clicked);

				if( $breadarrfertig == 'nok' ){
					$breadcrumbarr[$niveau]['link'] = $allgsysconf['siteurl'].'/index.php?id='.$requid; 
					$breadcrumbarr[$niveau]['name'] = $menuname;

					if( $clicked == 'yes' ){
						$breadarrfertig = 'ok';
						$breadcrumbarr['maxniv'] = $niveau;
					}
				}

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

function id_dropdown( $name, $id = 'siteid' ){
	global $idfile, $menuenames, $menuearray;
	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	if( !isset( $menuearray ) ){
		make_menue_array();
	}

	$return = '<select name="'.$name.'" >';

	foreach( $menuearray as $menuear ){

		$niveau = str_repeat( '==>' , $menuear['niveau'] );
		$valid = $menuear[$id];
		$menuename = $menuear['menuname'];

		$return .= '<option value="'.$valid.'">'.$niveau.' '.$menuename.' - '.$valid.'</option>';
	}

	$return .= '</select>';

	return $return;

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

function makepassw( $laenge , $chars = '!"#%&()*+,-./:;?[\]_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', $wa = 'off' ){
	if( $wa == 'az' ){
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}
	elseif( $wa == 'num' ){
		$chars = '0123456789';
	}
	elseif( $wa == 'numaz' ){
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}
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

function listdirrec_f( $dir, $grdir ){
	global $allgsysconf;
	
	$files = scandir( $dir );

	foreach( $files as $file ){

		if( $file == '..' || $file == '.' ){

		}
		elseif( is_file( $dir.'/'.$file ) ){

			$mime = mime_content_type ( $dir.'/'.$file );

			$mime = substr( $mime, 0, 5 ); 

			if( $mime == 'image' ){
				$out .= '{title: "'.$grdir.'/'.$file.'", value: "'.$allgsysconf['siteurl'].$grdir.'/'.$file.'"},';
			}
		}
		elseif( is_dir( $dir.'/'.$file ) ){
			$out .= listdirrec_f( $dir.'/'.$file , $grdir.'/'.$file );
		}
	}

	return $out;
}

function listdirrec( $dir, $grdir ){
	global $listdirrecold;

	if( !isset( $listdirrecold[$dir] ) ){
		$out = listdirrec_f( $dir, $grdir );
		$out = substr( $out, 0, strlen( $out ) - 1 );
		return $listdirrecold[$dir] = $out;
	}
	else{
		return $listdirrecold[$dir];
	}
}

function add_tiny( $big = false, $small = false, $ids = array( 'big' => '#inhalt', 'small' => '#footer' ) ){
	global $sitecontent, $allgsysconf, $tinyoo;

	$sitecontent->add_html_header('<script>');

	if( !$tinyoo ){
		$sitecontent->add_html_header('
		var tiny = [];

		function tinychange( id ){
			if( !tiny[id] ){
				tinymce.EditorManager.execCommand( "mceAddEditor", true, id);
				tiny[id] = true;
			}
			else{
				tinymce.EditorManager.execCommand( "mceRemoveEditor", true, id)
				tiny[id] = false;
			}
		}
		');
		$tinyoo = true;
	}

	if( $big ){
		$sitecontent->add_html_header('
		tinymce.init({
			selector: "'.$ids['big'].'",
			theme: "modern",
			plugins: [
				"advlist autosave autolink lists link image charmap preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons paste textcolor colorpicker textpattern codemagic"
			],
			toolbar1: "styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr",
			toolbar2: "fontselect | undo redo | forecolor backcolor | link image emoticons | preview fullscreen | codemagic searchreplace",
			image_advtab: true,
			language : "de",
			width : 680,
			height : 300,
			resize: "horizontal",
			content_css : "'.$allgsysconf['siteurl'].'/load/system/theme/design_for_tiny.css",
			browser_spellcheck : true,
			image_list: function( success ) {
				success( [ '.listdirrec( __DIR__.'/../../load/userdata', '/load/userdata' ).' ] );
			},
			autosave_interval: "20s",
			autosave_restore_when_empty: true,
			autosave_retention: "60m",
			menubar: "file edit insert view format table"
		});
		tiny[\''.substr( $ids['big'], 1 ).'\'] = true;
		');

	}
	if( $small ){
		$sitecontent->add_html_header('
		tinymce.init({
			selector: "'.$ids['small'].'",
			theme: "modern",
			plugins: [
				"advlist autosave autolink lists link image charmap preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons paste textcolor colorpicker textpattern codemagic"
			],
			toolbar1: "styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr",
			toolbar2: "fontselect | undo redo | forecolor backcolor | link image emoticons | preview fullscreen | codemagic searchreplace",
			image_advtab: true,
			language : "de",
			width : 680,
			height : 100,
			resize: "horizontal",
			content_css : "'.$allgsysconf['siteurl'].'/load/system/theme/design_for_tiny.css",
			browser_spellcheck : true,
			menubar : false,
			autosave_interval: "20s",
			autosave_restore_when_empty: true,
			autosave_retention: "60m",
			image_list: function( success ) {
				success( [ '.listdirrec( __DIR__.'/../../load/userdata', '/load/userdata' ).' ] );
			}
		});
		tiny[\''.substr( $ids['small'], 1 ).'\'] = true;
		');
	}

	$sitecontent->add_html_header('</script>');
}

function compare_cms_vers( $v1 , $v2 ) {

	$v[0] = $v1;
	$v[1] = $v2;

	foreach( $v as $ver ){

		//Ganze erste Nummer
		$vpos = stripos( $ver , 'V' );
		$ppos = strpos( $ver , '.', $vpos );

		$lv = $ppos - $vpos;

		$teil['eins'] = substr( $ver , $vpos + 1 , $lv - 1 );

		//Kommastelle & A,B,F
		$ppos = strpos( $ver , '.' , $vpos );
		$apos = stripos( $ver , 'A', $ppos );
		$bpos = stripos( $ver , 'B', $ppos );
		$fpos = stripos( $ver , 'F', $ppos );

		if ( $apos !== false ){
			$lpos = $apos;
			$teil['bst'] = '1';
		}
		elseif( $bpos !== false ){
			$lpos = $bpos;
			$teil['bst'] = '2';
		}
		elseif( $fpos !== false ){
			$lpos = $fpos;
			$teil['bst'] = '3';
		}
		else{
			return false;
		}

		$lv = $lpos - $ppos;

		$teil['komma'] = substr( $ver , $ppos + 1 , $lv - 1 );

		//Patch
		$papos = stripos( $ver , '-p', $lpos );
	
		$patch = substr( $ver , $papos + 2 );

		$kpos = strrpos( $patch , ',' );

		if( $kpos !== false ){
			$patch = substr( $patch , $kpos + 1 );
		}

		$patch = preg_replace( "/\D/", '', $patch );  

		$teil['patch'] = $patch;

		//fertig

		foreach( $teil as $tei ){
			if( !is_numeric( $tei ) ){
				return false;
			}
		}

		$varr[] = $teil;
	}

	//Ganze erste Nummer
	if( $varr[0]['eins'] > $varr[1]['eins'] ){

		return 'newer';

	}
	elseif( $varr[0]['eins'] < $varr[1]['eins'] ){

		return 'older';

	}
	elseif( $varr[0]['eins'] == $varr[1]['eins'] ){

		//Kommastelle
		if( $varr[0]['komma'] > $varr[1]['komma'] ){

			return 'newer';

		}
		elseif( $varr[0]['komma'] < $varr[1]['komma'] ){

			return 'older';

		}
		elseif( $varr[0]['komma'] == $varr[1]['komma'] ){

			//A,B,F
			if( $varr[0]['bst'] > $varr[1]['bst'] ){

				return 'newer';

			}
			elseif( $varr[0]['bst'] < $varr[1]['bst'] ){

				return 'older';

			}
			elseif( $varr[0]['bst'] == $varr[1]['bst'] ){

				//Patch
				if( $varr[0]['patch'] > $varr[1]['patch'] ){

					return 'newer';

				}
				elseif( $varr[0]['patch'] < $varr[1]['patch'] ){

					return 'older';

				}
				elseif( $varr[0]['patch'] == $varr[1]['patch'] ){

					return 'same';

				}
				else{
					return false;
				}

			}
			else{
				return false;
			}

		}
		else{
			return false;
		}

	}
	else{
		return false;
	}
}

function make_path_array(){
	global $idfile, $menuenames, $menuearray, $fileidlist, $filelisti;

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	$menuearray = '';
	$fileidlist = '';
	$filelisti = '';

	make_menue_array();

	foreach( $menuearray as $menuear ){

		$niveau = $menuear['niveau'];

		if( !isset( $thisniveau ) ){
			$grpath = $allgsysconf['siteurl'].'/'.$menuear['path'];
		}
		elseif( $thisniveau == $niveau ){
			$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			$grpath = $grpath.'/'.$menuear['path'];
		}
		elseif( $thisniveau < $niveau ){
			$grpath = $grpath.'/'.$menuear['path'];
			$thisulaufp = $thisulauf + 1;
		}
		elseif( $thisniveau > $niveau ){
			$i = 1;
			while( $thisniveau != $niveau + $i  ){
				$i++;
				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			}
			$thisulaufp = $thisulaufp - $i;

			$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			$grpath = $grpath.'/'.$menuear['path'];
		}
		$url = $grpath.'/';

		$patharr['id'] = $menuear['requid'];
		$patharr['url'] = $url;

		$return[] = $patharr;

		$thisniveau = $niveau;

	}

	return $return;

}

function make_path_outof_reqid( $requid ){
	global $allgsysconf;

	if( $allgsysconf['urlrewrite'] == 'on' ){

		foreach( make_path_array() as $path ){
			if( $path['id'] == $requid ){
				return $path['url'];
			}
		}

		return '/index.php?id='.$requid;
	}
	else{
		return '/index.php?id='.$requid;
	}
}

function check_addon_status( $addon ){
	global $addoninclude, $allinclpar;

	foreach( $allinclpar as $par ){

		if( $addoninclude->read_kimb_search_teilpl( $par , $addon ) ){
			return true;
		}

	}

	return false;
}


// Funktionen von Add-ons hinzufügen
require_once( __DIR__.'/../addons/addons_funcclass.php' );
?>
