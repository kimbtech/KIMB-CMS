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

defined('KIMB_CMS') or die('No clean Request');

$search_sitemap['file'] = new KIMBdbf( 'addon/search_sitemap__conf.kimb' );

$search_sitemap['mapsiteid'] = $search_sitemap['file']->read_kimb_one( 'mapsiteid' ); // off oder id
$search_sitemap['searchsiteid'] = $search_sitemap['file']->read_kimb_one( 'searchsiteid' ); // off oder id
$search_sitemap['searchform'] = $search_sitemap['file']->read_kimb_one( 'searchform' ); // on oder off

if( $allgsiteid == $search_sitemap['mapsiteid'] ){

	require_once( __DIR__.'/map.php' );

}

if( $search_sitemap['searchform'] == 'on' && $search_sitemap['searchsiteid'] != 'off' ){

	$search_sitemap['searchform'] = '<h2>Suche</h2>';
	$search_sitemap['searchform'] .= '<form method="post" action="'.$allgsysconf['siteurl'].'/index.php?id='.$search_sitemap['searchsiteid'].'">';
	$search_sitemap['searchform'] .= '<input type="text" name="search" placeholder="Suchbegriff" value="'.htmlentities( $_REQUEST['search'] ).'">';
	$search_sitemap['searchform'] .= '<input type="submit" value="Suchen">';
	$search_sitemap['searchform'] .= '</form>';

	$sitecontent->add_addon_area( $search_sitemap['searchform'] );
	
}

if( $_GET['id'] == $search_sitemap['searchsiteid'] ){

	$begriff = $_REQUEST['search'];

	require_once( __DIR__.'/search.php' );

	unset( $begriff );
}

unset( $search_sitemap['searchform'] );
?>
