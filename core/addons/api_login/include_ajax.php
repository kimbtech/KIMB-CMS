<?php

defined('KIMB_CMS') or die('No clean Request');

if( $_GET['addon'] == 'api_login' ){

	$file = new KIMBdbf( 'addon/api_login__dest.kimb' );

	if( !empty( $_GET['id'] ) && is_numeric( $_GET['id'] ) ){
		$read = $file->read_kimb_one( 'dest'.$_GET['id'] );
		if( !empty( $read ) ){

			$postarr['loginokay'] = $_SESSION['felogin']['loginokay'];
			$postarr['ip'] = $_SERVER['REMOTE_ADDR'];
			$postarr['ua'] = $_SERVER['HTTP_USER_AGENT'];
			$postarr['gr'] = $_SESSION['felogin']['gruppe'];
			$postarr['us'] = $_SESSION['felogin']['user'];
			$postarr['na'] = $_SESSION['felogin']['name'];
			
			echo $jsondata = json_encode( $postarr );
			$openurl = $read.'?id='.$id;

			$auth = $file->read_kimb_one( 'auth' );
			$response = http_post_fields( $read , array( 'auth' => $auth, 'jsondata' => $jsondata ) );
			if( $response == 'taken' ){
				header( 'Location: '.$openurl );
				die;
			}
			else{
				echo( 'Error No/Wrong Destination Answer' );
			}
		}
		else{
			echo( 'Error Destination Not Found' );
		}
	}
	else{
		echo( 'Error ID Not Set' );
	}
	
	die;
}

?>
