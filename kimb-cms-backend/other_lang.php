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

check_backend_login( 'twentyone' , 'more');

$sitecontent->add_site_content('<h2>Mehrsprachige Seite <span class="ui-icon ui-icon-info" title="Hier können Sie die Seite dieses CMS um weitere Sprachen erweitern!" style="display:inline-block;"></span></h2>');

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; } label{ width:200px; }</style>');

if( isset( $_GET['do'] ) && $allgsysconf['lang'] == 'on' ){

	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/langs.min.js"></script>');
	$sitecontent->add_html_header('<script>$(function() { $( "#autoc" ).autocomplete({ source: langtag }); });
	function setname(){ var tagna = $( "#autoc" ).val(); $( "#langnam" ).val( langobj[tagna] ); } </script>');

	$langfile = new KIMBdbf( 'site/langfile.kimb' );

	$sitecontent->add_site_content('<table width="100%"><tr> <th>ID</th> <th>Tag <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Der Tag dient für die URL!"></span></th> <th>Name</th> <th>Status <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Eine Sprache kann nicht gelöscht, nur deaktiviert, werden!"></span></th> </tr>');
	$sitecontent->add_site_content('<tr> <td title="Standard ( Sprache des ersten Contents )" >0</td> <td></td> <td></td> <td></td> </tr>');


	$sitecontent->add_site_content('<tr> <td></td> <td><input type="text" name="newtag" id="autoc" onchange="setname();"></td> <td><input type="text" name="langname" id="langnam"></td> <td></td> </tr>');


}
else{
	$sitecontent->add_html_header('<script>$(function() { $( "#onoff" ).buttonset(); $( "#on" ).click(function() { window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?chdeak=on"; }); $( "#off" ).click(function() { window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php?chdeak=off"; }); }); </script>' );

	if( isset( $_GET['chdeak'] ) ){
		if( $_GET['chdeak'] == 'on' ){
			$conffile->write_kimb_id( '001' , 'add' , 'lang' , 'on' );
			$allgsysconf['lang'] = 'on';
		}
		elseif( $_GET['chdeak'] == 'off' ){
			$conffile->write_kimb_id( '001' , 'add' , 'lang' , 'off' );
			$allgsysconf['lang'] = 'off';

		}
	}

	if( $allgsysconf['lang'] == 'on' ){
		$checked['on'] = 'checked="checked"';
		$checked['off'] = '';
	}
	else{
		$checked['off'] = 'checked="checked"';
		$checked['on'] = '';
	}

	$sitecontent->add_site_content('<br /><br /><center><form> <div id="onoff"> <input type="radio" id="on" name="onoff" '.$checked['on'].'> <label for="on">Aktiviert</label> <input type="radio" id="off" name="onoff" '.$checked['off'].'> <label for="off">Deaktiviert</label> </div> </form></center><br /><br />');

	if( $allgsysconf['lang'] == 'on' ){

	$sitecontent->add_html_header('<script> $(function() { $( "a#dospreinst" ).button(); }); </script>');
	$sitecontent->echo_message( 'Mehrsprachige Seiten aktiviert!' );

		$sitecontent->add_site_content('<br /><br /><center><a id="dospreinst" href="?do">Zu den Einstellungen &rarr;</a></center>');
	}
	else{
		$sitecontent->echo_message( '<center>Mehrsprachige Seiten deaktiviert!</center>' );
	}
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
