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

//alles laden
require_once( __DIR__.'/tracking_codes.php' );

//da in Klasse, evtl. dbf neu laden
if( !is_object( $analyticsconffile ) ){
	$analyticsconffile = new KIMBdbf( 'addon/analytics__conf.kimb' );
}

//Add-on URL, damit kann man später einfacher abrbeiten
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=analytics';

//Form beginnen
$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

//Texte Vorgaben
$examples = array( 'sys' => 'p' , 'infob' => 'on' , 'css' => 'div#analysehinweis{position:fixed; left:0; top:0; width:100%; background-color:orange; text-align:center; z-index:2;}' , 'text' => '<p>Diese Seite nutzt Cookies und einen Webanalysedienst. Mit der Nutzung dieser Seite erklären Sie sich damit einverstanden.</p><p><i>Weitere Informationen: <a href="http://www.example.com/" target="_blank" style="color:#ffffff; text-decoration:underline;">Impressum &amp; Datenschutz</a>!</i></p>' , 'url' => 'http://example.com/piwik' , 'pid' => '2' , 'piwikno' => 'on' , 'gaid' => 'UA-123456-1' );

//Daten zum Speichern da?
if( isset( $_POST['send'] ) ){

	//alles was zu tun muss (Zuordung POST Name und dbf Tag)
	$array[0] = array( 'sys' => 'anatool' , 'infob' => 'infobann' , 'css' => 'ibcss' , 'text' => 'ibtext' );
	$array[1] = array( 'url' => 'url' , 'pid' => 'id' , 'piwikno' => 'pimg' );
	$array[2] = array( 'gaid' => 'id' );

	//alles durchgehen und richtig abspeichern
	//	Medlungen bei Änderungen
	foreach( $array as $k1 => $v1 ){
		if( $k1 == 0 ){
			foreach( $v1 as $k2 => $v2 ){
				$inhalt = $analyticsconffile->read_kimb_one( $v2 );
				if( $inhalt != $_POST[$k2] || empty( $_POST[$k2] ) ){
					if( empty( $_POST[$k2] ) ){
						$_POST[$k2] = $examples[$k2];
					}

					$analyticsconffile->write_kimb_one( $v2 , $_POST[$k2] );
					
					$sitecontent->echo_message( 'Der Wert "'.$v2.'" wurde geändert!' );
				}
			}
		}
		else{
			foreach( $v1 as $k2 => $v2 ){
				$inhalt = $analyticsconffile->read_kimb_id( $k1 , $v2 );
				if( $inhalt != $_POST[$k2] || empty( $_POST[$k2] ) ){
					if( empty( $_POST[$k2] ) ){
						$_POST[$k2] = $examples[$k2];
					}

					$analyticsconffile->write_kimb_id( $k1 , 'add' , $v2 , $_POST[$k2] );
					$sitecontent->echo_message( 'Der Wert "'.$v2.'" wurde geändert!' );
				}
			}

		}
	}
}

//Werte lesen

//zu lesenden Werte (Zuordung dbf, Array Key für Formularausgabe)
$syss['anatool'] = array( 'p' => 'p' , 'pimg' => 'pimg' , 'ga' => 'ga' );
$syss['infobann'] = array( 'on' => 'ion' , 'off' => 'ioff' );
$syss[1] = array( 'on' => 'pnon' , 'off' => 'pnoff' );

//alles durchgehen und in Arrays lesen
foreach( $syss as $id => $value ){
	if( $id == 1 ){
		$inhalt = $analyticsconffile->read_kimb_id( $id , 'pimg' );
	}
	else{
		$inhalt = $analyticsconffile->read_kimb_one( $id );
	}

	foreach( $value as $iid => $vvalue ){
		if( $iid == $inhalt ){
			$sys[$vvalue] = 'checked="checked"';
		}
		else{
			$sys[$vvalue] = ' ';
		}
	}
}

//Vorgabetexte in Arrays für Formularausgabe
$val = array( 'css' => $examples['css'] , 'text'  => $examples['text'], 'url'  => $examples['url'] , 'pid' => $examples['pid'], 'gaid' => $examples['gaid'] );

//zu lesenden Werte (Zuordung dbf, Array Key für Formularausgabe)
$vals[0] = array( 'ibcss' => 'css' , 'ibtext' => 'text' );
$vals[1] = array( 'id' => 'pid' , 'url' => 'url' );
$vals[2] = array( 'id' => 'gaid' );

//alles durchgehen und Array entsprechende füllen
foreach( $vals as $key => $vval ){
	if( $key == 0 ){
		foreach( $vval as $kkkey => $vvval ){
			$temp = $analyticsconffile->read_kimb_one( $kkkey );
			if( !empty( $temp ) ){
				$val[$vvval] = $temp;
			}
		}
	}
	else{
		foreach( $vval as $kkkey => $vvval ){
			$temp = $analyticsconffile->read_kimb_id( $key , $kkkey );
			if( !empty( $temp ) ){
				$val[$vvval] = $temp;
			}

		}	
	}
}

//Formularausgaben
$sitecontent->add_site_content('<h3>Allgemein</h3>');
$sitecontent->add_site_content('<input name="sys" value="p" type="radio" '.$sys['p'].'> Piwik<span style="display:inline-block;" title="Piwik normal verwenden" class="ui-icon ui-icon-info"></span>' );
$sitecontent->add_site_content('<input name="sys" value="pimg" type="radio" '.$sys['pimg'].'> Piwik Img<span style="display:inline-block;" title="Piwik nur mit Bild verwenden (weniger Daten, kein JavaScript nötig)" class="ui-icon ui-icon-info"></span>' );
$sitecontent->add_site_content('<input name="sys" value="ga" type="radio" '.$sys['ga'].'> Google Analytics<span style="display:inline-block;" title="Google Analytics" class="ui-icon ui-icon-info"></span><br />' );

$sitecontent->add_site_content('<h3>Infobanner</h3>');
$sitecontent->add_site_content('<input name="infob" value="on" type="radio" '.$sys['ion'].'><span style="display:inline-block;" title="Infobanner bei erstem Seitenaufruf anzeigen ( rechtlich teilweise nötig um über Cookies und Webanalyse zu infomieren )" class="ui-icon ui-icon-check"></span>' );
$sitecontent->add_site_content('<input name="infob" value="off" type="radio" '.$sys['ioff'].'><span style="display:inline-block;" title="Kein Infobanner anzeigen" class="ui-icon ui-icon-closethick"></span><br />' );
$sitecontent->add_site_content('<textarea name="css" style="width:75%; height:70px;" >'.$val['css'].'</textarea> CSS<b title="CSS Code für den Infobanner ( leer => Voreinstellung )">*</b><br />');
$sitecontent->add_site_content('<textarea name="text" style="width:75%; height:70px;" >'.$val['text'].'</textarea> Text<b title="Text des Infobanners ( leer => Voreinstellung , Achtung: Link zum Impressum einfügen)">*</b><br />');

$sitecontent->add_site_content('<h3>Piwik <b title="Diese Einstelungen müssen Sie nur ändern wenn Sie Piwik verwenden!">*</b></h3>');
$sitecontent->add_site_content('<input name="url" value="'.$val['url'].'" type="text"> URL<b title="Bitte geben Sie hier die URL zu Ihrer Piwik-Installation an. (z.B.: http://example.com/piwik)">*</b><br />' );
$sitecontent->add_site_content('<input name="pid" value="'.$val['pid'].'" type="text"> ID<b title="Bitte geben Sie hier die ID der Seite an. ( Administration => Webseiten => ID )">*</b><br />' );
$sitecontent->add_site_content('<input name="piwikno" value="on" type="radio" '.$sys['pnon'].'><span style="display:inline-block;" title="Wenn der User kein JavaScript aktiviert hat, das Piwik-Tracking per Bild nutzen." class="ui-icon ui-icon-check"></span>' );
$sitecontent->add_site_content('<input name="piwikno" value="off" type="radio" '.$sys['pnoff'].'><span style="display:inline-block;" title="Kein alternatives Tracking per Bild." class="ui-icon ui-icon-closethick"></span><br />' );

$sitecontent->add_site_content('<h3>Google Analytics <b title="Diese Einstelungen müssen Sie nur ändern wenn Sie Google Analytics verwenden!">*</b></h3>');
$sitecontent->add_site_content('<input name="gaid" value="'.$val['gaid'].'" type="text"> Google Analytics Property<b title="Bitte geben Sie hier die Google Analytics Property für diese Seite an! (z.B.: UA-123456-1 )">*</b><br />' );

$sitecontent->add_site_content('<br /><br /><input type="hidden" value="send" name="send"><input type="submit" value="Speichern"> </form>');


?>
