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

defined('KIMB_CMS') or die('No clean Request');

$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );

if( $guestbook['file']->read_kimb_search_teilpl( 'siteid' , $allgsiteid ) && $allgerr != '403' ){

	$sitecontent->add_html_header( '<!-- jQuery -->' );
	$sitecontent->add_html_header('<style>'.$guestbook['file']->read_kimb_one( 'css' ).'</style>');

	$guestbook['sitefile'] = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'.kimb' );

	$sitecontent->add_site_content( "\r\n".'<hr id="guestbooktop" /><br />'."\r\n" );
	
	if( function_exists( 'check_felogin_login' ) && $guestbook['file']->read_kimb_one( 'nurfeloginuser' ) == 'on' ){
		if( check_felogin_login( '---session---', '---allgsiteid---', true ) ){
			$guestbook['add'] = 'allowed';
		}
		else{
			$guestbook['add'] = 'disallowed';
		}
	}
	else{
			$guestbook['add'] = 'allowed';
	}
	
	if( $guestbook['add'] == 'allowed' ){
	
		if( isset( $_POST['place'] ) ){

			if( function_exists( ' check_felogin_login' ) ){
				if( check_felogin_login( '---session---', '---allgsiteid---', true ) ){
					$_REQUEST['captcha_code'] = $_SESSION['captcha_code'];
					$_POST['mail'] = $_SESSION['felogin']['user'].'@feloginuser.sys';
					$_POST['name'] = $_SESSION['felogin']['name'];
				}
			}
			
			if( $_POST['place'] != 'new' && is_numeric( $_POST['place'] ) ){
				$id = $_POST['place'];
				$addjs = '$( function(){ answer( '.$id.', "yes" ); });';
			}
			else{
				$addjs = '$( function(){ add( "new" ); });';
			}

			if( check_captcha() && !empty( $_POST['name'] ) && !empty( $_POST['mail'] ) && !empty( $_POST['cont'] ) ){

				if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) ){
					
					if( $_POST['place'] != 'new' && is_numeric( $_POST['place'] ) ){
						$id = $_POST['place'];
						$addfile = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'_answer_'.$id.'.kimb' );
						$guestbook['sitefile']->write_kimb_id( $id , 'add' , 'antwo' , 'yes' );
						$guestbook['showadd'] = 'delsubmit();'."\r\n".'$( function(){ answer( '.$id.', "yes" ); });';
					}
					else{
						$addfile = $guestbook['sitefile'];
						$guestbook['showadd'] = 'delsubmit();';
					}

					$mail = $_POST['mail'];

					$array = make_guestbook_html( $_POST['cont'], $_POST['name'] );
					$name = $array[1];
					$cont = $array[0];

					$guestbook['newid'] = $addfile->next_kimb_id();

					$guestbook['idlist'] = $addfile->read_kimb_one( 'idlist' );
					if( empty( $guestbook['idlist'] ) ){
						$addfile->write_kimb_new( 'idlist' , $guestbook['newid'] );
					}
					else{
						$addfile->write_kimb_replace( 'idlist' , $guestbook['idlist'].','.$guestbook['newid'] );
					}

					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'name' , $name );
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'mail' , $mail );
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'cont' , $cont );
					if( $guestbook['file']->read_kimb_one( 'ipsave' ) == 'on' ){
						$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'ip' , $_SERVER['REMOTE_ADDR'] );
					}
					else{
						$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'ip' , '0.0.0.0' );
					}
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'time' , time() );
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'status' , $guestbook['file']->read_kimb_one( 'newstatus' ) );
					$addfile->write_kimb_id( $guestbook['newid'] , 'add' , 'antwo' , 'no' );

					if( $guestbook['file']->read_kimb_one( 'mailinfo' ) == 'on' ){
						send_mail( $guestbook['file']->read_kimb_one( 'mailinfoto' ) , 'Neuer Gaestebucheintrag von '.$name.' ('.$mail.') auf SiteID: '.$allgsiteid."\r\n\r\n".$cont );
					}

					if( $guestbook['file']->read_kimb_one( 'newstatus' ) == 'off' ){
						$sitecontent->add_site_content('<h3>Ihre Mitteilung wird vor der Veröffentlichung geprüft!</h3>'."\r\n");
					}
				}
				else{
					$sitecontent->add_site_content( '<h3>Die E-Mail-Adresse ist falsch!</h3>' );
					$guestbook['showadd'] = $addjs;
				}

			}
			else{
				$sitecontent->add_site_content( '<h3>Das Captcha wurde falsch gelöst oder eines der Felder war leer!</h3>' );
				$guestbook['showadd'] = $addjs;
			}
		}
	}

	$sitecontent->add_html_header('<script>var siteurl = "'.$allgsysconf['siteurl'].'", siteid = "'.$allgsiteid.'"; </script>');

	$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/addondata/guestbook/guestbook.min.js" type="text/javascript" ></script>');
	
	$sitecontent->add_html_header('<script>'.$guestbook['showadd'].'</script>');

	$guestbook['ids'] = explode( ',' , $guestbook['sitefile']->read_kimb_one( 'idlist' ) );
	foreach( $guestbook['ids'] as $guestbook['id'] ){
		$array = $guestbook['sitefile']->read_kimb_id( $guestbook['id'] );
		$array['file_id'] = $guestbook['id'];
		$guestbook['alles'][] = $array;
	}

	foreach( $guestbook['alles'] as $guestbook['einer'] ){
		if( $guestbook['einer']['status'] == 'on' ){

			$guestbook['output'] .= '<div id="guest" >'."\r\n";		
			$guestbook['output'] .= '<div id="guestname" >'.$guestbook['einer']['name']."\r\n";
			$guestbook['output'] .= '<span id="guestdate">'.date( 'd-m-Y H:i:s' , $guestbook['einer']['time'] ).'</span>'."\r\n";
			$guestbook['output'] .= '</div>'."\r\n";
			$guestbook['output'] .= $guestbook['einer']['cont']."\r\n";
			
			$i = $guestbook['einer']['file_id'];
			if( $guestbook['einer']['antwo'] == 'yes' ){
				$guestbook['output'] .= '<hr /><button onclick="answer( '.$i.', \'yes\' );">Antworten lesen und hinzufügen</button>'."\r\n";
			}
			else{
				$guestbook['output'] .= '<hr /><button onclick="answer( '.$i.' );">Antwort hinzufügen</button>'."\r\n";
			}
			
			$guestbook['output'] .= '</div>'."\r\n";
			$guestbook['output'] .= '<div class="answer_'.$i.' answer" style="display:none;" ><div id="answer_'.$i.'_dis" ></div><hr /><div id="answer_'.$i.'_add" ></div></div>'."\r\n\r\n";

			$guestbook['eintr'] = 'yes';
		}
	}

	if( !isset( $guestbook['eintr'] ) ){
		$guestbook['output'] .= '<div id="guest" >'."\r\n";		
		$guestbook['output'] .= 'Keine Mitteilungen';
		$guestbook['output'] .= '</div>'."\r\n\r\n";
	}

	$sitecontent->add_site_content($guestbook['output'] );

	if( $guestbook['add'] == 'allowed' ){

		$sitecontent->add_site_content( '<div id="guest" >'."\r\n" );
		$sitecontent->add_site_content('<button onclick="add( \'new\' ); " id="guestbuttadd">Hinzufügen</button>'."\r\n" );
		$sitecontent->add_site_content('<div style="display:none;" id="guestadd" >'."\r\n" );
		//per AJAX laden
		$sitecontent->add_site_content('</div><hr /><button onclick="dis(); " style="display:none;" id="guestbuttdis" >Ausblenden</button></div>'."\r\n" );
	}
	else{
		$sitecontent->add_site_content('<div id="guest"><button disabled="disabled">Hinzufügen</button> (Bitte loggen Sie sich ein!) </div>'."\r\n" );
	}	
}

unset( $guestbook );
?>
