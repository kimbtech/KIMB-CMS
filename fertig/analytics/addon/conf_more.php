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

require_once( __DIR__.'/tracking_codes.php' );

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=analytics';

$sitecontent->add_site_content('<hr /><br /><h2>Piwik &amp; Google Analytics</h2>');

$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

if( isset( $_POST['send'] ) ){

	$array[0] = array( 'sys' => 'anatool' , 'infob' => 'infobann' , 'css' => 'ibcss' , 'text' => 'ibtext' );
	$array[1] = array( 'url' => 'url' , 'pid' => 'id' , 'piwikno' => 'pimg' );
	$array[2] = array( 'gaid' => 'id' );

	$examples = array( 'sys' => 'p' , 'infob' => 'on' , 'css' => 'div#analysehinweis{position:fixed; left:0; top:0; width:100%; background-color:orange;}' , 'text' => '<p>Diese Seite nutzt Cookies und einen Webanalysedienst. Mit der Nutzung dieser Seite erklären Sie sich damit einverstanden.<i>Weitere Informationen: <a href="http://www.example.com/" target="_blank" style="color:#ffffff; text-decoration:underline;">Impressum &amp; Datenschutz</a>!</i><br>' , 'url' => 'http://example.com/piwik' , 'pid' => '2' , 'piwikno' => 'on' , 'gaid' => 'UA-123456-1' );

	foreach( $array as $k1 => $v1 ){
		if( $k1 == 0 ){
			foreach( $v1 as $k2 => $v2 ){
				$inhalt = $analytics['conffile']->read_kimb_one( $v2 );
				if( $inhalt != $_POST[$k2] || empty( $_POST[$k2] ) ){
					if( empty( $_POST[$k2] ) ){
						$_POST[$k2] = $examples[$k2];
					}

					if( empty( $inhalt ) ){
						$analytics['conffile']->write_kimb_new( $v2 , $_POST[$k2] );
					}
					else{
						$analytics['conffile']->write_kimb_replace( $v2 , $_POST[$k2] );
					}
					$sitecontent->echo_message( 'Der Wert "'.$v2.'" wurde geändert!' );
				}
			}
		}
		else{
			foreach( $v1 as $k2 => $v2 ){
				$inhalt = $analytics['conffile']->read_kimb_id( $k1 , $v2 );
				if( $inhalt != $_POST[$k2] || empty( $_POST[$k2] ) ){
					if( empty( $_POST[$k2] ) ){
						$_POST[$k2] = $examples[$k2];
					}

					$analytics['conffile']->write_kimb_id( $k1 , 'add' , $v2 , $_POST[$k2] );
					$sitecontent->echo_message( 'Der Wert "'.$v2.'" wurde geändert!' );
				}
			}

		}
	}
}

$syss['anatool'] = array( 'p' => 'p' , 'pimg' => 'pimg' , 'ga' => 'ga' );
$syss['infobann'] = array( 'on' => 'ion' , 'off' => 'ioff' );
$syss[1] = array( 'on' => 'pnon' , 'off' => 'pnoff' );

foreach( $syss as $id => $value ){
	if( $id == 1 ){
		$inhalt = $analytics['conffile']->read_kimb_id( $id , 'pimg' );
	}
	else{
		$inhalt = $analytics['conffile']->read_kimb_one( $id );
	}

	foreach( $value as $iid => $vvalue ){
		if( $iid == $inhalt ){
			$sys[$vvalue] = 'checked="checked"';
		}
		else{
			$sys[$vvalue] = ' ';
		}
	}
}


$vals[0] = array( 'ibcss' => 'css' , 'ibtext' => 'text' );
$vals[1] = array( 'id' => 'pid' , 'url' => 'url' );
$vals[2] = array( 'id' => 'gaid' );

foreach( $vals as $key => $vval ){
	if( $key == 0 ){
		foreach( $vval as $kkkey => $vvval ){
			$val[$vvval] = $analytics['conffile']->read_kimb_one( $kkkey );
		}
	}
	else{
		foreach( $vval as $kkkey => $vvval ){
			$val[$vvval] = $analytics['conffile']->read_kimb_id( $key , $kkkey );
		}	
	}
}

$sitecontent->add_site_content('<h3>Allgemein</h3>');
$sitecontent->add_site_content('<input name="sys" value="p" type="radio" '.$sys['p'].'> Piwik<span style="display:inline-block;" title="Piwik normal verwenden" class="ui-icon ui-icon-info"></span>' );
$sitecontent->add_site_content('<input name="sys" value="pimg" type="radio" '.$sys['pimg'].'> Piwik Img<span style="display:inline-block;" title="Piwik nur mit Bild verwenden (weniger Daten, kein JavaScript nötig)" class="ui-icon ui-icon-info"></span>' );
$sitecontent->add_site_content('<input name="sys" value="ga" type="radio" '.$sys['ga'].'> Google Analytics<span style="display:inline-block;" title="Google Analytics" class="ui-icon ui-icon-info"></span><br />' );

$sitecontent->add_site_content('<h3>Infobanner</h3>');
$sitecontent->add_site_content('<input name="infob" value="on" type="radio" '.$sys['ion'].'><span style="display:inline-block;" title="Infobanner bei erstem Seitenaufruf anzeigen ( rechtlich teilweise nötig um über Cookies und Webanalyse zu infomieren )" class="ui-icon ui-icon-check"></span>' );
$sitecontent->add_site_content('<input name="infob" value="off" type="radio" '.$sys['ioff'].'><span style="display:inline-block;" title="Kein Infobanner anzeigen" class="ui-icon ui-icon-closethick"></span><br />' );
$sitecontent->add_site_content('<textarea name="css" style="width:75%; height:70px;" >'.$val['css'].'</textarea> CSS<b title="CSS Code für den Infobanner ( leer => Voreinstellung )">*</b><br />');
$sitecontent->add_site_content('<textarea name="text" style="width:75%; height:70px;" >'.$val['text'].'</textarea> Text<b title="Text des Infobanners ( leer => Voreinstellung , Achtung: Link zum Impressum einfügen)">*</b><br />');

$sitecontent->add_site_content('<h3>Piwik <b title="Diese Einstelungen müssen Sie nur ändern wenn Sie Piwik verwenden!">*</b></h3>');
$sitecontent->add_site_content('<input name="url" value="'.$val['url'].'" type="text"> URL<b title="Bitte geben Sie hier die URL zu Ihrer Piwik-Installation an. (z.B.: http://example.com/piwik)">*</b><br />' );
$sitecontent->add_site_content('<input name="pid" value="'.$val['pid'].'" type="text"> ID<b title="Bitte geben Sie hier die ID der Seite an. ( Administration => Webseiten => ID )">*</b><br />' );
$sitecontent->add_site_content('<input name="piwikno" value="on" type="radio" '.$sys['pnon'].'><span style="display:inline-block;" title="Wenn der User kein JavaScript aktiviert hat, das Piwik-Tracking per Bild nutzen." class="ui-icon ui-icon-check"></span>' );
$sitecontent->add_site_content('<input name="piwikno" value="off" type="radio" '.$sys['pnoff'].'><span style="display:inline-block;" title="Kein alternatives Tracking per Bild." class="ui-icon ui-icon-closethick"></span><br />' );

$sitecontent->add_site_content('<h3>Google Analytics <b title="Diese Einstelungen müssen Sie nur ändern wenn Sie Google Analytics verwenden!">*</b></h3>');
$sitecontent->add_site_content('<input name="gaid" value="'.$val['gaid'].'" type="text"> Google Analytics Property<b title="Bitte geben Sie hier die Google Analytics Property für diese Seite an! (z.B.: UA-123456-1 )">*</b><br />' );

$sitecontent->add_site_content('<br /><br /><input type="hidden" value="send" name="send"><input type="submit" value="Speichern"> </form>');


?>
