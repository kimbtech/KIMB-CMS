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

if( $allgerr != '403'){
	
	$sitecontent->add_site_content( '<div class="addon_kontakt">');

	$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );

	$sitecontent->add_html_header('<!-- jQuery -->');

	$_SESSION['kontakt_code'] = makepassw( 10, '', 'numaz');

	if( $kontakt['file']->read_kimb_one( 'other' ) == 'on' ){

		$sitecontent->add_html_header('<script>
$( function(){
	var ccd = "'.$_SESSION['kontakt_code'].'"; 
	$.get( "'.$allgsysconf['siteurl'].'/ajax.php?addon=kontakt&code=" + ccd , function( data ) {
		$( "p.other" ).html( data );
	})
	.fail(function() {
		$( "p.other" ).html( "Fehlerhafte Abfrage" );	
	});
});
</script>');

		$sitecontent->add_site_content( '<p class="other" >Sie benötigen JavaScript um die Inhalt zu sehen</p>' );
	}

	if( $kontakt['file']->read_kimb_one( 'mail' ) == 'on' ){

		$sitecontent->add_html_header('<script>
$( function(){
	$( "p.mailadr" ).html( \'E-Mail: <img style="vertical-align:middle;" src="'.$allgsysconf['siteurl'].'/load/addondata/kontakt/'.$kontakt['file']->read_kimb_one( 'bildname' ).'.png" style="border:none;" title="E-Mail Adresse" alt="E-Mail Adresse">\' );
});
</script>');

		$sitecontent->add_site_content( '<p class="mailadr" >Sie benötigen JavaScript um die E-Mail Adresse zu sehen</p>' );
	}

	if( $kontakt['file']->read_kimb_one( 'form' ) == 'on' && !empty( $kontakt['file']->read_kimb_one( 'formaddr' ) ) ){

		$sitecontent->add_site_content( '<hr /><h2>Kontaktformular</h2>' );
		
		$sitecontent->add_site_content( '<div class="formular">');

		if( isset( $_POST['kontakt_cont'] ) ){

			if( check_captcha() && !empty( $_POST['kontakt_name'] ) && !empty( $_POST['kontakt_mail'] ) && !empty( $_POST['kontakt_cont'] ) ) {

				$inhalt = 'Kontaktformular'."\r\n\r\n\r\n".'Nachricht von '.$_POST['name'].' ( '.$_POST['mail'].' )'."\r\n\r\n".$_POST['cont']."\r\n\r\n".date( 'd.m.Y - H:i' );

				if( send_mail( $kontakt['file']->read_kimb_one( 'formaddr' ) , $inhalt) ){
					$sitecontent->add_site_content( '<center><b><u>Vielen Dank für Ihre Mitteilung!</u></b></center><br /><br /><hr />' );

					$sitecontent->add_html_header('<script>
$( function(){
	localStorage.removeItem( \'name\' );
	localStorage.removeItem( \'mail\' );
	localStorage.removeItem( \'cont\' );
});
</script>');
				}
				else{
					$sitecontent->echo_error( 'Das Formular konnte nicht abgesendet werden!<br /><a href="">Erneut senden!</a>' , 'unknown' );
					$sitecontent->add_site_content( '<br /><hr />' );
				}
			}
			else{
				$sitecontent->echo_error( 'Das Captcha wurde falsch gelöst oder eines der Felder war leer!<br /><a href="">Nochmal!</a>' , 'unknown' );
				$sitecontent->add_site_content( '<br /><hr />' );
			}
		}
		else{

			$sitecontent->add_html_header('<script>
$( function(){
	$( "input#name" ).val( localStorage.getItem( \'name\' ) );
	$( "input#mail" ).val( localStorage.getItem( \'mail\' ) );
	$( "textarea#cont" ).val( localStorage.getItem( \'cont\' ) );
});
</script>');

			$sitecontent->add_site_content('<form action="" method="post" onsubmit = "
localStorage.setItem( \'name\' , document.getElementById( \'name\' ).value );
localStorage.setItem( \'mail\' , document.getElementById( \'mail\' ).value );
localStorage.setItem( \'cont\' , document.getElementById( \'cont\' ).value );
" >');
			$sitecontent->add_site_content('<input name="kontakt_name" type="text" id = "kontakt_name" placeholder="Name" > <!--[if lt IE 10]> (Name) <![endif]--> <br />');
			$sitecontent->add_site_content('<input name="kontakt_mail" type="text" id = "kontakt_mail" placeholder="E-Mail-Adresse" > <!--[if lt IE 10]> (E-Mail-Adresse) <![endif]--> <br />');
			$sitecontent->add_site_content('<textarea name="kontakt_cont" id="kontakt_cont" placeholder="Ihre Mitteilung" style="width:75%; height:200px;" ></textarea> <!--[if lt IE 10]> (Ihre Mitteilung) <![endif]--> <br />');
			$sitecontent->add_site_content( make_captcha_html() );
			$sitecontent->add_site_content('<br />(Bitte geben Sie den Code oben ein, um zu beweisen, dass Sie kein Roboter sind!)<br />');
			$sitecontent->add_site_content('<input type="submit" value="Absenden"> </form><br /><br />');

		}
		
		$sitecontent->add_site_content( '</div>');
	}

	$sitecontent->add_site_content( '</div>');
	
	unset( $kontakt );
}
?>
