<?php

defined('KIMB_CMS') or die('No clean Request');

$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );

if( $guestbook['file']->read_kimb_search_teilpl( 'siteid' , $allgsiteid ) ){
if( check_for_kimb_file( 'addon/guestbook__id_'.$allgsiteid.'.kimb' ) ){


	//checklogin();

	$sitecontent->add_html_header('<style>'.$guestbook['file']->read_kimb_one( 'css' ).'</style>');

	$guestbook['sitefile'] = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'.kimb' );
	$guestbook['ids'] = explode( ',' , $guestbook['sitefile']->read_kimb_one( 'idlist' ) );
	foreach( $guestbook['ids'] as $guestbook['id'] ){
		$guestbook['alles'][] = $guestbook['sitefile']->read_kimb_id( $guestbook['id'] );
	}

	$guestbook['output'] = "\r\n".'<hr /><br />'."\r\n";

	foreach( $guestbook['alles'] as $guestbook['einer'] ){
		if( $guestbook['einer']['status'] == 'on' ){

			$guestbook['output'] .= '<div id="guest" >'."\r\n";		
			$guestbook['output'] .= '<div id="guestname" >'.$guestbook['einer']['name']."\r\n";
			$guestbook['output'] .= '<span id="guestdate">'.date( 'd-m-Y H:i:s' , $guestbook['einer']['time'] ).'</span>'."\r\n";
			$guestbook['output'] .= '</div>'."\r\n";
			$guestbook['output'] .= $guestbook['einer']['cont']."\r\n";
			$guestbook['output'] .= '</div>'."\r\n\r\n";

		}
	}


	$sitecontent->add_site_content(	$guestbook['output'] );
	

}	
}

unset( $guestbook );
?>
