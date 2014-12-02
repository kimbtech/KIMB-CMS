<?php

defined('KIMB_CMS') or die('No clean Request');

if( check_for_kimb_file( '/site/site_'.$allgsiteid.'.kimb' ) ){
	$sitefile = new KIMBdbf( '/site/site_'.$allgsiteid.'.kimb' );
}
else{
	$sitecontent->echo_error( 'Diese Seite existiert nicht!' , '404' );
	$allgerr = '404';
}

if( $allgerr == '403' ){
	$sitecontent->echo_error( 'Sie haben keinen Zugriff auf diese Seite!' , '403' );
}
elseif( is_object( $sitefile ) && !isset( $allgerr ) ){

	$seite['title'] = $sitefile->read_kimb_one( 'title' );
	$seite['header'] = $sitefile->read_kimb_one( 'header' );
	$seite['keywords'] = $sitefile->read_kimb_one( 'keywords' );
	$seite['description'] = $sitefile->read_kimb_one( 'description' );
	$seite['inhalt'] = $sitefile->read_kimb_one( 'inhalt' );
	$seite['time'] = $sitefile->read_kimb_one( 'time' );
	$seite['made_user'] = $sitefile->read_kimb_one( 'made_user' );
	$seite['footer'] = $sitefile->read_kimb_one( 'footer' );
	$seite['req_id'] = $_GET['id'];

	$sitecontent->add_site($seite);

}
elseif( !isset( $allgerr ) ){
	$sitecontent->echo_error( 'Fehler beim Erstellen des Seiteninhalts !' );
	$allgerr = 'unknown';
}
?>
