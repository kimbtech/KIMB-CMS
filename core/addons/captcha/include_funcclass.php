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

//
//  A simple PHP CAPTCHA script
//
//  Copyright 2013 by Cory LaViska for A Beautiful Site, LLC
//
//  MIT License
//

if( !function_exists('hex2rgb') ) {
    function hex2rgb($hex_str, $return_string = false, $separator = ',') {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = array();
        if( strlen($hex_str) == 6 ) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif( strlen($hex_str) == 3 ) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }
}

//Funktionen für das CMS

//HTML Code für Bild und Eingabefeld machen
function make_captcha_html(){

	$ret = '<img src="'.$allgsysconf['siteurl'].'/ajax.php?addon=captcha" alt="Captcha Code" id="captcha_img"><br />';
	$ret .= '<a href="#" onclick="document.getElementById( \'captcha_img\' ).src = \''.$allgsysconf['siteurl'].'/ajax.php?addon=captcha&n=\' + Math.random(); return false;">NEW?</a><br />';
	$ret .= '<input type="text" name="captcha_code" placeholder="Captcha Code" autocomplete="off"><br />';

	return $ret;
}

//Eingabe testen
function check_captcha(){
	if( $_SESSION['captcha_code'] == $_REQUEST['captcha_code'] ){
		return true;
	}
	elseif( !isset( $_REQUEST['captcha_code'] ) ){
		return NULL;
	}
	else{
		return false;
	}
}
?>
