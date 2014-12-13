<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login();

//Menues erstellen und zuordnen

$idfile = new KIMBdbf('menue/allids.kimb');
$menuenames = new KIMBdbf('menue/menue_names.kimb');
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if( $_GET['todo'] == 'new' ){
	check_backend_login('more');

	//!!!!!!!!!!!!!!!!!!!
	//!!!!!!!!!!!!!!!!!!!
	//!!!!!!!!!!!!!!!!!!!

}
elseif( $_GET['todo'] == 'connect' ){

	if( $_POST['post'] == 'post' ){
		$i = 0;
		while( 5 == 5 ){
			if( $_POST[$i.'-site'] != $idfile->read_kimb_id( $_POST[$i] , 'siteid' ) ){
				if( $idfile->write_kimb_id( $_POST[$i] , 'add' , 'siteid' , $_POST[$i.'-site'] ) ){
					$sitecontent->echo_message( 'Die Seite '.$_POST[$i.'-site'].' wurde einem Menue zugeordnet!<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$_POST[$i].'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Die Seite mit Menue aufrufen."></span></a>' );
				}
			}
			if( $_POST[$i] == '' ){
				break;
			}
			$i++;
		}
	}

	make_menue_array();
	$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect">');
	$sitecontent->add_site_content('<table width="100%"><tr> <th width="50px;"></th> <th>MenueName</th> <th>Status</th> <th>SiteID <span class="ui-icon ui-icon-info" title="Geben Sie einfach eine vorhadene SiteID in das Kästchen ein um die Seite zuzuordnen! Wenn ein Menue Widererwarten keine Seite haben soll geben Sie bitte &apos;none&apos; ein"></span></th> </tr>');
	$i = 0;
	foreach( $menuearray as $menuear ){

		$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );

		if ( $menuear['status'] == 'off' ){
			$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		else{
			$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		
		$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td>  <td>'.$menuear['menuname'].'</td> <td>'.$status.'</td> <td><input type="text" value="'.$menuear['siteid'].'" name="'.$i.'-site"><input type="hidden" value="'.$menuear['requid'].'" name="'.$i.'"></td> </tr>');
		$i++;
	}
	$sitecontent->add_site_content('</table>');
	$sitecontent->add_site_content('<input type="hidden" value="post" name="post"><input type="submit" value="Zuordnungen ändern"></form>');

}
elseif( $_GET['todo'] == 'list' ){
	check_backend_login('more');

	$sitecontent->add_html_header('<script>
	var del = function( fileid , requid , fileidbefore) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=del&file=" + fileid + "&reqid=" + requid + "&fileidbefore=" + fileidbefore;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	function delimp() {
		$( "#del-untermenue" ).show( "fast" );
		$( "#del-untermenue" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"OK": function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	</script>');

	make_menue_array();
	$sitecontent->add_site_content('<table width="100%"><tr> <th title="Jedes Menü hat eine Tiefe, ein Niveau. ( ein ==> ist eine Tiefe tiefer ) ">Niveau</th> <th title="Dieser Name wird Besuchern im Frontend angezeigt">MenueName</th> <th title="Pfad-Teil des Menues für URL-Rewriting">Pfad</th> <th title="ID für Aufruf /index.php?id=XXX">RequestID</th> <th>Status</th> <th title="ID der zugeordnenten Seite">SiteID</th> <th title="ID des Menüs ( Systemintern )">MenueID</th> <th>Löschen</th> </tr>');
	foreach( $menuearray as $menuear ){

		$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
		if ( $menuear['status'] == 'off' ){
			$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		else{
			$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		$requid = $menuear['requid'].'<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$menuear['requid'].'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Diese Seite aufrufen."></span></a>';

		if( $menuear['nextid'] == ''){	
			$del = '<span onclick="var delet = del( \''.$menuear['fileid'].'\' , '.$menuear['requid'].' , \''.$menuear['fileidbefore'].'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Menue löschen."></span></span>';
		}
		else{
			$del = '<span onclick="delimp();"><span class="ui-icon ui-icon-trash" title="Dieses Menue löschen."></span></span>';
		}

		$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td> <td>'.$menuear['menuname'].'</td> <td>'.$menuear['path'].'</td> <td>'.$requid.'</td> <td>'.$menuear['status'].'</td> <td>'.$menuear['siteid'].'</td> <td>'.$menuear['menueid'].'</td> <td>'.$del.'</td> </tr>');
	}
	$sitecontent->add_site_content('</table>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie das Menue wirklich löschen?</p></div></div>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-untermenue" title="Löschen nicht möglich!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Das Menue kann erst gelöscht werden, wenn es keine Untermenues mehr hat!</p></div></div>');
}
elseif( $_GET['todo'] == 'edit' ){
	check_backend_login('more');

	//!!!!!!!!!!!!!!!!!!!
	//!!!!!!!!!!!!!!!!!!!
	//!!!!!!!!!!!!!!!!!!!

}
elseif( $_GET['todo'] == 'del' ){
	check_backend_login('more');

	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) && ( $_GET['fileidbefore'] == 'first' || is_numeric( $_GET['fileidbefore'] ) || $_GET['fileidbefore'] == '' ) ){
		if( $_GET['file'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
		}
		$id = $file->search_kimb_xxxid( $_GET['reqid'] , 'requestid');
		$nextid = $file->read_kimb_id( $id , 'nextid');
		if( $id  != false && $nextid == ''){
			$wid = 1;
			while( 5 == 5 ){
				if( $wid != $id ){
					$wpath = $file->read_kimb_id( $wid , 'path' );
					$wnextid = $file->read_kimb_id( $wid , 'nextid' );
					$wrequid = $file->read_kimb_id( $wid , 'requestid' );
					$wstatus = $file->read_kimb_id( $wid , 'status');
				}
				if( $wpath == '' && $wid != $id ){
					break;
				}
				if( $wid != $id ){
					if( $wnextid == '' ){
						$wnextid = '---empty---';
					}
					$newmenuefile[] = array( 'path' => $wpath, 'nextid' => $wnextid , 'requid' => $wrequid, 'status' => $wstatus );
				}
				$wid++;
			}
			$inhalt = 'none';
			$i = 1;
			$file->delete_kimb_file();
			foreach( $newmenuefile as $newmenue ){
				print_r( $newmenue );
				$file->write_kimb_id( $i , 'add' , 'path' , $newmenue['path'] );
				$file->write_kimb_id( $i , 'add' , 'nextid' , $newmenue['nextid'] );
				$file->write_kimb_id( $i , 'add' , 'requestid' , $newmenue['requid'] );
				$file->write_kimb_id( $i , 'add' , 'status' , $newmenue['status'] );
				$inhalt = 'something';
				$i++;
			}
			if( $inhalt == 'none' && $_GET['file'] != 'first'){
				if( $_GET['fileidbefore'] == 'first' ){
					$filebef = new KIMBdbf( 'url/first.kimb' );
				}
				else{
					$filebef = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
				}
				$befid = $filebef->search_kimb_xxxid( $_GET['file'] , 'nextid');
				if( $befid  != false ){
					$filebef->write_kimb_id( $befid , 'add' , 'nextid' , '---empty---' );
				}				
			}
			else{
				echo 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
			}
			$menuenames->write_kimb_id( $_GET['reqid'] , 'del' );
			$idfile->write_kimb_id( $_GET['reqid'] , 'del' );

			open_url('/kimb-cms-backend/menue.php?todo=list');
			die;
		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
		}
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
elseif( $_GET['todo'] == 'deakch' ){
	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) ){
		if( $_GET['file'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
		}
		$id = $file->search_kimb_xxxid( $_GET['reqid'] , 'requestid');
		if( $id  != false ){
			$status = $file->read_kimb_id( $id , 'status' );
			if( $status == 'on' ){
				$file->write_kimb_id( $id , 'add' , 'status' , 'off' );
				$stat = 'off';
			}
			else{
				$file->write_kimb_id( $id , 'add' , 'status' , 'on' );
				$stat = 'on';
			}
			$sitecontent->echo_message( 'Das Menue (RequID "'.$_GET['reqid'].'") wurde auf den Status "'.$stat.'" gesetzt!' );
		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
		}
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
else{
	check_backend_login('more');

	//
	//Infokästen
	//

}


//
//Menue verschieben
//

$sitecontent->output_complete_site();
?>
