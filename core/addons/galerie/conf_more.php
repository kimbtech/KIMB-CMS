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

//URL für einfachere Aufrufe der Add-on Konfiguration
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie';

//Nach PHP-GD suchen
if ( !extension_loaded( 'gd' ) || !function_exists( 'gd_info' ) ) {
	//Fehlermeldung wenn nicht da
	$sitecontent->echo_error( '<b>Auf Ihrem Server fehlt PHP GD!</b><br />Dies ist notwendig für das Add-on Galerie!' );
}

//Konfigurationsdatei laden
$cfile = new KIMBdbf( 'addon/galerie__conf.kimb' );

//beim Filemanager das nicht gesicherte Verziechnis wählen (wenn man auf den Link zum Filemanager klickt, soll keine Frage (sicher,offen) mehr kommen)
$_SESSION['secured'] = 'off';

//ist eine ID gegeben (ein Galerie gewählt)
if( is_numeric( $_GET['id'] ) ){
	//Link zur Übersicht über alle Galerien
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Übersicht</a>');
	
	//Überprüfung ob ID vorhanden
	$path = $cfile->read_kimb_id( $_GET['id'] , 'imgpath' );
	if( !empty( $path ) ){	

		//Überschrift
		$sitecontent->add_site_content('<h3>Bearbeiten</h3>');
	
		//JavaScript für Löschen Dialog
		$sitecontent->add_html_header('<script>
			function del() {
				$( "#del_addon_galerie" ).show( "fast" );
				$( "#del_addon_galerie" ).dialog({
				resizable: false,
				height:200,
				modal: true,
				buttons: {
					"Delete": function() {
						$( this ).dialog( "close" );
						window.location = "'.$addonurl.'&id='.$_GET['id'].'&del";
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
	
	
		//wenn Daten gesendet werden ist immer der imgpath gesetzt 
		if( isset( $_POST['imgpath'] ) ){
			
			//ID extrahieren
			$id = $_GET['id'];
		
			//Namen aller übergebenen Werte
			$alls = array( 'siteid', 'rand', 'size', 'anz', 'pos' );
		
			//alle Übergaben durchgehen
			foreach( $alls as $all ){
				//aktueller Wert in dbf anders als Übergabe?
				if( $cfile->read_kimb_id( $id , $all ) != $_POST[$all] ){
					//in dbf ändern
					$cfile->write_kimb_id( $id , 'add' , $all , $_POST[$all] );
					//Medlung
					$sitecontent->echo_message( 'Der Parameter "'.$all.'" wurde geändert!' );
				}
			}
		}
		//Galerie löschen?
		elseif( isset( $_GET['del'] ) ){
			
			//aus dbf löschen
			//	Einstellungen
			$cfile->write_kimb_id( $_GET['id'] , 'del' );
			//	Verzeichnis aller
			$cfile->write_kimb_teilpl( 'galid' , $_GET['id'] , 'del' );
	
			//auf die Übersicht weiterleiten
			open_url( '/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie' );
			die;
		}
	
		//erstmal alle Daten der Galerie lesen
		$all = $cfile->read_kimb_id( $_GET['id'] );
	
		//Formular
		$sitecontent->add_site_content('<form method="post" action="'.$addonurl.'&amp;id='.$_GET['id'].'">');
	
		//Seitenauswahl
		//	JavaScript aktuelle Auswahl im Dropdown wählen
		$sitecontent->add_html_header('<script>$(function(){ $( "[name=siteid]" ).val( '.$all['siteid'].' ); }); </script>');
		//	Dropdown
		$sitecontent->add_site_content( id_dropdown( 'siteid', 'siteid' ).' (SiteID <b title="Bitte wählen Sie die Seite, auf welcher die Galerie angezeigt werden soll.">*</b>)<br />');
	
		//Pfad im Filemanager und Link zum Filemanager
		$sitecontent->add_site_content('<input type="text" value="'.$all['imgpath'].'" name="imgpath" readonly="readonly"> (Bildpfad <b title="Bitte wählen Sie einen Pfad unter Other &rarr; Filemanager. Dieser lässt sich später nicht ändern! (Bitte laden Sie mit dem Filmanager alle ihre Dateien in Ordner mit diesem Pfad.)">*</b>)');
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?action=rein&amp;path='.$all['imgpath'].'" target="_blank" >Zum Filemanager</a><br />');
	
		//checked für Zufall machen
		if( $all['rand'] == 'on' ){
			$rand[1] = ' ';
			$rand[2] = ' checked="checked"';
		}
		else{
			$rand[2] = ' ';
			$rand[1] = ' checked="checked"';
		}
		//Radio Buttons Zufall
		$sitecontent->add_site_content('<input type="radio" name="rand" value="off"'.$rand[1].'> <span style="display:inline-block;" title="Bilderreihenfolge nicht zufällig." class="ui-icon ui-icon-closethick"></span>');
		$sitecontent->add_site_content('<input type="radio" name="rand" value="on"'.$rand[2].'> <span style="display:inline-block;" title="Bilderreihenfolge zufällig." class="ui-icon ui-icon-check"></span> (Zufall)<br />');
	
		//Größe der Vorschau
		$sitecontent->add_site_content('<input type="number" name="size" value="'.$all['size'].'"> (Größe <b title="Bitte geben Sie eine maximale Pixelzahl für die Vorschau ein.">*</b>)<br />');
	
		//Anzahl der Bilder
		$sitecontent->add_site_content('<input type="number" name="anz" value="'.$all['anz'].'"> (Anzahl <b title="Bitte geben Sie eine maximale Anzahl an Bilder ein. (Leer => 99999)">*</b>)<br />');
	
		//Position
		//	JavaScript aktuelle Auswahl im Dropdown wählen
		$sitecontent->add_html_header('<script>$(function(){ $( "[name=pos]" ).val( \''.$all['pos'].'\' ); }); </script>');
		//	Dropdown
		$sitecontent->add_site_content('<select name="pos"><option value="top">Oben</option><option value="bottom">Unten</option><option value="none">Manuell</option></select>(Platzierung <b title="Soll die Galerie oben oder unten auf der Seite sein oder platzieren Sie die Galerie per HTML-Code (siehe unten).">*</b>)<br />');
	
		//sumbit Button
		$sitecontent->add_site_content('<input type="submit" value="Anpassen"></form>');
		//Mülleimer zum Löschen
		$sitecontent->add_site_content('<span onclick="del();"><span class="ui-icon ui-icon-trash" style="display: inline-block;" title="Diese Galerie löschen."></span></span>');
	
		//HTML-Code für manuelle Platzierung
		$sitecontent->add_site_content( '<hr /><b>HTML Code für manuelle Platzierung:</b><br /><i>'.htmlspecialchars( '<div class="imggallerydisplayhere" style="background-color:#ddd; border-radius:15px;" >Bitte aktivieren Sie für die Bildergalerie JavaScript!</div>' ).'</i>' );
		//HTML-Code für Löschen Dialog
		$sitecontent->add_site_content('<div style="display:none;"><div id="del_addon_galerie" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Galerie wirklich löschen?</p></div></div>');
	}
	else{
		//Fehlermeldung wenn Galerie nicht da
		$sitecontent->echo_error( 'Galerie nicht gefunden!' );
	}
}
//Path übergeben (Link vom Filemanager (neue Galerie)
elseif( !empty( $_GET['path'] ) ){

	//neue ID vergeben
	$id = $cfile->next_kimb_id();
	//der Liste der IDs hinzufügen
	$cfile->write_kimb_teilpl( 'galid' , $id , 'add' );

	//Vorgabewerte für die Galerie schreiben
	$cfile->write_kimb_id( $id , 'add' , 'siteid' , '0' );
	$cfile->write_kimb_id( $id , 'add' , 'imgpath' , $_GET['path'] );
	$cfile->write_kimb_id( $id , 'add' , 'rand' , 'off' );
	$cfile->write_kimb_id( $id , 'add' , 'size' , '200' );
	$cfile->write_kimb_id( $id , 'add' , 'anz' , '5' );
	$cfile->write_kimb_id( $id , 'add' , 'pos' , 'top' );

	//zu den Einstellungen der Galerie weiterleiten
	open_url( '/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie&id='.$id );
	die;
}
else{
	//Liste aller Galerien
	$sitecontent->add_site_content('<h3>Liste</h3>');
	//Info
	$sitecontent->add_site_content( '<span class="ui-icon ui-icon-info" title="Hier können Sie Einstellungen Ihrer Bildergalerien bearbeiten. In der Liste werden die SiteIDs (Seiten -> Auflisten) angezeigt."></span>' );
	//Tabelle Beginn
	$sitecontent->add_site_content( '<table width="100%">' );
	$sitecontent->add_site_content( '<tr> <th>SiteID</th> <th>Bildpfad</th> <th>Position</th> </tr>' );

	//alle Galerien durchgehen
	foreach( $cfile->read_kimb_all_teilpl( 'galid' ) as $id ){
		//Infos lesen
		$all = $cfile->read_kimb_id( $id );
		//Tabellenzeile hinzufügen
		$sitecontent->add_site_content('<tr> <td><a href="'.$addonurl.'&amp;id='.$id.'" title="Bearbeiten">'.$all['siteid'].'</a></td> <td>'.$all['imgpath'].'</td> <td>'.$all['pos'].'</td> <tr>');

		$liste = 'yes';
	}
	$sitecontent->add_site_content( '</table>' );

	//wenn keine Galerien Hinweis
	//immer Hinweis wie man neue Galerien hinzufügt
	if( $liste != 'yes' ){
		$sitecontent->echo_error( 'Keine Galerieseiten!!<br />Bitte fügen Sie Galerien über den Filemanager hinzu.' );
	}
	else{
		$sitecontent->add_site_content( '<br /><i>Weitere Galerien können Sie über den Filemanager hinzufügen.</i>' );
	}
}

?>
