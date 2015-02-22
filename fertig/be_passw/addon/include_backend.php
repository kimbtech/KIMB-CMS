<?php

defined('KIMB_Backend') or die('No clean Request');

if( $_SESSION['loginokay'] != $allgsysconf['loginokay'] ){

	if( isset( $_GET['passreset'] ) ){

		if( !empty( $_POST['usern'] ) || !empty( $_POST['mail'] ) ){
			
			$userfile = new KIMBdbf('backend/users/list.kimb');

			if( !empty( $_POST['usern'] ) ){
				$id = $userfile->search_kimb_xxxid( $_POST['usern'] , 'user' );
			}
			else{
				$id = $userfile->search_kimb_xxxid( $_POST['mail'] , 'mail' );
			}

		
			if( $id != false ){
				$user = $userfile->read_kimb_id( $id );

				$newpass = makepassw( 15 );

				$userfile->write_kimb_id( $id , 'add' , 'passw' , sha1( $newpass ) );

				$text = 'Hallo '.$user['name'].','."\n\r".' Ihr neues Passwort lautet:'."\n\r\n\r";
				$text .= $newpass."\n\r\n\r";
				$text .= $allgsysconf['sitename'];
				send_mail( $user['mail'] , $text );
			}

			$sitecontent->add_site_content( '<div class="ui-overlay"><div class="ui-widget-overlay"></div>' );
			$sitecontent->add_site_content( '<div class="ui-widget-shadow ui-corner-all" style="width: 300px; height: 150px; position: absolute; left: 300px; top: 100px;"></div></div>' );
			$sitecontent->add_site_content( '<div style="position: absolute; width: 280px; height: 130px; left: 300px; top: 100px; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all">' );
			$sitecontent->add_site_content( 'Sofern Ihr Username/ Ihre E-Mail-Adresse in der Datenbank gefunden wurde, haben Sie ein neues Paswort per E-Mail erhalten!' );
			$sitecontent->add_site_content( '<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/index.php" ><button>Login</button></a>' );
			$sitecontent->add_site_content( '</div>' );
		}
		else{
			$sitecontent->add_site_content( '<div class="ui-overlay"><div class="ui-widget-overlay"></div>' );
			$sitecontent->add_site_content( '<div class="ui-widget-shadow ui-corner-all" style="width: 300px; height: 150px; position: absolute; left: 300px; top: 100px;"></div></div>' );
			$sitecontent->add_site_content( '<div style="position: absolute; width: 280px; height: 130px; left: 300px; top: 100px; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all">' );
			$sitecontent->add_site_content( '<h2>Neues Passwort per Mail zusenden</h2>' );
			$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/index.php?passreset" method="post" >' );
			$sitecontent->add_site_content( '<input type="text" name="mail" placeholder="E-Mail-Adresse" ><!--[if lt IE 10]> ( E-Mail-Adresse ) <![endif]--> oder' );
			$sitecontent->add_site_content( '<input type="text" name="usern" placeholder="Username" ><!--[if lt IE 10]> ( Username ) <![endif]--><br />' );
			$sitecontent->add_site_content( '<input type="submit" value="Neues Passwort zusenden!" ><br />');
			$sitecontent->add_site_content( '</form>' );
			$sitecontent->add_site_content( '</div>' );
		}
	
	}
	else{
		$sitecontent->add_site_content( '<a style="position:absolute; top:200px;" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/index.php?passreset">Passwort vergessen?</a>' );
	}

}
?>
