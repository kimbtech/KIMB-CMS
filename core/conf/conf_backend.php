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



defined('KIMB_Backend') or die('No clean Request');

//erstelle globale conf Variablen aus config.kimb

$conffile = new KIMBdbf('config.kimb');

$allgsysconf = $conffile->read_kimb_id('001');

//session, ...

session_start();
error_reporting( 0 );
header('X-Robots-Tag: none');
header('Content-Type: text/html; charset=utf-8');

//wichtige Objekte

$sitecontent = new backend_output($allgsysconf);

$kimbcmsinfo = '<!--

	Diese Seite basiert auf dem KIMB-CMS!
	www.KIMB-technologies.eu

	CC BY-ND 4.0
	http://creativecommons.org/licenses/by-nd/4.0/

-->';

$sitecontent->add_html_header($kimbcmsinfo);

//allgemeine Funktionen

require_once(__DIR__.'/funktionen.php');

//Backend Add-ons ( mit zusätzlichem Schutz )
if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){
	require(__DIR__.'/../addons/addons_be_first.php');
}
elseif( substr( get_req_url() , -26 ) == 'kimb-cms-backend/index.php' || substr( get_req_url() , -16 ) == 'kimb-cms-backend' || substr( get_req_url() , -17 ) == 'kimb-cms-backend/' ){
	require(__DIR__.'/../addons/addons_be_first.php');
}
?>
