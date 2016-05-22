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

//Diese Datei stellt unter Other den Teil "Umzug" bereit
//	Mit Hilfe dieser Funktion kann man Aufrufe des CMS auf eine bestimmte Seite lenken.
//	Nach einem Umbau einer Seite gibt es fast immer viele alte Links, diese können hier
//	direkt auf ihre neue Seite gelenkt werden.
//		Auch Kurz-URLs lassen sich hier einfach erstellen 

//Rechte prüfen
check_backend_login( 'twenty' , 'more');

//CSS der Tabelle
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//Datei mit den URL-Daten laden
$oldurlfile = new KIMBdbf( 'menue/oldurl.kimb' );

//neue Weiterleitung einrichten?
if( $_GET['todo'] == 'add' && !empty( $_POST['url'] ) && !empty( $_POST['id'] ) ){

	//freie ID suchen
	$newid = $oldurlfile->next_kimb_id();

	//die URL muss einen / haben 
	//	wenn nicht vorhanden hinzufügen
	if( substr( $_POST['url'], 0, 1 ) != '/' ){
		$_POST['url'] = '/'.$_POST['url'];
	}
	
	//Hinten auch einen / anfügen
	//	wenn nicht schon geschehen
	if( substr( $_POST['url'], -1, 1 ) != '/' ){
		$_POST['url'] = $_POST['url'].'/';
	}

	//Seite auf die Weitergeleitet werden soll spiechern
	$oldurlfile->write_kimb_id( $newid, 'add', 'id', $_POST['id'] );
	//die URL speichern
	$oldurlfile->write_kimb_id( $newid, 'add', 'url', $_POST['url'] );

}
//eine Weiterleitung löschen?
//	ID sollte nummerisch sein!
elseif( $_GET['todo'] == 'del' && is_numeric( $_GET['id'] ) && !empty( $_GET['id'] ) ){

	//entsprechende ID aus Datei löschen
	if( $oldurlfile->write_kimb_id( $_GET['id'], 'del' ) ){
		//wenn OK, Meldung
		$sitecontent->echo_message( 'Die Weiterleitung wurde gelöscht!' );
	}
}
elseif( !empty( $_GET['todo'] ) ){
	//soll was machen, aber die Eingaben stimmen nicht
	//Hinweis
	$sitecontent->echo_error( 'Bitte füllen Sie alle Felder!' );
}

//JavaScript für Löschen Dialog
$sitecontent->add_html_header('<script>
	function del( id ) {
		$( "#delfwd" ).show( "fast" );
		$( "#delfwd" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_umzug.php?todo=del&id=" + id;
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

//Hinweistexte
$sitecontent->add_site_content('<h2>Umzug <span class="ui-icon ui-icon-info" title="Hier können Sie Links einer alten Homepage unter dieser Domain auf eine neue Seite des CMS umlenken!" style="display:inline-block;"></span></h2>');
$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Geben Sie die alte URL ( www.domain.de <</URL>> ) an und wählen Sie eine CMS Seite, dies ist nützlich wenn z.B. auf Google noch alte Links zu finden sind!" style="display:inline-block;"></span><br /><br />');

//Formular
$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_umzug.php?todo=add" method="post">');
$sitecontent->add_site_content('<table width="100%"><tr> <th>URL</th> <th>RequestID</th> <th>Löschen</th> </tr>');

//alle Weiterleitung listen
foreach( $oldurlfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){

	//Infos der aktuellen Weiterleitung lesen
	$pars = $oldurlfile->read_kimb_id( $id );

	//Tabellenzeile
	$sitecontent->add_site_content('<tr> <td>'.$pars['url'].'</td> <td>'.$pars['id'].'</td> <td><span onclick="del( '.$id.' );"><span class="ui-icon ui-icon-trash" title="Diese Weiterleitung löschen." style="display:inline-block;" ></span></span></td> </tr>');
}

//Weiterleitung hintzufügen (mit Dropdown zur Seitenauswahl)
$sitecontent->add_site_content('<tr> <td><input type="text" name="url" placeholder="/Kontakt-1-/"></td> <td>'.id_dropdown( 'id', 'requid' ).'</td> <td><input type="submit" value="Hinzufügen"></td> </tr>');

$sitecontent->add_site_content('</table></form>');

//HTML für Löschen Dialog
$sitecontent->add_site_content('<div style="display:none;"><div id="delfwd" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie die Weiterleitung wirklich löschen?</p></div></div>');

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
