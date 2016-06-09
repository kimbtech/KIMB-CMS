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

//Daten von funcclass und fe_first laden
$felogin = $addon_felogin_array;

//Ist die aktuelle Seite die Seite für alle Loginsachen?
//Ist entweder Passwort vergessen, Account erstellen oder Usereinstellungen gewählt?
if( $_GET['id'] == $felogin['requid'] && ( isset( $_GET['pwforg'] ) || isset( $_GET['register'] ) || isset( $_GET['settings'] ) ) ){

	//Felogin Userdatei laden
	$felogin['userfile'] = new KIMBdbf( 'addon/felogin__user.kimb'  );
	//einen Stich zur Trennung einfügen (und eine ID für bessere Links)
	$sitecontent->add_site_content( '<hr id="goto"/>');

	//Usereinstellungen 
	//	Loginokay, IP, UserAgent prüfen
	//	Username gesetzt?
	if( isset( $_GET['settings'] ) && $felogin['loginokay'] == $_SESSION['felogin']['loginokay']  && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] && isset( $_SESSION['felogin']['user'] ) ){

		//Überschrift
		$sitecontent->add_site_content( '<h2>'.$allgsys_trans['addons']['felogin']['usereinst'].'</h2>');

		//Daten Übergeben?
		if( isset( $_POST['user'] ) ){

			//ID des Users herausfinden
			$id = $felogin['userfile']->search_kimb_xxxid( $_SESSION['felogin']['user'] , 'user' );

			//Name und Mail nicht leer?
			if( !empty( $_POST['name'] ) && !empty( $_POST['mail'] ) ){
				//Mail geändert?
				if( $_POST['mail'] != $felogin['userfile']->read_kimb_id( $id , 'mail' ) ){
					//Syntax prüfen
					//wurde die Adresse gesestet oder ist es ein Backend User, der die Änderungen durchführt?
					if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) && ( ( $_POST['mailcode'] == $_SESSION['mailcode'] && $_POST['mail'] == $_SESSION['email'] )|| $_SESSION['felogin_uch_okay'] == 'yes' ) ){
						//ändern
						if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'mail' , $_POST['mail'] ) ){
							//Medlung
							$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['mailch'].'</h3>');
						}
					}
					else{
						//wenn nicht valide, Hinweis
						$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['mailerr'].'</h3>');
					}
				}
				//Name geändert?
				if( $_POST['name'] != $felogin['userfile']->read_kimb_id( $id , 'name' ) ){
					//Namen ändern
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'name' , strip_tags( $_POST['name'] ) ) ){
						//Medlung
						$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['namech'].'</h3>');
					}
				}
				//Passwort übergeben?
				if( !empty( $_POST['passwort1'] ) ){
					//ändern
					//Hash des Passworts und neues Salt
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] ) && $felogin['userfile']->write_kimb_id( $id , 'add' , 'salt' , $_SESSION['newusersalt'] ) ){
						//Medlung
						$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['passch'].'</h3>');
					}
				}
			}
			else{
				//wenn Felder leer -> Fehler
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['fillnamail'].'</h3>');
			}
		}
		
		//neues Salt für Passwort des Users erstellen 
		$_SESSION['newusersalt'] = makepassw( 15, '', 'numaz' );
		
		//JavaScript Code 
		//	Übersetzungen der Medlungen hinter den Eingabefeldern für JS Code laden
		$header = '<script>var siteurl = "'.$allgsysconf['siteurl'].'";';
		foreach( $allgsys_trans['addons']['felogin']['regjs'] as $key => $val ){
			$header .= 'var '.$key.' = "'.$val.'"; ';
		}
		$header .= 'var newsalt = "'.$_SESSION['newusersalt'].'";';
		$header .= '</script>';
		
		$sitecontent->add_html_header( $header );
		
		//JavaScript Funktionen
		//	Passwörter prüfen
		//	Mailadresse Syntax prüfen
		//	Mailadresse Testmail senden
		//	Mailadresse Code prüfen
		//	Formular senden prüfen
		$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/felogin/settings.min.js"></script>');

		//ID des Users herausfinden
		$id = $felogin['userfile']->search_kimb_xxxid( $_SESSION['felogin']['user'] , 'user' );
		//User gefunden?
		if( $id != false ){
			//Daten des Users lesen
			$user = $felogin['userfile']->read_kimb_id( $id );

			//Formular
			$sitecontent->add_site_content('<form action="" method="post" onsubmit="return checksumbit();" ><br />');
			$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i>'.$allgsys_trans['addons']['felogin']['username'].'</i><br />');
			$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i>'.$allgsys_trans['addons']['felogin']['name'].'</i><br />');
			$sitecontent->add_site_content('<input type="text" name="mail" id="mail" onchange=" checkmail(); " value="'.$user['mail'].'" > <i id="mailadr">'.$allgsys_trans['addons']['felogin']['mailunverae'].'</i><br />');
			
			//Admins müssen die E-Mail-Adresse nicht validieren
			if( $_SESSION['felogin_uch_okay'] == 'yes' ){
				$sitecontent->add_site_content('<b>Da Sie ein Admin sind, können Sie den E-Mail-Code ignorieren!!</b><br />'."\r\n");
				$sitecontent->add_html_header('<script>$(function(){ $( "input#mailcode" ).val( "irrelevant" ); }); </script>');
			}
			
			//JavaScript und HTML um E-Mail-Adresse zu prüfen
			$sitecontent->add_site_content('<div id="mailcheck" style="display:none;">
			<input type="text" name="mailcode" id="mailcode" placeholder="'.$allgsys_trans['addons']['felogin']['mailcode'].'" onchange="checkcode();" >
			<span id="codeokay"><button onclick="sendcode(); return false;">'.$allgsys_trans['addons']['felogin']['mailcodesenden'].'</button></span></div>'."\r\n");
			
			//restliches Formular
			$sitecontent->add_site_content('<input type="text" name="gruppr" readonly="readonly" value="'.$user['gruppe'].'"> <i>'.$allgsys_trans['addons']['felogin']['gruppe'].'</i><br />');
			$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onkeyup=" checkpw(); " onchange=" checkpw(); "> <i id="pwtext">'.$allgsys_trans['addons']['felogin']['passunverae'].'</i> <br />');
			$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onkeyup=" checkpw(); "> <i id="pwtext">'.$allgsys_trans['addons']['felogin']['passunverae'].'</i> <br />');
			$sitecontent->add_site_content('<input type="submit" value="'.$allgsys_trans['addons']['felogin']['aen'].'" ><br />');
			$sitecontent->add_site_content('</form>');

		}
		else{
			//User nicht gefunden
			$sitecontent->add_site_content($allgsys_trans['addons']['felogin']['usernotex']);
		}

	}
	//Passwort vergessen?
	elseif( isset( $_GET['pwforg'] ) ){
		
		//Überschrift
		$sitecontent->add_site_content('<h2>'.$allgsys_trans['addons']['felogin']['pwforg'].'</h2>');

		//Username oder E-Mail-Adresse gegeben?
		if( isset( $_POST['user'] ) || isset( $_POST['mail'] ) ){

			//Username leer, aber Mail nicht?
			if( empty( $_POST['user'] ) && !empty( $_POST['mail'] ) ){
				//UserID suchen
				$id = $felogin['userfile']->search_kimb_xxxid( $_POST['mail'] , 'mail' );
				//ok
				$ok = 'ok';

			}
			//Username nicht leer, aber Mail?
			elseif( !empty( $_POST['user'] ) && empty( $_POST['mail'] )){
				//UserID suchen
				$id = $felogin['userfile']->search_kimb_xxxid( $_POST['user'] , 'user' );
				//ok
				$ok = 'ok';

			}
			else{
				//Fehler wenn beide gefüllt!
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['fillone'].'</h3>');
				$ok = 'nok';
			}

			//ok?
			if( $ok == 'ok' ){
				//User gefunden?
				if( $id != false ){
					//neues Passwort machen
					$newpass = makepassw( 10 );
					//Salt für Passwort machen
					$passsalt = makepassw( 15, '', 'numaz' );
					//Code machen
					$setnewcode = makepassw( 30, '', 'numaz' );
					//neues Passwort und Code in der dbf sichern
					//	das Passwort wird erst durch eine Link in einer E-Mail aktiviert, vorher ist das alte Passwort gültig
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'newpassw' , sha1( $passsalt.$newpass ) ) && $felogin['userfile']->write_kimb_id( $id , 'add' , 'newsalt' , $passsalt ) && $felogin['userfile']->write_kimb_id( $id , 'add' , 'setnewcode' , $setnewcode ) && $felogin['userfile']->write_kimb_id( $id , 'add' , 'newpasswtime' , time() ) ){

						//Im vorgefertigten Text der E-Mail Platzhalter ersetzen 
						$inhalt = str_replace( array( '%name%', '%pass%', '%url%', '%sitename%' , '%br%' ) , array( $felogin['userfile']->read_kimb_id( $id , 'name' ), $newpass, $allgsysconf['siteurl'].'/ajax.php?addon=felogin&newpassak='.$id.'&code='.$setnewcode , $allgsysconf['sitename'], "\r\n" ) , $allgsys_trans['addons']['felogin']['mailtext']['newpass'] );

						//E-Mail senden
						send_mail( $felogin['userfile']->read_kimb_id( $id , 'mail' ) , $inhalt );
					}
				}
				//Medlung, dass User nach Mails gucken soll (User soll nicht wissen ob Username/ E-Mail-Adresse vorhanden)
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['mailsee'].'</h3>');
			}
		}

		//Formular
		//	Infotext
		$sitecontent->add_site_content($allgsys_trans['addons']['felogin']['textpwforg'].'<br />');
		$sitecontent->add_site_content('<form action="" method="post" >');
		//	Username oder E-Mail-Adresse
		$sitecontent->add_site_content('<input type="text" name="mail" placeholder="'.$allgsys_trans['addons']['felogin']['mail'].'" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['mail'].') <![endif]-->');
		$sitecontent->add_site_content('<input type="text" name="user" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['username'].') <![endif]--><br />');
		$sitecontent->add_site_content('<input type="submit" value="'.$allgsys_trans['addons']['felogin']['sendnewpass'].'" ><br />');
		$sitecontent->add_site_content('</form>');

	}
	//neuen Account erstellen
	//	nur wenn in Konfiguration erlaubt oder User nach Session BE-Admin
	elseif( isset( $_GET['register'] ) && ( $felogin['selfreg'] == 'on' || $_SESSION['registerokay'] == 'yes' ) ){

		//Überschrift
		$sitecontent->add_site_content('<h2>'.$allgsys_trans['addons']['felogin']['register'].'</h2>');

		//Daten übergeben?
		if( isset( $_POST['captcha_code'] ) ){
			//alle Eingaben okay
			if( !empty( $_POST['user'] ) && !empty( $_POST['name'] ) && !empty( $_POST['mailcode'] ) && !empty( $_POST['passwort1'] ) && !empty( $_POST['captcha_code'] ) && $_POST['akzep'] == 'ok'  ){

				//wenn User ein BE-Admin ist, muss die E-Mail nicht überprüft worden sein und das Captcha ist egal 
				if( $_SESSION['registerokay'] == 'yes' ){
					//Session anpassen, damit es keine Fehler gibt
					$_POST['mailcode'] = $_SESSION["mailcode"];
					$_REQUEST['captcha_code'] = $_SESSION['captcha_code'];
					$_SESSION['email'] = $_POST['mail'];
				}

				//Usernamen säubern
				$_POST['user'] = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $_POST['user'] ) );
				//nach User suchen
				$uid = $felogin['userfile']->search_kimb_xxxid( $_POST['user'] , 'user' );
				//wenn User nicht gefunden und Mailcode sowie captcha okay
				if( $uid == false && $_POST['mailcode'] == $_SESSION["mailcode"] && check_captcha() && !empty( $_SESSION['email'] ) ){
					
					//User erstellen
					
					//Gruppe für neuen User herausfinden
					$gruppe = $felogin['conf']->read_kimb_one( 'selfreggruppe' );

					//freie ID für User
					$id = $felogin['userfile']->next_kimb_id();

					//ID des Users der IDliste anfügen
					$felogin['userfile']->write_kimb_teilpl( 'userids' , $id , 'add' );

					//Daten über den User in die dbf schreiben
					//	Passwort (sha1)
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] );
					//	Salt für Passwort
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'salt' , $_SESSION['newusersalt'] );
					//	Gruppe (wie oben gefunden)
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'gruppe' , $gruppe );
					//	Namen
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'name' , strip_tags( $_POST['name'] ) );
					//	E-Mail-Adresse aus Session
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'mail' , $_SESSION['email'] );
					//	Username
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'user' , $_POST['user'] );
					//	Status erstmal auf "on"
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'status' , 'on' );

					//Medlung, dass Account erstellt
					$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['newokay'].'</h3>');
					//Infos zum Account
					$sitecontent->add_site_content( '<ul><li>'.$allgsys_trans['addons']['felogin']['mail'].': '.$_SESSION['email'].'</li><li>'.$allgsys_trans['addons']['felogin']['username'].': '.$_POST['user'].'</li><li>'.$allgsys_trans['addons']['felogin']['name'].': '.$_POST['name'].'</li></ul>' );

					//wenn gewünscht, eine Mail zum Admin senden
					if( $felogin['conf']->read_kimb_one( 'infomail' ) == 'on' ){
						
						//Text laden und Platzhalter ersetzen
						$inhalt = str_replace( array( '%sitename%' , '%br%', '%userna%' ) , array( $allgsysconf['sitename'], "\r\n", $_POST['user'] ) , $allgsys_trans['addons']['felogin']['mailtext']['newuseradm'] );
						
						send_mail( $allgsysconf['adminmail'] , $inhalt );
					}

				}
				else{
					//Fehler wenn Datenprüfung nicht okay
					$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['eingerr'].'</h3>' );
				}
			}
			else{
				//Fehler wenn Felder leer
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['fillall'].'</h3>');
			}
		}
		//keine Daten, also Formular
		else{
			//Das Formular testet alle Eingaben per JavaScript, damit kein User seine Eingaben verliert

			//neues Salt für Passwort des Users erstellen 
			$_SESSION['newusersalt'] = makepassw( 15, '', 'numaz' );

			//Übersetzungen für die externe JavaScript Datei laden
			$header = '<script>var siteurl = "'.$allgsysconf['siteurl'].'";';
			foreach( $allgsys_trans['addons']['felogin']['regjs'] as $key => $val ){
				$header .= 'var '.$key.' = "'.$val.'"; ';
			}
			$header .= 'var newsalt = "'.$_SESSION['newusersalt'].'";';
			$header .= '</script>';
		
			$sitecontent->add_html_header( $header );
			
			//externe JavaScript Datei
			$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/addondata/felogin/register.min.js" type="text/javascript" ></script>');

			//Infotext
			$sitecontent->add_site_content($allgsys_trans['addons']['felogin']['textreg'].'<br /><br />'."\r\n");
			//Formular beginn
			$sitecontent->add_site_content('<form action="#goto" method="post" onsubmit="return checksubmit(); " >'."\r\n");
			//Tabelle
			$sitecontent->add_site_content('<table width="100%;">'."\r\n");
			//	Username (Überprüfung ob schon vorhanden)
			$sitecontent->add_site_content('<tr><td><input type="text" name="user" id="user" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" onchange=" checkuser(); " ></td> <td colspan="2"><i id="usertext">Username -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i></td></tr>'."\r\n");
			//	Name (darf nicht leer sein)
			$sitecontent->add_site_content('<tr><td><input type="text" name="name" id="name" placeholder="'.$allgsys_trans['addons']['felogin']['name'].'" onkeyup=" checkname(); " onblur=" checkname(); "></td> <td colspan="2" ><i id="nametext">'.$allgsys_trans['addons']['felogin']['name'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i></td></tr>'."\r\n");
			//	E-Mail-Adresse (Testmail mit Code)
			$sitecontent->add_site_content('<tr><td><input type="text" name="mail" id="mail" placeholder="'.$allgsys_trans['addons']['felogin']['mail'].'" onkeyup="checkmail();" onchange="checkmail();" ></td> <td colspan="2" ><i id="mailtext">'.$allgsys_trans['addons']['felogin']['mail'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i></td></tr>'."\r\n");
			//	kommt wenn valide E-Mail-Adresse angegeben
			//	Prüfung des Codes
			$sitecontent->add_site_content('<tr style="display:none;" id="mailcodeinput" ><td><input type="text" name="mailcode" id="mailcode" placeholder="'.$allgsys_trans['addons']['felogin']['mailcode'].'" onchange=" checkcode(); " ></td> <td style="min-width:120px;"><i id="mailcodetext"><button onclick=" sendcode(); ">'.$allgsys_trans['addons']['felogin']['mailcodesenden'].'</button></i><button style="display:none" id="nochmalcode" onclick=" sendcode(); ">'.$allgsys_trans['addons']['felogin']['mailcodesendenneu'].'</button></td> <td>'.$allgsys_trans['addons']['felogin']['mailcodetext'].'</td></tr>'."\r\n");
			//	Passwort doppelt eingeben und vergleichen
			$sitecontent->add_site_content('<tr><td><input type="password" name="passwort1" id="passwort1" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" onkeyup=" checkpw(); " onblur=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">'.$allgsys_trans['addons']['felogin']['passwort'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i> </td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="password" name="passwort2" id="passwort2" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" onkeyup=" checkpw(); " onblur=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">'.$allgsys_trans['addons']['felogin']['passwort'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i> </td></tr>'."\r\n");
			
			//Admins müssen das Captcha und den Mailcode nicht eingeben
			if( $_SESSION['registerokay'] == 'yes' ){
				$sitecontent->add_site_content('<tr><td colspan="3"><b>Da Sie ein Admin sind, können Sie das Captcha und den E-Mail-Code ignorieren!!</b><input type="hidden" id="checku" value="nok"><input type="hidden" id="checkc" value="ok"><input type="hidden" id="checkm" value="ok"></td></tr>'."\r\n");
				$sitecontent->add_html_header('<script>$(function(){ $( "input#mailcode" ).val( "irrelevant" ); $( "input[name=captcha_code]" ).val( "irrelevant" ); }); </script>');
			}
			else{
				$sitecontent->add_site_content('<tr><td colspan="3"><input type="hidden" id="checku" value="nok"><input type="hidden" id="checkc" value="nok"><input type="hidden" id="checkm" value="nok"></td></tr>'."\r\n");
			}
			
			//Captcha anzeigen
			$sitecontent->add_site_content('<tr><td>'.make_captcha_html().'</td><td id="captchatd"><i id="captchatext">'.$allgsys_trans['addons']['felogin']['captcha'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i> </td><td>'.$allgsys_trans['addons']['felogin']['captchatext'].'</td></tr>'."\r\n");
			$sitecontent->add_site_content('</table>');

			//Text/ AGB die akzeptiert werden müssen
			$felogin['akzepttext'] = $felogin['conf']->read_kimb_one( 'akzepttext' );
			if( $_SESSION['registerokay'] == 'yes' ){
				//Admin muss das nicht
				$sitecontent->add_site_content('<input type="checkbox" name="akzep" id="akzep" value="ok" checked="checked">'.$felogin['akzepttext'].'<br />');
			}
			else{
				//andere schon
				$sitecontent->add_site_content('<input type="checkbox" name="akzep" id="akzep" value="ok">'.$felogin['akzepttext'].'<br />');
			}
			
			//Button und Formular Ende
			$sitecontent->add_site_content('<br /><input type="submit" value="'.$allgsys_trans['addons']['felogin']['registerbutton'].'" ><br />');
			$sitecontent->add_site_content('</form>');
		}
	}

	//Ende der Loginsachen von felogin
	$sitecontent->add_site_content( '<hr id="end"/>');
}

//Alle Variablen löschen
unset( $felogin, $addon_felogin_array );

?>
