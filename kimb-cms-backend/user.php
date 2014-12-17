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

	//verarbeiten und zu edit && jQuery Ajax Username und Passwortcheck!!

	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new" method="post" onsubmit="if( document.getElementById(\'passwort1\').value != document.getElementById(\'passwort2\').value ){ return false; } if( document.getElementById(\'passwort1\').value != \'\' ) { document.getElementById(\'passwort1\').value = SHA1( document.getElementById(\'passwort1\').value ); document.getElementById(\'passwort2\').value = \'\'; } else{ return false; }"><br />');
	$sitecontent->add_site_content('<input type="text" name="user" > <i title="Username für das Login ( keine Änderung möglich )">Username</i><br />');
	$sitecontent->add_site_content('<input type="text" name="name" > <i title="Name des Users" >Name</i><br />');
	$sitecontent->add_site_content('<input type="text" name="mail" > <i title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse</i><br />');
	$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more">Admin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
	$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onchange=" checkpw(); "> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort - keine Änderung</i> <br />');
	$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onchange=" checkpw(); "> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort - keine Änderung</i> <br />');
	$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
	$sitecontent->add_site_content('</form>');
}
elseif( $_GET['todo'] == 'edit' && isset( $_GET['user'] ) ){

	if( isset( $_GET['del'] ) && $_SESSION['permission'] == 'more' ){

		$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );		
		if( $id != false ){
			$userfile->write_kimb_teilpl( 'userids' , $id , 'del' );
			$userfile->write_kimb_id( $id , 'del' );
		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}

		$sitecontent->echo_message( 'Der User wurde gelöscht!<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=list"><button>Zurück zur Liste</button></a>' );
	}
	else{

		if( isset( $_POST['user'] ) ){
			$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );		
			if( $id != false ){
				$userinfo = $userfile->read_kimb_id( $id );
				if( $userinfo['passw'] != $_POST['passwort1'] && $_POST['passwort1'] != '' ){
					$userfile->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] );
					$sitecontent->echo_message( 'Das Passwort wurde geändert!' );
				}
				if( $userinfo['permiss'] != $_POST['level'] && $_SESSION['permission'] == 'more' ){
					$userfile->write_kimb_id( $id , 'add' , 'permiss' , $_POST['level'] );
					$sitecontent->echo_message( 'Das Nutzerlevel wurde geändert!' );
					$sitecontent->echo_message( '<b style="color:red;">Achtung, setzen Sie nicht alle User auf Editor, sonst können Sie den Systemzugriff verliehren!!</b>' );
				}
				if( $userinfo['name'] != $_POST['name'] ){
					$userfile->write_kimb_id( $id , 'add' , 'name' , $_POST['name'] );
					$sitecontent->echo_message( 'Der Name wurde geändert!' );
				}
				if( $userinfo['mail'] != $_POST['mail'] ){
					$userfile->write_kimb_id( $id , 'add' , 'mail' , $_POST['mail'] );
					$sitecontent->echo_message( 'Die E-Mail Adresse wurde geändert!' );
				}
				$sitecontent->echo_message( 'Achtung, einige Änderungen werden erst ab erneutem Login wirksam!' );
			}
			else{
				$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
			}
		}

		$sitecontent->add_html_header('<script>
		function deluser() {
			$( "#del-confirm" ).show( "fast" );
			$( "#del-confirm" ).dialog({
			resizable: false,
			height:220,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&user='.$_GET['user'].'&del";
					return true;
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
			});
		}
		function checkpw() {
			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();

			if( valzwei != valeins ){
				$("i#pwtext").text("Passwörter stimmen nicht überein!");
				$("i#pwtext").css( "background-color", "red" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
			else{
				$("i#pwtext").text("Passwörter - OK");
				$("i#pwtext").css( "background-color", "green" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
		}	
		</script>');

		$id = $userfile->search_kimb_xxxid( $_GET['user'] , 'user' );
		$user = $userfile->read_kimb_id( $id );

		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_GET['user'].'" method="post" onsubmit="if( document.getElementById(\'passwort1\').value != document.getElementById(\'passwort2\').value ){ return false; } if( document.getElementById(\'passwort1\').value != \'\' ) { document.getElementById(\'passwort1\').value = SHA1( document.getElementById(\'passwort1\').value ); document.getElementById(\'passwort2\').value = \'\'; }"><br />');
		$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i title="Username für das Login ( keine Änderung möglich )">Username</i><br />');
		$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i title="Name des Users" >Name</i><br />');
		$sitecontent->add_site_content('<input type="text" name="mail" value="'.$user['mail'].'"> <i title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse</i><br />');
		if( $_SESSION['permission'] == 'more' ){
			if( $user['permiss'] == 'less' ){
				$sitecontent->add_site_content('<input type="radio" name="level" value="less" checked="checked">Editor <input type="radio" name="level" value="more">Admin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
			}
			elseif(  $user['permiss'] == 'more'  ){
				$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more" checked="checked">Admin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
			}
		}
		$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onchange=" checkpw(); "> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort - keine Änderung</i> <br />');
		$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onchange=" checkpw(); "> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort - keine Änderung</i> <br />');
		$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
		$sitecontent->add_site_content('</form>');

		if( $_SESSION['permission'] == 'more' ){
			$sitecontent->add_site_content('<br /><span onclick=" deluser(); "><span class="ui-icon ui-icon-trash" title="Diesen User löschen."></span></span></a>');
		}

		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span>Möchten Sie den User "'.$_GET['user'].'" wirklich löschen?<br /><b>Sollten Sie alle User löschen verliehren Sie den Systemzugriff!</b></p></div></div>');
	}

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
