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

//URL
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=kontakt';

//Konfigurationsdatei
$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );

//wir die Seiten ID übergeben?
if( is_numeric( $_POST['id'] ) && !empty( $_POST['id'] ) ){

	//aktuelle Seite lesen
	$siteid = $kontakt['file']->read_kimb_one( 'siteid' );
	//Seiten ID verändert?
	if( $_POST['id'] != $siteid ){
		//Seiten ID überschreiben
		$kontakt['file']->write_kimb_one( 'siteid' , $_POST['id'] );
		
		//die neue Seiten ID der Add-on API mitteilen
		$a = new ADDonAPI( 'kontakt' );
		$a->set_fe( 'vorn', 's'.$_POST['id'] , 'no' );

		//Meldung
		$sitecontent->echo_message( 'Die SiteID wurde geändert!' );
	}

}

//zu prüfenden on/off Einstellungen
$dos = array( 'mailoo' => 'mail', 'formoo' => 'form', 'otheroo' => 'other' );

//alle durchgehen
//	$key -> Name in Übergabe
//	$val -> Name in dbf
foreach( $dos as $key => $val ){

	//Ist der Wert on oder off (anderes nicht möglich)
	if(  $_POST[$key] == 'on' ||  $_POST[$key] == 'off' ){
		//ist der Wert geändert?
		if( $_POST[$key] != $kontakt['file']->read_kimb_one( $val ) ){
			
			//Änderung speichern
			$kontakt['file']->write_kimb_one( $val , $_POST[$key] );
	
			//Medlung vorbereiten
			$changed .= 'Der Status von "'.$val.'" wurde verändert!<br />';
		}
	}
}
//Sind Meldungen vorbereitet?
if( !empty( $changed ) ){
	//wenn ja, ausgeben
	$sitecontent->echo_message( $changed );
}

//Ist ein über JavaScript zu sichernder Text übergeben
if( !empty( $_POST['othercont'] ) ){

	//unterscheidet sich der Text in der Übergabe von der dbf?
	if( $_POST['othercont'] != $kontakt['file']->read_kimb_one( 'othercont' ) ){
		//wenn ja, anpassen
		$kontakt['file']->write_kimb_one( 'othercont' , $_POST['othercont'] );
		//Medlung
		$sitecontent->echo_message( 'Der über JavaScript gesicherter Inhalt wurde geändert!' );
	}
}

//Wird eine E-Mail-Adresse übergeben?
if( !empty( $_POST['mail'] ) ){
	//Ist die Übergabe anders als in der dbf?
	//Wird expliziet eine Änderung vorgeschrieben?
	if( $_POST['mail'] != $kontakt['file']->read_kimb_one( 'formaddr' ) || isset( $_POST['newimg'] ) ){
	
		//Adresse in dbf schreiben	
		$kontakt['file']->write_kimb_one( 'formaddr' , $_POST['mail'] );

		//den Namen des alten E-Mail-Bildes lesen
		$oldname = $kontakt['file']->read_kimb_one( 'bildname' );
		//gibt es das alte Bild noch?
		if( is_file(__DIR__.'/../../../load/addondata/kontakt/'.$oldname.'.png') ){
			//wenn ja, löschen
			unlink( __DIR__.'/../../../load/addondata/kontakt/'.$oldname.'.png' );
		}
		
		//Namen für das neue Bild machen
		$name = makepassw( 20, '', 'numaz');
		//Namen in dbf sichern
		$kontakt['file']->write_kimb_one( 'bildname' , $name );
		
		//E-Mail-Adresse für Bild vorbereiten
		$string = $_POST['mail'];
		
		//nötige Größe des Bildes berechen
		$box = imagettfbbox ( 20 , 5 , __DIR__.'/Ubuntu-B.ttf' , $string );
		//	Breite
		$w = abs($box[4] - $box[0]);
		//	Höhe
		$h = abs($box[5] - $box[1]);
		
		//Bild passend erstellen (überall 5px Rand)
		$im = imagecreate ($w +10 , $h + 10 );
		//Hintergrund weiß
		imagecolorallocate( $im , 255 , 255 , 255);
		//Textfarbe (schwarz)
		$color = imagecolorallocate( $im, 0, 0, 0);
		//Text dem Bild hinzufügen
		//	Textgröße, Winkel, Abstände
		imagettftext ($im, 20, -5, 5, 25, $color, __DIR__.'/Ubuntu-B.ttf', $string );
		//Bild speichern 
		imagepng( $im , __DIR__.'/../../../load/addondata/kontakt/'.$name.'.png' );
		//Bild löschen
		imagedestroy( $im );

		//Medlung
		$sitecontent->echo_message( 'Die E-Mail-Adresse wurde geändert!' );
	}
}

//alle zu lesenden on/off Werte durchgehen
$i = 0;
foreach( $dos as $val ){
	
	//bei off Array ( checked, '' ) setzen
	if( $kontakt['file']->read_kimb_one( $val ) == 'off' ){
		$ch[$i] = ' checked="checked" ';
		$ch[$i+1] = ' ';
	}
	//bei on Array ( '', checked ) setzen
	elseif( $kontakt['file']->read_kimb_one( $val ) == 'on' ){
		$ch[$i+1] = ' checked="checked" ';
		$ch[$i] = ' ';
	}
	//wenn nichts, keine Vorauswahl
	
	//zwei Indexe im Array weiter
	$i = $i + 2;
}

//JavaScript Datei, welche Funktion liefert um sicheren Text zu codieren
$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/kontakt/coder.min.js"></script>');

//Formular
$sitecontent->add_site_content('<br /><br /><form action="'.$addonurl.'" method="post" onsubmit="submitsecure();">');

//SiteID lesen
$siteid = $kontakt['file']->read_kimb_one( 'siteid' );
if( empty( $siteid )){
	//leer -> keine
	$siteid = 'none';
}
//im Dropdown mit SiteIDs Akteulle wählen
$sitecontent->add_html_header('<script>$(function(){ $( "select[name=id]" ).val( \''.$siteid.'\' ); }); </script>');
//Dropdown und Infotext
$sitecontent->add_site_content(id_dropdown( 'id', 'siteid' ).' (SiteID <b title="Bitte geben Sie hier die Seite an, auf welcher die Kontaktinfos erscheinen sollen.">*</b>)<br />');

//Mailbild on/off
$sitecontent->add_site_content('<input type="radio" name="mailoo" value="off"'.$ch[0].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="mailoo" value="on"'.$ch[1].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse aktiviert" class="ui-icon ui-icon-check"></span> (E-Mail-Adresse)<br />');

//Formular on/off
$sitecontent->add_site_content('<input type="radio" name="formoo" value="off"'.$ch[2].'> <span style="display:inline-block;" title="Kontakformular deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="formoo" value="on"'.$ch[3].'> <span style="display:inline-block;" title="Kontaktformular aktiviert" class="ui-icon ui-icon-check"></span> (Kontaktformular)<br />');

//sicherer Text on/off
$sitecontent->add_site_content('<input type="radio" name="otheroo" value="off"'.$ch[4].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="otheroo" value="on"'.$ch[5].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt aktiviert" class="ui-icon ui-icon-check"></span> (JavaScript Inhalt)<br /><br />');

//E-Mail-Adresse lesen
$mailadr = $kontakt['file']->read_kimb_one( 'formaddr' );
//wenn leer, dann Systemadminmail
if( empty( $mailadr )){
	$mailadr = $allgsysconf['adminmail'];
}
//nach E-Mail-Adresse fragen (für Bild und als Ziel für Kontakformular) 
$sitecontent->add_site_content('<input name="mail" type="text" value="'.$mailadr.'" > (E-Mail-Adresse <b title="Die Adresse wird, wenn aktiviert, als Bild auf der Seite angezeigt und für das Kontaktformular genutzt!">*</b>)<br />');

//wenn Bild vorhanden
if( !empty( $kontakt['file']->read_kimb_one( 'bildname' ) ) ){
	//Link zum Bild erstellen
	$link = $allgsysconf['siteurl'].'/load/addondata/kontakt/'.$kontakt['file']->read_kimb_one( 'bildname' ).'.png';
	//Icon um Vorschau des Bildes zu sehen
	$sitecontent->add_site_content('<a href="'.$link.'" target="popup" onclick="window.open(\'\', \'popup\', \'width=900px,height=500px,top=20px,left=20px\'); "><span style="display:inline-block;" title="Vorschau" class="ui-icon ui-icon-image"></span></a>');
	//Auwahl, expliziet neues Bild erstellen 
	$sitecontent->add_site_content('<input type="checkbox" name="newimg" value="yes">(Neues Bild <b title="Ein neues Bild erstellen, auch wenn E-Mail-Adresse nicht geändert wurde">*</b>)<br /><br >');
}

//sicheren Text laden
$othercont = $kontakt['file']->read_kimb_one( 'othercont' );
//br's weg (werden per JS eingefügt)
$othercont = str_replace( '<br />', '', $othercont);
//Eingabefeld
$sitecontent->add_site_content('<textarea name="othercont" id="othercont" style="width:99%; min-height:150px;">'.$othercont.'</textarea>');
//Hinweistext
$sitecontent->add_site_content('(Über JavaScript gesicherter Inhalt &uarr; <b title="Der Text wird so nachgeladen, dass es für Bots schwer ist ihn zu lesen, so lassen sich z.B. Telefonnummern und Adressen schützen!">*</b>)<br />');
$sitecontent->add_site_content('<i>Bitte schreiben Sie nur reinen Text mit Absätzen!</i>)<br />');

//Formular absenden
$sitecontent->add_site_content('<input type="submit" value="Ändern"> </form>');

?>