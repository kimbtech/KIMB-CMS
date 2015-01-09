<?php

defined('KIMB_Backend') or die('No clean Request');

if( !is_object( $html_out['file'] ) ){
	$html_out['file'] = new KIMBdbf( 'addon/html_out__file.kimb' );
}

$html_out['be'] = $html_out['file']->read_kimb_one( 'backend' );

if( $html_out['be'] == 'all' ){
	$html_out['cont'] = new KIMBdbf( 'addon/html_out__contbe.kimb' );
	
	$html_out['site'] = $html_out['cont']->read_kimb_one( 'site' );
	if( $html_out['site'] != '' ){
		$sitecontent->add_site_content( $html_out['site'] );
	}

	$html_out['message'] = $html_out['cont']->read_kimb_one( 'message' );
	if( $html_out['message'] != '' ){
		$sitecontent->echo_message( $html_out['message'] );
	}

	$html_out['header'] = $html_out['cont']->read_kimb_one( 'header' );
	if( $html_out['header'] != '' ){
		$sitecontent->add_html_header( $html_out['header'] );
	}
	
}

unset( $html_out );
	
?>
