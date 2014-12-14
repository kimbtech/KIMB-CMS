<?php
define("KIMB_CMS", "Clean Request");

defined('KIMB_CMS') or die('No clean Request');

//Klassen und Funktionen
require_once(__DIR__.'/core/oop/kimbdbf.php');

//Konfiguration

$conffile = new KIMBdbf('config.kimb');

$allgsysconf = $conffile->read_kimb_id('001');

//session, ...

session_start();
error_reporting('0');
header('X-Robots-Tag: '.$allgsysconf['robots']);

//Funktionen laden
require_once(__DIR__.'/core/conf/funktionen.php');

//System initialisiert!

//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_ajax.php');

//Systemegeigen:
//Systemegeigen:
//Systemegeigen:

if( $_GET['file'] == 'menue.php' ){

	check_backend_login('more');

	if( $_GET['urlfile'] == 'first' || is_numeric( $_GET['urlfile'] ) ){
		if( $_GET['urlfile'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['urlfile'].'.kimb' );
		}

		$_GET['search'] = preg_replace("/[^0-9A-Za-z_-]/","", $_GET['search']);

		if( !$file->search_kimb_xxxid( $_GET['search'] , 'path') ){
			echo 'ok';
		}
		else{
			echo 'nok';
		}
	}
	elseif( ($_GET['fileid'] == 'first' || is_numeric( $_GET['fileid'] ) ) && ( $_GET['updo'] == 'up' || $_GET['updo'] == 'down' ) && is_numeric( $_GET['requid'] ) ){
		echo 'nok';
	}
}



?>
