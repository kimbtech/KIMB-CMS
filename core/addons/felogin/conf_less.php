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
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=felogin';
$feconf = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$feuser = new KIMBdbf( 'addon/felogin__user.kimb'  );

//Userliste und Usereinstellungen

//User löschen?
if( isset( $_GET['del'] ) && is_numeric( $_GET['uid'] ) ){

	//Ist der User vorhanden?
	$user = $feuser->read_kimb_id( $_GET['uid'] , 'user' );

	if( !empty( $user ) ){
		
		//ja -> Aus Liste löschen & gesamte ID löschen
		$feuser->write_kimb_id( $_GET['uid'] , 'del' );
		$feuser->write_kimb_teilpl( 'userids' , $_GET['uid'] , 'del' );

		//Meldung
		$sitecontent->echo_message( 'Users gelöscht!' );
	}
	else{
		//Fehler wenn nicht vorhanden
		$sitecontent->echo_error( 'Der User existiert nicht!' , 'unknown' );
	}


}
//Status ändern?
elseif( isset( $_GET['deakch'] ) && is_numeric( $_GET['uid'] ) ){

	//aktuellen Status lesen
	$status = $feuser->read_kimb_id( $_GET['uid'] , 'status' );

	//wenn leer -> dann User nicht vorhanden
	if( !empty( $status ) ){
		if( $status == 'on' ){
			//wenn an -> dann jetzt aus
			$feuser->write_kimb_id( $_GET['uid'] , 'add' , 'status' , 'off' );
		}
		else{
			//wenn aus -> dann jetzt an
			$feuser->write_kimb_id( $_GET['uid'] , 'add' , 'status' , 'on' );
		}
		//Meldung
		$sitecontent->echo_message( 'Status eines Users verändert!' );
	}
	else{
		//User nicht gefunden
		$sitecontent->echo_error( 'Der User existiert nicht!' , 'unknown' );
	}

}
//Gruppenzugehörigkeit ändern?
elseif( isset( $_POST['gruppen'] ) ){

	//alle Übergaben durchgehen
	$i = '1';
	while( $i <= $_POST['gruppen'] ){

		//aktuellen Stand lesen
		$gruppe = $feuser->read_kimb_id( $i , 'gruppe' );
		if( $gruppe != $_POST[$i] ){
			if( !empty( $_POST[$i] ) ){
				//wenn verändert und neuer Wert nicht leer -> Änderung 
				$feuser->write_kimb_id( $i , 'add' , 'gruppe' , $_POST[$i] );
				//Medlung
				$sitecontent->echo_message( 'Gruppe eines Users verändert!' );
			}
		}		
		$i++;
	}

}

//JavaScript für Löschen Dialog
$sitecontent->add_html_header('<script>
var del = function( id ) {
	$( "#del-feloginuser" ).show( "fast" );
	$( "#del-feloginuser" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$addonurl.'&del&uid=" + id;
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

//Formualr mit allen Usern
$sitecontent->add_site_content('<form method="post" action="'.$addonurl.'" >');

//Tabelle beginnen
$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&amp;newuser#goto" target="_blank" ><span class="ui-icon ui-icon-plusthick" title="Einen neuen User erstellen. ( Sie werden dafür auf die Registrieren-Seite des Frontends weitergeleitet. )"></span></a>');
$sitecontent->add_site_content('<table width="100%"><tr> <th>Username</th> <th>Name</th> <th>Gruppe</th> <th>Status</th> <th>Löschen</th> </tr>');

//alle UserIDs lesen
$users = $feuser->read_kimb_all_teilpl( 'userids' );

//Liste noch leer
$liste = 'no';

//Dropdown mit allen Gruppen erstellen
//	Gruppen lesen
$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );
foreach( $gruppen as $gr ){
	//Optionen hinzufügen
	$grdown .= '<option value="'.$gr.'" >'.$gr.'</option>';
}

//alle User durchgehen
foreach( $users as $id ){
	//alles über den User auslesen
	$user = $feuser->read_kimb_id( $id );

	//Link zum Bearbeiten erstellen
	//	Die Bearbeitung findet im FE statt (Session wird per /ajax.php gesetzt)
	$link = '<a title="User bearbeiten (Sie werden dafür auf die Einstellungen-Seite des Frontends weitergeleitet.)" href="'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&amp;settings='.$user['user'].'#goto" target="_blank" >'.$user['user'].'</a>';

	//Status X und V
	if ( $user['status'] == 'on' ){
		$status = '<a href="'.$addonurl.'&amp;deakch&amp;uid='.$id.'"><span class="ui-icon ui-icon-check" title="Dieser User ist zu Zeit aktiviert. ( click -> ändern )"></span></a>';
	}
	else{
		$status = '<a href="'.$addonurl.'&amp;deakch&amp;uid='.$id.'"><span class="ui-icon ui-icon-close" title="Dieser User ist zu Zeit deaktiviert. ( click -> ändern )"></span></a>';
	}

	//Mülltonne für Löschen Dialog
	$del = '<span onclick="var delet = del( '.$id.' ); delet();"><span class="ui-icon ui-icon-trash" title="Diesen User löschen."></span></span>';

	//Dropdown für User hinzufügen
	$grupp = '<select name="'.$id.'">'.$grdown.'</select>';
	//aktuelle Gruppe per JS wählen
	$js .= '$( "[name='.$id.']" ).val( "'.$user['gruppe'].'" );';

	//Tabellenzeile hinzufügen
	$sitecontent->add_site_content('<tr> <td>'.$link.'</td> <td>'.$user['name'].'</td> <td>'.$grupp.'</td> <td>'.$status.'</td> <td>'.$del.'</td> </tr>');

	//jetzt User vorhanden
	$liste = 'yes';
}

$sitecontent->add_site_content('</table>');

//Formular beenden
$sitecontent->add_site_content('<input type="hidden" name="gruppen" value="'.$id.'" ><input type="submit" value="Gruppen anpassen" title="Speichern von Veränderungen an der Gruppenzugehörigkeit." ></form>');
$sitecontent->add_site_content('<button onclick="window.location = \''.$addonurl.'\';" >Ansicht aktualisieren</button>');

//den gesamten JS Code hinzufügen
$sitecontent->add_html_header('<script>$(function(){ '.$js.' }); </script>');

//wenn Userliste leer
if( $liste == 'no' ){
	//Medlung
	$sitecontent->echo_error( 'Es sind keine Frontenduser vorhanden!' , 'unknown' );
}

//HTML für Löschen Dialog
$sitecontent->add_site_content('<div style="display:none;"><div id="del-feloginuser" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie den User wirklich löschen?</p></div></div>');

?>
