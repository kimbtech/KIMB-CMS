<?php

if( !is_dir( __DIR__.'/../captcha/' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "felogin" zu gewährleisten! ' , 'unknown' );

}
elseif( !$addoninclude->read_kimb_search_teilpl( 'ajax' , 'captcha' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "felogin" zu gewährleisten! ' , 'unknown' );

}

?>
