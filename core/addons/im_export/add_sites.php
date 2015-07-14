<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies
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

defined('KIMB_CMS') or die('No clean Request');

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

	if( !in_array( 'sites' , $jsoninfo['done'] ) ) {
		$sitecontent->echo_error(' Die Exportdatei enhält keine Seiten! ');
		$sitecontent->output_complete_site();
		die;
	}

	if( $jsoninfo['allg']['sysver'] != $allgsysconf['build'] ){
		$sitecontent->echo_message( 'Die Versionen dieses Systems und des Exportsystems stimmen nicht überein, dies kann zu Problemem führen, wenn Sie fortfahren.' );
	}

	
	$sitecontent->add_site_content( '<a href="'.$addonurl.'&amp;weiter='.$_SESSION['importfile'].'"><button>Import starten</button></a>' );
	$sitecontent->add_site_content( '<br />Mit dem Klick auf "Import starten" fügen Sie diesem CMS weitere Seiten hinzu. Die Seiten werden nicht automatisch einem Menü zugeordnet.' );
}
elseif( $_GET['weiter'] == $_SESSION['importfile'] && !empty( $_GET['weiter'] ) ){

	$fileroot = __DIR__.'/temp/'.$_SESSION['importfile'].'/';

	if( is_dir( $fileroot ) ){

		if( is_dir( $fileroot.'/sites/' ) ){

			$sitesex = scandir( $fileroot.'/sites/' );
			$sitesexin = count( $sitesex );

			$i = 1;
			while( 5 == 5 ){
				if( !check_for_kimb_file( '/site/site_'.$i.'.kimb') && !check_for_kimb_file( '/site/site_'.$i.'_deak.kimb') ){
					$newids[] = $i;
				}
				if( $sitesexin <= count( $newids ) ){
					break;
				}
				$i++;
			}

			$ii = 0;
			foreach( $sitesex as $file ){

				if( $file != '..' && $file != '.' && $file != 'index.kimb' ){

					copy( $fileroot.'/sites/'.$file , __DIR__.'/../../oop/kimb-data/site/site_'.$newids[$ii].'.kimb' );
				
					$ii++;

				}

			}

			$sitecontent->echo_message(' Seiten erfolgreich importiert! ');
			$sitecontent->echo_message(' Die Seiten werden nicht automatisch einem Menü zugeordnet! ');
			$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=im_export"><button>&larr; Zurück</button></a>');

			rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
		}
		else{
			$sitecontent->echo_error(' Keine Seiten in der Exportdatei! ');
		}
	}
	else{
		$sitecontent->echo_error(' Keine Exportdatei! ');
	}
}
else{
	$sitecontent->add_site_content('Fügen Sie Seiten Ihrem CMS hinzu.');

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
