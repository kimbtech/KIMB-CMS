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

$sitecontent->add_site_content('<hr /><br /><h2>Admin Login Mail</h2>');

$sitecontent->add_site_content('Dieses Add-on informiert per E-Mail wenn sich ein User am Backend angemeldet hat oder wenn fehlgeschlagene Anmeldeversuche am Backend verzeichnet wurden.');

$sitecontent->add_site_content('<h3>Backend Login</h3>');
$sitecontent->add_site_content('Jeder User erhält nach dem Login im Backend eine Information an seinen E-Mail-Adresse ( siehe Usereinstellungen ).');

$sitecontent->add_site_content('<h3>Fehlgeschlagene Anmeldeversuche</h3>');
$sitecontent->add_site_content('Sollte nach 3 Anmeldeversuchen noch ein weiterer Versuch durchgeführt werden, so wird an die in der Konfiguration angegebene Admin E-Mail-Adresse eine Information versandt.');

$sitecontent->add_site_content('<br /><br /><br /><b>Weitere Einstellungen sind nicht verfügbar!</b>');

?>
