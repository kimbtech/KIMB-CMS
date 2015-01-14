<?php

defined('KIMB_CMS') or die('No clean Request');

//check user vorhanden?

//send mailokay code

if( $_GET['addon'] == 'felogin' ){
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
	elseif(  isset( $_GET['captcha'] ) ){ 

		if( $_GET['captcha'] == $_SESSION['captcha'] ){
			echo 'ok';
		}
		else {
			echo 'nok';
		}

		die;
	}
	elseif(  isset( $_GET['mail'] ) ){

		if( filter_var( $_GET['mail'] , FILTER_VALIDATE_EMAIL) ){
			$code = makepassw( 50 );
			$_SESSION["mailcode"] = $code;

			$inhalt .= 'Hallo ,'."\r\n";
			$inhalt .= 'Ihr Code lautet:'.$code."\r\n\r\n";
			$inhalt .= $allgsysconf['sitename'];

			if( send_mail( $_GET['mail'] , $inhalt ) ){
				echo 'ok';
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
	elseif(  isset( $_GET['mailcode'] ) ){
		//check
	}

	die;
}
	
?>
