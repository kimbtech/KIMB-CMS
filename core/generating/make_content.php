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



defined('KIMB_CMS') or die('No clean Request');

if( check_for_kimb_file( '/site/site_'.$allgsiteid.'.kimb' ) ){
	$sitefile = new KIMBdbf( '/site/site_'.$allgsiteid.'.kimb' );
}
else{
	$sitecontent->echo_error( 'Diese Seite existiert nicht!' , '404' );
	$allgerr = '404';
}

if( $allgerr == '403' ){
	$sitecontent->echo_error( 'Sie haben keinen Zugriff auf diese Seite!' , '403' );
}
elseif( is_object( $sitefile ) && !isset( $allgerr ) ){

	$seite['title'] = $sitefile->read_kimb_one( 'title' );
	$seite['header'] = $sitefile->read_kimb_one( 'header' );
	$seite['keywords'] = $sitefile->read_kimb_one( 'keywords' );
	$seite['description'] = $sitefile->read_kimb_one( 'description' );
	$seite['inhalt'] = $sitefile->read_kimb_one( 'inhalt' );
	$seite['time'] = $sitefile->read_kimb_one( 'time' );
	$seite['made_user'] = $sitefile->read_kimb_one( 'made_user' );
	$seite['footer'] = $sitefile->read_kimb_one( 'footer' );
	$seite['req_id'] = $_GET['id'];

	$sitecontent->add_site($seite);

}
elseif( !isset( $allgerr ) ){
	$sitecontent->echo_error( 'Fehler beim Erstellen des Seiteninhalts !' );
	$allgerr = 'unknown';
}
?>
