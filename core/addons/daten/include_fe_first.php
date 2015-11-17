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

//jQuery und Hash werden benötigt
$sitecontent->add_html_header('<!-- jQuery UI -->');

/*
Management der Userrechte (Speicherplatz, nur lesen)
JavaScript Verschlüsselung von Dateien und Tabellen möglich (https://bitwiseshiftleft.github.io/sjcl/)
Verwendung von Felogin
JSON für Tabellen
AJAX gestützt

Seite per Felogin geschützt, lädt per AJAX Liste der Gruppen
Gruppen mit Tabellen und Dateien
Gruppe für alle User
Gruppe für bestimmte User
*/


?>
