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

$conffile = new KIMBdbf( 'addon/sitemapxml__conf.kimb' );

if( !empty($_POST['age']) && is_numeric($_POST['age'])){

	if( $_POST['age'] != $conffile->read_kimb_one( 'age' ) ){
						
		//Wert anpassen
		$conffile->write_kimb_one( 'age', $_POST['age'] );
						
		//Stunden aus Übergabe in Sekunden umrechen
		$agesec = $_POST['age']*3600;
							
		//Add-on API wish
		$a = new ADDonAPI( 'sitemapxml' );
		$a->set_cron( $agesec );
		
		$sitecontent->echo_message( 'Der Abstand der Erstellungen wurde verändert!');
				
	}
}

$age = $conffile->read_kimb_one( 'age' );

$sitecontent->add_site_content( '<form action="'.$addonurl.'" method="post">' );
$sitecontent->add_site_content( '<input type="number" name="age" placeholder="144" value="'.$age.'">Abstand <b title="Bitte geben Sie hier den Abstand zwischen den Erstellungen einer neuen Sitemap in Stunden an!">*</b><br />' );
$sitecontent->add_site_content( '<input type="submit" value="Ändern">' );
$sitecontent->add_site_content( '</form>' );

$sitecontent->add_site_content( '<h3>Cron-Job</h3>' );
$sitecontent->add_site_content( 'Bitte richten Sie einen Cron-Job auf diese URL für das CMS ein:<br />' );
$sitecontent->add_site_content( $allgsysconf['siteurl'].'/cron.php?key='.$allgsysconf['cronkey'].'<br />' );

$sitecontent->add_site_content( '<br /><br /><i>Der tatsächliche Abstand zwischen der Erstellung  der Sitemaps hängt vom Abstand der Aufrufe der cron.php ab!</i>' );

?>