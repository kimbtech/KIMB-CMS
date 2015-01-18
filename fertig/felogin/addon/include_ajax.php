<?php

defined('KIMB_CMS') or die('No clean Request');

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


			$code = makepassw( 50 );
			$_SESSION["mailcode"] = $code;
			$_SESSION["email"] = $_GET['mail'];

			$inhalt .= 'Hallo ,'."\r\n";
			$inhalt .= 'Ihr Code lautet:'.$code."\r\n\r\n";
			$inhalt .= $allgsysconf['sitename'];

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

	die;
}
	
?>
