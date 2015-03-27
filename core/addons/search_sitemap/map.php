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

if( is_object($sitecache) ){

	$menuecache = $sitecache->get_cached_addon( $search_sitemap['searchsiteid'] , 'search_sitemap' );

	if( $menuecache != false ){
		$sitecontent->add_site_content( $menuecache[0] );
		$cacheload = 'yes';
	}
	else{
		$cacheload = 'no';
	}

}
else{
	$cacheload = 'no';
}

if( $cacheload == 'no' ){

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	make_menue_array();

	$menue = '<ul id="sitemap">';

	foreach( $menuearray as $menuear ){

		$niveau = $menuear['niveau'];

		if( $allgsysconf['urlrewrite'] == 'on' ){

			if( !isset( $thisniveau ) ){
				$grpath = $allgsysconf['siteurl'].'/'.$menuear['path'];
			}
			elseif( $thisniveau == $niveau ){
				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				$grpath = $grpath.'/'.$menuear['path'];
			}
			elseif( $thisniveau < $niveau ){
				$grpath = $grpath.'/'.$menuear['path'];
				$thisulaufp = $thisulauf + 1;
			}
			elseif( $thisniveau > $niveau ){
				$i = 1;
				while( $thisniveau != $niveau + $i  ){
					$i++;
					$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				}
				$thisulaufp = $thisulaufp - $i;

				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				$grpath = $grpath.'/'.$menuear['path'];
			}
			$url = $grpath.'/';
		}
		else{
			$url = $allgsysconf['siteurl'].'/index.php?id='.$menuear['requid'];
		}

		if( !isset( $thisniveau ) ){
			$menue .= '<li>'."\r\n";
		}
		elseif( $thisniveau == $niveau ){
			$menue .= '</li><li>'."\r\n";
		}
		elseif( $thisniveau < $niveau ){
			$menue .= '<ul><li>'."\r\n";
			$thisulauf = $thisulauf + 1;
		}
		elseif( $thisniveau > $niveau ){
			$i = 1;
			while( $thisniveau != $niveau + $i  ){
				$i++;
			}
			$menue .= '</li>'.str_repeat( '</ul>' , $i ).'<li>'."\r\n";
			$thisulauf = $thisulauf - $i;
		}

		if ( $menuear['status'] == 'on' ){
			$menue .=  '<a href="'.$url.'">'.$menuear['menuname'].'</a>'."\r\n";
		}
		$thisniveau = $niveau;
	}

	$menue .= '</li>'.str_repeat( '</ul>' , $thisulauf );

	$sitecontent->add_site_content( $menue );

	if( is_object($sitecache) ){
		$sitecache->cache_addon( $search_sitemap['searchsiteid'] , $menue , 'search_sitemap' );
	}

}

?>
