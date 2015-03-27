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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie';

$sitecontent->add_site_content('<hr /><br /><h2>Bildergalerie</h2>');

if ( !extension_loaded( 'gd' ) || !function_exists( 'gd_info' ) ) {
	$sitecontent->echo_error( '<b>Auf Ihrem Server fehlt PHP GD!</b><br />Dies ist notwendig für das Add-on Galerie!' );
}

$cfile = new KIMBdbf( 'addon/galerie__conf.kimb' );

$_SESSION['secured'] = 'off';

if( is_numeric( $_GET['id'] ) ){
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Übersicht</a>');

	$sitecontent->add_site_content('<h3>Bearbeiten</h3>');

	$sitecontent->add_html_header('<script>
		function del() {
			$( "#del-confirm" ).show( "fast" );
			$( "#del-confirm" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					window.location = "'.$addonurl.'&id='.$_GET['id'].'&del";
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


	if( isset( $_POST['imgpath'] ) ){
		
		$id = $_GET['id'];

		$alls = array( 'siteid', 'rand', 'size', 'anz', 'pos' );

		foreach( $alls as $all ){
			if( $cfile->read_kimb_id( $id , $all ) != $_POST[$all] ){
				$cfile->write_kimb_id( $id , 'add' , $all , $_POST[$all] );

				$sitecontent->echo_message( 'Der Parameter "'.$all.'" wurde geändert!' );
			}
		}

	}
	elseif( isset( $_GET['del'] ) ){
		
		$path = $cfile->read_kimb_id( $_GET['id'] , 'imgpath' );

		if( !empty( $path ) ){
		
			$cfile->write_kimb_id( $_GET['id'] , 'del' );
			$cfile->write_kimb_teilpl( 'galid' , $_GET['id'] , 'del' );

			$sitecontent->echo_message( 'Galerie gelöscht!' );
		}

		open_url( '/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie' );
		die;
	}

	$all = $cfile->read_kimb_id( $_GET['id'] );

	$sitecontent->add_site_content('<form method="post" action="'.$addonurl.'&amp;id='.$_GET['id'].'">');

	$sitecontent->add_html_header('<script>$(function(){ $( "[name=siteid]" ).val( '.$all['siteid'].' ); }); </script>');
	$sitecontent->add_site_content( id_dropdown( 'siteid', 'siteid' ).' ( SiteID <b title="Bitte wählen Sie die Seite, auf welcher die Galerie angezeigt werden soll.">*</b> )<br />');

	$sitecontent->add_site_content('<input type="text" value="'.$all['imgpath'].'" name="imgpath" readonly="readonly"> ( Bildpfad <b title="Bitte wählen Sie einen Pfad unter Others -> Filemanager. Dieser lässt sich später nicht ändern! ( Bitte laden Sie mit dem Filmanager alle ihre Dateien in diesen Pfad )">*</b> )');
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?action=rein&amp;path='.$all['imgpath'].'" target="_blank" >Zum Filemanager</a><br />');


	if( $all['rand'] == 'on' ){
		$rand[1] = ' ';
		$rand[2] = ' checked="checked"';
	}
	else{
		$rand[2] = ' ';
		$rand[1] = ' checked="checked"';
	}
	$sitecontent->add_site_content('<input type="radio" name="rand" value="off"'.$rand[1].'> <span style="display:inline-block;" title="Bilderreihenfolge zufällig." class="ui-icon ui-icon-closethick"></span>');
	$sitecontent->add_site_content('<input type="radio" name="rand" value="on"'.$rand[2].'> <span style="display:inline-block;" title="Bilderreihenfolge nach Dateireihenfolge." class="ui-icon ui-icon-check"></span> (Zufall)<br />');

	$sitecontent->add_site_content('<input type="number" name="size" value="'.$all['size'].'"> ( Größe <b title="Bitte geben Sie eine maximale Pixelzahl für die Vorschau ein.">*</b> )<br />');

	$sitecontent->add_site_content('<input type="number" name="anz" value="'.$all['anz'].'"> ( Anzahl <b title="Bitte geben Sie eine maximale Anzahl an Bilder ein. ( Leer => 99999 )">*</b> )<br />');

	$sitecontent->add_html_header('<script>$(function(){ $( "[name=pos]" ).val( \''.$all['pos'].'\' ); }); </script>');
	$sitecontent->add_site_content('<select name="pos"><option value="top">Oben</option><option value="bottom">Unten</option><option value="none">Manuell</option></select>( Platzierung <b title="Soll die Galerie oben oder unten auf der Seite sein oder platzieren Sie die Galerie per HTML-Code ( siehe unten ).">*</b> )<br />');

	$sitecontent->add_site_content('<input type="submit" value="Anpassen"></form>');
	$sitecontent->add_site_content('<span onclick="del();"><span class="ui-icon ui-icon-trash" title="Diese Galerie löschen."></span></span>');

	$sitecontent->add_site_content( '<hr /><b>HTML Code für manuelle Platzierung:</b><br /><i>'.htmlspecialchars( '<div class="imggallerydisplayhere" style="background-color:#ddd; border-radius:15px;" >Bitte aktivieren Sie für die Bildergalerie JavaScript!</div>' ).'</i>' );

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Galerie wirklich löschen?</p></div></div>');

}
elseif( !empty( $_GET['path'] ) ){

	//new

	$id = $cfile->next_kimb_id();

	$cfile->write_kimb_teilpl( 'galid' , $id , 'add' );

	$cfile->write_kimb_id( $id , 'add' , 'siteid' , '0' );
	$cfile->write_kimb_id( $id , 'add' , 'imgpath' , $_GET['path'] );
	$cfile->write_kimb_id( $id , 'add' , 'rand' , 'off' );
	$cfile->write_kimb_id( $id , 'add' , 'size' , '200' );
	$cfile->write_kimb_id( $id , 'add' , 'anz' , '5' );
	$cfile->write_kimb_id( $id , 'add' , 'pos' , 'top' );

	open_url( '/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie&id='.$id );
	die;
}
else{
	$sitecontent->add_site_content('<h3>Liste</h3>');
	$sitecontent->add_site_content( '<span class="ui-icon ui-icon-info" title="Hier können Sie Einstellungen Ihrer Bildergalerien bearbeiten. In der Liste werden die SiteIDs ( Seiten -> Auflisten ) angezeigt."></span>' );
	$sitecontent->add_site_content( '<table width="100%">' );
	$sitecontent->add_site_content( '<tr> <th>SiteID</th> <th>Bildpfad</th> <th>Position</th> </tr>' );

	foreach( $cfile->read_kimb_all_teilpl( 'galid' ) as $id ){
		$all = $cfile->read_kimb_id( $id );

		$sitecontent->add_site_content('<tr> <td><a href="'.$addonurl.'&amp;id='.$id.'" title="Bearbeiten">'.$all['siteid'].'</a></td> <td>'.$all['imgpath'].'</td> <td>'.$all['pos'].'</td> <tr>');

		$liste = 'yes';
	}
	$sitecontent->add_site_content( '</table>' );

	if( $liste != 'yes' ){
		$sitecontent->echo_error( 'Keine Galerieseiten!!<br />Bitte fügen Sie eine neue über den Filemanager hinzu.' );
	}
}

?>
