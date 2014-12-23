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

//mache aus Links oder RequestID => MenueID und SiteID

if( isset($_SERVER['REQUEST_URI']) && $allgsysconf['urlrewrite'] == 'on' && !isset($_GET['id']) && $allgsysconf['use_request_url'] == 'ok'){
	$_GET['url'] = $_SERVER['REQUEST_URI'];
}

if( isset($_GET['url']) ){

	// URL => RequestID

	$urlteile = explode( '/' , $_GET['url'] );

	$i = '0';
	if($urlteile[$i] == ''){
		$i++;
	}

	$pos = strpos( $allgsysconf['siteurl'] , '/' , 8 );
	$wegteil = substr( $allgsysconf['siteurl'] , $pos + 1 );
	$wegteile = explode( '/' , $wegteil );
	foreach( $wegteile as $teil ){
		if( $urlteile[$i] == $teil ){
			$i++;
		}
		else{
			break;
		}
	}
	
	$file = new KIMBdbf('url/first.kimb');
	$ok = $file->search_kimb_xxxid( $urlteile[$i] , 'path' );
	if( $ok != false){
		$nextid = $file->read_kimb_id( $ok , 'nextid' );
		$i++;
		if( is_numeric( $nextid ) && $nextid != '' && $urlteile[$i] != '' ){
			while( 5 == 5 ){
				$file = new KIMBdbf('url/nextid_'.$nextid.'.kimb');
				$ok = $file->search_kimb_xxxid( $urlteile[$i] , 'path' );
				if( $ok != false){
					$nextid = $file->read_kimb_id( $ok , 'nextid' );
					$i++;
					if( is_numeric( $nextid ) && $nextid != '' && $urlteile[$i] != '' ){
						
					}
					else{
						$_GET['id'] = $file->read_kimb_id( $ok , 'requestid' );
						if( $urlteile[$i] != '' ){
							$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
							$allgerr = '404';
						}
						if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
							$sitecontent->echo_error( 'Fehlerhafte RequestURL !' );
							$allgerr = 'unknown';
							$_GET['id'] = '1';
						}
						break;
					}
				}
				else{
					$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
					$allgerr = '404';
					break;
				}
			}		
		}
		else{
			$_GET['id'] = $file->read_kimb_id( $ok , 'requestid' );
			if( $urlteile[$i] != '' ){
				$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
				$allgerr = '404';
			}
			if( !is_numeric($_GET['id']) || $_GET['id'] == '' ){
				$_GET['id'] = '1';
			}
		}
	}
	else{
		$sitecontent->echo_error( 'Diese URL zeigt auf keine Seite!' , '404' );
		$allgerr = '404';
	}
	
}
elseif( isset($_GET['id']) ){

	// RequestID => weiter gehts ...

	if( !is_numeric($_GET['id']) ){
		$sitecontent->echo_error( 'Fehlerhafte RequestID !' );
		$allgerr = 'unknown';
		$_GET['id'] = '1';

	}
}
else{

	$_GET['id'] = '1'; // Startseite

}

// get MenueID && get SiteID

$idfile = new KIMBdbf('menue/allids.kimb');

$allgsiteid = $idfile->read_kimb_id($_GET['id'], 'siteid');

$allgmenueid = $idfile->read_kimb_id($_GET['id'], 'menueid');

if( $allgsiteid == ''  || $allgmenueid == '' || $allgsiteid == false  || $allgmenueid == false ){
	$sitecontent->echo_error( 'Fehlerhafte RequestID Zuordnung!' , '404' );
	$allgerr = '404';
}

//Weitergabe von $allgsiteid, $allgmenueid, $allgerr

?>
