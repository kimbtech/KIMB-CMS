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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=sitemapxml';

$sitecontent->add_site_content( '<hr /><br /><h2>Sitemap.xml</h2>' );

$sitecontent->add_site_content( 'Dieses Add-on generiert für Sie eine Sitemap.xml.<br />' );

$conffile = new KIMBdbf( 'addon/sitemapxml__conf.kimb' );

if( isset( $_GET['keygen'] ) ){

	$key = makepassw( 30, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );	

	if( empty( $conffile->read_kimb_one( 'key' ) ) ){
		$conffile->write_kimb_new( 'key', $key );
	}
	else{
		$conffile->write_kimb_replace( 'key', $key );
	}
	$sitecontent->echo_message( 'Es wurde ein neuer Key erstellt!' );
}

$key = $conffile->read_kimb_one( 'key' );

$sitecontent->add_site_content( '<h3>Links</h3>' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;key='.$key.'" target="_blank" >Neue Sitemap anschauen</a><br />' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;save&amp;key='.$key.'" target="_blank" >Neue Sitemap speichern <b title="Unter der URL: '.$allgsysconf['siteurl'].'/sitemap.xml" >*</b></a><br />' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;down&amp;key='.$key.'" target="_blank" >Neue Sitemap herunterladen</a><br />' );
$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/sitemap.xml" target="_blank" >Aktuelle Sitemap ansehen</a><br />' );
$sitecontent->add_site_content( '<br />' );
$sitecontent->add_site_content( 'Key: <div style="background-color:#ccc; padding:10px; width:90%;" >'.$key.'</div><br />' );
$sitecontent->add_site_content( 'Cron URL: <div title="Nutzen Sie diese URL um mit einem Cron-Job regelmäßig vollkommen automatisch eine neue Sitemap zu generieren." style="background-color:#ccc; padding:10px; width:90%;" >'.$allgsysconf['siteurl'].'/ajax.php?addon=sitemapxml&amp;save&amp;key='.$key.'</div><br />' );
$sitecontent->add_site_content( '<br />' );

$sitecontent->add_site_content( '<hr /><a href="'.$addonurl.'&amp;keygen">Neuen Key generieren</a>' );



?>
