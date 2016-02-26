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


$sitecontent->add_site_content('Dieses Add-on informiert per E-Mail wenn sich ein User am Backend angemeldet hat oder wenn fehlgeschlagene Anmeldeversuche am Backend verzeichnet wurden.');

$sitecontent->add_site_content('<h3>Backend Login</h3>');
$sitecontent->add_site_content('Jeder User erhält nach dem Login im Backend eine Information an seinen E-Mail-Adresse (siehe Usereinstellungen).');

$sitecontent->add_site_content('<h3>Fehlgeschlagene Anmeldeversuche</h3>');
$sitecontent->add_site_content('Sollte nach 3 Anmeldeversuchen noch ein weiterer Versuch durchgeführt werden, so wird an die in der Konfiguration angegebene Admin E-Mail-Adresse eine Information versandt.');

$sitecontent->add_site_content('<br /><br /><br /><b>Einstellungen sind nicht verfügbar!</b>');

?>
