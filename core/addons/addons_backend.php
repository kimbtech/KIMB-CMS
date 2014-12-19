<?php

defined('KIMB_Backend') or die('No clean Request');

//includes addons die first wollen

//evtl siteid, menueid, err verarbeiten

$addoninclude = new KIMBdbf('addon/includes.kimb');

$allfirst = $addoninclude->read_kimb_all_teilpl( 'backend' );

foreach( $allfirst as $name ){

	if(strpos($name, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}

	require_once(__DIR__.'/'.$name.'/include_backend.php');

}

?>
