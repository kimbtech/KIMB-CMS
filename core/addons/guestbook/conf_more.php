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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=guestbook';

$cssallg = 'div#guestname{ position:relative; border-bottom:solid 1px #000000; font-weight:bold; } span#guestdate{ font-weight:normal; position:absolute; right:0px; } div#guest, div.answer{ border:solid 1px #000000; border-radius:15px; background-color:#dddddd; padding:10px; margin:5px;} div.answer{ margin-left:80px; }';

$guestfile = new KIMBdbf( 'addon/guestbook__conf.kimb' );

if( isset( $_GET['new'] ) && is_numeric( $_POST['id'] ) ){

	if( !$guestfile->read_kimb_search_teilpl( 'siteid' , $_POST['id'] ) && !check_for_kimb_file( 'addon/guestbook__id_'.$_POST['id'].'.kimb' ) ){

		if( $guestfile->write_kimb_teilpl( 'siteid' , $_POST['id'] , 'add' ) ){
			$sitecontent->echo_message( 'Ein Gästebuch wurde zur Seite "'.$_POST['id'].'" hinzugefügt!' );
		}

	}
	else{
		$sitecontent->echo_error( 'Diese Seite hat bereits ein Gästebuch!' , 'unknown' );
	}
}
elseif( isset( $_GET['del'] ) && is_numeric( $_GET['id'] ) ){

	if( $guestfile->read_kimb_search_teilpl( 'siteid' , $_GET['id'] ) ){

		if( $guestfile->write_kimb_teilpl( 'siteid' , $_GET['id'] , 'del' ) ){
			$sitecontent->echo_message( 'Das Gästebuch der Seite "'.$_GET['id'].'" wurde entfernt!' );
		}
		if( check_for_kimb_file( 'addon/guestbook__id_'.$_GET['id'].'.kimb' ) ){
			delete_kimb_datei( 'addon/guestbook__id_'.$_GET['id'].'.kimb' );
		}

	}
	else{
		$sitecontent->echo_error( 'Diese Seite hat kein Gästebuch!' , 'unknown' );
	}
}
elseif( isset( $_GET['settings'] , $_POST['feloginoo'] , $_POST['mailoo'] , $_POST['nstatoo'] , $_POST['ipoo'] , $_POST['mail'] , $_POST['css'] ) ){

	if( !empty( $_POST['feloginoo'] ) && !empty( $_POST['mailoo'] ) && !empty( $_POST['nstatoo'] ) && !empty( $_POST['ipoo'] ) && !empty( $_POST['mail'] ) ){

		$arrays[] = array( 'teil' => 'feloginoo' , 'trenner' => 'nurfeloginuser' );
		$arrays[] = array( 'teil' => 'mailoo' , 'trenner' => 'mailinfo' );
		$arrays[] = array( 'teil' => 'nstatoo' , 'trenner' => 'newstatus' );
		$arrays[] = array( 'teil' => 'ipoo' , 'trenner' => 'ipsave' );

		foreach( $arrays as $array ){
			$teil = $array['teil'];
			$trenner = $array['trenner'];

			if( $_POST[$teil] == 'on' || $_POST[$teil] == 'off' ){
				$wert = $guestfile->read_kimb_one( $trenner );
				if( $wert != $_POST[$teil] ){
					if( empty( $wert ) ){
						$guestfile->write_kimb_new( $trenner , $_POST[$teil] );
					}
					else{
						$guestfile->write_kimb_replace( $trenner , $_POST[$teil] );
					}
					$sitecontent->echo_message( '"'.$trenner.'" wurde auf "'.$_POST[$teil].'" gesetzt!' );
				}
			}
		}

		$mail = $guestfile->read_kimb_one( 'mailinfoto' );
		if( $mail != $_POST['mail'] ){
			if( empty( $mail ) ){
				$guestfile->write_kimb_new( 'mailinfoto' , $_POST['mail'] );
			}
			else{
				$guestfile->write_kimb_replace( 'mailinfoto' , $_POST['mail'] );
			}
			$sitecontent->echo_message( 'Die E-Mail-Adresse wurde auf "'.$_POST['mail'].'" gesetzt!' );
		}

		$css = $guestfile->read_kimb_one( 'css' );
		if( $css != $_POST['css'] ){
			if( empty( $_POST['css'] ) ){
				$_POST['css'] = $cssallg;
			}
			if( empty( $css ) ){
				$guestfile->write_kimb_new( 'css' , $_POST['css'] );
			}
			else{
				$guestfile->write_kimb_replace( 'css' , $_POST['css'] );
			}
			$sitecontent->echo_message( 'Das Design wurde geändert!' );
		}

	}
	else{
		$sitecontent->echo_error( 'Fehlerhafte Anfrage! Bitte füllen Sie alle Felder (CSS kann leer bleiben)!' , 'Eingaben' );
	}
}

$sitecontent->add_site_content('<hr /><h2>Seiten mit Gästebuch</h2>');

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

$sitecontent->add_html_header('<script>
var del = function( id ) {
	$( "#del-guestbooksite" ).show( "fast" );
	$( "#del-guestbooksite" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$addonurl.'&id=" + id + "&del";
			return true;
		},
		Cancel: function() {
			$( this ).dialog( "close" );
			return false;
		}
	}
	});
}
</script>');

$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Hier können Sie allgemeine Einstellungen vornhemen und ein Gästebuch/ eine Kommentarmöglichkeit auf bestimmten Seite anzeigen. In der Liste werden die SiteIDs (Seiten -> Auflisten) angezeigt. Die Beiträge der Gästebücher können Sie unter Nutzung (Link oben rechts) verwalten."></span>');
$sitecontent->add_site_content('<table width="100%"><tr><th>SiteID</th><th width="20px;">Löschen</th></tr>');

foreach( $guestfile->read_kimb_all_teilpl( 'siteid' ) as $id ){

	$del = '<span onclick="var delet = del( '.$id.' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Gästebuch löschen. (inklusive aller Beiträge)"></span></span>';

	$sitecontent->add_site_content('<tr><td>'.$id.'</td><td>'.$del.'</td></tr>');

	$gefunden = 'yes';
}

if( $gefunden != 'yes' ){
	$sitecontent->add_site_content('</table>');
	$sitecontent->echo_error( 'Es wurden keine Gästebuchseiten gefunden!' , 'unknown' );
}
else{
	$sitecontent->add_site_content('</table>');
}

$sitecontent->add_site_content('<form action="'.$addonurl.'&amp;new" method="post"><span class="ui-icon ui-icon-plus" title="Bei einer weiteren Seite erstellen." style="display:inline-block;"></span>'.id_dropdown( 'id', 'siteid' ).'<input type="submit" value="Erstellen" ></form>');

$sitecontent->add_site_content('<div style="display:none;"><div id="del-guestbooksite" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie dieses Gästebuch und alle seine Beiträge wirklich löschen?</p></div></div>');

$ch = array( ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' );

if( $guestfile->read_kimb_one( 'nurfeloginuser' ) == 'off' ){
	$ch[1] = ' checked="checked" ';
}
else{
	$ch[2] = ' checked="checked" ';
}
if( $guestfile->read_kimb_one( 'mailinfo' ) == 'off' ){
	$ch[3] = ' checked="checked" ';
}
else{
	$ch[4] = ' checked="checked" ';
}
if( $guestfile->read_kimb_one( 'newstatus' ) == 'off' ){
	$ch[5] = ' checked="checked" ';
}
else{
	$ch[6] = ' checked="checked" ';
}
if( $guestfile->read_kimb_one( 'ipsave' ) == 'off' ){
	$ch[7] = ' checked="checked" ';
}
else{
	$ch[8] = ' checked="checked" ';
}

$css = $guestfile->read_kimb_one( 'css' );
if( empty( $css ) ){
	$css = $cssallg;
}


$sitecontent->add_site_content('<hr /><h2>Allgemeine Einstellungen</h2>');

$sitecontent->add_site_content('<form action="'.$addonurl.'&amp;settings" method="post" >');

$sitecontent->add_site_content('<input type="radio" name="feloginoo" value="off"'.$ch[1].'><span style="display:inline-block;" title="Allen Usern das Kommentieren erlauben" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="feloginoo" value="on"'.$ch[2].'> <span style="display:inline-block;" title="Nur eingeloggten Usern das Kommentieren erlauben (Add-on &apos;felogin&apos; nötig)" class="ui-icon ui-icon-check"></span>(Login)<br />');

$sitecontent->add_site_content('<input type="radio" name="mailoo" value="off"'.$ch[3].'><span style="display:inline-block;" title="Keine E-Mail bei neuen Beiträgen senden" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="mailoo" value="on"'.$ch[4].'> <span style="display:inline-block;" title="Eine E-Mail an die Adresse unten senden, wenn ein neuer Beitrag vorhanden ist" class="ui-icon ui-icon-check"></span> (E-Mail)<br />');

$sitecontent->add_site_content('<input type="radio" name="nstatoo" value="off"'.$ch[5].'><span style="display:inline-block;" title="Neue Beiträge vor Veröffentlichung prüfen" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="nstatoo" value="on"'.$ch[6].'> <span style="display:inline-block;" title="Neue Beiträge gleich veröffentlichen" class="ui-icon ui-icon-check"></span> (Status)<br />');

$sitecontent->add_site_content('<input type="radio" name="ipoo" value="off"'.$ch[7].'><span style="display:inline-block;" title="IP des Users nicht speichern" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input type="radio" name="ipoo" value="on"'.$ch[8].'> <span style="display:inline-block;" title="IP des Users speichern (Hinweis wird angezeigt)" class="ui-icon ui-icon-check"></span> (IP)<br />');

$mailinfo = $guestfile->read_kimb_one( 'mailinfoto' );

if( empty( $mailinfo )){
	$mailinfo = $allgsysconf['adminmail'];
}

$sitecontent->add_site_content('<input type="text" name="mail" value="'.$mailinfo.'" > (E-Mail-Adresse)<br />');
$sitecontent->add_site_content('<textarea name="css" style="width:99%; height:75px;">'.$css.'</textarea>(&uarr; CSS-Style  (leer == Zurücksetzen))<br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');

?>
