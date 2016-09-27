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

//Überschrift
$sitecontent->add_site_content( '<h3>Backend</h3>' );

//dbf für be lesen
$html_out_be = new KIMBdbf( 'addon/code_terminal__be.kimb' );

$sitecontent->add_html_header( '<style>div.block{ margin:5px; padding:5px; background-color:#ddd; border-radius:5px;}
div.innerblock{ margin:5px; padding:5px; background-color:#fff; border-radius:5px;}</style>' );

// # Einbindung

//Übergabe?
if( isset( $_POST['send'] ) ){
	//noch nichts gemacht
	$okay = 0;
	$okaydo = 0;

	//alle Teile
	$wishdo = array( 'stelle', 'site', 'recht' );

	//Add-on API Daten lesen
	$wish = $addonapi->read( 'be' );
	//	alles leer 
	if( $wish === false ){
		$wish = array( 'stelle' => '', 'site' => '' , 'recht' => '' );
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
		$addonapi->set_be( $_POST['stelle'], $_POST['site'], $_POST['recht'] );

		//Medlung
		$sitecontent->echo_message( 'Die BE Einbindung wurde im CMS registriert.', 'Einbindung' );	
	}
	elseif( $okay != 3 ){
		//Medlung
		$sitecontent->echo_message( '<b>Bitte füllen Sie alle Felder unter Einbindung!</b>', 'Einbindung' );
	}
}
//deaktivieren?
elseif( isset( $_GET['disable'] ) ){
	//löschen
	if( !$addonapi->del( array( 'be' ) ) ){
		//Medlung (wenn Fehler)
		$sitecontent->echo_message( '<b>Konnte nicht deaktiviert werden!</b>', 'Einbindung' );
	}
}

//Add-on API Daten neu lesen
$wish = $addonapi->read( 'be' );
//	noch leer?
if( $wish === false ){
	//Status
	$status = '<span style="display:inline-block;" title="Deaktiviert" class="ui-icon ui-icon-closethick"></span>';
	//Werte
	$stelle = ''; 
	$site = '';
	$recht = '';
}
else{
	//Werte aus API lesen
	$stelle = $wish['stelle']; 
	$site = $wish['site'];
	$recht = $wish['recht'];
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


//Tabell um Eingabe und Text abzustimmen
$sitecontent->add_site_content('<table><tr><td>');

//vorne oder hinten Dropdown
$sitecontent->add_html_header('<script>$(function(){ $( "[name=stelle]" ).val( "'.$stelle.'" ); }); </script>');
$sitecontent->add_site_content( '<select name="stelle" title="Stelle innerhalb der Add-ons."><option value="vorn">Vorne</option><option value="hinten">Hinten</option></select>' );

$sitecontent->add_site_content('</td><td>');

//Dropdown Seiten
$sitesopt = '<option value="all">Überall</option>
<option value="index">Home/Login</option>
<option value="sites">Seiten</option>
<option value="menue">Menue</option>
<option value="user">User</option>
<option value="syseinst">Konfiguration</option>
<option value="addon_conf">Add-on Einstellungen</option>
<option value="addon_inst">Add-on Installation</option>
<option value="other_filemanager">Filemanager</option>
<option value="other_themes">Themes</option>
<option value="other_level">Userlevel Backend</option>
<option value="other_umzug">Umzug</option>
<option value="other_lang">Mehrsprachige Seite</option>
<option value="other_easymenue">Easy Menue</option>';
//Auswahl Seiten
$sitecontent->add_html_header('<script>$(function(){ $( "[name=site]" ).val( "'.$site.'" ); }); </script>');
$sitecontent->add_site_content( '<select name="site" title="Auf welcher Seite des Backends soll ausgegeben werden?">'.$sitesopt.'</select>' );

$sitecontent->add_site_content('</td><td>');

//Eingabe Rechte
$sitecontent->add_site_content('<input type="text" size="20" name="recht" value="'.$recht.'"><br />');

$sitecontent->add_site_content('</tr><tr><td></td><td></td><td>');

//Text Rechte
$sitecontent->add_site_content('&uarr; Bitte geben Sie hier per Komma getrennte Userlevel (nur User mit diesen Rechten sehen dann die Ausgaben) an. Die einzelnen Werte können "more" &amp; "less" sein. Außderm können Sie die englischen Zahlen der Userlevel nehmen ("Other &rarr; Userlevel Backend"). Wenn alle die Meldungen sehen sollen, geben Sie bitte "no" an!<br />Beispiele: "more,less,one,two" oder "more,twenty,ten"');

$sitecontent->add_site_content('</td></tr></table>');
//Text Level der Einschränkungen
$sitecontent->add_site_content('<hr />Bitte beachten Sie, dass die Auswahl der Einbindungsstelle &uarr; über der Auswahl der Seite jedes einzelnen Blocks &darr; steht. Das heißt, Sie müssen bei Einbindung <code>Überall</code> wählen, wenn Sie eine Meldung bei <code>Home/Login</code> und einen HTML-Header bei <code>Umzug</code> brauchen.');
$sitecontent->add_site_content( '</div>');

// # Inhalte

//	Typen der Inhalte
$types = array(
	'header' => 'HTML Header',
	'mess' => 'Meldung',
	'cont' => 'Seiteninhalt',
	'php' => 'PHP-Code'
);
//	Vorgaben
$types_struc = array(
	'header' => array( 'site' => 'all', 'code' => '' ),
	'mess' => array( 'site' => 'all', 'inhalt' => '', 'heading' => '' ),
	'cont' => array( 'site' => 'all', 'inhalt' => '' ),
	'php' => array( 'site' => 'all', 'phpcode' => '' )
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
		$id = $html_out_be->next_kimb_id();
		//schreiben
		if( $html_out_be->write_kimb_id( $id, 'add', 'type', $type ) ){
			foreach( $types_struc[$type] as $teil => $val ){
				$html_out_be->write_kimb_id( $id, 'add', $teil, $val );
			}
			//Medlung
			$message[] = 'Es wurde ein neuer Block eingefügt!';
		}
	}

	//geändert?
	//	alles lesen
	$alles = $html_out_be->read_kimb_id_all();
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

				//erlaubt
				if( $ok ){
					//Übergabe nicht leer?
					if( isset( $newvals[$name] ) && !empty( $newvals[$name] ) ){
						//Änderungen?
						if( $newvals[$name] != $vals[$name] ){
							//sichern
							$html_out_be->write_kimb_id( $id, 'add', $name, $newvals[$name] );

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
	if( $html_out_be->write_kimb_id( $_GET['delblock'] , 'del' ) ){
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
$alles = $html_out_be->read_kimb_id_all();
$leer = true;
//alles durchgehen
foreach( $alles as $id => $vals ){
	//hinzufügen

	$sitecontent->add_site_content( '<div class="innerblock">' );

	//Typ
	$sitecontent->add_site_content( 'Typ: '.$types[$vals['type']].'<br />' );
	//Seite
	$sitecontent->add_html_header('<script>$(function(){ $( "select#blcsit_'.$id.'" ).val( "'.$vals['site'].'" ); }); </script>');
	$sitecontent->add_site_content( '<select name="blocksite['.$id.']" id="blcsit_'.$id.'" '.( isset( $vals['phpcode']) && !check_backend_login( 'no' , 'more', false ) ? 'disabled="disabled"' : '' ).'>'.$sitesopt.'</select><br />' );
	//Eingaben
	if( isset( $vals['heading']) ){
		//Input
		$sitecontent->add_site_content( 'Überschrift: <input type="text" name="blockvals['.$id.'][heading]" value="'.htmlspecialchars( $vals['heading'], ENT_COMPAT | ENT_HTML401,'UTF-8').'"><br />' );
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
