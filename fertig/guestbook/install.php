<?php

if( !is_dir( __DIR__.'/../captcha/' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "guestbook" zu gewährleisten! ' , 'unknown' );

}
elseif( !$addoninclude->read_kimb_search_teilpl( 'ajax' , 'captcha' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "guestbook" zu gewährleisten! ' , 'unknown' );

}

if( !is_dir( __DIR__.'/../felogin/' ) ){

	$sitecontent->echo_message( 'Das Add-on "felogin" wurde nicht gefunden, bitte installieren und aktivieren Sie es um einen erweiterten Funktionsumfang von "guestbook" zu erhalten! ' , 'unknown' );

}
elseif( !$addoninclude->read_kimb_search_teilpl( 'first' , 'felogin' ) ){

	$sitecontent->echo_message( 'Das Add-on "felogin" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um einen erweiterten Funktionsumfang von "guestbook" zu erhalten! ' , 'unknown' );

}


?>
