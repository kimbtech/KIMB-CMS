<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS Add-on
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

defined('KIMB_Backend') or die('No clean Request');

$sitecontent->add_html_header('<script>
$(function() { 
	new nicEditor({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'}).panelInstance( \'nicedit1\'); 
	new nicEditor({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'}).panelInstance( \'nicedit2\');
	new nicEditor({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'}).panelInstance( \'nicedit3\');
	new nicEditor({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'}).panelInstance( \'nicedit4\');
});
</script>');


$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=html_out';
if( !is_object( $html_out['file'] ) ){
	$html_out['file'] = new KIMBdbf( 'addon/html_out__file.kimb' );
}
if( !is_object( $html_out['cont'] ) ){
	$html_out['cont'] = new KIMBdbf( 'addon/html_out__contfe.kimb' );
}

$sitecontent->add_site_content('<hr /><h2>"html_out" - Frontend Ausgaben</h2>');

if( isset( $_POST['onoff'] ) ){

	if( $_POST['onoff'] == 'on' || $_POST['onoff'] == 'off' ){

		if( $_POST['onoff'] == 'on' && $html_out['file']->read_kimb_one( 'fe' ) != 'all' ){
			$html_out['file']->write_kimb_replace( 'fe' , 'all' );
			$sitecontent->echo_message( 'Ausgaben aktiviert!' );
		}
		elseif( $_POST['onoff'] == 'off' && $html_out['file']->read_kimb_one( 'fe' ) == 'all' ){
			$html_out['file']->write_kimb_replace( 'fe' , 'none' );
			$sitecontent->echo_message( 'Ausgaben deaktiviert!' );
		}

		$allteile = array( 'sitefi' , 'sitese' , 'addonfi' , 'addonse' , 'header' , 'title' );

		foreach( $allteile as $teil ){	
			$dings = $html_out['cont']->read_kimb_one( $teil );
			if( $_POST[$teil] == '<br>' ){
				$_POST[$teil] = '';
			}
			if( $dings == '' && $_POST[$teil] != '' ){
				$html_out['cont']->write_kimb_new( $teil , $_POST[$teil] );
				$sitecontent->echo_message( '"'.$teil.'" hinzugefügt!' );
			}
			elseif( $dings != $_POST[$teil]  && $_POST[$teil] != '' ){
				$html_out['cont']->write_kimb_replace( $teil , $_POST[$teil] );
				$sitecontent->echo_message( '"'.$teil.'" geändert!' );
			}
			elseif( $_POST[$teil] == '' ){
				$html_out['cont']->write_kimb_delete( $teil );
				$sitecontent->echo_message( '"'.$teil.'" entfernt!' );
			}
		}
	}
	else{
		$sitecontent->echo_message( 'Fehler' , 'unknown' );
	}
}

if( $html_out['file']->read_kimb_one( 'fe' ) == '' ){
	$html_out['file']->write_kimb_new( 'fe' , 'all' );
}

if( $html_out['file']->read_kimb_one( 'fe' ) != 'all' ){
	$off = ' checked="checked" ';
	$on = ' ';
}
else{
	$on = ' checked="checked" ';
	$off = ' ';
}


$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');


$sitecontent->add_site_content('<input name="onoff" type="radio" value="off" '.$off.'><span style="display:inline-block;" title="Ausgabe deaktiviert" class="ui-icon ui-icon-closethick"></span><input name="onoff" value="on" type="radio" '.$on.'><span style="display:inline-block;" title="Ausgabe aktiviert" class="ui-icon ui-icon-check"></span><br />');

$sitecontent->add_site_content('<textarea name="sitefi" id="nicedit1" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'sitefi' ).'</textarea> (Zusätzlicher Seiteninhalt oben &uarr; )<br />');

$sitecontent->add_site_content('<textarea name="sitese" id="nicedit2" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'sitese' ).'</textarea> (Zusätzlicher Seiteninhalt unten &uarr; )<br />');

$sitecontent->add_site_content('<textarea name="addonfi" id="nicedit3" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'addonfi' ).'</textarea> (Zusätzliche Add-onausgabe oben &uarr; )<br />');

$sitecontent->add_site_content('<textarea name="addonse" id="nicedit4" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'addonse' ).'</textarea> (Zusätzliche Add-onausgabe unten &uarr; )<br />');

$sitecontent->add_site_content('<textarea name="header" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'header' ).'</textarea> (Zusätzlicher Header &uarr; )<br />');

$sitecontent->add_site_content('<input name="title" style="width:60%;" value="'.$html_out['cont']->read_kimb_one( 'title' ).'" > (Allgemeiner Titel)<br />');


$sitecontent->add_site_content('<input type="submit" value="Ändern"> </form>');

?>
