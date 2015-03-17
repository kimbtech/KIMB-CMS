<?php

defined('KIMB_Backend') or die('No clean Request');

$newver = json_decode( file_get_contents( 'http://api.kimb-technologies.eu/cms/getcurrentversion.php' ) , true );

if( compare_cms_vers( $allgsysconf['build'] , $newver['currvers'] ) == 'older' ){
	$update = 'yes';
	$updatearr['do'] = 'yes';
	$updatearr['sysv'] = $allgsysconf['build'];
	$updatearr['newv'] = $newver['currvers'];
}
else{
	$update = 'no';
	$updatearr['do'] = 'no';
	$updatearr['sysv'] = $allgsysconf['build'];
	$updatearr['newv'] = $newver['currvers'];
}

?>
