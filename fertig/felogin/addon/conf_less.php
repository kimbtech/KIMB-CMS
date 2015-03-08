<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS Add-on
//KIMB ContentManagementSystem
//WWW.KIMB-technologies.eu
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


defined('KIMB_Backend') or die('No clean Request');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=felogin';
$feconf = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$feuser = new KIMBdbf( 'addon/felogin__user.kimb'  );

$sitecontent->add_site_content('<hr /><h2>Frontendlogin User</h2>');

//Userlist & User erstellen, löschen, bearbeiten

if( isset( $_GET['del'] ) && is_numeric( $_GET['uid'] ) ){

	$user = $feuser->read_kimb_id( $_GET['uid'] , 'user' );

	if( !empty( $user ) ){
		
		$feuser->write_kimb_id( $_GET['uid'] , 'del' );
		$feuser->write_kimb_teilpl( 'userids' , $_GET['uid'] , 'del' );

		$sitecontent->echo_message( 'Users gelöscht!' );
	}
	else{
		$sitecontent->echo_error( 'Der User existiert nicht!' , 'unknown' );
	}


}
elseif( isset( $_GET['deakch'] ) && is_numeric( $_GET['uid'] ) ){

	$status = $feuser->read_kimb_id( $_GET['uid'] , 'status' );

	if( !empty( $status ) ){
		if( $status == 'on' ){
			$feuser->write_kimb_id( $_GET['uid'] , 'add' , 'status' , 'off' );
		}
		else{
			$feuser->write_kimb_id( $_GET['uid'] , 'add' , 'status' , 'on' );
		}
		$sitecontent->echo_message( 'Status eines Users verändert!' );
	}
	else{
		$sitecontent->echo_error( 'Der User existiert nicht!' , 'unknown' );
	}

}
elseif( isset( $_POST['gruppen'] ) ){

	$i = '1';
	while( $i <= $_POST['gruppen'] ){

		$gruppe = $feuser->read_kimb_id( $i , 'gruppe' );
		if( $gruppe != $_POST[$i] ){
			if( !empty( $_POST[$i] ) ){
				$feuser->write_kimb_id( $i , 'add' , 'gruppe' , $_POST[$i] );
				$sitecontent->echo_message( 'Gruppe eines Users verändert!' );
			}
		}		
		$i++;
	}

}

$sitecontent->add_html_header('<script>
var del = function( id ) {
	$( "#del-confirm" ).show( "fast" );
	$( "#del-confirm" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$addonurl.'&del&uid=" + id;
			return true;
		},
		Cancel: function() {
			$( this ).dialog( "close" );
			return false;
		}
	}
	});
}
</script>');

$sitecontent->add_site_content('<form method="post" action="'.$addonurl.'" >');

$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&amp;newuser#goto" target="_blank" ><span class="ui-icon ui-icon-plusthick" title="Einen neuen User erstellen. ( Sie werden dafür auf die Registrieren-Seite des Frontends weitergeleitet. )"></span></a>');
$sitecontent->add_site_content('<table width="100%"><tr> <th>Username</th> <th>Name</th> <th>Gruppe</th> <th>Status</th> <th>Löschen</th> </tr>');

$users = $feuser->read_kimb_all_teilpl( 'userids' );

$liste = 'no';

$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );
foreach( $gruppen as $gr ){
	$grdown .= '<option value="'.$gr.'" >'.$gr.'</option>';
}

foreach( $users as $id ){
	$user = $feuser->read_kimb_id( $id );

	$link = '<a title="User bearbeiten ( Sie werden dafür auf die Einstellungen-Seite des Frontends weitergeleitet. )" href="'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&amp;settings='.$user['user'].'#goto" target="_blank" >'.$user['user'].'</a>';

	if ( $user['status'] == 'on' ){
		$status = '<a href="'.$addonurl.'&amp;deakch&amp;uid='.$id.'"><span class="ui-icon ui-icon-check" title="Dieser User ist zu Zeit aktiviert. ( click -> ändern )"></span></a>';
	}
	else{
		$status = '<a href="'.$addonurl.'&amp;deakch&amp;uid='.$id.'"><span class="ui-icon ui-icon-close" title="Dieser User ist zu Zeit deaktiviert. ( click -> ändern )"></span></a>';
	}

	$del = '<span onclick="var delet = del( '.$id.' ); delet();"><span class="ui-icon ui-icon-trash" title="Diesen User löschen."></span></span>';

	$grupp = '<select name="'.$id.'">'.$grdown.'</select>';
	$js .= '$( "[name='.$id.']" ).val( "'.$user['gruppe'].'" );';

	$sitecontent->add_site_content('<tr> <td>'.$link.'</td> <td>'.$user['name'].'</td> <td>'.$grupp.'</td> <td>'.$status.'</td> <td>'.$del.'</td> </tr>');

	$liste = 'yes';
}

$sitecontent->add_site_content('</table>');

$sitecontent->add_site_content('<input type="hidden" name="gruppen" value="'.$id.'" ><input type="submit" value="Gruppen anpassen" title="Speichern von Veränderungen an der Gruppenzugehörigkeit." ></form>');
$sitecontent->add_site_content('<button onclick="window.location = \''.$addonurl.'\';" >Ansicht aktualisieren</button>');

$sitecontent->add_html_header('<script>$(function(){ '.$js.' }); </script>');

if( $liste == 'no' ){
	$sitecontent->echo_error( 'Es sind keine Frontenduser vorhanden!' , 'unknown' );
}

$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie den User wirklich löschen?</p></div></div>');

?>
