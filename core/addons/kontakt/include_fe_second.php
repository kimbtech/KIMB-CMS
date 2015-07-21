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

//nur wenn User Seite sehen darf anzeigen
if( $allgerr != '403'){
	
	//alles in div packen (so ist CSS nutzbar)
	$sitecontent->add_site_content( '<div class="addon_kontakt">');
	//dbf laden
	$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );
	//jQuery
	$sitecontent->add_html_header('<!-- jQuery -->');	

	//sicherer Text aktiviert?
	if( $kontakt['file']->read_kimb_one( 'other' ) == 'on' ){
		
		//Code für sicheren Text erstellen
		$_SESSION['kontakt_code'] = makepassw( 10, '', 'numaz');

		//sicheren Text automatisch per JavaScript in p laden (Bots haben oft kein JS und wenn, dann verstehen Sie die unicode-Codierung nicht)
		$sitecontent->add_html_header('<script>$( function(){ $.get( "'.$allgsysconf['siteurl'].'/ajax.php?addon=kontakt&code='.$_SESSION['kontakt_code'].'" , function( data ) { $( "p.kontakt_other" ).html( data ); }).fail(function() { $( "p.kontakt_other" ).html( "'.$allgsys_trans['addons']['kontakt']['fehlabfr'].'" ); }); }); </script>');

		//p für sicheren Text
		$sitecontent->add_site_content( '<p class="kontakt_other">'.$allgsys_trans['addons']['kontakt']['jsother'].'</p>' );
	}

	//E-Mail-Adresse anzeigen?
	if( $kontakt['file']->read_kimb_one( 'mail' ) == 'on' ){

		//p mit Bild der E-Mail-Adresse
		$sitecontent->add_site_content( '<p class="kontakt_mailadr">'.$allgsys_trans['addons']['kontakt']['mail'].': <img style="vertical-align:middle; border:none;" src="'.$allgsysconf['siteurl'].'/load/addondata/kontakt/'.$kontakt['file']->read_kimb_one( 'bildname' ).'.png" title="'.$allgsys_trans['addons']['kontakt']['mail'].'" alt="'.$allgsys_trans['addons']['kontakt']['mail'].'"></p>' );
		
	}

	//Kontaktformular anzeigen?
	//	Empfänger darf nicht leer sein!
	if( $kontakt['file']->read_kimb_one( 'form' ) == 'on' && !empty( $kontakt['file']->read_kimb_one( 'formaddr' ) ) ){

		//Überschrift nach Strich
		$sitecontent->add_site_content( '<hr /><h2>'.$allgsys_trans['addons']['kontakt']['konth'].'</h2>' );
		
		//in div packen (CSS nutzbar)
		$sitecontent->add_site_content( '<div class="kontakt_formular">');

		//sind Daten übergeben?
		if( isset( $_POST['kontakt_cont'] ) ){
			
			//Ist felogin vorhanden
			if( function_exists( 'check_felogin_login' ) ){
				//ist User eingeloggt und darf er Seite sehen?
				if( check_felogin_login( '---session---', '---allgsiteid---', true ) ){
					//wenn dass der Fall ist, muss der User keinen Namen, keine Mail und keinen Captcha-Code eingeben
					//	Captcha so manipulieren, dass check_captcha() true zurückgibt
					$_SESSION['captcha_code'] =makepassw( mt_rand(5, 7) , '', 'numaz');
					$_REQUEST['captcha_code'] = $_SESSION['captcha_code'];
					//	Pseudo-E-Mail-Adresse setzen (Verweis auf Felogin-User)
					$_POST['kontakt_mail'] = $_SESSION['felogin']['user'].'@feloginuser.sys';
					//	Name des Felogin-Users verwenden
					$_POST['kontakt_name'] = $_SESSION['felogin']['name'];
				}
				//wenn felogin vorhanden, wird die Pseudo-Domain 'feloginuser.sys' auf felogin-User beschränkt 
				elseif( substr( $_POST['kontakt_mail'], -16 ) == '@feloginuser.sys' ){
					$_POST['kontakt_mail'] = substr( $_POST['kontakt_mail'], 0, -16 ).'@nofelogin.sys';
				}
			}
			
			//Übergaben prüfen
			//	Captcha
			//	Name, Mail und Mitteilung nicht leer?
			if( check_captcha() && !empty( $_POST['kontakt_name'] ) && !empty( $_POST['kontakt_mail'] ) && !empty( $_POST['kontakt_cont'] ) ) {
			
				//E-Mail-Adresse valide?
				if( filter_var( $_POST['kontakt_mail'] , FILTER_VALIDATE_EMAIL) ){

					//alle HTML-Tags weg, Umlaute usw. zu HTML-Code (HTML-Mail wird versendet)
					$_POST['kontakt_name'] = htmlentities( strip_tags( $_POST['kontakt_name'] ) , ENT_COMPAT | ENT_HTML401,'UTF-8');
					//alle HTML-Zeichen codieren, ander Tags weg, Zeilenumbrüche zu br's'
					$_POST['kontakt_cont'] = nl2br( strip_tags( htmlentities( $_POST['kontakt_cont'] , ENT_COMPAT | ENT_HTML401,'UTF-8') ) );
		
					//Text laden und Platzhalter ersetzen
					$platzhalter = array( '%sitename%' , '%br%', '%name%', '%mail%', '%cont%', '%zeit%' );
					$ersetzungen = array( $allgsysconf['sitename'], "\r\n", $_POST['kontakt_name'], $_POST['kontakt_mail'], $_POST['kontakt_cont'], date( "d:m:Y H:i:s" ));
					$inhalt = str_replace( $platzhalter, $ersetzungen, $allgsys_trans['addons']['kontakt']['mailtext']['infomail'] );
	
					//E-Mail senden
					if( send_mail( $kontakt['file']->read_kimb_one( 'formaddr' ) , $inhalt, 'html') ){
						//wenn okay, Meldung
						$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['kontakt']['dank'].'</h3>' );
	
						//local.Storage leeren
						$sitecontent->add_html_header('<script>$( function(){ localStorage.removeItem( "kontakt_name" ); localStorage.removeItem( "kontakt_mail" ); localStorage.removeItem( "kontakt_cont" ); });</script>');
						//jetzt kein Formular anzeigen
						$makeform = false;
					}
					else{
						//Fehler bei der Senden
						$sitecontent->add_site_contentr( '<h3>'.$allgsys_trans['addons']['kontakt']['mailerr'].'</h3>' );
						//Formular anzeigen
						$makeform = true;
					}
				}
				else{
					//Fehler bei der E-Mail-Adresse
					$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['kontakt']['mailerr'].'</h3>' );
					//Formular anzeigen
					$makeform = true;
				}
			}
			else{
				//Fehler beim Captcha oder eine Eingabe ist leer
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['kontakt']['caerr'].'</h3>' );
				//Formular anzeigen
				$makeform = true;
			}
		}
		else{
			//keine Übergaben, Formular zeigen
			$makeform = true;
		}
		
		//soll Formular gezeigt werden?
		if( $makeform ){

			//alte Eingabewert aus dem localStorage laden
			//Funktion um Werte beim "submit" zu sichern
			$sitecontent->add_html_header('<script>$( function(){ $( "input#kontakt_name" ).val( localStorage.getItem( "kontakt_name" ) ); $( "input#kontakt_mail" ).val( localStorage.getItem( "kontakt_mail" ) ); $( "textarea#kontakt_cont" ).val( localStorage.getItem( "kontakt_cont" ) ); }); function kontakt_savesubmit () { localStorage.setItem( "kontakt_name", $( "input#kontakt_name" ).val() ); localStorage.setItem( "kontakt_mail", $( "input#kontakt_mail" ).val() ); localStorage.setItem( "kontakt_cont", $( "textarea#kontakt_cont" ).val() ); return true; }</script>');

			//Formular beginnen
			$sitecontent->add_site_content('<form action="" method="post" onsubmit="kontakt_savesubmit();" >');
			
			//Ist felogin nicht vorhanden?
			//Ist der User nicht eingeloggt?
			if( !function_exists( 'check_felogin_login' ) || !check_felogin_login( '---session---', '---allgsiteid---', true ) ){
				//User muss Namen und Mail angeben
								
				//Name
				$sitecontent->add_site_content('<input name="kontakt_name" type="text" id = "kontakt_name" placeholder="'.$allgsys_trans['addons']['kontakt']['name'].'" > <!--[if lt IE 10]> ('.$allgsys_trans['addons']['kontakt']['name'].') <![endif]--> <br />');
				//Mail
				$sitecontent->add_site_content('<input name="kontakt_mail" type="text" id = "kontakt_mail" placeholder="'.$allgsys_trans['addons']['kontakt']['mail'].'" > <!--[if lt IE 10]> ('.$allgsys_trans['addons']['kontakt']['mail'].') <![endif]--> <br />');
			}
			//Texteingabe
			$sitecontent->add_site_content('<textarea name="kontakt_cont" id="kontakt_cont" placeholder="'.$allgsys_trans['addons']['kontakt']['mitt'].'" style="width:75%; height:200px;" ></textarea> <!--[if lt IE 10]> ('.$allgsys_trans['addons']['kontakt']['mitt'].') <![endif]--> <br />');
			//Ist felogin nicht vorhanden?
			//Ist der User nicht eingeloggt?
			if( !function_exists( 'check_felogin_login' ) || !check_felogin_login( '---session---', '---allgsiteid---', true ) ){
				//User muss Captcha lösen
				$sitecontent->add_site_content( make_captcha_html() );
				//Infotext
				$sitecontent->add_site_content('('.$allgsys_trans['addons']['kontakt']['catext'].')<br />');
			}
			//Senden Button
			$sitecontent->add_site_content('<input type="submit" value="'.$allgsys_trans['addons']['kontakt']['send'].'"></form>');

		}
		
		//div's beenden
		$sitecontent->add_site_content( '</div>');
	}

	$sitecontent->add_site_content( '</div>');
	
	unset( $kontakt );
}
?>
