<?php

//Abhängikeiten
if( !is_dir( __DIR__.'/../felogin/' ) ){

	$sitecontent->echo_error( 'Das Add-on "felogin" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "daten" zu gewährleisten! ' , 'unknown' );

}
elseif( !$this->addoninclude->read_kimb_search_teilpl( 'ajax' , 'felogin' ) ){

	$sitecontent->echo_error( 'Das Add-on "felogin" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "daten" zu gewährleisten! ' , 'unknown' );

}

//Add-on API wish
$a = new ADDonAPI( 'felogin' );
$a->set_funcclass( 'vorn' );

?>
