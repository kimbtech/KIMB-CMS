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

if( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ){
	$req = substr( $_SERVER['REQUEST_URI'] , '0', '-'.strlen(strrchr( $_SERVER['REQUEST_URI'] , '?' )));
}
else{
	$req = $_SERVER['REQUEST_URI'];
}

if( substr( $req , -38 ) == 'kimb-cms-backend/other_filemanager.php' ){

	if( $_GET['action'] == 'rein' ){ 
		$path = $_GET['path']; 
	}
	elseif( $_GET['action'] == 'hoch' ){
		$path = substr( $_GET['path'] , '0', '-'.strlen(strrchr( $_GET['path'] , '/' )));
	}

	if( !empty( $path ) && $_SESSION['secured'] == 'off' ){

		$galerie['file'] = new KIMBdbf( 'addon/galerie__conf.kimb' );

		$galerie['id'] = $galerie['file']->search_kimb_xxxid( $path , 'imgpath');

		if( $galerie['id'] == false ){		
			$sitecontent->echo_message( 'Diesen Ordner als Bilderverezichnis für eine Galerie verwenden?<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=galerie&amp;path='.urlencode( $path ).'"><button>Los!</button></a>' );
		}
		else{
			$sitecontent->echo_message( 'Dieser Ordner ist ein Bilderverezichnis für eine Galerie!<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=galerie&amp;id='.$galerie['id'].'"><button>Einstellungen</button></a>' );
		}
	}
}
	
?>
