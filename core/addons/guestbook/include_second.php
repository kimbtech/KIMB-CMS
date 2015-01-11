<?php

defined('KIMB_CMS') or die('No clean Request');

$guestbook['file'] = new KIMBdbf( 'addon/guestbook__conf.kimb' );

if( $guestbook['file']->read_kimb_search_teilpl( 'siteid' , $allgsiteid ) ){
if( check_for_kimb_file( 'addon/guestbook__id_'.$allgsiteid.'.kimb' ) ){

	//checklogin();
	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>');
	$sitecontent->add_html_header('<style>'.$guestbook['file']->read_kimb_one( 'css' ).'</style>');

	$guestbook['sitefile'] = new KIMBdbf( 'addon/guestbook__id_'.$allgsiteid.'.kimb' );
	$guestbook['ids'] = explode( ',' , $guestbook['sitefile']->read_kimb_one( 'idlist' ) );
	foreach( $guestbook['ids'] as $guestbook['id'] ){
		$guestbook['alles'][] = $guestbook['sitefile']->read_kimb_id( $guestbook['id'] );
	}

	$guestbook['output'] = "\r\n".'<hr /><br />'."\r\n";

	foreach( $guestbook['alles'] as $guestbook['einer'] ){
		if( $guestbook['einer']['status'] == 'on' ){

			$guestbook['output'] .= '<div id="guest" >'."\r\n";		
			$guestbook['output'] .= '<div id="guestname" >'.$guestbook['einer']['name']."\r\n";
			$guestbook['output'] .= '<span id="guestdate">'.date( 'd-m-Y H:i:s' , $guestbook['einer']['time'] ).'</span>'."\r\n";
			$guestbook['output'] .= '</div>'."\r\n";
			$guestbook['output'] .= $guestbook['einer']['cont']."\r\n";
			$guestbook['output'] .= '</div>'."\r\n\r\n";

		}
	}


	$sitecontent->add_site_content(	$guestbook['output'] );

	if( isset( $_POST['captcha'] ) ){
		$guestbook['showadd'] = '$( function(){ add(); });';
	}
	else{
		$guestbook['showadd'] = ' ';
	}

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
$( function(){
	$( "input#name" ).val( localStorage.getItem( \'name\' ) );
	$( "input#mail" ).val( localStorage.getItem( \'mail\' ) );
	$( "textarea#cont" ).val( localStorage.getItem( \'cont\' ) );
});
'.$guestbook['showadd'].'
</script>');

	$sitecontent->add_site_content( '<div id="guest" >' );
	$sitecontent->add_site_content(	'<button onclick=" add(); " id="guestbuttadd">Hinzufügen</button>' );
	$sitecontent->add_site_content(	'<div style="display:none;" id="guestadd" >' );

	$sitecontent->add_site_content('<form action="" method="post" onsubmit = "
localStorage.setItem( \'name\' , document.getElementById( \'name\' ).value );
localStorage.setItem( \'mail\' , document.getElementById( \'mail\' ).value );
localStorage.setItem( \'cont\' , document.getElementById( \'cont\' ).value );
" >');
	$sitecontent->add_site_content('<input name="name" type="text" id = "name" placeholder="Name" > <!--[if lt IE 10]> ( Name ) <![endif]--> <br />');
	$sitecontent->add_site_content('<input name="mail" type="text" id = "mail" placeholder="E-Mail-Adresse" > ( E-Mail-Adresse - wird nicht veröffentlicht ) <br />');
	$sitecontent->add_site_content('<textarea name="cont" id="cont" placeholder="Ihre Mitteilung" style="width:75%; height:200px;" ></textarea> <!--[if lt IE 10]> ( Ihre Mitteilung ) <![endif]--> <br />');
	$sitecontent->add_site_content('<img id="captcha" src="'.$allgsysconf['siteurl'].'/ajax.php?addon=captcha" style="border:none;" /><br />');
	$sitecontent->add_site_content('<a href="#" onclick=" document.getElementById( \'captcha\' ).src = \''.$allgsysconf['siteurl'].'/ajax.php?addon=captcha&\' + Math.random(); return false;">Nicht lesbar?</a><br />');
	$sitecontent->add_site_content('<input name="captcha" autocomplete="off" type="text" ><br /> ( Bitte geben Sie den Code oben ein, um zu beweisen, dass Sie kein Roboter sind! )<br />');
	if( $guestbook['file']->read_kimb_one( 'ipsave' ) == 'on' ){
		$sitecontent->add_site_content('( Ihre IP wird gespeichert, aber nicht veröffentlicht! )<br />');
	}
	$sitecontent->add_site_content( '<input type="submit" value="Absenden"></form><br /><br /><hr />' );
	$sitecontent->add_site_content(	'</div><button onclick=" dis(); " style="display:none;" id="guestbuttdis" >Ausblenden</button></div>' );
	

}	
}

unset( $guestbook );
?>
