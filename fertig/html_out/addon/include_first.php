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
	
	$html_out['site'] = $html_out['cont']->read_kimb_one( 'sitefi' );
	if( $html_out['site'] != '' ){
		$sitecontent->add_site_content( $html_out['site'] );
	}

	$html_out['addon'] = $html_out['cont']->read_kimb_one( 'addonfi' );
	if( $html_out['addon'] != '' ){
		$sitecontent->add_addon_area( $html_out['addon'] );
	}

	$html_out['header'] = $html_out['cont']->read_kimb_one( 'header' );
	if( $html_out['header'] != '' ){
		$sitecontent->add_html_header( $html_out['header'] );
	}

}

?>
