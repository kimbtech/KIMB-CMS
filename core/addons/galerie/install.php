<?php

//PHP GD?
if( !function_exists('gd_info') ) {
	$sitecontent->echo_error('Bildergalerie benötigt PHP_GD!');	
}

//Add-on API wish
$a = new ADDonAPI( 'galerie' );
$a->set_be( 'hinten', 'other_filemanager' , 'more,less,seventeen' );
$a->set_fe( 'vorn', 'a' , 'no' );
$a->set_funcclass( 'hinten' );

?>
