<?php

defined('KIMB_Backend') or die('No clean Request');

if( substr( $_SERVER['REQUEST_URI'] , -26 ) == 'kimb-cms-backend/index.php' ){

	if( !isset( $_SESSION['admin_login_mail']['datengesanz'] ) ){
		$_SESSION['admin_login_mail']['datengesanz'] = 0;
	}

	if( isset($_POST['user']) || isset($_POST['pass']) ){
		$_SESSION['admin_login_mail']['datengesanz']++;
	}

	if( $_SESSION['admin_login_mail']['datengesanz'] == 4 ){
		
		$text = 'Hallo Admin,'."\n\r".' es wurden am Backend gerade mindestens 3 fehlerhafte Loginversuche festgestellt!'."\n\r\n\r";
		$text .= 'Zeit:'.date("d.m.Y \u\m H:i:s")."\n\r".'IP:'.$_SERVER['REMOTE_ADDR']."\n\r".'Useragent: '.$_SERVER['HTTP_USER_AGENT']."\n\r";

		send_mail( $allgsysconf['adminmail'] , $text );
	}
}

if( !isset( $_SESSION['admin_login_mail']['infomail'] ) ){
	if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] ){

		$text = 'Hallo '.$_SESSION['name'].','."\n\r".' Ihre Logindaten wurden gerade fuer ein Login im Backend verwendet'."\n\r\n\r";
		$text .= 'Zeit:'.date("d.m.Y \u\m H:i:s")."\n\r".'IP:'.$_SERVER['REMOTE_ADDR']."\n\r".'Useragent: '.$_SERVER['HTTP_USER_AGENT']."\n\r";

		$userfile = new KIMBdbf('backend/users/list.kimb');

		$id = $userfile->search_kimb_xxxid( $_SESSION['user'] , 'user' );		
		if( $id != false ){
			$maila = $userfile->read_kimb_id( $id , 'mail' );
			send_mail( $maila , $text );
		}
	
		$_SESSION['admin_login_mail']['infomail'] = 'send';
	}
}
?>
