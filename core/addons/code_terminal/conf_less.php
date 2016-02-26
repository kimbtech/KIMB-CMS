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
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=code_terminal';

//Konfigurationsdatei
$html_out_konf = new KIMBdbf( 'addon/code_terminal__conf.kimb' );

//Vorgabe (alles aus)
$oo = array(
	array('checked="checked"', ' ' ),
	array('checked="checked"', ' ' ),
	array('checked="checked"', ' ' ),
	array('checked="checked"', ' ' )
);
//Zuordnung der Index der Arrays ($oo und $_POST['oo']) zu den dbf Tags
$dbfoo = array(
	0 => 'fe',
	1 => 'be',
	2 => 'cr',
	3 => 'fc'	
);

//Übergabe?
if( isset( $_POST['oo'] ) ){
	//Auch ein Array?
	if( is_array( $_POST['oo'] ) ){
		//Übergabearray von Post Array trennen
		$poo = $_POST['oo'];
		
		//Übergabe durchgehen
		foreach( $poo as $key => $p ){
			//on oder off?
			if( $p == 'on' || $p == 'off' ){
				//gibt es den Index überhaupt?
				if( isset($dbfoo[$key]) ){
					//dbf Tag ermitteln
					$dbftag = $dbfoo[$key];
					//aktuellen Wert aus dbf lesen
					$dbfval = $html_out_konf->read_kimb_one( $dbftag );
					
					//Wert aus dbf anders als Wert aus Übergabe?
					if( $dbfval != $p ){
						//Wert anpassen
						$dbfval = $html_out_konf->write_kimb_one( $dbftag, $p );
						
						//Medlung anpassen
						$message .= 'Der Status von "'.$dbftag.'" wurde geändert!<br />';
					}
				}
			}
		}
		
		//Medlung vorhanden?
		if( !empty( $message) ){
			//ausgeben
			$sitecontent->echo_message( $message );
		}
	}
}

//aktuelle Werte lesen
//	alle dbf Tags durchgehen
foreach($dbfoo as $key => $dbftag ){
	//Wert lesen
	$dbfval = $html_out_konf->read_kimb_one( $dbftag );
	//ist der Wert auf on?
	if( $dbfval == 'on' ){
		//dann das Array für die Radio Buttons anpassen
		$oo[$key] = array( ' ', 'checked="checked"' );
	}
	//sonst Standard lassen
	//	(alles auf off)
}

//Hinweistext
$sitecontent->add_site_content('Hier können Sie nur verschiedene Ausgaben aktivieren und deaktivieren. Die Ausgaben (Codes) können Sie über Add-on Konfiguration verändern.');

//Formular beginnen
$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

//Frontend on/off 
$sitecontent->add_site_content('<h4>Frontend</h4>');
$sitecontent->add_site_content('<input name="oo[0]" type="radio" value="off" '.$oo[0][0].'><span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="oo[0]" value="on" type="radio" '.$oo[0][1].'><span style="display:inline-block;" title="Aktiviert" class="ui-icon ui-icon-check"></span><br /><br />');

//Backend on/off
$sitecontent->add_site_content('<h4>Backend</h4>');
$sitecontent->add_site_content('<input name="oo[1]" type="radio" value="off" '.$oo[1][0].'><span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="oo[1]" value="on" type="radio" '.$oo[1][1].'><span style="display:inline-block;" title="Aktiviert" class="ui-icon ui-icon-check"></span><br /><br />');

//Cron on/off
$sitecontent->add_site_content('<h4>Cron</h4>');
$sitecontent->add_site_content('<input name="oo[2]" type="radio" value="off" '.$oo[2][0].'><span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="oo[2]" value="on" type="radio" '.$oo[2][1].'><span style="display:inline-block;" title="Aktiviert" class="ui-icon ui-icon-check"></span><br /><br />');

//Funcclass on/off
$sitecontent->add_site_content('<h4>Funcclass</h4>');
$sitecontent->add_site_content('<input name="oo[3]" type="radio" value="off" '.$oo[3][0].'><span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="oo[3]" value="on" type="radio" '.$oo[3][1].'><span style="display:inline-block;" title="Aktiviert" class="ui-icon ui-icon-check"></span><br /><br />');

//Button
$sitecontent->add_site_content('<input type="submit" value="Änderungen übernehmen"> </form>');

?>