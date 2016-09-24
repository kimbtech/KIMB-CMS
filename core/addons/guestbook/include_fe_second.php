<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2014 by KIMB-technologies.eu
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
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//Gästebuchkonfiguration laden
$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );

//Hat die aktuelle Seite ein Gästebuch?
//Und existiert kein Fehler 403
//	(auch wenn per Add-on Wish verboten, kann Error 403 noch nach Überprüfung durch Wish gesetzt werden)
if( $guestbook['file']->read_kimb_search_teilpl( 'siteid' , $allgsiteid ) && $allgerr != '403' ){
	
	//jQuery
	$sitecontent->add_html_header( '<!-- jQuery -->' );
	//CSS für Beiträge
	$sitecontent->add_html_header('<style>'.$guestbook['file']->read_kimb_one( 'css' ).'</style>');

	//Datei der Beiträge laden
	$guestbook['sitefile'] = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'.kimb' );

	//den Anfang des Gästebuchs mit hr und ID kennzeichnen
	$sitecontent->add_site_content( "\r\n".'<hr id="guestbooktop" /><br />'."\r\n" );
	
	//Gästebuch überhaupt erlaubt?
	
	//Felogin vorhanden und "nur felogin User" aktiviert?
	if( function_exists( 'check_felogin_login' ) && $guestbook['file']->read_kimb_one( 'nurfeloginuser' ) == 'on' ){
		//Loginstatus prüfen
		//	Gruppe und User aus Session, SiteID des Gästebuchs und Loginstatus zeigen
		//	(ist User eingeloggt und darf er Seite sehen)		
		if( check_felogin_login( '---session---', '---allgsiteid---', true ) ){
			//okay, Gästebuch kann geladen werden			
			$guestbook['add'] = true;
		}
		else{
			//nicht erlaubt
			$guestbook['add'] = false;
		}
	}
	else{
		//kein Login verfügbar oder nicht nötig
		//	Gästebuch kann geladen werden
		$guestbook['add'] = true;
	}
	
	//wenn Gästebuch geladen werden kann:
	if( $guestbook['add'] ){
	
		//ist ein Platz gesetzt (das heißt User hat Formular gesendet!)
		if( isset( $_POST['place'] ) ){

			//Ist felogin vorhanden
			if( function_exists( 'check_felogin_login' ) ){
				//ist User eingeloggt und darf er Seite sehen?
				if( check_felogin_login( '---session---', '---allgsiteid---', true ) ){
					//wenn dass der Fall ist, muss der User keinen Namen, keine Mail und keinen Captcha-Code eingeben
					//	Captcha so manipulieren, dass check_captcha() true zurückgibt
					$_SESSION['captcha_code'] =makepassw( mt_rand(5, 7) , '', 'numaz');
					$_REQUEST['captcha_code'] = $_SESSION['captcha_code'];
					//	Pseudo-E-Mail-Adresse setzen (Verweis auf Felogin-User)
					$_POST['mail'] = $_SESSION['felogin']['user'].'@feloginuser.sys';
					//	Name des Felogin-Users verwenden
					$_POST['name'] = $_SESSION['felogin']['name'];
				}
				//wenn felogin vorhanden, wird die Pseudo-Domain 'feloginuser.sys' auf felogin-User beschränkt 
				elseif( substr( $_POST['mail'], -16 ) == '@feloginuser.sys' ){
					$_POST['mail'] = 'error';
				}
			}
			
			//Platz für neuen Beitrag auswerten
			if( $_POST['place'] != 'new' && is_numeric( $_POST['place'] ) ){
				//eine Antwort (alles was nicht new ist)
				
				//ID des Platztes (ID in der dbf zu welcher der neue Beitrag eine Antwort ist)
				$id = $_POST['place'];
				//sollte eine Eingabe falsch sein, JS-Code um Eingabeformular richtig zu laden
				$addjs = '$( function(){ answer( '.$id.', "yes" ); });';
			}
			else{
				//normaler Beitrag
				
				//sollte eine Eingabe falsch sein, JS-Code um Eingabeformular richtig zu laden
				$addjs = '$( function(){ add( "new" ); });';
			}

			//Eingabe prüfen
			//	Captcha, Name, Mail, Inhalt der Mitteilung
			if( check_captcha() && !empty( $_POST['name'] ) && !empty( $_POST['mail'] ) && !empty( $_POST['cont'] ) ){

				//E-Mail-Adresse prüfen (nur Syntax, User könnte sich eine ausdenken)
				if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) ){
					
					//Platz für neuen Beitrag auswerten, diesmal
					//entsprechende dbf laden
					if( $_POST['place'] != 'new' && is_numeric( $_POST['place'] ) ){
						//eine Antwort (alles was nicht new ist)
						
						//ID des Platztes (ID in der dbf zu welcher der neue Beitrag eine Antwort ist)
						$id = $_POST['place'];
						//Antwortdatei für Beitrag öffnen
						$addfile = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'_answer_'.$id.'.kimb' );
						//beim dem Beitrag, zu welchem eine Antwort verfasst wurde, speichern,
						//dass er jetzt Antworten hat
						$guestbook['sitefile']->write_kimb_id( $id , 'add' , 'antwo' , 'yes' );
						//per JS die im Browser gesicherten Eingaben löschen
						//Antworten zu dem Beitrag gleich laden
						$guestbook['showadd'] = 'delsubmit();'."\r\n".'$( function(){ answer( '.$id.', "yes" ); });';
					}
					else{
						//normaler Beitrag
						
						//Antwortdatei ist die normale Gästebuchdatei
						$addfile = $guestbook['sitefile'];
						//per JS die im Browser gesicherten Eingaben löschen
						$guestbook['showadd'] = 'delsubmit();';
					}

					//Mail in var setzen
					$mail = $_POST['mail'];

					//den Namen und die Mitteilung von falschem Code befreien
					//	genauso wie für Vorschau
					$array = make_guestbook_html( $_POST['cont'], $_POST['name'] );
					$name = $array[1];
					$cont = $array[0];

					//neue ID für Beitrag bestimmen
					$guestbook['newid'] = $addfile->next_kimb_id();

					//ID Liste lesen
					$guestbook['idlist'] = $addfile->read_kimb_one( 'idlist' );
					//wenn ID Liste leer:
					if( empty( $guestbook['idlist'] ) ){
						//erste ID reinschreiben
						$addfile->write_kimb_new( 'idlist' , $guestbook['newid'] );
					}
					else{
						//sonst mit Komma getrennt hinzufügen
						$addfile->write_kimb_replace( 'idlist' , $guestbook['idlist'].','.$guestbook['newid'] );
					}

					//Daten in die neue ID einfügen
					//	Name
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'name' , $name );
					//	Mail
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'mail' , $mail );
					//	Mitteilung
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'cont' , $cont );
					//wenn IP gespeichert werden soll:
					if( $guestbook['file']->read_kimb_one( 'ipsave' ) == 'on' ){
						//	IP speichern
						$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'ip' , $_SERVER['REMOTE_ADDR'] );
					}
					else{
						//sonst 
						//	IP 0.0.0.0
						$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'ip' , '0.0.0.0' );
					}
					//	Timestamp speichern
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'time' , time() );
					//	Status wie in der Konfiguration gewünscht setzen 
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'status' , $guestbook['file']->read_kimb_one( 'newstatus' ) );
					//	noch keine Antwort
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'antwo' , 'no' );

					//Soll eine E-Mail gesendet werden?
					//Ist die E-Mail-Adresse gesetzt?
					if( $guestbook['file']->read_kimb_one( 'mailinfo' ) == 'on' && !empty($guestbook['file']->read_kimb_one( 'mailinfoto' )) ){

						//Zufallscode, um Status per Link ändern zu können
						$statusauthcode = makepassw( 30, '', 'numaz' );
						//	Statuscode merken
						$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'statauth' , $statusauthcode );

						//Status lesen
						$newstatus = $guestbook['file']->read_kimb_one( 'newstatus' );
						//Link für Status ändern
						$link = $allgsysconf['siteurl'].'/ajax.php?addon=guestbook&review&site='.$allgsiteid.( $_POST['place'] != 'new' ? '&file='.$id : '' ).'&id='.$guestbook['newid'].'&auth='.$statusauthcode;
								
						//Text für Mail machen
						$text = 'Hallo Admin,'."\r\n";
						$text .= 'es gibt einen neuen Eintrag im Gästebuch der Seite '.$allgsiteid.'.'."\r\n";
						$text .= 'Name: '.$name."\r\n";
						$text .= 'E-Mail: '.$mail."\r\n";
						$text .= '<= Mitteilung ===================================================>'."\r\n";
						$text .= $cont."\r\n";
						$text .= '<================================================================>'."\r\n";
						$text .= 'Status: '.($newstatus == 'on' ? 'sichtbar' : 'verborgen' )."\r\n";
						$text .= 'Status ändern: '.$link."\r\n";

						//GNS URLadd
						$urladd = array(
							'url' => $link,
							'urlt' => 'Status ändern'
						); 
						
						//Mail absenden
						send_mail( $guestbook['file']->read_kimb_one( 'mailinfoto' ) , $text, 'plain', 1, $urladd );
					}

					//wenn der Status auf off gesetzt wurde, User informieren
					if( $guestbook['file']->read_kimb_one( 'newstatus' ) == 'off' ){
						//User soll sich nicht wundern, dass sein Beitrag nicht gezeigt wird
						$sitecontent->add_site_content('<h3>'.$allgsys_trans['addons']['guestbook']['pruef'].'</h3>'."\r\n");
					}

					//FullHTMLCache aktiviert?
					if($allgsysconf['fullcache'] == 'on'){
						//diese Seite jetzt nicht cachen
						$fullsitecache->end_cache();

						//den FullHTMLCache leeren
						//	jetzt muss ja alles mit dem neuen Eintrag erstellt werden
						FullHTMLCache::clear_cache();
					}
				}
				else{
					//Fehlermeldung wenn Mail falsch
					$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['guestbook']['mailerr'].'</h3>' );
					//oben vorbereiteten JS Code für erneute Eingabe ausgeben
					$guestbook['showadd'] = $addjs;
				}
			}
			else{
				//Fehlermeldung wenn nicht alle Felder gefüllt
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['guestbook']['caerr'].'</h3>' );
				//oben vorbereiteten JS Code für erneute Eingabe ausgeben
				$guestbook['showadd'] = $addjs;
			}
		}
	}

	//JS Variablen setzen
	//	dynamische Werte für aktuelle Seite
	$header = '<script>var siteurl = "'.$allgsysconf['siteurl'].'", siteid = "'.$allgsiteid.'", addmitt = '.json_encode( $guestbook['add'] ).'; ';
	//	Übersetzungen
	foreach( $allgsys_trans['addons']['guestbook']['externjs'] as $key => $val ){
		$header .= 'var '.$key.' = "'.$val.'"; ';
	}
	$header .= '</script>';
	//dem Header anfügen
	$sitecontent->add_html_header( $header );
	//die externe JavaScript Datei laden
	$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/addondata/guestbook/guestbook.min.js" type="text/javascript" ></script>');
	
	//den Code, welcher oben nach der Auswertung der Eingaben vorgegeben wurde, ausgeben
	$sitecontent->add_html_header('<script>'.$guestbook['showadd'].'</script>');

	//ID Liste der Beiträge lesen und IDs in Array
	$guestbook['ids'] = explode( ',' , $guestbook['sitefile']->read_kimb_one( 'idlist' ) );
	//alle IDs in foreach
	foreach( $guestbook['ids'] as $guestbook['id'] ){
		//Werte des Beitrages lesen
		$array = $guestbook['sitefile']->read_kimb_id( $guestbook['id'] );
		//auch die ID des Beitrages speichern
		$array['file_id'] = $guestbook['id'];
		//alles in Array
		$guestbook['alles'][] = $array;
	}

	//oben erstelltes Array durchgehen
	foreach( $guestbook['alles'] as $guestbook['einer'] ){
		//Beitrag aktiviert?
		if( $guestbook['einer']['status'] == 'on' ){

			//Beitrag der Ausgabe anfügen
			$guestbook['output'] .= '<div class="guest" >'."\r\n";
			//	Name		
			$guestbook['output'] .= '<div class="guestname" >'.$guestbook['einer']['name']."\r\n";
			//	Zeit
			$guestbook['output'] .= '<span class="guestdate">'.date( 'd-m-Y H:i:s' , $guestbook['einer']['time'] ).'</span>'."\r\n";
			$guestbook['output'] .= '</div>'."\r\n";
			//	Inhalt
			$guestbook['output'] .= $guestbook['einer']['cont']."\r\n";
			
			//	Kommentar
			if( !empty( $guestbook['einer']['comm'] ) ){
				$guestbook['output'] .= '<div class="guestcomment">'."\r\n";
				$guestbook['output'] .= '<span>'.$allgsys_trans['addons']['guestbook']['ajax']['adminkomm'].'</span>'."\r\n";
				$guestbook['output'] .= $guestbook['einer']['comm']."\r\n";
				$guestbook['output'] .= '</div>'."\r\n";
			}
			
			//ID in $i
			$i = $guestbook['einer']['file_id'];
			//Antworten vorhanden?
			if( $guestbook['einer']['antwo'] == 'yes' ){
				//wenn ja, dann Button um Antworten zu laden und neue hinzuzufügen 
				$guestbook['output'] .= '<hr /><button onclick="answer( '.$i.', \'yes\' );">'.$allgsys_trans['addons']['guestbook']['awles'].'</button>'."\r\n";
			}
			else{
				//wenn keine Antworten, dann Button neue hinzuzufügen
				$guestbook['output'] .= '<hr /><button onclick="answer( '.$i.', \'none\' );">'.$allgsys_trans['addons']['guestbook']['awadd'].'</button>'."\r\n";
			}
			
			$guestbook['output'] .= '</div>'."\r\n";
			//divs, in welche die Antworten geladen werden, der Ausgabe hinzufügen 
			$guestbook['output'] .= '<div class="answer_'.$i.' answer" style="display:none;" ><div id="answer_'.$i.'_dis" ></div><hr /><div id="answer_'.$i.'_add" class="answer_add" ></div></div>'."\r\n\r\n";

			//Einträge vorhanden
			$guestbook['eintr'] = 'yes';
		}
	}

	//wenn keine Einträge:
	if( !isset( $guestbook['eintr'] ) ){
		//Meldung, dass keine Mitteilungen
		$guestbook['output'] .= '<div class="guest" >'."\r\n";		
		$guestbook['output'] .= $allgsys_trans['addons']['guestbook']['nomitt'];
		$guestbook['output'] .= '</div>'."\r\n\r\n";
	}

	//Ausgabe der Einträge
	$sitecontent->add_site_content($guestbook['output'] );

	//wenn hinzufügen erlaubt:
	if( $guestbook['add'] ){

		$sitecontent->add_site_content( '<div class="guest" >'."\r\n" );
		//Button um Formular zu hinzufügen zu laden
		$sitecontent->add_site_content('<button onclick="add( \'new\' ); " id="guestbuttadd">'.$allgsys_trans['addons']['guestbook']['add'].'</button>'."\r\n" );
		$sitecontent->add_site_content('<div style="display:none;" id="guestadd" >'."\r\n" );
		//dieser div wird per AJAX gefüllt
		
		//Button um Formular wieder auszublenden
		$sitecontent->add_site_content('</div><hr /><button onclick="dis(); " style="display:none;" id="guestbuttdis" >'.$allgsys_trans['addons']['guestbook']['dis'].'</button></div>'."\r\n" );
	}
	else{
		//Meldung, wenn keine Rechte einen neuen Beitrag hinzuzufügen
		$sitecontent->add_site_content('<div class="guest"><button disabled="disabled">'.$allgsys_trans['addons']['guestbook']['add'].'</button> ('.$allgsys_trans['addons']['guestbook']['login'].') </div>'."\r\n" );
	}	
}

unset( $guestbook );
?>
