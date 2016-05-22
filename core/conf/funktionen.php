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

//Session und HTTP Header des CMS initialisieren
//	$robots => Header robots tag
//	$cont => MIME der Ausgabe
function SYS_INIT( $robots, $cont = 'text/html' ){
	global $allgsysconf;
	
	//Lebenszeit des Cookie aus Konfiguration
	$lifetime = $allgsysconf['coolifetime'];
	//SSL oder nicht?
	if( substr( $allgsysconf['siteurl'], 0, 8) == 'https://' ){
		$secure = true;
	}
	else{
		$secure = false;		
	}
	
	//Domain & Path aus Konfiguration
	//http(s):// abschneiden
	if( $secure ){
		$do = substr( $allgsysconf['siteurl'], 8);	
	}
	else{
		$do = substr( $allgsysconf['siteurl'], 7);
	}
	
	//Ende der Domain am / erkennen
	$po = strpos ( $do, '/' );
	//kein /, dann Ende der Domain = Länge von $do
	if( empty( $po )){
		$po = strlen($do);
	}
	//Domain bestimmen
	$domain = substr( $do, 0, $po );
	if( strpos( $domain, ':') !== false ){
		//Domain Port entfernen
		$dopp = strpos( $domain, ':');
		$domain = substr( $domain, 0, $dopp );
	}
	//Path bestimmen
	$path = substr( $do, $po ).'/';
	
	//Sicherung der Session ID
	ini_set('session.use_only_cookies', 1);
	//Session Cookie vorbereiten
	session_set_cookie_params ( $lifetime, $path, $domain, $secure, true );
	session_name ("KIMBCMS");
	//Session Cookie setzen
	session_start();
	//Fehlermeldungen & HTTP Header
	error_reporting( 0 );
	header('X-Robots-Tag: '.$robots);
	header('Content-Type: '.$cont.'; charset=utf-8');
}

//sichere Zufallszahlen, neu seit PHP 7
//	$a => Anfang
//	$e => Ende
//	Rückgabe: Nummer
function gen_zufallszahl( $a, $e ){
	
	//neue Funktion von PHP 7 verfügbar?
	if( function_exists( 'random_int') ){
		//nutzen
		return random_int( $a, $e );
	}
	else{
		//alte Zufallszahl nutzen
		return mt_rand( $a, $e );
	}
	
}

//E-Mail versenden
//	Unterschrift per S/MIME, wenn im Backend des CMS aktiviert!	
//		$to => Empfänger
//		$inhalt => Inhalt
//		$mime => MIME Type (text oder html)
function send_mail($to, $inhalt, $mime = 'plain'){
	global $allgsysconf;
	
	//In einigen Add-ons ist ein Bug, welcher die Umbrüche falsch ausgibt:
	//	nr statt rn!!
	//		diesen Fehler hier entfernen, sonst schlägt Unterschrift fehl!!
	if( strpos( $inhalt, "\n\r" ) !== false ){
		$inhalt = str_replace( "\n\r", "\r\n", $inhalt );
	}
	
	//nicht leer ?
	if( empty( $inhalt ) || empty( $to ) ){
		$return = false;
	}
	else{
		//erstmal S/MIME nehmen
		$oldstyle = false;
		//aber noch nicht signiert
		$this_signed = false;
		
		//Pfad absolut (beginnt mit /) oder relativ?
		if( substr( $allgsysconf['smime_cert_ini_folder'], 0 , 1) != '/' ){
			//absolut machen (Angabe aus config ist relativ zum Ordner dieser Datei!)
			$allgsysconf['smime_cert_ini_folder'] = __DIR__.'/'.$allgsysconf['smime_cert_ini_folder'];
		}

		//S/MIME INI gegeben und Order vorhanden??
		if( !empty( $allgsysconf['smime_cert_ini_name'] ) ){
			//Cert Vars laden
			$certini = $allgsysconf['smime_cert_ini_folder'].'/'.$allgsysconf['smime_cert_ini_name'];
			$certpath = $allgsysconf['smime_cert_ini_folder'].'/';

			//	INI vorhanden?
			//	Pfad vorhnaden?
			if( is_file( $certini ) && is_dir( $certpath ) ){

				//Daten für Certs lesen
				$certdata = parse_ini_file( $certini , true );

				//Certs für Absenderadresse vorhanden?
				//	in Vars laden
				//		Public Cert
				$cert_pub = $certpath.$certdata[$allgsysconf['mailvon']]['smime_cert_pub'];
				//		Priv Cert
				$cert_priv = $certpath.$certdata[$allgsysconf['mailvon']]['smime_cert_priv'];
				//		Key für Priv Cert
				$cert_key = $certdata[$allgsysconf['mailvon']]['smime_cert_priv_key'];

				//alles für Certs gegeben?
				if( !empty( $cert_pub ) && !empty( $cert_priv ) && !empty( $cert_key ) ){

					//E-Mail in einer Datei speichern
					//	Pfad von INI nehmen und Dateinamen machen
					$randint = gen_zufallszahl( 1000, 9999 );
					$mailfile = $certpath.'maildraft_'.$randint.'.txt';
					$mailfile_signed = $certpath.'mailsigned_'.$randint.'.txt';

					//	einen Umbruch adden, sonst sign-Fehler
					$inhalt = "\r\n".$inhalt;
					//	Header für Inhalt
					$inhalt = 'Content-Type: text/'.$mime.'; charset=utf-8'."\r\n".$inhalt;

					//	E-Mail Inhalt in Datei
					$fp = fopen( $mailfile , 'w+');
					fwrite( $fp, $inhalt );
					fclose( $fp );
		
					//Verschlüsselung durchführen
					if(
						openssl_pkcs7_sign(
							$mailfile,
							$mailfile_signed,
							'file://'.$cert_pub,
							array( 'file://'.$cert_priv, $cert_key ),
							array( 'From: '.$allgsysconf['sitename'].' <'.$allgsysconf['mailvon'].'>' )
						)
					){
						//Mail signiert
						$this_signed = true;

						//Mail in Inhalt und Header teilen
						$parts = explode("This is an S/MIME signed message", file_get_contents( $mailfile_signed ), 2);

						//durch Teilung entferntes wieder anfügen
						$parts[1] = 'This is an S/MIME signed message'.$parts[1];

						//Mail absenden
						$return = mail($to, 'Nachricht von: '.$allgsysconf['sitename'], $parts[1], $parts[0] );
					}
					else{
						//keine S/MIME
						$oldstyle = true;
					}

					//aufräumen
					unlink( $mailfile );
					unlink( $mailfile_signed );
				}
				else{
					//keine S/MIME
					$oldstyle = true;
				}
			}
			else{
				//keine S/MIME
				$oldstyle = true;
			}
		}
		else{
			//keine S/MIME
			$oldstyle = true;
		}

		//Ohne S/MIME?
		if( $oldstyle ){
			//Header erstellen
			//	Absender
			$header = 'From: '.$allgsysconf['sitename'].' <'.$allgsysconf['mailvon'].'>'."\r\n";
			//	MIME & Charset
			$header .= 'MIME-Version: 1.0' ."\r\n";
			$header .= 'Content-Type: text/'.$mime.'; charset=utf-8' . "\r\n";

			//sende Mail und gebe zurück
			$return = mail($to, 'Nachricht von: '.$allgsysconf['sitename'], $inhalt, $header);
		}
	}

	//Mail Logging?
	if( isset( $allgsysconf['mail_log'] ) && $allgsysconf['mail_log'] == 'on' ){

		//Trenner
		$logdata = "\r\n".'====================================================================='."\r\n";
		$logdata .= 'From: '.$allgsysconf['sitename'].' <'.$allgsysconf['mailvon'].'>'."\r\n";
		$logdata .= 'To: '.$to."\r\n";
		$logdata .= 'Subject: Nachricht von: '.$allgsysconf['sitename']."\r\n";
		$logdata .= "\r\n";
		$logdata .= $inhalt;
		$logdata .= "\r\n\r\n";
		$logdata .= 'Status: Signatur ('.( $this_signed ? 'true' : 'false' ).'); Versandt ('.( $return ? 'true' : 'false' ).'); Type ('.$mime.')'."\r\n";
		$logdata .= 'Zeitpunkt: '.date( 'd.m.Y H:i:s' )."\r\n";

		$f = fopen( __DIR__.'/mail.log', 'a+' );
		fwrite( $f, $logdata );
		fclose( $f );
	}

	return $return;
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
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace( '/([^A-Za-z0-9\_\.\-\/])/' , '' , $datei );
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
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace( '/([^A-Za-z0-9\_\.\-\/])/' , '' , $datei );
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

//Diese Funktion ergänzt check_backend_login: Beim CMS ist es möglich für bestimmte User 
//Seiten/ Add-ons/ Menüs getrennt zur Bearbeitung freizugeben oder auch zu sperren.
//Diese Funktion prüft ob ein User auf eine Seite/ Add-on/ Menü zugreifen darf oder nicht!
//	Die Sperrungen sind nur für Systemspezifische Gruppen verfügbar! (nicht more bzw. less)
//	Die Sperrungen können unter Other -> Userlevel Backend für jede Gruppe definiert werden.
//
//	$art => s(Seite)/ a(Add-on)/ m(Menü)
//	$id => ID der  Seite/ Add-on/ Menü für welche die Zugriffe abgefragt werden sollen
//		Seite: SiteID
//		Add-on: NameID
//		Menü: NextID des Menüs (Menüdatei ID (first = 0))
//	Rückgabe: Boolean (true => Zugriff erlaubt, false => kein Zugriff)
//
//	-> Die Funktion überprüft nicht den allgemeinen Loginstatus oder ob eine Gruppe/
//	     ein User überhaupt einen Zugriff auf die entsprechende Backendseite hat.
//	-> Die Funktion beendet außerdem das Skript nicht und gibt keine
//	     Fehlermeldungen aus!
function check_backend_permission( $art, $id ){
	//less und more dürfen immer
	if( $_SESSION['permission'] == 'more' || $_SESSION['permission'] == 'less' ){
		return true;
	}
	else{
		//Leveldatei für BE User lesen
		$levellist = new KIMBdbf( 'backend/users/level.kimb' );
		
	}
}

//KIMB-Datei umbenennen/verschieben
//wie PHP rename( $datei1, $datei2  );
function rename_kimbdbf( $datei1 , $datei2 ){
	//Dateinamen bereinigen
	$datei1 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei1);
	$datei1 = preg_replace( '/([^A-Za-z0-9\_\.\-\/])/' , '' , $datei1 );

	$datei2 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei2);
	$datei2 = preg_replace( '/([^A-Za-z0-9\_\.\-\/])/' , '' , $datei2 );

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
	while( true ){
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
		//ist es schon fertig (wenn clicked schon yes war)
		if( $breadarrfertig == 'nok' ){
			//Link und Name dem Array hinzufügen

			//Unterscheidung für URL zuwischen URL-Rewrite und ID
			if( $allgsysconf['urlrewrite'] == 'on' ){
				$breadcrumbarr[$niveau]['link'] = $allgsysconf['siteurl'].$grpath.$path;
			}
			else{
				$breadcrumbarr[$niveau]['link'] = $allgsysconf['siteurl'].'/index.php?id='.$requid;
			} 
			$breadcrumbarr[$niveau]['name'] = $menuname;

			//clicked yes, also letzter Wert im Array 
			if( $clicked == 'yes' ){
				$breadarrfertig = 'ok';
				$breadcrumbarr['maxniv'] = $niveau;
			}
		}
		
		//Nextid des aktuellen Menüpunktes lesen (Untermenüs vorhanden?)
		$nextid = $file->read_kimb_id( $id , 'nextid');
		if( $nextid != '' ){
			//wenn vorhanden, dann diese Funktion verschachtelt starten (mit passenden Parametern)
			$newniveau = $niveau + 1;
			gen_menue( $allgrequestid , 'url/nextid_'.$nextid.'.kimb' , $grpath.$path.'/' , $newniveau);
		}
		$id++;
	}
}

//Array mit allen Menüpunkten erstellen
//Aufruf z.B. im Backend "Menue" -> "Auflisten"
//Ausgabe eines Menuearrays
// => Aufruf über "make_menue_array_helper()" empfohlen [erfüllt automatisch alle Abhängigkeiten]
function make_menue_array( $filename = 'url/first.kimb' , $niveau = '1' , $fileid = 'first' , $oldfilelisti = 'none', $deeper = true ){
	global $menuenames, $idfile, $menuearray, $fileidlist, $filelisti;

	//für Array mit den URL-Dateien, welche die NextID enthalten, erstellen
	if( !isset( $filelisti ) ){
		$filelisti = 0;
	}
	else{
		$filelisti++;
	}

	//gewünschte URL-Datei öffnen
	$file = new KIMBdbf( $filename );
	//URL-Datei ID auf 1 setzen
	//	in den URL-Dateien sind alle IDs fortlaufen ab 1 vergeben (1,2,3) 
	$id = 1;
	//ID für ID durchgehen
	while( true ){
		//einzelne Werte auslesen
		$path = $file->read_kimb_id( $id , 'path' );
		$nextid = $file->read_kimb_id( $id , 'nextid' );
		$requid = $file->read_kimb_id( $id , 'requestid' );
		$status = $file->read_kimb_id( $id , 'status');
		$menuname = $menuenames->read_kimb_one( $requid );
		$siteid = $idfile->read_kimb_id( $requid , 'siteid' );
		$menueid = $idfile->read_kimb_id( $requid , 'menueid' );
		if( $oldfilelisti != 'none' ){
			$fileidbefore = $fileidlist[$oldfilelisti];
		}

		//kein Path mehr definiert, also keine Menüpunkte des Menüs mehr!
		if( $path == '' ){
			//Funktion verlassen
			return true;
		}
		
		//für Array mit den URL-Dateien, welche die NextID enthalten
		$fileidlist[$filelisti] = $fileid;

		//alle Daten des Menüpunktes in ein Array schreiben
		$menuearray[] = array( 'niveau' => $niveau, 'path' => $path, 'nextid' => $nextid , 'requid' => $requid, 'status' => $status, 'menuname' => $menuname, 'siteid' => $siteid, 'menueid' => $menueid, 'fileid' => $fileid , 'fileidbefore' => $fileidbefore );

		//Nextid des aktuellen Menüpunktes lesen (Untermenüs vorhanden?)
		//	nur wenn für diesen Aufruf erlaubt!
		if( $nextid != '' && $deeper ){
			//wenn vorhanden, dann diese Funktion verschachtelt starten (mit passenden Parametern)
			$newniveau = $niveau + 1;
			make_menue_array( 'url/nextid_'.$nextid.'.kimb' , $newniveau , $nextid , $filelisti, $deeper );
		}
		$id++;
	}
}

//Erstellung eines Dropdowns mit allen Menüpunkten und deren ID
//Zur Auswahl einer Seite/ eines Menüpunktes  bei Add-ons sinnvoll
//	$name => name des <select> Tags
//	$id => Wahl der zu wählenden ID, z.B. requid, siteid, menueid [alles aus $menuearray von make_menue_array();]
function id_dropdown( $name, $id = 'siteid' ){
	//Abhängigkeiten von make_menue_array(); erfüllen
	global $idfile, $menuenames, $menuearray;
	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	//bei mehrfachem Aufruf nur einmal make_menue_array() nötig
	if( !isset( $menuearray ) ){
		make_menue_array();
	}

	//Dropdown erstellen
	$return = '<select name="'.$name.'" >';

	foreach( $menuearray as $menuear ){

		//Lesen der Daten: ID, Name, Niveau
		$niveau = str_repeat( '==>' , $menuear['niveau'] );
		$valid = $menuear[$id];
		$menuename = $menuear['menuname'];

		//Aufbau eines Wertes: VALUE = <ID>; AUSWAHL = <Darstellung des Niveau mit "==>"> <Menüname> - <ID>
		$return .= '<option value="'.$valid.'">'.$niveau.' '.$menuename.' - '.$valid.'</option>';
	}

	//beenden
	$return .= '</select>';

	//fertiges HTML-Select zurückgeben
	return $return;

}

//Array mit allen installierten Add-on Namen (ID-Name) erstellen
//	Rückgabe: Array
function listaddons(){

	//Add-on Verzeichnis lesen
	$files = scandir(__DIR__.'/../addons/');

	foreach ($files as $file) {
		//alles was nicht passt aussortieren
		if( $file != '.' && $file != '..' &&  is_dir(__DIR__.'/../addons/'.$file) ){
			//Array füllen
			$read[] = $file;
		}
	}
	
	//Array Rückgabe
	return $read;
}

//Zufallsstrings erzeugen
//	$laenge => Länge des zu erzeugenden Stings
// 	$chars => Charakter des Stings
//	$wa => voreingestellte Charakter nutzen ('az' = A bis z; 'num' = 0 bis 9; 'numaz' = 0 bis 9 und A bis z; $chars und $wa nicht gegeben = alles inkl. Sonderzeichen)
function makepassw( $laenge , $chars = '!"#%&()*+,-./:;?[\]_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', $wa = 'off' ){
	//Sofern voreingestellte Charakter gewünscht, Auswahl dieser
	if( $wa == 'az' ){
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}
	elseif( $wa == 'num' ){
		$chars = '0123456789';
	}
	elseif( $wa == 'numaz' ){
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}
	
	//Anzahl der möglichen Charakter bestimmen
	$anzahl = strlen($chars);
	//mit dem ersten Charakter geht es los
	$i = '1';
	//noch keine Ausgabe
	$output = '';
	//solange weniger oder genausoviele Charakter wie gwünscht im Sting weiteren erstellen 
	while($i <= $laenge){
		//Charakter zufällig wählen (Zufallszahl als Stelle für $chars nutzen)
		$stelle = gen_zufallszahl('0', $anzahl);
		//Ausgabe erweitern 
		$output .= $chars{$stelle};
		$i++;
	}
	//Ausgeben
	return $output;
}

//Verzeichnis rekursiv auf Fotos duchsuchen und als JSON String ausgeben (für Foto Auswahl von TinyMCE)
//Achtung: Komma am Ende des JSON Strings!! (mit listdirrec(); entfernt)
//	$dir => zu durchsuchendes Verzeichnis auf dem Server
//	$grdir => grundlegende URL zum zu durchsuchenden Verzeichnis auf dem Server
function listdirrec_f( $dir, $grdir ){
	global $allgsysconf;
	
	//Verezichnis lesen
	$files = scandir( $dir );

	//alle Dateien duchgehen
	foreach( $files as $file ){

		if( $file == '..' || $file == '.' ){

		}
		elseif( is_file( $dir.'/'.$file ) ){

			//wenn Datei, dann MIME Type bestimmen
			$mime = mime_content_type ( $dir.'/'.$file );

			//MIME String zuschneiden (nur image bleibt)
			$mime = substr( $mime, 0, 5 ); 

			//wenn MIME image, dann zu JSON Sting hinzufügen
			if( $mime == 'image' ){
				$out .= '{title: "'.$grdir.'/'.$file.'", value: "'.$allgsysconf['siteurl'].$grdir.'/'.$file.'"},';
			}
		}
		elseif( is_dir( $dir.'/'.$file ) ){
			//wenn Verzeichnis, dann dieses durchsuchen
			$out .= listdirrec_f( $dir.'/'.$file , $grdir.'/'.$file );
		}
	}
	//JSON Sting ausgeben
	return $out;
}

//wie listdirrec_f(); nur ohne Komma am Ende und bei mehrfachem Aufruf Speicherung der Strings
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

//	====================================
//	------ BITTE NICHT MEHR AKTIV NUTZEN -------
//	------    siehe add_content_editor( $id )     -------
//	====================================
//einfaches Hinzufügen von TinyMCE in eine Textarea
//Die benötigten JavaScript Dateien werden im Backend automatisch geladen, im Frontend ist das Hinzufügen des HTML-Headers '<!-- TinyMCE -->' nötig!
//	$big => großes Feld aktivieren (mit TinyMCE Menü) [boolean]
//	$small => kleines Feld aktivieren (ohne TinyMCE menü) [boolean]
//	$ids => Array ()'big' => ' HTML ID des Textarea für großes Feld ', 'small' => ' HTML ID des Textarea für kleines Feld ' ) 
function add_tiny( $big = false, $small = false, $ids = array( 'big' => '#inhalt', 'small' => '#footer' ) ){
	
	//es gibt keine Größenunterschiede mehr!!
	
	//hier wird eindfach die neue Funktion add_content_editor() benutzt!!	
	if( $big ){
		add_content_editor( substr( $ids['small'], 1 ) );
	}
	if( $small ){
		add_content_editor( substr( $ids['big'], 1 ) );
	}
	
}

//Ein Inhaltseingabefeld einer Seite hinzufügen
//	$id => ID der Textarea (ohne #)
//	$md => true (CodeMirror als Standard für Markdowneingabe)/ false (TinyMCE als Standard) [Hat nur beim ersten Aufruf innerhalb des Requests Auswirkungen]
function add_content_editor( $id, $md = false ){
	global $sitecontent, $allgsysconf, $add_content_editor_globals;
	
	//Funktion schon mal ausgeführt??
	if( !is_array( $add_content_editor_globals ) ){
		//nein
		//	Array mit Editorinfos erstellen
		$add_content_editor_globals = array(
			//JS lib noch nicht geladen
			'libload'  => false
		);
	}
	
	//Die Edtioren benötigen zusätzliche JS und CSS Dateien
	//	hier laden (wenn noch nicht getan!)
	if( !$add_content_editor_globals['libload'] ){

		$header = '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/tinymce/tinymce.min.js"></script>'."\r\n";
		$header .= '<script>var codemirrorloader_siteurl = "'.$allgsysconf['siteurl'].'", codemirrorloader_done = false;</script>'."\r\n";
		$header .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/codemirror/codemirrorloader.min.js"></script>'."\r\n";
		//Seite mit MD?
		//	hier gewollt?
		if( $md ){
			//JS mitteilen
			$header .= '<script>editorloader_hasmd = true;</script>';
		}
		$header .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/editorloader.min.js"></script>'."\r\n";
		
		$sitecontent->add_html_header( $header );
		
		$sitecontent->add_html_header( '<style>.CodeMirror{border: 1px solid black;}</style>' );
		
		$add_content_editor_globals['libload'] = true;
	}
	
	//Editor laden (per JS Funktion)	
	$sitecontent->add_html_header( '<script>editorloader_add( "'.$id.'" );</script>' );
	
	return true;
}

//CMS und KIMB-Software Versionsstings vergleichen
//	Verhaeltnis von $v1 zu $v2, z.B.:
//		return 'newer' -> $v1 neuer als $v2
//		return 'older' -> $v1 aelter als $v2
//		return 'same' -> $v1 gleich wie $v2
//		return false -> $v1 oder $v2 haben eine fehlerhafte Syntax
//
//	Beispiele und Syntax für Versionsstings:
//		V1.0B-p2
//		V2.11A
//		V2.3F-p1,2
//			Aufbau:
//				"V" für Version
//				Zahl für ganze Versionsnummer
//				"." Punkt als Komma
//				Zahl für Unterversionsnummer (max. 4 Stellen)
//				"A" oder "B" oder "F" für Alpha, Beta, Final Version
//				evtl.: 
//					"-p" für Patch
//		 			Nummer des Patches
//					weitere Nummer mit "," angeschlossen (Reihenfolge brachten: 1,2,3; nicht 2,3,1!!)
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

		//als Nachkommastelle behandeln (mal 1000 nehmen)
		$teil['komma'] = (int) str_pad( $teil['komma'], 4, 0 );

		//max 4 Stellen
		$teil['komma'] = (int) substr( $teil['komma'], 0, 4 );

		//Patch
		$papos = stripos( $ver , '-p', $lpos );
	
		//wenn -p gegeben, lesen
		if( $papos !== false  ){
			$patch = substr( $ver , $papos + 2 );
	
			$kpos = strrpos( $patch , ',' );
	
			if( $kpos !== false ){
				$patch = substr( $patch , $kpos + 1 );
			}
	
			$patch = preg_replace( "/\D/", '', $patch );  
	
			$teil['patch'] = $patch;
		}
		else{
			//sonst Patch 0
			$teil['patch'] = 0;
		}

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

//Ein Array wie make_menue_array_helper();, nur mit Path und ID, erstellen
//	Rückgabe Array
function make_path_array(){
	//Abhängigkeiten von make_menue_array(); erfüllen
	global $idfile, $menuenames, $menuearray, $fileidlist, $filelisti;

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	$menuearray = '';
	$fileidlist = '';
	$filelisti = '';

	//make_menue_array(); ausführen
	make_menue_array();

	//$menuearray durchgehen
	foreach( $menuearray as $menuear ){

		//akuelles Niveau lesen
		$niveau = $menuear['niveau'];

		//schon Durchgang vorher?
		if( !isset( $thisniveau ) ){
			//keiner, also $grpath neu setzen
			$grpath = '/'.$menuear['path'];
		}
		elseif( $thisniveau == $niveau ){
			//Niveaus unverändert, alten $grpath von letztem Teil (/ Trennung) befreien
			$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			//neuen Path ansetzen
			$grpath = $grpath.'/'.$menuear['path'];
		}
		elseif( $thisniveau < $niveau ){
			//altes Niveau kleiner, also tiefer rein ($grpath mit letztem Teil erweitern)
			$grpath = $grpath.'/'.$menuear['path'];
			//Menütiefe mitzählen
			$thisulaufp = $thisulaufp + 1;
		}
		elseif( $thisniveau > $niveau ){
			//altes Niveau größer, also wieder raus
			//	$grpath anpassen, mehrere Niveaus auf einmal raus möglich, daher per Schleife bis es passt
			$i = 1;
			while( $thisniveau != $niveau + $i  ){
				$i++;
				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			}
			//Menütiefe mitzählen
			$thisulaufp = $thisulaufp - $i;

			//weiterhin letzten / abschneiden
			$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			//und alten $grpath von letztem Teil (/ Trennung) befreien
			$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
			//neuen Path ansetzen
			$grpath = $grpath.'/'.$menuear['path'];
		}
		//URL hat immer eine / am Ende
		$url = $grpath.'/';

		//ID und Path[URL]den neuen Array hinzufügen
		$patharr['id'] = $menuear['requid'];
		$patharr['url'] = $url;

		//Ausgabearray erstellen
		$return[] = $patharr;

		//aktuelles Niveau für nächsten Durchgang speichern
		$thisniveau = $niveau;

	}

	//Ausgaben
	return $return;

}

//Pfad aus RequestID erstellen
//	$requid => RequestID
//	Rückgabe URL-Pfad oder URL ID-Zugriff (relatativ zu $allgsysconf['siteurl'] )
//direkte Weiterleitung z.B.: open_url( make_path_outof_reqid( $requid ) );
function make_path_outof_reqid( $requid ){
	global $allgsysconf;

	//URL-Rewriting an?
	if( $allgsysconf['urlrewrite'] == 'on' ){

		//Path Array erstellen und durchgehen
		foreach( make_path_array() as $path ){
			//wenn RequestID in Array == gewünscht RequestID, Pfad ausgeben
			if( $path['id'] == $requid ){
				return $path['url'];
			}
		}

		//Alternativ per ID-Zugriff
		return '/index.php?id='.$requid;
	}
	else{
		return '/index.php?id='.$requid;
	}
}

//Add-on Status prüfen
//	Rückgabe => boolean
//	$addon => Name (ID) des zu testenden Add-ons
//	$addoninclude => KIMBdbf includes.kimb
function check_addon_status( $addon, $addoninclude ){

	//alle Include Stellen durchgehen
	foreach( BEaddinst::$allinclpar as $par ){

		//nach Add-on und Stelle suchen
		if( $addoninclude->read_kimb_search_teilpl( $par , $addon ) ){
			//gefunden, also Add-on aktiviert
			return true;
		}

	}
	//nicht gefunden, deaktiviert
	return false;
}

//Add-on Infos holen
//	$nameid => Naem des Add-ons
//	Rückgabe: Array( bool, bool );
//		Index 0: Add-on installiert
//		Index 1: Add-on aktiviert
function check_addon( $nameid ){
	
	//NameID okay?
	if( preg_match( "/[^a-z_]/" , $nameid ) === 0 ){
	
		//Ordner des Add-ons vorhnaden?
		//	dann installiert!
		if( is_dir( __DIR__.'/../addons/'.$nameid.'/' ) ){
			$inst = true;
		}
		else{
			$inst = false;
		}
		
		//Status prüfen
		$stat = check_addon_status( $nameid, new KIMBdbf( 'addon/includes.kimb' ) );
		
		//Rückgabe
		return array( $inst, $stat );
	}
	else{
		//NameID unpassend
		return false;
	}
}

//Die RequestURL ohne alles nach dem ? extrahieren
//	Rückgabe => RequestURL
function get_req_url(){
	if( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ){
		$req = substr( $_SERVER['REQUEST_URI'] , '0', '-'.strlen(strrchr( $_SERVER['REQUEST_URI'] , '?' )));
	}
	else{
		$req = $_SERVER['REQUEST_URI'];
	}

	return $req;
}

//Sprachwahl Dropdown machen
//	$openurl => zu öffnende URL nach Sprachwahl z.B.: '"'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&id='.$_GET['id'].'&langid=" + val' ( '+ val'' wird als JavaScript Variable verwendet und enthält die gewählte LangID)
//	$langid => aktuel zu wählenden Sprache (per LangID)
function make_lang_dropdown( $openurl, $langid ){
		global $sitecontent;
		
		//Lang Datei lesen	
		$langfile = new KIMBdbf( 'site/langfile.kimb' );
		
		//Select beginnen (padding für Flaggen)
		$sitecontent->add_site_content('<select id="langs" style="padding:2px; padding-left:25px;" title="Bitte wählen Sie die Sprache, für die Sie die Inhalte verändern wollen!">');
		
		//alle Sprachen lesen und durchgehen
		foreach( $langfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){
			//gesamte Sprache lesen
			$vals = $langfile->read_kimb_id( $id );
			
			//aktuelle Sprache == Sprache des aktuellen foreach?
			if( $id == $langid ){
				//URL zu Flagge extra speichern
				$flagurl = $vals['flag'];
			}
			//Status der Sprache testen
			if( $vals['status'] == 'off' ){
				$statinfo = ' (deaktiviert)';
			}
			else{
				$statinfo = '';
			}
			
			//Select Option machen: Flagge und Sprachname mit Status; VALUE = ID
			$sitecontent->add_site_content('<option style="background: url( '.$vals['flag'].' ) center left no-repeat; padding:2px; padding-left:25px;" value="'.$id.'">'.$vals['name'].$statinfo.'</option>');
		}
		
		//Select beenden
		$sitecontent->add_site_content('</select>');
		
		//JS Code um aktuelle Sprache im Select zu aktivieren
		//bei Änderung entsprechenden Seite aufrufen
		$sitecontent->add_html_header('<script>	
		$( function() {
			$( "#langs" ).on( "change", function() {
				var val = $( "#langs" ).val();
				window.location = '.$openurl.';
			});
			$( "#langs" ).val( '.$langid.' );
			$( "#langs" ).css( "background", "url( '.$flagurl.' ) center left no-repeat" );
		});
		</script>');
}

//Array mit allen Menüpunkten erstellen
//Vereinfachter Zugriff für make_menue_array(); [erfüllt automatisch alle Abhängigekeiten]
//	$start => Menüdatei, welche als Oberste genommen werden soll (0 = first, einfach ID der Datei)
//	$deeper => Untermenüs mit Menüpunkten auslesen (Funktion verschachteln?)
//	Rückgabe: Menuearray
function make_menue_array_helper( $start = 0, $deeper = true ){
	//Abhängigekeiten erfüllen
	global $idfile, $menuenames, $menuearray, $fileidlist, $filelisti;
	
	$menuearray = array();
	$fileidlist = array();
	$filelisti;
	
	//Zahl aus Start in MenuePathFile umschreiben
	if( $start == 0 || !is_numeric( $start ) ){
		$startfi = 'url/first.kimb';
		$startint = 'first';
		$filebefore = 'none';
		$oldfileidlisti;
	}
	else {
		$startfi = 'url/nextid_'.$start.'.kimb';
		$startint = $start;
		
		$filelisti = 0;
		
		//URL Datei finden, deren Untermenü hier gelesen wird!
		//	Zuerst in first gucken, dann allen anderen Datein anschauen
		$menfi = new KIMBdbf( 'url/first.kimb' );
		$fid = $menfi->search_kimb_xxxid( $start, 'nextid' );
		
		//	gefunden in first?
		if( $fid !== false ){
			//first war vorher
			$fileidlist[0] =  'first';
			$oldfileidlisti = '0';
		}
		else{
			//nicht gefunden
			
			//alle URL-Dateien lesen
			$mens = scan_kimb_dir( 'url/' );
			//und durchgehen
			foreach ($mens as $value) {
				//nur vom Namen her passende nutzen
				if( preg_match( '/nextid_([0-9]*).kimb/', $value ) ){
					//Datei einlesen
					$menfi = new KIMBdbf( 'url/'.$value );
					//Gucken ob Überdatei von Startdatei
					$fid = $menfi->search_kimb_xxxid( $start, 'nextid' );
					
					//Gefunden?
					if( $fid !== false ){
						//ID merken
						$fileidlist[0] = preg_replace(  '/[^0-9]/', '' ,$value );
						$oldfileidlisti = '0';
						//fertig
						break;
					}
				}
			}
		}
	}

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	
	//ausführen	
	make_menue_array( $startfi, '1' , $startint , $oldfileidlisti , $deeper );
	
	//Rückgabe
	return $menuearray;
}

//Alle Seiten in Array listen
//	Rückgabe => Array[] array( 'site' => Seitename, 'id' => SeitenID )
//	$dir => Verzeichnis mit Seitendateien unter kimb-data/
function list_sites_array( $dir = 'site/' ){
	//KIMBdbf Verzeichnis lesen
	$sites = scan_kimb_dir( $dir );
	//nacheinander durchgehen
	foreach ( $sites as $site ){
		if( $site != 'langfile.kimb'){
			//wenn nicht langfile
			
			//Seitendatei lesen und ID sowie Seitenamen bestimmen
			$sitef = new KIMBdbf('site/'.$site);
			$id = preg_replace("/[^0-9]/","", $site);
			$title = $sitef->read_kimb_one('title');
		
			//der Ausgabe hinzufügen
			$allsites[] = array( 'site' => $title, 'id' => $id );
		}
	}
	
	//ausgeben
	return $allsites;
}

function replace_urloutofid( $content ){
	//Es ist möglich eine RequestID als Verweis zu definieren, welche bei der Ausgabe automatisch in die aktuelle URL umgesetzt wird
	//	<!--URLoutofID=1--> wird zu http://cms.de/index.php?id=1 oder http://cms.de/home
	//Diese Funktion führt die Ersetzungen durch.
	//	$content => String in dem ersetzt werden soll.
	//	Rückgabe => String mit Ersetzungen (wenn keine Platzhalter gefunden unverändert)
	global $allgsysconf;
	
	//wurde der PLatzhalter verwendet
	if( strpos($content, '<!--URLoutofID=' ) !== false || strpos($content, '&lt;!--URLoutofID=' ) !== false ){
	
		//TinyMCE codiert < und > mit &lt; und &gt; --- dies wieder Rückgängig machen		
		$content = str_replace( '&lt;!--URLoutofID=',  '<!--URLoutofID=' , $content );
		$content = str_replace( '--&gt;',  '-->' , $content );
		
		//die erste Stelle für die Ersetzung suchen
		$stelle = strpos($content, '<!--URLoutofID=' );
		//solange Stellen gefunden Ersetzungen durchführen
		while( $stelle !== false ){

			//Ende des Platzhalters finden
			//Anfang schon in $stelle
			$end = strpos($content, '-->', $stelle );
			
			//Die ID im Platzhalter extrahieren
			
			//Stellen der ID Feststellen
			//	Ende des Platzhalters - Anfang des Platzhalters + 15 (Länge des ersten Teils des Platzhalters) = Länge der ID
			$idlen = $end - ( $stelle + 15 );
			//ID in $id speichern
			$id = substr($content, $stelle + 15 , $idlen );
			
			//aus der ID die URL machen
			$link = $allgsysconf['siteurl'].make_path_outof_reqid( $id );
			
			//den String am Platzhalter teilen
			$teile = explode( '<!--URLoutofID='.$id.'-->', $content, 2 );
			
			//und neu zusammensetzen
			$content = $teile[0].$link.$teile[1];
			
			//nach einem neuen Platzhalter suchen (für den nächsten Durchgang)
			$off = strlen($teile[0].$link);
			$stelle = strpos($content, '<!--URLoutofID=', $off );				
		}
			
	}
	
	//fertigen String zurückgeben
	return $content;
}

//Parse Markdown
//	$md => Markdown String
//	Rückgabe => HTML String
function parse_markdown( $md ){
	//ParsedownExtra Klasse
	$obj = new ParsedownExtra();
	//Parsen
	$html =  $obj->text( $md );
	//OBJ löschen
	unset( $obj );
	//Zurückgeben
	return $html;
}

//Umbruchbaren String machen
//	$str => String, welcher vom Browser umgebrochen werden soll
//	$zeichen => Nach wie vielen Zeichen soll immer ein Umbruch möglich sein
//	Rüchgabe: String mit Umbruchstellen (&shy;)
//		Achtung: Diese Funktion zerstört Umlaute!! 
function breakable( $str, $zeichen = 10 ){
	$a = str_split( $str, $zeichen );
	return implode( '&shy;', $a );
}

// Funktionen von Add-ons hinzufügen
require_once( __DIR__.'/../addons/addons_funcclass.php' );
?>
