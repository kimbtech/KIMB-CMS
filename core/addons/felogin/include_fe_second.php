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

$felogin = $addon_felogin_array;

//register (captcha, mailcheck), usereinstellungen, pw-forgot,

if( $_GET['id'] == $felogin['requid'] && ( isset( $_GET['pwforg'] ) || isset( $_GET['register'] ) || isset( $_GET['settings'] ) ) ){

	$felogin['userfile'] = new KIMBdbf( 'addon/felogin__user.kimb'  );

	$sitecontent->add_site_content( '<hr id="goto"/>');

	if( isset( $_GET['settings'] ) && $felogin['loginokay'] == $_SESSION['felogin']['loginokay'] && isset( $_SESSION['felogin']['user'] ) ){

		$sitecontent->add_site_content( '<h2>'.$allgsys_trans['addons']['felogin']['usereinst'].'</h2>');

		if( isset( $_POST['user'] ) ){

			$id = $felogin['userfile']->search_kimb_xxxid( $_SESSION['felogin']['user'] , 'user' );

			if( !empty( $_POST['name'] ) && !empty( $_POST['mail'] ) ){
				if( $_POST['mail'] != $felogin['userfile']->read_kimb_id( $id , 'mail' ) ){
					if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) && $_POST['mailcode'] == $_SESSION['mailcode'] ){
						if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'mail' , $_POST['mail'] ) ){
							$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['mailch'].'</h3>');
						}
					}
					else{
						$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['mailerr'].'</h3>');
					}
				}
				if( $_POST['name'] != $felogin['userfile']->read_kimb_id( $id , 'name' ) ){
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'name' , $_POST['name'] ) ){
						$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['namech'].'</h3>');
					}
				}
				if( !empty( $_POST['passwort1'] ) ){
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] ) ){
						$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['passch'].'</h3>');
					}
				}
			}
			else{
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['fillnamail'].'</h3>');
			}
		}
		
		$header = '<script>var siteurl = "'.$allgsysconf['siteurl'].'";';
			foreach( $allgsys_trans['addons']['felogin']['regjs'] as $key => $val ){
				$header .= 'var '.$key.' = "'.$val.'"; ';
			}
			$header .= '</script>';
		
			$sitecontent->add_html_header( $header );
		
		$sitecontent->add_html_header('<script>
		function checkpw() {
			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();

			if( valzwei != valeins ){
				$("i#pwtext").text("'.$allgsys_trans['addons']['felogin']['userjs']['pwtext1'].'");
				$("i#pwtext").css( "background-color", "red" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
			else{
				$("i#pwtext").text("'.$allgsys_trans['addons']['felogin']['userjs']['pwtext2'].'");
				$("i#pwtext").css( "background-color", "green" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
		}

		function checkmail(){
			var valmail = $( "input#mail" ).val();
			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

			if( mailmatch.test( valmail ) ){
				$("i#mailadr").text( "'.$allgsys_trans['addons']['felogin']['regjs']['mailprue'].'" );
				$("i#mailadr").css( "background-color", "orange" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
				
				$( "div#mailcheck" ).css( "display", "block" );
			
			}
			else{
				$("i#mailadr").text( "'.$allgsys_trans['addons']['felogin']['userjs']['mailadr2'].'" );
				$("i#mailadr").css( "background-color", "red" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
			}
		}
		
		function sendcode(){
			var valmail = $( "input#mail" ).val();
			$.get( siteurl + "/ajax.php?addon=felogin&mail=" + valmail + "&lang=" + langfile, function( data ) {
				if( data == "ok" ){
					$("i#mailadr").html( codese );
				}
				else{
					$("i#mailadr").text( codeanzb );
					$("i#mailadr").css( "background-color", "red" );
				}
			});
		}

		function checkcode(){
			var valcode =  encodeURIComponent( $( "input#mailcode" ).val() );
			$.get( siteurl + "/ajax.php?addon=felogin&code=" + valcode , function( data ) {
				if( data == "ok" ){
					$("i#mailadr").text( mailok );
					$("i#mailadr").css( "background-color", "green" );
					$( "input#checkm" ).val( "ok" );
					
					$("span#codeokay" ).text( codeok );
					$("span#codeokay").css( "background-color", "green" );
					$("span#codeokay").css( "color", "white" );
					$("span#codeokay").css( "padding", "5px" );

				}
				else{
					$("i#mailadr").text( codeerr );
					$("i#mailadr").css( "background-color", "red" );
				}
			});
		}

		function checksumbit(){

			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();
			var valmail = $( "input#mail" ).val();

			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
			if( mailmatch.test( valmail ) == false ){
				return false;
			}

			if( valzwei != valeins ){ 
				return false;
			}
			else if( valeins != "" ){
				$( "input#passwort1" ).val( SHA1( valeins ) );
				$( "input#passwort2" ).val( \'\' );
				return true;
			}
			else{
				return true;
			}

		}	
		</script>');

		$id = $felogin['userfile']->search_kimb_xxxid( $_SESSION['felogin']['user'] , 'user' );
		if( $id != false ){
			$user = $felogin['userfile']->read_kimb_id( $id );

			$sitecontent->add_site_content('<form action="" method="post" onsubmit="return checksumbit();" ><br />');
			$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i>'.$allgsys_trans['addons']['felogin']['username'].'</i><br />');
			$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i>'.$allgsys_trans['addons']['felogin']['name'].'</i><br />');
			$sitecontent->add_site_content('<input type="text" name="mail" id="mail" onchange=" checkmail(); " value="'.$user['mail'].'" > <i id="mailadr">'.$allgsys_trans['addons']['felogin']['mailunverae'].'</i><br />');
			
			$sitecontent->add_site_content('<div id="mailcheck" style="display:none;">
			<input type="text" name="mailcode" id="mailcode" placeholder="'.$allgsys_trans['addons']['felogin']['mailcode'].'" onchange="checkcode();" >
			<span id="codeokay"><button onclick="sendcode(); return false;">'.$allgsys_trans['addons']['felogin']['mailcodesenden'].'</button></span></div>'."\r\n");
			
			$sitecontent->add_site_content('<input type="text" name="gruppr" readonly="readonly" value="'.$user['gruppe'].'"><i>'.$allgsys_trans['addons']['felogin']['gruppe'].'</i><br />');
			$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onchange=" checkpw(); "> <i id="pwtext">'.$allgsys_trans['addons']['felogin']['passunverae'].'</i> <br />');
			$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onchange=" checkpw(); "> <i id="pwtext">'.$allgsys_trans['addons']['felogin']['passunverae'].'</i> <br />');
			$sitecontent->add_site_content('<input type="submit" value="'.$allgsys_trans['addons']['felogin']['aen'].'" ><br />');
			$sitecontent->add_site_content('</form>');

		}
		else{
			$sitecontent->add_site_content($allgsys_trans['addons']['felogin']['usernotex']);
		}

	}
	elseif( isset( $_GET['pwforg'] ) ){
		
		$sitecontent->add_site_content('<h2>'.$allgsys_trans['addons']['felogin']['pwforg'].'</h2>');

		if( isset( $_POST['user'] ) || isset( $_POST['mail'] ) ){

			if( empty( $_POST['user'] ) && !empty( $_POST['mail'] ) ){
				$id = $felogin['userfile']->search_kimb_xxxid( $_POST['mail'] , 'mail' );
				$ok = 'ok';

			}
			elseif( !empty( $_POST['user'] ) && empty( $_POST['mail'] )){
				$id = $felogin['userfile']->search_kimb_xxxid( $_POST['user'] , 'user' );
				$ok = 'ok';

			}
			else{
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['fillone'].'</h3>');
				$ok = 'nok';
			}

			if( $ok == 'ok' ){
				if( $id != false ){
					$newpass = makepassw( 10 );
					$setnewcode = makepassw( 30, '', 'numaz' );
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'newpassw' , sha1( $newpass ) ) && $felogin['userfile']->write_kimb_id( $id , 'add' , 'setnewcode' , $setnewcode ) ){

						$inhalt = str_replace( array( '%name%', '%pass%', '%url%', '%sitename%' , '%br%' ) , array( $felogin['userfile']->read_kimb_id( $id , 'name' ), $newpass, $allgsysconf['siteurl'].'/ajax.php?addon=felogin&newpassak='.$id.'&code='.$setnewcode , $allgsysconf['sitename'], "\r\n" ) , $allgsys_trans['addons']['felogin']['mailtext']['newpass'] );

						send_mail( $felogin['userfile']->read_kimb_id( $id , 'mail' ) , $inhalt );
					}
				}
				
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['mailsee'].'</h3>');
			}
		}

		$sitecontent->add_site_content($allgsys_trans['addons']['felogin']['textpwforg'].'<br />');
		$sitecontent->add_site_content('<form action="" method="post" >');
		$sitecontent->add_site_content('<input type="text" name="mail" placeholder="'.$allgsys_trans['addons']['felogin']['mail'].'" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['mail'].') <![endif]-->');
		$sitecontent->add_site_content('<input type="text" name="user" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['username'].') <![endif]--><br />');
		$sitecontent->add_site_content('<input type="submit" value="'.$allgsys_trans['addons']['felogin']['sendnewpass'].'" ><br />');
		$sitecontent->add_site_content('</form>');

	}
	elseif( isset( $_GET['register'] ) && ( $felogin['selfreg'] == 'on' || $_SESSION['registerokay'] == 'yes' ) ){

		$sitecontent->add_site_content('<h2>'.$allgsys_trans['addons']['felogin']['register'].'</h2>');

		if( isset( $_POST['captcha_code'] ) ){
			if( !empty( $_POST['user'] ) && !empty( $_POST['name'] ) && !empty( $_POST['mailcode'] ) && !empty( $_POST['passwort1'] ) && !empty( $_POST['captcha_code'] ) && $_POST['akzep'] == 'ok'  ){

				if( $_SESSION['registerokay'] == 'yes' ){
					$_POST['mailcode'] = $_SESSION["mailcode"];
					$_POST['captcha_code'] = $_SESSION['captcha_code'];
					$_SESSION['email'] = $_POST['mail'];
				}

				$_POST['user'] = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $_POST['user'] ) );
				$uid = $felogin['userfile']->search_kimb_xxxid( $_POST['user'] , 'user' );
				if( $uid == false && $_POST['mailcode'] == $_SESSION["mailcode"] && check_captcha() && !empty( $_SESSION['email'] ) ){

					$gruppe = $felogin['conf']->read_kimb_one( 'selfreggruppe' );

					$id = $felogin['userfile']->next_kimb_id();

					$felogin['userfile']->write_kimb_teilpl( 'userids' , $id , 'add' );

					$felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'gruppe' , $gruppe );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'name' , $_POST['name'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'mail' , $_SESSION['email'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'user' , $_POST['user'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'status' , 'on' );

					$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['newokay'].'</h3>');
					$sitecontent->add_site_content( '<ul><li>'.$allgsys_trans['addons']['felogin']['mail'].': '.$_SESSION['email'].'</li><li>'.$allgsys_trans['addons']['felogin']['username'].': '.$_POST['user'].'</li><li>'.$allgsys_trans['addons']['felogin']['name'].': '.$_POST['name'].'</li></ul>' );

					if( $felogin['conf']->read_kimb_one( 'infomail' ) == 'on' ){
						
						$inhalt = str_replace( array( '%sitename%' , '%br%', '%userna%' ) , array( $allgsysconf['sitename'], "\r\n", $_POST['user'] ) , $allgsys_trans['addons']['felogin']['mailtext']['newuseradm'] );
						
						send_mail( $allgsysconf['adminmail'] , $inhalt );
					}

				}
				else{
					$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['eingerr'].'</h3>' );
				}
			}
			else{
				$sitecontent->add_site_content( '<h3>'.$allgsys_trans['addons']['felogin']['fillall'].'</h3>');
			}
		}
		else{

			$header = '<script>var siteurl = "'.$allgsysconf['siteurl'].'";';
			foreach( $allgsys_trans['addons']['felogin']['regjs'] as $key => $val ){
				$header .= 'var '.$key.' = "'.$val.'"; ';
			}
			$header .= '</script>';
		
			$sitecontent->add_html_header( $header );

			$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/addondata/felogin/register.min.js" type="text/javascript" ></script>');

			$sitecontent->add_site_content($allgsys_trans['addons']['felogin']['textreg'].'<br /><br />'."\r\n");
			$sitecontent->add_site_content('<form action="#goto" method="post" onsubmit="return checksubmit(); " >'."\r\n");
			$sitecontent->add_site_content('<table width="100%;">'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="text" name="user" id="user" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" onchange=" checkuser(); " ></td> <td colspan="2"><i id="usertext">Username -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i></td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="text" name="name" id="name" placeholder="'.$allgsys_trans['addons']['felogin']['name'].'" onkeyup=" checkname(); " onblur=" checkname(); "></td> <td colspan="2" ><i id="nametext">Name -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i></td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="text" name="mail" id="mail" placeholder="'.$allgsys_trans['addons']['felogin']['mail'].'" onkeyup="checkmail();" onchange="checkmail();" ></td> <td colspan="2" ><i id="mailtext">E-Mail-Adresse -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i></td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr style="display:none;" id="mailcodeinput" ><td><input type="text" name="mailcode" id="mailcode" placeholder="'.$allgsys_trans['addons']['felogin']['mailcode'].'" onchange=" checkcode(); " ></td> <td style="min-width:120px;"><i id="mailcodetext"><button onclick=" sendcode(); ">'.$allgsys_trans['addons']['felogin']['mailcodesenden'].'</button></i><button style="display:none" id="nochmalcode" onclick=" sendcode(); ">'.$allgsys_trans['addons']['felogin']['mailcodesendenneu'].'</button></td> <td>'.$allgsys_trans['addons']['felogin']['mailcodetext'].'</td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="password" name="passwort1" id="passwort1" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" onkeyup=" checkpw(); " onblur=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">'.$allgsys_trans['addons']['felogin']['passwort'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i> </td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="password" name="passwort2" id="passwort2" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" onkeyup=" checkpw(); " onblur=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">'.$allgsys_trans['addons']['felogin']['passwort'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i> </td></tr>'."\r\n");
			if( $_SESSION['registerokay'] == 'yes' ){
				$sitecontent->add_site_content('<tr><td colspan="3"><b>Da Sie ein Admin sind, können Sie das Captcha und den E-Mail-Code ignorieren!!</b><input type="hidden" id="checku" value="nok"><input type="hidden" id="checkc" value="ok"><input type="hidden" id="checkm" value="ok"></td></tr>'."\r\n");
				$sitecontent->add_html_header('<script>$(function(){ $( "input#mailcode" ).val( "irrelevant" ); $( "input#captcha" ).val( "irrelevant" ); }); </script>');
			}
			else{
				$sitecontent->add_site_content('<tr><td colspan="3"><input type="hidden" id="checku" value="nok"><input type="hidden" id="checkc" value="nok"><input type="hidden" id="checkm" value="nok"></td></tr>'."\r\n");
			}
			$sitecontent->add_site_content('<tr><td>'.make_captcha_html().'</td><td id="captchatd"><i id="captchatext">'.$allgsys_trans['addons']['felogin']['captcha'].' -- '.$allgsys_trans['addons']['felogin']['eintragen'].'</i> </td><td>'.$allgsys_trans['addons']['felogin']['captchatext'].'</td></tr>'."\r\n");
			$sitecontent->add_site_content('</table>');

			$felogin['akzepttext'] = $felogin['conf']->read_kimb_one( 'akzepttext' );
			$sitecontent->add_site_content('<input type="checkbox" name="akzep" id="akzep" value="ok">'.$felogin['akzepttext'].'<br />');
				
			$sitecontent->add_site_content('<br /><input type="submit" value="'.$allgsys_trans['addons']['felogin']['registerbutton'].'" ><br />');
			$sitecontent->add_site_content('</form>');
		}
	}

	$sitecontent->add_site_content( '<hr id="end"/>');
}

unset( $felogin, $addon_felogin_array );

?>
