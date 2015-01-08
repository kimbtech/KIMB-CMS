<?php

defined('KIMB_Backend') or die('No clean Request');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=html_out';
if( !is_object( $html_out['file'] ) ){
	$html_out['file'] = new KIMBdbf( 'addon/html_out__file.kimb' );
}
if( !is_object( $html_out['cont'] ) ){
	$html_out['cont'] = new KIMBdbf( 'addon/html_out__contbe.kimb' );
}

$sitecontent->add_site_content('<hr /><h2>"html_out" - Backend Ausgaben</h2>');

if( isset( $_POST['onoff'] ) ){

	echo 'post';

}

if( $html_out['file']->read_kimb_one( 'backend' ) != 'all' ){
	$off = ' checked="checked" ';
	$on = ' ';
}
else{
	$on = ' checked="checked" ';
	$off = ' ';
}

$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

$sitecontent->add_site_content('<input name="onoff" type="radio" value="off" '.$off.'><span style="display:inline-block;" title="Ausgabe deaktiviert" class="ui-icon ui-icon-closethick"></span><input name="onoff" value="all" type="radio" '.$on.'><span style="display:inline-block;" title="Ausgabe aktiviert" class="ui-icon ui-icon-check"></span><br />');


$sitecontent->add_site_content('<textarea name="site" style="width:60%; height:200px;" >'.$html_out['cont']->read_kimb_one( 'site' ).'</textarea> (Zusätzlicher Seiteninhalt)<br />');

$sitecontent->add_site_content('<textarea name="message" style="width:60%; height:200px;" >'.$html_out['cont']->read_kimb_one( 'message' ).'</textarea> (Immervorhandene Meldung)<br />');

$sitecontent->add_site_content('<textarea name="header" style="width:60%; height:200px;" >'.$html_out['cont']->read_kimb_one( 'header' ).'</textarea> (Zusätzliche HTML-Header)<br />');


$sitecontent->add_site_content('<input type="submit" value="Ändern"> <form>');


?>
