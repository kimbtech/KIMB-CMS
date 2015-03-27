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

$sitecontent->add_site_content( '<form method="post" action="">' );
$sitecontent->add_site_content( '<input type="text" name="search" placeholder="Suchbegriff" value="'.htmlentities( $begriff ).'">' );
$sitecontent->add_site_content( '<input type="submit" value="Suchen">' );
$sitecontent->add_site_content( '</form>' );

if( !empty( $begriff ) ){

	$anzahl = 0;

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	make_menue_array();

	foreach( $menuearray as $arr ){
		$seachlist[] = array( 'siteid' => $arr['siteid'], 'requestid' => $arr['requid'], 'menuename' => $arr['menuname'] );
	}

	$maxveruch = $search_sitemap['file']->read_kimb_one( 'maxversuch' );
	$maxerg = $search_sitemap['file']->read_kimb_one( 'maxerg' );

	$parts = array( 'name', 'content' );
	$foundsites = array();

	foreach( $parts as $part ){
		foreach( $seachlist as $teil ){

			if( !in_array( $teil['requestid'], $foundsites ) ){
				if( $part == 'name' ){
					$string = $teil['menuename'];
					$requid = $teil['requestid'];
					$sitename = $teil['menuename'];
					$id = $teil['siteid'];
				}
				elseif( $part == 'content' ){

					$file = new KIMBdbf( 'site/site_'.$teil['siteid'].'.kimb' );				;
					$requid = $teil['requestid'];
					$sitename = $teil['menuename'];
					$inhalt = $file->read_kimb_one( 'inhalt' );
					$string = strip_tags( $inhalt );
				}

				if( stripos ( $string , $begriff ) !== false ){

					if( !isset( $inhalt ) ){
						$file = new KIMBdbf( 'site/site_'.$id.'.kimb' );
						$inhalt = $file->read_kimb_one( 'inhalt' );
					}
					$inhalt = strip_tags( $inhalt );

					$resultate .= '<li style="margin:5px; background-color:#ddd;">';
					$resultate .= '<b><u><a href="'.$allgsysconf['siteurl'].'/index.php?id='.$requid.'" target="_blank">'.$sitename.'</a></b></u><br />';
					$resultate .= substr( $inhalt , '0' , '100' ).' ...';
					$resultate .= '</li>';

					$foundsites[] = $teil['requestid'];

					$anzahl++;
					$versuch++;
				}
				else{
					$versuch++;
				}

				unset( $inhalt );
			}

			if( $versuch >= $maxveruch || $anzahl >= $maxerg ){
			
				$anzahl .= ' +';
				$break = 'yes';
				break;
			}
		}

		if( isset( $break ) ){
			break;
		}
	}

	$sitecontent->add_site_content( '<h2>Resultate der Suche</h2>' );
	$sitecontent->add_site_content( '<hr />' );
	$sitecontent->add_site_content( 'Anzahl der Ergebnisse: '.$anzahl );
	$sitecontent->add_site_content( '<br /><br />' );
	$sitecontent->add_site_content( '<ul>' );
	$sitecontent->add_site_content( $resultate );
	$sitecontent->add_site_content( '<ul>' );
}

?>
