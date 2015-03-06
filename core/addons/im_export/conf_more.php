<?php

defined('KIMB_Backend') or die('No clean Request');

$sitecontent->add_site_content('<hr /><br /><h2>Im- / Export</h2>');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=im_export';

if( $_GET['task'] == 'export' ){

	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');

	$sitecontent->add_site_content('<h3>Export</h3>');

	$addonurl .= '&task='.$_GET['task'];

	require_once( __DIR__.'/export_cms.php' );

}
elseif( $_GET['task'] == 'import' ){

	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');

	$sitecontent->add_site_content('<h3>Import</h3>');

	$addonurl .= '&task='.$_GET['task'];

	require_once( __DIR__.'/import_cms.php' );

}
elseif( $_GET['task'] == 'add' ){

	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Auswahl</a>');

	$sitecontent->add_site_content('<h3>Add</h3>');

	$addonurl .= '&task='.$_GET['task'];

	require_once( __DIR__.'/add_sites.php' );

}
else{
	$sitecontent->add_site_content('<h3>Auswahl</h3>');
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&task=export"><button title="Exportieren Sie die Inhalt dieses CMS in eine Datei." >Export</button></a>');
	$sitecontent->add_site_content('<br /><br />');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&task=import"><button title="Importieren Sie eine der Exportdateien in dieses CMS, dies Überschreibt alle aktuellen Inhalte" >Import</button></a>');
	$sitecontent->add_site_content('<br /><br />');
	$sitecontent->add_site_content('<a href="'.$addonurl.'&task=add"><button title="Fügen Sie Inhalte wie Seiten aus einer Exportdatei diesem CMS hinzu." >Add</button></a>');
	$sitecontent->add_site_content('<br />');
}

?>
