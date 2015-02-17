<?php

defined('KIMB_CMS') or die('No clean Request');

require_once( __DIR__.'/tracking_codes.php' );

$sitecontent->add_html_header( $analytics['codes'] );

if( $analytics['conf']['anatool'] == 'p' && $analytics['toold']['pimg'] == 'on' ){
	$sitecontent->add_footer( $analytics['codes']['pimg'] );
}


unset( $analytics );
?>
