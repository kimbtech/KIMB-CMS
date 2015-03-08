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

if( $analytics['conf']['anatool'] == 'pimg' ){
	$sitecontent->add_site_content( $analytics['codes'] );
}
else{
	$sitecontent->add_html_header( $analytics['codes'] );
}

if( $analytics['conffile']->read_kimb_one( 'infobann' ) == 'on' ){
	$sitecontent->add_html_header( '<style>'.$analytics['conffile']->read_kimb_one( 'ibcss' ).'</style>' );
	$sitecontent->add_html_header( '<script type="text/javascript">
	$(function() {
		if (document.cookie.indexOf("info") >= 0){
		
		}
		else {
			$( "#analysehinweis" ).css( "display" , "block" );
			document.cookie = "info=analyse; path=/;";
		}
	});
	</script>');

	$sitecontent->add_site_content( '<div id="analysehinweis" style="display:none;">' );
	$sitecontent->add_site_content( $analytics['conffile']->read_kimb_one( 'ibtext' ) );	
	$sitecontent->add_site_content( '<button onclick="$( \'#analysehinweis\' ).css( \'display\' , \'none\' );">OK</button></p> ' );
	$sitecontent->add_site_content( '</div> ' );
}
	
?>
