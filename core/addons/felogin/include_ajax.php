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

if(  isset( $_GET['user'] ) ){ 

	$userfile = new KIMBdbf('addon/felogin__user.kimb');

	$_GET['user'] = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $_GET['user'] ) );

	$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );

	if( $id == false ){
		echo 'ok';
	}
	else {
		echo 'nok';
	}

	die;
}
elseif(  isset( $_GET['captcha_code'] ) ){ 

	if( check_captcha() ){
		echo 'ok';
	}
	else {
		echo 'nok';
	}

	die;
}
elseif(  isset( $_GET['mail'] ) ){

	if( filter_var( $_GET['mail'] , FILTER_VALIDATE_EMAIL) && ( $_GET['lang'] == 'de' ||  $_GET['lang'] == 'en' ) && !empty( $_SESSION['captcha_code'] ) ){

		$aufruffile = new KIMBdbf( 'addon/felogin__mailaufr.kimb' );
		$ip = $aufruffile->search_kimb_xxxid( $_SERVER['REMOTE_ADDR'] , 'ip' );

		if( $ip != false ){
			$uhr = $aufruffile->read_kimb_id( $ip , 'uhr' );
			if( $aufruffile->read_kimb_id( $ip , 'time' ) >= '4' && time() - $uhr <= '84400' ){
				echo 'nok';
				die;
			}
			$id = $ip;

			if( time() - $uhr > '84400' ){
				$aufruffile->write_kimb_id( $id , 'del' );
			}
		}
		else{
			$id = $aufruffile->next_kimb_id();
		}

		$code = makepassw( 75 );
		$_SESSION["mailcode"] = $code;
		$_SESSION["email"] = $_GET['mail'];
		
		$mailtext = parse_ini_file ( __DIR__.'/lang_'.$_GET['lang'].'.ini' , true );
		$mailtext = $mailtext['mailtext']['code'];
		
		$s = array( '%sitename%' , '%br%', '%code%' );
		$r = array( $allgsysconf['sitename'], "\r\n", $code );
		$inhalt = str_replace( $s , $r , $mailtext );
		
		if( send_mail( $_GET['mail'] , $inhalt ) ){

			echo 'ok';

			if( !$aufruffile->read_kimb_id( $id , 'time' ) ){
				$aufruffile->write_kimb_id( $id , 'add' , 'time' , 1 );
			}
			else{
				$time = $aufruffile->read_kimb_id( $id , 'time' ) + 1;
				$aufruffile->write_kimb_id( $id , 'add' , 'time' , $time );
			}
			$aufruffile->write_kimb_id( $id , 'add' , 'ip' , $_SERVER['REMOTE_ADDR'] );
			$aufruffile->write_kimb_id( $id , 'add' , 'uhr' , time() );
		}
		else{
			echo 'nok';
		}
	}
	else{
		echo 'nok';
	}

	die;
}
elseif(  isset( $_GET['code'] ) ){
		
	if( $_GET['code'] == $_SESSION['mailcode'] ){
		echo 'ok';
	}
	else {
		echo 'nok';
	}

	die;
}
elseif( isset( $_GET['newuser'] ) ){
	check_backend_login( 'thirteen' );

	$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
	$felogin['requid'] = $felogin['conf']->read_kimb_one( 'requid' );

	$_SESSION['registerokay'] = 'yes';

	open_url( '/index.php?id='.$felogin['requid'].'&register' );
	die;

}
elseif( isset( $_GET['settings'] ) ){
	check_backend_login( 'thirteen' );

	$felogin['conf'] = new KIMBdbf( 'addon/felogin__conf.kimb'  );
	$felogin['requid'] = $felogin['conf']->read_kimb_one( 'requid' );

	$_SESSION['felogin']['loginokay'] = $felogin['conf']->read_kimb_one( 'loginokay' );
	$_SESSION['felogin']['user'] = $_GET['settings'];

	$_SESSION['felogin']['name'] = 'BE-Admin';

	open_url( '/index.php?id='.$felogin['requid'].'&settings' );
	die;

}
	
?>
