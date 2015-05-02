<?php
// Diese Datei gibt das Grundgerüst für die Ausgabe
// Folgende Variablen sollten verwendet werden:
//    $this->header, $this->title, $this->menue, $this->addon, $this->sitecontent, $this->footer
//    array( $this->allgsysconf )
// Diese Datei ist Teil eines Objekts

if( $this->allgsysconf['lang'] == 'on' ){
	echo('<!DOCTYPE html> <html lang="'.$this->requestlang['tag'].'"> <head>'."\r\n");
}
else{
	echo('<!DOCTYPE html> <html> <head>'."\r\n");
}
echo ('<title>'.$this->allgsysconf['sitename'].' : '.$this->title.'</title>'."\r\n");
echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\r\n");
echo ('<meta name="robots" content="'.$this->allgsysconf['robots'].'">'."\r\n");
echo ('<meta name="description" content="'.$this->allgsysconf['description'].'">'."\r\n");
echo ('<meta charset="utf-8">'."\r\n");
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" media="all">'."\r\n");
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/design_new.css" media="screen">'."\r\n");
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/print.css" media="print">'."\r\n");
echo ('<link href="'.$this->allgsysconf['siteurl'].'/load/system/theme/touch_icon.png" rel="apple-touch-icon" />'."\r\n");
echo ('<script> var clicks = new Array(); function menueclick( id ){ var isTouch = (("ontouchstart" in window) || (navigator.msMaxTouchPoints > 0)); var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false ); if( isTouch && !iOS ){ if (!( id in clicks)) { clicks[id] = 0; } clicks[id]++; if( clicks[id] == 2 ){ return true; } else{ return false; } } else{ return true; } }</script>'."\r\n");
		
	echo($this->header);
	echo("\r\n");

echo('</head><body>'."\r\n");

echo('<div id="page">'."\r\n");

	if( $this->allgsysconf['lang'] == 'on' ){
		echo( '<div id="lang"><ul>' );
		foreach( $this->allglangs as $lang ){
			echo( '<li><a href="'.$lang['thissite'].'"><img src="'.$lang['flag'].'" title="'.$lang['name'].'" alt=""></a></li>' );
		}
		echo( '</ul></div>' );
	}

	//echo('<div id="page">'."\r\n");
		echo('<div id="header">'."\r\n");
			echo('<a href="'.$this->allgsysconf['siteurl'].'/"><span>'.$this->allgsysconf['sitename'].'</span>'."\r\n");
			echo('<img src="'.$this->allgsysconf['siteurl'].'/load/system/theme/logo.png" style="border:none; float:right;"></a>'."\r\n");
		echo('</div>'."\r\n");
		echo('<ul id="nav">'."\r\n");

			echo($this->menue);
			echo("\r\n");
			echo('</li>');
			echo( str_repeat( '</ul>' , $this->ulauf ) );

		if( is_dir( __DIR__.'/../addons/search_sitemap/' ) ){

			$search_sitemap['file'] = new KIMBdbf( 'addon/search_sitemap__conf.kimb' );

			$search_sitemap['searchsiteid'] = $search_sitemap['file']->read_kimb_one( 'searchsiteid' ); // off oder id

			if( $search_sitemap['searchsiteid'] != 'off' ){

				echo('<li>'."\r\n");
				echo('<form method="post"  action="'.$this->allgsysconf['siteurl'].'/index.php?id='.$search_sitemap['searchsiteid'].'">'."\r\n");
				echo('<input id="searchform" type="text" name="search" placeholder="Suchbegriff" value="'.htmlentities( $_REQUEST['search'] ).'">'."\r\n");
				echo('</form>'."\r\n");	
				echo('</li>'."\r\n");
			}
		}

		echo('</ul>'."\r\n");

		if( $this->addon != '' ){
			echo('<div id="site">'."\r\n");
			
					echo($this->addon);
					echo("\r\n");

				echo('<div id="contents" >'."\r\n");

					echo($this->sitecontent);
					echo("\r\n");

				echo('</div></div>'."\r\n");
			echo('<div id="footer" >'."\r\n");
		}
		else{
			echo('<div id="site">'."\r\n");
				echo('<div id="contentm" >'."\r\n");

					echo($this->sitecontent);
					echo("\r\n");

				echo('</div></div>'."\r\n");
			echo('<div id="footer" >'."\r\n");
		}
			echo('&copy; '.date('Y').' ' );		

			echo($this->footer);
			echo("\r\n");

		echo('</div>'."\r\n");
	
	echo('</div>'."\r\n");
	
	echo ('<script>if( document.getElementById( "contentm" ) != null ){ var cont = document.getElementById( "contentm" ); var fooadd = 4; } else{ var cont = document.getElementById( "contents" ); var fooadd = 4; } cont.style.paddingTop = document.getElementById("breadcrumb").clientHeight + 5 + "px";  document.getElementById("footer").style.width = cont.offsetWidth - 24 + fooadd + "px"; if( document.getElementById( "permalink" ) != null ){ cont.style.paddingBottom = document.getElementById("permalink").clientHeight + 5 + "px"; } if( document.getElementById( "usertime" ) != null ){ cont.style.paddingBottom = document.getElementById("usertime").clientHeight + 5 + "px"; }</script>'."\r\n");
echo('</body> </html>');

?>
