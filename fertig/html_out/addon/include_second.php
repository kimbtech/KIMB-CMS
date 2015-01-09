<?php

defined('KIMB_CMS') or die('No clean Request');

if( !is_object( $html_out['file'] ) ){
	$html_out['file'] = new KIMBdbf( 'addon/html_out__file.kimb' );
}

$html_out['fe'] = $html_out['file']->read_kimb_one( 'fe' );

if( $html_out['fe'] == 'all' ){
	if( !is_object( $html_out['cont'] ) ){
		$html_out['cont'] = new KIMBdbf( 'addon/html_out__contfe.kimb' );
	}
	
	$html_out['site'] = $html_out['cont']->read_kimb_one( 'sitese' );
	if( $html_out['site'] != '' ){
		$sitecontent->add_site_content( $html_out['site'] );
	}

	$html_out['addon'] = $html_out['cont']->read_kimb_one( 'addonse' );
	if( $html_out['addon'] != '' ){
		$sitecontent->add_addon_area( $html_out['addon'] );
	}

	$html_out['title'] = $html_out['cont']->read_kimb_one( 'title' );
	if( $html_out['title'] != '' ){
		$sitecontent->set_title( $html_out['title'] );
	}
}

unset( $html_out );

?>
