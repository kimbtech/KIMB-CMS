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

if( substr( $_SERVER['REQUEST_URI'] , -26 ) == 'kimb-cms-backend/index.php' ){

	if( !isset( $_SESSION['admin_login_mail']['datengesanz'] ) ){
		$_SESSION['admin_login_mail']['datengesanz'] = 0;
	}

	if( isset($_POST['user']) || isset($_POST['pass']) ){
		$_SESSION['admin_login_mail']['datengesanz']++;
	}

	if( $_SESSION['admin_login_mail']['datengesanz'] == 4 ){
		
		$text = 'Hallo Admin,'."\n\r".' es wurden am Backend gerade mindestens 3 fehlerhafte Loginversuche festgestellt!'."\n\r\n\r";
		$text .= 'Zeit:'.date("d.m.Y \u\m H:i:s")."\n\r".'IP:'.$_SERVER['REMOTE_ADDR']."\n\r".'Useragent: '.$_SERVER['HTTP_USER_AGENT']."\n\r";

		send_mail( $allgsysconf['adminmail'] , $text );
	}
}

if( !isset( $_SESSION['admin_login_mail']['infomail'] ) ){
	if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] ){

		$text = 'Hallo '.$_SESSION['name'].','."\n\r".' Ihre Logindaten wurden gerade fuer ein Login im Backend verwendet'."\n\r\n\r";
		$text .= 'Zeit:'.date("d.m.Y \u\m H:i:s")."\n\r".'IP:'.$_SERVER['REMOTE_ADDR']."\n\r".'Useragent: '.$_SERVER['HTTP_USER_AGENT']."\n\r";

		$userfile = new KIMBdbf('backend/users/list.kimb');

		$id = $userfile->search_kimb_xxxid( $_SESSION['user'] , 'user' );		
		if( $id != false ){
			$maila = $userfile->read_kimb_id( $id , 'mail' );
			send_mail( $maila , $text );
		}
	
		$_SESSION['admin_login_mail']['infomail'] = 'send';
	}
}
?>
