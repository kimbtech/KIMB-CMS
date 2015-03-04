<?php

defined('KIMB_CMS') or die('No clean Request');

if( $galerie['c']['pos'] == 'bottom' ){
	$sitecontent->add_site_content( '<div class="imggallerydisplayhere" style="background-color:#ddd; border-radius:15px;" >Bitte aktivieren Sie für die Bildergalerie JavaScript!</div>' );
}

?>
