<?php

defined('KIMB_Backend') or die('No clean Request');

require_once( __DIR__.'/tracking_codes.php' );

if( $analytics['conf']['anatool'] == 'pimg' ){
	$sitecontent->add_site_content( $analytics['codes'] );
}
else{
	$sitecontent->add_html_header( $analytics['codes'] );
}

if( $analytics['conffile']->read_kimb_one( 'infobann' ) == 'on' ){
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

	$sitecontent->add_site_content( '<div id="analysehinweis" style="display:none;">' );
	$sitecontent->add_site_content( $analytics['conffile']->read_kimb_one( 'ibtext' ) );	
	$sitecontent->add_site_content( '<button onclick="$( \'#analysehinweis\' ).css( \'display\' , \'none\' );">OK</button></p> ' );
	$sitecontent->add_site_content( '</div> ' );
}
	
?>
