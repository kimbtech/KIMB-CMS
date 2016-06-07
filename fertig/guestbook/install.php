<?php
//Abhängigkeiten
if( check_addon( 'captcha' ) == array( false, false ) ){
	$sitecontent->echo_error( 'Das Add-on "CAPTCHA API" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "Gästebuch/ Kommentare" zu gewährleisten! ' , 'unknown' );
}
elseif( check_addon( 'captcha' ) == array( true, false ) ){
	$sitecontent->echo_error( 'Das Add-on "CAPTCHA API" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "Gästebuch/ Kommentare" zu gewährleisten! ' , 'unknown' );
}
//erweiterter Funktionumfang
if( check_addon( 'felogin' ) == array( false, false ) || check_addon( 'felogin' ) == array( true, false ) ){
	$sitecontent->echo_message( 'Das Add-on "Frontend Login" ist nicht aktivert. Sie können mit "Frontend Login" den Funktionsumfang von "Gästebuch/ Kommentare" erweitern!' );
}

//Add-on API wish
$a = new ADDonAPI( 'guestbook' );
$a->set_fe( 'hinten', 'a' , 'no' );
$a->set_funcclass( 'hinten' );
?>
