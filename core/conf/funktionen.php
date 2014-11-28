<?php

function send_mail($to, $inhalt){
	global $allgsysconf;
	if( $inhalt == '' || $to == ''){
		return false;
	}

	if(mail($to, 'Nachricht von: '.$allgsysconf['sitename'], $inhalt, 'From: '.$allgsysconf['sitename'].' <'.$allgsysconf['mailvon'].'>')){
		return true;
	}
	else{
		return false;
	}
}


function open_url($url, $area = 'insystem'){
	global $allgsysconf;

	if( $area == 'insystem'){
		$url = $allgsysconf['siteurl'].$url;
	}

	if($allgsysconf['urlweitermeth'] == '1'){
		header('Location: '.$url);
		die;
	}
	elseif($allgsysconf['urlweitermeth'] == '2'){
		echo('<meta http-equiv="Refresh" content="0; URL='.$url.'">');
		die;
	}
}

?>
