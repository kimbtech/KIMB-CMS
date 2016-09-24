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

//URL zu Add-on Konfiguration
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=guestbook';

//CSS Style Vorschlag
$cssallg = get_guestbook_css();

//Konfigurations dbf laden
$guestfile = new KIMBdbf( 'addon/guestbook__conf.kimb' );

//Soll ein neues Gästebuch einer Seite hinzugefügt werden?
if( isset( $_GET['new'] ) && is_numeric( $_POST['id'] ) ){

	//Ist die ID schon in der Liste der Seiten mit Gästebuch
	//	existiert die Datei für die Seite schon?
	if( !$guestfile->read_kimb_search_teilpl( 'siteid' , $_POST['id'] ) && !check_for_kimb_file( 'addon/guestbook__id_'.$_POST['id'].'.kimb' ) ){

		//ID der Liste hinzufügen
		if( $guestfile->write_kimb_teilpl( 'siteid' , $_POST['id'] , 'add' ) ){
			//Medlung
			$sitecontent->echo_message( 'Ein Gästebuch wurde zur Seite "'.$_POST['id'].'" hinzugefügt!' );
			
			//Seiten geändert!
			$siteschanged = true;
		}

	}
	else{
		//Fehler wenn Gästebuch schon da
		$sitecontent->echo_error( 'Diese Seite hat bereits ein Gästebuch!' , 'unknown' );
	}
}
//soll ein Gästebuch einer Seite entfernt werden
elseif( isset( $_GET['del'] ) && is_numeric( $_GET['id'] ) ){

	//Hat die Seite überhaupt ein Gästebuch?
	if( $guestfile->read_kimb_search_teilpl( 'siteid' , $_GET['id'] ) ){
		//ja
		
		//entfernen der ID aus der Gästebuchdatei
		if( $guestfile->write_kimb_teilpl( 'siteid' , $_GET['id'] , 'del' ) ){
			//Meldung
			$sitecontent->echo_message( 'Das Gästebuch der Seite "'.$_GET['id'].'" wurde entfernt!' );
			
			//Seiten geändert!
			$siteschanged = true;
		}
		//Beiträge und Antworten löschen!
		
		//Datei überhaupt erstellt?
		if( check_for_kimb_file( 'addon/guestbook__id_'.$_GET['id'].'.kimb' ) ){
			//Antworten
			//	Datei lesen
			$file = new KIMBdbf( 'addon/guestbook__id_'.$_GET['id'].'.kimb' );
			//	alle IDs
			$arr = $file->read_kimb_id_all();
			//	durchgehen
			foreach( $arr as $id => $val ){
				//Antwort für ID vorhanden?
				if( $val['antwo'] == 'yes' ){
					//Datei vorhanden?
					if( check_for_kimb_file( 'addon/guestbook__id_'.$_GET['id'].'_answer_'.$id.'.kimb' ) ){
						//Datei löschen
						delete_kimb_datei( 'addon/guestbook__id_'.$_GET['id'].'_answer_'.$id.'.kimb' );
					}
				}
			}
			//zum Ende Hauptdatei löschen
			$file->delete_kimb_file();
		}

	}
	else{
		//Fehlermeldung wenn Seite kein Gästebuch hat
		$sitecontent->echo_error( 'Diese Seite hat kein Gästebuch!' , 'unknown' );
	}
}
//Änderung der Einstellungen
//	Sind alle Felder gesetzt?
elseif( isset( $_GET['settings'] , $_POST['feloginoo'] , $_POST['mailoo'] , $_POST['nstatoo'] , $_POST['ipoo'] , $_POST['mail'] , $_POST['css'] ) ){

	//Habe alle on/off Werte einen Wert?
	//Ist die E-Mail-Adresse gesetzt?
	if( !empty( $_POST['feloginoo'] ) && !empty( $_POST['mailoo'] ) && !empty( $_POST['nstatoo'] ) && !empty( $_POST['ipoo'] ) && !empty( $_POST['mail'] ) ){

		//Array mit zu setztenden Werten
		//	teil -> Name im Formular
		//	trenner -> Name in dbf
		$arrays[] = array( 'teil' => 'feloginoo' , 'trenner' => 'nurfeloginuser' );
		$arrays[] = array( 'teil' => 'mailoo' , 'trenner' => 'mailinfo' );
		$arrays[] = array( 'teil' => 'nstatoo' , 'trenner' => 'newstatus' );
		$arrays[] = array( 'teil' => 'ipoo' , 'trenner' => 'ipsave' );
		
		$message = '';

		//zu Tuenden durchgehen
		foreach( $arrays as $array ){
			//Teil und Trenner aus Array bestimmen
			$teil = $array['teil'];
			$trenner = $array['trenner'];

			//Ist der Wert on oder off?
			//	muss so sein!
			if( $_POST[$teil] == 'on' || $_POST[$teil] == 'off' ){
				//aktuellen Wert lesen
				$wert = $guestfile->read_kimb_one( $trenner );
				//ist der übergebene Wert anders?
				//	nur dann Änderung nötig
				if( $wert != $_POST[$teil] ){
					//ist der Wert überhaupt schon gesetzt?
					if( empty( $wert ) ){
						//neu schreiben
						$guestfile->write_kimb_new( $trenner , $_POST[$teil] );
					}
					else{
						//überschreiben
						$guestfile->write_kimb_replace( $trenner , $_POST[$teil] );
					}
					//Meldung
					$message .= '"'.$trenner.'" wurde auf "'.$_POST[$teil].'" gesetzt!<br />'."\r\n";
				}
			}
		}
		
		if( $message != '' ){
			$sitecontent->echo_message( $message );
		}

		//aktuelle Mail lesen
		$mail = $guestfile->read_kimb_one( 'mailinfoto' );
		//ist die aktuelle Mail anders als die Übergabe? 
		if( $mail != $_POST['mail'] ){
			//ist der Wert überhaupt schon gesetzt?
			if( empty( $mail ) ){
				//neu schreiben
				$guestfile->write_kimb_new( 'mailinfoto' , $_POST['mail'] );
			}
			else{
				//überschreiben
				$guestfile->write_kimb_replace( 'mailinfoto' , $_POST['mail'] );
			}
			//Meldung
			$sitecontent->echo_message( 'Die E-Mail-Adresse wurde auf "'.$_POST['mail'].'" gesetzt!' );
		}

		//aktuellen CSS Wert lesen
		$css = $guestfile->read_kimb_one( 'css' );
		//ist das aktuelle CSS anders als Übergabe?
		if( $css != $_POST['css'] ){
			//ist die Übergabe leer?
			if( empty( $_POST['css'] ) ){
				//wenn ja, Vorschlag nutzen
				$_POST['css'] = $cssallg;
			}
			//ist der Wert überhaupt schon gesetzt?
			if( empty( $css ) ){
				//neu schreiben
				$guestfile->write_kimb_new( 'css' , $_POST['css'] );
			}
			else{
				//überschreiben
				$guestfile->write_kimb_replace( 'css' , $_POST['css'] );
			}
			//Meldung
			$sitecontent->echo_message( 'Das Design wurde geändert!' );
		}

	}
	else{
		//Fehlermeldung wenn Felder leer, außer CSS
		//	kann eingentlich nicht vorkommen, denn wenn dbf leer werden automatisch Vorschläge in das Formular geschrieben
		$sitecontent->echo_error( 'Fehlerhafte Anfrage! Bitte füllen Sie alle Felder (CSS kann leer bleiben)!' , 'Eingaben' );
	}
}

//Seiten verändert (FullHTMLCache Wünsche anpassen)
if( isset( $siteschanged ) && $siteschanged ){
	
	//Wish API Klasse laden
	$api = new ADDonAPI( 'guestbook' );
	
	//für API vorbereiten
	$data = array();
	
	//ID Zuordnungen
	$idfile = new KIMBdbf('menue/allids.kimb');
	
	//alle aktuellen Seiten lesen
	foreach( $guestfile->read_kimb_all_teilpl( 'siteid' ) as $id ){
		
		//SiteID zu requID
		$requid = $idfile->search_kimb_xxxid( $id , 'siteid' );
		
		//Suche okay?
		if( $requid != false ){
			
			//explizit zu INT
			$requid = intval( $requid );
		
			//für diese Seite erlauben, aber auf bestimmte Werte achten
			$data[$requid] = array(
				'on', array( 'POST' => array(
					//alle Werte die für neue Einträge gesendet werden
					'cont',
					'mail',
					'name',
					'place',
					'captcha_code'
				), 'GET' => array(), 'COOKIE' => array() )
			);
			
		}
		
	}
	
	//Array an API übergeben	
	$api->full_html_cache_wish( 'set', $data ); 	
}

//Liste der Seiten mit Gästebuch
$sitecontent->add_site_content('<h2>Seiten mit Gästebuch</h2>');

//CSS für Tabelle
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//JavaScript für Löschen Dialog
$sitecontent->add_html_header('<script>
function delete_gues( id, name ) {
	
	$( "div#del-guestbooksite p.inf" ).html( "<b>Gästebuch der Seite:</b><br />"+name );
	
	$( "#del-guestbooksite" ).show( "fast" );
	$( "#del-guestbooksite" ).dialog({
	resizable: false,
	height:240,
	modal: true,
	buttons: {
		"Löschen": function() {
			$( this ).dialog( "close" );
			window.location = "'.$addonurl.'&id=" + id + "&del";
			return true;
		},
		"Abbrechen": function() {
			$( this ).dialog( "close" );
			return false;
		}
	}
	});
}
</script>');

//Info
$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Hier können Sie allgemeine Einstellungen vornhemen und ein Gästebuch/ eine Kommentarmöglichkeit auf bestimmten Seite anzeigen. In der Liste werden die SiteIDs (Seiten -> Auflisten) angezeigt. Die Beiträge der Gästebücher können Sie unter Nutzung (Link oben rechts) verwalten."></span>');
//Tabelle Beginn
$sitecontent->add_site_content('<table width="100%"><tr><th>Seitenname (SiteID)</th><th width="20px;">Löschen</th></tr>');

//Array Seiten Name, ID
//	passend erstellen
foreach ( list_sites_array() as $v ){
	$sitesnames[$v['id']] = $v['site'];
}

//alle SiteIDs mit Gästebuch lesen
foreach( $guestfile->read_kimb_all_teilpl( 'siteid' ) as $id ){

	//Mülleimer Button (löschen)
	$del = '<span onclick="delete_gues('.$id.', \''.$sitesnames[$id].' ('.$id.')\');"><span class="ui-icon ui-icon-trash" title="Dieses Gästebuch löschen. (inklusive aller Beiträge)"></span></span>';

	//Tabellenzeile hinzufügen
	$sitecontent->add_site_content('<tr><td>'.$sitesnames[$id].' ('.$id.')</td><td>'.$del.'</td></tr>');

	//jetzt was in Tabelle
	$gefunden = 'yes';
}

//Tabelle beenden
$sitecontent->add_site_content('</table>');

//wenn Tabelle leer:
if( $gefunden != 'yes' ){
	//Medlung (keine Gästebücher)
	$sitecontent->echo_error( 'Es wurden keine Gästebuchseiten gefunden!' , 'unknown' );
}

//Formular um neue Gästebuchseite hinzuzufügen
//	Dropdown mit SiteIDs
$sitecontent->add_site_content('<form action="'.$addonurl.'&amp;new" method="post"><span class="ui-icon ui-icon-plus" title="Bei einer weiteren Seite erstellen." style="display:inline-block;"></span>'.id_dropdown( 'id', 'siteid' ).'<input type="submit" value="Erstellen" ></form>');

//HTML-Code für Löschen Dialog
$sitecontent->add_site_content('<div style="display:none;"><div id="del-guestbooksite" title="Gästebuch löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie das Gästebuch und alle seine Beiträge wirklich löschen?</p><p class="inf"></p></div></div>');

//Allgemeine Einstellungen
$sitecontent->add_site_content('<hr /><h2>Allgemeine Einstellungen</h2>');

//erstmal nichts aktivert
$ch = array( ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' );

//Für die on/off Auswahl die aktuelle Auswahl laden
//	jeden Wert in dbf durchgehen und  checked=checked passend setzen

//Array key
$i = 0;
foreach( array('nurfeloginuser', 'mailinfo', 'newstatus', 'ipsave' ) as $dbf ){
	//wenn off, dann ersten Wert checked
	if( $guestfile->read_kimb_one( $dbf ) == 'off' ){
		$ch[$i] = ' checked="checked" ';
	}
	//wenn on, dann zweiten Wert checked
	else{
		$ch[$i + 1] = ' checked="checked" ';
	}
	//Key um zwei erhähen
	$i = $i + 2;
}

//CSS Style lesen
$css = $guestfile->read_kimb_one( 'css' );
//wenn leer, Vorschlag
if( empty( $css ) ){
	$css = $cssallg;
}

//E-Mail-Adresse lesen
$mailinfo = $guestfile->read_kimb_one( 'mailinfoto' );
//wenn leer, dann Systemadminmail
if( empty( $mailinfo )){
	$mailinfo = $allgsysconf['adminmail'];
}

//Formular beginnen
$sitecontent->add_site_content('<form action="'.$addonurl.'&amp;settings" method="post" >');

//on/off felogin
$sitecontent->add_site_content('<input type="radio" name="feloginoo" value="off"'.$ch[0].'><span style="display:inline-block;" title="Allen Usern das Kommentieren erlauben" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="feloginoo" value="on"'.$ch[1].'> <span style="display:inline-block;" title="Nur eingeloggten Usern das Kommentieren erlauben (Add-on &apos;felogin&apos; nötig)" class="ui-icon ui-icon-check"></span>(Login)<br />');

//on/off Infomail
$sitecontent->add_site_content('<input type="radio" name="mailoo" value="off"'.$ch[2].'><span style="display:inline-block;" title="Keine E-Mail bei neuen Beiträgen senden" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="mailoo" value="on"'.$ch[3].'> <span style="display:inline-block;" title="Eine E-Mail an die Adresse unten senden, wenn ein neuer Beitrag vorhanden ist" class="ui-icon ui-icon-check"></span> (E-Mail)<br />');

//on/off Prüfen
$sitecontent->add_site_content('<input type="radio" name="nstatoo" value="off"'.$ch[4].'><span style="display:inline-block;" title="Neue Beiträge vor Veröffentlichung prüfen" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="nstatoo" value="on"'.$ch[5].'> <span style="display:inline-block;" title="Neue Beiträge gleich veröffentlichen" class="ui-icon ui-icon-check"></span> (Status)<br />');

//on/off IPsave
$sitecontent->add_site_content('<input type="radio" name="ipoo" value="off"'.$ch[6].'><span style="display:inline-block;" title="IP des Users nicht speichern" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="ipoo" value="on"'.$ch[7].'> <span style="display:inline-block;" title="IP des Users speichern (Hinweis wird angezeigt)" class="ui-icon ui-icon-check"></span> (IP)<br />');

//E-Mail-Adresse
$sitecontent->add_site_content('<input type="text" name="mail" value="'.$mailinfo.'" > (E-Mail-Adresse)<br />');
//CSS
$sitecontent->add_site_content('<textarea name="css" style="width:99%; height:75px;">'.$css.'</textarea>(&uarr; CSS-Style  <small><a href="#" onclick="$( \'textarea[name=css]\').val(\'\'); $(\'form\').submit();">Standard wiederherstellen</a></small>)<br />');

//Button
$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');

?>
