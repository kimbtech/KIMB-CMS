<?php

$auth = <<[[auth]]>>;
$sysurl = <<[[sysurl]]>>;

if( $_POST['auth'] == $auth ){

	$cont = file_get_contents( __DIR__.'/inhalte.php', NULL, NULL, 13 );

	$arr = json_decode( $cont );
	$arr[] = $_REQUEST['jsondata'];

	file_put_contents( __DIR__.'/inhalte.php', '<?php die; ?>'.json_encode ( $arr ) );

	echo 'taken';
	die;
}
elseif( isset( $_GET['id'] ) ){

	session_start();

	$cont = file_get_contents( __DIR__.'/inhalte.php', NULL, NULL, 13 );
	$arr = json_decode( $cont, true );

	foreach( $arr as $ar ){
		$user = json_decode( $ar, true );
		if( $user['id'] == $_GET['id'] ){
			$_SESSION['felogin']['loginokay'] = $user['loginokay'];
			$_SESSION["ip"] = $user['ip'];
			$_SESSION["useragent"] = $user['ua'];
			$_SESSION['felogin']['gruppe'] = $user['gr'];
			$_SESSION['felogin']['user'] = $user['us'];
			$_SESSION['felogin']['name'] = $user['na'];
		}
		else{
			$newarr[] = $ar;
		}
	}

	file_put_contents( __DIR__.'/inhalte.php', '<?php die; ?>'.json_encode ( $newarr ) );

}

header( 'Location: '.$sysurl );
die;

?>
