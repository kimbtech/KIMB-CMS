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

check_backend_login( 'eightteen' , 'more' );

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//Themes

if( isset( $_FILES['userfile']['name'] ) ){

	$zip = new ZipArchive;
	if ($zip->open($_FILES["userfile"]["tmp_name"]) === TRUE) {
	    $zip->extractTo( __DIR__.'/../load/system/theme/' );
	    $zip->close();
	}
	else{
		$sitecontent->echo_error( 'Die Installation schlug fehl!' , 'unknown');
		$sitecontent->output_complete_site();
		die;
	}

	$name = file_get_contents( __DIR__.'/../load/system/theme/name.info' );
	$name = preg_replace("/[^a-z_]/","", $name);
	unlink( __DIR__.'/../load/system/theme/name.info' );

	if( file_exists( __DIR__.'/../load/system/theme/output_menue_'.$name.'.php' ) && file_exists( __DIR__.'/../load/system/theme/output_site_'.$name.'.php' ) ){
		rename ( __DIR__.'/../load/system/theme/output_menue_'.$name.'.php' , __DIR__.'/../core/theme/output_menue_'.$name.'.php' );
		rename ( __DIR__.'/../load/system/theme/output_site_'.$name.'.php' , __DIR__.'/../core/theme/output_site_'.$name.'.php' );

		$sitecontent->echo_message( 'Das Theme "'.$name.'" wurde installiert!' );
	}
	else{
		$sitecontent->echo_error( 'Die Installation schlug fehl!' , 'unknown');
	}
}
if( isset( $_GET['del'] ) ){
	$_GET['del'] = preg_replace( "/[^a-z_]/" , "" , $_GET['del']);	

	if( file_exists( __DIR__.'/../core/theme/output_menue_'.$_GET['del'].'.php' ) ){
		unlink( __DIR__.'/../core/theme/output_menue_'.$_GET['del'].'.php' );
	}
	if( file_exists( __DIR__.'/../core/theme/output_site_'.$_GET['del'].'.php' ) ){
		unlink( __DIR__.'/../core/theme/output_site_'.$_GET['del'].'.php' );
	}

	$sitecontent->echo_message( 'Das Theme "'.$_GET['del'].'" wurde gelöscht!' );
}
if( isset( $_GET['chdeak'] ) && isset( $_GET['theme'] ) ){
	$_GET['theme'] = preg_replace( "/[^a-z_]/" , "" , $_GET['theme'] );
	if( $conffile->write_kimb_id( '001' , 'add' , 'theme' , $_GET['theme'] ) ){
		$sitecontent->echo_message( 'Das Theme "'.$_GET['theme'].'" wurde aktiviert!' );
		$allgsysconf = $conffile->read_kimb_id('001');
	}
}


$sitecontent->add_html_header('<script>
var del = function( theme ) {
	$( "#del-confirm" ).show( "fast" );
	$( "#del-confirm" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_themes.php?del=" + theme;
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

$sitecontent->add_site_content('<h2>Themesliste</h2>');
$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Achtung: Themes können sich gegenseitig beeinflussen, es müssen also nicht alle unten aufgeführten Themes funktionstüchtig sein, bitte installieren Sie ein Theme erneut, sollte es Probleme gibt!"></span>');
$sitecontent->add_site_content('<table width="100%"><tr> <th>Code</th> <th>Status</th> <th>Löschen</th> </tr>');

$dir = scandir( __DIR__.'/../core/theme/' );

foreach( $dir as $file ){
	if( $file != '.' && $file != '..' ){
		if( strpos( $file , "site" ) !== false ){
			$teil = substr( $file , 12 , -4 );

			if( $teil != 'norm' ){
				$del = '<span onclick="var delet = del( \''.$teil.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Theme löschen." style="display:inline-block;" ></span></span>';
			}
			else{
				$del = '';
			}

			if ( $allgsysconf['theme'] == $teil ){
				$status = '<span class="ui-icon ui-icon-check" title="Dieses Theme ist zu Zeit aktiviert. ( Bitte aktivieren Sie ein anderes, um dies zu ändern. )" style="display:inline-block;" ></span>';
			}
			elseif( !isset( $allgsysconf['theme'] ) && $teil == 'norm' ){
				$status = '<span class="ui-icon ui-icon-check" title="Dieses Theme ist zu Zeit aktiviert. ( Bitte aktivieren Sie ein anderes, um dies zu ändern. )" style="display:inline-block;" ></span>';
			}
			else{
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_themes.php?theme='.$teil.'&amp;chdeak"><span class="ui-icon ui-icon-close" title="Dieses Theme ist zu Zeit deaktiviert. ( click -> aktivieren )" style="display:inline-block;" ></span></a>';
			}

			$sitecontent->add_site_content('<tr> <td>'.$teil.'</td> <td>'.$status.'</td> <td>'.$del.'</td> </tr>');
		}
	}
}

$sitecontent->add_site_content('</table>');

$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie dieses Theme wirklich löschen?</p></div></div>');


$sitecontent->add_site_content('<br /><br /><h2>Theme installieren</h2>');
$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_themes.php" enctype="multipart/form-data" method="post">');
$sitecontent->add_site_content('<input name="userfile" type="file" /><br />');
$sitecontent->add_site_content('<input type="submit" value="Installieren" title="Wählen Sie eine Theme Zip Datei von Ihrem Rechner zur Installation." />');
$sitecontent->add_site_content('</form>');

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
