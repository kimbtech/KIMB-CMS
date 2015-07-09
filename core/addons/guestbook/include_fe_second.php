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

if( $guestbook['file']->read_kimb_search_teilpl( 'siteid' , $allgsiteid ) ){

	$sitecontent->add_html_header( '<!-- jQuery -->' );
	$sitecontent->add_html_header('<style>'.$guestbook['file']->read_kimb_one( 'css' ).'</style>');

	$guestbook['sitefile'] = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'.kimb' );

	$sitecontent->add_site_content( "\r\n".'<hr id="guestbooktop" /><br />'."\r\n" );

	if( function_exists( 'checklogin' ) && $guestbook['file']->read_kimb_one( 'nurfeloginuser' ) == 'on' ){
		if( isset( $_SESSION['felogin']['user'] ) ){
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

			if( isset( $_POST['captcha'] ) ){

				if( function_exists( 'checklogin' ) && isset( $_SESSION['felogin']['user'] ) ){
					$_POST['captcha'] = $_SESSION['captcha'];
					$_POST['mail'] = $_SESSION['felogin']['user'].'@feloginuser.sys';
					$_POST['name'] = $_SESSION['felogin']['name'];
				}

				if( $_POST['captcha'] == $_SESSION['captcha'] && $_POST['name'] != '' && $_POST['mail'] != '' && $_POST['cont'] != ''){

					if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) ){

						$mail = $_POST['mail'];
						$sitecontent->add_html_header('<script>delsubmit();</script>');

						$array = make_guestbook_html( $_POST['cont'], $_POST['name'] );
						$name = $array['name'];
						$cont = $array['cont'];

						$guestbook['newid'] = $guestbook['sitefile']->next_kimb_id();


						$guestbook['idlist'] = $guestbook['sitefile']->read_kimb_one( 'idlist' );
						if( $guestbook['idlist'] == '' ){
							$guestbook['sitefile']->write_kimb_new( 'idlist' , $guestbook['newid'] );
						}
						else{
							$guestbook['sitefile']->write_kimb_replace( 'idlist' , $guestbook['idlist'].','.$guestbook['newid'] );
						}

						$guestbook['sitefile']->write_kimb_id( $guestbook['newid'] , 'add' , 'name' , $name );
						$guestbook['sitefile']->write_kimb_id( $guestbook['newid'] , 'add' , 'mail' , $mail );
						$guestbook['sitefile']->write_kimb_id( $guestbook['newid'] , 'add' , 'cont' , $cont );
						if( $guestbook['file']->read_kimb_one( 'ipsave' ) == 'on' ){
							$guestbook['sitefile']->write_kimb_id( $guestbook['newid'] , 'add' , 'ip' , $_SERVER['REMOTE_ADDR'] );
						}
						else{
							$guestbook['sitefile']->write_kimb_id( $guestbook['newid'] , 'add' , 'ip' , '0.0.0.0' );
						}
						$guestbook['sitefile']->write_kimb_id( $guestbook['newid'] , 'add' , 'time' , time() );
						$guestbook['sitefile']->write_kimb_id( $guestbook['newid'] , 'add' , 'status' , $guestbook['file']->read_kimb_one( 'newstatus' ) );

						if( $guestbook['file']->read_kimb_one( 'mailinfo' ) == 'on' ){
							send_mail( $guestbook['file']->read_kimb_one( 'mailinfoto' ) , 'Neuer Gästebucheintrag von '.$name.' ('.$mail.') auf SiteID: '.$allgsiteid."\r\n\r\n".$cont );
						}

						$guestbook['showadd'] = ' ';

						if( $guestbook['file']->read_kimb_one( 'newstatus' ) == 'off' ){
							$sitecontent->add_site_content('<center><b><u>Ihre Mitteilung wird vor der Veröffentlichung geprüft!</u></b></center><br />'."\r\n");
						}
					}
					else{
						$sitecontent->echo_error( 'Die E-Mail-Adresse ist falsch!<br /><a href="#guestadd"><button>Verändern</button></a>' , 'unknown' );
						$guestbook['showadd'] = 'add();';
					}

				}
				else{
					$sitecontent->echo_error( 'Das Captcha wurde falsch gelöst oder eines der Felder war leer!<br /><a href="#guestadd"><button>Verbessern</button></a>' , 'unknown' );
					$guestbook['showadd'] = 'add();';
				}
			}
	}

	$guestbook['ids'] = explode( ',' , $guestbook['sitefile']->read_kimb_one( 'idlist' ) );
	foreach( $guestbook['ids'] as $guestbook['id'] ){
		$guestbook['alles'][] = $guestbook['sitefile']->read_kimb_id( $guestbook['id'] );
	}

	$i = 1;
	foreach( $guestbook['alles'] as $guestbook['einer'] ){
		if( $guestbook['einer']['status'] == 'on' ){

			$guestbook['output'] .= '<div id="guest" >'."\r\n";		
			$guestbook['output'] .= '<div id="guestname" >'.$guestbook['einer']['name']."\r\n";
			$guestbook['output'] .= '<span id="guestdate">'.date( 'd-m-Y H:i:s' , $guestbook['einer']['time'] ).'</span>'."\r\n";
			$guestbook['output'] .= '</div>'."\r\n";
			$guestbook['output'] .= $guestbook['einer']['cont']."\r\n";
			if( !empty( $guestbook['einer']['antwo'] ) ){
				$guestbook['output'] .= '<hr /><button onclick="answer( '.$i.', '.$guestbook['einer']['antwo'].');">Antworten lesen und hinzufügen</button>'."\r\n";
			}
			else{
				$guestbook['output'] .= '<hr /><button onclick="answer( '.$i.' );">Antwort hinzufügen</button>'."\r\n";
			}
			$guestbook['output'] .= '</div>'."\r\n";
			$guestbook['output'] .= '</div style="display:none;" id="answer_'.$i.'" class="answer"></div>'."\r\n\r\n";

			$guestbook['eintr'] = 'yes';

			$i++;
		}
	}

	if( !isset( $guestbook['eintr'] ) ){
		$guestbook['output'] .= '<div id="guest" >'."\r\n";		
		$guestbook['output'] .= 'Keine Mitteilungen';
		$guestbook['output'] .= '</div>'."\r\n\r\n";
	}

	$sitecontent->add_site_content($guestbook['output'] );

//später per AJAX
	if( $guestbook['add'] == 'allowed' ){

		$sitecontent->add_html_header('<script>$( function(){ '.$guestbook['showadd'].' }); var siteurl = "'.$allgsysconf['siteurl'].'"; </script>');

		$sitecontent->add_html_header('<script src="'.$allgsysconf['siteurl'].'/load/addondata/guestbook/guestbook.min.js" type="text/javascript" ></script>');

		$sitecontent->add_site_content( '<div id="guest" >'."\r\n" );
		$sitecontent->add_site_content('<button onclick="add( \'new\' ); " id="guestbuttadd">Hinzufügen</button>'."\r\n" );
		$sitecontent->add_site_content('<div style="display:none;" id="guestadd" >'."\r\n" );
		//per AJAX laden
		$sitecontent->add_site_content('</div><button onclick="dis(); " style="display:none;" id="guestbuttdis" >Ausblenden</button></div>'."\r\n" );
	}
	else{
		$sitecontent->add_site_content('<div id="guest"><button disabled="disabled">Hinzufügen</button> (Bitte loggen Sie sich ein!) </div>'."\r\n" );
	}	
}

unset( $guestbook );
?>
