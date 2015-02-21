<?php

defined('KIMB_CMS') or die('No clean Request');

require_once( __DIR__.'/tracking_codes.php' );

if( $analytics['conf']['anatool'] == 'pimg' ){
	$sitecontent->add_footer( $analytics['codes'] );
}
else{
	$sitecontent->add_html_header( $analytics['codes'] );
}

if( $analytics['conf']['anatool'] == 'p' && $analytics['toold']['pimg'] == 'on' ){
	$sitecontent->add_footer( $analytics['toold']['pimgcode'] );
}

if( $analytics['conffile']->read_kimb_one( 'infobann' ) == 'on' ){
	$sitecontent->add_html_header( '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>' );
	$sitecontent->add_html_header( '<style>'.$analytics['conffile']->read_kimb_one( 'ibcss' ).'</style>' );
	$sitecontent->add_html_header( '<script type="text/javascript">
	$(function() {
		if (document.cookie.indexOf("info") >= 0){
		
		}
		else {
			$( "#analysehinweis" ).css( "display" , "block" );
			document.cookie = "info=analyse; path=/;";
		}
	});
	</script>');

	$sitecontent->add_footer( '<div id="analysehinweis" style="display:none;">' );
	$sitecontent->add_footer( $analytics['conffile']->read_kimb_one( 'ibtext' ) );	
	$sitecontent->add_footer( '<button onclick="$( \'#analysehinweis\' ).css( \'display\' , \'none\' );">OK</button></p> ' );
	$sitecontent->add_footer( '</div> ' );
}


unset( $analytics );
?>
