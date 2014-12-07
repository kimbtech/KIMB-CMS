<?php

defined('KIMB_CMS') or die('No clean Request');

//includes addons die first wollen

//evtl siteid, menueid, err verarbeiten

$addoninclude = new KIMBdbf('addon/includes.kimb');

$allfirst = $addoninclude->read_kimb_all_teilpl( 'ajax' );

foreach( $allfirst as $name ){

	require_once(__DIR__.'/'.$name.'/include_ajax.php');

}

?>
