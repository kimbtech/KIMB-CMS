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

	$breadarrfertig = 'nok';

	gen_menue( $allgrequestid );

	$breadcrumblinks = '<div id="breadcrumb" >';

	$niveau = 1;
	while( $breadcrumbarr['maxniv'] >= $niveau ){

		$breadcrumblinks .= ' &rarr; ';
		$breadcrumblinks .= '<a href="'.$breadcrumbarr[$niveau]['link'].'">'.$breadcrumbarr[$niveau]['name'].'</a>';

		$niveau++;
	}
	$breadcrumblinks .= '</div>';

	if( is_object($sitecache) ){
		$sitecache->cache_addon( $allgmenueid , $breadcrumblinks , 'breadcrumb');
	}

}
else{
	$breadcrumblinks = $sitecache->get_cached_addon( $allgmenueid , 'breadcrumb' );

	$breadcrumblinks = $breadcrumblinks[0];
}

$sitecontent->add_site_content( $breadcrumblinks );

?>
