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

//Hier werden die IDs aus dem Request erstellt.
//Unterscheidung zwischen URL-Rewrite und ID Zugriff

//Es kann zwischen URL-Rewriting per .htaccess oder per $_SERVER['REQUEST_URI'] entschieden werden (Wahl in der Konfiguration)
//	Nur wenn $_SERVER['REQUEST_URI'] gesetzt ist, URL-Rewriting aktiviert und  use_request_url erlaubt ist sowie kein ID-Zugriff stattfindet,
//	wird mit use_request_url gearbeitet
if( isset($_SERVER['REQUEST_URI']) && $allgsysconf['urlrewrite'] == 'on' && !isset($_GET['id']) && $allgsysconf['use_request_url'] == 'ok'){
	$_GET['url'] = $_SERVER['REQUEST_URI'];
}

//	Die URL für das URL-Rewriting befindet sich hier immer in $_GET['url'], sie wird benutzt, wenn kein ID-Zugriff stattfindet und 
//	URL-Rewriting aktiviert ist
if( isset($_GET['url']) && $allgsysconf['urlrewrite'] == 'on' && !isset($_GET['id']) ){

	//Unter Other -> Umzug ist es möglich ganze URLs auf bestimmte Seiten des CMS weiterzuleiten, dies erfolgt hier
	//Lesen der KIMBdbf
	$oldurlfile = new KIMBdbf( 'menue/oldurl.kimb' );

	//ist die aufgerufenen URL für eine Weiterleitung reserviert?
	$oldurl = $oldurlfile->search_kimb_xxxid( $_GET['url'] , 'url' );

	//sofern ja, dann machen
	if( $oldurl != false ){
		//Lesen der RequestID, auf welche weitergleitet werden soll
		$newrequid = $oldurlfile->read_kimb_id( $oldurl , 'id' );
		//permanet weiterleiten
		open_url( make_path_outof_reqid( $newrequid ), 'insystem', 301 );
	}

	//Verarbeitung der aufgerufenen URL
	// URL => RequestID

	//Aufteilen der URL in "Verzeichnisse"
	$urlteile = explode( '/' , $_GET['url'] );

	//Der erste Teil des Array mit den URL-Verzeichnissen ist oft leer ($_GET['url'] beginnt mit /)
	$i = 0;
	if( empty( $urlteile[$i] ) ){
		//zum nächsten Teil gehen
		$i++;
	}

	//Das CMS ist nicht immer auf einer ganzen Domain installiert, es kann auch in einem Verzeichnis liegen
	//sofern dies der Fall ist, enthält $_ GET['url'] auch dieses Verzeichnis. Das Verzeichnis muss weg, denn die
	//URLs des CMS beginnen dann erst ein Verzeichnis später.
	
	//Aus der Systemkonfiguration das Verzeichnis des CMS lesen 
	$pos = strpos( $allgsysconf['siteurl'] , '/' , 8 );
	$wegteil = substr( $allgsysconf['siteurl'] , $pos + 1 );
	//es muss nicht immer nur ein Verzeichnis sein, dann in ein Array teilen
	$wegteile = explode( '/' , $wegteil );
	//Array Schritt für Schritt durchgehen
	foreach( $wegteile as $teil ){
		//Wenn der Wert des Array aus $_GET['url'] mit dem des gerade erstellten Array übereinstimmt,
		//Zeiger im Arry aus $_GET['url'] weiterschieben
		//wenn dies nicht mehr der Fall beenden
		if( $urlteile[$i] == $teil ){
			$i++;
		}
		else{
			break;
		}
	}
	
	//Das Array mit den URL-Verzeichnissen aus $_GET['url'] ist jetzt mit dem key $i an der Stelle wo das
	//CMS mit dem URL-Rewriting beginnen kann
	
	//Beim URL-Rewriting ist bei akitvierten mehrsprachigen Seiten das Lang-Tag in der URL zu finden
	//Dieses wird hier verarbeitet, wenn Sprache an und nicht die 'index.php' aufgerufen wird
	if( $allgsysconf['lang'] == 'on'  && $urlteile[$i] != 'index.php'){
		//lesen der Sprachdatei
		$langfile = new KIMBdbf( 'site/langfile.kimb' );
		
		//Die Sprachen haben alle eine ID und eine Tag 
		//	Die Standardsprache hat immer die ID 0
		//Lesen des Tags Standardsprache
		$langnull = $langfile->read_kimb_id( '0', 'tag' );
		$langid = (int) '0';
		
		//gibt es überhaupt noch einen weiteren Teil im Array mit den URL-Verzeichnissen
		if( empty( $urlteile[$i] ) ){
			//wenn nicht keine Sprache gewählt
			$langid = false;
			$done = true;
		}
		//ist die Standardsprache gewählt?
		elseif( $langnull != $urlteile[$i] ){
			//nein, nach anderer Sprache schauen
			$langid = $langfile->search_kimb_xxxid( $urlteile[$i] , 'tag' );
			$done = true;
		}
		
		//keine Sprache gewählt und auch nicht Standardsprache ($done wäre dann nicht true)
		if( $langid == false && $done ){
			//Besucher soll zu einer Sprache umgeleitet werden
			$opennew = true;
			//evtl. ein nicht im CMS vorhandener Lang-Tag verwendet?
			if( strlen( $urlteile[$i] ) == 2 ){
				//Überprüfung anhand der ersten URL Datei
				$file = new KIMBdbf('url/first.kimb');
				if( $file->search_kimb_xxxid( $urlteile[$i] , 'path' ) == false ){
					//Der Key von dem Array mit den URL-Verzeichnissen muss erhöht werden, man will die Umleitung ja ohne den 
					//falsche Tag durchführen
					$iplus = true;	
				}
			}
		}
		//es wurde eine Sprache über den Request gewählt
		elseif( is_numeric( $langid ) ){
			//lesen aller Daten über die Sprache
			$requestlang = $langfile->read_kimb_id( $langid );
			//das Array $requestlang dient im weiteren Programmablauf dazu alles über die aktuell gewählte Sprache auffindbar zu machen
			$requestlang['id'] = $langid;
			//wenn eine Sprache gewählt ist, aber daktiviert, dann  muss der Besucher
			//zu einer anderen Sprache umgeleitet werden
			if( $requestlang['status'] == 'off' ){
				$opennew = true;
				//Der Key von dem Array mit den URL-Verzeichnissen muss erhöht werden, man will die Umleitung ja ohne die 
				//deaktivierte Sprache durchführen
				$iplus = true;
			}
		}
		
		//Umleiten des Users auf eine andere Sprache?
		if( $opennew ){
			
			//Der Browser eines Benutzer gibt Sprachwünsche an, bei der Umleitung daran orientieren
			$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			//es können mehrere Sprachen gewünscht werden (je früher genannt, desto lieber gesehen)
			foreach($langs as $lang){
				//durchgehen der Wünsche
				
				//Die Wünsche sehen teilweise so aus: en-US
				//	daher nur die ersten zwei Zeichen für als Lang-Tag nutzen
				$lang = substr($lang, 0, 2);
				
				//Ist die Standardsprache vielleicht vom Benutzer gewünscht? 
				$langnull = $langfile->read_kimb_id( '0', 'tag' );
				if( $langnull == $lang ){
					//wenn ja, LangID = 0
					$id = (int) '0';
					//schon alles okay!
					$okay = true;
				}
				else{
					//Ist eine andere Sprache des CMS vom Benutzer gewünscht?
					$id = $langfile->search_kimb_xxxid( $lang , 'tag' );
					//nicht unbedingt okay, vielleicht nichts gefunden
					$okay = false;					
				}

				//wurde eine passende Sprache gefunden oder ist es okay (Standardsprache)
				if( $id != false || $okay ){ 
					//den Tag der jetzt gewählten Sprache herausfinden
					$langarr = $langfile->read_kimb_id( $id );
					//wenn Status an, dann URL für Umleitung beginnen (Tag als erstes anfügen)
					//forach verlassen
					if( $langarr['status'] == 'on' ){
						$url = '/'.$lang;
						break;
					}
				}
			}
			
			//Ist das foreach verlassen worden, ohne eine für den Benutzer passende Sprache finden zu können? 
			if( empty( $url ) ){
				//Verwendung der Standardsprache
				$url = '/'.$langfile->read_kimb_id( '0', 'tag' );
			}
			
			//Wenn oben gewünscht Key von dem Array mit den URL-Verzeichnissen erhöhen
			if( $iplus ){
				$i++;
			}
			
			//solange noch Inhalt im Array mit den URL-Verzeichnissen
			while( !empty( $urlteile[$i] ) ){
				//Erzeugung der URL für die Weiterleitung, man soll beim Sprachwechsel nicht immer auf der Startseite laden
				$url .= '/'.$urlteile[$i];
				$i++;
			}
			
			//Umleiten auf die gewünscht URL
			open_url( $url );
			
			//Programm verlassen
			die;
		}
		else{
			//Den Sprach-Tag-Teil des  Array mit den URL-Verzeichnissen verlassen
			$i++;
			
			//solange noch Inhalt im Array mit den URL-Verzeichnissen
			//Verwendung des Key $ii um $i für später 'sauber'' zu lassen
			$ii = $i;
			while( !empty( $urlteile[$ii] ) ){
				//Erzeugung der URL für die Buttons zum Sprachwechsel, man soll nicht immer auf der Startseite laden
				$url .= '/'.$urlteile[$ii];
				$ii++;
			}
			
			//Erzeugung eines Array mit den allgemeinen Sprachdaten z.B. für das Theme
			//	alle Sprachen durchgehen
			foreach( $langfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){
				//jede Sprache lesen
				$vals = $langfile->read_kimb_id( $id );
				if( $vals['status'] == 'on' ){
					//wenn aktiviert Sprache aktiviert
					//	Sprachwechesel URL anfügen
					$vals['thissite'] = $allgsysconf['siteurl'].'/'.$vals['tag'].$url;
					//Array für CMS/ Theme füllen
					$allglangs[] = $vals;
				}
			}
		}
	}
	
	//eigentliche Erzeugung der ID aus der URL
	
	//erste URL-Date lesen (in dieser gehts immer los)
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
