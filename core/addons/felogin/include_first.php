<?php

defined('KIMB_CMS') or die('No clean Request');

$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/system/hash.js" type="text/javascript" ></script>');
$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js" type="text/javascript" ></script>');

$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$felogin['loginokay'] = $felogin['conf']->read_kimb_one( 'loginokay' );
$felogin['grlist'] = $felogin['conf']->read_kimb_one( 'grlist' );
$felogin['grs'] = explode( ',' , $felogin['grlist'] );
$felogin['siteid'] = $felogin['conf']->read_kimb_one( 'siteid' );

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


if( isset($_POST['feloginuser']) && isset($_POST['feloginpassw']) ){

	if($_SESSION["loginfehler"] == ''){ $_SESSION["loginfehler"] = '0'; }

	$userfile = new KIMBdbf( 'addon/felogin__user.kimb'  );
		
	$user = $_POST['feloginuser'];
	$userda = $userfile->search_kimb_xxxid( $user , 'user' );

	if($userda != false && $_SESSION["loginfehler"] <= 6 ){
 		$passhash = $_POST['feloginpassw'];
		$passpruef = sha1($userfile->read_kimb_id( $userda , 'passw' ).$_SESSION["loginsalt"]);
	
		if( $passhash == $passpruef ){
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
		$felogin['addonarea'] .= '<input type="text" name="feloginuser" placeholder="Username" ><br />';
		$felogin['addonarea'] .= '<input type="password" name="feloginpassw" placeholder="Passwort" id="pass" ><br />';
		$felogin['addonarea'] .= '<input type="submit" value="Login"><br />';
		$felogin['addonarea'] .= '</form><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['siteid'].'&amp;pwforg">Passwort vergessen?</a><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['siteid'].'&amp;register">Registrieren</a><br />';
	}
	else{
		$felogin['addonarea'] .= 'Hallo '.$_SESSION['felogin']['name'].',<br />Sie sind eingeloggt!<br /><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['siteid'].'&amp;logout"><button>Logout</button></a><br /><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['siteid'].'&amp;settings">Einstellungen</a><br />';
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

if( !checklogin() ){
	$allgerr = '403';
}

//print_r( $felogin );

?>
