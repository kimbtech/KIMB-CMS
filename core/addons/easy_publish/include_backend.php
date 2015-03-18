<?php

defined('KIMB_Backend') or die('No clean Request');

if( isset( $_GET['easypublishstart'] ) ){
	$_SESSION['easypublish']['do'] = 'yes';
}
elseif( isset( $_GET['easypublishstop'] ) ){
	$_SESSION['easypublish']['do'] = 'no';
}

if( $_SESSION['easypublish']['do'] == 'yes' && $_SESSION['loginokay'] == $allgsysconf['loginokay'] && !isset( $_GET['noeasypub'] ) ){

	require_once( __DIR__.'/do.php' );

}
elseif( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION['permission'] != 'less' ) {

	$addfile = new KIMBdbf( 'addon/easy_publish__user.kimb' );

	if( !$addfile->read_kimb_search( 'user' , $_SESSION['user'] ) ){

		$sitecontent->add_html_header('<script>
		$(function() {
			if (document.cookie.indexOf( "easypinfo" ) >= 0){
		
			}
			else {
				$( "#easypubinfo" ).show( "fast" );
				$( "#easypubinfo" ).dialog({
				resizable: false,
				height: 250,
				width: 300,
				modal: true,
				buttons: {
					"Los geht\'s": function() {
						$( this ).dialog( "close" );
						document.cookie = "easypinfo=yes; path=/;";
						window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?easypublishstart";
						return true;
					},
					"Später": function() {
						$( this ).dialog( "close" );
						document.cookie = "easypinfo=yes; path=/;";
						return false;
					},
					"Nein": function() {
						$( this ).dialog( "close" );
						$.get( "'.$allgsysconf['siteurl'].'/ajax.php?addon=easy_publish&user='.$_SESSION['user'].'" );
						document.cookie = "easypinfo=yes; path=/;";
						return false;
					}
				}
				});
			}
		});
		</script>');

		$sitecontent->add_site_content('<div style="display:none;"><div id="easypubinfo" title="Einführung!"><p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 70px 0;"></span>Brauchen Sie Hilfe beim Erstellen der ersten Inhalte?<br /><br />Lassen Sie sich durch die Erstellung von einer Seite inklusive Menü führen!</p></div></div>');

	}

	if( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ){
		$req = substr( $_SERVER['REQUEST_URI'] , '0', '-'.strlen(strrchr( $_SERVER['REQUEST_URI'] , '?' )));
	}
	else{
		$req = $_SERVER['REQUEST_URI'];
	}

	if( substr( $req , -26 ) == 'kimb-cms-backend/sites.php' && ( $_GET['todo'] == 'new' || !isset( $_GET['todo'] ) ) ){
		//seite neu
		$sitecontent->echo_message( 'Brauchen Sie Hilfe beim Erstellen der ersten Inhalte?<br />Lassen Sie sich durch die Erstellung von einer Seite inklusive Menü führen!<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?easypublishstart"><button>Los geht&apos;s!</button></a>' );
	}
}

?>
