<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS Add-on
//KIMB ContentManagementSystem
//KIMB-technologies.blogspot.com
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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=felogin';
$feconf = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$feuser = new KIMBdbf( 'addon/felogin__user.kimb'  );

$sitecontent->add_site_content('<hr /><h2>Userlogin Gruppen</h2>');

$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );

if( !empty( $_POST['newgr'] ) && !empty( $_POST['firstrequid'] ) ){

	$_POST['newgr'] = preg_replace( "/[^a-zA-Z0-9]/" , "" , $_POST['newgr'] );

	if( !in_array( $_POST['newgr'] , $gruppen ) ){
		$feconf->write_kimb_new( $_POST['newgr'] , $_POST['firstrequid'] );

		if( empty( $feconf->read_kimb_one( 'grlist' ) ) ){
			$feconf->write_kimb_new( 'grlist' , $_POST['newgr'] );
		}
		else{
			$new = $feconf->read_kimb_one( 'grlist' );
			$new .= ','.$_POST['newgr'];
			$feconf->write_kimb_replace( 'grlist' , $new );
		}
		$sitecontent->echo_message( 'Die Gruppe "'.$_POST['newgr'].'" wurde hinzugefügt!' );

		$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );
	}
	else{
		$sitecontent->echo_error( 'Die Gruppe existiert bereits!' , 'unknown' );
	}
}
elseif( isset( $_GET['gruppe'] ) && isset( $_GET['del'] )  ){
	$read = $feconf->read_kimb_one( $_GET['gruppe'] );
	if( !empty( $read ) ){

		foreach( $gruppen as $gr ){
			if( $gr != $_GET['gruppe'] ){
				$newgrlist .= ','.$gr;
			}
		}

		$newgrlist = substr( $newgrlist , 1 );

		$feconf->write_kimb_delete( $_GET['gruppe'] );

		if( empty( $newgrlist ) ){
			$feconf->write_kimb_delete( 'grlist' );
		}
		else{
			$feconf->write_kimb_replace( 'grlist' , $newgrlist );
		}		

		$sitecontent->echo_message( 'Die Gruppe "'.$_GET['gruppe'].'" wurde gelöscht!' );

		$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );
	}
}

if( isset( $_GET['gruppe'] ) && !isset( $_GET['del'] ) ){

	$sitecontent->add_site_content('<b>Gruppe "'.$_GET['gruppe'].'"</b>');

	$read = $feconf->read_kimb_one( $_GET['gruppe'] );

	if( !empty( $read ) ){

		if( isset( $_POST['change'] ) ){
			
			$i = 1;
			while( 5 == 5 ){
				if( isset( $_POST['siteids'.$i] ) ){
					$sitelist .= ','.$_POST['siteids'.$i];
				}
				if( $i >= $_POST['change'] ){
					break;
				}
				$i++;
			}
			$sitelist = substr( $sitelist , 1 );

			$feconf->write_kimb_replace( $_GET['gruppe'] , $sitelist );

			$sitecontent->echo_message( 'Die Seiten wurden verändert!' );	
		}

		$read = $feconf->read_kimb_one( $_GET['gruppe'] );
		$sites = explode( ',' , $read );

		$sitecontent->add_site_content('<form method="post" action="'.$addonurl.'&amp;gruppe='.$_GET['gruppe'].'">');

		$i = '1';
		foreach( $sites as $site ){
			$bef .= '$( "[name=siteids'.$i.']" ).val( '.$site.' );';
			$sitecontent->add_site_content( id_dropdown( 'siteids'.$i, 'siteid' ) );
			$sitecontent->add_site_content( '<span class="ui-icon ui-icon-trash" style="display:inline-block;" onclick="$( \'[name=siteids'.$i.']\' ).val( \'none\' );" title="Diese Seite entfernen."></span><br />' );
			$i++;
		}

		$bef .= '$( "[name=siteids'.$i.']" ).val( "none" );';
		$sitecontent->add_site_content( id_dropdown( 'siteids'.$i, 'siteid' ) .' <b title="Eine Seite hinzufügen!">*</b><br />' );
		$sitecontent->add_site_content('<input type="hidden" name="change" value="'.$i.'" ><input type="submit" value="Ändern"></form>');

		$sitecontent->add_html_header('<script>$(function(){ '.$bef.' }); </script>');
	}
	else{
		$sitecontent->echo_error( 'Die Gruppe existiert nicht!' , 'unknown' );
	}

	$sitecontent->add_site_content('<br /><br /><a href="'.$addonurl.'" ><button>Zurück</button></a>');
}
else{
	$sitecontent->add_html_header('<script>
	var del = function( gruppe ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$addonurl.'&del&gruppe=" + gruppe;
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

	$sitecontent->add_site_content('<table width="100%"><tr> <th width="75px;">Gruppenname</th> <th>Seiten <span class="ui-icon ui-icon-info" title="Entsprechen der SiteID ( Seiten -> Auflisten )!"></span></th> <th width="20px;">Löschen</th> </tr>');

	foreach( $gruppen as $gr ){
		$read = $feconf->read_kimb_one( $gr );
		if( $read != '' ){
			$del = '<span onclick="var delet = del( \''.$gr.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Diese Gruppe löschen."></span></span>';
			$sitecontent->add_site_content('<tr> <td><a title="Seiten ändern" href="'.$addonurl.'&amp;gruppe='.$gr.'">'.$gr.'</a></td> <td>'.$read.'</td> <td>'.$del.'</td> </tr>');
		}
	}
	$sitecontent->add_site_content('<tr> <td><form method="post" action="'.$addonurl.'"><input type="text" name="newgr" placeholder="hinzufügen" ></td> <td>'.id_dropdown( 'firstrequid', 'siteid' ).'</td> <td><input type="submit" value="Los" title="Eine neue Gruppe erstellen!" ></form></td> </tr>');
	$sitecontent->add_site_content('</table>');

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie die Usergruppe wirklich löschen?</p></div></div>');
}

$sitecontent->add_site_content('<hr /><h2>Userlogin Einstellungen</h2>');

if( empty( $feconf->read_kimb_one( 'loginokay' ) ) ){
	$loginokay = makepassw( '75' , '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );
	$feconf->write_kimb_new( 'loginokay' , $loginokay );
}

if( !empty( $_POST['selfoo'] ) && !empty( $_POST['areaoo'] ) && !empty( $_POST['infomailoo'] ) && !empty( $_POST['selfgr'] ) && !empty( $_POST['akzep'] ) && !empty( $_POST['requid'] ) ){

	$arrays[] = array( 'teil' => 'selfoo' , 'trenner' => 'selfreg' );
	$arrays[] = array( 'teil' => 'areaoo' , 'trenner' => 'addonarea' );
	$arrays[] = array( 'teil' => 'infomailoo' , 'trenner' => 'infomail' );

	foreach( $arrays as $array ){
		$teil = $array['teil'];
		$trenner = $array['trenner'];

		if( $_POST[$teil] == 'on' || $_POST[$teil] == 'off' ){
			$wert = $feconf->read_kimb_one( $trenner );
			if( $wert != $_POST[$teil] ){
				if( empty( $wert ) ){
					$feconf->write_kimb_new( $trenner , $_POST[$teil] );
				}
				else{
					$feconf->write_kimb_replace( $trenner , $_POST[$teil] );
				}
				$sitecontent->echo_message( '"'.$trenner.'" wurde auf "'.$_POST[$teil].'" gesetzt!' );
			}
		}
	}

	$arrays[] = array( 'teil' => 'selfgr' , 'trenner' => 'selfreggruppe' );
	$arrays[] = array( 'teil' => 'akzep' , 'trenner' => 'akzepttext' );
	$arrays[] = array( 'teil' => 'requid' , 'trenner' => 'requid' );

	foreach( $arrays as $array ){
		$teil = $array['teil'];
		$trenner = $array['trenner'];

		$wert = $feconf->read_kimb_one( $trenner );
		if( $wert != $_POST[$teil] ){
			if( empty( $wert ) ){
				$feconf->write_kimb_new( $trenner , $_POST[$teil] );
			}
			else{
				$feconf->write_kimb_replace( $trenner , $_POST[$teil] );
			}
			$sitecontent->echo_message( '"'.$trenner.'" wurde auf verändert!' );
		}
	}

}
if( !empty( $_POST['selfoo'] ) && !empty( $_POST['areaoo'] ) && empty( $_POST['selfgr'] ) ){
	$sitecontent->echo_error( 'Bitte erstellen Sie zuerst eine Gruppe und/oder füllen Sie alle Felder' );
}

$oo = array( ' ' , ' ' , ' ' , ' ' , ' ' , ' ' , ' ' );

if( $feconf->read_kimb_one( 'selfreg' ) == 'off' ){
	$oo[1] = ' checked="checked" ';
}
else{
	$oo[2] = ' checked="checked" ';
}
if( $feconf->read_kimb_one( 'addonarea' ) == 'off' ){
	$oo[3] = ' checked="checked" ';
}
else{
	$oo[4] = ' checked="checked" ';
}
if( $feconf->read_kimb_one( 'infomail' ) == 'off' ){
	$oo[5] = ' checked="checked" ';
}
else{
	$oo[6] = ' checked="checked" ';
}

$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

$sitecontent->add_site_content('<input name="selfoo" type="radio" value="off" '.$oo[1].'><span style="display:inline-block;" title="Keine Möglichkeit zum selbstständigen Registrieren anzeigen." class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="selfoo" value="on" type="radio" '.$oo[2].'><span style="display:inline-block;" title="Selbstständiges Registrieren ermöglichen." class="ui-icon ui-icon-check"></span><br />');

$sitecontent->add_site_content('<input name="areaoo" type="radio" value="off" '.$oo[3].'><span style="display:inline-block;" title="Das Loginformular nur auf einer Seite zeigen." class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="areaoo" value="on" type="radio" '.$oo[4].'><span style="display:inline-block;" title="Das Loginformular überall in einer Addonarea anzeigen." class="ui-icon ui-icon-check"></span><br />');

$sitecontent->add_site_content('<input name="infomailoo" type="radio" value="off" '.$oo[5].'><span style="display:inline-block;" title="Es wird keine E-Mail versendet, wenn sich ein neuer User registriert." class="ui-icon ui-icon-closethick"></span>');
$sitecontent->add_site_content('<input name="infomailoo" value="on" type="radio" '.$oo[6].'><span style="display:inline-block;" title="Wenn sich ein neuer User registriert, wird eine E-Mail an die Adresse des Administrators in der Konfiguration gesendet" class="ui-icon ui-icon-check"></span><br />');

foreach( $gruppen as $gr ){
	if( $gr == $feconf->read_kimb_one( 'selfreggruppe' ) ){
		$grdown .= '<option value="'.$gr.'" selected="selected" >'.$gr.'</option>';
	}
	else{
		$grdown .= '<option value="'.$gr.'" >'.$gr.'</option>';
	}
}

$sitecontent->add_site_content('<select name="selfgr">'.$grdown.'</select><span style="display:inline-block;" title="Bitte geben Sie an, zu welcher Gruppe selbstständig registrierte User hinzugefügt werden sollen." class="ui-icon ui-icon-info"></span><br />');
$sitecontent->add_site_content('<textarea name="akzep" style="width:200px; height:75px;">'.$feconf->read_kimb_one( 'akzepttext' ).'</textarea><span style="display:inline-block;" title="Dieser Text muss beim selbstständigen Registrieren eines Accounts angeklickt werden." class="ui-icon ui-icon-info"></span><br />');
$sitecontent->add_html_header('<script>$(function(){ $( "[name=requid]" ).val( '.$feconf->read_kimb_one( 'requid' ).' ); }); </script>');
$sitecontent->add_site_content( id_dropdown( 'requid', 'requid' ).' <span style="display:inline-block;" title="Bitte wählen Sie hier eine Seite, auf welcher alles rund um das Login angezeigt werden soll." class="ui-icon ui-icon-info"></span><br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern"><span style="display:inline-block;" title="Sie müssen alle Felder füllen!" class="ui-icon ui-icon-info"></span></form>');

?>
