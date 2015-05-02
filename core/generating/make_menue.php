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

if(!is_object( $idfile )){
	$idfile = new KIMBdbf('menue/allids.kimb');
}

if($allgmenueid == $idfile->read_kimb_id($_GET['id'], 'menueid')){
	$allgrequestid = $_GET['id'];
}
else{
	$allgrequestid = $idfile->search_kimb_xxxid( $allgmenueid , 'menueid' );
}

if( $allgsysconf['lang'] == 'off' ){
	$requestlang['id'] = 0;
}

if( $allgsysconf['cache'] == 'on' ){
	if( $sitecache->load_cached_menue($allgmenueid, $requestlang['id'] ) ){
		$menuecache = 'loaded';
	}
}

if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
	$menuenames = new KIMBdbf('menue/menue_names_lang_'.$requestlang['id'].'.kimb');
	$menuenameslangst = new KIMBdbf('menue/menue_names.kimb');
}
else{
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	$menuenameslangst = $menuenames;
}


if( $menuecache != 'loaded'){

	$breadarrfertig = 'nok';
	
	gen_menue( $allgrequestid );

	$breadcrumblinks = '<div id="breadcrumb" >';

	$niveau = 1;
	while( $breadcrumbarr['maxniv'] >= $niveau ){

		$breadcrumblinks .= ' &rarr; ';
		$breadcrumblinks .= '<a href="'.$breadcrumbarr[$niveau]['link'].'">'.$breadcrumbarr[$niveau]['name'].'</a>';

		$niveau++;
	}
	$breadcrumblinks .= '</div>';

	if( is_object($sitecache) ){
		if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
			$sitecache->cache_addon( $allgmenueid , $breadcrumblinks , 'breadcrumb-'.$requestlang['id'] );
		}
		else{
			$sitecache->cache_addon( $allgmenueid , $breadcrumblinks , 'breadcrumb' );
		}
	}

}
else{
	if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
		$breadcrumblinks = $sitecache->get_cached_addon( $allgmenueid , 'breadcrumb-'.$requestlang['id'] );
	}
	else{
		$breadcrumblinks = $sitecache->get_cached_addon( $allgmenueid , 'breadcrumb' );
	}

	$breadcrumblinks = $breadcrumblinks[0];
}

$sitecontent->add_site_content( $breadcrumblinks );

?>
