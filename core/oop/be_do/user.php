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

//Diese Klasse ist für alle Aufgaben rund um die User des Backend zuständig.
//	nur für Backenduser!!

class BEuser{
	
	//Klasse init.
	protected $allgsysconf, $sitecontent, $userfile;
	
	public function __construct( $allgsysconf, $sitecontent, $tabelle = true ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		$this->userfile = new KIMBdbf('backend/users/list.kimb');
		
		if( is_object( $this->sitecontent ) && $tabelle ){
			$this->sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		}
	}
	
	//Neuen User erstellen
	public function make_user_new_dbf( $name, $passwort, $salt, $user, $level,$mail ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		//Name leer -> Vorgabe verwenden
		if( empty( $name ) ){
			$name = 'Herr Franz Mustermann';
		}

		//Usernamen reinigen
		$user = preg_replace( "/[^a-z]/" , "" , strtolower( $user ) );
		//User vorhanden?
		$id = $userfile->search_kimb_xxxid( $user , 'user' );
		if( $id == false ){
			$username = $user;
		}
		else{
			//das darf nicht sein -> Fehler
			return false;
		}

		//Level richtig angegeben (Systemsprzifische Level sind erst beim Bearbeiten eines Users wählbar)
		if( $level == 'more' || $level == 'less' ){
			$permiss = $level;
		}
		else{
			return false;
		}

		//E-Mail Adresse okay?
		if( !filter_var($mail, FILTER_VALIDATE_EMAIL) ) {
			return false;
		}

		//freie UserID suchen
		$id = $userfile->next_kimb_id();

		//UserID der ID Liste hinzufügen
		$userfile->write_kimb_teilpl( 'userids' , $id , 'add' );

		//Alle Userdaten in dbf speichern
		$userfile->write_kimb_id( $id , 'add' , 'passw' , $passwort );
		$userfile->write_kimb_id( $id , 'add' , 'salt' , $salt );
		$userfile->write_kimb_id( $id , 'add' , 'permiss' , $permiss );
		$userfile->write_kimb_id( $id , 'add' , 'name' , $name );
		$userfile->write_kimb_id( $id , 'add' , 'mail' , $mail );
		$userfile->write_kimb_id( $id , 'add' , 'user' , $username );
		
		//Daten zurückgeben
		return array( 'name' => $name, 'passwort' => $passwort, 'salt' => $salt, 'username' => $username, 'permiss' => $permiss, 'mail' => $mail  );
	}
	
	//Neuen User erstellen - Eingabemaske 
	public function make_user_new(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>User erstellen</h2>');

		//Daten übertragen ?
		if( !empty( $_POST['user'] ) ){
	
			//Daten für Methode vorbereiten
			$passwort = $_POST['passwort1'];
			$name = $_POST['name'];
			$salt = $_POST['salt'];
			$user = $_POST['user'];
			$level = $_POST['level'];
			$mail = $_POST['mail'];
			
			//Methode ausführen
			$ret = $this->make_user_new_dbf( $name, $passwort, $salt, $user, $level,$mail );
			
			//wenn okay User bearbeiten aufrufen
			if( is_array( $ret ) ){
				open_url( '/kimb-cms-backend/user.php?todo=edit&user='.$ret['username'] );
				die;
			}
			else{
				//Fehlermeldung
				$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' );
			}
		}
	
		//JavaScript
		$this->jsobject->for_user_new();
	
		//Eingabeformular
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new" method="post" onsubmit="return checksumbit();"><br />');
		$sitecontent->add_site_content('<input type="text" name="user" onkeyup=" userreplace(); " onchange=" checkuser(); " id="user"> <i id="textuser" title="Username für das Login ( später keine Änderung möglich )">Username - bitte eingeben</i><br />');
		$sitecontent->add_site_content('<input type="text" name="name" > <i title="Name des Users" >Name</i><br />');
		$sitecontent->add_site_content('<input type="text" name="mail" id="mail" onkeyup=" checkmail(); " onchange=" checkmail(); " > <i id="mailadr" title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse - bitte eingeben</i><br />');
		//Passwortindikator & Salted Hashing zur sicheren Speicherung
		$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onkeyup=" checkpw(); passwordbarchange( \'passwort1\' );" onblur="passbar_weg(); "> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort - bitte eingeben</i> <div id="pwind"></div>');
		$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onkeyup=" checkpw(); "> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort - bitte eingeben</i> <br />');
		$sitecontent->add_site_content('<input type="radio" name="level" value="less" checked="checked">Editor <input type="radio" name="level" value="more">Admin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
		$sitecontent->add_site_content('<input type="hidden" value="nok" id="check" >');
		//Salt für Passwort
		$sitecontent->add_site_content('<input type="hidden" value="'.makepassw( 10, '', 'numaz' ).'" id="salt" name="salt" >');
		$sitecontent->add_site_content('<input type="submit" value="Erstellen" ><br />');
		$sitecontent->add_site_content('</form>');
	}
	
	//User löschen
	public function make_user_edit_dbf_del( $user ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		//User suchen
		$id = $userfile->search_kimb_xxxid( $user , 'user' );		
		if( $id != false ){
			//wenn gefunden, aus UserID Liste entfernen und Userdaten löschen
			$userfile->write_kimb_teilpl( 'userids' , $id , 'del' );
			$userfile->write_kimb_id( $id , 'del' );
			
			return true;
		}
		else{
			return false;	
		}
	}
	
	//User Passwort ändern
	//	$user -> Username
	//	$pw -> neues Passwort, wenn nicht gesetzt automatische Wahl
	public function make_user_changepw( $user, $pw = '---auto---' ){
		
		//Übergabeparamter prüfen
		if( empty( $user ) ){
			return false;
		}
		//automatsiches Passwort gewünscht?
		if( $pw == '---auto---' ){
			//erstellen
			$pw = makepassw( 12, '', 'numaz' );
		}
		//Passwort Hash für dbf erstellen (Salt wählen)
		$salt = makepassw( 10, '', 'numaz' );
		$passw = $pw;
		$pw = sha1( $salt.$pw );

		//alles für Methode vorbereiten
		$POST['user'] = $user;
		$POST['salt'] = $salt;
		$POST['passwort1'] = $pw;
		
		//Daten der User bearbeiten Methode übergeben
		if( $this->make_user_edit_dbf_new( $POST ) ){
			return $passw;
		}
		else{
			return false;
		}
		
	}
	
	//User bearbeiten 
	public function make_user_edit_dbf_new( $POST ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		//Nach User suchen
		$id = $userfile->search_kimb_xxxid( $POST['user'] , 'user' );		
		if( $id != false ){
			//User gefunden
			
			//noch nichts geändert
			$ch = false;
			
			//Userdaten lesen
			$userinfo = $userfile->read_kimb_id( $id );
			//Passwort ändern gewünscht? (wenn leer nichts ändern)
			if( $userinfo['passw'] != $POST['passwort1'] && !empty( $POST['passwort1'] ) ){
				//Salt und neuen Hash speichern
				$userfile->write_kimb_id( $id , 'add' , 'passw' , $POST['passwort1'] );
				$userfile->write_kimb_id( $id , 'add' , 'salt' , $POST['salt'] );
				//Meldung
				$sitecontent->echo_message( 'Das Passwort wurde geändert!' );
				//jetzt wurde was geändert
				$ch = true;
			}
			//Rechte des Users ändern? (leer geht nicht)
			//	Nur User mit den Admin Rechten (more) dürfen das!! 
			if( $userinfo['permiss'] != $POST['level'] && $_SESSION['permission'] == 'more'  && !empty( $POST['level'] )){
				//neues Level speichern
				$userfile->write_kimb_id( $id , 'add' , 'permiss' , $POST['level'] );
				//Meldung
				$sitecontent->echo_message( 'Das Nutzerlevel wurde geändert!' );
				if($POST['level'] != 'more' ){
					//Hinweis wenn kein "more" Level mehr -> kein Vollzugriff auf das System merh möglich
					$sitecontent->echo_message( '<b style="color:red;">Achtung, setzen Sie nicht alle User auf ein niedriges Level, sonst können Sie den Systemzugriff verliehren!!</b>' );
				}
				//jetzt wurde was geändert
				$ch = true;
			}
			//Namen des Users ändern? (leer geht nicht)
			if( $userinfo['name'] != $POST['name'] &&  !empty( $POST['name'] ) ){
				//neuen Namen speichern
				$userfile->write_kimb_id( $id , 'add' , 'name' , $POST['name'] );
				//Meldung
				$sitecontent->echo_message( 'Der Name wurde geändert!' );
				//jetzt wurde was geändert
				$ch = true;
			}
			//E-Mail des Users ändern? (leer geht nicht)
			if( $userinfo['mail'] != $POST['mail'] &&  !empty( $POST['mail'] ) ){
				//hier findet keine Prüfung der Adresse statt, ein Admin sollte keinen Mist eingeben (zu viele Prüfungen nerven bei der Eingabe von Musterdaten)
				$userfile->write_kimb_id( $id , 'add' , 'mail' , $POST['mail'] );
				//Medlung
				$sitecontent->echo_message( 'Die E-Mail Adresse wurde geändert!' );
				//jetzt wurde was geändert
				$ch = true;
			}
			
			//bei erfolgten Änderungen darauf hinweisen, dass einige Änderungen werden erst ab erneutem Login wirksam werden
			//	(Name, Userlevel)
			if( $ch ){
				$sitecontent->echo_message( 'Achtung, einige Änderungen werden erst ab erneutem Login wirksam!' );
			}
			
			return true;
		}
		else{
			return false;
		}
		
	}
	
	//User bearbieten Formular
	public function make_user_edit(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		$sitecontent->add_site_content('<h2>User bearbeiten</h2>');

		//User löschen gewünscht?
		//	Nur User mit den Admin Rechten (more) dürfen das!! 
		if( isset( $_GET['del'] ) && $_SESSION['permission'] == 'more' ){
	
			//User löschen und Meldung ob erfolgreich
			if( $this->make_user_edit_dbf_del( $_GET['user'] ) ){
				$sitecontent->echo_message( 'Der User wurde gelöscht!<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=list"><button>Zurück zur Liste</button></a>' );	
			}
			else{
				$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
			}
		}
		else{
			//also nicht löschen 
	
			//Daten gesendet, also ran an die Änderungen
			if( !empty( $_POST['user'] ) ){
				
				//Daten an die User bearbeiten Methode weitergeben
				if( ! $this->make_user_edit_dbf_new( $_POST ) ){
					//Fehler -> Meldung
					$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');	
				}
			}
	
			//JavaScript
			$this->jsobject->for_user_edit( $_GET['user'] );
	
			//User suchen
			$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );
			if( $id != false ){
				//gefunden!
				
				//alle Userdaten lesen
				$user = $userfile->read_kimb_id( $id );
	
				//Formular ausgeben 
				$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_GET['user'].'" method="post" onsubmit=" return changesub(); "><br />');
				$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i title="Username für das Login ( keine Änderung möglich )">Username</i><br />');
				$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i title="Name des Users" >Name</i><br />');
				$sitecontent->add_site_content('<input type="text" name="mail" value="'.$user['mail'].'"> <i title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse</i><br />');
				
				//Levelwahl wenn Admin Rechte ("more")
				if( $_SESSION['permission'] == 'more' ){
	
					//wenn nicht geladen Levelliste laden
					if( !is_object( $levellist ) ){
						$levellist = new KIMBdbf( 'backend/users/level.kimb' );
					}
	
					//alle möglichen Level laden
					$levs = $levellist->read_kimb_one( 'levellist' );
					if( !empty( $levs ) ){
						//aufteilen
						$levs = explode( ',' , $levs );
	
						//Wahlmöglichkeit systemspezifische Level einblenden
						$other = '<b style="background-color:gray;" title="Systemspezifische Userlevel">';
	
						//jedes mögliche Level hinzufügen
						foreach( $levs as $name ){
							if( $user['permiss'] == $name ){
								//wenn aktuellen Level des Users auf checked setzen
								$other .= '<input type="radio" name="level" value="'.$name.'" checked="checked" >'.$name.' ';
							}
							else{
								$other .= '<input type="radio" name="level" value="'.$name.'">'.$name.' ';
							}
						}
						//Wahlmöglichkeit systemspezifische Level beenden
						$other .= '</b>';
					}
	
					//User aktuell Level "less"?
					if( $user['permiss'] == 'less' ){
						//passende Wahlmöglichkeit
						$sitecontent->add_site_content('<input type="radio" name="level" value="less" checked="checked">Editor <input type="radio" name="level" value="more">Admin '.$other.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
					}
					//User aktuell Level "more"?
					elseif(  $user['permiss'] == 'more'  ){
						//passende Wahlmöglichkeit
						$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more" checked="checked">Admin '.$other.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
					}
					//User aktuell in einem systemspezifische Level?
					else{
						//passende Wahlmöglichkeit
						$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more" >Admin '.$other.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
					}
				}
				//Rest des Formulars
				$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onkeyup=" checkpw(); passwordbarchange( \'passwort1\' );" onblur="passbar_weg();"> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort - keine Änderung</i> <div id="pwind"></div>');
				$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onkeyup=" checkpw(); "> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort - keine Änderung</i> <br />');
				$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
				//neues Salt
				$sitecontent->add_site_content('<input type="hidden" value="'.makepassw( 10, '', 'numaz' ).'" id="salt" name="salt" >');
				$sitecontent->add_site_content('</form>');
	
				//User löschen anbieten, wenn Admin Rechte ("more")
				if( $_SESSION['permission'] == 'more' ){
					$sitecontent->add_site_content('<br /><span onclick=" deluser(); "><span class="ui-icon ui-icon-trash" title="Diesen User löschen." style="display:inline-block;" ></span></span></a>');
				}
	
			}
			else{
				//Fehlermeldung wenn User nicht gefunden
				$sitecontent->echo_error( 'Der User existiert nicht!' , 'unknown');
			}
		}

	}
	
	//Userliste anziegen
	public function make_user_list(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		$sitecontent->add_site_content('<h2>Userliste</h2>');

		//Tabelle beginnen
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new"><span class="ui-icon ui-icon-plusthick" title="Einen neuen User erstellen." style="display:inline-block;" ></span></a>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th>Username</th> <th>Name</th> <th>E-Mail</th> <th>Level</th> </tr>');
	
		//Alle UserIDs lesen
		$users = $userfile->read_kimb_all_teilpl( 'userids' );
		
		//UserIDs durchgehen
		foreach( $users as $id ){
			//entsprechenden User lesen
			$user = $userfile->read_kimb_id( $id );
	
			//Rechte anzeigen
			if( $user['permiss'] == 'more' ){
				$permiss = '<span class="ui-icon ui-icon-plus" title="Dieser User hat erhöhte Admin-Rechte."></span>';
			}
			else{
				$permiss = '<span class="ui-icon ui-icon-minus" title="Dieser User hat geringere Rechte. (keine Admin-Rechte - &apos;more&apos;)"></span>';
			}
	
			//Link zu User bearbeiten
			$link = '<a title="User bearbeiten" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$user['user'].'">'.$user['user'].'</a>';
	
			//Tabellenzeile
			$sitecontent->add_site_content('<tr> <td>'.$link.'</td> <td>'.$user['name'].'</td> <td>'.$user['mail'].'</td> <td>'.$permiss.'</td> </tr>');
		}
	
		$sitecontent->add_site_content('</table>');
	}
	
	//Where has all the coffee gone?
}
?>
