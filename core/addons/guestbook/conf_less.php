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

//URL zur Konf less
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=guestbook';

//Konfigurationsdatei des Add-ons
$guestfile = new KIMBdbf( 'addon/guestbook__conf.kimb' );

//Tabellendesign
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//Ist das Gästebuch einer Seite zum Bearbeiten gewählt?
if( isset( $_GET['edit'] ) && is_numeric( $_GET['id'] ) ){

	//Ist die Datei für das gewählte Gästebuch vorhanden?
	if( check_for_kimb_file( 'addon/guestbook__id_'.$_GET['id'].'.kimb' ) ){
		//Alle Einträge auflisten
		
		//Überschrift
		$sitecontent->add_site_content('<h2>Gästebuch der Seite "'.$_GET['id'].'"</h2>');
		//Link zur Seitenauswahl
		$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Zurück zur Übersicht</a><br /><br />');
		//CSS Design für Gästebuchbeiträge
		$sitecontent->add_html_header('<style>div#guestinfo{ position:relative; border-top:solid 1px #000000; font-weight:bold; text-align:center; }
span#guestlinks{ font-weight:normal; position:absolute; left:0px;}
span#guestrechts{ font-weight:normal; position:absolute; right:0px;}
div#guestname{ position:relative; border-bottom:solid 1px #000000; font-weight:bold; }
span#guestdate{ font-weight:normal; position:absolute; right:0px; }
div#guest, div.answer{ border:solid 1px #000000; border-radius:15px; background-color:#dddddd; padding:10px; margin:5px;}
div.answer{ margin-left:80px; }
</style>');

		//Bearbeitung von Einträgen
		//	löschen und Status ändern
		
		//Ist es eine Antwort?
		//	Bei Antworten wird der Wert GET[answer] mit der ID der Antwort in der dbf gesetzt,
		//	die GET[bid] enthält die ID in der dbf des Beitrages auf den geantwortet wurde
		if( is_numeric( $_GET['answer'] ) && is_numeric( $_GET['bid'] ) ){
			//Antwort!
			
			//Datei mit Antwort lesen
			$gsitefile = new KIMBdbf( 'addon/guestbook__id_'.$_GET['id'].'_answer_'.$_GET['bid'].'.kimb' );
			//Datei der Seite lesen (Beiträge auf die geantwortet wird)
			$gsitefile_parent = new KIMBdbf( 'addon/guestbook__id_'.$_GET['id'].'.kimb' );
			
			//gegebene bid für löschen sichern
			$bid_old = $_GET['bid'];
			//bid auf die ID der answer setzten (nötig für Status ändern)
			$_GET['bid'] = $_GET['answer'];
		}
		else{
			//keine Antwort, also normal die Datei der Seite lesen
			$gsitefile = new KIMBdbf( 'addon/guestbook__id_'.$_GET['id'].'.kimb' );
		}

		//Status ändern
		//	GET[bid] enthält ID in der dbf $gsitefile wessen Status geändert werden soll 
		if( isset( $_GET['deakch'] ) && is_numeric( $_GET['bid'] ) ){
			
			//aktuellen Status lesen
			$status = $gsitefile->read_kimb_id( $_GET['bid'] , 'status' );

			//wenn an, dann auf aus setzen
			if( $status == 'on' ){
				$status = 'off';
				//okay
				$ok = 'ok';
			}
			//wenn aus, dann auf an setzen
			elseif( $status == 'off' ){
				$status = 'on';
				//okay
				$ok = 'ok';
			}

			//wenn okay
			if( $ok == 'ok' ){
				//Änderung durchführen			
				if( $gsitefile->write_kimb_id( $_GET['bid'] , 'add' , 'status' , $status ) ){
					//Medlung wenn erfolgreich
					$sitecontent->echo_message( 'Status eines Beitrages geändert!' );
				}
			}

		}
		//Antwort löschen
		//	GET[answer] enthält ID der zu löschenden Antwort in der dbf $getsitefile
		//	$bid_old enthält ID in der dbf $gsitefile_parent des Beitrages auf den geantwortet wurde
		elseif( isset( $_GET['del'] )  && isset($_GET['answer']) ){
			
			//Der Zeitpunkt ist nie leer, diesen lesen um zu prüfen ob Antwort vorhanden
			$time = $gsitefile->read_kimb_id( $_GET['answer'], 'time' );
			
			if( !empty( $time ) ){
				//wenn Anwtort vorhanden
				
				//gesamte Antwort löschen
				$gsitefile->write_kimb_id( $_GET['answer'] , 'del');
				
				//Ist der erste Wert der Liste aller Antworten leer?
				if( empty( $gsitefile->read_kimb_one( 'allidslist1' ) ) ){
					//das heißt, keine Antworten mehr
					
					//Datei mit Antworten löschen
					$gsitefile->delete_kimb_file();
					
					//beim Beitrag auf den geantwortet wurde eintragen, dass keine Antworten vorhanden sind
					$gsitefile_parent->write_kimb_id( $bid_old , 'add' , 'antwo' , 'no' );
				}
				
				//Variable für Datei löschen (sonst wird beim auflisten der Antworten eine alte Version genutzt)
				unset( $gsitefile );
			}
		}
		//Löschen eines normalen Beitrages
		elseif( isset( $_GET['del'] ) && is_numeric( $_GET['bid'] ) ){

			//Der Zeitpunkt ist nie leer, diesen lesen um zu prüfen ob Beitrag vorhanden
			$time = $gsitefile->read_kimb_id( $_GET['bid'] , 'time' );

			if( !empty( $time ) ){
				//wenn Beitrag vorhanden

				//alle IDs lesen
				//Array aus String erstellen
				$allids = explode( ',' , $gsitefile->read_kimb_one( 'idlist' ) );
				//IDs durchgehen
				foreach( $allids as $id ){
					//alle IDs wieder aufnehmen, außer die, die gelöscht werden sollen
					if( $id != $_GET['bid'] ){
						//Liste neu erstellen
						$newidlist .= $id.',';
					}
				}
				//am Ende das Komma entfernen
				$newidlist = substr( $newidlist , 0 , -1 );

				//hat der zu löschende Beitrag Antworten?
				if( $gsitefile->read_kimb_id( $_GET['bid'] , 'antwo' ) == 'yes'){
					//wenn ja, dann Datei mt Antworten löschen
					delete_kimb_datei( 'addon/guestbook__id_'.$_GET['id'].'_answer_'.$_GET['bid'].'.kimb');
				}

				//Ist die neue Liste der IDs leer?
				if( empty( $newidlist ) ){
					//wenn ja, Beitrag löschen und ID Liste löschen
					if( $gsitefile->write_kimb_id( $_GET['bid'] , 'del') && $gsitefile->write_kimb_delete( 'idlist' ) ){
						//Medlung
						$sitecontent->echo_message( 'Letzter Beitrag gelöscht!' );
					}
				}
				//wenn nicht,
				//	Beitrag löschen und ID Liste neu schreiben
				elseif( $gsitefile->write_kimb_id( $_GET['bid'] , 'del') && $gsitefile->write_kimb_replace( 'idlist' , $newidlist ) ){
					//Medlung
					$sitecontent->echo_message( 'Beitrag gelöscht!' );
				}
			}


		}
		
		//Ist die Eltern Guestbook dbf geladen?
		if( is_object( $gsitefile_parent )){
			//jetzt wird wieder die normale Guestbook dbf benötigt, anstatt der mit Antworten
			$gsitefile = $gsitefile_parent;
		}

		//Einträge ausgeben
		
		//ID Liste lesen
		$idlist = $gsitefile->read_kimb_one( 'idlist' );

		//wenn leer -> Fehler
		if( !empty( $idlist ) ){
			//ID Liste in Array
			$ids = explode( ',' , $idlist );
			$i = 0;
			//durchgehen
			foreach( $ids as $id ){
				//gleich die Werte jeder ID lesen
				$alles[] = $gsitefile->read_kimb_id( $id );
				//und auch ID speichern
				$alles[$i]['id'] = $id;
				$i++;
			}

			//alle Beiträge ausgeben (Array von oben)
			foreach( $alles as $einer ){
				
				//Je nach Status ein X oder ein V anzeigen (bei Click Status ändern)
				if ( $einer['status'] == 'off' ){
					$status = '<a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;deakch"><span style="display:inline-block;" class="ui-icon ui-icon-close" title="Dieser Beitrag ist zur Zeit nicht sichtbar. (click -> ändern)"></span></a>';
				}
				else{
					$status = '<a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;deakch"><span style="display:inline-block;" class="ui-icon ui-icon-check" title="Dieser Beitrag ist zur Zeit sichtbar. (click -> ändern)"></span></a>';
				}
				//Mülltonne für löschen
				$status .= '<span id="bid'.$einer['id'].'" style="display:none; margin-left:20px;" ><a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;del"><span style="display:inline-block;" class="ui-icon ui-icon-trash" title="Diesen Beitrag löschen! (erneut clicken)"></span></a></span>';
				$status .= '<span onclick=" $(\'span#bid'.$einer['id'].'\').css( \'display\' , \'inline-block\' ); $( this ).css( \'display\' , \'none\' ); " style="display:inline-block;" class="ui-icon ui-icon-trash" title="Diesen Beitrag löschen! (zweimal clicken)"></span>';

				//Ausgabe des Beitrages
				//	Beginn
				$sitecontent->add_site_content( '<div id="guest" >' );
				//	obere Zeile
				//		Name		
				$sitecontent->add_site_content( '<div id="guestname" ><span title="Name des User" >'.$einer['name'].'</span>' );
				//		Zeit
				$sitecontent->add_site_content( '<span id="guestdate" title="Tag und Zeit des Erstellens">'.date( 'd-m-Y H:i:s' , $einer['time'] ).'</span>' );
				$sitecontent->add_site_content( '</div>' );
				//	Hauptteil
				//		Inhalt
				$sitecontent->add_site_content( $einer['cont'] );
				//	untere Zeile
				//		IP
				$sitecontent->add_site_content( '<div id="guestinfo" >');
				$sitecontent->add_site_content( '<span title="IP des Users (0.0.0.0 wenn Speicherung aus)" id="guestlinks">'.$einer['ip'].'</span>' );
				//		Mülltonne und Status
				$sitecontent->add_site_content( $status );
				//		E-Mail-Adresse
				$sitecontent->add_site_content( '<span title="E-Mail-Adresse des Users" id="guestrechts">'.$einer['mail'].'</span>' );
				$sitecontent->add_site_content( '</div>' );
				$sitecontent->add_site_content( '</div>' );
				
				//gibt es Antworten?
				if( $einer['antwo'] == 'yes' ){
					
					//wenn ja, ausgeben
					
					//eingerückt
					$sitecontent->add_site_content( '<div class="answer" >' );
					
					//dbf mit Antworten laden
					$readfile = new KIMBdbf( 'addon/guestbook__id_'.$_GET['id'].'_answer_'.$einer['id'].'.kimb' );
					
					//jede ID nacheinander durchgehen
					foreach( $readfile->read_kimb_all_teilpl('allidslist') as $id ){
						
						//Daten der Antwort lesen
						$eintr = $readfile->read_kimb_id( $id );
						
						//Je nach Status ein X oder ein V anzeigen (bei Click Status ändern)
						if ( $eintr['status'] == 'off' ){
							$status = '<a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;deakch&amp;answer='.$id.'"><span style="display:inline-block;" class="ui-icon ui-icon-close" title="Dieser Beitrag ist zur Zeit nicht sichtbar. (click -> ändern)"></span></a>';
						}
						else{
							$status = '<a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;deakch&amp;answer='.$id.'"><span style="display:inline-block;" class="ui-icon ui-icon-check" title="Dieser Beitrag ist zur Zeit sichtbar. (click -> ändern)"></span></a>';
						}
						
						//Mülltonne für löschen
						$status .= '<span id="bid'.$einer['id'].'_'.$id.'" style="display:none; margin-left:20px;" ><a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;del&amp;answer='.$id.'"><span style="display:inline-block;" class="ui-icon ui-icon-trash" title="Diesen Beitrag löschen! (erneut clicken)"></span></a></span>';
						$status .= '<span onclick=" $(\'span#bid'.$einer['id'].'_'.$id.'\').css( \'display\' , \'inline-block\' ); $( this ).css( \'display\' , \'none\' ); " style="display:inline-block;" class="ui-icon ui-icon-trash" title="Diesen Beitrag löschen! (zweimal clicken)"></span>';
						
						//Ausgaben (wie normnalen Beitrag)
						$sitecontent->add_site_content( '<div id="guest" >');		
						$sitecontent->add_site_content( '<div id="guestname" title="Name des User" >'.$eintr['name'] );
						$sitecontent->add_site_content( '<span id="guestdate" title="Tag und Zeit des Erstellens" >'.date( 'd-m-Y H:i:s' , $eintr['time'] ).'</span>' );
						$sitecontent->add_site_content( '</div>' );
						$sitecontent->add_site_content( $eintr['cont'] );
						$sitecontent->add_site_content( '<div id="guestinfo" >');
						$sitecontent->add_site_content( '<span title="IP des Users (0.0.0.0 wenn Speicherung aus)" id="guestlinks">'.$eintr['ip'].'</span>' );
						$sitecontent->add_site_content( $status );
						$sitecontent->add_site_content( '<span title="E-Mail Adresse des Users" id="guestrechts">'.$eintr['mail'].'</span>' );
						$sitecontent->add_site_content( '</div>' );
						$sitecontent->add_site_content( '</div>' );

					}
				
					//Antworten Ende, einrücken Ende
					$sitecontent->add_site_content( '</div>' );

				}
			}

			//keine Liste mit allen Gästebuchseiten zeigen
			$list = 'no';
		}
		else{
			//Fehler wenn Gästebuch leer
			$sitecontent->echo_error( 'Das Gästebuch ist leer!');
			//Liste mit allen Gästebuchseiten zeigen
			$list = 'yes';
		}
	}
	else{
		//Fehler wenn Gästebuch leer oder ID nicht gefunden
		$sitecontent->echo_error( 'Die gewünschte Seite hat kein Gästebuch oder es ist leer!' , 'unknown');
		//Liste mit allen Gästebuchseiten zeigen
		$list = 'yes';
	}
}
else{
	//Liste mit allen Gästebuchseiten zeigen
	$list = 'yes';
}

//Soll Liste mit allen Gästebuchseiten angezeigt werden?
if( $list == 'yes' ){
	//Überschrift
	$sitecontent->add_site_content('<h2>Seiten mit Gästebuch</h2>');

	//Info und Tabellenbeginn
	$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Hier können Sie die Beiträge verwalten. Weiters finden Sie unter Konfiguration (oben rechts)."></span>');
	$sitecontent->add_site_content('<table width="100%"><tr><th>SiteID</th></tr>');

	//alle SiteIDs auflisten
	foreach( $guestfile->read_kimb_all_teilpl( 'siteid' ) as $id ){

		//Tabellenzeile
		$sitecontent->add_site_content('<tr><td><a href="'.$addonurl.'&amp;id='.$id.'&amp;edit">'.$id.'</a></td></tr>');
		//Liste nicht leer
		$gefunden = 'yes';
	}

	//Tabelle beenden
	$sitecontent->add_site_content('</table>');

	//wenn Liste leer
	if( $gefunden != 'yes' ){
		//Meldung		
		$sitecontent->echo_error( 'Es wurden keine Gästebuchseiten gefunden!' , 'unknown' );
	}
}

?>