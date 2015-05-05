<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
/*************************************************/
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License version 3
//published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.
/*************************************************/
//www.KIMB-technologies.eu
//www.bitbucket.org/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/



define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');
//BE Klassen
require_once(__DIR__.'/../core/oop/be_do/be_do_all.php');

//Menues erstellen und zuordnen

$bemenue = new BEmenue( $allgsysconf, $sitecontent );

$idfile = new KIMBdbf('menue/allids.kimb');
$menuenames = new KIMBdbf('menue/menue_names.kimb');


if( $_GET['todo'] == 'new' ){
	check_backend_login('five' , 'more');
	
	$bemenue->make_menue_new();
	
}
elseif( $_GET['todo'] == 'connect' ){
	check_backend_login( 'six' );

	$bemenue->make_menue_connect();

}
elseif( $_GET['todo'] == 'list' ){
	check_backend_login('seven' , 'more');

	$sitecontent->add_site_content('<h2>Alle Menues auflisten</h2>');

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
	var updown = function( fileid , updo , requid ){
		$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=menue.php&fileid=" + fileid + "&updo=" + updo + "&requid=" + requid , function( data ) {
			if( data == "ok" ){
				location.reload();
			}
			else{
				$( "#updown" ).show( "fast" );
				$( "#updown" ).dialog({
				resizable: false,
				height:270,
				modal: true,
				buttons: {
					"OK": function() {
						$( this ).dialog( "close" );
						return false;
					}
				}
				});
			}
		});
	}
	</script>');

	make_menue_array();
	$sitecontent->add_site_content('<table width="100%"><tr> <th title="Jedes Menü hat eine Tiefe, ein Niveau. ( ein ==> ist eine Tiefe tiefer ) ">Niveau</th> <th></th> <th title="Dieser Name wird Besuchern im Frontend angezeigt">MenueName</th> <th title="Pfad-Teil des Menues für URL-Rewriting">Pfad</th> <th title="ID für Aufruf /index.php?id=XXX">RequestID</th> <th>Status</th> <th title="ID der zugeordnenten Seite">SiteID</th> <th title="ID des Menüs ( Systemintern )">MenueID</th> <th>Löschen</th> <th>Neu</th> </tr>');
	foreach( $menuearray as $menuear ){

		$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
		if ( $menuear['status'] == 'off' ){
			$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		else{
			$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		$requid = $menuear['requid'].'<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$menuear['requid'].'" target="_blank"><span class="ui-icon ui-icon-newwin" style="display:inline-block;" title="Diese Seite aufrufen."></span></a>';
		$menuename = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'" title="Dieses Menue bearbeiten." >'.$menuear['menuname'].'</a>';

		if( $menuear['nextid'] == ''){	
			$del = '<span onclick="var delet = del( \''.$menuear['fileid'].'\' , '.$menuear['requid'].' , \''.$menuear['fileidbefore'].'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Menue löschen."></span></span>';
		}
		else{
			$del = '<span onclick="delimp();"><span class="ui-icon ui-icon-trash" style="display:inline-block;" title="Dieses Menue löschen."></span></span>';
		}
		$newmenue = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$menuear['fileid'].'&amp;niveau=same" ><span class="ui-icon ui-icon-plusthick" title="Auf diesem Niveau ein weiteres Menue erstellen."></span></a>';
		if( $menuear['nextid'] == ''){
			$newmenue .= '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$menuear['fileid'].'&amp;niveau=deeper&amp;requid='.$menuear['requid'].'"><span class="ui-icon ui-icon-arrow-1-se" title="Unter diesem Menue ein Untermenue erstellen."></span></a>';
		}

		$versch = '<span onclick="var updo = updown( \''.$menuear['fileid'].'\' , \'up\' , '.$menuear['requid'].' ); updo();"><span class="ui-icon ui-icon-arrowthick-1-n" title="Dieses Menue nach oben schieben."></span></span>';
		$versch .= '<span onclick="var updo = updown( \''.$menuear['fileid'].'\' , \'down\' , '.$menuear['requid'].' ); updo();"><span class="ui-icon ui-icon-arrowthick-1-s" title="Dieses Menue nach unten schieben."></span></span>';

		$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td> <td>'.$versch.'</td> <td>'.$menuename.'</td> <td>'.$menuear['path'].'</td> <td>'.$requid.'</td> <td>'.$menuear['status'].'</td> <td>'.$menuear['siteid'].'</td> <td>'.$menuear['menueid'].'</td> <td>'.$del.'</td> <td>'.$newmenue.'</td> </tr>');

		$liste = 'yes';
	}
	$sitecontent->add_site_content('</table>');

	if( $liste != 'yes' ){
		$sitecontent->echo_error( 'Es wurden keine Menues gefunden!' );
	}

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie das Menue wirklich löschen?</p></div></div>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-untermenue" title="Löschen nicht möglich!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Das Menue kann erst gelöscht werden, wenn es keine Untermenues mehr hat!</p></div></div>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="updown" title="Fehler beim Verschieben!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 100px 0;"></span>Achtung, Menues können nur innerhalb ihres Niveaus verschoben werden!<br /><br />Auch ein Verschieben auf einen höheren Platz als den Ersten oder einen tieferen als den Letzten ist nicht möglich!</p></div></div>');
}
elseif( $_GET['todo'] == 'edit' ){
	check_backend_login('seven' , 'more');

	$sitecontent->add_site_content('<h2>Ein Menue bearbeiten</h2>');

	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) ){
		if( $_GET['file'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
		}
		$id = $file->search_kimb_xxxid( $_GET['reqid'] , 'requestid');
		if( $id  != false ){
			
			if( $allgsysconf['lang'] == 'on' && $_GET['langid'] != 0 && is_numeric( $_GET['langid'] ) ){
				$menuenames = new KIMBdbf('menue/menue_names_lang_'.$_GET['langid'].'.kimb');
			}
			else{
				$_GET['langid'] = 0;
			}
			
			if( isset( $_POST['name'] ) && isset( $_POST['pfad'] ) ){
				$_POST['pfad'] = preg_replace("/[^0-9A-Za-z_-]/","", $_POST['pfad']);
				$ok = $file->search_kimb_xxxid( $_POST['pfad'] , 'path');
				if( $ok == false || $ok == $id ){
					if( $file->read_kimb_id( $id , 'path') != $_POST['pfad'] ){
						$file->write_kimb_id( $id , 'add' , 'path' , $_POST['pfad'] );
						$sitecontent->echo_message( 'Der Pfad wurde angepasst!' );
					}
				}
				if( !empty( $_POST['name'] ) && $menuenames->read_kimb_one( $_GET['reqid'] ) != $_POST['name'] ){
					$menuenames->write_kimb_replace( $_GET['reqid'] , $_POST['name'] );
					$sitecontent->echo_message( 'Der Name wurde angepasst!' );
				}
			}

			$sitecontent->add_html_header('<script>
			$(function() {
				$("i#pfadtext").text("(Menuepfad -- OK)");
				$("i#pfadtext").css( "background-color", "green" );
				$("i#pfadtext").css( "color", "white" );
				$("i#pfadtext").css( "padding", "5px" );
			});
			function checkpath(){
				var pathinput = $( "input#pfad" ).val();
				if( "'.$file->read_kimb_id( $id , 'path').'" != pathinput ){

					$( "input#check" ).val( "nok" );
					$("i#pfadtext").text("(Menuepfad -- Überprüfung läuft)");
					$("i#pfadtext").css( "background-color", "orange" );

					$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=menue.php&urlfile='.$_GET['file'].'&search=" + pathinput , function( data ) {
						$( "input#check" ).val( data );
						if( data == "nok" ){
							$("i#pfadtext").text("(Menuepfad -- Achtung dieser Pfad ist schon vergeben!!)");
							$("i#pfadtext").css( "background-color", "red" );
						}
						else{
							$( "input#check" ).val( "ok" );
							$("i#pfadtext").text("(Menuepfad -- OK)");
							$("i#pfadtext").css( "background-color", "green" );
						}
					});
				}
				else{
					$( "input#check" ).val( "ok" );
					$("i#pfadtext").text("(Menuepfad -- OK)");
					$("i#pfadtext").css( "background-color", "green" );
				}
			}
			function pfadreplace() {
				$( "input#check" ).val( "nok" );
				$( "i#pfadtext" ).html("(Menuepfad -- Überprüfung ausstehend) <button onclick=\'checkpath(); return false;\'>Prüfen</button>");
				$( "i#pfadtext" ).css( "background-color", "blue" );
				$( "input#pfad" ).val( $( "input#pfad" ).val().replace( /[^0-9A-Za-z_-]/g, "") );
			}
			</script>');

			$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&amp;file='.$_GET['file'].'&amp;reqid='.$_GET['reqid'].'&amp;langid='.$_GET['langid'].'" method="post" onsubmit="if( document.getElementById(\'check\').value == \'nok\' ){ return false; } ">');
			
			if( $allgsysconf['lang'] == 'on'){
				make_lang_dropdown( '"'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&file='.$_GET['file'].'&reqid='.$_GET['reqid'].'&langid=" + val', $_GET['langid'] );
			}
			$sitecontent->add_site_content('<input type="text" value="'.$menuenames->read_kimb_one( $_GET['reqid'] ).'" name="name" > <i title="Name des Menues der im Frontend angezeigt wird." >(Menuename)</i><br />');
			
			$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'path').'" name="pfad" id="pfad" onkeyup="pfadreplace();" onchange="checkpath();" > <i id="pfadtext" title="Ein Menuepfad besteht aus Buchstaben, Zahlen, &apos;_&apos; und &apos;-&apos;.">(Menuepfad)</i><br />');
			$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'status').'" name="status" readonly="readonly"> <i title="Veränderbar auf Seite Auflisten sowie Zuordnung." >(Status)</i><br />');
			$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'requestid').'" name="requid" readonly="readonly"> <i title="Automatisch bei Erstellung des Menue generiert.">(RequestID)</i><br />');
			$sitecontent->add_site_content('<input type="text" value="'.$idfile->read_kimb_id( $_GET['reqid'] , 'siteid' ).'" name="siteid" readonly="readonly"> <i title="Veränderbar auf Seite Zuordnen." >(SiteID)</i><br />');
			$sitecontent->add_site_content('<input type="hidden" value="ok" id="check">');
			$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
			$sitecontent->add_site_content('</form>');

		}
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
elseif( $_GET['todo'] == 'del' ){
	check_backend_login('seven' , 'more');

	$sitecontent->add_site_content('<h2>Ein Menue löschen</h2>');

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
					$filebef = new KIMBdbf( 'url/nextid_'.$_GET['fileidbefore'].'.kimb' );
				}
				$befid = $filebef->search_kimb_xxxid( $_GET['file'] , 'nextid');
				if( $befid  != false ){
					$filebef->write_kimb_id( $befid , 'add' , 'nextid' , '---empty---' );
				}				
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
	check_backend_login( 'four' );

	$sitecontent->add_site_content('<h2>Einen Menuestatus verändern</h2>');

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

			if( $_SESSION['permission'] == 'more' ){
				open_url('/kimb-cms-backend/menue.php?todo=list');
				die;
			}
			else{
				open_url('/kimb-cms-backend/menue.php?todo=connect');
				die;
			}

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
	check_backend_login('four' , 'more');

	$sitecontent->add_site_content('<h2>Startseite Menue</h2>');

	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new">Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Eine neues Menue erstellen.</i></span></a>');
	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect">Zuordnen</b><br /><span class="ui-icon ui-icon-arrowthick-2-e-w"></span><br /><i>Die Menues einer Seite zuordnen und de-, aktivieren.</i></span></a>');
	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list">Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle Menues zum Bearbeiten, De-, Aktivieren und Löschen auflisten.</i></span></a>');
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
