<?php

defined('KIMB_Backend') or die('No clean Request');

require_once( __DIR__.'/tracking_codes.php' );

$sitecontent->add_html_header( $analytics['codes'] );
	
?>
