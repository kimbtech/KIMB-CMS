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

if( $_GET['addon'] == 'api_login' ){

	$file = new KIMBdbf( 'addon/api_login__dest.kimb' );
	$feconf = new KIMBdbf( 'addon/felogin__conf.kimb'  );
	$loginokay = $feconf->read_kimb_one( 'loginokay' );

	if( $loginokay == $_SESSION['felogin']['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

		if( !empty( $_GET['id'] ) && is_numeric( $_GET['id'] ) ){
			$read = $file->read_kimb_one( 'dest'.$_GET['id'] );
			if( !empty( $read ) ){
			
				$id = makepassw( 100 , '_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );

				$postarr['loginokay'] = $_SESSION['felogin']['loginokay'];
				$postarr['ip'] = $_SERVER['REMOTE_ADDR'];
				$postarr['ua'] = $_SERVER['HTTP_USER_AGENT'];
				$postarr['gr'] = $_SESSION['felogin']['gruppe'];
				$postarr['us'] = $_SESSION['felogin']['user'];
				$postarr['na'] = $_SESSION['felogin']['name'];
				$postarr['id'] = $id;
			
				$jsondata = json_encode( $postarr );
				$openurl = $read.'?id='.$id;

				$auth = $file->read_kimb_one( 'auth' );
			
				require_once( __DIR__.'/unirest/unirest_loader.php' );

				$headers = array("Accept" => "application/json");
				$body = array( 'auth' => $auth, 'jsondata' => $jsondata );
				$response = Unirest\Request::post( $read , $headers, $body);

				if( $response->body == 'taken' ){
					open_url( $openurl, 'outsystem' );
					die;
				}
				else{
					echo( 'Error No/Wrong Destination Answer' );
				}
			}
			else{
				echo( 'Error Destination Not Found' );
			}
		}
		else{
			echo( 'Error ID Not Set' );
		}
	}
	else{
		echo( 'Not Logged In!' );
	}
	
	die;
}

?>
