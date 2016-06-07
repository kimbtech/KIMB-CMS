<?php
//PHP GD?
if( !function_exists('gd_info') ) {
	$sitecontent->echo_error('Captcha benötigt PHP_GD!');	
}
//Add-on API wish
$a = new ADDonAPI( 'captcha' );
$a->set_funcclass( 'vorn' );
?>
