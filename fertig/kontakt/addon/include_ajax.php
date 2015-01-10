<?php

defined('KIMB_CMS') or die('No clean Request');

if( $_GET['addon'] == 'kontakt' ){ 

	if( $_SESSION['code'] == $_GET['code'] ){
		$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );

		echo $kontakt['file']->read_kimb_one( 'othercont' );
	}
	else{
		http_response_code(400);
	}

	die;

}


?>
