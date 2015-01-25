<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS Add-on
//KIMB ContentManagementSystem
//KIMB-technologies.blogspot.com
/*************************************************/
//CC BY-ND 4.0
//http://creativecommons.org/licenses/by-nd/4.0/
//http://creativecommons.org/licenses/by-nd/4.0/legalcode
/*************************************************/
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
//BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
//IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
//IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
/*************************************************/


defined('KIMB_CMS') or die('No clean Request');

//register (captcha, mailcheck), usereinstellungen, pw-forgot,

$felogin['userfile'] = new KIMBdbf( 'addon/felogin__user.kimb'  );

if( $_GET['id'] == $felogin['requid'] && ( isset( $_GET['pwforg'] ) || isset( $_GET['register'] ) || isset( $_GET['settings'] ) ) ){

	$sitecontent->add_site_content( '<hr id="goto"/>');

	if( isset( $_GET['settings'] ) && $felogin['loginokay'] == $_SESSION['felogin']['loginokay'] && isset( $_SESSION['felogin']['user'] ) ){

		$sitecontent->add_site_content( '<h2>Usereinstellungen</h2>');

		if( isset( $_POST['user'] ) ){

			$id = $felogin['userfile']->search_kimb_xxxid( $_SESSION['felogin']['user'] , 'user' );

			if( !empty( $_POST['name'] ) && !empty( $_POST['mail'] ) ){
				if( $_POST['mail'] != $felogin['userfile']->read_kimb_id( $id , 'mail' ) ){
					if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) ){
						if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'mail' , $_POST['mail'] ) ){
							$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Die E-Mail-Adresse wurde geändert!</div>');
						}
					}
					else{
						$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Bitte geben Sie eine korrekte E-Mail-Adresse an!</div>');
					}
				}
				if( $_POST['name'] != $felogin['userfile']->read_kimb_id( $id , 'name' ) ){
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'name' , $_POST['name'] ) ){
						$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Der Name wurde geändert!</div>');
					}
				}
				if( !empty( $_POST['passwort1'] ) ){
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] ) ){
						$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Das Passwort wurde geändert!</div>');
					}
				}
			}
			else{
				$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Bitte Füllen Sie den Namen und die E-Mail-Adresse!</div>');
			}
		}
		
		$sitecontent->add_html_header('<script>
		function checkpw() {
			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();

			if( valzwei != valeins ){
				$("i#pwtext").text("Passwörter stimmen nicht überein!");
				$("i#pwtext").css( "background-color", "red" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
			else{
				$("i#pwtext").text("Passwörter - OK");
				$("i#pwtext").css( "background-color", "green" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
		}

		function checkmail(){
			var valmail = $( "input#mail" ).val();
			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

			if( mailmatch.test( valmail ) ){
				$("i#mailadr").text( "E-Mail Adresse - OK" );
				$("i#mailadr").css( "background-color", "green" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
			
			}
			else{
				$("i#mailadr").text( "Die E-Mail Adresse ist fehlerhaft!" );
				$("i#mailadr").css( "background-color", "red" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
			}
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
			$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i>Username</i><br />');
			$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i>Name</i><br />');
			$sitecontent->add_site_content('<input type="text" name="mail" id="mail" onchange=" checkmail(); " value="'.$user['mail'].'" > <i id="mailadr">E-Mail-Adresse - keine Änderung</i><br />');
			$sitecontent->add_site_content('<input type="text" name="gruppr" readonly="readonly" value="'.$user['gruppe'].'"><i>Gruppe</i><br />');
			$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onchange=" checkpw(); "> <i id="pwtext">Passwort - keine Änderung</i> <br />');
			$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onchange=" checkpw(); "> <i id="pwtext">Passwort - keine Änderung</i> <br />');
			$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
			$sitecontent->add_site_content('</form>');

		}
		else{
			$sitecontent->add_site_content( 'Der User existiert nicht!');
		}

	}
	elseif( isset( $_GET['pwforg'] ) ){

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
				$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Bitte füllen Sie ein Feld!</div>');
				$ok = 'nok';
			}

			if( $ok == 'ok' ){
				if( $id != false ){
					$newpass = makepassw( 10 );
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , sha1( $newpass ) ) ){

						$inhalt .= 'Hallo '.$felogin['userfile']->read_kimb_id( $id , 'name' ).','."\r\n";
						$inhalt .= 'ihr neues Passwort lautet: '.$newpass."\r\n";
						$inhalt .= 'Gleich einloggen: '.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid']."\r\n\r\n";
						$inhalt .= $allgsysconf['sitename'];

						if( send_mail( $felogin['userfile']->read_kimb_id( $id , 'mail' ) , $inhalt ) ){
							$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Gefunden - Bitte schauen Sie nach Ihren E-Mails!</div>');
						}
					}
				}
			}

		}

		$sitecontent->add_site_content('<h2>Passwort vergessen?!</h2>');
		$sitecontent->add_site_content('Hier können Sie sich ein neues Passwort zusenden lassen! Geben Sie Ihre E-Mail-Adresse oder Ihren Usernamen an und wir schicken Ihnen Ihr neues Passwort!<br />');
		$sitecontent->add_site_content('<form action="" method="post" >');
		$sitecontent->add_site_content('<input type="text" name="mail" placeholder="E-Mail-Adresse" ><!--[if lt IE 10]> ( E-Mail-Adresse ) <![endif]-->');
		$sitecontent->add_site_content('<input type="text" name="user" placeholder="Username" ><!--[if lt IE 10]> ( Username ) <![endif]--><br />');
		$sitecontent->add_site_content('<input type="submit" value="Neues Passwort zusenden!" ><br />');
		$sitecontent->add_site_content('</form>');

		$sitecontent->add_site_content('<br /><br />Sollten Sie sich mit dem neuen Passwort nicht einloggen können, kann Ihr Account gesperrt sein!');




	}
	elseif( isset( $_GET['register'] ) && ( $felogin['selfreg'] == 'on' || $_SESSION['registerokay'] == 'yes' ) ){

		if( isset( $_POST['captcha'] ) ){
			if( !empty( $_POST['user'] ) && !empty( $_POST['name'] ) && !empty( $_POST['mailcode'] ) && !empty( $_POST['passwort1'] ) && !empty( $_POST['captcha'] ) && $_POST['akzep'] == 'ok'  ){

				if( $_SESSION['registerokay'] == 'yes' ){
					$_POST['mailcode'] = $_SESSION["mailcode"];
					$_POST['captcha'] = $_SESSION['captcha'];
					$_SESSION['email'] = $_POST['mail'];
				}

				$_POST['user'] = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $_POST['user'] ) );
				$uid = $felogin['userfile']->search_kimb_xxxid( $_POST['user'] , 'user' );
				if( $uid == false && $_POST['mailcode'] == $_SESSION["mailcode"] && $_POST['captcha'] == $_SESSION['captcha'] && !empty( $_SESSION['email'] ) ){

					$gruppe = $felogin['conf']->read_kimb_one( 'selfreggruppe' );

					$id = $felogin['userfile']->next_kimb_id();

					$felogin['userfile']->write_kimb_teilpl( 'userids' , $id , 'add' );

					$felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'gruppe' , $gruppe );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'name' , $_POST['name'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'mail' , $_SESSION['email'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'user' , $_POST['user'] );
					$felogin['userfile']->write_kimb_id( $id , 'add' , 'status' , 'on' );

					$sitecontent->add_site_content( '<center><div style="color:red; font-size:20px;">Ihr Account wurde eingerichtet!</div>E-Mail-Adresse: '.$_SESSION['email'].'<br />Username: '.$_POST['user'].'<br />Name: '.$_POST['name'].'</center>' );

					if( $felogin['conf']->read_kimb_one( 'infomail' ) == 'on' ){
						send_mail( $allgsysconf['adminmail'] , 'Es hat sich ein neuer User "'.$_POST['user'].'" registriert!' );
					}

				}
				else{
					$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Die Eingabeprüfung ist fehlgeschlagen!</div>' );
				}
			}
			else{
				$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Bitte Füllen Sie alle Felder!</div>' );
			}
		}
		else{
			$sitecontent->add_html_header('<script>var siteurl = "'.$allgsysconf['siteurl'].'";</script>');
			$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/addondata/felogin/register.js" type="text/javascript" ></script>');

			$sitecontent->add_site_content('<h2>Account anlegen?!</h2>'."\r\n");
			$sitecontent->add_site_content('Hier können Sie sich einen Account anlegen!<br /><br />'."\r\n");
			$sitecontent->add_site_content('<form action="#goto" method="post" onsubmit="return checksubmit(); " >'."\r\n");
			$sitecontent->add_site_content('<table width="100%;">'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="text" name="user" id="user" placeholder="Username" onchange=" checkuser(); " ></td> <td colspan="2"><i id="usertext">Username -- bitte eintragen</i></td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="text" name="name" id="name" placeholder="Name" onchange=" checkname(); "></td> <td colspan="2" ><i id="nametext">Name -- bitte eintragen</i></td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="text" name="mail" id="mail" placeholder="E-Mail-Adresse" onchange=" checkmail(); " ></td> <td colspan="2" ><i id="mailtext">E-Mail-Adresse -- bitte eintragen</i></td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr style="display:none;" id="mailcodeinput" ><td><input type="text" name="mailcode" id="mailcode" placeholder="E-Mail-Code" onchange=" checkcode(); " ></td> <td style="min-width:120px;"><i id="mailcodetext"><button onclick=" sendcode(); ">Verifizierungscode an Ihre E-Mail-Adresse senden</button></i><button style="display:none" id="nochmalcode" onclick=" sendcode(); ">Nochmal versuchen?!</button></td> <td>( Wir senden Ihnen einen Code an die angegebene E-Mail-Adresse um diese zu testen, bitte geben Sie den Code links ein! )</td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="password" name="passwort1" id="passwort1" placeholder="Passwort" onchange=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">Passwort -- bitte eintragen</i> </td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input type="password" name="passwort2" id="passwort2" placeholder="Passwort" onchange=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">Passwort -- bitte eintragen</i> </td></tr>'."\r\n");
			if( $_SESSION['registerokay'] == 'yes' ){
				$sitecontent->add_site_content('<tr><td colspan="3"><b>Da Sie ein Admin sind, können Sie das Captcha und den E-Mail-Code ignorieren!!</b><input type="hidden" id="checku" value="nok"><input type="hidden" id="checkc" value="ok"><input type="hidden" id="checkm" value="ok"></td></tr>'."\r\n");
				$sitecontent->add_html_header('<script>$(function(){ $( "input#mailcode" ).val( "irrelevant" ); $( "input#captcha" ).val( "irrelevant" ); }); </script>');
			}
			else{
				$sitecontent->add_site_content('<tr><td colspan="3"><input type="hidden" id="checku" value="nok"><input type="hidden" id="checkc" value="nok"><input type="hidden" id="checkm" value="nok"></td></tr>'."\r\n");
			}
			$sitecontent->add_site_content('<tr><td><img src="'.$allgsysconf['siteurl'].'/ajax.php?addon=captcha" id="captchaimg" style="border:none;" /></td> <td><a href="#" onclick=" document.getElementById( \'captchaimg\' ).src = \''.$allgsysconf['siteurl'].'/ajax.php?addon=captcha&\' + Math.random(); return false;">Nicht lesbar?</a></td><td>( Bitte geben Sie die Buchstaben, die Sie oben sehen, in das Feld ein. Nur so können wir sicherstellen, dass nur Menschen einen Account bekommen. )</td></tr>'."\r\n");
			$sitecontent->add_site_content('<tr><td><input name="captcha" id="captcha" autocomplete="off" placeholder="Code" onchange=" checkcaptcha(); " type="text" ></td> <td colspan="2" ><i id="captchatext">Ich bin kein Roboter - Captcha</i></td></tr>'."\r\n");
			$sitecontent->add_site_content('</table>');

			$felogin['akzepttext'] = $felogin['conf']->read_kimb_one( 'akzepttext' );
			$sitecontent->add_site_content('<input type="checkbox" name="akzep" id="akzep" value="ok">'.$felogin['akzepttext'].'<br />');
				
			$sitecontent->add_site_content('<br /><input type="submit" value="Account erstellen!" ><br />');
			$sitecontent->add_site_content('</form>');
		}
	}

	$sitecontent->add_site_content( '<hr id="end"/>');
}

unset( $felogin );

?>
