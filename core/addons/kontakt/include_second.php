<?php

defined('KIMB_CMS') or die('No clean Request');

$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );

$kontakt['siteid'] = $kontakt['file']->read_kimb_one( 'siteid' );

if( $allgsiteid == $kontakt['siteid'] ){

	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>');

	$_SESSION['code'] = mt_rand();

	if( $kontakt['file']->read_kimb_one( 'other' ) == 'on' ){

		$sitecontent->add_html_header('<script>
$( function(){
	var ccd = '.$_SESSION['code'].'; 
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

	if( $kontakt['file']->read_kimb_one( 'form' ) == 'on' && $kontakt['file']->read_kimb_one( 'formaddr' ) != '' ){

		$sitecontent->add_site_content( '<hr /><h2>Kontaktformular</h2>' );

		if( isset( $_POST['captcha'] ) ){

			if( $_POST['captcha'] == $_SESSION['captcha'] && $_POST['name'] != '' && $_POST['mail'] != '' && $_POST['cont'] != ''){

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
			$sitecontent->add_site_content('<input name="name" type="text" id = "name" placeholder="Name" > <!--[if lt IE 10]> ( Name ) <![endif]--> <br />');
			$sitecontent->add_site_content('<input name="mail" type="text" id = "mail" placeholder="E-Mail-Adresse" > <!--[if lt IE 10]> ( E-Mail-Adresse ) <![endif]--> <br />');
			$sitecontent->add_site_content('<textarea name="cont" id="cont" placeholder="Ihre Mitteilung" style="width:75%; height:200px;" ></textarea> <!--[if lt IE 10]> ( Ihre Mitteilung ) <![endif]--> <br />');
			$sitecontent->add_site_content('<img id="captcha" src="'.$allgsysconf['siteurl'].'/ajax.php?addon=captcha" style="border:none;" /><br />');
			$sitecontent->add_site_content('<a href="#" onclick=" document.getElementById( \'captcha\' ).src = \''.$allgsysconf['siteurl'].'/ajax.php?addon=captcha&\' + Math.random(); return false;">Nicht lesbar?</a><br />');
			$sitecontent->add_site_content('<input name="captcha" autocomplete="off" type="text" ><br /> ( Bitte geben Sie den Code oben ein, um zu beweisen, dass Sie kein Roboter sind! )<br />');
			$sitecontent->add_site_content('<input type="submit" value="Absenden"> </form><br /><br /><hr />');

		}
	}

}

unset( $kontakt );
?>
