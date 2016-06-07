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

$sitecontent->add_site_content('<b>Keine Konfiguration möglich!</b>');

$sitecontent->add_site_content('<h2>Test</h2>');

if(check_captcha()){
	$sitecontent->echo_message('Captcha richtig gelöst!');
}
elseif( check_captcha() === false ){
	$sitecontent->echo_error('Captcha falsch gelöst!', '', 'Meldung');
}

$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=captcha" method="post">');

$sitecontent->add_site_content( make_captcha_html() );

$sitecontent->add_site_content('<input type="submit" value="Testen" ></form>');

?>
