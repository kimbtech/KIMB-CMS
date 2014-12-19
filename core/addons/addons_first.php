<?php

defined('KIMB_CMS') or die('No clean Request');

//includes addons die first wollen

//evtl siteid, menueid, err verarbeiten

$addoninclude = new KIMBdbf('addon/includes.kimb');

$allfirst = $addoninclude->read_kimb_all_teilpl( 'first' );

foreach( $allfirst as $name ){

	if(strpos($name, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}

	require_once(__DIR__.'/'.$name.'/include_first.php');

}

?>
