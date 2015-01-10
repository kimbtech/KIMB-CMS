<?php

defined('KIMB_Backend') or die('No clean Request');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=kontakt';


if( isset( $_POST['mail'] ) ){
	$string = $_POST['mail'];
	$im = imagecreate (400, 30);
	imagecolorallocate( $im , 255 , 255 , 255 );
	$color = imagecolorallocate( $im , 0 , 0 , 0 );
	imagettftext ($im, 20, 0, 5, 25, $color, __DIR__.'/Ubuntu-B.ttf', $string );
	imagepng( $im , __DIR__.'/../../../load/addondata/kontakt/mail.png' );
	imagedestroy( $im );
}


$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

$sitecontent->add_site_content('<input name="mail" type="text" value="" ><br />');

$sitecontent->add_site_content('<input name="other" type="text" value="" ><br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern"> <form>');


?>
