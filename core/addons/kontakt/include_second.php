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
			$( "p.mailadr" ).html( \'E-Mail: <img style="vertical-align:middle;" src="'.$allgsysconf['siteurl'].'/load/addondata/kontakt/mail.png" style="border:none;" title="E-Mail Adresse" alt="E-Mail Adresse">\' );
		});
		</script>');

		$sitecontent->add_site_content( '<p class="mailadr" >Sie benötigen JavaScript um die E-Mail Adresse zu sehen</p>' );
	}

	if( $kontakt['file']->read_kimb_one( 'form' ) == 'on' ){

		$sitecontent->add_site_content( '<h2>Kontaktformular</h2>' );

	}

}

unset( $kontakt );
?>
