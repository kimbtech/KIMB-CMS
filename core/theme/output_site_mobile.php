<?php
// Diese Datei gibt das Grundgerüst für die Ausgabe
// Folgende Variablen sollten verwendet werden:
//    $this->header, $this->title, $this->menue, $this->addon, $this->sitecontent, $this->footer
//    array( $this->allgsysconf )
// Diese Datei ist Teil eines Objekts

echo('<!DOCTYPE html> <html> <head>'."\r\n");
echo ('<title>'.$this->allgsysconf['sitename'].' : '.$this->title.'</title>'."\r\n");
echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\r\n");
echo ('<meta name="robots" content="'.$this->allgsysconf['robots'].'">'."\r\n");
echo ('<meta name="description" content="'.$this->allgsysconf['description'].'">'."\r\n");
echo ('<meta charset="utf-8">'."\r\n");
echo ('<meta name="apple-mobile-web-app-capable" content="yes">'."\r\n");
echo ('<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">'."\r\n");
echo ('<script src="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js" type="text/javascript"></script>'."\r\n");
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" media="all">'."\r\n");
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/mobile.css" media="screen">'."\r\n");
echo ('<link href="'.$this->allgsysconf['siteurl'].'/load/system/theme/touch_icon.png" rel="apple-touch-icon" />'."\r\n");
echo ('
<script type="text/javascript">
function showleftgr() {
	$( "div.leftgr" ).css( "display" , "block" );
	$( "div.left" ).css( "display" , "none" );
	$( "div.content" ).css( "margin-left" , "56.5%" );
}
function showleft() {
	$( "div.left" ).css( "display" , "block" );
	$( "div.leftgr" ).css( "display" , "none" );
	$( "div.content" ).css( "margin-left" , "10.5%" );
}
</script>
');
		
	echo($this->header);
	echo("\r\n");

echo('</head><body>'."\r\n");

echo('
<div class="header">');
	echo('<a href="'.$this->allgsysconf['siteurl'].'/"><div style="float:left;"><img src="'.$this->allgsysconf['siteurl'].'/load/system/theme/logo.png" style="border:none;"></div>'."\r\n");
	echo('<div style="line-height: 100px;">'.$this->allgsysconf['sitename'].'</div></a>'."\r\n");
echo('</div>

<div class="left" ><span onclick=" showleftgr(); ">asd</span></div>
<div class="leftgr">');
	echo('<div><ul id="nav">'."\r\n");
	echo($this->menue);
	echo("\r\n");
	echo('</li>');
	echo( str_repeat( '</ul>' , $this->ulauf ) );
	echo('</ul></div>'."\r\n");
echo('</div>

<div class="content">');
	echo($this->sitecontent);
echo ('</div>');

echo('</body> </html>');

?>
