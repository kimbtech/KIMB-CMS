<?php

defined('KIMB_CMS') or die('No clean Request');

if(!is_object( $idfile )){
	$idfile = new KIMBdbf('menue/allids.kimb');
}

if($allgmenueid == $idfile->read_kimb_id($_GET['id'], 'menueid')){
	$allgrequestid = $_GET['id'];
}
else{
	$allgrequestid = $idfile->search_kimb_xxxid( $allgmenueid , 'menueid' );
}

if( $allgsysconf['cache'] == 'on' ){
	if( $sitecache->load_cached_menue($allgmenueid) ){
		$menuecache = 'loaded';
	}
}

$menuenames = new KIMBdbf('menue/menue_names.kimb');

if( $menuecache != 'loaded'){

	gen_menue( $allgrequestid );

}

?>
