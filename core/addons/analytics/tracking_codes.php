<?php
/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies
/*************************************************/
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License version 3
//published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.
/*************************************************/
//www.KIMB-technologies.eu
//www.bitbucket.org/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//Konfiguration laden und Tracking Codes generieren
$analyticsconffile = new KIMBdbf( 'addon/analytics__conf.kimb' );

//Konf Werte lesen
$allconft = array( 'anatool', 'infobann', 'ibcss', 'ibtext' );
foreach( $allconft as $conf ){
	$analytics['conf'][$conf] = $analyticsconffile->read_kimb_one( $conf );
}
unset( $allconft );

//Piwik normal?
if( $analytics['conf']['anatool'] == 'p' ){
	$analytics['toold'] = $analyticsconffile->read_kimb_id( '1' );

	$analytics['codes'] = '<!-- Piwik Code -->
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
<!-- End Piwik Code -->';

	$analytics['toold']['pimgcode'] = '<noscript><img src="' . $analytics['toold']['url'] . '/piwik.php?idsite=' . $analytics['toold']['id'] . '&amp;rec=1" style="border:0" alt="" /></noscript>';
}
//Piwik per Bild?
elseif( $analytics['conf']['anatool'] == 'pimg' ){
	$analytics['toold'] = $analyticsconffile->read_kimb_id( '1' );

	$analytics['codes'] = '<img src="' . $analytics['toold']['url'] . '/piwik.php?idsite=' . $analytics['toold']['id'] . '&amp;rec=1" style="border:0" alt="" />';
}
//Google Analytics?
elseif( $analytics['conf']['anatool'] == 'ga' ){
	$analytics['toold'] = $analyticsconffile->read_kimb_id( '2' );

	$analytics['codes'] = "<!-- Google Analytics -->
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', '" . $analytics['toold']['id'] . "', 'auto');
	ga('send', 'pageview');
</script>
<!-- End Google Analytics -->";
}
//Fehler
else{
	$sitecontent->echo_error( 'Das Add-on Analytics ist fehlerhaft konfiguriert.' );
}

//Infobanner?
if( $analyticsconffile->read_kimb_one( 'infobann' ) == 'on' ){
		
	//Banner nur anzeigen, wenn Cookie nicht vorhanden, also noch nicht okay
	if( !isset( $_COOKIE['analytics'] ) || ( isset( $_COOKIE['analytics'] ) && $_COOKIE['analytics'] != 'ok' ) ){
		
		//jQuery für OK Button
		$sitecontent->add_html_header( '<!-- jQuery -->' );
		//CSS
		$sitecontent->add_html_header( '<style>'.$analyticsconffile->read_kimb_one( 'ibcss' ).'</style>' );
		
		//Banner
		$sitecontent->add_site_content( '<div id="analysehinweis">' );
		//Text
		$sitecontent->add_site_content( $analyticsconffile->read_kimb_one( 'ibtext' ) );	
		//Button
		$sitecontent->add_site_content( '<button onclick="$( \'#analysehinweis\' ).css( \'display\' , \'none\' ); document.cookie = \'analytics=ok; path=/;\';">OK</button>' );
		$sitecontent->add_site_content( '</div> ' );
	}
}

?>
