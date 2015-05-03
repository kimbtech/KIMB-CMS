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

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');
//BE Klassen
require_once(__DIR__.'/../core/oop/be_do/be_do_all.php');

check_backend_login( 'twentytwo' , 'more');

$sitecontent->add_site_content('<h2>Easy Menue <span class="ui-icon ui-icon-info" title="Erlauben Sie Usern, welche nur Seiten erstellen können, zu einer Seite ein Menue zu erstellen!" style="display:inline-block;"></span></h2>');

$easyfile = new KIMBdbf( 'backend/easy_menue.kimb' );

//Änderungen speichern
if( !empty( $_POST['oo'] ) || !empty( $_POST['stat'] ) ){
	
	//Status und on/off bei Änderung speichern
	$dos = array( 'oo' => 'Easy Menue on/off', 'stat' => 'Der Status des neuen Menues' );
	foreach( $dos as $do => $name ){
		if( $easyfile->read_kimb_one( $do ) != $_POST[$do] ){
			$easyfile->write_kimb_one( $do, $_POST[$do] );
			
			$sitecontent->echo_message( $name.' wurde angepasst.' );
		}
	}
	
	//Menuewahl bei Änderung speichern
	$dos = array( 'same', 'deeper' );
	foreach( $dos as $do ){
		$easyfile->write_kimb_teilpl_del_all( $do );
		foreach( $_POST[$do] as $wert ){
			if( $do == 'deeper' ){
				$array = explode( '||', $wert );
			}
			if( ( ( $wert == 'first' || is_numeric( $wert ) ) && $do == 'same' )
				|| ( $do == 'deeper' && is_numeric( $array[0] ) && ( $array[1] == 'first' || is_numeric( $array[1] ) ) )
			 ){
				 $easyfile->write_kimb_teilpl($do, $wert, 'add');
			}
		}
	}
}

//Status und on/off laden
$checked = array( 'ooon' => '', 'oooff' => 'checked="checked"', 'staton' => 'checked="checked"', 'statoff' => '' );
$dos = array( 'oo', 'stat' );
foreach( $dos as $do ){
	$wert = $easyfile->read_kimb_one( $do );
	if( $wert == 'on' ){
		$checked[$do.'on'] = 'checked="checked"';
		$checked[$do.'off'] = '';
	}
	elseif( $wert == 'off' ){
		$checked[$do.'off'] = 'checked="checked"';
		$checked[$do.'on'] = '';
	}
}

//Menuewahl laden
$dos = array( 'same', 'deeper' );
foreach( $dos as $do ){
	foreach( $easyfile->read_kimb_all_teilpl( $do ) as $wert ){
		$checked[$do][$wert] = 'checked="checked"';
	} 
}

$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_easymenue.php" method="post">');

$sitecontent->add_site_content('<br /><h4>De-/ Aktivieren</h4>');
$sitecontent->add_site_content('<input type="radio" name="oo" value="on" '.$checked['ooon'].'> <span style="display:inline-block;" title="Easy Menue aktivieren!" class="ui-icon ui-icon-check"></span>');
$sitecontent->add_site_content('<input type="radio" name="oo" value="off" '.$checked['oooff'].'> <span style="display:inline-block;" title="Easy Menue deaktivieren!" class="ui-icon ui-icon-closethick"></span>');

$sitecontent->add_site_content('<br /><h4>Status des neuen Menues</h4>');
$sitecontent->add_site_content('<input type="radio" name="stat" value="on" '.$checked['staton'].'> <span style="display:inline-block;" title="Das neue Menue auf den Status &apos;on&apos; setzen!" class="ui-icon ui-icon-check"></span>');
$sitecontent->add_site_content('<input type="radio" name="stat" value="off" '.$checked['statoff'].'> <span style="display:inline-block;" title="Das neue Menue auf den Status &apos;off&apos; setzen!" class="ui-icon ui-icon-closethick"></span>');

$sitecontent->add_site_content('<br /><h4>Erlaubte Menues</h4><table>');
$sitecontent->add_site_content('<th></th> <th>Menü <b title="Menü auf diesem Niveau erlauben">*</b></th> <th>Untermenü <b title="Untermenü erlauben ( Ein Untermenü kann nur einmal erstellt werden, danach wird es automatisch als Menü gelistet. )">*</b></th>');

$menuearray =  make_menue_array_helper();

foreach( $menuearray as $menue ){
	
	$out = array();
	
	$out['niveau'] = str_repeat( '==>' , $menue['niveau'] );
	$out['name'] = $menue['menuname'];
	if( empty( $menue['nextid'] ) ){
		$out['deeper'] = '<input type="checkbox" name="deeper[]" value="'.$menue['requid'].'||'.$menue['fileid'].'" '.$checked['deeper'][$menue['requid'].'||'.$menue['fileid']].'>';
	}
	if( !in_array( $menue['fileid'], $filearr ) ){
		$out['same'] = '<input type="checkbox" name="same[]" value="'.$menue['fileid'].'" '.$checked['same'][$menue['fileid']].'>';
	}
	$filearr[] = $menue['fileid'];
		
	$sitecontent->add_site_content('<tr><td>'.$out['niveau'].' <i>'.$out['name'].'</i></td><td>'.$out['same'].'</td><td>'.$out['deeper'].'</td></tr>' );
	
}

$sitecontent->add_site_content('</table>');

$sitecontent->add_site_content('<input type="submit" value="Speichern"></form>');


//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
