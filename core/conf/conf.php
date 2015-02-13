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



defined('KIMB_CMS') or die('No clean Request');

//erstelle globale conf Variablen aus config.kimb

$conffile = new KIMBdbf('config.kimb');

$allgsysconf = $conffile->read_kimb_id('001');

//session, ...

session_start();
error_reporting( 0 );
header('X-Robots-Tag: '.$allgsysconf['robots']);
header('Content-Type: text/html; charset=utf-8');

//wichtige Objekte

$sitecontent = new system_output($allgsysconf);

if($allgsysconf['cache'] == 'on'){
	$sitecache = new cacheCMS($allgsysconf, $sitecontent);
}

$kimbcmsinfo = '<!--

	Diese Seite basiert auf dem KIMB-CMS!
	www.KIMB-technologies.eu

	CC BY-ND 4.0
	http://creativecommons.org/licenses/by-nd/4.0/

-->';

$sitecontent->add_html_header($kimbcmsinfo);

//allgemeine Funktionen

require_once(__DIR__.'/funktionen.php');
?>
