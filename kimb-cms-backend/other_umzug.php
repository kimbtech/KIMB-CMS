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

define("KIMB_CMS", "Clean Request");

//Diese Datei ist Teil des Backends, sie wird direkt aufgerufen.

//Konfiguration & Klassen & Funktionen laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Diese Datei stellt unter Other den Teil "Umzug" bereit

check_backend_login( 'twenty' , 'more');

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

$oldurlfile = new KIMBdbf( 'menue/oldurl.kimb' );

if( $_GET['todo'] == 'add' && !empty( $_POST['url'] ) && !empty( $_POST['id'] ) ){

	$newid = $oldurlfile->next_kimb_id();

	if( substr( $_POST['url'], 0, 1 ) != '/' ){
		$_POST['url'] = '/'.$_POST['url'];
	}

	$oldurlfile->write_kimb_id( $newid, 'add', 'id', $_POST['id'] );
	$oldurlfile->write_kimb_id( $newid, 'add', 'url', $_POST['url'] );

}
elseif( $_GET['todo'] == 'del' && is_numeric( $_GET['id'] ) && !empty( $_GET['id'] ) ){

	if( $oldurlfile->write_kimb_id( $_GET['id'], 'del' ) ){
		$sitecontent->echo_message( 'Die Weiterleitung wurde gelöscht!' );
	}
}
elseif( !empty( $_GET['todo'] ) ){
	$sitecontent->echo_error( 'Bitte füllen Sie alle Felder!' );
}

$sitecontent->add_html_header('<script>
	function del( id ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_umzug.php?todo=del&id=" + id;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	</script>');

$sitecontent->add_site_content('<h2>Umzug <span class="ui-icon ui-icon-info" title="Hier können Sie Links einer alten Homepage unter dieser Domain auf eine neue Seite des CMS umlenken!" style="display:inline-block;"></span></h2>');
$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Geben Sie die alte URL ( www.domain.de <</URL>> ) an und wählen Sie eine CMS Seite, dies ist nützlich wenn z.B. auf Google noch alte Links zu finden sind!" style="display:inline-block;"></span><br /><br />');

$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_umzug.php?todo=add" method="post">');
$sitecontent->add_site_content('<table width="100%"><tr> <th>URL</th> <th>RequestID</th> <th>Löschen</th> </tr>');

foreach( $oldurlfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){

	$pars = $oldurlfile->read_kimb_id( $id );

	$sitecontent->add_site_content('<tr> <td>'.$pars['url'].'</td> <td>'.$pars['id'].'</td> <td><span onclick="del( '.$id.' );"><span class="ui-icon ui-icon-trash" title="Diese Weiterleitung löschen." style="display:inline-block;" ></span></span></td> </tr>');

}

$sitecontent->add_site_content('<tr> <td><input type="text" name="url" placeholder="/Kontakt-1-/"></td> <td>'.id_dropdown( 'id', 'requid' ).'</td> <td><input type="submit" value="Hinzufügen"></td> </tr>');

$sitecontent->add_site_content('</table></form>');

$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie die Weiterleitung wirklich löschen?</p></div></div>');

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
