<?php

defined('KIMB_Backend') or die('No clean Request');

if( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ){
	$req = substr( $_SERVER['REQUEST_URI'] , '0', '-'.strlen(strrchr( $_SERVER['REQUEST_URI'] , '?' )));
}
else{
	$req = $_SERVER['REQUEST_URI'];
}

if( substr( $req , -29 ) == 'kimb-cms-backend/syseinst.php' ){

	$sitecontent->echo_message( 'Sichern Sie die Inhalte des CMS in einer Datei!<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=im_export"><button>Los!</button></a>' );

}

?>
