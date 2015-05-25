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

//email versenden
// $to => Empfänger
// $inhalt => Inhalt
function send_mail($to, $inhalt){
	global $allgsysconf;
	
	//nicht leer ?
	if( empty( $inhalt ) || empty( $to ) ){
		return false;
	}

	//sende Mail und gebe zurück
	return mail($to, 'Nachricht von: '.$allgsysconf['sitename'], $inhalt, 'From: '.$allgsysconf['sitename'].' <'.$allgsysconf['mailvon'].'>');
}

//Browser an  andere URL weiterleiten
//	$url => zu öffnende URL
//	$area => insystem ($allgsysconf['siteurl'] + $url)
//	$code => HTTP-Code
function open_url($url, $area = 'insystem', $code = 303 ){
	global $allgsysconf;

	//innerhalb des CMS weiterleiten, also $allgsysconf['siteurl'] vor die URL setzen
	if( $area == 'insystem'){
		$url = $allgsysconf['siteurl'].$url;
	}

	//Weiterleitung per HTTP ?
	if($allgsysconf['urlweitermeth'] == '1'){
		//machen und beenden
		header('Location: '.$url, true, $code );
		die;
	}
	//Weiterleitung per HTML ?
	elseif($allgsysconf['urlweitermeth'] == '2'){
		//machen und beenden
		echo('<meta http-equiv="Refresh" content="0; URL='.$url.'">');
		die;
	}
}

//schauen ob kimb datei vorhanden
//	$datei => KIMB-Datei Name
function check_for_kimb_file($datei){
	//Dateinamen bereinigen
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei );
	//Sicherheit des Dateisystems
	if(strpos($datei, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
	//Datei vorhanden?
	//Rückgabe
	if(file_exists(__DIR__.'/../oop/kimb-data/'.$datei)){
		return true;
	}
	else{
		return false;
	}
}

// für scan_kimb_dir, alles außer Zahlen aus Sting entfernen
function justnum( $str ) { return preg_replace( "/[^0-9]/" , "" , $str ); }

//alle KIMB-Dateien in einem Verzeichnis ausgeben
// $datei => Verzeichnis
function scan_kimb_dir($datei){
	//Dateinamen bereinigen
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei );
	//Dateisystem schützen
	if(strpos($datei, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
	//Verzeichnis in Array lesen
	$files = scandir(__DIR__.'/../oop/kimb-data/'.$datei);
	
	//Rückgabe Array aufbauen
	$i = 0;
	//Alle Dateien durchgehen
	foreach ( $files as $file ){
		//Dateiname werder . noch .. oder index.kimb?
		if( $file != '.' && $file != '..' && $file != 'index.kimb' ){
			//zum Rückgabe Array hinzufügen und Index erhöhen
			$return[$i] .= $file;
			$i++;
		}
	}
	
	//Rückgabe nach ID (Zahl im Dateinamen) sortieren 
	
	//Array mit nur IDs aus Rückgabe Array erstellen
	$returnref = array_map( 'justnum' , $return );
	
	//Rückgabe Array nach dem Array $returnref sortieren
	array_multisort( $returnref, $return);

	//Array Rückgabe ausführen
	return $return;
}

//request URL herausfinden
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
//	$number => englische Zahl für BE Seiten
//	$permiss => more,less,none
//	$die => true,false (soll der Ablauf abgebrochen werden und Error 403 angezeigt werden wenn keine Rechte)
function check_backend_login( $number , $permiss = 'less', $die = true ){
	global $sitecontent, $allgsysconf;

	//Allgemein eingeloggt?
	if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

		//Hat User Permission more oder less und ist dies als Parameter gegeben?
		if( ( $_SESSION['permission'] == 'more' || $_SESSION['permission'] == 'less' ) && ( $permiss == 'more' || $permiss == 'less' ) ){
			//wenn more gegeben, aber User nicht more -> keine Rechte
			if( $permiss == 'more' && $_SESSION['permission'] != 'more' ){
				//die oder nur return false
				if( $die ){
					//Seiteninhalt per Klasse, dann nutzen, sonst ohne
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
			
			//Permission less übergeben und User muss more oder less sein -> OK
			return true;
		}
		//kein more oder less bei dem User oder nicht als Parameter gegeben
		else{
			//lese BE Leveldatei
			$levellist = new KIMBdbf( 'backend/users/level.kimb' );
			//lese die englischen Zahlen der Nutzergruppe des Users
			$permissteile = $levellist->read_kimb_one( $_SESSION['permission'] );
			//Nutzergruppe vorhanden?
			if( !empty( $permissteile ) ){
					//Zerteile den Sting mit den englischen Zahlen in einzelne, packe in eine Array
					$permissteile = explode( ',' , $permissteile );
					
					//ist in der Nutzergruppe (Array $permissteile) die englische Zahl (Parameter) vorhanden?
					if( !in_array( $number , $permissteile ) ){
						//keine Rechte
						
						//die oder nur return false
						if( $die ){
							//Seiteninhalt per Klasse, dann nutzen, sonst ohne
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
						//Nutzergruppe des Users hat Rechte für gegebene englische Zahl (Paramter $number)
						return true;
					}

			}
			//Nutzergruppe existiert nicht
			else{
				//die oder nur return false
				if( $die ){
					//Seiteninhalt per Klasse, dann nutzen, sonst ohne
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
	//gar nicht eingeloggt -> keine Rechte
	else{
		//die oder nur return false
		if( $die ){
			//Seiteninhalt per Klasse, dann nutzen, sonst ohne
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

//KIMB-Datei umbenennen/verschieben
//wie PHP rename( $datei1, $datei2  );
function rename_kimbdbf( $datei1 , $datei2 ){
	//Dateinamen bereinigen
	$datei1 = preg_replace('/[\r\n]+/', '', $datei1);
	$datei1 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei1);
	$datei1 = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei1 );

	$datei2 = preg_replace('/[\r\n]+/', '', $datei2);
	$datei2 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei2);
	$datei2 = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei2 );

	//Dateisystem schützen
	if(strpos($datei2, "..") !== false || strpos($datei1, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
	
	//Dateien umbenennen/verschieben
	return rename( __DIR__.'/../oop/kimb-data/'.$datei1 , __DIR__.'/../oop/kimb-data/'.$datei2 );
}

//rekursiv leoschen
//	$dir => Verzeichnis
function rm_r($dir){
	//lese Verzeichnis
	$files = scandir($dir);
	//gehe Dateien und Ordner durch
	foreach ($files as $file) {
		if($file == '.' || $file == '..'){
			//nichts
		}
		else{
			//Datei oder Ordner?
			if(is_dir($dir.'/'.$file)){
				//lösche den Ordner
				//	Verschachtelung der Funktion
				rm_r($dir.'/'.$file);
			}
			else{
				//lösche Datei direkt
				unlink($dir.'/'.$file);
			}
		}
	}
	//verlasse und lösche Ordner
	return rmdir($dir);
}

//rekursiv zippen
//	$zip => PHP Zip Objekt
//	$dir => zu zippendes Verzeichnis
//	$base => Basis Ordner in der Zip Datei
function zip_r($zip, $dir, $base = '/'){
	//gibt es $dir überhaupt?
	if (!file_exists($dir)){
		return false;
	}
	//lese Verzeichnis
	$files = scandir($dir);

	//gehe Verzeichnis durch
	foreach ($files as $file){
		if ($file == '..' || $file == '.'){
			//nichts
		}
		else{
			//füge Datei direkt in die Zip Datei
			if (is_file($dir.'/'.$file)){
				$zip->addFile($dir.'/'.$file, $base.$file);
			}
			//erstelle einen neuen Ordner in der Zip
			//Verschachtelung der Funktion mit richiger $base
       			elseif (is_dir($dir.'/'.$file)){
				$zip->addEmptyDir($base.$file);
				zip_r($zip, $dir.'/'.$file, $base.$file.'/');
			}
		}
	}
	//fertig
	return true;
}

//rekursiv kopieren
//	$dir => zu kopierender Ordner
//	$dest => Ziel für Kopien
function copy_r( $dir , $dest ){

	//wenn Ziel nicht vorhanden machen einen neuen Ordner mit richtigen Rechten
	if( !is_dir( $dest ) ){
		mkdir( $dest );
		chmod( $dest , ( fileperms( $dest.'/../' ) & 0777));
	}
	
	//ließ alle Dateien im Verzeichnis
	$files = scandir( $dir );
	foreach ($files as $file){
		if ($file == '..' || $file == '.'){
			//nichts
		}
		else{
			//kopiere Dateien direkt
			if ( is_file($dir.'/'.$file) ){
				copy( $dir.'/'.$file , $dest.'/'.$file );
			}
			//Verschachtelung der Funktion bei Ordnern
       			elseif ( is_dir($dir.'/'.$file) ){
				copy_r( $dir.'/'.$file , $dest.'/'.$file );
			}
		}
	}
	//beenden
	return true;
}

//Menue erstellen
//Aufruf nur bei der Menueerstellung im Frontend !!
//Diese Funktion wird verschachtelt aufgerufen, für jeden Menuepunkt ruft Sie die $sitecontent->add_menue_one_entry(); Methode des Themes auf.
//	$allgrequestid	=> RequestID der Seite für die das gesamte Menü sein soll
//	$filename => Dateiname der URL Datei des aktuell zu erstellenden Menues
//	$grpath => Grundverzeichnis des aktuell zu erstellenden Menues
///	$niveau => Niveau des aktuell zu erstellenden Menues
function gen_menue( $allgrequestid , $filename = 'url/first.kimb' , $grpath = '/' , $niveau = '1'){
	//$sitecache => wenn aktiviert Cache Objekt
	//$sitecontent => Seitenausgabeobjekt
	//$menuenames => Menuenamen KIMBdbf Objekt (Usersprache)
	//$allgsysconf => Systemkonfiguration
	//$allgmenueid => ID des Menue (an RequestID gebunden)
	//$breadcrumbarr => Array für Breadcrumb
	//$breadarrfertig => Breadcrumb Array fertig oder nicht (wird von dieser Funktion auf fertig gesetzt)
	//$requestlang => Array mit Sprache der auszugebenden Seite (Multilingual Site)
	//$requestlangid => ID der Sprache der auszugebenden Seite (Multilingual Site)
	//$menuenameslangst =>  Menuenamen KIMBdbf Objekt (Standardsprache)
	global $sitecache, $sitecontent, $menuenames, $allgsysconf, $allgmenueid, $breadcrumbarr, $breadarrfertig, $requestlang, $requestlangid, $menuenameslangst;
	
	//beim ersten Durchlauf die Sprachdaten setzen, sofern Multilingual Site aktiviert
	if( $allgsysconf['lang'] == 'on' && $grpath == '/' && !empty( $requestlang['tag'] ) ){
		//Menuelinks mit Sprachtag erweitern
		$grpath = '/'.$requestlang['tag'].'/';
		//ID der Sprache der auszugebenden Seite speichern
		$requestlangid = $requestlang['id'];
	}
	//Multilingual Site aus, also Standardsprache
	elseif( $grpath == '/' && empty( $requestlang['tag'] ) ){
		$requestlangid = 0;
	}

	//Menueerstellung
	//URL-Datei laden
	$file = new KIMBdbf( $filename );
	//URL-Datei ID auf 1 setzen
	//	in den URL-Dateien sind alle IDs fortlaufen ab 1 vergeben (1,2,3) 
	$id = 1;
	//jeden Menüpunkt der Datei in einer Schleife lesen
	while( 5 == 5 ){
		//Resquest ID für Menuepunkt lesen
		$requid = $file->read_kimb_id( $id , 'requestid' );
		//Pfad für Menuepunkt lesen
		$path = $file->read_kimb_id( $id , 'path' );
		//Menuenamen lesen (in jeweiliger Sprache)
		$menuname = $menuenames->read_kimb_one( $requid );
		//ist der Menuename leer? (kann sein, wenn in einer Sprache nicht vorhanden)
		if( empty( $menuname ) ){
			//Menuename in Standardsprache ausgeben
			$menuname = $menuenameslangst->read_kimb_one( $requid );
		}
		//ist dieser Menuepunkt der Menuepunkt der grade aufgerufenen Seite
		if( $allgrequestid == $requid ){
			$clicked = 'yes';
		}
		else{
			$clicked = 'no';
		}
		
		//sollte der Pfad leer sein ist die URL Datei ausgelesen, Funktion wird verlassen
		if( $path == '' ){
			return true;
		}
		
		//sofern der Menuepunkt aktiviert ist, wird er ausgegeben
		if( $file->read_kimb_id( $id , 'status') == 'on' ){
			//als Rewrite URL ausgeben?
			if( $allgsysconf['urlrewrite'] == 'on' ){
				//führe Ausgabemethode aus
				$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].$grpath.$path , $niveau, $clicked, $requid);
				
				//wenn Cache geladen, cache Menuepunkt
				if(is_object($sitecache)){
					$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].$grpath.$path , $niveau , $clicked, $requid, $requestlangid );
				}
			}
			//oder als ID URL
			else{
				//führe Ausgabemethode aus
				$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau, $clicked, $requid);

				//wenn Cache geladen, cache Menuepunkt
				if(is_object($sitecache)){
					$sitecache->cache_menue($allgmenueid, $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , $niveau , $clicked, $requid);
				}
			}
		}
		
		//erstelle Breadcrumb Array
		if( $allgsysconf['urlrewrite'] == 'on' ){
			if( $breadarrfertig == 'nok' ){
				$breadcrumbarr[$niveau]['link'] = $allgsysconf['siteurl'].$grpath.$path; 
				$breadcrumbarr[$niveau]['name'] = $menuname;

				if( $clicked == 'yes' ){
					$breadarrfertig = 'ok';
					$breadcrumbarr['maxniv'] = $niveau;
				}
			}
		}
		else{
			if( $breadarrfertig == 'nok' ){
				$breadcrumbarr[$niveau]['link'] = $allgsysconf['siteurl'].'/index.php?id='.$requid; 
				$breadcrumbarr[$niveau]['name'] = $menuname;

				if( $clicked == 'yes' ){
					$breadarrfertig = 'ok';
					$breadcrumbarr['maxniv'] = $niveau;
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

function check_addon_status( $addon, $addoninclude, $allinclpar ){

	foreach( $allinclpar as $par ){

		if( $addoninclude->read_kimb_search_teilpl( $par , $addon ) ){
			return true;
		}

	}

	return false;
}

function get_req_url(){
	if( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ){
		$req = substr( $_SERVER['REQUEST_URI'] , '0', '-'.strlen(strrchr( $_SERVER['REQUEST_URI'] , '?' )));
	}
	else{
		$req = $_SERVER['REQUEST_URI'];
	}

	return $req;
}

function make_lang_dropdown( $openurl, $langid ){
		global $sitecontent;
	
		$langfile = new KIMBdbf( 'site/langfile.kimb' );
		
		$sitecontent->add_site_content('<select id="langs" style="padding:2px; padding-left:25px;" title="Bitte wählen Sie die Sprache, für die Sie die Inhalte verändern wollen!">');
		
		foreach( $langfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){
			$vals = $langfile->read_kimb_id( $id );
			if( $id == $langid ){
					$flagurl = $vals['flag'];
			}
			if( $vals['status'] == 'off' ){
				$statinfo = ' (deaktiviert)';
			}
			else{
				$statinfo = '';
			}
			$sitecontent->add_site_content('<option style="background: url( '.$vals['flag'].' ) center left no-repeat; padding:2px; padding-left:25px;" value="'.$id.'">'.$vals['name'].$statinfo.'</option>');
		}
		
		$sitecontent->add_site_content('</select>');
		
		$sitecontent->add_html_header('<script>	
		$( function() {
			$( "#langs" ).on( "change", function() {
				var val = $( "#langs" ).val();
				window.location = '.$openurl.';
			});
			$( "#langs" ).val( '.$_GET['langid'].' );
			$( "#langs" ).css( "background", "url( '.$flagurl.' ) center left no-repeat" );
		});
		</script>');
}

function make_menue_array_helper(  ){
	global $idfile, $menuenames, $menuearray, $fileidlist, $filelisti;

	$menuearray = array();
	$fileidlist = '';
	$filelisti = '';

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	
	make_menue_array();
	
	return $menuearray;
}

function list_sites_array( $dir = 'site/' ){
	$sites = scan_kimb_dir( $dir );
	foreach ( $sites as $site ){
		if( $site != 'langfile.kimb'){
			$sitef = new KIMBdbf('site/'.$site);
			$id = preg_replace("/[^0-9]/","", $site);
			$title = $sitef->read_kimb_one('title');
		
			$allsites[] = array( 'site' => $title, 'id' => $id );
		}
	}
	
	return $allsites;
}

// Funktionen von Add-ons hinzufügen
require_once( __DIR__.'/../addons/addons_funcclass.php' );
?>
