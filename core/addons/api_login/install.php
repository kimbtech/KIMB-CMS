<?php

//Abhängikeiten
if( !is_dir( __DIR__.'/../felogin/' ) ){

	$sitecontent->echo_error( 'Das Add-on "felogin" wurde nicht gefunden, bitte installieren und aktivieren Sie es!' );

}
elseif( !$this->addoninclude->read_kimb_search_teilpl( 'first' , 'felogin' ) ){

	$sitecontent->echo_error( 'Das Add-on "felogin" scheint nicht aktiviert zu sein, bitte aktivieren Sie es!' );

}

?>