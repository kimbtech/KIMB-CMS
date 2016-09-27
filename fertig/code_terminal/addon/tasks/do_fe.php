<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies.eu
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

$sitecontent->add_site_content( '<h3>Frontend</h3>' );

//design
$sitecontent->add_html_header( '<style>div.block{ margin:5px; padding:5px; background-color:#ddd; border-radius:5px;}
div.innerblock{ margin:5px; padding:5px; background-color:#fff; border-radius:5px;}</style>' );

//dbf für fe lesen
$html_out_fe = new KIMBdbf( 'addon/code_terminal__fe.kimb' );

// # Einbindung

//Übergabe?
if( isset( $_POST['send'] ) ){
	//noch nichts gemacht
	$okay = 0;
	$okaydo = 0;

	//alle Teile
	$wishdo = array( 'stelle', 'ids', 'error' );

	//Add-on API Daten lesen
	$wish = $addonapi->read( 'fe' );
	//	alles leer 
	if( $wish === false ){
		$wish = array( 'stelle' => '', 'ids' => '' , 'error' => '' );
	}

	//SiteID Prefix?
	if( isset( $_POST['ids'] ) && !empty( $_POST['ids'] ) ){
		if( $_POST['ids'] != 'a' && is_numeric( $_POST['ids'] )){
			$_POST['ids'] = 's'.$_POST['ids'];
		}
	}
	
	//alle Werte für wishes durchgehen
	foreach( $wishdo as $names ){
		//Wert darf nicht leer sein!
		if(!empty( $_POST[$names] ) ){
			//Wert verändert?
			if( $_POST[$names] != $wish[$names] ){
				$okaydo++;
			}
			$okay++;
		}
	}
	//alle Werte okay?
	if( $okay == 3 && $okaydo != 0 ){
		$addonapi->set_fe( $_POST['stelle'], $_POST['ids'], $_POST['error'] );

		//Medlung
		$sitecontent->echo_message( 'Die FE Einbindung wurde im CMS registriert.', 'Einbindung' );	
	}
	elseif( $okay != 3 ){
		//Medlung
		$sitecontent->echo_message( '<b>Bitte füllen Sie alle Felder unter Einbindung!</b>', 'Einbindung' );
	}
}
//deaktivieren?
elseif( isset( $_GET['disable'] ) ){
	//löschen
	if( !$addonapi->del( array( 'fe' ) ) ){
		//Medlung (wenn Fehler)
		$sitecontent->echo_message( '<b>Konnte nicht deaktiviert werden!</b>', 'Einbindung' );
	}
}

//Add-on API Daten neu lesen
$wish = $addonapi->read( 'fe' );
//	noch leer?
if( $wish === false ){
	//Status
	$status = '<span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>';
	//Werte
	$stelle = ''; 
	$ids = '';
	$error = '';
}
else{
	//Werte aus API lesen
	$stelle = $wish['stelle']; 
	$ids = $wish['ids'];
	//	Präfix weg, wenn nicht alles 
	if( $ids != 'a' && !empty( $ids ) ){
		//weg
		$ids = substr( $ids, 1 );
	}
	$error = $wish['error'];
	//Status
	$status = '<span style="display:inline-block;" title="Aktiviert" class="ui-icon ui-icon-check"></span>';
	$status .= ' <a href="'.$addonurl.'&disable">deaktivieren</a>';
}

$sitecontent->add_site_content( '<div class="block">');
//Status
$sitecontent->add_site_content( '<p>Aktueller Status: '.$status.'</p>' );
$sitecontent->add_site_content( '</div>');

//Formular beginnen
$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

$sitecontent->add_site_content( '<div class="block">');
//Wish
$sitecontent->add_site_content( '<h4>Einbindung</h4>' );
$sitecontent->add_site_content( 'Bitte wählen Sie wo die Eingaben ausgegeben werden sollen:<br />' );

//vorne oder hinten Dropdown
$sitecontent->add_html_header('<script>$(function(){ $( "[name=stelle]" ).val( "'.$stelle.'" ); }); </script>');
$sitecontent->add_site_content( '<select name="stelle" title="Stelle innerhalb der Add-ons."><option value="vorn">Vorne</option><option value="hinten">Hinten</option></select>' );
	
//Auswahl IDs
$sitecontent->add_html_header('<script>$(function(){ $( "[name=ids]" ).prepend( "<option value=\'a\'>Alle</option>" ); $( "[name=ids]" ).val( "'.$ids.'" ); }); </script>');
$sitecontent->add_site_content( '<span title="Auf welcher Seite des Frontends soll ausgegeben werden?">'.id_dropdown( 'ids', 'siteid' ).'</span>' );
	
//Auswahl Fehler
$sitecontent->add_html_header('<script>$(function(){ $( "[name=error]" ).val( "'.$error.'" );  $( "option" ).tooltip( { position: { my: "left+15 center", at: "right center" } } ); }); </script>');
$sitecontent->add_site_content( '<select name="error" title="Wann soll ausgegeben werden?">
	<option value="no" title="Immer ausgeben, sofern keine Fehler.">Keine Fehler</option>
	<option value="all" title="Immer ausgeben, auch bei Fehlern">Immer</option>
	<option value="403" title="Nur bei Fehler 403 ausgeben">Fehler 403</option>
	<option value="404" title="Nur bei Fehler 404 ausgeben">Fehler 404</option>
</select>' );
//Text Level der Einschränkungen
$sitecontent->add_site_content('<hr />Bitte beachten Sie, dass die Auswahl der Einbindungsstelle &uarr; über der Auswahl der Seite jedes einzelnen Blocks &darr; steht. Das heißt, Sie müssen bei Einbindung <code>Alle</code> wählen, wenn Sie eine Add-on Area auf Seite 1 und einen HTML-Header auf Seite 5 brauchen.');
$sitecontent->add_site_content( '</div>');
	
// # Inhalte

//	Typen der Inhalte
$types = array(
	'header' => 'HTML-Header',
	'area' => 'Add-on Area',
	'cont' => 'Seiteninhalt',
	'php' => 'PHP-Code',
);
//	Vorgaben
$types_struc = array(
	'header' => array( 'site' => 'a', 'code' => '' ),
	'area' => array( 'site' => 'a', 'place' => 'top', 'inhalt' => '', 'cssclass' => '' ),
	'cont' => array( 'site' => 'a', 'place' => 'top', 'inhalt' => '' ),
	'php' => array( 'site' => 'a', 'place' => 'top', 'phpcode' => '', 'fullcache' => 'on' )
);

$sitecontent->add_site_content( '<div class="block">');
$sitecontent->add_site_content( '<h4>Inhalte</h4>');


//für Meldungen
$message = array();
//Übergabe?
if( isset( $_POST['send'] ) ){

	//neu?
	if(
		$_POST['add'] !== 'none'
		&&
		in_array( $_POST['add'], array_keys( $types )  )
	){
		$type = $_POST['add'];

		//ID holen
		$id = $html_out_fe->next_kimb_id();
		//schreiben
		if( $html_out_fe->write_kimb_id( $id, 'add', 'type', $type ) ){
			foreach( $types_struc[$type] as $teil => $val ){
				$html_out_fe->write_kimb_id( $id, 'add', $teil, $val );
			}
			//Medlung
			$message[] = 'Es wurde ein neuer Block eingefügt!';
		}
	}

	//geändert?
	//	alles lesen
	$alles = $html_out_fe->read_kimb_id_all();
	//	durchgehen
	foreach( $alles as $id => $vals ){
		//Daten zur ID übergeben?
		if(
			isset( $_POST['blocksite'][$id] ) && !empty( $_POST['blocksite'][$id] )
			&&
			isset( $_POST['blockvals'][$id] ) && !empty( $_POST['blockvals'][$id] )
		){
			//Daten in Vars.
			$site = $_POST['blocksite'][$id];
			$type = $vals['type'];
			$newvals = $_POST['blockvals'][$id];
			//Seite zu newvals
			$newvals['site'] = $site;

			//noch nichts
			$done = false;

			//Werte des Types
			foreach( $types_struc[$type] as $name => $val ){

				//hier nicht okay
				$ok = false;

				//PHP?
				if( $name == 'phpcode' ){
					//Rechte prüfen
					if( check_backend_login( 'no' , 'more', false ) ){
						//PHP Code okay?
						if(
							substr($newvals[$name], 0, 5 ) == '<?php'
							&&
							substr($newvals[$name], -2 ) == '?>'
						){

							//Code optimieren
							//	\<\?\php und \?\> weg
							$newvals[$name] = substr( $newvals[$name], 5, -2 );

							//ok
							$ok = true;
						}
						else{
							$message[] = 'Fehlerhafter PHP-Code <small>(muss immer direkt mit <code>&lt;?php</code> beginnen und mit <code>?&gt;</code> enden)</small>!';
						}
					}
				}	
				else{
					//sonst immer okay
					$ok = true;
				}

				//FullCache anpassen?
				if( $name == 'fullcache' ){

					//aktuelle Daten lesen
					$data = $addonapi->full_html_cache_wish();

					//leer?
					if( $data == $addonapi->emptyarray ){
						$data = array();
					}

					//PHP-Code für überall und FullCache aktiviert?
					if(
						$site == 'a'
						&&
						$newvals[$name] == 'on'
					){
						unset( $data['a'] );
					}
					//PHP-Code für überall und FullCache deaktiviert?
					elseif(
						$site == 'a'
						&&
						$newvals[$name] == 'off'
					){
						$data['a'] = array('off', array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() ));
					}

					//SeitenID?
					if( is_numeric( $site ) ){
						//Seiten ID zu RequestID umrechnen
						//	ID Zuordnungen Datei laden
						$idfile = new KIMBdbf('menue/allids.kimb');
						//	do it
						$requid = $idfile->search_kimb_xxxid( $site , 'siteid' );

						//gefunden?
						if( $requid != false ){
							//je nach Status Array apassen
							if( $newvals[$name] == 'on' ){
								//Cache an
								unset( $data[$requid] );
							}
							elseif( $newvals[$name] == 'off' ){
								//Cache aus
								$data[$requid] = array('off', array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() ));
							}
						}
					}

					//leer?
					if( empty( $data ) ){
						//keine Wünsche!
						$addonapi->del( array( 'fullcache' ) );
					}
					else{
						//Array an API übergeben	
						$addonapi->full_html_cache_wish( 'set', $data );
					} 	

				}

				//erlaubt
				if( $ok ){
					//Übergabe nicht leer?
					if( isset( $newvals[$name] ) && !empty( $newvals[$name] ) ){
						//Änderungen?
						if( $newvals[$name] != $vals[$name] ){
							//sichern
							$html_out_fe->write_kimb_id( $id, 'add', $name, $newvals[$name] );

							$done = true;
						}
					}
				}
			}

			//gemacht?
			if( $done ){
				$message[] = 'Änderungen an '.$types[$type].' ('.$id.') übernommen!';
			}
		}

	}
}
//Löschen?
elseif( isset( $_GET['delblock'] ) && is_numeric( $_GET['delblock'] ) ){
	//löschen
	if( $html_out_fe->write_kimb_id( $_GET['delblock'] , 'del' ) ){
		$message[] = 'Es wurde ein Block gelöscht!';
	}
	else{
		$message[] = '<b>Konnte den Block nicht löschen!</b>';
	}

}

//wenn Medlungen vorhanden
if( !empty( $message ) ){
	//ausgeben
	$sitecontent->echo_message( implode( '<br />', $message ) );
}

//auslesen
$alles = $html_out_fe->read_kimb_id_all();
$leer = true;
//alles durchgehen
foreach( $alles as $id => $vals ){
	//hinzufügen

	$sitecontent->add_site_content( '<div class="innerblock">' );

	//Typ
	$sitecontent->add_site_content( 'Typ: '.$types[$vals['type']].'<br />' );
	//Seite
	//	Root?
	if( isset( $vals['phpcode']) && !check_backend_login( 'no' , 'more', false ) ){
		//keine Seite für PHP-Code wählen
		$sitecontent->add_html_header('<script>$(function(){ $( "[name=\'blocksite['.$id.']\']" )[0].disabled = true; }); </script>');
		//keine Stelle für PHP Code wählen
		$sitecontent->add_html_header('<script>$(function(){ $( "[name=\'blockvals['.$id.'][place]\']" )[0].disabled = true; }); </script>');	
	}
	$sitecontent->add_html_header('<script>$(function(){ $( "[name=\'blocksite['.$id.']\']" ).prepend( "<option value=\'a\'>Alle</option>" ).val( "'.$vals['site'].'" ); }); </script>');
	$sitecontent->add_site_content( 'Ausgabeseite: '.id_dropdown( 'blocksite['.$id.']', 'siteid' ).'<br />' );

	//oben/ unten?
	if( isset( $vals['place'] ) ){
		$sitecontent->add_html_header( '<script>$(function(){ $( "[name=\'blockvals['.$id.'][place]\']" ).val( "'.$vals['place'].'" ); }); </script>');
		$sitecontent->add_site_content( 'Ausgabestelle: <select name="blockvals['.$id.'][place]"><option value="top">Oben</option><option value="bottom">Unten</option></select><br />' );
	}

	//FullCache on/off
	if( isset( $vals['fullcache'] ) ){
		$sitecontent->add_site_content('FullHTMLCache: ');
		$sitecontent->add_site_content( '<input type="radio" name="blockvals['.$id.'][fullcache]" value="on" '.($vals['fullcache'] == 'on' ? 'checked="checked"' : '' ).'> Aktiviert ' );
		$sitecontent->add_site_content( '<input type="radio" name="blockvals['.$id.'][fullcache]" value="off" '.($vals['fullcache'] == 'off' ? 'checked="checked"' : '' ).'> Deaktiviert <br />' );
	}

	//Eingaben
	if( isset( $vals['cssclass']) ){
		//Input
		$sitecontent->add_site_content( 'CSS Klasse: <input type="text" name="blockvals['.$id.'][cssclass]" value="'.htmlspecialchars( $vals['cssclass'], ENT_COMPAT | ENT_HTML401,'UTF-8').'"><br />' );
	}
	if( isset( $vals['inhalt']) ){
		//Editor
		add_content_editor( 'blvinh_'.$id );
		//Textarea
		$sitecontent->add_site_content( 'Inhalt: <textarea name="blockvals['.$id.'][inhalt]" id="blvinh_'.$id.'">'.htmlspecialchars( $vals['inhalt'], ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea><br />' );
	}
	if( isset( $vals['code']) ){
		//Editor
		add_content_editor( 'blvcod_'.$id, true );
		//Textarea
		$sitecontent->add_site_content( 'HTML-Code: <textarea name="blockvals['.$id.'][code]" id="blvcod_'.$id.'">'.htmlspecialchars( $vals['code'], ENT_COMPAT | ENT_HTML401,'UTF-8').'</textarea><br />' );
	}
	if( isset( $vals['phpcode']) ){

		//Code optimieren
		if( empty( $vals['phpcode'] ) ){
			//wenn leer, Beispiel
			$code = '<?php'."\r\n\r\n".'?>';
		}
		else{
			$code = '<?php'.htmlspecialchars( $vals['phpcode'], ENT_COMPAT | ENT_HTML401,'UTF-8').'?>';
		}

		//Rechte okay?
		if( check_backend_login( 'no' , 'more', false ) ){
			make_code_terminal_phpedi( 'blvphp_'.$id );
			$sitecontent->add_site_content( 'PHP-Code: <textarea name="blockvals['.$id.'][phpcode]" id="blvphp_'.$id.'">'.$code.'</textarea><br />' );
		}
		else{
			$sitecontent->add_site_content( 'PHP-Code: <textarea style="width:95%; height:100px; resize:vertical;" disabled="disabled">'.$code.'</textarea><br />' );
		}
		
	}
	$sitecontent->add_site_content( '<a href="'.$addonurl.'&delblock='.$id.'" onclick="return confirm(\'Möchten Sie den Block wirklich löschen?\');"><span class="ui-icon ui-icon-trash" title="Diesen Ausgabeblock löschen?"></span></a>' );
	$sitecontent->add_site_content( '</div>' );

	//welche drin
	$leer = false;
}
//leer?
if( $leer ){
	$sitecontent->add_site_content( '<div class="innerblock">' );
	$sitecontent->add_site_content( 'Bisher noch keine Blöcke!' );
	$sitecontent->add_site_content( '</div>' );
}

$sitecontent->add_site_content( '</div>');

//neu
$sitecontent->add_site_content( '<div class="block">');
$sitecontent->add_site_content( '<h4>Inhalt hinzufügen</h4>');
$sitecontent->add_site_content( '<select name="add">');
$sitecontent->add_site_content( '<option value="none"></option>');
foreach( $types as $key => $name ){
	$sitecontent->add_site_content( '<option value="'.$key.'">'.$name.'</option>');
}
$sitecontent->add_site_content( '</select>');
$sitecontent->add_site_content( '</div>');

$sitecontent->add_site_content( '<div class="block">');
//Button
$sitecontent->add_site_content( '<input type="hidden" name="send" value="yes">');
$sitecontent->add_site_content( '<input type="submit" value="Speichern">');
$sitecontent->add_site_content( '</form>');

//Hinweis
$sitecontent->add_site_content( '<br /><br />' );
$sitecontent->echo_message( 'Sofern Ihr Code fehlerhaft ist, kann das Backend des CMS unbrauchbar werden. Bitte testen Sie PHP-Code im Terminal und HTML-Code z.B. mit Firebug!', 'Wichtig');

$sitecontent->add_site_content( '</div>');
?>