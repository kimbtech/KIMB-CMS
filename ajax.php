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

if( !isset( $_GET['file'] ) && !isset( $_GET['addon'] ) ){

	echo('Fehlerhafte Ajax-Anfrage!');
	die;

}

//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_ajax.php');

//Systemeigen:
//Systemeigen:
//Systemeigen:

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
		if( $_GET['fileid'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['fileid'].'.kimb' );
		}
		$id = $file->search_kimb_xxxid( $_GET['requid'] , 'requestid');
		if( $id  != false ){
			if( $id == 1 && $_GET['updo'] == 'up' ){
				echo 'nok';
				die;
			}
			if( $file->read_kimb_id( $id+1 , 'path' ) == '' && $_GET['updo'] == 'down' ){
				echo 'nok';
				die;
			}

			$wid = 1;
			while( 5 == 5 ){
				$wpath = $file->read_kimb_id( $wid , 'path' );
				$wnextid = $file->read_kimb_id( $wid , 'nextid' );
				$wrequid = $file->read_kimb_id( $wid , 'requestid' );
				$wstatus = $file->read_kimb_id( $wid , 'status');

				if( $wnextid == '' ){
					$wnextid = '---empty---';
				}
				
				if( $wpath == '' ){
					break;
				}

				$newmenuefile[$wid] = array( 'path' => $wpath, 'nextid' => $wnextid , 'requid' => $wrequid, 'status' => $wstatus );
				
				$wid++;
			}

			if( $_GET['updo'] == 'down' ){
				$newid = $id+1;
			}
			if( $_GET['updo'] == 'up' ){
				$newid = $id-1;
			}

			$i = 1;
			$file->delete_kimb_file();
			while( 5 == 5 ){
				if( $newid == $i ){

					$data = $newmenuefile[$id];

					$file->write_kimb_id( $i , 'add' , 'path' , $data['path'] );
					$file->write_kimb_id( $i , 'add' , 'nextid' , $data['nextid'] );
					$file->write_kimb_id( $i , 'add' , 'requestid' , $data['requid'] );
					$file->write_kimb_id( $i , 'add' , 'status' , $data['status'] );

					$i++;
				}

				$data = $newmenuefile[$i];

				if( $data['requid'] == $_GET['requid'] && $_GET['updo'] == 'down' ){
					$data = $newmenuefile[$i+1];
				}

				if( $data['requid'] == $_GET['requid'] && $_GET['updo'] == 'up' ){
					$data = $newmenuefile[$i-1];
				}

				if( !isset( $data['path'] ) ){
					break;
				}

				$file->write_kimb_id( $i , 'add' , 'path' , $data['path'] );
				$file->write_kimb_id( $i , 'add' , 'nextid' , $data['nextid'] );
				$file->write_kimb_id( $i , 'add' , 'requestid' , $data['requid'] );
				$file->write_kimb_id( $i , 'add' , 'status' , $data['status'] );

				$i++;				
			}
			echo 'ok';
		}
		else{
			echo 'nok';
		}
	}
}



?>
