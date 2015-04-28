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

//includes addons die first wollen

if( !isset( $addoninclude ) ){
	$addoninclude = new KIMBdbf('addon/includes.kimb');
}

$all = $addoninclude->read_kimb_all_teilpl( 'be_first' );

$addonwish = new KIMBdbf('addon/wish/be_all.kimb');

//Reihenfolge && Rechte && Sites
$includes = array();
$url = get_req_url();

foreach( $all as $add ){

	$id = $addonwish->search_kimb_xxxid( $add , 'addon' );

	if( $id != false ){

		//Rechte
		$re = $addonwish->read_kimb_id( $id, 'recht' );
		$recht = false;

		foreach( explode( ',' , $re ) as $res ){
			if( $res == 'more' && $_SESSION['permission'] == 'more' ){
				$recht = true;
			}
			elseif( $res == 'less' && $_SESSION['permission'] == 'less' ){
				$recht = true;
			}
			elseif( check_backend_login( $res , 'none', false ) ){
				$recht = true;
			}
			elseif( $res == 'no' ){
				$recht = true;
			}

			if( $recht ){
				break;
			}
		}

		//Sites
		$si = $addonwish->read_kimb_id( $id, 'site' );
		if( $si == 'all' ){
			$site = true;
		}
		elseif( substr( $url , '-'.strlen( 'kimb-cms-backend/'.$si.'.php' ) ) == 'kimb-cms-backend/'.$si.'.php' ){
			$site = true;
		}
		else{
			$site = false;
		}

		if( $site && $recht ){
			//Reihenfolge
			$wi = $addonwish->read_kimb_id( $id, 'stelle' );
			if( $wi == 'vorn' ){
				array_unshift( $includes , $add );
			}
			elseif( $wi == 'hinten' ){
				$includes[] = $add;
			}
		}
	}

}

//Ausführen
foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_be_first.php');

}

$besecondincludesaddons = $includes;

?>
