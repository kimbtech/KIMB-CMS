<?php

defined('KIMB_Backend') or die('No clean Request');

require_once( __DIR__.'/cmsv.php' );

$updatefile = new KIMBdbf( 'addon/auto_update__info.kimb' );

if( ini_get('allow_url_fopen') && $_SESSION['loginokay'] == $allgsysconf['loginokay'] && ( $_SESSION['permission'] == 'more' || $_SESSION['permission'] == 'fourteen' ) ) {

	$lasttime = $updatefile->read_kimb_one( 'lasttime' );

	if( empty( $lasttime ) ){
		$updatefile->write_kimb_new( 'lasttime', '100' );
		$updatefile->write_kimb_new( 'lastanswer', 'no' );
		$updatefile->write_kimb_new( 'newv', 'none' );
	}
	
	//alle 3 Tage testen
	if( $lasttime + 259200 < time() ){

		$updatefile->write_kimb_replace( 'lasttime', time() );

		require_once( __DIR__.'/check.php' );

		$updatefile->write_kimb_replace( 'lastanswer', $update );

		$updatefile->write_kimb_replace( 'newv', $updatearr['newv'] );
	}
	else{
		$update = $updatefile->read_kimb_one( 'lastanswer' );
	}

	if( $update == 'yes' ){

		$sitecontent->add_html_header('<script>
		$(function() {
			if (document.cookie.indexOf( "uinfo" ) >= 0){
		
			}
			else {
				$( "#del-confirm" ).show( "fast" );
				$( "#del-confirm" ).dialog({
				resizable: false,
				height: 250,
				modal: true,
				buttons: {
					"Update": function() {
						$( this ).dialog( "close" );
						document.cookie = "uinfo=update; path=/;";
						window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=auto_update";
						return true;
					},
					"Später": function() {
						$( this ).dialog( "close" );
						document.cookie = "uinfo=update; path=/;";
						return false;
					}
				}
				});
			}
		});
		</script>');

		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Update verfügbar!"><p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 50px 0;"></span>Es ist eine neue Version des KIMB-CMS verfügbar, möchten Sie das Update gleich durchführen?</p></div></div>');

	}
}
else{
	$sitecontent->add_html_header( "<script>document.cookie = 'uinfo=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';</script>" );
}

?>
