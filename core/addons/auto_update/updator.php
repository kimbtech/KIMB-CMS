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

if( !empty( $_GET['los'] ) && is_file( __DIR__.'/temp/'.$_GET['los'].'.zip' ) ){

	$zip = new ZipArchive;
	if( $zip->open( __DIR__.'/temp/'.$_GET['los'].'.zip' ) === TRUE ){
		$zip->extractTo( __DIR__.'/../../../' );
		$zip->close();

		unlink( __DIR__.'/temp/'.$_GET['los'].'.zip' );

		if( is_file( __DIR__.'/../../../update.php' ) ){
			require_once( __DIR__.'/../../../update.php' );
			unlink( __DIR__.'/../../../update.php' );
		}

		$sitecontent->echo_message( 'Das Update wurde erfogreich beendet!' );
		$sitecontent->echo_message( 'Es wurden alle Add-ons deaktiviert!' );
		$sitecontent->echo_message( 'Das Theme wurde auf "norm" gesetzt ( Bitte installieren Sie Ihr Theme neu, sollte es nach dem Themewechsel zu Darstellungsproblemen kommen )!' );

		$updatefile->write_kimb_replace( 'lasttime', '100' );

	}
	else {
		$sitecontent->echo_error( 'Die Datei konnte nicht entpackt werden!' );
	}
}
else{

	$updinf = json_decode( file_get_contents( 'http://api.kimb-technologies.eu/cms/getupdatelink.php?v='.$allgsysconf['build'] ) , true );

	if( $updinf['err'] == 'no' ){

		if( $updinf['link'] != 'none' ){

			$ufile = mt_rand();

			$src = fopen( $updinf['link'] , 'r');
			$dest = fopen( __DIR__.'/temp/'.$ufile.'.zip' , 'w+');
			if( !stream_copy_to_stream( $src, $dest ) ){
				$sitecontent->echo_error( 'Download des Updates nicht möglich!' );
			}
			else{
				$sitecontent->add_site_content('<b>Zusammenfassung:</b>');
				$sitecontent->add_site_content('<ul>');
				$sitecontent->add_site_content('<li>Update von: '.$updinf['von'].'</li>');
				$sitecontent->add_site_content('<li>Update zu: '.$updinf['zu'].'</li>');
				$sitecontent->add_site_content('</ul>');
				$sitecontent->add_site_content('<b style="color:red;">Ein Update birgt ein gewisses Risiko (z.B. CMS nicht mehr funktionstüchtig), bitte halten Sie ein Backup für den Fehlerfall bereit!</b>');
				$sitecontent->add_site_content('<div style="background-color:yellow; padding:20px; border-radius:15px; text-align:center;">');
				$sitecontent->add_site_content('<a href="'.$addonurl.'&los='.$ufile.'"><button>OK, trotzdem weiter!</button></a>');				
				$sitecontent->add_site_content('</div>');
			}

		}
		else{
			$sitecontent->echo_error( 'Es ist ein Fehler aufgetreten!' );
			$sitecontent->echo_message( '<b>Fehlermedlung</b>: Kein passendes Update gefunden!' );
			$sitecontent->echo_message( '<b>Lösungsansatz</b>: Das Update muss manuell installiert werden!' );
		}

	}
	else{
		$sitecontent->echo_error( 'Es ist ein Fehler aufgetreten!' );
		$sitecontent->echo_message( '<b>Fehlermedlung</b>: '.$updinf['userinfo'] );
		$sitecontent->echo_message( '<b>Lösungsansatz</b>: '.htmlentities( $updinf['idea'] ) );
	}
}
?>
