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

//Konfiguration laden und Handhabung vereinfachen
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=felogin';
$feconf = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$feuser = new KIMBdbf( 'addon/felogin__user.kimb'  );

//Überschrift
$sitecontent->add_site_content('<h2>Userlogin Gruppen</h2>');

//alle Gruppen lesen
$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );

//neue Gruppe und erste RequestID dafür gesetzt?
if( !empty( $_POST['newgr'] ) && !empty( $_POST['firstrequid'] ) ){
	
	//Gruppennamen säubern
	$_POST['newgr'] = preg_replace( "/[^a-zA-Z0-9]/" , "" , $_POST['newgr'] );

	//neue Gruppe schon vorhanden?
	if( !in_array( $_POST['newgr'] , $gruppen ) ){
		//wenn nicht dann aufschreiben
		$feconf->write_kimb_new( $_POST['newgr'] , $_POST['firstrequid'] );

		//Gruppenliste schon da?
		if( empty( $feconf->read_kimb_one( 'grlist' ) ) ){
			//erste Gruppe hinzufügen
			$feconf->write_kimb_new( 'grlist' , $_POST['newgr'] );
		}
		else{
			//neue Gruppe der Liste anfügen
			$new = $feconf->read_kimb_one( 'grlist' );
			$new .= ','.$_POST['newgr'];
			$feconf->write_kimb_replace( 'grlist' , $new );
		}
		//Medlung
		$sitecontent->echo_message( 'Die Gruppe "'.$_POST['newgr'].'" wurde hinzugefügt!' );
		//Gruppen neu laden
		$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );
	}
	else{
		//Fehler wenn Gruppe schon da
		$sitecontent->echo_error( 'Die Gruppe existiert bereits!' , 'unknown' );
	}
}
//Gruppe löschen?
elseif( isset( $_GET['gruppe'] ) && isset( $_GET['del'] )  ){
	//Gruppe vorhaden
	$read = $feconf->read_kimb_one( $_GET['gruppe'] );
	if( !empty( $read ) ){
		//wenn Gruppe gefunden -> Löschen beginnen
		
		//Gruppe aus der Gruppenliste entfernen
		foreach( $gruppen as $gr ){
			if( $gr != $_GET['gruppe'] ){
				$newgrlist .= ','.$gr;
			}
		}
		//vorne ist in der Liste immer ein Komma zu viel, weg damit
		$newgrlist = substr( $newgrlist , 1 );

		//Gruppendaten löschen
		$feconf->write_kimb_delete( $_GET['gruppe'] );

		if( empty( $newgrlist ) ){
			//wenn neue Gruppenliste leer, Gruppenliste löschen
			$feconf->write_kimb_delete( 'grlist' );
		}
		else{
			//sonst überschreiben
			$feconf->write_kimb_replace( 'grlist' , $newgrlist );
		}		

		//Medlung
		$sitecontent->echo_message( 'Die Gruppe "'.$_GET['gruppe'].'" wurde gelöscht!' );
		//Gruppen neu laden
		$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );
	}
}

//Gruppe bearbeiten?
if( isset( $_GET['gruppe'] ) && !isset( $_GET['del'] ) ){
	
	//per JavaScript den unteren Teil "Userlogin Einstellungen" ausbelden (User kann diesen per Button wieder einblenden)
	$sitecontent->add_html_header('<script>$( function () { change_userlogineinst(); });</script>');

	//Link zur Übersichr über alle Gruppen
	$sitecontent->add_site_content('<a href="'.$addonurl.'" >&larr; Zur Übersicht</a><br /><br />');
	
	//Überschrift
	$sitecontent->add_site_content('<b>Gruppe "'.$_GET['gruppe'].'"</b>');

	//Gruppeninfos lesen
	$read = $feconf->read_kimb_one( $_GET['gruppe'] );

	//Gruppe gefunden?
	if( !empty( $read ) ){

		//Änderungen übertragen?
		if( isset( $_POST['change'] ) ){
			
			//alle Werte durchgehen
			$i = 1;
			while( 5 == 5 ){
				//SiteID gesetzt und nummerisch?
				if( isset( $_POST['siteids'.$i] ) && is_numeric( $_POST['siteids'.$i] ) ){
					//der Liste anfügen
					$sitelist .= ','.$_POST['siteids'.$i];
				}
				//SiteID nicht nummerisch (none) und auch nicht leer (->löschen)
				if(  !is_numeric( $_POST['siteids'.$i] ) && !empty( $_POST['siteids'.$i] ) ){
					//Medlung, dass none nicht erlaubt
					$sitecontent->echo_error( 'Sie können keine Seite mit "none" wählen!', 'unknown', 'Achtung' );
				}
				//wenn alle Übergaben durchgearbeitet
				if( $i >= $_POST['change'] ){
					break;
				}
				$i++;
			}
			//die Seitenliste hat immer ein Komma vorne -> weg damit
			$sitelist = substr( $sitelist , 1 );

			//Seitelist der Gruppe neu schreiben
			$feconf->write_kimb_replace( $_GET['gruppe'] , $sitelist );

			//Medlung
			$sitecontent->echo_message( 'Die Seiten wurden verändert!' );	
		}

		//Seitenliste der Gruppe lesen
		$read = $feconf->read_kimb_one( $_GET['gruppe'] );
		//Array mit Seiten erstellen
		$sites = explode( ',' , $read );

		//Formular beginnen
		$sitecontent->add_site_content('<form method="post" action="'.$addonurl.'&amp;gruppe='.$_GET['gruppe'].'">');

		//für jede Seite ein Dropdown anziegen
		//	Anzahl mitzählen
		$i = '1';
		foreach( $sites as $site ){
			//per JS aktuelle Seite wählen
			$bef .= '$( "[name=siteids'.$i.']" ).val( '.$site.' );';
			//Dropdown mit SiteIDs erstellen
			$sitecontent->add_site_content( id_dropdown( 'siteids'.$i, 'siteid' ) );
			//Mülleimer zum löschen der Seite anzeigen
			$sitecontent->add_site_content( '<span class="ui-icon ui-icon-trash" style="display:inline-block;" onclick="$( \'[name=siteids'.$i.']\' ).val( \'---none---\' );" title="Diese Seite entfernen."></span><br />' );
			$i++;
		}

		//weitere Seite hinzufügen
		//	Wert -> leer
		$bef .= '$( "[name=siteids'.$i.']" ).val( "---none---" );';
		//	Dropdown
		$sitecontent->add_site_content( id_dropdown( 'siteids'.$i, 'siteid' ) .' <b title="Eine Seite hinzufügen!">*</b><br />' );
		//Button und Anzahl der Dropdowns (durchläufe der while-Schleife oben)
		$sitecontent->add_site_content('<input type="hidden" name="change" value="'.$i.'" ><input type="submit" value="Ändern"></form>');

		//JS Code dem Header hinzufügen
		$sitecontent->add_html_header('<script>$(function(){ '.$bef.' }); </script>');
	}
	else{
		//Fehler wenn Gruppe nicht vorhanden
		$sitecontent->echo_error( 'Die Gruppe existiert nicht!' , 'unknown' );
	}

}
else{
	//JavaScript für Löschen Dialog der Gruppen
	$sitecontent->add_html_header('<script>
	var del = function( gruppe ) {
		$( "#del-felogingruppe" ).show( "fast" );
		$( "#del-felogingruppe" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$addonurl.'&del&gruppe=" + gruppe;
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

	$sitecontent->add_site_content('<table width="100%"><tr> <th width="75px;">Gruppenname</th> <th>Seiten <span class="ui-icon ui-icon-info" title="Entsprechen der SiteID ( Seiten -> Auflisten )!"></span></th> <th width="20px;">Löschen</th> </tr>');

	foreach( $gruppen as $gr ){
		$read = $feconf->read_kimb_one( $gr );
		if( $read != '' ){
			$del = '<span onclick="var delet = del( \''.$gr.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Diese Gruppe löschen."></span></span>';
			$sitecontent->add_site_content('<tr> <td><a title="Seiten ändern" href="'.$addonurl.'&amp;gruppe='.$gr.'">'.$gr.'</a></td> <td>'.$read.'</td> <td>'.$del.'</td> </tr>');
		}
	}
	$sitecontent->add_site_content('<tr> <td><form method="post" action="'.$addonurl.'"><input type="text" name="newgr" placeholder="hinzufügen" ></td> <td>'.id_dropdown( 'firstrequid', 'siteid' ).'</td> <td><input type="submit" value="Los" title="Eine neue Gruppe erstellen!" ></form></td> </tr>');
	$sitecontent->add_site_content('</table>');

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-felogingruppe" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie die Usergruppe wirklich löschen?</p></div></div>');
}

$sitecontent->add_html_header('<script>
function change_userlogineinst() {
	if( $( "div#userlogineinst" ).css( "display") == "none" ){
		$( "div#userlogineinst" ).css( "display", "block" );
		$( "div#userlogineinstanbutton" ).css( "display", "none" );
		$( "div#userlogineinstausbutton" ).css( "display", "block" );		
	}
	else{
		$( "div#userlogineinst" ).css( "display", "none" );
		$( "div#userlogineinstanbutton" ).css( "display", "block" );
		$( "div#userlogineinstausbutton" ).css( "display", "none" );
	}
}
</script>');

$sitecontent->add_site_content('<div id="userlogineinst"><hr />');

$sitecontent->add_site_content('<h2>Userlogin Einstellungen</h2>');

if( empty( $feconf->read_kimb_one( 'loginokay' ) ) ){
	$loginokay = makepassw( '75' , '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );
	$feconf->write_kimb_new( 'loginokay' , $loginokay );
}

if( !empty( $_POST['selfoo'] ) && !empty( $_POST['areaoo'] ) && !empty( $_POST['infomailoo'] ) && !empty( $_POST['selfgr'] ) && !empty( $_POST['akzep'] ) && !empty( $_POST['requid'] ) ){

	$arrays[] = array( 'teil' => 'selfoo' , 'trenner' => 'selfreg' );
	$arrays[] = array( 'teil' => 'areaoo' , 'trenner' => 'addonarea' );
	$arrays[] = array( 'teil' => 'infomailoo' , 'trenner' => 'infomail' );

	foreach( $arrays as $array ){
		$teil = $array['teil'];
		$trenner = $array['trenner'];

		if( $_POST[$teil] == 'on' || $_POST[$teil] == 'off' ){
			$wert = $feconf->read_kimb_one( $trenner );
			if( $wert != $_POST[$teil] ){
				if( empty( $wert ) ){
					$feconf->write_kimb_new( $trenner , $_POST[$teil] );
				}
				else{
					$feconf->write_kimb_replace( $trenner , $_POST[$teil] );
				}
				$sitecontent->echo_message( '"'.$trenner.'" wurde auf "'.$_POST[$teil].'" gesetzt!' );
			}
		}
	}

	$arrays[] = array( 'teil' => 'selfgr' , 'trenner' => 'selfreggruppe' );
	$arrays[] = array( 'teil' => 'akzep' , 'trenner' => 'akzepttext' );
	$arrays[] = array( 'teil' => 'requid' , 'trenner' => 'requid' );

	foreach( $arrays as $array ){
		$teil = $array['teil'];
		$trenner = $array['trenner'];

		$wert = $feconf->read_kimb_one( $trenner );
		if( $wert != $_POST[$teil] ){
			if( empty( $wert ) ){
				$feconf->write_kimb_new( $trenner , $_POST[$teil] );
			}
			else{
				$feconf->write_kimb_replace( $trenner , $_POST[$teil] );
			}
			$sitecontent->echo_message( '"'.$trenner.'" wurde auf verändert!' );
		}
	}

}
if( !empty( $_POST['selfoo'] ) && !empty( $_POST['areaoo'] ) && empty( $_POST['selfgr'] ) ){
	$sitecontent->echo_error( 'Bitte erstellen Sie zuerst eine Gruppe und/oder füllen Sie alle Felder' );
}

$oo = array( ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' );

if( $feconf->read_kimb_one( 'selfreg' ) == 'off' ){
	$oo[1] = ' checked="checked" ';
}
else{
	$oo[2] = ' checked="checked" ';
}
if( $feconf->read_kimb_one( 'addonarea' ) == 'off' ){
	$oo[3] = ' checked="checked" ';
}
else{
	$oo[4] = ' checked="checked" ';
}
if( $feconf->read_kimb_one( 'infomail' ) == 'off' ){
	$oo[5] = ' checked="checked" ';
}
else{
	$oo[6] = ' checked="checked" ';
}

$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

$sitecontent->add_site_content('<input name="selfoo" type="radio" value="off" '.$oo[1].'><span style="display:inline-block;" title="Keine Möglichkeit zum selbstständigen Registrieren anzeigen." class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="selfoo" value="on" type="radio" '.$oo[2].'><span style="display:inline-block;" title="Selbstständiges Registrieren ermöglichen." class="ui-icon ui-icon-check"></span><br />');

$sitecontent->add_site_content('<input name="areaoo" type="radio" value="off" '.$oo[3].'><span style="display:inline-block;" title="Das Loginformular nur auf einer Seite zeigen." class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="areaoo" value="on" type="radio" '.$oo[4].'><span style="display:inline-block;" title="Das Loginformular überall in einer Addonarea anzeigen." class="ui-icon ui-icon-check"></span><br />');

$sitecontent->add_site_content('<input name="infomailoo" type="radio" value="off" '.$oo[5].'><span style="display:inline-block;" title="Es wird keine E-Mail versendet, wenn sich ein neuer User registriert." class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="infomailoo" value="on" type="radio" '.$oo[6].'><span style="display:inline-block;" title="Wenn sich ein neuer User registriert, wird eine E-Mail an die Adresse des Administrators in der Konfiguration gesendet" class="ui-icon ui-icon-check"></span><br />');

foreach( $gruppen as $gr ){
	if( $gr == $feconf->read_kimb_one( 'selfreggruppe' ) ){
		$grdown .= '<option value="'.$gr.'" selected="selected" >'.$gr.'</option>';
	}
	else{
		$grdown .= '<option value="'.$gr.'" >'.$gr.'</option>';
	}
}

$sitecontent->add_site_content('<select name="selfgr">'.$grdown.'</select><span style="display:inline-block;" title="Bitte geben Sie an, zu welcher Gruppe selbstständig registrierte User hinzugefügt werden sollen." class="ui-icon ui-icon-info"></span><br />');
$sitecontent->add_site_content('<textarea name="akzep" style="width:200px; height:75px;">'.$feconf->read_kimb_one( 'akzepttext' ).'</textarea><span style="display:inline-block;" title="Dieser Text muss beim selbstständigen Registrieren eines Accounts angeklickt werden." class="ui-icon ui-icon-info"></span><br />');
$sitecontent->add_html_header('<script>$(function(){ $( "[name=requid]" ).val( '.$feconf->read_kimb_one( 'requid' ).' ); }); </script>');
$sitecontent->add_site_content( id_dropdown( 'requid', 'requid' ).' <span style="display:inline-block;" title="Bitte wählen Sie hier eine Seite, auf welcher alles rund um das Login angezeigt werden soll." class="ui-icon ui-icon-info"></span><br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern"><span style="display:inline-block;" title="Sie müssen alle Felder füllen!" class="ui-icon ui-icon-info"></span></form>');

$sitecontent->add_site_content('</div>');

$sitecontent->add_site_content('<hr /><div id="userlogineinstanbutton" style="display:none;">');
$sitecontent->add_site_content('<button onclick="change_userlogineinst(); return false;">Userlogin Einstellungen anzeigen</button>');
$sitecontent->add_site_content('</div>');
$sitecontent->add_site_content('<div id="userlogineinstausbutton">');
$sitecontent->add_site_content('<button onclick="change_userlogineinst(); return false;">Userlogin Einstellungen ausblenden</button>');
$sitecontent->add_site_content('</div>');

?>
