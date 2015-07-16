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

if( isset( $_POST['file'] ) && ( !empty( $_FILES['exportfile']['name'] ) || !empty( $_POST['exportarchiv'] ) ) ){

	if( !empty( $_POST['exportarchiv'] ) ){
		if( strpos($_POST['exportarchiev'], '..') !== false ){
			echo 'Do not hack me!';
			die;
		}
		
		$_FILES["exportfile"]["tmp_name"] = __DIR__.'/exporte/'.$_POST['exportarchiv'].'.zip';
	}

	$_SESSION['importfile'] = mt_rand();

	$fileroot = __DIR__.'/temp/'.$_SESSION['importfile'].'/';

	$zip = new ZipArchive;
	if ( $zip->open($_FILES["exportfile"]["tmp_name"]) === TRUE ) {
		$zip->extractTo( $fileroot );
		$zip->close();
	}
	else{
		$sitecontent->echo_error('Konnte Exportdatei nicht verarbeiten!');
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

	if( $jsoninfo['allg']['sysver'] != $allgsysconf['build'] ){
		$sitecontent->echo_message( 'Die Versionen dieses Systems und des Exportsystems stimmen nicht überein, dies kann zu Problemem führen, wenn Sie fortfahren.' );
	}

	$sitecontent->add_site_content('<br />');
	$sysaddons = listaddons();
	foreach( $jsoninfo['addons'] as $addon ){
		if( !in_array( $addon, $sysaddons ) ){
			$sitecontent->echo_message( '<b style="color:red;" >Das Add-on "'.$addon.'" war auf dem Exportsystem vorhanden und ist auf diesem System nicht installiert.<br />Dies kann zu Problemen führen!</b>' );
		}
	}
	$sitecontent->add_site_content('<br />');
	
	$sitecontent->add_site_content( '<form action="'.$addonurl.'&amp;weiter='.$_SESSION['importfile'].'" method="post">' );

	$sitecontent->add_site_content( '<h4>Wählen Sie die zu importierenden Kategorien:</h4>' );
	
	$doneread = array( "sites" => 'Seiten', "menue" => 'Menüstruktur', "users" => 'Backend-User', "addon" => 'Add-on Konfiguration', "confs" => 'Systemkonfiguration' , "filem" => 'Dateien des Filemanager' );
	$onclick = array( "sites" => 'onclick=" $(\'input[value=menue]\').prop(\'checked\', false); "', "menue" => 'onclick=" $(\'input[value=sites]\').prop(\'checked\', true); "', "users" => '', "addon" => '', "confs" => '' , "filem" => '' );

	foreach( $jsoninfo['done'] as $done ){
		$sitecontent->add_site_content('<input type="checkbox" value="'.$done.'" name="todo[]" checked="checked" '.$onclick[$done].' >'.$doneread[$done].'<br />');
	}

	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="submit" value="Import starten">');
	$sitecontent->add_site_content('</form>');
	$sitecontent->add_site_content( '<br />Mit dem Klick auf "Import starten" ersetzen Sie die gewählten Daten dieses CMS mit denen der Exportdatei.' );

}
elseif( $_GET['weiter'] == $_SESSION['importfile'] && !empty( $_GET['weiter'] ) ){

	$fileroot = __DIR__.'/temp/'.$_SESSION['importfile'].'/';

	$dbfroot = __DIR__.'/../../oop/kimb-data/';

	if( is_dir( $fileroot ) ){

		$jsoninfo = json_decode( file_get_contents( $fileroot.'/info.jkimb' ) , true );

		//Seiten
		if( in_array( 'sites', $_POST['todo'] ) && in_array( 'sites', $jsoninfo['done'] ) ){

			rm_r( $dbfroot.'site/' );

			copy_r( $fileroot.'sites/', $dbfroot.'site/' );			

			$sitecontent->echo_message(' Seiten importiert! ');
			$sitecontent->add_site_content('<br />');
		}
		//menue
		if( in_array( 'menue', $_POST['todo'] ) && in_array( 'menue', $jsoninfo['done'] ) ){

			rm_r( $dbfroot.'url/' );

			rm_r( $dbfroot.'menue/' );
			
			unlink( $dbfroot.'backend/easy_menue.kimb' );

			copy_r( $fileroot.'menue/url/', $dbfroot.'url/' );		

			copy_r( $fileroot.'menue/menue/', $dbfroot.'menue/' );
			
			copy( $fileroot.'menue/easy_menue.kimb', $dbfroot.'backend/easy_menue.kimb');

			$sitecontent->echo_message(' Menü importiert! ');
			$sitecontent->add_site_content('<br />');
		}
		//User
		if( in_array( 'users', $_POST['todo'] ) && in_array( 'users', $jsoninfo['done'] ) ){

			rm_r( $dbfroot.'backend/users/' );

			copy_r( $fileroot.'users/', $dbfroot.'backend/users/' );	

			$sitecontent->echo_message(' User importiert! <br /> <b style="color:red;" >Achtung, bitte stellen Sie vor Ihrem Logout sicher, dass es einen gültigen Admin (more) Account gibt!</b>');
			$sitecontent->add_site_content('<br />');
		}
		//Add-on
		if( in_array( 'addon', $_POST['todo'] ) && in_array( 'addon', $jsoninfo['done'] ) ){

			rm_r( $dbfroot.'addon/' );

			copy_r( $fileroot.'addon/', $dbfroot.'addon/' );

			$sitecontent->echo_message(' Add-on Konfiguration importiert! <br /> <b style="color:red;" >Achtung, überprüfen Sie die Konfiguration möglichst noch einmal! <br />Um Probleme zu vermeiden wurden alle Add-ons deaktiviert!</b>');
			$sitecontent->add_site_content('<br />');
		}
		//Konfiguration
		if( in_array( 'confs', $_POST['todo'] ) && in_array( 'confs', $jsoninfo['done'] ) ){

			if( !is_object( $conffile ) ){
				$conffile = new KIMBdbf('config.kimb');
			}

			$conffile->write_kimb_id( '001' , 'add' , 'sitename' , $jsoninfo['confs']['sitename'] );

			$conffile->write_kimb_id( '001' , 'add' , 'description' , $jsoninfo['confs']['description'] );

			copy( $fileroot.'confs/sonder.kimb' , $dbfroot.'sonder.kimb' );

			$sitecontent->echo_message(' Systemkonfiguration importiert! ');
			$sitecontent->add_site_content('<br />');
		}
		//Filemanager dateien
		if( in_array( 'filem', $_POST['todo'] ) && in_array( 'filem', $jsoninfo['done'] ) ){

			//open
			copy_r( $fileroot.'filem/open/',  __DIR__.'/../../../load/userdata/' );

			//secured
			rm_r( __DIR__.'/../../secured/' );

			copy_r( $fileroot.'filem/close/', __DIR__.'/../../secured/' );

			copy( $fileroot.'filem/filemanager.kimb' , $dbfroot.'backend/filemanager.kimb' );

			$sitecontent->echo_message(' Filemanager importiert! ');
			$sitecontent->add_site_content('<br />');
		}


		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=im_export"><button>&larr; Zurück</button></a>');

		rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
	}
	else{
		$sitecontent->echo_error(' Keine Exportdatei! ');
	}

}
else{
	$sitecontent->echo_message( 'Mit dem Import <b><u>ersetzen</u></b> Sie alle Inhalte Ihres CMS!!' );
	$sitecontent->add_site_content( '<br /><br />' );

	$sitecontent->add_site_content('<form action="'.$addonurl.'" enctype="multipart/form-data" method="post">');

	$sitecontent->add_site_content('<input name="exportfile" type="file" />');
	$sitecontent->add_site_content('Exportdatei hochladen <span style="display:inline-block;" title="Bitte wählen Sie eine KIMB-CMS Export-Datei (*.kimbex)" class="ui-icon ui-icon-info"></span><br />');
	
	$exps = scandir( __DIR__.'/exporte/', SCANDIR_SORT_DESCENDING );
	$dropdown = '<option value=""></option>';
	foreach( $exps as $exp ){
				
		if( $exp != '.' && $exp != '..'){
					
			$time = substr( $exp, 16, 10);
					
			$dropdown .= '<option value="'.substr($exp, 0, -4 ).'">'.date( 'd.m.Y - H:i', $time).'</option>';
					
			$liste = true;
		}	
	}
	if( $liste ){
		$sitecontent->add_site_content('oder aus der Liste wählen');
		$sitecontent->add_site_content('<select name="exportarchiv">');
		$sitecontent->add_site_content( $dropdown );
		$sitecontent->add_site_content('</select>');
	}
	
	$sitecontent->add_site_content('<br />');
	$sitecontent->add_site_content('<input type="hidden" value="file" name="file">');
	$sitecontent->add_site_content('<input type="submit" value="Import starten">');
	$sitecontent->add_site_content('</form>');

	if( isset( $_SESSION['importfile'] ) ){
		rm_r( __DIR__.'/temp/'.$_SESSION['importfile'].'/' );
	}

}

?>
