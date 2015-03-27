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

$newver = json_decode( file_get_contents( 'http://api.kimb-technologies.eu/cms/getcurrentversion.php' ) , true );

if( compare_cms_vers( $allgsysconf['build'] , $newver['currvers'] ) == 'older' ){
	$update = 'yes';
	$updatearr['do'] = 'yes';
	$updatearr['sysv'] = $allgsysconf['build'];
	$updatearr['newv'] = $newver['currvers'];
}
else{
	$update = 'no';
	$updatearr['do'] = 'no';
	$updatearr['sysv'] = $allgsysconf['build'];
	$updatearr['newv'] = $newver['currvers'];
}

?>
