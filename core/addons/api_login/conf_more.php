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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=api_login';

$sitecontent->add_site_content( '<hr /><br /><h2>API Login</h2>' );
$sitecontent->add_site_content( 'Generieren Sie hier einen Datei die es ermöglicht den Login des CMS auch auf anderen Seiten zu nutzen.<br />' );
$sitecontent->add_site_content( '<b>Die Systeme (CMS und zu sichernde Seite) müssen hier <u>nicht</u> auf dem gleichen Server und der gleichen Domain liegen.</b><br />' );

$file = new KIMBdbf( 'addon/api_login__dest.kimb' );

if( !function_exists('curl_version') ){
	$sitecontent->echo_error( 'Diese Funktion benötigt CURL!' );
}
else{
	if( is_numeric( $_GET['del'] ) ){

		$read = $file->read_kimb_one( 'dest'.$_GET['del'] );
		if( !empty( $read ) ){
			$file->write_kimb_replace( 'dest'.$_GET['del'], 'none' );
			$sitecontent->echo_message( 'Das externe System "'.$_GET['del'].'" wurde gelöscht!' );
		}

	}
	elseif( is_numeric( $_GET['file'] ) ){

		$dat = file_get_contents( __DIR__.'/example_dest.php' );

		$auth = $file->read_kimb_one( 'auth' );
		$dest = $file->read_kimb_one( 'dest'.$_GET['file'] );
		$dest = substr( $dest , '0', '-'.strlen(strrchr( $dest , '/')));

		$search = array( '<<[[auth]]>>', '<<[[sysurl]]>>' );
		$replace = array( "'".$auth."'", "'".$dest."'" );

		header("Content-Type: application/force-download");
		header("Content-type: text/php");
		header('Content-Disposition: attachment; filename= api_login_ext.php');

		echo str_replace( $search, $replace, $dat );

		die;

	}
	elseif( isset( $_POST['url'] ) ){

		$i = 1;
		$fertig = 'no';
		while( $fertig != 'yes' ){	
			$read = $file->read_kimb_one( 'dest'.$i );
			if( empty( $read ) ){
				$file->write_kimb_new( 'dest'.$i, $_POST['url'] );
				$fertig = 'yes';
				$sitecontent->echo_message( 'Das externe System "'.$i.'" wurde hinzugefügt!' );
			}
			elseif( $read == 'none' ){
				$file->write_kimb_replace( 'dest'.$i, $_POST['url'] );
				$fertig = 'yes';
				$sitecontent->echo_message( 'Das externe System "'.$i.'" wurde hinzugefügt!' );
			}
			$i++;
		}

	}

	$sitecontent->add_html_header('<script>
	var del = function( id ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$addonurl.'&del=" + id;
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

	if( empty( $file->read_kimb_one( 'auth' ) ) ){
		$file->write_kimb_new( 'auth', makepassw( 100 , '_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' ) );
	}

	$sitecontent->add_site_content( '<h3>Liste von externen Systemen</h3>' );

	$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post">');
	$sitecontent->add_site_content('<table width="100%"><tr> <th>Link <span class="ui-icon ui-icon-info" title="Diesem Link müssen User zu den entsprechenden anderen Systemen folgen."></span></th> <th>Externes Ziel</th> <th width="20px;"></th> </tr>');

	$read = 'test';
	$i = 1;
	while( !empty( $read ) ){	
		$read = $file->read_kimb_one( 'dest'.$i );
		if( !empty( $read ) && $read != 'none' ){
			$del = '<span onclick="var delet = del( \''.$i.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses externe System löschen."></span></span>';
			$del .= '<a href="'.$addonurl.'&amp;file='.$i.'"><span class="ui-icon ui-icon-document" title="Die Datei für das externe System herunterladen."></span></a>';
			$sitecontent->add_site_content('<tr> <td>'.$allgsysconf['siteurl'].'/ajax.php?addon=api_login&amp;id='.$i.'</td> <td>'.$read.'</td> <td>'.$del.'</td> </tr>');
		}
		$i++;
		
	}
	$sitecontent->add_site_content('<tr> <td>Hinzufügen:</td> <td><input type="url" name="url" placeholder="Ziel URL"></td> <td><input type="submit" value="Neu"></td> </tr>');
	$sitecontent->add_site_content('</table></form>');

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie dieses externe System wirklich löschen?</p></div></div>');


	$sitecontent->add_site_content( '<br /><h3>Nutzung</h3>' );

	$sitecontent->add_site_content( '<ol>' );
	$sitecontent->add_site_content( '<li>Fügen Sie oben einen Link auf den externen Server, mit dem zu sichernden System, zu einer Datei "api_login_ext.php" hinzu.</li>' );
	$sitecontent->add_site_content( '<li>Laden Sie über das kleine Icon rechts in der Tabelle die Datei "api_login_ext.php" herunter.</li>' );
	$sitecontent->add_site_content( '<li>Packen Sie die Datei "api_login_ext.php" auf Ihren Server, so dass sie über den in 1. angegebenen Link zu erreichen ist.<br /><i>Die Datei muss die Rechte haben eine "inhalte.php" im gleichen Verzeichnis zu erstellen.</i></li>' );
	$sitecontent->add_site_content( '<li>Passen Sie evtl. die Variable "$sysurl" an.<br /><i>Unter dieser URL sollte sich das zu sichernde System befinden.</i></li>' );
	$sitecontent->add_site_content( '<li>Gehen Sie nach Nutzung -> api_login und sichern Sie Ihr System wie angegeben.</li>' );
	$sitecontent->add_site_content( '<li>Lassen Sie den oben angezeigten Link eingeloggten Usern des CMS anzeigen.</li>' );
	$sitecontent->add_site_content( '</ol>' );
}

?>
