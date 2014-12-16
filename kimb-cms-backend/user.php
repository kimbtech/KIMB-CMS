<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

if( $_GET['user'] == $_SESSION['user'] && $_GET['todo'] == 'edit' ){
	check_backend_login();
}
else{
	check_backend_login('more');
}

//BE-User erstellen, bearbeiten

$userfile = new KIMBdbf('backend/users/list.kimb');
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if( $_GET['todo'] == 'new' ){
	//felder für neu dann zu edit
}
elseif( $_GET['todo'] == 'edit'){

	//verarbeitung löschen und level nur bei admins (löschen mit Fragedialog)
	//Passworteigaben jQuery Farben

	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit" method="post" onsubmit="if( document.getElementById(\'passwort1\').value != document.getElementById(\'passwort2\').value ){ return false; }"><br />');
	$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly"> <i title="Username für das Login ( keine Änderung möglich )">Username</i><br />');
	$sitecontent->add_site_content('<input type="text" name="name" > <i title="Name des Users" >Name</i><br />');
	$sitecontent->add_site_content('<input type="text" name="mail" > <i title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse</i><br />');
	$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more">Admin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
	$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1"> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort</i> <br />');
	$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2"> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort</i> <br />');
	$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
	$sitecontent->add_site_content('</form>');

	$sitecontent->add_site_content('<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_GET['user'].'&amp;del"><span class="ui-icon ui-icon-trash" title="Diesen User löschen."></span></a>');
}
elseif( $_GET['todo'] == 'list'){

	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new"><span class="ui-icon ui-icon-plusthick" title="Einen neuen User erstellen."></span></a>');
	$sitecontent->add_site_content('<table width="100%"><tr> <th>Username</th> <th>Name</th> <th>E-Mail</th> <th>Level</th> </tr>');

	$users = $userfile->read_kimb_all_teilpl( 'userids' );
	
	foreach( $users as $id ){
		$user = $userfile->read_kimb_id( $id );

		if( $user['permiss'] == 'more' ){
			$permiss = '<span class="ui-icon ui-icon-plus" title="Dieser User hat erhöhte Admin-Rechte."></span>';
		}
		else{
			$permiss = '<span class="ui-icon ui-icon-minus" title="Dieser User hat geringere Editor-Rechte."></span>';
		}

		$link = '<a title="User bearbeiten" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$user['user'].'">'.$user['user'].'</a>';

		$sitecontent->add_site_content('<tr> <td>'.$link.'</td> <td>'.$user['name'].'</td> <td>'.$user['mail'].'</td> <td>'.$permiss.'</td> </tr>');
	}

	$sitecontent->add_site_content('</table>');
}
else{
	//kasten und Schnellzugriff
}

$sitecontent->output_complete_site();
?>
