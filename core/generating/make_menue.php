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



defined('KIMB_CMS') or die('No clean Request');

if(!is_object( $idfile )){
	$idfile = new KIMBdbf('menue/allids.kimb');
}

if($allgmenueid == $idfile->read_kimb_id($_GET['id'], 'menueid')){
	$allgrequestid = $_GET['id'];
}
else{
	$allgrequestid = search_kimb_xxxid( $allgmenueid , 'menueid' );
}

if( $allgsysconf['cache'] == 'on' ){
	if( $sitecache->load_cached_menue($allgmenueid) ){
		$menuecache = 'loaded';
	}
}

$menuenames = new KIMBdbf('menue/menue_names.kimb');

if( $menuecache != 'loaded'){

	gen_menue( $allgrequestid );

}

?>
