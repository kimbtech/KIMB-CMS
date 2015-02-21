<?php

defined('KIMB_Backend') or die('No clean Request');

$sitecontent->add_html_header('<script>
$(function() { 
	new nicEditor({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'}).panelInstance( \'nicedit1\');
	new nicEditor({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'}).panelInstance( \'nicedit2\');
});
</script>');


$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=html_out';
if( !is_object( $html_out['file'] ) ){
	$html_out['file'] = new KIMBdbf( 'addon/html_out__file.kimb' );
}
if( !is_object( $html_out['cont'] ) ){
	$html_out['cont'] = new KIMBdbf( 'addon/html_out__contbe.kimb' );
}

$sitecontent->add_site_content('<hr /><h2>"html_out" - Backend Ausgaben</h2>');

if( isset( $_POST['onoff'] ) ){

	if( $_POST['onoff'] == 'on' || $_POST['onoff'] == 'off' ){

		if( $_POST['onoff'] == 'on' && $html_out['file']->read_kimb_one( 'backend' ) != 'all' ){
			$html_out['file']->write_kimb_replace( 'backend' , 'all' );
			$sitecontent->echo_message( 'Ausgaben aktiviert!' );
		}
		elseif( $_POST['onoff'] == 'off' && $html_out['file']->read_kimb_one( 'backend' ) == 'all' ){
			$html_out['file']->write_kimb_replace( 'backend' , 'none' );
			$sitecontent->echo_message( 'Ausgaben deaktiviert!' );
		}

		$site = $html_out['cont']->read_kimb_one( 'site' );
		if( $_POST['site'] == '<br>' ){
			$_POST['site'] = '';
		}
		if( $site == '' && $_POST['site'] != '' ){
			$html_out['cont']->write_kimb_new( 'site' , $_POST['site'] );
			$sitecontent->echo_message( 'Seitenausgabe hinzugefügt!' );
		}
		elseif( $site != $_POST['site']  && $_POST['site'] != '' ){
			$html_out['cont']->write_kimb_replace( 'site' , $_POST['site'] );
			$sitecontent->echo_message( 'Seitenausgabe geändert!' );
		}
		elseif( $_POST['site'] == '' ){
			$html_out['cont']->write_kimb_delete( 'site' );
			$sitecontent->echo_message( 'Seitenausgabe entfernt!' );
		}

		$message = $html_out['cont']->read_kimb_one( 'message' );
		if( $_POST['message'] == '<br>' ){
			$_POST['message'] = '';
		}
		if( $message == '' && $_POST['message'] != '' ){
			$html_out['cont']->write_kimb_new( 'message' , $_POST['message'] );
			$sitecontent->echo_message( 'Meldung hinzugefügt!' );
		}
		elseif( $message != $_POST['message']  && $_POST['message'] != '' ){
			$html_out['cont']->write_kimb_replace( 'message' , $_POST['message'] );
			$sitecontent->echo_message( 'Meldung geändert!' );
		}
		elseif( $_POST['message'] == '' ){
			$html_out['cont']->write_kimb_delete( 'message' );
			$sitecontent->echo_message( 'Meldung entfernt!' );
		}

		$header = $html_out['cont']->read_kimb_one( 'header' );
		if( $header == '' && $_POST['header'] != '' ){
			$html_out['cont']->write_kimb_new( 'header' , $_POST['header'] );
			$sitecontent->echo_message( 'Header hinzugefügt!' );
		}
		elseif( $header != $_POST['header']  && $_POST['header'] != '' ){
			$html_out['cont']->write_kimb_replace( 'header' , $_POST['header'] );
			$sitecontent->echo_message( 'Header geändert!' );
		}
		elseif( $_POST['header'] == '' ){
			$html_out['cont']->write_kimb_delete( 'header' );
			$sitecontent->echo_message( 'Header entfernt!' );
		}

		$sitecontent->echo_message( 'Bitte öffnen Sie eine andere Seite um die Änderungen zu sehen!' );
	}
	else{
		$sitecontent->echo_message( 'Fehler' , 'unknown' );
	}
}

if( $html_out['file']->read_kimb_one( 'backend' ) == '' ){
	$html_out['file']->write_kimb_new( 'backend' , 'all' );
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

$sitecontent->add_site_content('<input name="onoff" type="radio" value="off" '.$off.'><span style="display:inline-block;" title="Ausgabe deaktiviert" class="ui-icon ui-icon-closethick"></span><input name="onoff" value="on" type="radio" '.$on.'><span style="display:inline-block;" title="Ausgabe aktiviert" class="ui-icon ui-icon-check"></span><br />');


$sitecontent->add_site_content('<textarea name="site" id="nicedit1" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'site' ).'</textarea> (Zusätzlicher Seiteninhalt &uarr; )<br />');

$sitecontent->add_site_content('<textarea name="message" id="nicedit2" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'message' ).'</textarea> (Allgemeine Meldung &uarr; )<br />');

$sitecontent->add_site_content('<textarea name="header" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'header' ).'</textarea> (Zusätzliche HTML-Header &uarr; )<br />');


$sitecontent->add_site_content('<input type="submit" value="Ändern"> </form>');


?>