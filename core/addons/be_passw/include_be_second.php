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

//nur wenn User nicht eingeloggt sinnvoll 
if( $_SESSION['loginokay'] != $allgsysconf['loginokay'] ){
	
	//JavaScript um Overlay anzuzeigen / auszubelenden
	$sitecontent->add_html_header( '<script>
	function showbe_passw(){
		$( "div.be_passw_over" ).css( "display", "block" );
		return false;	
	}
	function hidebe_passw(){
		$( "div.be_passw_over" ).css( "display", "none" );
		return false;	
	}
</script>' );
	
	//Overlay Code
	$sitecontent->add_site_content( '<div class="ui-overlay be_passw_over"><div class="ui-widget-overlay"></div>');
	$sitecontent->add_site_content( '<div class="ui-widget-shadow ui-corner-all" style="width: 520px; height: 140px; position: absolute; left: 255px; top: 15px; margin:0;"></div></div>');
	$sitecontent->add_site_content( '<div style="width: 500px; height: 120px; position: absolute; left: 260px; top: 20px; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all be_passw_over">');
	$sitecontent->add_site_content( '<h2>Backend Passwort vergessen?</h2>' );

	//Username oder E-Mail gegeben?
	if( !empty( $_POST['usern'] ) || !empty( $_POST['mail'] ) ){
		
		//Userliste laden		
		$userfile = new KIMBdbf('backend/users/list.kimb');

		//wenn Username gegeben, nach User suchen
		if( !empty( $_POST['usern'] ) ){
			$id = $userfile->search_kimb_xxxid( $_POST['usern'] , 'user' );
		}
		//sonst nach E-Mail suchen
		else{
			$id = $userfile->search_kimb_xxxid( $_POST['mail'] , 'mail' );
		}

		//wenn User gefunden
		if( $id != false ){
			
			//Userdaten lesen
			$udata = $userfile->read_kimb_id( $id );
		
			//Die Passwort ändern Methode gibt eine Meldung bei Erfolg aus
			//diese soll nur in einem Klon des Ausgabeobjekt erscheinen 	
			$sitecontent_dummy = clone $sitecontent;
		
			//Backend-Klasse User laden
			$class = new BEuser(  $allgsysconf, $sitecontent_dummy, false );
			//Passwort ändern
			//	Rückgabe neues Passwort oder false
			$newpass = $class->make_user_changepw( $udata['user'] );
			
			//Klon des Ausgabeobjekts wieder löschen
			unset($sitecontent_dummy);
			
			//wenn neues Passwort erfolgreich gesetzt
			if( $newpass !== false ){
			
				//E-Mail Text erstellen
				$text = 'Hallo '.$udata['name'].','."\r\n".'Ihr neues Backendpasswort lautet:'."\r\n";
				$text .= $newpass."\r\n\r\n";
				$text .= $allgsysconf['sitename'];
				
				//E-Mail versenden
				send_mail( $udata['mail'] , $text );
			}
		}

		//User informieren, dass etwas gemacht wurde
		$sitecontent->add_site_content( 'Sofern Ihr Username/ Ihre E-Mail-Adresse in der Datenbank gefunden wurde, haben Sie ein neues Passwort per E-Mail erhalten!' );			
	}
	else{
		//normale Aufrufe der Login-Seite laden hier, Overlay erstmal ausbleden
		$sitecontent->add_html_header( '<style>div.be_passw_over{ display:none; }</style>');

		//Formular im Overlay anzeigen
		$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/index.php" method="post" >' );
		$sitecontent->add_site_content( '<input type="text" name="mail" placeholder="E-Mail-Adresse" ><!--[if lt IE 10]>(E-Mail-Adresse)<![endif]--> oder' );
		$sitecontent->add_site_content( '<input type="text" name="usern" placeholder="Username" ><!--[if lt IE 10]>(Username)<![endif]--><br />' );
		$sitecontent->add_site_content( '<input type="submit" value="Neues Passwort zusenden" >');
		$sitecontent->add_site_content( '</form>' );
	}
	
	//Ende des Overlays
	$sitecontent->add_site_content( '<hr /><button onclick="return hidebe_passw()" >Schließen</button>' );
	$sitecontent->add_site_content( '</div>' );
	
	//Passwort vergessen Button, der Overlay anzeigt
	$sitecontent->add_site_content( '<button onclick="return showbe_passw();">Passwort vergessen?</button>' );
}
?>
