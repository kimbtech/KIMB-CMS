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

$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/system/hash.js" type="text/javascript" ></script>');
$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js" type="text/javascript" ></script>');

$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$felogin['loginokay'] = $felogin['conf']->read_kimb_one( 'loginokay' );
$felogin['grlist'] = $felogin['conf']->read_kimb_one( 'grlist' );
$felogin['grs'] = explode( ',' , $felogin['grlist'] );
$felogin['requid'] = $felogin['conf']->read_kimb_one( 'requid' );
$felogin['selfreg'] = $felogin['conf']->read_kimb_one( 'selfreg' );

foreach( $felogin['grs'] as $felogin['gr'] ){

	$felogin['grteile'] = $felogin['conf']->read_kimb_one( $felogin['gr'] );

	$felogin['teilesite'][$felogin['gr']] = explode( ',' , $felogin['grteile'] );

	foreach($felogin['teilesite'][$felogin['gr']] as $felogin['teilsite'] ){

		$felogin['allsites'][] = $felogin['teilsite'];

	}
}

function checklogin( ){
	global $felogin, $allgsiteid;

	if( in_array( $allgsiteid , $felogin['allsites'] ) ){
		if( $felogin['loginokay'] == $_SESSION['felogin']['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

			if( in_array( $allgsiteid , $felogin['teilesite'][$_SESSION['felogin']['gruppe']] ) ){
				return true;
			}
			else{
				return false;
			}

		}
		else{
			return false;
		}
	}
	else{
		return true;
	}
}

if( isset( $_POST['logout'] ) ){
	$loginfehler = $_SESSION["loginfehler"];
	session_destroy();
	session_start();
	$_SESSION["loginfehler"] = $loginfehler;

	$sitecontent->add_site_content( '<center><hr /><div style="color:red; font-size:20px;">Sie wurden ausgeloggt!</div><b>Auf Wiedersehen</b><hr /></center>' );
}

if( isset($_POST['feloginuser']) && isset($_POST['feloginpassw']) ){

	if($_SESSION["loginfehler"] == ''){ $_SESSION["loginfehler"] = '0'; }

	$userfile = new KIMBdbf( 'addon/felogin__user.kimb'  );
		
	$user = $_POST['feloginuser'];
	$userda = $userfile->search_kimb_xxxid( $user , 'user' );

	if($userda != false && $_SESSION["loginfehler"] <= 6 ){
 		$passhash = $_POST['feloginpassw'];
		$passpruef = sha1($userfile->read_kimb_id( $userda , 'passw' ).$_SESSION["loginsalt"]);
	
		if( $passhash == $passpruef && $userfile->read_kimb_id( $userda , 'status' ) == 'on' ){
			//eingeloggt
			$_SESSION['felogin']['loginokay'] = $felogin['loginokay'];
			$_SESSION['felogin']['name'] = $userfile->read_kimb_id( $userda , 'name' );
			$_SESSION['felogin']['gruppe'] = $userfile->read_kimb_id( $userda , 'gruppe' );
			$_SESSION['felogin']['user'] = $user;
			$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
			$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
			unset($_SESSION["loginsalt"]);
		}
		else{
			$_SESSION["loginfehler"]++;
			if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
				$felogin['area'] .= '<div style="color:red;">Ihr Passwort ist falsch!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!</div><br />';
			}
			else{
				$sitecontent->add_site_content( '<div style="color:red;">Ihr Passwort ist falsch!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!</div><br />' );
			}
		}
		
	}
	else{
		$_SESSION["loginfehler"]++;
		if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
			$felogin['area'] .= '<div style="color:red;">Sie haben die maximale Anzahl an Versuchen erreicht oder Ihr Username ist inkorrekt!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!</div><br />';		
		}
		else{
			$sitecontent->add_site_content( '<div style="color:red;">Ihr Passwort ist falsch!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!</div><br />' );
		}
	}
}

if( !isset( $_SESSION['felogin']['user'] ) ){
	$loginsalt = makepassw( 40 , '_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );
	$_SESSION["loginsalt"] = $loginsalt;
}

//addonarea form
if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
	if( !isset( $_SESSION['felogin']['user'] ) ){
		$felogin['addonarea'] .= '<form action="" method="post" onsubmit="hash();" >';
		$felogin['addonarea'] .= '<input type="text" name="feloginuser" placeholder="Username" ><!--[if lt IE 10]> ( Username ) <![endif]--><br />';
		$felogin['addonarea'] .= '<input type="password" name="feloginpassw" placeholder="Passwort" id="pass" ><!--[if lt IE 10]> ( Passwort ) <![endif]--><br />';
		$felogin['addonarea'] .= '<input type="submit" value="Login"><br />';
		$felogin['addonarea'] .= '</form><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">Passwort vergessen?</a><br />';
		if( $felogin['selfreg'] == 'on' ){
			$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">Registrieren</a><br />';
		}
	}
	else{
		$felogin['addonarea'] .= 'Hallo '.$_SESSION['felogin']['name'].',<br />Sie sind eingeloggt!<br /><br />';
		$felogin['addonarea'] .= '<form action="" method="post"><input type="hidden" name="logout" value="yes"><input type="submit" value="Logout" ></form><br /><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;settings#goto">Einstellungen</a><br />';
	}

	$felogin['area'] .= '<h2>Login</h2>';
	$felogin['area'] .= '<div id="felogin">Für das Login wird JavaScript benötigt, bitte aktivieren Sie dieses!</div>';

	$sitecontent->add_addon_area( $felogin['area'] );

	$sitecontent->add_html_header('<script>
	$(function() {
		$("div#felogin").html( \''.$felogin['addonarea'].'\' );
	});
	function hash() {
		document.getElementById(\'pass\').value = SHA1(SHA1(document.getElementById(\'pass\').value)+\''.$loginsalt.'\');
	}
	</script>');
}
if( $_GET['id'] == $felogin['requid'] ){

	if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'off' ){
		if( !isset( $_SESSION['felogin']['user'] ) ){
			$felogin['formareal'] .= '<form action="" method="post" onsubmit="hash();" >';
			$felogin['formareal'] .= '<input type="text" name="feloginuser" placeholder="Username" > <!--[if lt IE 10]> ( Username ) <![endif]-->';
			$felogin['formareal'] .= '<input type="password" name="feloginpassw" placeholder="Passwort" id="pass" > <!--[if lt IE 10]> ( Passwort ) <![endif]-->';
			$felogin['formareal'] .= '<input type="submit" value="Login"><br />';
			$felogin['formareal'] .= '</form><br />';
			$felogin['formareal'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">Passwort vergessen?</a>&nbsp;';
			if( $felogin['selfreg'] == 'on' ){
				$felogin['formareal'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">Registrieren</a><br />';
			}
		}
		else{
			$felogin['formareal'] .= 'Hallo '.$_SESSION['felogin']['name'].', Sie sind eingeloggt!<br />';
			$felogin['formareal'] .= '<form action="" method="post"><input type="hidden" name="logout" value="yes">';
			$felogin['formareal'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;settings#goto">Einstellungen</a> <input type="submit" value="Logout" ></form>';
		}

		$sitecontent->add_site_content( '<hr /><center><div style="color:red; font-size:20px;">Login!</div><div id="felogin">Für das Login wird JavaScript benötigt, bitte aktivieren Sie dieses!</div></center><hr />' );

		$sitecontent->add_html_header('<script>
		$(function() {
			$("div#felogin").html( \''.$felogin['formareal'].'\' );
		});
		function hash() {
			document.getElementById(\'pass\').value = SHA1(SHA1(document.getElementById(\'pass\').value)+\''.$loginsalt.'\');
		}
		</script>');
	}
	elseif( !isset( $_SESSION['felogin']['user'] ) ){
		$sitecontent->add_site_content( '<center><hr /><div style="color:red; font-size:20px;">Bitte loggen Sie sich im Kasten ein!</div><b><a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">Passwort vergessen?</a>&nbsp;');
		if( $felogin['selfreg'] == 'on' ){
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">Registrieren</a>' );
		}
		$sitecontent->add_site_content( '</b><hr /></center>');
	}

}

if( !checklogin() ){
	$allgerr = '403';

	$sitecontent->set_title( 'Error - 403' );
}
?>
