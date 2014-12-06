<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login();

//Seite erstellen, zuordnen

if( $_GET['todo'] == 'new' ){

	$sitecontent->add_site_content('<h2>Neue Seite</h2>');

}
elseif( $_GET['todo'] == 'list' ){

	$sitecontent->add_site_content('<h2>Liste aller Seiten</h2>');

	$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

	$sites = scan_kimb_dir('site/');

	$sitecontent->add_site_content('<table width="100%"><tr><th width="40px;" >ID</th><th>Name</th><th width="20px;">Status</th></tr>');

	foreach ( $sites as $site ){
		$sitef = new KIMBdbf('site/'.$site);
		$id = substr( $site , 5, -5); 
		$name = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$id.'">'.$sitef->read_kimb_one('title').'</a>';
		if ( strpos( $site , 'deak' ) !== false ){
			$status = '<span class="ui-icon ui-icon-close" title="Diese Seite ist zu Zeit deaktiviert, also nicht auffindbar."></span>';
		}
		else{
			$status = '<span class="ui-icon ui-icon-check" title="Diese Seite ist zu Zeit aktiviert, also sichtbar."></span>';
		}
		$sitecontent->add_site_content('<tr><td>'.$id.'</td><td>'.$name.'</td><td>'.$status.'</td></tr>');
	}
	$sitecontent->add_site_content('</table>');

}
elseif( $_GET['todo'] == 'edit' && is_numeric( $_GET['id'] ) ){

	$sitecontent->add_site_content('<h2>Seite bearbeiten</h2>');

	//
	//NicEdit
	//
	//speichern
	//

	if( !is_object( $sitef ) ){
		if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
			$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'.kimb' );
		}
	}
	
	$seite['title'] = $sitef->read_kimb_one( 'title' );
	$seite['header'] = $sitef->read_kimb_one( 'header' );
	$seite['keywords'] = $sitef->read_kimb_one( 'keywords' );
	$seite['description'] = $sitef->read_kimb_one( 'description' );
	$seite['inhalt'] = $sitef->read_kimb_one( 'inhalt' );
	$seite['footer'] = $sitef->read_kimb_one( 'footer' );
	$seite['time'] = $sitef->read_kimb_one( 'time' );
	$seite['time'] = date( "d.m.Y \u\m H:i" , $seite['time'] );

	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit" method="post"><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['title'].'" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['header'].'" name="header" style="width:74%;"> <i>HTML Header </i><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['keywords'].'" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
	$sitecontent->add_site_content('<textarea name="description" style="width:74%; height:50px;">'.$seite['description'].'</textarea> <i>Description</i> <br />');
	$sitecontent->add_site_content('<textarea name="inhalt" style="width:74%; height:600px;">'.$seite['inhalt'].'</textarea> <i>Seitentitel</i> <br />');
	$sitecontent->add_site_content('<textarea name="footer" style="width:74%; height:50px;">'.$seite['footer'].'</textarea> <i>Footer</i> <br />');
	$sitecontent->add_site_content('<input type="text" readonly="readonly" value="'.$seite['time'].'" name="time" style="width:74%;"> <i>Zuletzt geändert</i><br />');
	$sitecontent->add_site_content('<input type="submit" value="Ändern"> </form>');
	$sitecontent->add_site_content('<hr /><i>Tipps für den Header:</i><br />');
	$sitecontent->add_site_content('<input class="select" type="text" value="'.htmlentities('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>').'" style="width:74%;"> <b>jQuery</b><br />');
	$sitecontent->add_site_content('<input class="select" type="text" value="'.htmlentities('<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.css" > <script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.js"></script>').'" style="width:74%;"> <b>jQuery UI</b><br />');
	$sitecontent->add_site_content('<input class="select" type="text" value="'.htmlentities('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/hash.js"></script>').'" style="width:74%;"> <b>MD5, SHA1, SHA256</b><br />');

}



$sitecontent->output_complete_site();
?>
