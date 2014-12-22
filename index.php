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


define("KIMB_CMS", "Clean Request");

defined('KIMB_CMS') or die('No clean Request');

//Klassen und Funktionen
require_once(__DIR__.'/core/oop/all_oop.php');
//Konfiguration laden
require_once(__DIR__.'/core/conf/conf.php');

//System initialisiert!

//Menue und Site IDs aus Request 
require_once(__DIR__.'/core/generating/get_ids.php');
//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_first.php');
//Menuestruktur erstellen
require_once(__DIR__.'/core/generating/make_menue.php');
//Inhalt erstellen
require_once(__DIR__.'/core/generating/make_content.php');
//Addons ermoeglichen einzugreifen
require_once(__DIR__.'/core/addons/addons_second.php');


$sitecontent->output_complete_site();
?>
