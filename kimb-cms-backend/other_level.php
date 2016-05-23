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

//Diese Datei stellt unter Other den Teil "Backend Userlevel" bereit

//Rechte prüfen
check_backend_login( 'nineteen' , 'more');

//CSS Tabelle
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//Level schon geladen, sonst tun
if( !is_object( $levellist ) ){
	$levellist = new KIMBdbf( 'backend/users/level.kimb' );
}

//Erstellung eines neuen Levels gewünscht?
if( $_GET['todo'] == 'new' ){
	$sitecontent->add_site_content('<h1>Neues Userlevel Backend erstellen</h1>');
	//Link zur Liste aller Level
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php">&larr; Alle Level</a><br /><br />');

	//schon Daten gesendet?
	if( isset( $_POST['name'] ) ){
		//Namen säubern
		$_POST['name'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_POST['name'] ) );
		//den Namen prüfen, sollte nicht leer sein
		//außerdem auch nicht more, less, all, levellist
		if( !empty($_POST['name'] ) && $_POST['name'] != 'more' && $_POST['name'] != 'less' && $_POST['name'] != 'all' && $_POST['name'] != 'levellist' ){
			//Name darf noch nicht vergeben sein
			if( empty( $levellist->read_kimb_one( $_POST['name'] ) ) ){
				//neues Level speichern (erstmal mit den Rechten für one,two,three)
				$levellist->write_kimb_new( $_POST['name'] , 'one,two,three' );
				//neues Level der Liste aller Level anfügen
				
				//aktuelle Liste lesen 
				$alllev = $levellist->read_kimb_one( 'levellist' );
				//gibt überhaupt schon Level?
				if( empty( $alllev ) ){
					//neues Level als erstes Level hinzufügen
					$levellist->write_kimb_new( 'levellist' , $_POST['name'] );
				}
				else{
					//neues Level den anderen hinzufügen
					$levellist->write_kimb_replace( 'levellist' , $alllev.','.$_POST['name'] );
				}

				//Levelbearbeitung öffnen
				open_url( '/kimb-cms-backend/other_level.php?todo=edit&level='.$_POST['name']  );
				die;
			}
			else{
				//Fehler wenn Levelname schon vergeben
				$sitecontent->echo_error( 'Der Levelname ist schon vergeben!' , 'unknown');
			}
		}
		else{
			//Eingabe nicht passend
			$sitecontent->echo_error( 'Ihre Eingabe war leer oder ein verbotener Wert (more, less, all, levellist)!' , 'unknown');
		}
	}

	//oder erstmal das Formular
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=new" method="post" >');
	$sitecontent->add_site_content('<input type="text" name="name"> <i title="Pflichtfeld, a-z">(Levelname *)</i><br />');
	$sitecontent->add_site_content('<input type="submit" value="Erstellen" >');
	$sitecontent->add_site_content('</form>');


}
//Levelbearbeitung
elseif( $_GET['todo'] == 'edit' && isset( $_GET['level'] ) ){
	$sitecontent->add_site_content('<h1>Userlevel Backend bearbeiten</h1>');
	//Link zur Liste aller Level
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php">&larr; Alle Level</a><br /><br />');

	//Namen säubern
	$_GET['level'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_GET['level'] ) );
	
	//alle Level lesen
	$alllev = $levellist->read_kimb_one( 'levellist' );
	//Array mit allen Leveln erstellen
	$alllev = explode( ',' , $alllev );

	//wenn Level vorhanden
	if( in_array( $_GET['level'] , $alllev ) ){

		//Checkboxen per JavaScript aktivierbar machen
		$sitecontent->add_html_header('<script>
		function set_on( val ){
			$( "input[value=" + val + "]" ).prop( "checked" , true);
		}
		</script>');

		//wurden Daten zum abspeichern übergeben?
		if( is_array( $_POST['numbers'] ) ){

			//Seitenzugriffe
			//	das Array zu einem String machen
			$ges = implode( ',', $_POST['numbers'] );
			//	String schreiben
			$levellist->write_kimb_replace( $_GET['level'] , $ges );

			//Untergruppenzugriffe
			$arten = backend_untergrlevelarten();
			//	Daten der drei lesen
			foreach( $arten as $art ){
				//POST Daten holen
				$allg = $_POST[$art.'_allg'];
				$list = $_POST[$art.'_list'];
				
				//Tags für dbf machen
				$allgteil = $_GET['level'].'_'.$art.'_allg';
				$listteil = $_GET['level'].'_'.$art.'_list';
				
				//schreiben
				$levellist->write_kimb_one( $allgteil, $allg );
				//	Liste
				$listdata = implode( ',', $list );
				$levellist->write_kimb_one( $listteil, $listdata );
			}

			//Meldung
			$sitecontent->echo_message( 'Das Level wurde angepasst!' );
			$sitecontent->add_site_content('<br />');
		}

		//Rechte des Levels auslesen

		//Sting des Levels lesen
		$numbers = $levellist->read_kimb_one( $_GET['level'] );
		//Array erstellen
		$numbers = explode( ',' , $numbers );

		//Array $checks erstellen und für jedes gegebene Recht Value checked=checked setzen
		foreach( $numbers as $number ){
			$checks[$number] = ' checked="checked" ';
		}

		//alle möglichen Rechte lesen
		$numbers = $levellist->read_kimb_one( 'all' );
		//Array erstellen
		$numbers = explode( ',' , $numbers );

		//das Array $checkd ergänzen, für alle nicht gegebenen Rechte das Value '' setzen
		foreach( $numbers as $number ){
			if( !isset( $checks[$number] ) ){
				$checks[$number] = ' ';
			}
		}
		
		//Levelauswahlformular
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=edit&amp;level='.$_GET['level'].'" method="post" >');
		$sitecontent->add_site_content('<input type="text" name="name" readonly="readonly" value="'.$_GET['level'].'"> <i title="Nicht zu ändern.">(Levelname *)</i><br />');
		$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Klicken Sie alle Menuepunkte an, auf die ein User der Gruppe Zugriff haben soll! (Es dürfen nicht alle Felder deaktiviert sein!)"></span><br />');


		//Zugriffe auf Backendseiten
		$sitecontent->add_site_content('<h2>Backendseiten</h2>');
		$sitecontent->add_site_content('Wählen Sie aus auf welche Seiten im Backend die User zugreifen können.<br />');
		
		//Checkbox für jedes mögliche Recht
		//	neuen Backendseiten müssen hier hinzugefügt werden
		//	außerdem auch in die 'level.kimb'' unter 'all''
		//	und als Menüpunkt in die 'output_backend.php''
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="one"'.$checks['one'].'> Seiten (one)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'one\' );" value="two"'.$checks['two'].'> Neue Seite (two)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'one\' );" value="three"'.$checks['three'].'> Seite bearbeiten (three)<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="four"'.$checks['four'].'> Menue (four)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'four\' );" value="five"'.$checks['five'].'> Neues Menue (five)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'four\' );" value="six"'.$checks['six'].'> Menue Zuordnen (six)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'four\' );" value="seven"'.$checks['seven'].'> Menue bearbeiten (seven)<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="eight"'.$checks['eight'].'> User (eight) <b title="Dies muss aktiviert sein, wenn ein User die Möglichkeit haben soll, sich selbst zu verändern! (z.B Passwort )">*</b><br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'eight\' );" value="nine"'.$checks['nine'].'> Neuer User (nine)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'eight\' );" value="ten"'.$checks['ten'].'> User bearbeiten (ten)<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="eleven"'.$checks['eleven'].'> Konfiguration (eleven)<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="twelve"'.$checks['twelve'].'> Add-ons (twelve)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'twelve\' );" value="thirteen"'.$checks['thirteen'].'> Normale-Add-on-Einstellungen (thirteen)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'twelve\' );" value="fourteen"'.$checks['fourteen'].'> Admin-Add-on-Einstellungen (fourteen)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'twelve\' );" value="fiveteen"'.$checks['fiveteen'].'> Add-on installieren (fiveteen)<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="sixteen"'.$checks['sixteen'].'> Other (sixteen)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="seventeen"'.$checks['seventeen'].'> Filemanager (seventeen)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="eightteen"'.$checks['eightteen'].'> Themes (eightteen)<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="nineteen"'.$checks['nineteen'].'> Userlevel (nineteen) <br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="twenty"'.$checks['twenty'].'> Umzug (twenty) <br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="twentyone"'.$checks['twentyone'].'> Mehrsprachige Seite (twentyone) <br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="twentytwo"'.$checks['twentytwo'].'> Easy Menue (twentytwo) <br />');

		$sitecontent->add_site_content('<p><input type="submit" value="Änderungen Speichern" ></p>');

		//Zugriffe auf Untergruppen
		$sitecontent->add_site_content('<h2>Untergruppen</h2>');
		$sitecontent->add_site_content('Schränken Sie den Zugriff/ die Bearbeitung für einen User dieser Usergruppe auf bestimmte Seiten, Add-ons oder Menüs ein.<br />');
		
		//aktuelle Daten lesen
		//	Arten
		$arten = backend_untergrlevelarten();
		$untergrdata = array();
		foreach( $arten as $art ){				
			//Tags für dbf machen
			$allgteil = $_GET['level'].'_'.$art.'_allg';
			$listteil = $_GET['level'].'_'.$art.'_list';
				
			//lesen
			$allg = $levellist->read_kimb_one( $allgteil );
			$list = $levellist->read_kimb_one( $listteil );
			
			//bereiten
			$untergrdata[$art] = array(
				$allg,
				explode( ',', $list )
			);
		}

		//Tabs machen
		$sitecontent->add_html_header( '<script>  $(function() { $( "#untergrtabs" ).tabs(); });</script>' );
		$sitecontent->add_site_content('<div id="untergrtabs"><ul>');
		$sitecontent->add_site_content('<li><a href="#seiten">Seiten</a></li>');
		$sitecontent->add_site_content('<li><a href="#addon">Add-ons</a></li>');
		$sitecontent->add_site_content('<li><a href="#menues">Menüs</a></li>');
		$sitecontent->add_site_content('</ul>');
		
		//	Seiten
		$sitecontent->add_site_content('<div id="seiten">');
			$art = 'seiten';
			$sitecontent->add_site_content('<h3>Seiten</h3>');
			$sitecontent->add_site_content('Stellen Sie den Zugriff des Users auf die Seiten des CMS ein.<br />');
			$sitecontent->add_site_content('<small></small><br />');
			//allgemein (verbieten/ erlauben)
			$sitecontent->add_site_content('<input type="radio" name="seiten_allg" value="erlauben" '.( ( $untergrdata[$art][0] == 'erlauben' ) ? 'checked="checked"' : '' ).'>Ausgewählte Seiten erlauben<br />');
			$sitecontent->add_site_content('<input type="radio" name="seiten_allg" value="verbieten" '.( ( $untergrdata[$art][0] == 'verbieten' ) ? 'checked="checked"' : '' ).'>Ausgewählte Seiten verbieten<br />');
			$sitecontent->add_site_content('<br />');
			//Seitenliste
			$sitecontent->add_site_content('<select multiple="multiple" name="seiten_list[]">');
			//	alle IDs und Namen lesen
			$sites = list_sites_array();
			foreach ($sites as $site) {
				//durchgehen
				
				//aktivert?
				$selected = ( in_array( $site['id'], $untergrdata[$art][1] )  ? 'selected="selected"' : '' );
				
				$sitecontent->add_site_content('<option value="'.$site['id'].'" '.$selected.'>'.$site['site'].' ('.$site['id'].')</option>');
			}
			$sitecontent->add_site_content('</select>');
			$sitecontent->add_site_content('<small>Ctrl. oder cmd für Mehrfachauswahl.</small>');
				
		//	Add-ons
		$sitecontent->add_site_content('</div><div id="addon">');
			$art = 'addon';
			$sitecontent->add_site_content('<h3>Add-ons</h3>');
			$sitecontent->add_site_content('Stellen Sie den Zugriff des Users auf Add-ons des CMS ein.<br />');
			$sitecontent->add_site_content('<small>Die Einstellungen gelten für Add-ons Nutzung sowie Konfiguration, wobei hier auch der Seitenzuriff oben zum tragen kommt!</small><br />');
			//allgemein (verbieten/ erlauben)
			$sitecontent->add_site_content('<input type="radio" name="addon_allg" value="erlauben" '.( ( $untergrdata[$art][0] == 'erlauben' ) ? 'checked="checked"' : '' ).'>Ausgewählte Add-ons erlauben<br />');
			$sitecontent->add_site_content('<input type="radio" name="addon_allg" value="verbieten"  '.( ( $untergrdata[$art][0] == 'verbieten' ) ? 'checked="checked"' : '' ).'>Ausgewählte Add-ons verbieten<br />');
			$sitecontent->add_site_content('<br />');
			//Add-onliste
			$sitecontent->add_site_content('<select multiple="multiple" name="addon_list[]">');
			//	alle Add-ons lesen
			//		Name und NameID
			$adds = listaddons( true );
			foreach ($adds as $add) {
				
				//aktivert?
				$selected = ( in_array( $add[1], $untergrdata[$art][1] )  ? 'selected="selected"' : '' );
				
				$sitecontent->add_site_content('<option value="'.$add[1].'" '.$selected.'>'.$add[0].'</option>');
			}
			$sitecontent->add_site_content('</select>');
			$sitecontent->add_site_content('<small>Ctrl. oder cmd für Mehrfachauswahl.</small>');
		
		//	Menüs
		$sitecontent->add_site_content('</div><div id="menues">');
			$art = 'menues';
			$sitecontent->add_site_content('<h3>Menüs</h3>');
			$sitecontent->add_site_content('Sie können nicht jeden Menüpunkt einzeln einstellen, sondern nur ganze Menüs!<br />');
			$sitecontent->add_site_content('<small></small><br />');
			//allgemein (verbieten/ erlauben)
			$sitecontent->add_site_content('<input type="radio" name="menues_allg" value="erlauben" '.( ( $untergrdata[$art][0] == 'erlauben' ) ? 'checked="checked"' : '' ).'>Ausgewählte Menüs erlauben<br />');
			$sitecontent->add_site_content('<input type="radio" name="menues_allg" value="verbieten" '.( ( $untergrdata[$art][0] == 'verbieten' ) ? 'checked="checked"' : '' ).'>Ausgewählte Menüs verbieten<br />');
			$sitecontent->add_site_content('<br />');
			//Add-onliste
			$sitecontent->add_site_content('<select multiple="multiple" name="menues_list[]">');
			//	first immer adden
			$sitecontent->add_site_content('<option value="0" '.( in_array( 0, $untergrdata[$art][1] )  ? 'selected="selected"' : '' ).'>Hauptmenü (0)</option>');
			//	Liste mit Name
			$namesfile = new KIMBdbf( 'menue/menue_names.kimb' );
			//Alle Dateiene lesen
			$mens = scan_kimb_dir( 'url/' );
			foreach ($mens as $men) {
				//nur passende Dateiene nehmen
				if( preg_match( '/nextid_([0-9]*).kimb/', $men ) ){
					
					//dateiID
					$meid = preg_replace( '/[^0-9]/', '', $men );
					
					//Namen herausfinden
					$file = new KIMBdbf( 'url/' .$men );
					$id = $file->read_kimb_one( '1-requestid' );
					$name = $namesfile->read_kimb_one( $id );
					
					//aktivert?
					$selected = ( in_array( $meid, $untergrdata[$art][1] )  ? 'selected="selected"' : '' );
					
					//Auswahlmöglichkeit
					$sitecontent->add_site_content('<option value="'.$meid.'" '.$selected.'>'.$name.' ('.$meid.')</option>');
				}
			}
			$sitecontent->add_site_content('</select>');
			$sitecontent->add_site_content('<small>Ctrl. oder cmd für Mehrfachauswahl.</small>');

		$sitecontent->add_site_content('</div></div>');

  		$sitecontent->add_site_content('<small>Diese Funktion macht nur Sinn sofern der User überhaupt Zugriff auf die entsprechende Backendseite hat!</small><br />');
		$sitecontent->add_site_content('<small>Sie können jeweils wählen ob nur bestimmte gesperrt werden sollen und alle anderen erlaubt oder ob nur bestimmte erlaubt und alle anderen gesperrt sein sollen!</small>');
		$sitecontent->add_site_content('<small>Neue erstellte Seiten/ Add-ons/ Menüs fallen immer in die Gruppe "alle andere"!</small><br />');

		$sitecontent->add_site_content('<p><input type="submit" value="Änderungen Speichern" ></p>');
		$sitecontent->add_site_content('</form>');
	}
	else{
		//wenn Level nicht gefunden -> Fehler
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}

}
//Level löschen?
elseif( $_GET['todo'] == 'del' && isset( $_GET['level'] ) ){
	
	//Link zur Liste aller Level
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php">&larr; Alle Level</a><br /><br />');
	
	//Namen säubern
	$_GET['level'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_GET['level'] ) );
	//alle Level lesen
	$alllev = $levellist->read_kimb_one( 'levellist' );
	//Array mit allen Leveln machen
	$alllev = explode( ',' , $alllev );

	//existiert das Level?
	if( in_array( $_GET['level'] , $alllev ) ){
		
		//Level löschen
		$levellist->write_kimb_delete( $_GET['level'] );
		
		//Untergruppenwahlen löschen
		foreach( backend_untergrlevelarten() as $art ){
			//Tags löschen
			$levellist->write_kimb_delete( $_GET['level'].'_'.$art.'_allg' );
			$levellist->write_kimb_delete( $_GET['level'].'_'.$art.'_list' );
		}

		//Liste mit allen Leveln anpassen
		
		//neuen Sting erstellen (ohne gelöschtes Level)
		foreach( $alllev as $lev ){
			if( $lev != $_GET['level'] ){
				$newlev .= ','.$lev;
			}
		}
		$newlev = substr( $newlev , '1' );

		//sind jetzt überhaupt noch Level vorhanden?
		if( !empty( $newlev ) ){
			//Sting mit allen Leveln neu schreiben
			$levellist->write_kimb_replace( 'levellist' , $newlev );
		}
		else{
			//keine Level mehr
			//Sting mit allen Leveln komplett löschen
			$levellist->write_kimb_delete( 'levellist' );
		}

		//Übersicht aufrufen
		open_url( '/kimb-cms-backend/other_level.php' );
		die;
	}
	else{
		//Fehlermeldung
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
//nichts besonderes gewünscht
else{
	//Übersicht über alle Level
	$sitecontent->add_site_content('<h1>Userlevel Backend Liste</h1>');

	//JavaScript für Löschen Dialog
	$sitecontent->add_html_header('<script>
	var del = function( level ) {
		$( "#del-level" ).show( "fast" );
		$( "#del-level" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=del&level=" + level;
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

	//alle Level lesen
	$levs = $levellist->read_kimb_one( 'levellist' );
	//Array mit allen Leveln machen
	$levs = explode( ',' , $levs );

	//Tabelle beginnen
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=new"><span class="ui-icon ui-icon-plusthick" title="Eine neues Level erstellen." style="display:inline-block;" ></span></a>');
	$sitecontent->add_site_content('<table width="100%"><tr> <th>Levelname</th> <th>Rechte <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Die englischen Zahlen stehen für die erlaubten Menuepunkte!"></span></th> <th>Löschen</th> </tr>');
	//feste Level (more&less)
	$sitecontent->add_site_content('<tr> <td title="Voreingestellt, nicht zu verändern" >more</td> <td><i>Alle Rechte.</i></td> <td></td> </tr>');
	$sitecontent->add_site_content('<tr> <td title="Voreingestellt, nicht zu verändern" >less</td> <td><i>Rechte die ein Editor benötigt.</i></td> <td></td> </tr>');

	//alle anderen Level aus Array durchgehen
	foreach( $levs as $lev ){
		//Level lesen
		$read = $levellist->read_kimb_one( $lev );
		//wenn Level Rechte hat ausgeben
		if( !empty( $read ) ){
			//Button zum  löschen erstellen
			$del = '<span onclick="var delet = del( \''.$lev.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Level löschen." style="display:inline-block;" ></span></span>';
			//Tabellenzeile
			$sitecontent->add_site_content('<tr> <td><a title="Ändern" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=edit&amp;level='.$lev.'">'.$lev.'</a></td> <td>'.substr( $read , '0' , '50' ).' (...)</td> <td>'.$del.'</td> </tr>');
		}
	}
	$sitecontent->add_site_content('</table>');

	//HTML-Code für löschen Dialog
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-level" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie das Userlevel wirklich löschen?</p></div></div>');
}

//Hinweis wie man User einem Level zuordnet
$sitecontent->add_site_content('<br /><br /><span class="ui-icon ui-icon-info" title="Die User können Sie unter &apos;User&apos; -> &apos;Auflisten&apos; -> &apos;Name (User bearbeiten)&apos; den Gruppen zuordnen! Das Zuordnen ist nur für User der Gruppe Admin (&apos;more&apos;) möglich!"></span><br />');
//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();

//Commands done :D
?>
