<?php

defined('KIMB_CMS') or die('No clean Request');

$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );

if( $guestbook['file']->read_kimb_search_teilpl( 'siteid' , $allgsiteid ) ){

	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>');
	$sitecontent->add_html_header('<style>'.$guestbook['file']->read_kimb_one( 'css' ).'</style>');

	$guestbook['sitefile'] = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'.kimb' );

	$sitecontent->add_site_content( "\r\n".'<hr id="guestbooktop" /><br />'."\r\n" );

	if( function_exists( 'checklogin' ) && $guestbook['file']->read_kimb_one( 'nurfeloginuser' ) == 'on' ){
		if( checklogin() ){
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

				if( $_POST['captcha'] == $_SESSION['captcha'] && $_POST['name'] != '' && $_POST['mail'] != '' && $_POST['cont'] != ''){

					if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) ){

						$mail = $_POST['mail'];
						$sitecontent->add_html_header('<script>
$( function(){
	localStorage.removeItem( \'name\' );
	localStorage.removeItem( \'mail\' );
	localStorage.removeItem( \'cont\' );
});
</script>');
						$name = preg_replace( "/[^a-zA-Z0-9]/" , "" , $_POST['name'] );
						$codiert = array( '&lt;b&gt;' , '&lt;/b&gt;' , '&lt;u&gt;' , '&lt;/u&gt;' , '&lt;i&gt;' , '&lt;/i&gt;' , '&lt;center&gt;' , '&lt;/center&gt;' );
						$unkodiert = array( '<b>' , '</b>' , '<u>' , '</u>' , '<i>' , '</i>' , '<center>' , '</center>' );
						$cont = str_replace( $codiert , $unkodiert , nl2br( htmlentities ( $_POST['cont'] ) ) );

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

						unset( $mail , $cont , $name , $codiert , $unkodiert );

						$guestbook['showadd'] = ' ';

						if( $guestbook['file']->read_kimb_one( 'newstatus' ) == 'off' ){
							$sitecontent->add_site_content('<center><b><u>Ihre Mitteilung wird vor der Veröffentlichung geprüft!</u></b></center><br />'."\r\n");
						}
					}
					else{
						$sitecontent->echo_error( 'Die E-Mail-Adresse ist falsch!<br /><a href="#guestadd"><button>Verändern</button></a>' , 'unknown' );
						$guestbook['showadd'] = '$( function(){ add(); });';
					}

				}
				else{
					$sitecontent->echo_error( 'Das Captcha wurde falsch gelöst oder eines der Felder war leer!<br /><a href="#guestadd"><button>Verbessern</button></a>' , 'unknown' );
					$guestbook['showadd'] = '$( function(){ add(); });';
				}
			}
	}

	$guestbook['ids'] = explode( ',' , $guestbook['sitefile']->read_kimb_one( 'idlist' ) );
	foreach( $guestbook['ids'] as $guestbook['id'] ){
		$guestbook['alles'][] = $guestbook['sitefile']->read_kimb_id( $guestbook['id'] );
	}

	foreach( $guestbook['alles'] as $guestbook['einer'] ){
		if( $guestbook['einer']['status'] == 'on' ){

			$guestbook['output'] .= '<div id="guest" >'."\r\n";		
			$guestbook['output'] .= '<div id="guestname" >'.$guestbook['einer']['name']."\r\n";
			$guestbook['output'] .= '<span id="guestdate">'.date( 'd-m-Y H:i:s' , $guestbook['einer']['time'] ).'</span>'."\r\n";
			$guestbook['output'] .= '</div>'."\r\n";
			$guestbook['output'] .= $guestbook['einer']['cont']."\r\n";
			$guestbook['output'] .= '</div>'."\r\n\r\n";

			$guestbook['eintr'] = 'yes';

		}
	}

	if( !isset( $guestbook['eintr'] ) ){
		$guestbook['output'] .= '<div id="guest" >'."\r\n";		
		$guestbook['output'] .= 'Keine Mitteilungen';
		$guestbook['output'] .= '</div>'."\r\n\r\n";
	}

	$sitecontent->add_site_content(	$guestbook['output'] );

	if( $guestbook['add'] == 'allowed' ){

		$sitecontent->add_html_header('<script>
function add(){
	$( "div#guestadd" ).css( "display" , "block" );
	$( "button#guestbuttdis" ).css( "display" , "block" );
	$( "button#guestbuttadd" ).css( "display" , "none" );
}
function dis(){
	$( "div#guestadd" ).css( "display" , "none" );
	$( "button#guestbuttdis" ).css( "display" , "none" );
	$( "button#guestbuttadd" ).css( "display" , "block" );
}
function preview(){
	var cont = $( "textarea#cont" ).val();
	var cont = cont.replace(/\n/g, "<br />");
	$( "div#prew" ).html( cont );
	$( "div#prewa" ).css( "display" , "block" );

	return false;
}
$( function(){
	$( "input#name" ).val( localStorage.getItem( \'name\' ) );
	$( "input#mail" ).val( localStorage.getItem( \'mail\' ) );
	$( "textarea#cont" ).val( localStorage.getItem( \'cont\' ) );
});
'.$guestbook['showadd'].'
</script>');

		$sitecontent->add_site_content( '<div id="guest" >'."\r\n" );
		$sitecontent->add_site_content(	'<button onclick=" add(); " id="guestbuttadd">Hinzufügen</button>'."\r\n" );
		$sitecontent->add_site_content(	'<div style="display:none;" id="guestadd" >'."\r\n" );

		$sitecontent->add_site_content('<form action="#guestbooktop" method="post" onsubmit = "
localStorage.setItem( \'name\' , document.getElementById( \'name\' ).value );
localStorage.setItem( \'mail\' , document.getElementById( \'mail\' ).value );
localStorage.setItem( \'cont\' , document.getElementById( \'cont\' ).value );
" >');
		$sitecontent->add_site_content('<input name="name" type="text" id = "name" placeholder="Name" > <!--[if lt IE 10]> ( Name ) <![endif]--> <br />'."\r\n");
		$sitecontent->add_site_content('<input name="mail" type="text" id = "mail" placeholder="E-Mail-Adresse" > ( E-Mail-Adresse - wird nicht veröffentlicht ) <br />'."\r\n");
		$sitecontent->add_site_content('<textarea name="cont" id="cont" placeholder="Ihre Mitteilung" style="width:75%; height:100px;" ></textarea> <!--[if lt IE 10]> ( Ihre Mitteilung ) <![endif]--> <br />'."\r\n");
		$sitecontent->add_site_content('(Erlaubtes HTML: &lt;b&gt; &lt;/b&gt; &lt;u&gt; &lt;/u&gt; &lt;i&gt; &lt;/i&gt; &lt;center&gt; &lt;/center&gt; )<br />'."\r\n");
		$sitecontent->add_site_content('<div style="display:none;" id="prewa" ><div style="background-color:orange; padding:10px; margin:10px;" id="prew" ></div>( Vorschau )<br /></div>'."\r\n");
		$sitecontent->add_site_content('<img id="captcha" src="'.$allgsysconf['siteurl'].'/ajax.php?addon=captcha" style="border:none;" /><br />'."\r\n");
		$sitecontent->add_site_content('<a href="#" onclick=" document.getElementById( \'captcha\' ).src = \''.$allgsysconf['siteurl'].'/ajax.php?addon=captcha&\' + Math.random(); return false;">Nicht lesbar?</a><br />'."\r\n");
		$sitecontent->add_site_content('<input name="captcha" autocomplete="off" type="text" ><br /> ( Bitte geben Sie den Code oben ein, um zu beweisen, dass Sie kein Roboter sind! )<br />'."\r\n");
		if( $guestbook['file']->read_kimb_one( 'ipsave' ) == 'on' ){
			$sitecontent->add_site_content('( Ihre IP wird gespeichert, aber nicht veröffentlicht! )<br />'."\r\n");
		}
		$sitecontent->add_site_content( '<input type="submit" value="Absenden"><button onclick="return preview(); " >Vorschau</button></form><br /><br /><hr />'."\r\n" );
		$sitecontent->add_site_content(	'</div><button onclick=" dis(); " style="display:none;" id="guestbuttdis" >Ausblenden</button></div>'."\r\n" );
	}
	else{
		$sitecontent->add_site_content(	'<div id="guest"><button disabled="disabled">Hinzufügen</button> ( Bitte loggen Sie sich ein! ) </div>'."\r\n" );
	}	
}

unset( $guestbook );
?>
