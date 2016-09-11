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

//URL
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=sitemapxml';


//Add-on API wish
$a = new ADDonAPI( 'sitemapxml' );
$agesec = $a->read( 'cron' );
$age = intval( $agesec['minage'] / 3600 );
$age = ( empty( $age ) ? '' : $age );

if( !empty($_POST['age']) && is_numeric($_POST['age'])){

	if( $_POST['age'] != $age ){
						
		//Stunden aus Übergabe in Sekunden umrechen
		$agesec = $_POST['age']*3600;

		//für Formular
		$age = $_POST['age'];
							
		//Mit Add-on API setzen		
		$a->set_cron( $agesec );
		
		$sitecontent->echo_message( 'Der Abstand der Erstellungen wurde verändert!');
				
	}
}

//Formular
$sitecontent->add_site_content( '<form action="'.$addonurl.'" method="post">' );
$sitecontent->add_site_content( '<input type="number" name="age" placeholder="144" value="'.$age.'">Abstand <b title="Bitte geben Sie hier den Abstand zwischen den Erstellungen einer neuen Sitemap in Stunden an!">*</b><br />' );
$sitecontent->add_site_content( '<input type="submit" value="Ändern">' );
$sitecontent->add_site_content( '</form>' );
$sitecontent->add_site_content( '<i>Der tatsächliche Abstand zwischen den Erstellungen der Sitemaps hängt vom Abstand der Aufrufe der cron.php ab!</i>' );

$sitecontent->add_site_content( '<h3>Cron-Job</h3>' );
$sitecontent->add_site_content( '<p>Bitte richten Sie einen Cron-Job für diese Installation des CMS ein!</p>' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/cron.php?key='.$allgsysconf['cronkey'].'" target="_blank" title="URL für Cron-Job">Cron-Job</a>' );

$sitecontent->add_site_content( '<h3>Sitemap veröffentlichen</h3>' );
$sitemaplink = $allgsysconf['siteurl'].'/sitemap.xml';
$sitecontent->add_site_content( '<p>Ihre Sitemap befindet sich <a href="'.$sitemaplink.'" target="_blank" title="Sitemap Link">hier!</a></p>' );
$sitecontent->add_site_content( '<p>Sie müssen diese URL den Suchmaschinen zeigen, geben Sie die URL z.B. in den Google Webmastertools ein und tragen Sie sie in die <code>robots.txt</code> ein.</p>' );

//text für robots.txt
$text = "\r\n".'Sitemap: '.$sitemaplink."\r\n";

//in robots.txt eintragen
//	GET task
//	noch nicht gemacht
if( isset( $_GET['task'] ) && $_GET['task'] == 'eintr' && !isset( $_SESSION['sitemapinrob'] ) ){

	//Datei öffen
	$file = fopen( __DIR__.'/../../../robots.txt' , 'a+' );
	//Schreiben mit Rückmeldung
	if( fwrite( $file , $text ) ){
		$sitecontent->echo_message( 'Die Sitemap wurde in die <code>robots.txt</code> eingetragen!' );
		//jetzt gemacht!
		$_SESSION['sitemapinrob'] = true;
	}
	else{
		$sitecontent->echo_message( 'Die Sitemap konnte nicht in die <code>robots.txt</code> eingetragen werden!' );;
	}
	//schließen
	fclose( $file );
}

//schon eingetragen ?
$robtxt = file_get_contents( __DIR__.'/../../../robots.txt' );
//	nicht gefunden
if( strpos( $robtxt, $text ) === false ){
	$sitecontent->add_site_content( '<p style="color:red;">Sitemap ist nicht in <code>robots.txt</code> eingetragen.</p>' );
	$sitecontent->add_site_content( '<p>Sitemap in die <code>robots.txt</code> eintragen: <a href="'.$addonurl.'&amp;task=eintr"><button>Eintragen</button></a></p>' );
}
else{
	//muss da sein
	$sitecontent->add_site_content( '<p style="color:green;">Sitemap ist in <code>robots.txt</code> eingetragen.</p>' );
}

?>