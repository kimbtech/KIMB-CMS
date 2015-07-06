<?php

//Abhängikeiten
if( !is_dir( __DIR__.'/../captcha/' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "felogin" zu gewährleisten! ' , 'unknown' );

}
elseif( !$this->addoninclude->read_kimb_search_teilpl( 'ajax' , 'captcha' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "felogin" zu gewährleisten! ' , 'unknown' );

}

//Add-on API wish
$a = new ADDonAPI( 'felogin' );
$a->set_fe( 'vorn', 'a' , 'all' );
$a->set_funcclass( 'vorn' );

?>
