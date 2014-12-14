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

	$sitecontent->add_html_header('<script>
	$(function() { 
		nicEditors.allTextAreas({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'});
	});
	</script>');

	if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){

		$i=1;
		while( 5 == 5 ){
			if( !check_for_kimb_file( '/site/site_'.$i.'.kimb') && !check_for_kimb_file( '/site/site_'.$i.'_deak.kimb') ){
				break;
			}
			$i++;
		}

		$sitef = new KIMBdbf( '/site/site_'.$i.'.kimb' );

		$sitef->write_kimb_new( 'title' , $_POST['title'] );
		$sitef->write_kimb_new( 'header' , $_POST['header'] );
		$sitef->write_kimb_new( 'keywords' , $_POST['keywords'] );
		$sitef->write_kimb_new( 'description' , $_POST['description'] );
		$sitef->write_kimb_new( 'inhalt' , $_POST['inhalt'] );
		$sitef->write_kimb_new( 'footer' , $_POST['footer'] );
		$sitef->write_kimb_new( 'time' , time() );
		$sitef->write_kimb_new( 'made_user' , $_SESSION['name'] );

		open_url('/kimb-cms-backend/sites.php?todo=edit&id='.$i);
		die;

	}
	
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new" method="post"><br />');
	$sitecontent->add_site_content('<input type="text" value="Titel" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
	$sitecontent->add_site_content('<input type="text" value="" name="header" style="width:74%;"> <i>HTML Header </i><br />');
	$sitecontent->add_site_content('<input type="text" value="" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
	$sitecontent->add_site_content('<input type="text" value="" name="description" style="width:74%;"> <i>Description</i> <br />');
	$sitecontent->add_site_content('<textarea name="inhalt" style="width:99%;"></textarea> <i>Inhalt &uarr;</i> <br />');
	$sitecontent->add_site_content('<textarea name="footer" style="width:99%;"></textarea> <i>Footer &uarr;</i> <br />');
	$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');	

}
elseif( $_GET['todo'] == 'list' ){

	$sitecontent->add_site_content('<h2>Liste aller Seiten</h2>');

	$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

	$sitecontent->add_html_header('<script>
	var del = function( id ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=del&id="+id;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	function search(){
		var search = $( "input.search" ).val();
		window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list#" + search;
	}
	</script>');

	$sites = scan_kimb_dir('site/');

	$sitecontent->add_site_content('<span><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new"><span class="ui-icon ui-icon-plus" title="Eine neue Seite erstellen."></span></a>');
	$sitecontent->add_site_content('<input type="text" class="search"><button onclick="search();" title="Nach Seitenamen suchen ( genauer Seitenname nötig ).">Suchen</button></span><hr />');
	$sitecontent->add_site_content('<table width="100%"><tr><th width="40px;" >ID</th><th>Name</th><th width="20px;">Status</th><th width="20px;">Löschen</th></tr>');

	$idfile = new KIMBdbf('menue/allids.kimb');
	
	foreach ( $sites as $site ){
		$sitef = new KIMBdbf('site/'.$site);
		$id = preg_replace("/[^0-9]/","", $site);
		$title = $sitef->read_kimb_one('title');
		$name = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$id.'" title="Seite bearbeiten.">'.$title.'</a>';
		if ( strpos( $site , 'deak' ) !== false ){
			$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-close" title="Diese Seite ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern )"></span></a>';
		}
		else{
			$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-check" title="Diese Seite ist zu Zeit aktiviert, also sichtbar. ( click -> ändern )"></span></a>';
		}
		$del = '<span onclick="var delet = del( '.$id.' ); delet();"><span class="ui-icon ui-icon-trash" title="Diese Seite löschen."></span></span>';
		$zugeor = $idfile->search_kimb_xxxid( $id , 'siteid' );
		if( $zugeor == false ){
			$status .= '<span class="ui-icon ui-icon-alert" title="Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!"></span>';
		}
		$sitecontent->add_site_content('<tr><td>'.$id.'</td><td id="'.$title.'">'.$name.'</td><td>'.$status.'</td><td>'.$del.'</td></tr>');
	}
	$sitecontent->add_site_content('</table>');

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Seite wirklich löschen?</p></div></div>');

}
elseif( $_GET['todo'] == 'edit' && is_numeric( $_GET['id'] ) ){

	$sitecontent->add_site_content('<h2>Seite bearbeiten</h2>');

	$sitecontent->add_html_header('<script>
	var del = function( id ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=del&id="+id;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	$(function() { 
		nicEditors.allTextAreas({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'});
	});
	</script>');

	if( !is_object( $sitef ) ){
		if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
			$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'.kimb' );
		}
		else{
			$sitecontent->echo_error('Die Seite wurde nicht gefunden' , '404');
			$sitecontent->output_complete_site();
			die;
		}
	}

	if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){

		$sitef->write_kimb_replace( 'title' , $_POST['title'] );
		$sitef->write_kimb_replace( 'header' , $_POST['header'] );
		$sitef->write_kimb_replace( 'keywords' , $_POST['keywords'] );
		$sitef->write_kimb_replace( 'description' , $_POST['description'] );
		$sitef->write_kimb_replace( 'inhalt' , $_POST['inhalt'] );
		$sitef->write_kimb_replace( 'footer' , $_POST['footer'] );
		$sitef->write_kimb_replace( 'time' , time() );
		$sitef->write_kimb_replace( 'made_user' , $_SESSION['name'] );

	}
	
	$seite['title'] = $sitef->read_kimb_one( 'title' );
	$seite['header'] = $sitef->read_kimb_one( 'header' );
	$seite['keywords'] = $sitef->read_kimb_one( 'keywords' );
	$seite['description'] = $sitef->read_kimb_one( 'description' );
	$seite['inhalt'] = $sitef->read_kimb_one( 'inhalt' );
	$seite['footer'] = $sitef->read_kimb_one( 'footer' );
	$seite['time'] = $sitef->read_kimb_one( 'time' );
	$seite['time'] = date( "d.m.Y \u\m H:i" , $seite['time'] );

	$idfile = new KIMBdbf('menue/allids.kimb');
	$id = $idfile->search_kimb_xxxid( $_GET['id'] , 'siteid' );

	if( $id == false ){
		$sitecontent->echo_message( 'Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!' );
	}

	$sitecontent->add_site_content('<span onclick="var delet = del( '.$_GET['id'].' ); delet();"><span class="ui-icon ui-icon-trash" title="Diese Seite löschen."></span></span> <a href="'.$allgsysconf['siteurl'].'/index.php?id='.$id.'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Diese Seite anschauen."></span></a>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Seite wirklich löschen?</p></div></div>');
	
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$_GET['id'].'" method="post"><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['title'].'" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['header'].'" name="header" style="width:74%;"> <i>HTML Header </i><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['keywords'].'" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['description'].'" name="description" style="width:74%;"> <i>Description</i> <br />');
	$sitecontent->add_site_content('<textarea name="inhalt" style="width:99%;">'.$seite['inhalt'].'</textarea> <i>Inhalt &uarr;</i> <br />');
	$sitecontent->add_site_content('<textarea name="footer" style="width:99%;">'.$seite['footer'].'</textarea> <i>Footer &uarr;</i> <br />');
	$sitecontent->add_site_content('<input type="text" readonly="readonly" value="'.$seite['time'].'" name="time" style="width:74%;"> <i>Zuletzt geändert</i><br />');
	$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');
	$sitecontent->add_site_content('<hr /><i>Tipps für den Header:</i><br />');
	$sitecontent->add_site_content('<input class="select" type="text" value="'.htmlentities('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>').'" style="width:74%;"> <b>jQuery</b><br />');
	$sitecontent->add_site_content('<input class="select" type="text" value="'.htmlentities('<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.css" > <script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.js"></script>').'" style="width:74%;"> <b>jQuery UI</b><br />');
	$sitecontent->add_site_content('<input class="select" type="text" value="'.htmlentities('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/hash.js"></script>').'" style="width:74%;"> <b>MD5, SHA1, SHA256</b><br />');

}
elseif( $_GET['todo'] == 'del' && is_numeric( $_GET['id'] ) ){

	if( !is_object( $sitef ) ){
		if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
			$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'.kimb' );
		}
		else{
			$sitecontent->echo_error('Die Seite wurde nicht gefunden' , '404');
			$sitecontent->output_complete_site();
			die;
		}
	}

	$sitef->delete_kimb_file();

	open_url('/kimb-cms-backend/sites.php?todo=list');
	die;

}
elseif( $_GET['todo'] == 'deakch' && is_numeric( $_GET['id'] ) ){

	if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
		rename_kimbdbf( '/site/site_'.$_GET['id'].'.kimb' , '/site/site_'.$_GET['id'].'_deak.kimb' );
	}
	elseif( !check_for_kimb_file('/site/site_'.$_GET['id'].'.kimb') && check_for_kimb_file( '/site/site_'.$_GET['id'].'_deak.kimb' )  ){
		rename_kimbdbf( '/site/site_'.$_GET['id'].'_deak.kimb' , '/site/site_'.$_GET['id'].'.kimb' );
	}
	open_url('/kimb-cms-backend/sites.php?todo=list');
	die;
}
else{
	$sitecontent->add_site_content('<h2>Seiten</h2>');

	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new">Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Eine neue Seite erstellen.</i></span></a>');
	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list">Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle Seiten zum Bearbeiten, De-, Aktivieren und Löschen auflisten.</i></span></a>');

	$sitecontent->add_site_content('<hr /><u>Schnellzugriffe:</u><br /><br />');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" method="get"><input type="text" name="id" placeholder="ID"><input type="hidden" value="edit" name="todo"><input type="submit" value="Los"> <span title="Geben Sie die SeitenID ein und bearbeiten Sie sofort die Inhalte!">(Seite bearbeiten)</span></form>');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" method="get"><input type="text" name="id" placeholder="ID"><input type="hidden" value="del" name="todo"><input type="submit" value="Los"> <span title="Geben Sie die SeitenID ein und löschen Sie sofort die Seite!">(Seite löschen)</span></form>');
}


$sitecontent->output_complete_site();
?>
