<?php

//Abhängikeiten
if( check_addon( 'captcha' ) == array( false, false ) ){
	$sitecontent->echo_error( 'Das Add-on "CAPTCHA API" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "Frontend Login" zu gewährleisten! ' , 'unknown' );
}
elseif( check_addon( 'captcha' ) == array( true, false ) ){
	$sitecontent->echo_error( 'Das Add-on "CAPTCHA" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "Frontend Login" zu gewährleisten! ' , 'unknown' );
}

$sitecontent->echo_message( '<b>Sollte nach dem Update kein Login mehr möglich sein, weisen Sie bitte alle Ihre Nutzer darauf hin, dass alle Passwörter neu vergeben werden müssen!</b><br /><i>Man kann ein neues Passwort über "Passwort vergessen?" erhalten.</i>', 'WICHTIG' );


//Add-on API wish
$a = new ADDonAPI( 'felogin' );
$a->set_fe( 'vorn', 'a' , 'all' );
$a->set_funcclass( 'vorn' );


//FullHTMLCache Wish Werte
//	für alle Seiten komplett deaktivieren und
//		nötig da Seiten je nach Loginstatus ausgeblendet werden müssen und aktueller
//		Loginstatus in Add-on Area verfügbar sein muss, außerdem für Einstellungen, PW vergessen und Register nötig!
//	Array an API übergeben	
$a->full_html_cache_wish( 'set', array( 
	'a' => array(
		'off', array( 'POST' => array(), 'GET' => array(), 'COOKIE' => array() )
	)
 ) );

?>
