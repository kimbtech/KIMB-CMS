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



defined('KIMB_Backend') or die('No clean Request');

class BEuser{
	
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
	
	public function make_user_new_dbf( $name, $passwort, $salt, $user, $level,$mail ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		if( empty( $name ) ){
			$name = 'Herr Franz Mustermann';
		}

		$user = preg_replace( "/[^a-z]/" , "" , strtolower( $user ) );
		$id = $userfile->search_kimb_xxxid( $user , 'user' );
		if( $id == false ){
			$username = $user;
		}
		else{
			return false;
		}

		if( $level == 'more' || $level == 'less' ){
			$permiss = $level;
		}
		else{
			return false;
		}

		if( !preg_match( "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/" , $mail ) ) {
			return false;
		}

		$id = $userfile->next_kimb_id();

		$userfile->write_kimb_teilpl( 'userids' , $id , 'add' );

		$userfile->write_kimb_id( $id , 'add' , 'passw' , $passwort );
		$userfile->write_kimb_id( $id , 'add' , 'salt' , $salt );
		$userfile->write_kimb_id( $id , 'add' , 'permiss' , $permiss );
		$userfile->write_kimb_id( $id , 'add' , 'name' , $name );
		$userfile->write_kimb_id( $id , 'add' , 'mail' , $mail );
		$userfile->write_kimb_id( $id , 'add' , 'user' , $username );
		
		return array( 'name' => $name, 'passwort' => $passwort, 'salt' => $salt, 'username' => $username, 'permiss' => $permiss, 'mail' => $mail  );
	}
	
	public function make_user_new(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		$sitecontent->add_site_content('<h2>User erstellen</h2>');

		if( !empty( $_POST['user'] ) ){
	
			$passwort = $_POST['passwort1'];
			$name = $_POST['name'];
			$salt = $_POST['salt'];
			$user = $_POST['user'];
			$level = $_POST['level'];
			$mail = $_POST['mail'];
			
			$ret = $this->make_user_new_dbf( $name, $passwort, $salt, $user, $level,$mail );
			
			if( is_array( $ret ) ){
				open_url( '/kimb-cms-backend/user.php?todo=edit&user='.$ret['username'] );
				die;
			}
			else{
				$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' );
			}
		}
	
		$this->jsobject->for_user_new();
	
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new" method="post" onsubmit="return checksumbit();"><br />');
		$sitecontent->add_site_content('<input type="text" name="user" onkeyup=" userreplace(); " onchange=" checkuser(); " id="user"> <i id="textuser" title="Username für das Login ( später keine Änderung möglich )">Username - bitte eingeben</i><br />');
		$sitecontent->add_site_content('<input type="text" name="name" > <i title="Name des Users" >Name</i><br />');
		$sitecontent->add_site_content('<input type="text" name="mail" id="mail" onkeyup=" checkmail(); " onchange=" checkmail(); " > <i id="mailadr" title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse - bitte eingeben</i><br />');
		$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onkeyup=" checkpw(); passwordbarchange( \'passwort1\' );" onblur="passbar_weg(); "> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort - bitte eingeben</i> <div id="pwind"></div>');
		$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onkeyup=" checkpw(); "> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort - bitte eingeben</i> <br />');
		$sitecontent->add_site_content('<input type="radio" name="level" value="less" checked="checked">Editor <input type="radio" name="level" value="more">Admin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
		$sitecontent->add_site_content('<input type="hidden" value="nok" id="check" >');
		$sitecontent->add_site_content('<input type="hidden" value="'.makepassw( 10, '', 'numaz' ).'" id="salt" name="salt" >');
		$sitecontent->add_site_content('<input type="submit" value="Erstellen" ><br />');
		$sitecontent->add_site_content('</form>');
	}
	
	public function make_user_edit_dbf_del( $user ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		$id = $userfile->search_kimb_xxxid( $user , 'user' );		
		if( $id != false ){
			$userfile->write_kimb_teilpl( 'userids' , $id , 'del' );
			$userfile->write_kimb_id( $id , 'del' );
			
			return true;
		}
		else{
			return false;	
		}
	}
	
	public function make_user_changepw( $user, $pw = '---auto---' ){
		
		if( empty( $user ) ){
			return false;
		}
		if( $pw == '---auto---' ){
			$pw = makepassw( 12, '', 'numaz' );
		}
		$salt = makepassw( 10, '', 'numaz' );
		$passw = $pw;
		$pw = sha1( $salt.$pw );

		$POST['user'] = $user;
		$POST['salt'] = $salt;
		$POST['passwort1'] = $pw;
		
		if( $this->make_user_edit_dbf_new( $POST ) ){
			return $passw;
		}
		else{
			return false;
		}
		
	}
	
	public function make_user_edit_dbf_new( $POST ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		$id = $userfile->search_kimb_xxxid( $POST['user'] , 'user' );		
		if( $id != false ){
			$ch = false;
			
			$userinfo = $userfile->read_kimb_id( $id );
			if( $userinfo['passw'] != $POST['passwort1'] && !empty( $POST['passwort1'] ) ){
				$userfile->write_kimb_id( $id , 'add' , 'passw' , $POST['passwort1'] );
				$userfile->write_kimb_id( $id , 'add' , 'salt' , $POST['salt'] );
				$sitecontent->echo_message( 'Das Passwort wurde geändert!' );
				$ch = true;
			}
			if( $userinfo['permiss'] != $POST['level'] && $_SESSION['permission'] == 'more'  && !empty( $POST['level'] )){
				$userfile->write_kimb_id( $id , 'add' , 'permiss' , $POST['level'] );
				$sitecontent->echo_message( 'Das Nutzerlevel wurde geändert!' );
				if($POST['level'] != 'more' ){
					$sitecontent->echo_message( '<b style="color:red;">Achtung, setzen Sie nicht alle User auf ein niedriges Level, sonst können Sie den Systemzugriff verliehren!!</b>' );
				}
				$ch = true;
			}
			if( $userinfo['name'] != $POST['name'] &&  !empty( $POST['name'] ) ){
				$userfile->write_kimb_id( $id , 'add' , 'name' , $POST['name'] );
				$sitecontent->echo_message( 'Der Name wurde geändert!' );
				$ch = true;
			}
			if( $userinfo['mail'] != $POST['mail'] &&  !empty( $POST['mail'] ) ){
				$userfile->write_kimb_id( $id , 'add' , 'mail' , $POST['mail'] );
				$sitecontent->echo_message( 'Die E-Mail Adresse wurde geändert!' );
				$ch = true;
			}
			
			if( $ch ){
				$sitecontent->echo_message( 'Achtung, einige Änderungen werden erst ab erneutem Login wirksam!' );
			}
			
			return true;
		}
		else{
			return false;
		}
		
	}
	
	public function make_user_edit(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		$sitecontent->add_site_content('<h2>User bearbeiten</h2>');

		if( isset( $_GET['del'] ) && $_SESSION['permission'] == 'more' ){
	
			if( $this->make_user_edit_dbf_del( $_GET['user'] ) ){
				$sitecontent->echo_message( 'Der User wurde gelöscht!<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=list"><button>Zurück zur Liste</button></a>' );	
			}
			else{
				$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
			}
		}
		else{
	
			if( !empty( $_POST['user'] ) ){
				
				if( ! $this->make_user_edit_dbf_new( $_POST ) ){
					$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');	
				}
			}
	
			$this->jsobject->for_user_edit( $_GET['user'] );
	
			$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );
			if( $id != false ){
				$user = $userfile->read_kimb_id( $id );
	
				$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_GET['user'].'" method="post" onsubmit=" return changesub(); "><br />');
				$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i title="Username für das Login ( keine Änderung möglich )">Username</i><br />');
				$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i title="Name des Users" >Name</i><br />');
				$sitecontent->add_site_content('<input type="text" name="mail" value="'.$user['mail'].'"> <i title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse</i><br />');
				if( $_SESSION['permission'] == 'more' ){
	
					if( !is_object( $levellist ) ){
						$levellist = new KIMBdbf( 'backend/users/level.kimb' );
					}
	
					$levs = $levellist->read_kimb_one( 'levellist' );
					if( $levs != '' ){
						$levs = explode( ',' , $levs );
	
						$other = '<b style="background-color:gray;" title="Systemspezifische Userlevel">';
	
						foreach( $levs as $name ){
							if( $user['permiss'] == $name ){
								$other .= '<input type="radio" name="level" value="'.$name.'" checked="checked" >'.$name.' ';
							}
							else{
								$other .= '<input type="radio" name="level" value="'.$name.'">'.$name.' ';
							}
						}
						$other .= '</b>';
					}
	
					if( $user['permiss'] == 'less' ){
						$sitecontent->add_site_content('<input type="radio" name="level" value="less" checked="checked">Editor <input type="radio" name="level" value="more">Admin '.$other.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
					}
					elseif(  $user['permiss'] == 'more'  ){
						$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more" checked="checked">Admin '.$other.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
					}
					else{
						$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more" >Admin '.$other.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
					}
				}
				$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onkeyup=" checkpw(); passwordbarchange( \'passwort1\' );" onblur="passbar_weg();"> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort - keine Änderung</i> <div id="pwind"></div>');
				$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onkeyup=" checkpw(); "> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort - keine Änderung</i> <br />');
				$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
				$sitecontent->add_site_content('<input type="hidden" value="'.makepassw( 10, '', 'numaz' ).'" id="salt" name="salt" >');
				$sitecontent->add_site_content('</form>');
	
				if( $_SESSION['permission'] == 'more' ){
					$sitecontent->add_site_content('<br /><span onclick=" deluser(); "><span class="ui-icon ui-icon-trash" title="Diesen User löschen." style="display:inline-block;" ></span></span></a>');
				}
	
			}
			else{
				$sitecontent->echo_error( 'Der User existiert nicht!' , 'unknown');
			}
		}

	}
	
	public function make_user_list(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		$userfile = $this->userfile;
		
		$sitecontent->add_site_content('<h2>Userliste</h2>');

		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new"><span class="ui-icon ui-icon-plusthick" title="Einen neuen User erstellen." style="display:inline-block;" ></span></a>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th>Username</th> <th>Name</th> <th>E-Mail</th> <th>Level</th> </tr>');
	
		$users = $userfile->read_kimb_all_teilpl( 'userids' );
		
		foreach( $users as $id ){
			$user = $userfile->read_kimb_id( $id );
	
			if( $user['permiss'] == 'more' ){
				$permiss = '<span class="ui-icon ui-icon-plus" title="Dieser User hat erhöhte Admin-Rechte."></span>';
			}
			else{
				$permiss = '<span class="ui-icon ui-icon-minus" title="Dieser User hat geringere Editor-Rechte."></span>';
			}
	
			$link = '<a title="User bearbeiten" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$user['user'].'">'.$user['user'].'</a>';
	
			$sitecontent->add_site_content('<tr> <td>'.$link.'</td> <td>'.$user['name'].'</td> <td>'.$user['mail'].'</td> <td>'.$permiss.'</td> </tr>');
		}
	
		$sitecontent->add_site_content('</table>');
	}
}
?>
