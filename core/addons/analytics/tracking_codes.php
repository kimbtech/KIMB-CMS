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

//Konfiguration laden und Tracking Codes generieren
$analytics['conffile'] = new KIMBdbf( 'addon/analytics__conf.kimb' );

$allconft = array( 'anatool', 'infobann', 'ibcss', 'ibtext' );
foreach( $allconft as $conf ){
	$analytics['conf'][$conf] = $analytics['conffile']->read_kimb_one( $conf );
}
unset( $allconft );

if( $analytics['conf']['anatool'] == 'p' ){
	$analytics['toold'] = $analytics['conffile']->read_kimb_id( '1' );

	$analytics['codes'] = '
		<!-- Piwik Code -->
		<script type="text/javascript">
		  var _paq = _paq || [];
		  _paq.push(["setDoNotTrack", true]);
		  _paq.push(["trackPageView"]);
		  _paq.push(["enableLinkTracking"]);
		  (function() {
		    var u="' . $analytics['toold']['url'] . '/";
		    _paq.push(["setTrackerUrl", u+"piwik.php"]);
		    _paq.push(["setSiteId", ' . $analytics['toold']['id'] . ']);
		    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];
		    g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
		  })();
		</script>
		<!-- End Piwik Code -->
	';

	$analytics['toold']['pimgcode'] = '
		<noscript><img src="' . $analytics['toold']['url'] . '/piwik.php?idsite=' . $analytics['toold']['id'] . '&amp;rec=1" style="border:0" alt="" /></noscript>
	';
}
elseif( $analytics['conf']['anatool'] == 'pimg' ){
	$analytics['toold'] = $analytics['conffile']->read_kimb_id( '1' );

	$analytics['codes'] = '
		<img src="' . $analytics['toold']['url'] . '/piwik.php?idsite=' . $analytics['toold']['id'] . '&amp;rec=1" style="border:0" alt="" />
	';
}
elseif( $analytics['conf']['anatool'] == 'ga' ){
	$analytics['toold'] = $analytics['conffile']->read_kimb_id( '2' );

	$analytics['codes'] = "
		<!-- Google Analytics -->
		<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', '" . $analytics['toold']['id'] . "', 'auto');
		ga('send', 'pageview');

		</script>
		<!-- End Google Analytics -->
	";
}
else{
	$sitecontent->echo_error( 'Das Add-on Analytics ist fehlerhaft konfiguriert.' );
}

?>