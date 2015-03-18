<?php

defined('KIMB_CMS') or die('No clean Request');

if( $_GET['addon'] == 'easy_publish' ){

	if( !empty( $_GET['user'] ) && $_GET['user'] == $_SESSION['user'] ){

		$conffile = new KIMBdbf( 'addon/easy_publish__user.kimb' );

		if( $conffile->write_kimb_new( 'user' , $_GET['user'] ) ){
			echo 'done';
			die;
		}
	}

	echo 'error';
	die;
}

?>
