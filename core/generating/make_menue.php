<?php

if(!is_object( $idfile )){
	$idfile = new KIMBdbf('menue/allids.kimb');
}

if($allgmenueid == $idfile->read_kimb_id($_GET['id'], 'menueid')){
	$allgrequestid = $_GET['id'];
}
else{
	$allgrequestid = search_kimb_xxxid( $allgmenueid , 'menueid' );
}

if( $allgsysconf['urlrewrite'] == 'on' ){

	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	$file = new KIMBdbf('url/first.kimb');
	$ii = '1';
	while( 5 == 5){
		$path = $file->read_kimb_id($ii, 'path');
		$requid = $file->read_kimb_id($ii, 'requestid');
		if( $allgrequestid == $requid ){
			$clicked = 'yes';
		}
		else{
			$clicked = 'no';
		}
		$menuname = $menuenames->read_kimb_one( $requid );

		if( $path == '' ){
			break;
		}
		$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?url=/'.$path.'/' , '1', $clicked);
		$ii++;
	}	

	$i = '0';
	if($urlteile[$i] == ''){
		$i++;
	}
	$ok = $file->search_kimb_xxxid( $urlteile[$i] , 'path' );
	if( $ok != false){
		$nextid = $file->read_kimb_id( $ok , 'nextid' );
		if( $allgerr == '' && !$file->search_kimb_xxxid( $allgrequestid , 'requestid' ) ){
			$return = godeeper_menue($allgrequestid, $nextid, $menuenames, $urlteile, $i);
			$file = $return['file'];
			$niveau = $return['niveau'];
		}
		else{
			$niveau = 2;
			$file = new KIMBdbf('url/nextid_'.$nextid.'.kimb');

		}
		
		$ii = '1';
		while( 5 == 5){
			$path = $file->read_kimb_id($ii, 'path');
			$requid = $file->read_kimb_id($ii, 'requestid');
			if( $allgrequestid == $requid ){
				$clicked = 'yes';
			}
			else{
				$clicked = 'no';
			}
			$menuname = $menuenames->read_kimb_one( $requid );

			if( $path == '' ){
				break;
			}
			$sitecontent->add_menue_one_entry( $menuname.$niveau , $allgsysconf['siteurl'].'/index.php?url=/'.$urlteile[$i].'/'.$path.'/' , $niveau, $clicked);
			$ii++;
		}
	}

}
else{
	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	$file = new KIMBdbf('url/first.kimb');
	$ii = '1';
	while( 5 == 5){
		$path = $file->read_kimb_id($ii, 'path');
		$requid = $file->read_kimb_id($ii, 'requestid');
		if( $allgrequestid == $requid ){
			$clicked = 'yes';
		}
		else{
			$clicked = 'no';
		}
		$menuname = $menuenames->read_kimb_one( $requid );

		if( $path == '' ){
			break;
		}
		$sitecontent->add_menue_one_entry( $menuname , $allgsysconf['siteurl'].'/index.php?id='.$requid , '1', $clicked);
		$ii++;
	}
	
	
	//alle weiteren nextid durchsuchen, nach requid (max 3 tief), wenn gefunden Menue machen
}
//nach cache suchen und erstellen
?>
