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

//jQuery und Hash werden benötigt
$sitecontent->add_html_header('<!-- Hash --><!-- jQuery -->');

//Daten von funcclass laden
$felogin = $addon_felogin_array;

if( isset( $_POST['logout'] ) ){
	$loginfehler = $_SESSION["loginfehler"];
	//Session leeren
	session_unset();
	//Session zerstören
	session_destroy();
	//Session neu aufesetzen
	//	jetzt ist alles, was mit dem User zu tun hatte weg
	session_start();
	$_SESSION["loginfehler"] = $loginfehler;

	$sitecontent->add_site_content( '<center><hr />'.$allgsys_trans['addons']['felogin']['logouttext'].'<hr /></center>' );
}

//Loginversuch?
if( !empty($_POST['feloginuser']) && !empty($_POST['feloginpassw']) ){
	
	//Prüfung der Daten
	//	Usernames dürfen nur a-z,0-9 enthalten
	//		Kulanz gegenüber GROSS-Schreibung
	$_POST['feloginuser'] = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $_POST['feloginuser'] ) );
	//	Hier wird nur ein SHA1 Hash des Passworts übergeben, also können hier auch nur Zeichen von A-Z,0-9,a-z vorhanden sein  
	$_POST['feloginpassw'] = preg_replace( "/[^A-Z0-9a-z]/" , "" , strtolower( $_POST['feloginpassw'] ) );

	//Hat der User schon falsche Loginversuche?, wenn nicht dann wohl 0
	if(  empty( $_SESSION["loginfehler"] ) ){
		$_SESSION["loginfehler"] = 0; 
	}

	//Userliste laden
	$userfile = new KIMBdbf( 'addon/felogin__user.kimb'  );
	
	//Übergabe Usernamen
	$user = $_POST['feloginuser'];
	//Nach dem User suchen
	$userda = $userfile->search_kimb_xxxid( $user , 'user' );

	//Ist der User vorhaden und hat er nicht zu viele Fehleversuche
	if($userda != false && $_SESSION["loginfehler"] <= 6 ){
		//Übergabe Hash
 		$passhash = $_POST['feloginpassw'];
		 //Hash zum Vergleich aus dbf
		$passpruef = sha1($userfile->read_kimb_id( $userda , 'passw' ).$_SESSION["loginsalt"]);
	
		//Hashes geich und User aktiviert?
		if( $passhash == $passpruef && $userfile->read_kimb_id( $userda , 'status' ) == 'on' ){
			//eingeloggt
			//Session füttern
			//	Loginokay
			$_SESSION['felogin']['loginokay'] = $felogin['loginokay'];
			//	Name
			$_SESSION['felogin']['name'] = $userfile->read_kimb_id( $userda , 'name' );
			//	Gruppe
			$_SESSION['felogin']['gruppe'] = $userfile->read_kimb_id( $userda , 'gruppe' );
			//	Username
			$_SESSION['felogin']['user'] = $user;
			//	an IP binden
			$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
			//	an Useragent binden
			$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
			//	Salt nicht mehr nötig
			unset($_SESSION["loginsalt"]);
		}
		//Hashes gleich und User deaktiviert?
		elseif( $passhash == $passpruef && $userfile->read_kimb_id( $userda , 'status' ) == 'off' ){
			//Medlung an der richtigen Stelle 
			if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
				$felogin['area'] .= '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['accoff'].'</div><br />';
			}
			else{
				$sitecontent->add_site_content( '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['accoff'].'</div><br />' );
			}
		}
		//Passwort falsch?
		else{
			//Loginfehler +1
			$_SESSION["loginfehler"]++;
			//Medlung an richtiger Stelle
			
			if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
				$felogin['area'] .= $accinfo.'<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />';
			}
			else{
				$sitecontent->add_site_content( $accinfo.'<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />' );
			}
		}
		
	}
	//Username falsch
	else{
		//Loginfehler +1
		$_SESSION["loginfehler"]++;
		//Medlung an richtiger Stelle
		//	Fehlermedlung immer gleich (User soll nicht wissen ob Username oder Passwort falsch)
		if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
			$felogin['area'] .= '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />';		
		}
		else{
			$sitecontent->add_site_content( '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />' );
		}
	}
}

//Loginsalt machen
$loginsalt = makepassw( 40 , '_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );
$_SESSION["loginsalt"] = $loginsalt;

$loginjavascript = '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/felogin/logindata.dev.js"></script>';

//Addonarea anzeigen?
if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
	//eingeloggt?
	if( !isset( $_SESSION['felogin']['user'] ) ){
		//nein
		
		//Loginformular anzeigen
		$felogin['addonarea'] .= '<form action="" method="post" onsubmit="return submitsys();" >';
		$felogin['addonarea'] .= '<input type="text" name="feloginuser" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" onkeydown="if(event.keyCode == 13){ return false; }" onchange="getsalt();"> <img src="'.$allgsysconf['siteurl'].'/load/system/spin_load.gif" title="Loading Userdata" style="display:none;" id="loadergif" width="20px" title="spin_load.gif"> <!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['username'].') <![endif]--><br />';
		$felogin['addonarea'] .= '<input type="password" name="feloginpassw" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" id="pass" onkeydown="if(event.keyCode == 13){ return true; }" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['passwort'].') <![endif]--><br />';
		$felogin['addonarea'] .= '<input type="submit" value="'.$allgsys_trans['addons']['felogin']['login'].'"><br />';
		$felogin['addonarea'] .= '</form><span id="passsalt" style="display:none;" >none</span><br />';
		//Passwort vergessen Link
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">'.$allgsys_trans['addons']['felogin']['pwforg'].'</a><br />';
		if( $felogin['selfreg'] == 'on' ){
			//wenn aktiviert -> Account erstellen
			$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">'.$allgsys_trans['addons']['felogin']['register'].'</a><br />';
		}
	}
	else{
		//ja
		
		//Willkommensnachricht
		$felogin['addonarea'] .= $allgsys_trans['addons']['felogin']['hallo'].' '.$_SESSION['felogin']['name'].',<br />'.$allgsys_trans['addons']['felogin']['eingeloggt'].'<br /><br />';
		//Logout Button
		$felogin['addonarea'] .= '<form action="" method="post"><input type="hidden" name="logout" value="yes"><input type="submit" value="'.$allgsys_trans['addons']['felogin']['logout'].'" ></form><br /><br />';
		//Link zu Usereinstellungen
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;settings#goto">'.$allgsys_trans['addons']['felogin']['einst'].'</a><br />';
	}

	//Gerüst der Addonarea
	$felogin['area'] .= '<h2>'.$allgsys_trans['addons']['felogin']['login'].'</h2>';
	//Div für Form usw.
	$felogin['area'] .= '<div id="felogin">'.$allgsys_trans['addons']['felogin']['jsinfo'].'</div>';

	//Addonarea hinzufügen
	$sitecontent->add_addon_area( $felogin['area'] );

	//das Geüst wird per JavaScript gefüllt
	//Funktion um Passwort mit salt zu hashen
	$sitecontent->add_html_header( '<script> var htmlcodeform = '.json_encode( $felogin['addonarea'] ).'; var loginrandsalt = "'.$loginsalt.'"; var siteurl = "'.$allgsysconf['siteurl'].'";</script>');
	$sitecontent->add_html_header( $loginjavascript );
}

//wird aktuell die Seite für alle Loginaufgaben angefragt?
if( $_GET['id'] == $felogin['requid'] ){

	//keine Addonarea?
	if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'off' ){
		//das geiche wie bei der Addonarea nur hier direkt auf die Seite
		if( !isset( $_SESSION['felogin']['user'] ) ){
			$felogin['formareal'] .= '<form action="" method="post" onsubmit="return submitsys();" >';
			$felogin['formareal'] .= '<input type="text" name="feloginuser" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" onkeydown="if(event.keyCode == 13){ return false; }" onchange="getsalt();" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['username'].') <![endif]-->';
			$felogin['formareal'] .= '<input type="password" name="feloginpassw" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" id="pass" > <!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['passwort'].') <![endif]-->';
			$felogin['formareal'] .= '<input type="submit" value="'.$allgsys_trans['addons']['felogin']['login'].'"> <span style="width:20px; display:inline-block;"><img src="'.$allgsysconf['siteurl'].'/load/system/spin_load.gif" title="Loading Userdata" style="display:none;" id="loadergif" width="20px" title="spin_load.gif"></span><br />';
			$felogin['formareal'] .= '</form><span id="passsalt" style="display:none;" >none</span><br />';
			$felogin['formareal'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">'.$allgsys_trans['addons']['felogin']['pwforg'].'</a>&nbsp;';
			if( $felogin['selfreg'] == 'on' ){
				$felogin['formareal'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">'.$allgsys_trans['addons']['felogin']['register'].'</a><br />';
			}
		}
		else{
			$felogin['formareal'] .= $allgsys_trans['addons']['felogin']['hallo'].' '.$_SESSION['felogin']['name'].', '.$allgsys_trans['addons']['felogin']['eingeloggt'].'<br />';
			$felogin['formareal'] .= '<form action="" method="post"><input type="hidden" name="logout" value="yes">';
			$felogin['formareal'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;settings#goto">'.$allgsys_trans['addons']['felogin']['einst'].'</a> <input type="submit" value="'.$allgsys_trans['addons']['felogin']['logout'].'" ></form>';
		}

		$sitecontent->add_site_content( '<hr /><center><h1>'.$allgsys_trans['addons']['felogin']['login'].'</h1><div id="felogin">'.$allgsys_trans['addons']['felogin']['jsinfo'].'</div></center><hr />' );

		$sitecontent->add_html_header( '<script> var htmlcodeform = '.json_encode( $felogin['formareal'] ).'; var loginrandsalt = "'.$loginsalt.'"; var siteurl = "'.$allgsysconf['siteurl'].'";</script>');
		$sitecontent->add_html_header( $loginjavascript );
	}
	//nicht eingeloggt und eine Addonarea
	elseif( !isset( $_SESSION['felogin']['user'] ) ){
		//Hinweis wo man sich einloggen kann
		//Link zu Passwort vergessen und Account erstellen
		$sitecontent->add_site_content( '<center><hr /><h1>'.$allgsys_trans['addons']['felogin']['kasten'].'</h1><b><a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">'.$allgsys_trans['addons']['felogin']['pwforg'].'</a>&nbsp;');
		if( $felogin['selfreg'] == 'on' ){
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">'.$allgsys_trans['addons']['felogin']['register'].'</a>' );
		}
		$sitecontent->add_site_content( '</b><hr /></center>');
	}

}

//Menüpunkte für welche ein User keine Rechte hat ausblenden

//IDfile laden
$idfile = new KIMBdbf('menue/allids.kimb');

//alle Seiten die von felogin geschützt werden
$dissites = $felogin['allsites'];

//wenn User in einer Gruppe
if( !empty( $_SESSION['felogin']['gruppe'] ) ){
	//IDs lesen, die User sehen darf
	$nodis =  $felogin['teilesite'][ $_SESSION['felogin']['gruppe']];
	//zu verbietende Seiten (wenn User eingeloggt, nicht alle Seiten)
	$dissites = array();
	//alle Seiten von felogin durchgehen
	foreach( $felogin['allsites'] as $val ){
		//ist die Seite nicht für diese Gruppe erlaubt zu "diallowsites" hinzufügen 
		if( !in_array( $val, $nodis) ){
			$dissites[] = $val;	
		}
	}
}

//felogin arbeitet mit SiteIDs, hide_menue() aber mit RequestIDs
//	alle umrechnen 
foreach( $dissites as $val ){
	//ID in IDfile suchen 
	$id = $idfile->search_kimb_xxxid( $val, 'siteid' );
	if( $id != false ){
		//Array mit allen zu versteckenden ReqeuestIDs erstellen 
		$disarray[] = $id;	
	}
}

//verstecken
$sitecontent->hide_menu( $disarray );

//darf der User die aktuell geforderte Seite sehen?
if( !check_felogin_login() ){
	//Error 403 setzen 
	//	CMS gibt dann automatisch eine Fehler aus
	$allgerr = '403';

	//Titel der Seite verstecken
	$sitecontent->set_title( 'Error - 403' );
}

//Die Daten von felogin für FE second sichern
$addon_felogin_array = $felogin;
unset ( $felogin );
?>
