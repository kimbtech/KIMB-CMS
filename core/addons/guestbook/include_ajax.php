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

if( !empty( $_POST['vorschau_name'] ) && !empty( $_POST['vorschau_cont'] ) ){
	//Vorschau
	
	if( $_POST['vorschau_name'] == '---username---' && !empty($_SESSION['felogin']['user']) ){
		$_POST['vorschau_name'] = $_SESSION['felogin']['user'];
	}
	elseif($_POST['vorschau_name'] == '---username---'){
		$_POST['vorschau_name'] = 'Name';
	}
	
	$arr = make_guestbook_html( $_POST['vorschau_cont'], $_POST['vorschau_name'] );
	
	echo '<div id="guest" >'."\r\n";		
	echo '<div id="guestname" >'.$arr[1]."\r\n";
	echo '<span id="guestdate">'.date( 'd-m-Y H:i:s' ).'</span>'."\r\n";
	echo '</div>'."\r\n";
	echo $arr[0]."\r\n";
	echo '</div>'."\r\n\r\n";

	die;
}
elseif( isset( $_GET['loadadd'] ) ){
	
	$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );
	
	$pl = htmlspecialchars( $_GET['pl'] );
	
	if( $pl == 'new' ){
		$pl = 0;
	}
	
	echo ('<form action="#guestbooktop" method="post" onsubmit = "return savesubmit();" >');

	if( !function_exists( 'check_felogin_login' ) || !check_felogin_login( '---session---', '---allgsiteid---', true ) ){
		echo ('<input name="name" type="text" id = "name" class="name_'.$pl.'" placeholder="Name" > <!--[if lt IE 10]> (Name) <![endif]--> <br />'."\r\n");
		echo ('<input name="mail" type="text" id = "mail" placeholder="E-Mail-Adresse" > (E-Mail-Adresse - wird nicht veröffentlicht) <br />'."\r\n");
	}
	echo ('<textarea name="cont" id="cont" class="cont_'.$pl.'" placeholder="Ihre Mitteilung" style="width:75%; height:100px;" ></textarea> <!--[if lt IE 10]> (Ihre Mitteilung) <![endif]--> <br />'."\r\n");
	echo ('(Erlaubtes HTML: &lt;b&gt; &lt;/b&gt; &lt;u&gt; &lt;/u&gt; &lt;i&gt; &lt;/i&gt; &lt;center&gt; &lt;/center&gt; )<br />URLs (http://example.com/) werden automatisch zu Links umgewandelt.<br />'."\r\n");
	echo ('<div style="display:none;" id="prewarea_'.$pl.'" ><div style="background-color:orange; padding:10px; margin:10px;" id="prew_'.$pl.'" ></div>(Vorschau)<br /></div>'."\r\n");

	if( !function_exists( 'check_felogin_login' ) || !check_felogin_login( '---session---', '---allgsiteid---', true ) ){
		echo ( make_captcha_html() );
		echo ('<br />(Bitte geben Sie den Code oben ein, um zu beweisen, dass Sie kein Roboter sind!)<br />'."\r\n");
	}

	if( $guestbook['file']->read_kimb_one( 'ipsave' ) == 'on' ){
		echo ('(Ihre IP wird gespeichert, aber nicht veröffentlicht!)<br />'."\r\n");
	}
	echo ('<input type="hidden" value="'.htmlspecialchars( $_GET['pl'] ).'" name="place">');
	echo ( '<input type="submit" value="Absenden"><button onclick="return preview( '.$pl.' ); " >Vorschau</button></form>'."\r\n" );

	die;	
}
elseif( isset( $_GET['answer'] ) && is_numeric( $_GET['id'] ) && is_numeric( $_GET['siteid'] ) ){
	
	$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );
	
	if( function_exists( 'check_felogin_login' ) && $guestbook['file']->read_kimb_one( 'nurfeloginuser' ) == 'on' ){
		if( check_felogin_login( '---session---', $_GET['siteid'], true ) ){
			$guestbook['add'] = true;
		}
		else{
			$guestbook['add'] = false;
		}
	}
	else{
			$guestbook['add'] = true;
	}
	
	if( $guestbook['add'] ){
	
		$readfile = new KIMBdbf( 'addon/guestbook__id_'.$_GET['siteid'].'_answer_'.$_GET['id'].'.kimb' );
		
		foreach( $readfile->read_kimb_all_teilpl('allidslist') as $id ){
			
			$eintr = $readfile->read_kimb_id( $id );
			
			if( $eintr['status'] == 'on' ){
	
				echo '<div id="guest" >'."\r\n";		
				echo '<div id="guestname" >'.$eintr['name']."\r\n";
				echo '<span id="guestdate">'.date( 'd-m-Y H:i:s' , $eintr['time'] ).'</span>'."\r\n";
				echo '</div>'."\r\n";
				echo $eintr['cont']."\r\n";
				echo '</div>'."\r\n";
				
			}
		}
	}
	else{
		echo 'Keine Rechte!';
	}
	die;
}

echo 'Falscher Zugriff auf Guestbook AJAX';

?>
