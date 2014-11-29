<?php

//include addons die second wollen

//evtl siteid, menueid, err verarbeiten

if(!is_object($addoninclude)){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

$allsecond = $addoninclude->read_kimb_all_teilpl( 'second' );

foreach( $allsecond as $name ){

	require_once(__DIR__.'/'.$name.'/include_second.php');

}

?>
