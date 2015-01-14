<?php

defined('KIMB_CMS') or die('No clean Request');

//register (captcha, mailcheck), usereinstellungen, pw-forgot, form

if( !is_object( $felogin['file'] ) ){
	$felogin['file'] = new KIMBdbf( 'addon/html_out__file.kimb' );
}

$felogin['fe'] = $felogin['file']->read_kimb_one( 'fe' );

if( $felogin['fe'] == 'all' ){
	if( !is_object( $felogin['cont'] ) ){
		$felogin['cont'] = new KIMBdbf( 'addon/html_out__contfe.kimb' );
	}
	
	$felogin['site'] = $felogin['cont']->read_kimb_one( 'sitese' );
	if( $felogin['site'] != '' ){
		$sitecontent->add_site_content( $felogin['site'] );
	}

	$felogin['addon'] = $felogin['cont']->read_kimb_one( 'addonse' );
	if( $felogin['addon'] != '' ){
		$sitecontent->add_addon_area( $felogin['addon'] );
	}

	$felogin['title'] = $felogin['cont']->read_kimb_one( 'title' );
	if( $felogin['title'] != '' ){
		$sitecontent->set_title( $felogin['title'] );
	}
}

unset( $felogin );

?>
