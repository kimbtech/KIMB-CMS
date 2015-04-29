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

$addonwish = new KIMBdbf('addon/wish/fe_all.kimb');

$includes = array();

foreach( $all as $add ){

	$id = $addonwish->search_kimb_xxxid( $add , 'addon' );

	if( $id != false ){

		//IDs
		$wid = $addonwish->read_kimb_id( $id, 'ids' );
		$wids = false;
		$ebst = substr( $wid, 0, 1 );
		$wid = substr( $wid, 1);

		if( $ebst == 'a' ){
			$wids = true;
		}
		elseif( $ebst == 'r' && $_GET['id'] == $wid ){
			$wids = true;
		}
		elseif( $ebst == 's' && $allgsiteid == $wid ){
			$wids = true;
		}

		//Error
		$err = $addonwish->read_kimb_id( $id, 'error' );
		$error = false;

		if( $err == 'all' ){
			$error = true;
		}
		elseif( $err == 'no' && $allgerr != '404' && $allgerr != '403' ){
			$error = true;
		}
		elseif( $err == '404' && $allgerr == '404' ){
			$error = true;
		}
		elseif( $err == '403' && $allgerr == '403' ){
			$error = true;
		}

		if( $error && $wids ){
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

foreach( $includes as $name ){

	require_once(__DIR__.'/'.$name.'/include_fe_first.php');

}

$fesecondincludesaddons = $includes;

?>
