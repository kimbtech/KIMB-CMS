<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies
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

//Sind POST Daten für die Vorschau gegeben?
if( !empty( $_POST['vorschau_name'] ) && !empty( $_POST['vorschau_cont'] ) ){
	//Vorschau
	
	//Wenn der Username ---username--- ist und ein Name in der Session von felogin definiert ist, dann diesen Namen nutzen
	if( $_POST['vorschau_name'] == '---username---' && !empty($_SESSION['felogin']['name']) ){
		//Namen nutzen
		$_POST['vorschau_name'] = $_SESSION['felogin']['name'];
	}
	//wenn kein Name in der Session ist
	elseif($_POST['vorschau_name'] == '---username---'){
		//einfach Name setzen (wird beim Eintrag in dbf noch angepasst)
		$_POST['vorschau_name'] = 'Name';
	}
	
	//Aus der Eingabe sauberes HTML machen, wie es im Gästebuch eingetragen werden kann (nur wenig HTML, URLs zu Links, ...)
	$arr = make_guestbook_html( $_POST['vorschau_cont'], $_POST['vorschau_name'] );
	
	//Ausgeben wie einen Beitrag
	echo '<div class="guest" >'."\r\n";		
	echo '<div class="guestname" >'.$arr[1]."\r\n";
	echo '<span class="guestdate">'.date( 'd-m-Y H:i:s' ).'</span>'."\r\n";
	echo '</div>'."\r\n";
	echo $arr[0]."\r\n";
	echo '</div>'."\r\n\r\n";
	
	//fertig
	//	Daten werden per AJAX in einem div zur Vorschau gezeigt
	die;
}
//Beitrag hinzufügen per AJAX laden
elseif( isset( $_GET['loadadd'] ) ){
	
	//Lang nehmen (von Add-on API)
	$lang = $allgsys_trans['addons']['guestbook']['ajax'];
	
	//Gästebuchkonfiguration laden
	$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );
	
	//die Übergabe Platz ist für später wichtig (Antwort oder Beitrag und wo)
	//	$pl wird in der ID vieler Elemente verwendet, nur so wird die Vorschau in den richitgen div geladen
	//	und auch die Inhalt stimmen (es können mehere Formulare gleichzeitig geladen sein
	//	(User will erst antworten, dann aber doch einen neuen Beitrag verfassen))
	$pl = htmlspecialchars( $_GET['pl'] , ENT_COMPAT | ENT_HTML401,'UTF-8');
	//neuer Beitrag -> ID 0 (0 kommt in der dbf nicht vor)	
	if( $pl == 'new' ){
		$pl = 0;
	}
	
	//Formular beginnen
	echo ('<form action="#guestbooktop" method="post" onsubmit = "return savesubmit();" >');

	//Felogin Integration
	//	Name und E-Mail müssen nicht angegeben werden
	//Ist felogin verfügbar und ist der User für diese Seite eingeloggt oder überhaupt eingeloggt? 
	if( !function_exists( 'check_felogin_login' ) || !check_felogin_login( '---session---', '---none---', true ) ){
		//nur wenn User nicht eingeloggt oder felogin nicht da nach Namen und E-Mail fragen
		//	Input Name mit $pl ID
		echo ('<input name="name" type="text" id="name" class="name_'.$pl.'" placeholder="'.$lang['name'].'" > <!--[if lt IE 10]> ('.$lang['name'].') <![endif]--> <br />'."\r\n");
		//	Input Mail mit $pl ID
		echo ('<input name="mail" type="text" id="mail" placeholder="'.$lang['mail'].'" > ('.$lang['mail'].' - '.$lang['nopu'].') <br />'."\r\n");
	}
	//Textfeld für Mitteilung
	echo ('<textarea name="cont" id="cont" class="cont_'.$pl.'" placeholder="'.$lang['cont'].'" style="width:75%; height:100px;" ></textarea> <!--[if lt IE 10]> ('.$lang['mail'].') <![endif]--> <br />'."\r\n");
	//erlaubtes HTML mit $pl ID
	echo ('('.$lang['allhtml'].': &lt;b&gt; &lt;/b&gt; &lt;u&gt; &lt;/u&gt; &lt;i&gt; &lt;/i&gt; &lt;center&gt; &lt;/center&gt;)<br />'.$lang['url'].'<br />'."\r\n");
	//divs für Vorschau mit $pl ID
	echo ('<div style="display:none;" id="prewarea_'.$pl.'" ><div style="background-color:orange; padding:10px; margin:10px;" id="prew_'.$pl.'" ></div>('.$lang['prev'].')<br /></div>'."\r\n");

	//Felogin Integration
	//	Captcha muss nicht angegeben werden
	//Ist felogin verfügbar und ist der User für diese Seite eingeloggt oder überhaupt eingeloggt? 
	if( !function_exists( 'check_felogin_login' ) || !check_felogin_login( '---session---', '---none---', true ) ){
		//nur wenn User nicht eingeloggt oder felogin nicht da nach Captcha fragen
		echo ( make_captcha_html() );
		//Infotext zu Captcha
		echo ('<br />('.$lang['captext'].')<br />'."\r\n");
	}

	//wird die IP gesichert?
	if( $guestbook['file']->read_kimb_one( 'ipsave' ) == 'on' ){
		//wenn ja, Hinweis
		echo ('('.$lang['ipsave'].')<br />'."\r\n");
	}
	//Platz übertragen (ohne HTML-Code)
	echo ('<input type="hidden" value="'.htmlspecialchars( $_GET['pl'] , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'" name="place">');
	//Buttons
	//	Absenden und Vorschau
	echo ( '<input type="submit" value="'.$lang['sub'].'"><button onclick="return preview( '.$pl.' ); " >'.$lang['prev'].'</button></form>'."\r\n" );

	//fertig
	die;	
}
//Antworten laden?
//	GET[id] enthält die Beitrags ID aus der dbf für welche die Antworten geladen werden sollen
//	GET[siteid] enthält die ID der Seite
elseif( isset( $_GET['answer'] ) && is_numeric( $_GET['id'] ) && is_numeric( $_GET['siteid'] ) ){
	
	//Gästebuchkonfiguration laden
	$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );
	
	//Felogin vorhanden und "nur felogin User" aktiviert?
	if( function_exists( 'check_felogin_login' ) ){
		//Loginstatus prüfen
		//	Gruppe und User aus Session, SiteID des Gästebuchs
		//	(darf User Seite sehen?)
		if( check_felogin_login( '---session---', $_GET['siteid'] ) ){
			//okay, Antworten können geladen werden
			$guestbook['add'] = true;
		}
		else{
			//nicht erlaubt
			$guestbook['add'] = false;
		}
	}
	else{
		//kein Login verfügbar oder nicht nötig
		//	Antworten können geladen werden
		$guestbook['add'] = true;
	}
	
	//wenn Antworten laden erlaubt:
	if( $guestbook['add'] ){

		//Antworten vorhanden?		
		if( check_for_kimb_file(  'addon/guestbook__id_'.$_GET['siteid'].'_answer_'.$_GET['id'].'.kimb' ) ){
	
			//Datei mit Antworten öffnen
			$readfile = new KIMBdbf( 'addon/guestbook__id_'.$_GET['siteid'].'_answer_'.$_GET['id'].'.kimb' );
			
			//alle IDs durchgehen
			foreach( $readfile->read_kimb_all_teilpl('allidslist') as $id ){
				
				//Daten der Antwort lesen
				$eintr = $readfile->read_kimb_id( $id );
				
				//wenn aktiviert
				if( $eintr['status'] == 'on' ){
		
					//Ausgabe wie normalen Beitrag
					echo '<div class="guest" >'."\r\n";
					//	Name		
					echo '<div class="guestname" >'.$eintr['name']."\r\n";
					//	Zeit
					echo '<span class="guestdate">'.date( 'd-m-Y H:i:s' , $eintr['time'] ).'</span>'."\r\n";
					echo '</div>'."\r\n";
					//	Inhalt
					echo $eintr['cont']."\r\n";
					//	Kommentar
					if( !empty( $eintr['comm'] ) ){
						echo '<div class="guestcomment">'."\r\n";
						echo '<span>'.$allgsys_trans['addons']['guestbook']['ajax']['adminkomm'].'</span>'."\r\n";
						echo $eintr['comm']."\r\n";
						echo '</div>'."\r\n";
					}
					echo '</div>'."\r\n";
					
				}
			}
		}
		else{
			echo 'Keine Antworten - No Answers';
		}
	}
	else{
		//Fehler wenn keine Rechte
		echo 'Keine Rechte - Not allowed!';
	}
	die;
}
//BE Adminkommentar
elseif ( !empty( $_POST['site'] ) && !empty( $_POST['id'] ) ) {
	
	//Login testen
	check_backend_login( 'thirteen' );
	
	//Werte testen
	if(
		//Gästebuch Seite
		is_numeric( $_POST['site'] ) &&
		//ID des zu kommentierden Eintrages
		is_numeric( $_POST['id'] ) &&
		//entweder Antwort, dann ID der zu kommentierende Antwort oder leer
		(
			is_numeric( $_POST['aid'] ) ||
			empty( $_POST['aid'] )
		) &&
		//	neu/ verändern oder löschen?
		(
			//Inhalt gegeben?
			!empty( $_POST['inh'] ) ||
			$_POST['del'] == 'true'
		)
	){
		//dbf Datei für Kommentar lesen
		if( empty( $_POST['aid'] ) ){
			//Dateiname
			//	Hauptkommentar
			$filena = 'addon/guestbook__id_'.$_POST['site'].'.kimb';
			//ID zu welcher der Kommentar soll 
			$addid = $_POST['id'];
		}
		else{
			//Dateiname
			//	Antwort
			$filena = 'addon/guestbook__id_'.$_POST['site'].'_answer_'.$_POST['id'].'.kimb';
			//ID zu welcher der Kommentar soll 
			$addid = $_POST['aid'];
		}
		
		// passende dbf lesen
		$file = new KIMBdbf( $filena );
		
		//neu/ ändern?
		if( !empty( !empty( $_POST['inh'] ) ) ){
			// Inhalt vorbereiten
			$inhalt = make_guestbook_html( $_POST['inh'], '' );
			$inhalt = $inhalt[0];
			
			//schreiben
			if( $file->write_kimb_id( $addid, 'add', 'comm', $inhalt ) ){
				// true AJAX Rückgabe
				echo "ok";
			}
			else{
				// false AJAX Rückgabe
				echo "nok";
			}
		}
		//del?
		else{
			//schreiben
			if( $file->write_kimb_id( $addid, 'del', 'comm' ) ){
				// true AJAX Rückgabe
				echo "ok";
			}
			else{
				// false AJAX Rückgabe
				echo "nok";
			}
		}
	}
	else{
		//Fehler
		echo "Der Zugriff war nicht korrekt! (Adminkommentar konnte nicht veröffentlicht werden.)";
	}
		
	die;
}
//neuen Beitrag Status ändern?
elseif(
	isset( $_GET['review'] )

){
	//alle Parameter prüfen und zu vars
	$site = preg_replace( '/[^0-9]/', '', $_GET['site'] );
	$file = (isset($_GET['file']) ? preg_replace( '/[^0-9]/', '', $_GET['file'] ) : false );
	$id = preg_replace( '/[^0-9]/', '',$_GET['id'] );
	$auth = preg_replace( '/[^0-9A-Za-z]/', '', $_GET['auth'] );

	//Dateiname
	if( $file !== false ){
		//Datei mit Antworten zu einem Eintrag in Hauptdatei der Seite
		$datei = 'addon/guestbook__id_'.$site.'_answer_'.$file.'.kimb';
	}
	else{
		//Hauptdatei der Seite
		$datei = 'addon/guestbook__id_'.$site.'.kimb';
	} 

	//Datei vorhnaden?
	if( check_for_kimb_file( $datei ) ){
		//wenn ja, öffnen
		$file = new KIMBdbf( $datei );

		//Authcode für ID lesen
		$code = $file->read_kimb_id( $id, 'statauth' );

		//Zugriff okay?
		if( !empty( $code ) && $code == $auth ){
			//Status lesen
			$status = $file->read_kimb_id( $id, 'status' );
			//neuen Status bestimmen
			$newstat = ( $status == 'on' ? 'off' : 'on' );
			//neuen Status schreiben
			if( $file->write_kimb_id( $id, 'add', 'status', $newstat ) ){
				//Code löschen
				$file->write_kimb_id( $id, 'del', 'statauth' );

				//Seiten ID zu RequestID umrechnen
				//	ID Zuordnungen Datei laden
				$idfile = new KIMBdbf('menue/allids.kimb');
				//	rechnen
				$requid = $idfile->search_kimb_xxxid( $site , 'siteid' );

				if( $requid != false ){
					//Weiterleiten
					open_url( make_path_outof_reqid( $requid ) );
				}
				else{
					echo "200 - Status verändert, konnte aber nicht weiterleiten";
				}
			}
			else{
				echo "500 - Konnte Status nicht verändern!";
			}
		}
		else{
			echo "403 - Kein Zugriff!<br />\r\n";
			echo "<small><em>(jeder Link funktioniert immer nur einmal)</em></small>";
		}
	}
	else{
		echo "404 - Eintrag nicht gefunden!";
	}

	//beenden
	die;

 }

//Fehler wenn man hier landet, falsch auf AJAX zugegriffen
echo 'Falscher Zugriff auf Guestbook AJAX';

?>
