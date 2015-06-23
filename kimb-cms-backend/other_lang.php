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

define("KIMB_CMS", "Clean Request");

//Diese Datei ist Teil des Backends, sie wird direkt aufgerufen.

//Konfiguration & Klassen & Funktionen laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Diese Datei stellt unter Other den Teil "Mehrsprachige Seite" bereit

//Rechte prüfen
check_backend_login( 'twentyone' , 'more');

//Infotext und Überschrift
$sitecontent->add_site_content('<h2>Mehrsprachige Seite <span class="ui-icon ui-icon-info" title="Hier können Sie die Seite dieses CMS um weitere Sprachen erweitern!" style="display:inline-block;"></span></h2>');

//CSS für Buttons
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; } label{ width:200px; }</style>');

//Einstellungsmenü (nur wenn User dies aufruft und mehrsprachige Seiten aktiviert)
if( isset( $_GET['do'] ) && $allgsysconf['lang'] == 'on' ){
	
	//Sprachdatei lesen
	$langfile = new KIMBdbf( 'site/langfile.kimb' );
	
	//Standardsprache Werte übergeben?
	if( !empty( $_POST['stdtag'] ) && !empty( $_POST['stdlangname'] ) ){
		
		//aktuelle Werte lesen
		$vals = $langfile->read_kimb_id( '0' );
		
		//bisher nichts geändert
		$ch = false;
		
		//aktueller Tag anders als übergebener Wert
		if( $vals['tag'] != $_POST['stdtag'] ){
			//ändern
			$langfile->write_kimb_id( '0', 'add', 'tag', $_POST['stdtag'] );
			//da wurde was geändert
			$ch = true;
		}
		//Status anders?
		if( $vals['status'] != 'on' ){
			//anpassen
			$langfile->write_kimb_id( '0', 'add', 'status', 'on' );
			//da wurde was geändert
			$ch = true;
		}
		//Name anders?
		if( $vals['name'] != $_POST['stdlangname'] ){
			//anpassen
			$langfile->write_kimb_id( '0', 'add', 'name', $_POST['stdlangname'] );
			//da wurde was geändert
			$ch = true;
		}
		//Flagge verändert?
		if( $vals['flag'] != $allgsysconf['siteurl'].'/load/system/flags/'.$_POST['stdtag'].'.gif' ){
			//existiert die neuen Flagge überhaupt?
			if( is_file( __DIR__.'/../load/system/flags/'.$_POST['stdtag'].'.gif' ) ){
				//hinzufügen (mit SYS-SITEURL Platzhalter)
				$langfile->write_kimb_id( '0', 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/'.$_POST['stdtag'].'.gif' );
			}
			else{
				//die Flagge exstiert nicht
				//	? hinzufügen (mit SYS-SITEURL Platzhalter)
				$langfile->write_kimb_id( '0', 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/new.gif' );			
			}
			//da wurde was geändert
			$ch = true;
		}
		
		//wenn Änderung drfolgt Hinweis
		if( $ch ){
			$sitecontent->echo_message( 'Die Änderungen der Standardsprache wurden gespeichert!' );
		}
	}
	//wurde eine neue Sprache hinzugefügt?
	if( !empty( $_POST['newtag'] ) && !empty( $_POST['newlangname'] ) ){
		
		//existiert der Tag schon (Standardprache und andere Sperachen) (jede Sprache darf es nur ein Mal geben)?
		//ist der 2 Zeichen lang?
		if( $langfile->search_kimb_xxxid( $_POST['newtag'] , 'tag' ) == false && strlen( $_POST['newtag'] ) == 2 && $langfile->read_kimb_id( '0', 'tag' ) != $_POST['newtag'] ){
		
			//freie ID für Sprache suchen
			$id = $langfile->next_kimb_id();
			
			//Tag speichern
			$langfile->write_kimb_id( $id, 'add', 'tag', $_POST['newtag'] );
			//Status speichern
			$langfile->write_kimb_id( $id, 'add', 'status', 'on' );
			//Namen speichern
			$langfile->write_kimb_id( $id, 'add', 'name', $_POST['newlangname'] );
			//existiert die neuen Flagge überhaupt?
			if( is_file( __DIR__.'/../load/system/flags/'.$_POST['newtag'].'.gif' ) ){
				//hinzufügen (mit SYS-SITEURL Platzhalter)
				$langfile->write_kimb_id( $id, 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/'.$_POST['newtag'].'.gif' );
			}
			else{
				//die Flagge exstiert nicht
				//	? hinzufügen (mit SYS-SITEURL Platzhalter)
				$langfile->write_kimb_id( $id, 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/new.gif' );			
			}
			
			//Meldung
			$sitecontent->echo_message( 'Eine neue Sprache wurde hinzugefügt!' );
			
		}
		else{
			//Fehler ausgeben wenn Sprache schon da oder Tag falsch
			$sitecontent->echo_error( 'Der Tag ist schon vergeben oder hat keine 2 Zeichen!' );
		}
	}
	//Status ändern
	//	SprachID korrekt & nicht Standardsprache (immer aktiviert)
	if( isset( $_GET['chdeak'] ) && is_numeric( $_GET['id'] ) && $_GET['id'] != 0 ){
		
		//aktuellen Status lesen
		$stat = $langfile->read_kimb_id( $_GET['id'], 'status' );
		
		//wenn aktuell an,
		if( $stat == 'on' ){
			//dann aus machen
			$langfile->write_kimb_id( $_GET['id'], 'add', 'status', 'off' );
		}
		else{
			//sonst an machen
			$langfile->write_kimb_id( $_GET['id'], 'add', 'status', 'on' );
		}
		
		//Meldung
		$sitecontent->echo_message( 'Der Status einer Sprache wurde geändert!' );
	}

	//Formular
	
	//	JavaScript Datei mit allen Tags und Namen der Sprachen
	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/langs.min.js"></script>');
	//Autocomplete aktivieren
	//bei Eingabe eines Tag automatisch Namen und Flagge anpassen
	//Standardsprache Eingabe on/off bei Klick
	$sitecontent->add_html_header('<script>$(function() { $( "#autoc" ).autocomplete({ source: langtag }); $( "#autocstd" ).autocomplete({ source: langtag }); });
	function setnameflag( id , tagv, langn ){ var tagna = $( "input." + tagv ).val(); $( "#" + langn ).val( langobj[tagna] ); $( "img#" + id ).attr( "src" , "'.$allgsysconf['siteurl'].'/load/system/flags/" + tagna + ".gif" ); }
	function make_uneditable( id ){ $("input#" + id ).attr( "readonly" , true); } function make_editable( id ){ $("input#" + id ).removeAttr( "readonly" , true); } </script>');

	$sitecontent->add_site_content('<h3>Sprachen dieser Seite</h3>');

	//HTML Form
	$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?do">');

	//Standardsprache lesen
	$vals = $langfile->read_kimb_id( '0' );

	//Tabelle für Sprachdaten
	$sitecontent->add_site_content('<table width="100%"><tr> <th>ID</th> <th>Tag <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Der Tag dient für die URL!"></span></th> <th>Name</th> <th>Flagge</th> <th>Status <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Eine Sprache kann nicht gelöscht, nur deaktiviert, werden!"></span></th> </tr>');
	
	//Tabellenzeile Standardsprache
	$sitecontent->add_site_content('<tr> 
		<td>0 <span class="ui-icon ui-icon-info" title="Standard ( Sprache des ersten Contents )" style="display:inline-block;"></span></td> 
		<td><input style="width:95%;" type="text" name="stdtag" id="autocstd" class="stdtag" placeholder="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" title="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" onblur="setnameflag( \'stdflag\', \'stdtag\', \'stdlangnam\' );" value="'.$vals['tag'].'"></td> 
		<td><input style="width:95%;" type="text" readonly="readonly" onfocus="make_editable( \'stdlangnam\' );" onblur="make_uneditable( \'stdlangnam\' );" name="stdlangname" title="Klicken zum Ändern!" id="stdlangnam" value="'.$vals['name'].'"></td>
		');
		//Flagge gesetzt?
		if( !empty( $vals['flag'] ) ){
			//ja, diese anzeigen
			$sitecontent->add_site_content('<td><img src="'.$vals['flag'].'" id="stdflag" title="Flag" alt="Flagge" /></td>');
		}
		else{
			//nein, ? anzeigen
			$sitecontent->add_site_content('<td><img src="'.$allgsysconf['siteurl'].'/load/system/flags/new.gif" id="stdflag" title="Flag" alt="Flagge" /></td>');	
		}
		//Button für Änderungen
		//Zwischenzeile
		$sitecontent->add_site_content(' 
		<td><input type="submit" value="Ändern"></td>
	</tr><tr><td colspan="5"></td></tr>');
	
	//alle anderen Sprachen lesen
	foreach( $langfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){
		//ID 0 wäre Standard und der ist schon gelesen
		if( $id != 0){
			//Werte der aktuellen Sprache lesen
			$vals = $langfile->read_kimb_id( $id );
			//Je nach Status passendes Icon des Status-ändern-Buttons
			if( $vals['status'] == 'on' ){
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?do&amp;chdeak&amp;id='.$id.'"><span class="ui-icon ui-icon-check" style="display:inline-block;" title="Diese Sprache ist zu Zeit aktiviert. ( click -> ändern )" ></span></a>';	
			}
			else{
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?do&amp;chdeak&amp;id='.$id.'"><span class="ui-icon ui-icon-close" style="display:inline-block;" title="Diese Sprache ist zu Zeit deaktiviert. ( click -> ändern )" ></span></a>';
			}
			//Tabellenzeile ausgeben
			$sitecontent->add_site_content('<tr> <td>'.$id.'</td> <td>'.$vals['tag'].'</td> <td>'.$vals['name'].'</td> <td><img src="'.$vals['flag'].'" title="Flag" alt="Flagge" /></td> <td>'.$status.'</td> </tr>');
		}
	}

	//neue Sprache hinzufügen Eingabe
	$sitecontent->add_site_content('<tr> 
		<td>X</td> 
		<td><input style="width:95%;" type="text" name="newtag" id="autoc" class="newtag" placeholder="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" title="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" onblur="setnameflag( \'flag\', \'newtag\', \'langnam\' );"></td> 
		<td><input style="width:95%;" type="text" readonly="readonly" onfocus="make_editable( \'langnam\' );" onblur="make_uneditable( \'langnam\' );" name="newlangname" title="Klicken zum Ändern!" id="langnam"></td> 
		<td><img src="'.$allgsysconf['siteurl'].'/load/system/flags/new.gif" id="flag" title="Flag" alt="Flagge" /></td> 
		<td><input type="submit" value="Hinzufügen"></td> 
	</tr>');
	
	$sitecontent->add_site_content('</form>');


}
//User will Einstellungsmenü nicht oder mehrsprachige Seiten deaktiviert!
else{
	//Button de-, aktivieren per jQueryUI
	//	onlick -> Status (mehrsprachige Seiten on/off) entsprechend setzen
	$sitecontent->add_html_header('<script>$(function() { $( "#onoff" ).buttonset(); $( "#on" ).click(function() { window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?chdeak=on"; }); $( "#off" ).click(function() { window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?chdeak=off"; }); }); </script>' );

	//Status (mehrsprachige Seiten on/off) ändern?
	if( isset( $_GET['chdeak'] ) ){
		//auf on setzen
		if( $_GET['chdeak'] == 'on' ){
			//dbf anpassen
			$conffile->write_kimb_id( '001' , 'add' , 'lang' , 'on' );
			//allgsysconfig aktualisieren
			$allgsysconf['lang'] = 'on';
		}
		//auf off setzen
		elseif( $_GET['chdeak'] == 'off' ){
			//dbf anpassen
			$conffile->write_kimb_id( '001' , 'add' , 'lang' , 'off' );
			//allgsysconfig aktualisieren
			$allgsysconf['lang'] = 'off';
		}
	}

	//checked für den on/off Button richtig in Array laden
	if( $allgsysconf['lang'] == 'on' ){
		$checked['on'] = 'checked="checked"';
		$checked['off'] = '';
	}
	else{
		$checked['off'] = 'checked="checked"';
		$checked['on'] = '';
	}

	//HTML Code der Buttons
	$sitecontent->add_site_content('<br /><br /><center><form> <div id="onoff"> <input type="radio" id="on" name="onoff" '.$checked['on'].'> <label for="on">Aktiviert</label> <input type="radio" id="off" name="onoff" '.$checked['off'].'> <label for="off">Deaktiviert</label> </div> </form></center><br /><br />');

	//mehrsprachige Seiten aktiviert?
	if( $allgsysconf['lang'] == 'on' ){

		//wenn aktiviert, dann Button zum Einstellungsmenü
		//	JavaScript des Buttons
		$sitecontent->add_html_header('<script> $(function() { $( "a#dospreinst" ).button(); }); </script>');
		//und Meldung
		$sitecontent->echo_message( 'Mehrsprachige Seiten aktiviert!' );

		//	HTML des Buttons
		$sitecontent->add_site_content('<br /><br /><center><a id="dospreinst" href="?do">Zu den Einstellungen &rarr;</a></center>');
	}
	else{
		//deaktiviert, Hinweis geben
		$sitecontent->echo_message( 'Mehrsprachige Seiten deaktiviert!' );
	}
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
