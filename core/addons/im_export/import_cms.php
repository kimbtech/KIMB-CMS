<?php

defined('KIMB_Backend') or die('No clean Request');

$sitecontent->echo_message( 'Erstellen Sie vor jedem Import ein Backup um einem Datenverlust vorzubeugen!!' );
$sitecontent->add_site_content( '<br /><hr /><br />' );

if( isset( $_POST['file'] ) && !empty( $_FILES['exportfile']['name'] ) ){

	$_SESSION['importfile'] = mt_rand();

	$fileroot = __DIR__.'/temp/'.$_SESSION['importfile'].'/';

	$zip = new ZipArchive;
	if ( $zip->open($_FILES["exportfile"]["tmp_name"]) === TRUE ) {
		$zip->extractTo( $fileroot );
		$zip->close();
	}
	else{
		$sitecontent->echo_error(' Konnte Exportdatei nicht verarbeiten! ');
		$sitecontent->output_complete_site();
		die;
	}
	
	$jsoninfo = json_decode( file_get_contents( $fileroot.'/info.jkimb' ) , true );

	$sitecontent->add_site_content( '<b>Über diese Exportdatei:</b><ul>' );
	$sitecontent->add_site_content( '<li>Exportsystem: '.$jsoninfo['allg']['sysname'].'</li>' );
	$sitecontent->add_site_content( '<li>Exportzeit: '.date( 'd.m.Y H:i' , $jsoninfo['allg']['time'] ).'</li>' );
	$sitecontent->add_site_content( '<li>Inhalte:<ul>' );
		foreach( $jsoninfo['done'] as $done ){
			$sitecontent->add_site_content( '<li>'.$done.'</li>' );
		}
		$sitecontent->add_site_content( '</ul></li>' );
	$sitecontent->add_site_content( '</ul>' );

	if( $jsoninfo['allg']['sysver'] != $allgsysconf['build'] ){
		$sitecontent->echo_message( 'Die Versionen dieses Systems und des Exportsystems stimmen nicht überein, dies kann zu Problemem führen, wenn Sie fortfahren.' );
	}

	$sitecontent->add_site_content('<br />');
	$sysaddons = listaddons();
	foreach( $jsoninfo['addons'] as $addon ){
		if( !in_array( $addon, $sysaddons ) ){
			$sitecontent->echo_message( 'Das Add-on "'.$addon.'" war auf dem Exportsystem vorhanden und ist auf diesem System nicht installiert.<br />Dies kann zu Problemen führen!' );
		}
	}
	$sitecontent->add_site_content('<br />');
	
	$sitecontent->add_site_content( '<form action="'.$addonurl.'&amp;weiter='.$_SESSION['importfile'].'" method="post">' );

	$sitecontent->add_site_content( '<h4>Wählen Sie die zu importierenden Kategorien:</h4>' );
	
	$doneread = array( "sites" => 'Seiten', "menue" => 'Menüstruktur', "users" => 'Backend-User', "addon" => 'Add-on Konfiguration', "confs" => 'Systemkonfiguration' , "filem" => 'Dateien des Filemanager' );

	foreach( $jsoninfo['done'] as $done ){
		$sitecontent->add_site_content('<input type="checkbox" value="'.$done.'" name="todo[]" checked="checked">'.$doneread[$done].'<br />');
	}

	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="submit" value="Import starten">');
	$sitecontent->add_site_content('</form>');
}
elseif( $_GET['weiter'] == $_SESSION['importfile'] ){

	echo 'weiter';	

}
else{
	$sitecontent->echo_message( 'Mit dem Import <b><u>ersetzen</u></b> Sie alle Inhalte Ihres CMS!!' );
	$sitecontent->add_site_content( '<br /><br />' );

	$sitecontent->add_site_content('<form action="'.$addonurl.'" enctype="multipart/form-data" method="post">');

	$sitecontent->add_site_content('<input name="exportfile" type="file" />');
	$sitecontent->add_site_content('Exportdatei <span style="display:inline-block;" title="Bitte wählen Sie eine KIMB-CMS Export-Datei ( *.kimbex )" class="ui-icon ui-icon-info"></span>');
	
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="hidden" value="file" name="file">');
	$sitecontent->add_site_content('<input type="submit" value="Import starten">');
	$sitecontent->add_site_content('</form>');

	if( isset( $_SESSION['importfile'] ) ){
		rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
	}

}

?>
