<?php

defined('KIMB_CMS') or die('No clean Request');

//include addons die second wollen

//evtl siteid, menueid, err verarbeiten

if(!is_object($addoninclude)){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

$allsecond = $addoninclude->read_kimb_all_teilpl( 'second' );

foreach( $allsecond as $name ){

	if(strpos($name, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}

	require_once(__DIR__.'/'.$name.'/include_second.php');

}

?>
