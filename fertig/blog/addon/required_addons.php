<?php

foreach ( array(
		'captcha' => 'CAPTCHA API',
		'guestbook' => 'Gästebuch/ Kommentare'
	) as $nameid => $name ){

	//Abhängikeiten
	if( check_addon( $nameid ) == array( false, false ) ){
		$sitecontent->echo_error( 'Das Add-on "'.$name.'" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "Blogging Tools" zu gewährleisten! ' , 'unknown' );
	}
	elseif( check_addon( $nameid ) == array( true, false ) ){
		$sitecontent->echo_error( 'Das Add-on "'.$name.'" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "Blogging Tools" zu gewährleisten! ' , 'unknown' );
	}
}

//Add-on API wish
$api = new ADDonAPI( 'blog' );
$api->set_fe( 'vorn', 'a' , 'no' );
?>