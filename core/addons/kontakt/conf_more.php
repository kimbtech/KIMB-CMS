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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=kontakt';

$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );

if( is_numeric( $_POST['id'] ) && !empty( $_POST['id'] ) ){

	$siteid = $kontakt['file']->read_kimb_one( 'siteid' );
	if( $_POST['id'] != $siteid ){
		$kontakt['file']->write_kimb_one( 'siteid' , $_POST['id'] );
		
		$a = new ADDonAPI( 'kontakt' );
		$a->set_fe( 'vorn', 's'.$_POST['id'] , 'no' );

		$sitecontent->echo_message( 'Die SiteID wurde geändert!' );
	}

}

$dos = array( 'mailoo' => 'mail', 'formoo' => 'form', 'otheroo' => 'other' );

foreach( $dos as $key => $val ){

	if(  $_POST[$key] == 'on' ||  $_POST[$key] == 'off' ){
		if( $_POST[$key] != $kontakt['file']->read_kimb_one( $val ) ){
			
			$kontakt['file']->write_kimb_one( $val , $_POST[$key] );
	
			$changed .= 'Der Status von "'.$val.'" wurde verändert!<br />';
		}
	}
}
if( !empty( $changed ) ){
	$sitecontent->echo_message( $changed );
}

if( !empty( $_POST['othercont'] ) ){

	if( $_POST['othercont'] != $kontakt['file']->read_kimb_one( 'othercont' ) ){
		
		$kontakt['file']->write_kimb_one( 'othercont' , $_POST['othercont'] );

		$sitecontent->echo_message( 'Der über JavaScript gesicherter Inhalt wurde geändert!' );
	}

}


if( !empty( $_POST['mail'] ) ){

	if( $_POST['mail'] != $kontakt['file']->read_kimb_one( 'formaddr' ) || isset( $_POST['newimg'] ) ){
		
		$kontakt['file']->write_kimb_one( 'formaddr' , $_POST['mail'] );

		$oldname = $kontakt['file']->read_kimb_one( 'bildname' );
		if( is_file(__DIR__.'/../../../load/addondata/kontakt/'.$oldname.'.png') ){
			unlink( __DIR__.'/../../../load/addondata/kontakt/'.$oldname.'.png' );
		}
		
		$name = makepassw( 20, '', 'numaz');
		$kontakt['file']->write_kimb_one( 'bildname' , $name );
		
		$string = $_POST['mail'];
		
		$box = imagettfbbox ( 20 , 5 , __DIR__.'/Ubuntu-B.ttf' , $string );
		$w = abs($box[4] - $box[0]);
		$h = abs($box[5] - $box[1]);
		
		$im = imagecreate ($w +10 , $h + 10 );
		imagecolorallocate( $im , 0 , 0 , 0 );
		$color = imagecolorallocate( $im , 255 , 255 , 255 );
		imagettftext ($im, 20, -5, 5, 25, $color, __DIR__.'/Ubuntu-B.ttf', $string );
		imagepng( $im , __DIR__.'/../../../load/addondata/kontakt/'.$name.'.png' );
		imagedestroy( $im );

		$sitecontent->echo_message( 'Die E-Mail-Adresse wurde geändert!' );
	}
}

$i = 0;
foreach( $dos as $val ){
	
	if( $kontakt['file']->read_kimb_one( $val ) == 'off' ){
		$ch[$i] = ' checked="checked" ';
		$ch[$i+1] = ' ';
	}
	elseif( $kontakt['file']->read_kimb_one( $val ) == 'on' ){
		$ch[$i+1] = ' checked="checked" ';
		$ch[$i] = ' ';
	}
	
	$i = $i + 2;
}

//$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/kontakt/coder.min.js"></script>');
$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/kontakt/coder.dev.js"></script>');

$sitecontent->add_site_content('<br /><br /><form action="'.$addonurl.'" method="post" onsubmit="submitsecure();">');

$siteid = $kontakt['file']->read_kimb_one( 'siteid' );
if( empty( $siteid )){
	$siteid = 'none';
}
$sitecontent->add_html_header('<script>$(function(){ $( "select[name=id]" ).val( \''.$siteid.'\' ); }); </script>');

$sitecontent->add_site_content(id_dropdown( 'id', 'siteid' ).' (SiteID <b title="Bitte geben Sie hier die Seite an, auf welcher die Kontaktinfos erscheinen sollen.">*</b>)<br />');

$sitecontent->add_site_content('<input type="radio" name="mailoo" value="off"'.$ch[0].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="mailoo" value="on"'.$ch[1].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse aktiviert" class="ui-icon ui-icon-check"></span> (E-Mail-Adresse)<br />');

$sitecontent->add_site_content('<input type="radio" name="formoo" value="off"'.$ch[2].'> <span style="display:inline-block;" title="Kontakformular deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="formoo" value="on"'.$ch[3].'> <span style="display:inline-block;" title="Kontaktformular aktiviert" class="ui-icon ui-icon-check"></span> (Kontaktformular)<br />');

$sitecontent->add_site_content('<input type="radio" name="otheroo" value="off"'.$ch[4].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="otheroo" value="on"'.$ch[5].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt aktiviert" class="ui-icon ui-icon-check"></span> (JavaScript Inhalt)<br /><br />');

//E-Mail-Adresse lesen
$mailadr = $kontakt['file']->read_kimb_one( 'formaddr' );
//wenn leer, dann Systemadminmail
if( empty( $mailadr )){
	$mailadr = $allgsysconf['adminmail'];
}
$sitecontent->add_site_content('<input name="mail" type="text" value="'.$mailadr.'" > (E-Mail-Adresse <b title="Die Adresse wird, wenn aktiviert, als Bild auf der Seite angezeigt und für das Kontaktformular genutzt!">*</b>)<br />');

if( !empty( $kontakt['file']->read_kimb_one( 'bildname' ) ) ){
	$link = $allgsysconf['siteurl'].'/load/addondata/kontakt/'.$kontakt['file']->read_kimb_one( 'bildname' ).'.png';
	$sitecontent->add_site_content('<a href="'.$link.'" target="popup" onclick="window.open(\'\', \'popup\', \'width=900px,height=500px,top=20px,left=20px\'); "><span style="display:inline-block;" title="Vorschau" class="ui-icon ui-icon-image"></span></a>');
	$sitecontent->add_site_content('<input type="checkbox" name="newimg" value="yes">(Neues Bild <b title="Ein neues Bild erstellen, auch wenn E-Mail-Adresse nicht geändert wurde">*</b>)<br /><br >');
}

$othercont = $kontakt['file']->read_kimb_one( 'othercont' );
$othercont = str_replace( '<br />', '', $othercont);
$sitecontent->add_site_content('<textarea name="othercont" id="othercont" style="width:99%; min-height:150px;">'.$othercont.'</textarea>');
$sitecontent->add_site_content('(Über JavaScript gesicherter Inhalt &uarr; <b title="Der Text wird so nachgeladen, dass es für Bots schwer ist ihn zu lesen, so lassen sich z.B. Telefonnummern und Adressen schützen!">*</b>)<br />');
$sitecontent->add_site_content('<i>Bitte schreiben Sie nur reinen Text mit Absätzen!</i>)<br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern"> </form>');


?>
