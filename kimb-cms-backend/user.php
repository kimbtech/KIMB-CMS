<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS
//KIMB ContentManagementSystem
//www.KIMB-technologies.eu
/*************************************************/
//CC BY-ND 4.0
//http://creativecommons.org/licenses/by-nd/4.0/
//http://creativecommons.org/licenses/by-nd/4.0/legalcode
/*************************************************/
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
//BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
//IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
//IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
/*************************************************/



define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

$checkerneut = 'yes';
if( $_GET['user'] == $_SESSION['user'] && $_GET['todo'] == 'edit' ){
	check_backend_login( 'eight' );
	$checkerneut = 'no';
}

//BE-User erstellen, bearbeiten

$userfile = new KIMBdbf('backend/users/list.kimb');
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if( $_GET['todo'] == 'new' ){
	check_backend_login( 'nine' , 'more' );

	$sitecontent->add_site_content('<h2>User erstellen</h2>');

	if( isset( $_POST['user'] ) ){

		$passwort = $_POST['passwort1'];
		$name = $_POST['name'];
		
		if( $name == '' ){
			$name = 'Max Heiner Mustermann';
		}

		$_POST['user'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_POST['user'] ) );
		$id = $userfile->search_kimb_xxxid( $_POST['user'] , 'user' );
		if( $id == false ){
			$username = $_POST['user'];
		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}

		if( $_POST['level'] == 'more' || $_POST['level'] == 'less' ){
			$permiss = $_POST['level'];
		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}

		if( preg_match( "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/" , $_POST['mail'] ) ) {
			$mail = $_POST['mail'];
		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
			$sitecontent->output_complete_site();
			die;
		}

		$id = $userfile->next_kimb_id();

		$userfile->write_kimb_teilpl( 'userids' , $id , 'add' );

		$userfile->write_kimb_id( $id , 'add' , 'passw' , $passwort );
		$userfile->write_kimb_id( $id , 'add' , 'permiss' , $permiss );
		$userfile->write_kimb_id( $id , 'add' , 'name' , $name );
		$userfile->write_kimb_id( $id , 'add' , 'mail' , $mail );
		$userfile->write_kimb_id( $id , 'add' , 'user' , $username );
		

		open_url( '/kimb-cms-backend/user.php?todo=edit&user='.$username );
		die;
	}

	$sitecontent->add_html_header('<script>
	function checkuser(){
		var userinput = $( "input#user" ).val();
		if( "" != userinput ){

			$( "input#check" ).val( "nok" );
			$("i#textuser").text("Username -- Überprüfung läuft");
			$("i#textuser").css( "background-color", "orange" );
			$("i#textuser").css( "color", "white" );
			$("i#textuser").css( "padding", "5px" );

			$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=user.php&user=" + userinput , function( data ) {
				$( "input#check" ).val( data );
				if( data == "nok" ){
					$("i#textuser").text("Username - Achtung, dieser Name ist schon vergeben!!");
					$("i#textuser").css( "background-color", "red" );
				}
				else{
					$( "input#check" ).val( "ok" );
					$("i#textuser").text("Username - OK");
					$("i#textuser").css( "background-color", "green" );
				}
			});
		}
		else{
			$( "input#check" ).val( "ok" );
			$("i#pfadtext").text("(Menuepfad -- OK)");
			$("i#pfadtext").css( "background-color", "green" );
		}
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
	function checkmail(){
		var valmail = $( "input#mail" ).val();
		var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

		if( mailmatch.test( valmail ) ){
			$("i#mailadr").text( "E-Mail Adresse - OK" );
			$("i#mailadr").css( "background-color", "green" );
			$("i#mailadr").css( "color", "white" );
			$("i#mailadr").css( "padding", "5px" );
			
		}
		else{
			$("i#mailadr").text( "Die E-Mail Adresse ist fehlerhaft!" );
			$("i#mailadr").css( "background-color", "red" );
			$("i#mailadr").css( "color", "white" );
			$("i#mailadr").css( "padding", "5px" );
		}
	}

	function checksumbit(){

		var valeins = $( "input#passwort1" ).val();
		var valzwei = $( "input#passwort2" ).val();
		var valmail = $( "input#mail" ).val();

		var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		if( mailmatch.test( valmail ) == false ){
			return false;
		}

		if( valzwei != valeins ){ 
			return false;
		}
		if( valeins != \'\' ) {
			$( "input#passwort1" ).val( SHA1( valeins ) );
			$( "input#passwort2" ).val( \'\' );
			return true;
		}
		else{
			return false;
		}

	}
	</script>');

	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new" method="post" onsubmit="return checksumbit();"><br />');
	$sitecontent->add_site_content('<input type="text" name="user" onchange=" checkuser(); " id="user"> <i id="textuser" title="Username für das Login ( keine Änderung möglich )">Username - bitte eingeben</i><br />');
	$sitecontent->add_site_content('<input type="text" name="name" > <i title="Name des Users" >Name</i><br />');
	$sitecontent->add_site_content('<input type="text" name="mail" id="mail" onchange=" checkmail(); " > <i id="mailadr" title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse - bitte eingeben</i><br />');
	$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onchange=" checkpw(); "> <i title="Lassen Sie das Feld leer um das Passwort unverändert zu lassen!" id="pwtext">Passwort - bitte eingeben</i> <br />');
	$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onchange=" checkpw(); "> <i title="Zur Sicherheit erneut eigeben." id="pwtext">Passwort - bitte eingeben</i> <br />');
	$sitecontent->add_site_content('<input type="radio" name="level" value="less" checked="checked">Editor <input type="radio" name="level" value="more">Admin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
	$sitecontent->add_site_content('<input type="hidden" value="nok" id="check" >');
	$sitecontent->add_site_content('<input type="submit" value="Erstellen" ><br />');
	$sitecontent->add_site_content('</form>');
}
elseif( $_GET['todo'] == 'edit' && isset( $_GET['user'] ) ){
	if( $checkerneut != 'no' ){
		check_backend_login( 'ten' , 'more' );
	}

	$sitecontent->add_site_content('<h2>User bearbeiten</h2>');

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
		if( $id != false ){
			$user = $userfile->read_kimb_id( $id );

			$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_GET['user'].'" method="post" onsubmit="if( document.getElementById(\'passwort1\').value != document.getElementById(\'passwort2\').value ){ return false; } if( document.getElementById(\'passwort1\').value != \'\' ) { document.getElementById(\'passwort1\').value = SHA1( document.getElementById(\'passwort1\').value ); document.getElementById(\'passwort2\').value = \'\'; }"><br />');
			$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i title="Username für das Login ( keine Änderung möglich )">Username</i><br />');
			$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i title="Name des Users" >Name</i><br />');
			$sitecontent->add_site_content('<input type="text" name="mail" value="'.$user['mail'].'"> <i title="E-Mail Adresse des Users für Nachrichten und Meldungen">E-Mail Adresse</i><br />');
			if( $_SESSION['permission'] == 'more' ){

				if( !is_object( $levellist ) ){
					$levellist = new KIMBdbf( 'backend/users/level.kimb' );
				}

				$levs = $levellist->read_kimb_one( 'levellist' );
				if( $levs != '' ){
					$levs = explode( ',' , $levs );

					$other = '<b style="background-color:gray;" title="Systemspezifische Userlevel">';

					foreach( $levs as $name ){
						if( $user['permiss'] == $name ){
							$other .= '<input type="radio" name="level" value="'.$name.'" checked="checked" >'.$name.' ';
						}
						else{
							$other .= '<input type="radio" name="level" value="'.$name.'">'.$name.' ';
						}
					}
					$other .= '</b>';
				}

				if( $user['permiss'] == 'less' ){
					$sitecontent->add_site_content('<input type="radio" name="level" value="less" checked="checked">Editor <input type="radio" name="level" value="more">Admin '.$other.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
				}
				elseif(  $user['permiss'] == 'more'  ){
					$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more" checked="checked">Admin '.$other.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
				}
				else{
					$sitecontent->add_site_content('<input type="radio" name="level" value="less">Editor <input type="radio" name="level" value="more" >Admin '.$other.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i title="Das Rechte-Level des Users einstellen.">Level</i><br />');
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
		else{
			$sitecontent->echo_error( 'Der User existiert nicht!' , 'unknown');
		}
	}

}
elseif( $_GET['todo'] == 'list'){
	check_backend_login( 'ten' , 'more' );

	$sitecontent->add_site_content('<h2>Userliste</h2>');

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
	check_backend_login( 'eight' , 'more' );

	$sitecontent->add_site_content('<h2>User</h2>');

	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new">Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Einen neuen User erstellen.</i></span></a>');
	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=list">Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle User zum Bearbeiten und Löschen auflisten.</i></span></a>');

	$sitecontent->add_site_content('<hr /><u>Schnellzugriffe:</u><br /><br />');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php" method="get"><input type="text" name="user" placeholder="Username"><input type="hidden" value="edit" name="todo"><input type="submit" value="Los"> <span title="Geben Sie Usernamen ein und bearbeiten Sie ihn sofort!">User bearbeiten</span></form>');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php" method="get"><input type="text" name="user" placeholder="Username"><input type="hidden" value="edit" name="todo"><input type="hidden" name="del"><input type="submit" value="Los"> <span title="Geben Sie einen Usernamen ein und löschen Sie ihn sofort!">User löschen</span></form>');
}

$sitecontent->output_complete_site();
?>
