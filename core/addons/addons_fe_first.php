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

//includes addons die first wollen

//evtl siteid, menueid, err verarbeiten

if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

$all = $addoninclude->read_kimb_all_teilpl( 'fe_first' );

//Machen!!!!!
//Machen!!!!!
//Machen!!!!!
//Machen!!!!!

foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_fe_first.php');

}

$fesecondincludesaddons = $includes;

?>
