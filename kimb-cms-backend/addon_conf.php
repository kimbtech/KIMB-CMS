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



define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login();

//Add-on Konfiguration

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if( $_GET['todo'] == 'more' ){
	check_backend_login('more');
	
	if( isset( $_GET['addon'] ) ){

		$sitecontent->add_site_content('<h2>Ein Addon konfigurieren</h2>');

		if(strpos( $_GET['addon'] , "..") !== false){
			echo ('Do not hack me!!');
			die;
		}


		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/conf_more.php') ){
			require_once( __DIR__.'/../core/addons/'.$_GET['addon'].'/conf_more.php' );
		}
		else{
			$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
		}
	}
	else{
		$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th></th> </tr>');

		$addons = listaddons();
		foreach( $addons as $addon ){
			
			$sitecontent->add_site_content('<tr> <td><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon='.$addon.'">'.$addon.'</a></td> </tr>');

		}
		$sitecontent->add_site_content('</table>');
	}


}
elseif( $_GET['todo'] == 'less' ){

	if( isset( $_GET['addon'] ) ){

		$sitecontent->add_site_content('<h2>Ein Addon nutzen</h2>');

		if(strpos( $_GET['addon'] , "..") !== false){
			echo ('Do not hack me!!');
			die;
		}


		if( file_exists(__DIR__.'/../core/addons/'.$_GET['addon'].'/conf_less.php') ){
			require_once( __DIR__.'/../core/addons/'.$_GET['addon'].'/conf_less.php' );
		}
		else{
			$sitecontent->echo_error( 'Das gewählte Add-on wurde nicht gefunden!' , 'unknown');
		}
	}
	else{
		$sitecontent->add_site_content('<h2>Ein Addon wählen</h2>');
		$sitecontent->add_site_content('<table width="100%"><tr> <th></th> </tr>');

		$addons = listaddons();
		foreach( $addons as $addon ){
			
			$sitecontent->add_site_content('<tr> <td><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon='.$addon.'">'.$addon.'</a></td> </tr>');

		}
		$sitecontent->add_site_content('</table>');
	}

}
else{
	$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
}


$sitecontent->output_complete_site();
?>
