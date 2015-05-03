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

if( $allgerr != '404' ){
	if( check_for_kimb_file( '/site/site_'.$allgsiteid.'.kimb' ) ){
		$sitefile = new KIMBdbf( '/site/site_'.$allgsiteid.'.kimb' );
	}
	else{
		$sitecontent->echo_error( 'Diese Seite existiert nicht!' , '404' );
		$allgerr = '404';
	}
}

if( $allgerr == '403' ){
	$sitecontent->echo_error( 'Sie haben keinen Zugriff auf diese Seite!' , '403' );
}
elseif( is_object( $sitefile ) && !isset( $allgerr ) ){
	
	if( $allgsysconf['lang'] == 'on' && $requestlang['id'] != 0 ){
			
		$dbftag['title'] = 'title-'.$requestlang['id'];
		$dbftag['keywords'] = 'keywords-'.$requestlang['id'];
		$dbftag['description'] = 'description-'.$requestlang['id'];
		$dbftag['inhalt'] = 'inhalt-'.$requestlang['id'];
		$dbftag['footer'] = 'footer-'.$requestlang['id'];
		
		if( empty($sitefile->read_kimb_one( $dbftag['title'] )) && empty($sitefile->read_kimb_one( $dbftag['inhalt'] )) ){
			$sitecontent->echo_error( 'Achtung: Diese Seite ist nicht in der von Ihnen gewünschten Sprache verfügbar!' );
			$normtags = true;
		}
	}
	else{
		$normtags = true;
	}
	
	if( $normtags ){
		$dbftag['title'] = 'title';
		$dbftag['keywords'] = 'keywords';
		$dbftag['description'] = 'description';
		$dbftag['inhalt'] = 'inhalt';
		$dbftag['footer'] = 'footer';	
	}
		
	$seite['title'] = $sitefile->read_kimb_one( $dbftag['title'] );
	$seite['header'] = $sitefile->read_kimb_one( 'header' );
	$seite['keywords'] = $sitefile->read_kimb_one( $dbftag['keywords'] );
	$seite['description'] = $sitefile->read_kimb_one( $dbftag['description'] );
	$seite['inhalt'] = $sitefile->read_kimb_one( $dbftag['inhalt'] );
	$seite['time'] = $sitefile->read_kimb_one( 'time' );
	$seite['made_user'] = $sitefile->read_kimb_one( 'made_user' );
	$seite['footer'] = $sitefile->read_kimb_one( $dbftag['footer'] );
	$seite['req_id'] = $_GET['id'];

	$sitecontent->add_site($seite);

}
elseif( !isset( $allgerr ) ){
	$sitecontent->echo_error( 'Fehler beim Erstellen des Seiteninhalts !' );
	$allgerr = 'unknown';
}
?>
