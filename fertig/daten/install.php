<?php
//Abhängikeiten
if( check_addon( 'felogin' ) == array( false, false ) ){
	$sitecontent->echo_error( 'Das Add-on "Frontend Login" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "Cloudspeicher für User" zu gewährleisten! ' , 'unknown' );
}
elseif( check_addon( 'felogin' ) == array( true, false ) ){
	$sitecontent->echo_error( 'Das Add-on "Frontend Login" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "Cloudspeicher für User" zu gewährleisten! ' , 'unknown' );
}


//Add-on API wish
$a = new ADDonAPI( 'daten' );
$a->set_funcclass( 'hinten' );

?>
