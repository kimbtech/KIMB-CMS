<?php

defined('KIMB_Backend') or die('No clean Request');

class backend_output{

	protected $title, $header, $allgsysconf, $menue, $sitecontent, $addon, $footer, $sonderfile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->footer = $this->sonderfile->read_kimb_one('footer')."\r\n";
	}

	public function add_site_content($content){
		$this->sitecontent .= $content;
	}

	public function add_html_header($inhalt){
		$this->header .= $inhalt."\r\n";
	}

	public function echo_error($message = '', $art = 'unknown'){
		if( $art == '404' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-404');
			$this->sitecontent .= '<div id="errorbox"><h1>Error - 404</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i></div>'."\r\n";
			header("HTTP/1.0 404 Not Found");

		}
		elseif( $art == '403' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-403');
			$this->sitecontent .= '<div id="errorbox"><h1>Error - 403</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i></div>'."\r\n";
			header('HTTP/1.0 403 Forbidden');
		}
		else{
			$this->sitecontent .= '<div id="errorbox"><h1>Error - Fehler</h1>'.$message.'</div>'."\r\n";
		}

	}


	public function output_complete_site(){
		echo('<!DOCTYPE html> <html> <head>'."\r\n");
			echo ('<title>'.$this->allgsysconf['sitename'].' : Backend</title>'."\r\n");
			echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\r\n");
			echo ('<meta name="robots" content="none">'."\r\n");
			echo ('<meta charset="utf-8">'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" >'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/be.css" >'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.css" >'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/hash.js"></script>'."\r\n");
			
				echo($this->header);
				echo("\r\n");

		echo('</head><body>'."\r\n");
			echo('<div id="page">'."\r\n");
				echo('<div id="header">'."\r\n");
					echo("<pre>\r\n _  _____ __  __ ____         ____ __  __ ____  \r\n| |/ /_ _|  \/  | __ )       / ___|  \/  / ___| \r\n| ' / | || |\/| |  _ \ _____| |   | |\/| \___ \ \r\n| . \ | || |  | | |_) |_____| |___| |  | |___) |\r\n|_|\_\___|_|  |_|____/       \____|_|  |_|____/ \r\n</pre>"."\r\n");
				echo('</div>'."\r\n");
				echo('<div id="menu">'."\r\n");

					echo('<div id="tabs">
	<ul>
		<li><a href="#tabs-1">First</a></li>
		<li><a href="#tabs-2">Second</a></li>
		<li><a href="#tabs-3">Third</a></li>
	</ul>
	<div id="tabs-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore.</div>
	<div id="tabs-2">Phasellus mattis tincidunt nibh. Cras orci urna, blandit id, pretium vel, aliquet ornare, felis. Mae.</div>
	<div id="tabs-3">Nam dui erat, auctor a, dignissim quis, sollicitudin eu, felis. Pellentesque nisi urna, interdum ege.</div>
</div>

<script>$( "#tabs" ).tabs();</script>');

				echo('</div>'."\r\n");
				echo('<div id="content">'."\r\n");

					echo($this->sitecontent);
					echo("\r\n");


			echo('</div>'."\r\n");
			echo('<div id="footer">'."\r\n");

				echo('Backend der Seite '.$this->allgsysconf['sitename'].' - Zugang nur für Befugte!<br /><br />');


				echo($this->footer);
				echo("\r\n");

			echo('</div></div>'."\r\n");
		echo('</body> </html>');
	}


}

?>
