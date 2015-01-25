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

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&amp;addon=kontakt';

$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );

if( is_numeric( $_POST['id'] ) && $_POST['id'] != '' ){

	$siteid = $kontakt['file']->read_kimb_one( 'siteid' );
	if( $_POST['id'] != $siteid ){
		if( $siteid == '' ){
			$kontakt['file']->write_kimb_new( 'siteid' , $_POST['id'] );
		}
		else{
			$kontakt['file']->write_kimb_replace( 'siteid' , $_POST['id'] );
		}

		$sitecontent->echo_message( 'Die SiteID wurde geändert!' );
	}

}
if( $_POST['mailoo'] == 'on' || $_POST['mailoo'] == 'off' ){

	$new = $_POST['mailoo'];
	$param = 'mail';
	$oo = $kontakt['file']->read_kimb_one( $param );
	if( $new != $oo ){
		if( $oo == '' ){
			$kontakt['file']->write_kimb_new( $param , $new );
		}
		else{
			$kontakt['file']->write_kimb_replace( $param , $new );
		}

	}

}
if( $_POST['formoo'] == 'on' || $_POST['formoo'] == 'off' ){

	$new = $_POST['formoo'];
	$param = 'form';
	$oo = $kontakt['file']->read_kimb_one( $param );
	if( $new != $oo ){
		if( $oo == '' ){
			$kontakt['file']->write_kimb_new( $param , $new );
		}
		else{
			$kontakt['file']->write_kimb_replace( $param , $new );
		}

	}
}
if( $_POST['otheroo'] == 'on' || $_POST['otheroo'] == 'off' ){

	$new = $_POST['otheroo'];
	$param = 'other';
	$oo = $kontakt['file']->read_kimb_one( $param );
	if( $new != $oo ){
		if( $oo == '' ){
			$kontakt['file']->write_kimb_new( $param , $new );
		}
		else{
			$kontakt['file']->write_kimb_replace( $param , $new );
		}

	}
}
if( isset( $_POST['mail'] ) && $_POST['mail'] != '' ){
	$mail = $kontakt['file']->read_kimb_one( 'formaddr' );
	if( $_POST['mail'] != $mail ){
		if( $mail == '' ){
			$kontakt['file']->write_kimb_new( 'formaddr' , $_POST['mail'] );
		}
		else{
			$kontakt['file']->write_kimb_replace( 'formaddr' , $_POST['mail'] );
		}

		$oldname = $kontakt['file']->read_kimb_one( 'bildname' );
		$name = mt_rand();
		if( $oldname == '' ){
			$kontakt['file']->write_kimb_new( 'bildname' , $name );
		}
		else{
			unlink( __DIR__.'/../../../load/addondata/kontakt/'.$oldname.'.png' );
			$kontakt['file']->write_kimb_replace( 'bildname' , $name );
		}
		
		$string = $_POST['mail'];
		$im = imagecreate (400, 30);
		imagecolorallocate( $im , 255 , 255 , 255 );
		$color = imagecolorallocate( $im , 0 , 0 , 0 );
		imagettftext ($im, 20, 0, 5, 25, $color, __DIR__.'/Ubuntu-B.ttf', $string );
		imagepng( $im , __DIR__.'/../../../load/addondata/kontakt/'.$name.'.png' );
		imagedestroy( $im );

		$sitecontent->echo_message( 'Die E-Mail-Adresse wurde geändert!' );
	}
}
if( isset( $_POST['othercont'] ) && $_POST['othercont'] != '' ){

	$cont = $kontakt['file']->read_kimb_one( 'othercont' );
	if( $_POST['othercont'] != $cont ){
		if( $cont == '' ){
			$kontakt['file']->write_kimb_new( 'othercont' , $_POST['othercont'] );
		}
		else{
			$kontakt['file']->write_kimb_replace( 'othercont' , $_POST['othercont'] );
		}

		$sitecontent->echo_message( 'Der über JavaScript gesicherter Inhalt wurde geändert!' );
	}

}


$ch = array( ' ' , ' ' , ' ' , ' ' , ' ' , ' ' );

if( $kontakt['file']->read_kimb_one( 'mail' ) == 'on' ){
	$ch[2] = ' checked="checked" ';
}
else{
	$ch[1] = ' checked="checked" ';
}
if( $kontakt['file']->read_kimb_one( 'form' ) == 'on' ){
	$ch[4] = ' checked="checked" ';
}
else{
	$ch[3] = ' checked="checked" ';
}
if( $kontakt['file']->read_kimb_one( 'other' ) == 'on' ){
	$ch[6] = ' checked="checked" ';
}
else{
	$ch[5] = ' checked="checked" ';
}

$sitecontent->add_html_header('<script>
$(function() { 
	nicEditors.allTextAreas({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'});
});
</script>');

$sitecontent->add_site_content('<br /><br /><form action="'.$addonurl.'" method="post" >');

$sitecontent->add_html_header('<script>$(function(){ $( "[name=id]" ).val( '.$kontakt['file']->read_kimb_one( 'siteid' ).' ); }); </script>');

$sitecontent->add_site_content(id_dropdown( 'id', 'siteid' ).' ( SiteID <b title="Bitte geben Sie hier die Seite an, auf welcher die Kontaktinfos erscheinen sollen.">*</b> )<br />');
$sitecontent->add_site_content('<input type="radio" name="mailoo" value="off"'.$ch[1].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse deaktiviert" class="ui-icon ui-icon-closethick"></span> <input type="radio" name="mailoo" value="on"'.$ch[2].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse aktiviert" class="ui-icon ui-icon-check"></span> (E-Mail-Adresse)<br />');
$sitecontent->add_site_content('<input type="radio" name="formoo" value="off"'.$ch[3].'> <span style="display:inline-block;" title="Kontakformular deaktiviert" class="ui-icon ui-icon-closethick"></span> <input type="radio" name="formoo" value="on"'.$ch[4].'> <span style="display:inline-block;" title="Kontaktformular aktiviert" class="ui-icon ui-icon-check"></span> (Kontaktformular)<br />');
$sitecontent->add_site_content('<input type="radio" name="otheroo" value="off"'.$ch[5].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt deaktiviert" class="ui-icon ui-icon-closethick"></span> <input type="radio" name="otheroo" value="on"'.$ch[6].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt aktiviert" class="ui-icon ui-icon-check"></span> (JavaScript Inhalt)<br /><br />');
$sitecontent->add_site_content('<input name="mail" type="text" value="'.$kontakt['file']->read_kimb_one( 'formaddr' ).'" > ( Mail-Adresse <b title="Die Adresse wird, wenn aktiviert, als Bild auf der Seite angezeigt und für das Kontaktformular genutzt!">*</b>)<br />');
$sitecontent->add_site_content('<textarea name="othercont" style="width:99%;">'.$kontakt['file']->read_kimb_one( 'othercont' ).'</textarea> ( Über JavaScript gesicherter Inhalt &uarr; <b title="Der Text wird so nachgeladen, dass es für Bots schwer ist ihn zu lesen, so lassen sich z.B. Telefonnummern und Adressen schützen!">*</b>)<br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern"> </form>');


?>
