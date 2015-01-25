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


if( !is_dir( __DIR__.'/../captcha/' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" wurde nicht gefunden, bitte installieren und aktivieren Sie es um eine ordnungsgemäße Funktion von "guestbook" zu gewährleisten! ' , 'unknown' );

}
elseif( !$addoninclude->read_kimb_search_teilpl( 'ajax' , 'captcha' ) ){

	$sitecontent->echo_error( 'Das Add-on "captcha" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um eine ordnungsgemäße Funktion von "guestbook" zu gewährleisten! ' , 'unknown' );

}

if( !is_dir( __DIR__.'/../felogin/' ) ){

	$sitecontent->echo_message( 'Das Add-on "felogin" wurde nicht gefunden, bitte installieren und aktivieren Sie es um einen erweiterten Funktionsumfang von "guestbook" zu erhalten! ' , 'unknown' );

}
elseif( !$addoninclude->read_kimb_search_teilpl( 'first' , 'felogin' ) ){

	$sitecontent->echo_message( 'Das Add-on "felogin" scheint nicht aktiviert zu sein, bitte aktivieren Sie es um einen erweiterten Funktionsumfang von "guestbook" zu erhalten! ' , 'unknown' );

}


?>
