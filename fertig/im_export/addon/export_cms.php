<?php

defined('KIMB_Backend') or die('No clean Request');

if( isset( $_POST['send'] ) ){

	$zip = new ZipArchive();
	if ( $zip->open( __DIR__.'/temp/export_'.date("dmY").'.zip' , ZIPARCHIVE::CREATE) !== TRUE ) {
		$sitecontent->echo_error(' Kann keine Export-Zip erstellen! ');
		$sitecontent->output_complete_site();
		die;
	}

	$teile = $_POST['teile'];
	$dbfroot = __DIR__.'/../../oop/kimb-data/';

	//allgemeines zum System
	$infojson['allg'] = array( 'date' => date( 'dmY' ), 'time' => time(), 'sysver' => $allgsysconf['build'], 'sysname' => $allgsysconf['sitename'] );

	foreach( listaddons() as $addon ){
		$infojson['addons'][] = $addon;
	}


	//Seiten
	if( in_array( 'sites', $teile ) ){

		zip_r( $zip, $dbfroot.'site/', '/sites/' );

		$infojson['done'][] = 'sites';
	}
	//menue
	if( in_array( 'menue', $teile ) ){

		zip_r( $zip, $dbfroot.'menue/', '/menue/menue/' );

		zip_r( $zip, $dbfroot.'url/', '/menue/url/' );

		$infojson['done'][] = 'menue';
	}
	//User
	if( in_array( 'users', $teile ) ){

		zip_r( $zip, $dbfroot.'/backend/users/', '/users/' );

		$infojson['done'][] = 'users';
	}
	//Add-on
	if( in_array( 'addon', $teile ) ){

		zip_r( $zip, $dbfroot.'addon/', '/addon/' );

		$zip->deleteName( '/addon/includes.kimb' );

		$infojson['done'][] = 'addon';
	}
	//Konfiguration
	if( in_array( 'confs', $teile ) ){

		$infojson['confs']['sitename'] = $allgsysconf['sitename'];
		$infojson['confs']['description'] = $allgsysconf['description'];

		$zip->addFile( $dbfroot.'sonder.kimb' , '/confs/sonder.kimb' );

		$infojson['done'][] = 'confs';
	}
	//Filemanager dateien
	if( in_array( 'filem', $teile ) ){

		$zip->addFile( $dbfroot.'backend/filemanager.kimb' , '/filem/filemanager.kimb' );

		zip_r( $zip, __DIR__.'/../../../load/userdata/', '/filem/open/' );

		zip_r( $zip, __DIR__.'/../../secured/', '/filem/close/' );

		$infojson['done'][] = 'filem';	
	}


	$zip->addFromString( '/info.jkimb' , json_encode( $infojson ) );

	$zip->close();

	header('Content-type: application/kimbex');
	header('Content-Disposition: attachment; filename= export_'.date("dmY").'.kimbex');
	echo file_get_contents( __DIR__.'/temp/export_'.date("dmY").'.zip' );
	unlink( __DIR__.'/temp/export_'.date("dmY").'.zip' );
	die;
}
else{

	$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post">');

	$sitecontent->add_site_content('<input type="checkbox" value="sites" name="teile[]" onclick=" $(\'input[value=menue]\').prop(\'checked\', false); ">');
	$sitecontent->add_site_content('Seiten <span style="display:inline-block;" title="Alle Seiteninhalte exportieren." class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="menue" name="teile[]" onclick=" $(\'input[value=sites]\').prop(\'checked\', true); " >');
	$sitecontent->add_site_content('Menüstruktur <span style="display:inline-block;" title="Die gesamte Menüstruktur und die Zuordnungen zu Seiten exportieren. ( Seiten müssen auch exportiert werden. )" class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="users" name="teile[]">');
	$sitecontent->add_site_content('User <span style="display:inline-block;" title="Alle Backend-User und Userlevel exportieren." class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="addon" name="teile[]">');
	$sitecontent->add_site_content('Add-on Konfiguration <span style="display:inline-block;" title="Die Konfiguration aller Add-ons exportieren. ( Achtung: Die Add-ons selber werden nicht exportiert, sie müssen auf dem Import-System vorhanden sein! )" class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="confs" name="teile[]">');
	$sitecontent->add_site_content('Systemkonfiguration <span style="display:inline-block;" title="Die Systemkonfiguration und die Fehlertexte exportieren. ( Es werden nur inhaltliche Werte exportiert, z.B. der Seitenname und die Description. ) " class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="checkbox" value="filem" name="teile[]">');
	$sitecontent->add_site_content('Filemanager <span style="display:inline-block;" title="Sichern Sie alle Dateien aus dem Filemanager. ( Dies kann große Exportdateien verursachen! )" class="ui-icon ui-icon-info"></span>');
	$sitecontent->add_site_content('<br />');

	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="hidden" value="send" name="send">');
	$sitecontent->add_site_content('<input type="submit" value="Exportieren">');
	$sitecontent->add_site_content('</form>');

}

?>
