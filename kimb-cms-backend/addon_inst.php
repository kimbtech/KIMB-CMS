<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS
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



define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login( 'fiveteen' , 'more');

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//Add-ons installieren und löschen

$_GET['addon'] = preg_replace( "/[^a-z_]/" , "" , $_GET['addon'] );

if( isset( $_GET['del'] ) && isset( $_GET['addon'] ) ){

	if( is_dir( __DIR__.'/../core/addons/'.$_GET['addon'].'/' ) ){
		rm_r( __DIR__.'/../core/addons/'.$_GET['addon'].'/' );
	}
	if( is_dir( __DIR__.'/../load/addondata/'.$_GET['addon'].'/' ) ){
		rm_r( __DIR__.'/../load/addondata/'.$_GET['addon'].'/' );
	}
	if ( $addoninclude->read_kimb_search_teilpl( 'ajax' , $addon ) || $addoninclude->read_kimb_search_teilpl( 'backend' , $addon ) || $addoninclude->read_kimb_search_teilpl( 'first' , $addon ) || $addoninclude->read_kimb_search_teilpl( 'second' , $addon ) ){
		$addoninclude->write_kimb_teilpl( 'ajax' , $_GET['addon'] , 'del' );
		$addoninclude->write_kimb_teilpl( 'first' , $_GET['addon'] , 'del' );
		$addoninclude->write_kimb_teilpl( 'backend' , $_GET['addon'] , 'del' );
		$addoninclude->write_kimb_teilpl( 'second' , $_GET['addon'] , 'del' );
	}

	$sitecontent->echo_message( 'Das Add-on "'.$_GET['addon'].'" wurde gelöscht!' );
	
}
elseif( isset( $_FILES['userfile']['name'] ) ){

	if( !mkdir( __DIR__.'/../core/addons/temp/' ) ){
		$sitecontent->echo_error( 'Die Installation schlug fehl!' , 'unknown');
		$sitecontent->output_complete_site();
		die;
	}

	if( !chmod( __DIR__.'/../core/addons/temp/' , ( fileperms( __DIR__.'/../core/addons/' ) & 0777) ) ){
		$sitecontent->echo_error( 'Die Installation schlug fehl!' , 'unknown');
		$sitecontent->output_complete_site();
		die;
	}

	$zip = new ZipArchive;
	if ($zip->open($_FILES["userfile"]["tmp_name"]) === TRUE) {
	    $zip->extractTo( __DIR__.'/../core/addons/temp/' );
	    $zip->close();
	}
	else{
		$sitecontent->echo_error( 'Die Installation schlug fehl!' , 'unknown');
		$sitecontent->output_complete_site();
		die;
	}

	$name = file_get_contents( __DIR__.'/../core/addons/temp/name.info' );
	$name = preg_replace("/[^a-z_]/","", $name);

	if( is_dir( __DIR__.'/../core/addons/'.$name.'/' ) ){
		rm_r( __DIR__.'/../core/addons/temp/' );
		$sitecontent->echo_error( 'Das Add-on "'.$name.'" ist bereits installiert!' , 'unknown');
		$sitecontent->output_complete_site();
		die;
	}

	copy_r( __DIR__.'/../core/addons/temp/addon' , __DIR__.'/../core/addons/'.$name.'/' );
	copy_r( __DIR__.'/../core/addons/temp/load' , __DIR__.'/../load/addondata/'.$name.'/' );

	if( file_exists( __DIR__.'/../core/addons/temp/install.php' ) ){
		require( __DIR__.'/../core/addons/temp/install.php' );
	}

	if( !rm_r( __DIR__.'/../core/addons/temp/' ) ){
		$sitecontent->echo_error( 'Die Installation schlug fehl!' , 'unknown');
		$sitecontent->output_complete_site();
		die;
	}

	$sitecontent->echo_message( 'Das Add-on "'.$name.'" wurde installiert!' );

}
elseif( $_GET['todo'] == 'chdeak' && isset( $_GET['addon'] ) ){

	if( !$addoninclude->read_kimb_search_teilpl( 'ajax' , $_GET['addon'] ) ){
		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/include_ajax.php') ){
			$addoninclude->write_kimb_teilpl( 'ajax' , $_GET['addon'] , 'add' );
		}
	}
	else{
		$addoninclude->write_kimb_teilpl( 'ajax' , $_GET['addon'] , 'del' );
	}

	if( !$addoninclude->read_kimb_search_teilpl( 'first' , $_GET['addon'] ) ){
		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/include_first.php') ){
			$addoninclude->write_kimb_teilpl( 'first' , $_GET['addon'] , 'add' );
		}
	}
	else{
		$addoninclude->write_kimb_teilpl( 'first' , $_GET['addon'] , 'del' );
	}

	if( !$addoninclude->read_kimb_search_teilpl( 'backend' , $_GET['addon'] ) ){
		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/include_backend.php') ){
			$addoninclude->write_kimb_teilpl( 'backend' , $_GET['addon'] , 'add' );
		}
	}
	else{
		$addoninclude->write_kimb_teilpl( 'backend' , $_GET['addon'] , 'del' );
	}

	if( !$addoninclude->read_kimb_search_teilpl( 'second' , $_GET['addon'] ) ){
		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/include_second.php') ){
			$addoninclude->write_kimb_teilpl( 'second' , $_GET['addon'] , 'add' );
		}
	}
	else{
		$addoninclude->write_kimb_teilpl( 'second' , $_GET['addon'] , 'del' );
	}

	$sitecontent->echo_message( 'Der Status des Add-ons "'.$_GET['addon'].'" wurde geändert!' );
}

$sitecontent->add_html_header('<script>
var del = function( addon ) {
	$( "#del-confirm" ).show( "fast" );
	$( "#del-confirm" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?addon=" + addon + "&del";
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


$sitecontent->add_site_content('<h2>Add-onliste</h2>');
$sitecontent->add_site_content('<table width="100%"><tr> <th>Name</th> <th>Status</th> <th>Löschen</th> </tr>');
$addons = listaddons();
foreach( $addons as $addon ){

	$del = '<span onclick="var delet = del( \''.$addon.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Add-on löschen."></span></span>';

	if ( $addoninclude->read_kimb_search_teilpl( 'ajax' , $addon ) || $addoninclude->read_kimb_search_teilpl( 'backend' , $addon ) || $addoninclude->read_kimb_search_teilpl( 'first' , $addon ) || $addoninclude->read_kimb_search_teilpl( 'second' , $addon ) ){
		$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=chdeak&amp;addon='.$addon.'"><span class="ui-icon ui-icon-check" title="Dieses Add-on ist zu Zeit aktiviert. ( click -> ändern )"></span></a>';
	}
	else{
		$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?todo=chdeak&amp;addon='.$addon.'"><span class="ui-icon ui-icon-close" title="Dieses Add-on ist zu Zeit deaktiviert. ( click -> ändern )"></span></a>';
	}
			
	$sitecontent->add_site_content('<tr> <td>'.$addon.'</td> <td>'.$status.'</td> <td>'.$del.'</td> </tr>');

	$liste = 'yes';

}
$sitecontent->add_site_content('</table>');

if( $liste != 'yes' ){
	$sitecontent->echo_error( 'Es wurden keine Add-ons gefunden!' );
}

$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie dieses Addon wirklich löschen?</p></div></div>');


$sitecontent->add_site_content('<br /><br /><h2>Add-on installieren</h2>');
$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php" enctype="multipart/form-data" method="post">');
$sitecontent->add_site_content('<input name="userfile" type="file" /><br />');
$sitecontent->add_site_content('<input type="submit" value="Installieren" title="Wählen Sie eine Add-on Zip Datei von Ihrem Rechner zur Installation." />');
$sitecontent->add_site_content('</form>');

$sitecontent->output_complete_site();
?>
