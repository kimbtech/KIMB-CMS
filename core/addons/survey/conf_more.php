<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2016 by KIMB-technologies
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
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//Konfiguration laden und Handhabung vereinfachen
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=survey';

//Liste Button
$sitecontent->add_site_content( '<p id="survey_be_back"><a href="'.$addonurl.'">&larr; Zurück zur Liste</a></p>' );

//Systemkonfigurationsdatei
//	TeilerPlus Werte unter dem Teiler "uid"
//	sind die SeitenID der Seiten mit Umfragen.
$sysfile = new KIMBdbf( 'addon/survey__conf.kimb' );

//Konfigurationswerte für jede Umfrage
$uconfs = array(
	//allg
	array(
		'zugriff' => array( 'fe', 'li', 'oe' ),
		'auswer' => array( 'an', 'na' ),
		'zugaus' => array( 'ad', 'oe' ),
		'inform' => array( "## Umfrage 7 \r\n\r\nDiese Umfrage soll uns dabei helfen XXX zu verstehen. \r\n\r\n**Ihre Daten werden natürlich anonym ausgewertet!**" )
	),
	//Fragen
	array(
		//Fragentypen
		'type' => array(
			'au' => 'Auswahl',
			'mc' => 'Multiple Choice',
			'ab' => 'Abstufung',
			'za' => 'Zahl',
			'ft' => 'Freitext'
		),
		//Text der Frage
		'frage' => 'Hier kommt eine schöne Frage hin!',
		//Beschriftung der Felder
		'felder' => array(
			1 => 'A',
			2 => 'B'
		)
	)
);

//Umfrage bearbeiten?
//ID gegeben?
if(
	isset( $_GET['task'] ) && $_GET['task'] == 'edit'
	&&
	isset( $_GET['uid'] ) && is_numeric( $_GET['uid'] )
){
	//machen
	//	Vars.
	$uid =  $_GET['uid'];
	$addonurlhere = $addonurl.'&amp;task=edit&amp;uid='.$uid;

	//Datei laden
	$ufile = new KIMBdbf( 'addon/survey__'.$uid.'_conf.kimb' );

	$sitecontent->add_site_content( '<h3>Konfiguration der Umfrage auf Seite: '.$uid.'</h3>' );

	//jQuery UI Tabs (in de: Reiter)
	//	zuerst gewählter Tab nach GET[tab] (0 oder 1)
	if( empty( $_GET['tab'] ) || $_GET['tab'] != 1 && $_GET['tab'] != 0 ){
		//wenn nicht gewählt oder falsch -> 0 
		$_GET['tab'] = 0;
	}
	//JS für Tabs
	$sitecontent->add_html_header('<script> $(function() { $( "#reiter" ).tabs( { active: '.$_GET['tab'].' } ); }); </script>');

	//Übergaben verarbeiten
	if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
		if( $_GET['tab'] == 0 ){
			//Allgemein

			$changed = 0;
			
			//alle POST durchgehen
			foreach( $uconfs[0] as $name => $vals ){
				//definiert?
				if( isset( $_POST[$name] ) ){
					$value = $_POST[$name];
					
					if( $name == 'inform' ){
						//bei Freitext Value immer okay
						$valok = true;
					}
					else{
						//Value in Array der möglichen Values?
						$valok = in_array( $value , $vals );	
					}
					//wenn okay
					if( $valok ){
						//aktuelles lesen
						$oldval = $ufile->read_kimb_one( $name );
						//Änderung ?
						if( $value != $oldval ){
							if( $ufile->write_kimb_one( $name, $value ) ){
								$changed++;

								//Markdown
								if( $name == 'inform' ){
									//parsen
									$valueh = parse_markdown( $value );
									//ablegen
									$ufile->write_kimb_one( $name.'-parsed', $valueh );
								}
							}
						}
					}
				}
			}

		}
		elseif( $_GET['tab'] == 1 ){
			//Fragen
			
			//neu?
			if( isset( $_POST['type'][0] ) ){
				//Wert okay?
				if( in_array( $_POST['type'][0], array_keys( $uconfs[1]['type'] ) ) ){
					//Frage erstellen, mit Standardwerten
					$id = $ufile->next_kimb_id();
					$ufile->write_kimb_id( $id, 'add', 'type', $_POST['type'][0] );
					$ufile->write_kimb_id( $id, 'add', 'frage', $uconfs[1]['frage'] );
					$ufile->write_kimb_id( $id, 'add', 'felder', $uconfs[1]['felder'] );

					$sitecontent->echo_message( 'Es wurde eine neue Frage erstellt!' );
				}
			}

			//verändern

			//...
		}
	}
	
	//Links oben für Tabs
	$sitecontent->add_site_content( '<div id="reiter" style="overflow:hidden;">
  	<ul>
		<li><a href="#reiter_allg">Allgemein</a></li>
		<li><a href="#reiter_fragen">Fragen</a></li>
	</ul>');
		
	//ersten Tab beginnen
	$sitecontent->add_site_content( '<div id="reiter_fragen">');
	
	//Fragen
	$sitecontent->add_site_content( '<h4>Fragen</h4>' );

	//alle lesen
	$fragen = $ufile->read_kimb_id_all();

	//Formular
	$sitecontent->add_site_content('<form action="'.$addonurlhere.'&amp;tab=1" method="post">');

	//Liste
	$sitecontent->add_html_header('<style>table#fragen{ border:1px solid; border-collapse:collapse; width: 100%; } table#fragen tr.borderbott{ border-bottom:1px solid; border-collapse:collapse; } table#fragen tr.borderbott div{ background-color: #fff; border-radius:5px; padding:10px; margin:5px; }</style>');
	$sitecontent->add_site_content('<table id="fragen">');

	//alle auflisten
	foreach( $fragen as $id => $data ){
		$sitecontent->add_site_content('<tr>');
		//	Infos zur Frage
		$sitecontent->add_site_content('<th>'.$id.'</th>');
		$sitecontent->add_site_content('<td>'.$uconfs[1]['type'][$data['type']].'</td>');
		//	Text der Frage
		$sitecontent->add_site_content('<td><textarea name="frage['.$id.']" style="width:90%;">'.htmlentities( $data['frage'], ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea></td>');
		$sitecontent->add_site_content('</tr>');
		$sitecontent->add_site_content('<tr class="borderbott">');
		$sitecontent->add_site_content('<td colspan="3"><div>');
		//	Fragefelder
		$sitecontent->add_site_content( nl2br( print_r( $data['felder'], true) ) );
		$sitecontent->add_site_content('</div>');
		//Löschen und verschieben Button
		$sitecontent->add_site_content('<span class="ui-icon ui-icon-trash" style="display:inline-block;" onclick="delete_fra('.$id.');" title="Diese Frage löschen."></span>');
		$sitecontent->add_site_content('<span class="ui-icon ui-icon-arrowthick-1-n" style="display:inline-block;" onclick="versch_fra('.$id.', \'hoch\');" title="Diese Frage nach oben schieben."></span>');
		$sitecontent->add_site_content('<span class="ui-icon ui-icon-arrowthick-1-s" style="display:inline-block;" onclick="delete_fra('.$id.', \'runter\');" title="Diese Frage nach unten schieben."></span>');

		$sitecontent->add_site_content('<td>');
		$sitecontent->add_site_content('</tr>');
	}

	//Neu
	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<th>0</th>');
	$sitecontent->add_site_content('<td>');
	$sitecontent->add_site_content('<select name="type[0]">');
	$sitecontent->add_site_content('<option value="none">Bitte wählen</option>');
	foreach( $uconfs[1]['type'] as $value => $name ){
		$sitecontent->add_site_content('<option value="'.$value.'">'.$name.'</option>');
	}
	$sitecontent->add_site_content('</select>');
	$sitecontent->add_site_content('</td>');
	$sitecontent->add_site_content('<td width="50%">');
	$sitecontent->add_site_content('Bitte wählen Sie hier den Typ der neuen Frage.
	<ul>
	<li><b>Auswahl:</b> Sie geben verschiedene Optionen an und der User kann genau eine davon auswählen.</li>
	<li><b>Multiple Choice:</b> Sie geben verschiedene Optionen an und der User kann beliebig viele davon auswählen.</li>
	<li><b>Abstufung:</b> Sie nennen verschieden Punkte und der User kann diese bewerten. (1 [sehr gut] - 6 [schlecht] und keine Angabe)</li>
	<li><b>Zahl:</b> Sie fragen den User und dieser kann frei eine Zahl eingeben.</li>
	<li><b>Freitext:</b> Der User kann frei Text schreiben.</li>
	</ul>');
	$sitecontent->add_site_content('</td>');
	$sitecontent->add_site_content('</tr>');

	$sitecontent->add_site_content('</table>');

	$sitecontent->add_site_content('<input type="submit" value="Speichern">');
	$sitecontent->add_site_content('</form>');

	//zweiter Tab
	$sitecontent->add_site_content( '</div><div id="reiter_allg">');

	//Standardwerte
	foreach( $uconfs[0] as $teil => $vala ){
		//Text einfach in Var
		if( $teil == 'inform' ){
			$outvals[$teil] = $ufile->read_kimb_one( $teil );
		}
		//alle Möglichkeiten in Array $vala
		else{
			//Wahl lesen
			$correct = $ufile->read_kimb_one( $teil );
			//alle durchgehen
			foreach( $vala as $val ){
				//gewähltes aus checked
				$outvals[$teil][$val] = ($correct == $val ? ' checked="checked"' : '' );
			}
		}
	}

	//Allgemein
	$sitecontent->add_site_content( '<h4>Allgemein</h4>' );
	//	Formular
	$sitecontent->add_site_content('<form action="'.$addonurlhere.'&amp;tab=0" method="post">');
	//	Wertetabelle
	//Button mit Link zur Liste weg
	$sitecontent->add_html_header('<style>table#editwerte, table#editwerte tr, table#editwerte th{ border:1px solid; border-collapse:collapse; } table#editwerte{ width: 100%; }</style>');
	$sitecontent->add_site_content('<table id="editwerte">');

	//Zugriff
	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<th>Zugriff</th>');
	$sitecontent->add_site_content('<td><input type="radio" name="zugriff" value="fe"'.$outvals['zugriff']['fe'].'> Felogin<br />');
	$sitecontent->add_site_content('<input type="radio" name="zugriff" value="li"'.$outvals['zugriff']['li'].'> Link<br />');
	$sitecontent->add_site_content('<input type="radio" name="zugriff" value="oe"'.$outvals['zugriff']['oe'].'> Öffentlich</td>');
	$sitecontent->add_site_content('<td style="width:50%;">');
	$sitecontent->add_site_content('Geben Sie an über welchen Weg User Zugriff auf die Umfrage haben sollen.
	<ul>
	<li><b>Felogin:</b> Nur über Felogin angemeldete User können einmal abstimmen. (Erfordert Add-on Felogin; Die User müssen zum Abstimmen Zugriff auf die Seite mit der Umfrage haben.)</li>
	<li><b>Link:</b> Sie können sich eine CSV Liste mit einer wählbaren Anzahl an Links ausgeben lassen. Mit jedem Link kann einmal an der Umfrage teilgenommen werden.</li>
	<li><b>Öffentlich:</b> Jeder Besucher der Seite kann teilnehmen. (Es wird versucht die Anzahl der Teilnahmen pro User auf 1 zu beschränken; Der User muss Zugriff auf die Seite haben.)</li>
	</ul>');
	$sitecontent->add_site_content('</td>');
	$sitecontent->add_site_content('</tr>');

	//Linkmaker
	//	Zugriff per Link aktiviert?
	if( !empty( $outvals['zugriff']['li'] ) ){
		//Formular
		$sitecontent->add_site_content('<tr>');
		$sitecontent->add_site_content('<th>Links für Zugriff</th>');
		$sitecontent->add_site_content('<td><input type="number" id="linksanz" min="0" max="500" placeholder="Anzahl">');
		$sitecontent->add_site_content('<button onclick="make_links(); return false;">Erstellen</button></td>');
		$sitecontent->add_site_content('<td style="width:50%;">');
		$sitecontent->add_site_content('Erstellen Sie hier eine CSV Liste mit einer von Ihnen gewünschten Anzahl an Zugriffslinks zur Umfrage.<br />');
		$sitecontent->add_site_content('<em>(Sofern Sie 0 eingeben, werden alle Links gelöscht bzw. funktionieren nicht mehr.)</em>');
		$sitecontent->add_site_content('</td>');
		$sitecontent->add_site_content('</tr>');

		//JS für Links
		$sitecontent->add_html_header( '<script>
		function make_links( uid ){
			var anz = $( "input#linksanz" ).val();
			var url = "'.$allgsysconf['siteurl'].'/ajax.php?addon=survey&todo=makelinks&uid='.$uid.'&anz="+anz;
			window.open( url, "_blank", "width=900px,height=500px,top=20px,left=20px");
		}
		</script>');
	}

	//Auswertung
	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<th>Auswertung</th>');
	$sitecontent->add_site_content('<td><input type="radio" name="auswer" value="an" '.$outvals['auswer']['an'].'> Anonym<br />');
	$sitecontent->add_site_content('<input type="radio" name="auswer" value="na" '.$outvals['auswer']['na'].'> Name</td>');
	$sitecontent->add_site_content('<td style="width:50%;">');
	$sitecontent->add_site_content('Geben Sie an wie die Umfrage ausgewertet werden soll.
	<ul>
	<li><b>Anonym:</b> Alle Angaben werden anonym in die Statistik überführt.</li>
	<li><b>(User-)Name:</b> Jeder User kann vor dem Ausfüllen einen Namen angeben, unter welchem dann seine Wahl zu sehen ist. (Sinvoll für z.B. Terminabstimmungen)</li>
	</ul>');
	$sitecontent->add_site_content('</td>');
	$sitecontent->add_site_content('</tr>');

	//Auswertung
	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<th>Zugriff auf die Auswertung</th>');
	$sitecontent->add_site_content('<td><input type="radio" name="zugaus" value="ad" '.$outvals['zugaus']['ad'].'> Administratoren<br />');
	$sitecontent->add_site_content('<input type="radio" name="zugaus" value="oe" '.$outvals['zugaus']['oe'].'> Öffentlich</td>');
	$sitecontent->add_site_content('<td style="width:50%;">');
	$sitecontent->add_site_content('Geben Sie an wer die Auswertungen der Umfrage sehen darf.
	<ul>
	<li><b>Administratoren:</b> Die Auswertung ist nur im Backend unter "Nutzung" zu finden.</li>
	<li><b>Öffentlich:</b> Jeder User, der Zugriff auf die Seite mit der Umfrage hat, kann die Auswerung sehen.</li>
	</ul>');
	$sitecontent->add_site_content('</td>');
	$sitecontent->add_site_content('</tr>');

	//Infotext
	//	Editor
	add_content_editor( 'inform', true );
	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<th>Information</th>');
	$sitecontent->add_site_content('<td colspan="2"><textarea name="inform" id="inform" rows="10">'.htmlentities( $outvals['inform'], ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea><br />');
	$sitecontent->add_site_content('Geben Sie eine kleine Einleitung zu Ihrer Umfrage. (Markdown möglich)');
	$sitecontent->add_site_content('</td>');
	$sitecontent->add_site_content('</tr>');

	//	Ende
	$sitecontent->add_site_content('</table>');

	$sitecontent->add_site_content('<input type="submit" value="Speichern">');
	$sitecontent->add_site_content('</form>');

	//Tabs Ende
	$sitecontent->add_site_content( '</div></div>');
}
//Neue Umfrage?
//SeitenID gegeben?
elseif(
	isset( $_GET['task'] ) && $_GET['task'] == 'new'
	&&
	!empty( $_POST['sid'] ) && is_numeric( $_POST['sid'] )
){
	//neu machen
	$sid = $_POST['sid'];
	//	schon vergeben?
	if( $sysfile->read_kimb_search_teilpl( 'uid', $sid ) == false ){
		//hinzufügen
		if( $sysfile->write_kimb_teilpl( 'uid', $sid, 'add' ) ){

			//Standardwerte
			//	Datei laden
			$ufile = new KIMBdbf( 'addon/survey__'.$sid.'_conf.kimb');
			//	schreiben
			foreach( $uconfs[0] as $teil => $vala ){
				$ufile->write_kimb_one( $teil, $vala[0] );
			}

			//weiter zu edit => FWD
			open_url( '/kimb-cms-backend/addon_conf.php?todo=more&addon=survey&task=edit&uid='.$sid );
			die;
		}
		else{
			$list = true;
			$sitecontent->echo_error('Konnte die Umfrage nicht erstellen!');
		}
	}
	else{
		$list = true;
		$sitecontent->echo_error('Diese Seite hat bereits eine Umfrage!');
	}
}
//Umfrage löschen
//ID gegeben?
elseif(
	isset( $_GET['task'] ) && $_GET['task'] == 'del'
	&&
	isset( $_GET['uid'] ) && is_numeric( $_GET['uid'] )
){
	$uid = $_GET['uid'];
	//löschen
	if(
		//austragen
		$sysfile->write_kimb_teilpl( 'uid', $uid, 'del' )
		&&
		//Umfragedateien löschen
		//	Konfdatei
		delete_kimb_datei( 'addon/survey__'.$uid.'_conf.kimb' )
		&&
		//	Ergdatei
		delete_kimb_datei( 'addon/survey__'.$uid.'_erg.kimb' )
	){
		$sitecontent->echo_message('Die Umfrage der Seite wurde gelöscht!');
	}
	else{
		//Fehler
		$sitecontent->echo_error('Konnte die Umfrage der Seite nicht löschen!');
	}
	$list = true;
}
//nichts
else{
	//Liste aller Umfragen zeigen
	$list = true;
}

//Liste der Umfragen?
if( $list ){
	//los gehts

	//Button mit Link zur Liste weg
	$sitecontent->add_html_header( '<style>p#survey_be_back{ display:none; }</style>');

	//Löschen Dialog
	$sitecontent->add_html_header( '<script>
	function delete_sur( uid ){
		$( "div#deldia span" ).text( uid );
		$( "div#deldia" ).css( "display", "block" );
		$( "div#deldia" ).dialog({
			modal: true,
			buttons:{
				"Löschen" : function (){
					window.location.href = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=survey&task=del&uid=" + uid;
					$( this ).dialog( "close" );
				},
				"Abbrechen" : function (){
					$( this ).dialog( "close" );
				}
			}
		});
	}
	</script>');
	

	//alle UIDs lesen
	$uids = $sysfile->read_kimb_all_teilpl( 'uid' );
	//Array ( SeitenID => Seitenname ) erstellen
	foreach( list_sites_array() as $array ){
		$names[$array['id']] = $array['site'];
	}

	//Tabelle für Ausgabe
	$sitecontent->add_site_content('<table>');
	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<th>ID</th>');
	$sitecontent->add_site_content('<th>Seite</th>');
	$sitecontent->add_site_content('<th>Löschen</th>');
	$sitecontent->add_site_content('</tr>');

	//alle Seiten mit Umfrage durchgehen
	foreach( $uids as $uid ){
		$sitecontent->add_site_content('<tr>');
		$sitecontent->add_site_content('<td>'.$uid.'</td>');
		$sitecontent->add_site_content('<td><a href="'.$addonurl.'&amp;task=edit&amp;uid='.$uid.'">'.$names[$uid].'</a></td>');
		$sitecontent->add_site_content('<td><span class="ui-icon ui-icon-trash" onclick="delete_sur('.$uid.');"></span></td>');
		$sitecontent->add_site_content('</tr>');
	}
	//leer ?
	if( $uids == array () ){
		$sitecontent->add_site_content('<tr><td colspan="3">&nbsp;</td></tr>');
		$sitecontent->add_site_content('<tr>');
		$sitecontent->add_site_content('<td colspan="3">Bisher keine Umfragen erstellt.</td>');
		$sitecontent->add_site_content('</tr>');
		$sitecontent->add_site_content('<tr><td colspan="3">&nbsp;</td></tr>');
	}

	// neu hinzufügen

	$sitecontent->add_site_content('<tr>');
	$sitecontent->add_site_content('<td><span class="ui-icon ui-icon-plus"></span></td>');
	$sitecontent->add_site_content('<td>');
	$sitecontent->add_site_content('<form action="'.$addonurl.'&amp;task=new" method="post">');
	//	SeitenID Dropdown	
	$sitecontent->add_site_content( id_dropdown( 'sid', 'siteid' ) );
	$sitecontent->add_site_content('</td><td>');
	//	Button
	$sitecontent->add_site_content('<input type="submit" value="Erstellen">');
	$sitecontent->add_site_content('</form>');

	$sitecontent->add_site_content('</td>');
	$sitecontent->add_site_content('</tr>');

	//Tabelle beenden
	$sitecontent->add_site_content('</table>');

	//Dialog fürs Löschen
	$sitecontent->add_site_content( '<div id="deldia" style="display:none;" title="Löschen">Möchten Sie die Umfrage auf Seite <span>0</span> wirklich löschen?<br />Alle Fragen und Ergebnisse der Umfrage gehen unwiderruflich verloren.</div>' );
}
?>