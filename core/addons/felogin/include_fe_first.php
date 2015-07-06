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

$sitecontent->add_html_header('<!-- Hash --><!-- jQuery -->');

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

	$sitecontent->add_site_content( '<center><hr />'.$allgsys_trans['addons']['felogin']['logout'].'<hr /></center>' );
}

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
		elseif( $userfile->read_kimb_id( $userda , 'status' ) == 'off' ){
			if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
				$felogin['area'] .= '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['accoff'].'</div><br />';
			}
			else{
				$sitecontent->add_site_content( '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['accoff'].'</div><br />' );
			}
		}
		else{
			$_SESSION["loginfehler"]++;
			if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
				$felogin['area'] .= $accinfo.'<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />';
			}
			else{
				$sitecontent->add_site_content( $accinfo.'<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />' );
			}
		}
		
	}
	else{
		$_SESSION["loginfehler"]++;
		if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'on' ){
			$felogin['area'] .= '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />';		
		}
		else{
			$sitecontent->add_site_content( '<div style="color:red;">'.$allgsys_trans['addons']['felogin']['loginfehler1'].' '.$_SESSION["loginfehler"].'. '.$allgsys_trans['addons']['felogin']['loginfehler2'].'</div><br />' );
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
		$felogin['addonarea'] .= '<input type="text" name="feloginuser" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['username'].') <![endif]--><br />';
		$felogin['addonarea'] .= '<input type="password" name="feloginpassw" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" id="pass" ><!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['passwort'].') <![endif]--><br />';
		$felogin['addonarea'] .= '<input type="submit" value="'.$allgsys_trans['addons']['felogin']['login'].'"><br />';
		$felogin['addonarea'] .= '</form><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">'.$allgsys_trans['addons']['felogin']['pwforg'].'</a><br />';
		if( $felogin['selfreg'] == 'on' ){
			$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">'.$allgsys_trans['addons']['felogin']['register'].'</a><br />';
		}
	}
	else{
		$felogin['addonarea'] .= $allgsys_trans['addons']['felogin']['hallo'].' '.$_SESSION['felogin']['name'].',<br />'.$allgsys_trans['addons']['felogin']['eingeloggt'].'<br /><br />';
		$felogin['addonarea'] .= '<form action="" method="post"><input type="hidden" name="logout" value="yes"><input type="submit" value="'.$allgsys_trans['addons']['felogin']['logout'].'" ></form><br /><br />';
		$felogin['addonarea'] .= '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;settings#goto">'.$allgsys_trans['addons']['felogin']['einst'].'</a><br />';
	}

	$felogin['area'] .= '<h2>'.$allgsys_trans['addons']['felogin']['login'].'</h2>';
	$felogin['area'] .= '<div id="felogin">'.$allgsys_trans['addons']['felogin']['jsinfo'].'</div>';

	$sitecontent->add_addon_area( $felogin['area'] );

	$sitecontent->add_html_header('<script>
	$(function() { $("div#felogin").html( \''.$felogin['addonarea'].'\' ); });
	function hash() { document.getElementById(\'pass\').value = SHA1(SHA1(document.getElementById(\'pass\').value)+\''.$loginsalt.'\'); }
</script>');
}
if( $_GET['id'] == $felogin['requid'] ){

	if( $felogin['conf']->read_kimb_one( 'addonarea' ) == 'off' ){
		if( !isset( $_SESSION['felogin']['user'] ) ){
			$felogin['formareal'] .= '<form action="" method="post" onsubmit="hash();" >';
			$felogin['formareal'] .= '<input type="text" name="feloginuser" placeholder="'.$allgsys_trans['addons']['felogin']['username'].'" > <!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['username'].') <![endif]-->';
			$felogin['formareal'] .= '<input type="password" name="feloginpassw" placeholder="'.$allgsys_trans['addons']['felogin']['passwort'].'" id="pass" > <!--[if lt IE 10]> ('.$allgsys_trans['addons']['felogin']['passwort'].') <![endif]-->';
			$felogin['formareal'] .= '<input type="submit" value="'.$allgsys_trans['addons']['felogin']['login'].'"><br />';
			$felogin['formareal'] .= '</form><br />';
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

		$sitecontent->add_html_header('<script>
		$(function() { $("div#felogin").html( \''.$felogin['formareal'].'\' ); });
		function hash() { document.getElementById(\'pass\').value = SHA1(SHA1(document.getElementById(\'pass\').value)+\''.$loginsalt.'\'); }
</script>');
	}
	elseif( !isset( $_SESSION['felogin']['user'] ) ){
		$sitecontent->add_site_content( '<center><hr /><h1>'.$allgsys_trans['addons']['felogin']['kasten'].'</h1><b><a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;pwforg#goto">'.$allgsys_trans['addons']['felogin']['pwforg'].'</a>&nbsp;');
		if( $felogin['selfreg'] == 'on' ){
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid'].'&amp;register#goto">'.$allgsys_trans['addons']['felogin']['register'].'</a>' );
		}
		$sitecontent->add_site_content( '</b><hr /></center>');
	}

}

//disable menue
$idfile = new KIMBdbf('menue/allids.kimb');

$dissites = $felogin['allsites'];

if( !empty( $_SESSION['felogin']['gruppe'] ) ){
	$nodis =  $felogin['teilesite'][ $_SESSION['felogin']['gruppe']];
	$dissites = array();
	foreach( $felogin['allsites'] as $val ){
		if( !in_array( $val, $nodis) ){
			$dissites[] = $val;	
		}
	}
}

foreach( $dissites as $val ){
	$id = $idfile->search_kimb_xxxid( $val, 'siteid' );
	if( $id != false ){
		$disarray[] = $id;	
	}
}

$sitecontent->hide_menu( $disarray );

if( !check_felogin_login() ){
	$allgerr = '403';

	$sitecontent->set_title( 'Error - 403' );
}

$addon_felogin_array = $felogin;
unset ( $felogin );
?>
