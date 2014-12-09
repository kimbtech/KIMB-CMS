<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//BE-Login

if($_GET['todo'] == 'logout'){

	$loginfehler = $_SESSION["loginfehler"];
	session_destroy();
	session_start();
	$_SESSION["loginfehler"] = $loginfehler;	

	$sitecontent->echo_message('Sie wurden ausgeloggt!');
}

//Loginteil

if( isset($_POST['user']) && isset($_POST['pass']) ){

	if($_SESSION["loginfehler"] == ''){ $_SESSION["loginfehler"] = '0'; }

	$userfile = new KIMBdbf('/backend/users/list.kimb');
		
	$user = $_POST['user'];
	$userda = $userfile->search_kimb_xxxid( $user , 'user' );

	if($userda != false && $_SESSION["loginfehler"] <= 6 ){
 		$passhash = $_POST['pass'];
		$passpruef = sha1($userfile->read_kimb_id( $userda , 'passw' ).$_SESSION["loginsalt"]);
	
		if( $passhash == $passpruef ){
			//eingeloggt
			$sitecontent->echo_message('Sie wurden eingeloggt!');
			$_SESSION['loginokay'] = $allgsysconf['loginokay'];
			$_SESSION['name'] = $userfile->read_kimb_id( $userda , 'name' );
			$_SESSION['permission'] = $userfile->read_kimb_id( $userda , 'permiss' );
			$_SESSION['user'] = $user;
			$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
			$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
			unset($_SESSION["loginsalt"]);
		}
		else{
			$_SESSION["loginfehler"]++;
			$sitecontent->echo_error( 'Ihr Passwort ist falsch!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!' , 'unknown');
		}
		
	}
	else{
		$_SESSION["loginfehler"]++;
		$sitecontent->echo_error( 'Sie haben die maximale Anzahl an Versuchen erreicht oder Ihr Username ist inkorrekt!<br />Es sind nur 6 Versuche erlaubt, dies ist schon der '.$_SESSION["loginfehler"].'. Versuch!' , 'unknown');		
	}
}
elseif( $_SESSION['loginokay'] == $allgsysconf['loginokay'] ){
	$sitecontent->echo_message('Sie sind eingeloggt!');
	$sitecontent->add_site_content('<h2>Willkommen!</h2>');
	$sitecontent->add_site_content('Hallo '.$_SESSION['name'].',<br />Sie können jetzt Ihre Homepage bearbeiten!');
}
if( $_SESSION['loginokay'] != $allgsysconf['loginokay'] ){
	$loginsalt = sha1(mt_rand());
	$_SESSION["loginsalt"] = $loginsalt;

	$sitecontent->add_site_content( '<h2>Login</h2>' );
	$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/index.php" method="post" onsubmit="document.getElementById(\'pass\').value = SHA1(SHA1(document.getElementById(\'pass\').value)+\''.$loginsalt.'\');">
		<table><tr><td>Username:</td><td><input type="text" name="user"><br /></td></tr>
		<tr><td>Passwort:</td><td><input type="password" name="pass" id="pass" maxlength="32"><br /></td></tr></table>
		<input type="submit" value="Absenden">
		</form><br /><br />' );
	
	$sitecontent->add_site_content( '<noscript>' );
	$sitecontent->add_site_content( '<h3>Diese Seite benötigt Java Script!!</h3>' );
	$sitecontent->add_site_content( '</noscript>' );

}

$sitecontent->output_complete_site();
?>
