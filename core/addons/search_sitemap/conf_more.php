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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=search_sitemap';

$sitecontent->add_site_content('<hr /><br /><h2>Suche &amp; Sitemap</h2>');

$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

$conffile = new KIMBdbf( 'addon/search_sitemap__conf.kimb' );


if( isset( $_POST['send'] ) ){

	$allnames = array( 'mapsiteid', 'searchsiteid', 'searchform', 'maxversuch', 'maxerg' );

	$examples = array( 'mapsiteid' => '2', 'searchsiteid' => '2', 'searchform' => 'on', 'maxversuch' => '100', 'maxerg' => '10' );

	foreach( $_POST as $name => $val ){

		if( in_array( $name , $allnames ) ){

			if( empty( $val ) ){
				$val = $examples[$name];
			}

			$old = $conffile->read_kimb_one( $name );

			if( $old != $val ){
				if( !empty( $old ) ){
					$conffile->write_kimb_replace( $name , $val );
				}
				else{
					$conffile->write_kimb_new( $name , $val );
				}
				$sitecontent->echo_message( 'Der Wert "'.$name.'" wurde geändert!' );
			}		

		}

	}
} 


$sitecontent->add_site_content('<h3>Sitemap</h3>');
$sitecontent->add_html_header('<script>$(function(){ $( "[name=mapsiteid]" ).append( "<option value=\'off\'>Off</option>" ); $( "[name=mapsiteid]" ).val( "'.$conffile->read_kimb_one( 'mapsiteid' ).'" ); }); </script>');
$sitecontent->add_site_content( id_dropdown( 'mapsiteid', 'siteid' ).' <span style="display:inline-block;" title="Bitte wählen Sie eine Seite auf der die Sitemap angezeigt werden soll!" class="ui-icon ui-icon-info"></span><br />');


$sitecontent->add_site_content('<h3>Suche</h3>');
$sitecontent->add_html_header('<script>$(function(){ $( "[name=searchsiteid]" ).append( "<option value=\'off\'>Off</option>" ); $( "[name=searchsiteid]" ).val( "'.$conffile->read_kimb_one( 'searchsiteid' ).'" ); }); </script>');
$sitecontent->add_site_content( id_dropdown( 'searchsiteid', 'requid' ).' <span style="display:inline-block;" title="Bitte wählen Sie eine Seite auf der die Suche angezeigt werden soll!" class="ui-icon ui-icon-info"></span><br />');

$form = array( 'on' => ' ' , 'off' => ' ' );
if( $conffile->read_kimb_one( 'searchform' ) == 'on' ){
	$form['on'] = ' checked="checked" ';
}
else{
	$form['off'] = ' checked="checked" ';
}

$sitecontent->add_site_content('<input name="searchform" value="on" type="radio" '.$form['on'].'><span style="display:inline-block;" title="Ein Formular für die Suche auf jeder Seite anzeigen ( Sollte Ihr Template schon diese Funktion beinhalten müssen Sie hier deaktivieren!)" class="ui-icon ui-icon-check"></span>' );
$sitecontent->add_site_content('<input name="searchform" value="off" type="radio" '.$form['off'].'><span style="display:inline-block;" title="Das Formular nur auf der Such-Seite anzeigen." class="ui-icon ui-icon-closethick"></span><br />' );

$sitecontent->add_site_content('<input name="maxversuch" value="'.$conffile->read_kimb_one( 'maxversuch' ).'" type="text"> Maximale Versuche<b title="Geben Sie an, wie viele Seiten der Suchalgorithmus durchsuchen soll. (Achtung: Zuserst findet ein Durchlauf mit den Menuenamen statt, erst dann wird in den Seiteninhalten gesucht. (Einen Seite => 2 Versuche))">*</b><br />' );
$sitecontent->add_site_content('<input name="maxerg" value="'.$conffile->read_kimb_one( 'maxerg' ).'" type="text"> Maximale Ergebnisse<b title="Geben Sie an, nach wie vielen Ergebnissen der Suchalgorithmus abbrechen soll.">*</b><br />' );

$sitecontent->add_site_content('<br /><br /><input type="hidden" value="send" name="send"><input type="submit" value="Speichern"> </form>');
?>
