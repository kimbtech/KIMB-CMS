<?php
session_start();

function cmsfelogin(){
	$url403 = <<[[url403]]>>;
	$homelink = <<[[homelink]]>>;
	$loginokay = <<[[loginokay]]>>;
	$allowgr = array( <<[[allowgr]]>> );
	$allowusr = array( <<[[allowusr]]>> );

	if( $loginokay == $_SESSION['felogin']['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

		if( in_array( $_SESSION['felogin']['gruppe'], $allowgr ) ){
			$loggedin = true;
		}
		elseif( in_array( $_SESSION['felogin']['user'], $allowusr ) ){
			$loggedin = true;
		}
		else{
			$loggedin = false;
		}

	}
	else{
		$loggedin = false;
	}
	
	if( $loggedin == false ){
		header( 'HTTP/1.0 403 Forbidden' );
		if( !empty( $url403 ) ){
			header( 'Location: '.$url403 );
		}
		else{
			$output = '<!DOCTYPE html>';
			$output .= '<html><head>';
			$output .= '<title>Error 403</title>';
			$output .= '</head><body>';
			$output .= '<h1>Error 403 - Kein Zugriff!</h1>';
			$output .= '<h3>Bitte loggen Sie sich ein!</h3>';
			$output .= '<a href="'.$homelink.'" target="_blank" >Login</a>';
			$output .= '</body></html>';
		}
		echo $output;
		die;
	}

	$output = '<!-- KIMB CMS API Login - Anfang -->';
	$output .= '<div style="position:absolute; right:2px; top:2px; border-radius:15px; border:solid 2px #888; min-width:100px; min-height:100px; padding:20px; background-color:#ccc;">';
	$output .= 'Hallo '.$_SESSION['felogin']['name'].',<br />';
	$output .= 'Sie sind eingeloggt!<br />';
	$output .= '<a href="'.$homelink.'" target="_blank" >Home</a>';
	$output .= '<form method="post" action="'.$homelink.'"><input type="hidden" value="yes" name="logout"><input type="submit" value="Logout"></form>';
	$output .= '</div>';
	$output .= '<!-- KIMB CMS API Login - Ende -->';

	return $output;

}

//echo cmsfelogin();
?>
