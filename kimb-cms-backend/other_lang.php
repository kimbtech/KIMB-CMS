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

//Diese Datei stellt unter Other den Teil "Mehrsprachige Seite" bereit

check_backend_login( 'twentyone' , 'more');

$sitecontent->add_site_content('<h2>Mehrsprachige Seite <span class="ui-icon ui-icon-info" title="Hier können Sie die Seite dieses CMS um weitere Sprachen erweitern!" style="display:inline-block;"></span></h2>');

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; } label{ width:200px; }</style>');

if( isset( $_GET['do'] ) && $allgsysconf['lang'] == 'on' ){
	
	$langfile = new KIMBdbf( 'site/langfile.kimb' );
	
	if( !empty( $_POST['stdtag'] ) && !empty( $_POST['stdlangname'] ) ){
		
		$vals = $langfile->read_kimb_id( '0' );
		
		$ch = false;
		
		if( $vals['tag'] != $_POST['stdtag'] ){
			$langfile->write_kimb_id( '0', 'add', 'tag', $_POST['stdtag'] );
			$ch = true;
		}
		if( $vals['status'] != 'on' ){
			$langfile->write_kimb_id( '0', 'add', 'status', 'on' );
			$ch = true;
		}
		if( $vals['name'] != $_POST['stdlangname'] ){
			$langfile->write_kimb_id( '0', 'add', 'name', $_POST['stdlangname'] );
			$ch = true;		
		}
		if( $vals['flag'] != $allgsysconf['siteurl'].'/load/system/flags/'.$_POST['stdtag'].'.gif' ){
			if( is_file( __DIR__.'/../load/system/flags/'.$_POST['stdtag'].'.gif' ) ){
				$langfile->write_kimb_id( '0', 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/'.$_POST['stdtag'].'.gif' );
			}
			else{
				$langfile->write_kimb_id( '0', 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/new.gif' );			
			}
			$ch = true;
		}
		
		if( $ch ){
			$sitecontent->echo_message( 'Die Änderungen der Standardsprache wurden gespeichert!' );
		}
	}
	if( !empty( $_POST['newtag'] ) && !empty( $_POST['newlangname'] ) ){
		
		if( $langfile->search_kimb_xxxid( $_POST['newtag'] , 'tag' ) == false && strlen( $_POST['newtag'] ) == 2 && $langfile->read_kimb_id( '0', 'tag' ) != $_POST['newtag'] ){
		
			$id = $langfile->next_kimb_id();
			
			$langfile->write_kimb_id( $id, 'add', 'tag', $_POST['newtag'] );
			$langfile->write_kimb_id( $id, 'add', 'status', 'on' );
			$langfile->write_kimb_id( $id, 'add', 'name', $_POST['newlangname'] );
			if( is_file( __DIR__.'/../load/system/flags/'.$_POST['newtag'].'.gif' ) ){
				$langfile->write_kimb_id( $id, 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/'.$_POST['newtag'].'.gif' );
			}
			else{
				$langfile->write_kimb_id( $id, 'add', 'flag', '<!--SYS-SITEURL-->/load/system/flags/new.gif' );			
			}
			
			$sitecontent->echo_message( 'Eine neue Sprache wurde hinzugefügt!' );
			
		}
		else{
			$sitecontent->echo_error( 'Der Tag ist schon vergeben oder hat keine 2 Zeichen!' );
		}
	}
	if( isset( $_GET['chdeak'] ) && is_numeric( $_GET['id'] ) && $_GET['id'] != 0 ){
		
		$stat = $langfile->read_kimb_id( $_GET['id'], 'status' );
		
		if( $stat == 'on' ){
			$langfile->write_kimb_id( $_GET['id'], 'add', 'status', 'off' );
		}
		else{
			$langfile->write_kimb_id( $_GET['id'], 'add', 'status', 'on' );
		}
		
		$sitecontent->echo_message( 'Der Status einer Sprache wurde geändert!' );
	}

	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/langs.min.js"></script>');
	$sitecontent->add_html_header('<script>$(function() { $( "#autoc" ).autocomplete({ source: langtag }); $( "#autocstd" ).autocomplete({ source: langtag }); });
	function setnameflag( id , tagv, langn ){ var tagna = $( "input." + tagv ).val(); $( "#" + langn ).val( langobj[tagna] ); $( "img#" + id ).attr( "src" , "'.$allgsysconf['siteurl'].'/load/system/flags/" + tagna + ".gif" ); }
	function make_uneditable( id ){ $("input#" + id ).attr( "readonly" , true); } function make_editable( id ){ $("input#" + id ).removeAttr( "readonly" , true); } </script>');

	$sitecontent->add_site_content('<h3>Sprachen dieser Seite</h3>');

	$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?do">');

	$vals = $langfile->read_kimb_id( '0' );

	$sitecontent->add_site_content('<table width="100%"><tr> <th>ID</th> <th>Tag <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Der Tag dient für die URL!"></span></th> <th>Name</th> <th>Flagge</th> <th>Status <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Eine Sprache kann nicht gelöscht, nur deaktiviert, werden!"></span></th> </tr>');
	$sitecontent->add_site_content('<tr> 
		<td>0 <span class="ui-icon ui-icon-info" title="Standard ( Sprache des ersten Contents )" style="display:inline-block;"></span></td> 
		<td><input style="width:95%;" type="text" name="stdtag" id="autocstd" class="stdtag" placeholder="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" title="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" onblur="setnameflag( \'stdflag\', \'stdtag\', \'stdlangnam\' );" value="'.$vals['tag'].'"></td> 
		<td><input style="width:95%;" type="text" readonly="readonly" onfocus="make_editable( \'stdlangnam\' );" onblur="make_uneditable( \'stdlangnam\' );" name="stdlangname" title="Klicken zum Ändern!" id="stdlangnam" value="'.$vals['name'].'"></td>
		');
		if( !empty( $vals['flag'] ) ){
			$sitecontent->add_site_content('<td><img src="'.$vals['flag'].'" id="stdflag" title="Flag" alt="Flagge" /></td>');
		}
		else{
			$sitecontent->add_site_content('<td><img src="'.$allgsysconf['siteurl'].'/load/system/flags/new.gif" id="stdflag" title="Flag" alt="Flagge" /></td>');	
		}
		$sitecontent->add_site_content(' 
		<td><input type="submit" value="Ändern"></td>
	</tr><tr><td colspan="5"></td></tr>');
	
	foreach( $langfile->read_kimb_all_teilpl( 'allidslist' ) as $id ){
		if( $id != 0){
			$vals = $langfile->read_kimb_id( $id );
			if( $vals['status'] == 'on' ){
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?do&amp;chdeak&amp;id='.$id.'"><span class="ui-icon ui-icon-check" style="display:inline-block;" title="Diese Sprache ist zu Zeit aktiviert. ( click -> ändern )" ></sapn></a>';	
			}
			else{
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?do&amp;chdeak&amp;id='.$id.'"><span class="ui-icon ui-icon-close" style="display:inline-block;" title="Diese Sprache ist zu Zeit deaktiviert. ( click -> ändern )" ></sapn></a>';
			}
			$sitecontent->add_site_content('<tr> <td>'.$id.'</td> <td>'.$vals['tag'].'</td> <td>'.$vals['name'].'</td> <td><img src="'.$vals['flag'].'" title="Flag" alt="Flagge" /></td> <td>'.$status.'</td> </tr>');
		}
	}


	$sitecontent->add_site_content('<tr> 
		<td>X</td> 
		<td><input style="width:95%;" type="text" name="newtag" id="autoc" class="newtag" placeholder="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" title="Geben Sie einen Tag für eine Sprache an ( nach ISO 639; z.B.: de, en )" onblur="setnameflag( \'flag\', \'newtag\', \'langnam\' );"></td> 
		<td><input style="width:95%;" type="text" readonly="readonly" onfocus="make_editable( \'langnam\' );" onblur="make_uneditable( \'langnam\' );" name="newlangname" title="Klicken zum Ändern!" id="langnam"></td> 
		<td><img src="'.$allgsysconf['siteurl'].'/load/system/flags/new.gif" id="flag" title="Flag" alt="Flagge" /></td> 
		<td><input type="submit" value="Hinzufügen"></td> 
	</tr>');
	
	$sitecontent->add_site_content('</form>');


}
else{
	$sitecontent->add_html_header('<script>$(function() { $( "#onoff" ).buttonset(); $( "#on" ).click(function() { window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?chdeak=on"; }); $( "#off" ).click(function() { window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?chdeak=off"; }); }); </script>' );

	if( isset( $_GET['chdeak'] ) ){
		if( $_GET['chdeak'] == 'on' ){
			$conffile->write_kimb_id( '001' , 'add' , 'lang' , 'on' );
			$allgsysconf['lang'] = 'on';
		}
		elseif( $_GET['chdeak'] == 'off' ){
			$conffile->write_kimb_id( '001' , 'add' , 'lang' , 'off' );
			$allgsysconf['lang'] = 'off';

		}
	}

	if( $allgsysconf['lang'] == 'on' ){
		$checked['on'] = 'checked="checked"';
		$checked['off'] = '';
	}
	else{
		$checked['off'] = 'checked="checked"';
		$checked['on'] = '';
	}

	$sitecontent->add_site_content('<br /><br /><center><form> <div id="onoff"> <input type="radio" id="on" name="onoff" '.$checked['on'].'> <label for="on">Aktiviert</label> <input type="radio" id="off" name="onoff" '.$checked['off'].'> <label for="off">Deaktiviert</label> </div> </form></center><br /><br />');

	if( $allgsysconf['lang'] == 'on' ){

	$sitecontent->add_html_header('<script> $(function() { $( "a#dospreinst" ).button(); }); </script>');
	$sitecontent->echo_message( 'Mehrsprachige Seiten aktiviert!' );

		$sitecontent->add_site_content('<br /><br /><center><a id="dospreinst" href="?do">Zu den Einstellungen &rarr;</a></center>');
	}
	else{
		$sitecontent->echo_message( '<center>Mehrsprachige Seiten deaktiviert!</center>' );
	}
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
