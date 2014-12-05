<?php

defined('KIMB_Backend') or die('No clean Request');

class backend_output{

	protected $header, $allgsysconf, $sitecontent, $sonderfile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
	}

	public function add_site_content($content){
		$this->sitecontent .= $content;
	}

	public function add_html_header($inhalt){
		$this->header .= $inhalt."\r\n";
	}

	public function echo_message($message){
		$this->sitecontent .= '<div class="ui-widget" style="position: relative;">'."\r\n";
		$this->sitecontent .= '<div class="ui-state-highlight ui-corner-all" style="padding:10px;">'."\r\n";
		$this->sitecontent .= '<span class="ui-icon ui-icon-info" style="position:absolute; left:20px; top:7px;"></span>'."\r\n";
		$this->sitecontent .= '<h1>Meldung</h1>'.$message."\r\n";
		$this->sitecontent .= '</div></div>'."\r\n";
	}

	public function echo_error($message = '', $art = 'unknown'){
		$this->sitecontent .= '<div class="ui-widget" style="position: relative;">'."\r\n";
		$this->sitecontent .= '<div class="ui-state-error ui-corner-all" style="padding:10px;">'."\r\n";
		$this->sitecontent .= '<span class="ui-icon ui-icon-alert" style="position:absolute; left:20px; top:7px;"></span>'."\r\n";
		if( $art == '404' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-404');
			$this->sitecontent .= '<h1>Error - 404</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i>'."\r\n";
			header("HTTP/1.0 404 Not Found");

		}
		elseif( $art == '403' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-403');
			$this->sitecontent .= '<h1>Error - 403</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i>'."\r\n";
			header('HTTP/1.0 403 Forbidden');
		}
		else{
			$this->sitecontent .= '<h1>Error - Fehler</h1>'.$message."\r\n";
		}
		$this->sitecontent .= '</div></div>'."\r\n";
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
			echo ('<script>'."\r\n");
			echo ('$(function() {'."\r\n");
			echo ('	$( document ).tooltip();'."\r\n");
			echo ('	$( "#menu" ).menu();'."\r\n");
			echo ('});'."\r\n");
			echo ('</script>'."\r\n");
			
				echo($this->header);
				echo("\r\n");

		echo('</head><body>'."\r\n");
				echo('<div id="header">'."\r\n");
					echo("<pre>\r\n _  _____ __  __ ____         ____ __  __ ____  \r\n| |/ /_ _|  \/  | __ )       / ___|  \/  / ___| \r\n| ' / | || |\/| |  _ \ _____| |   | |\/| \___ \ \r\n| . \ | || |  | | |_) |_____| |___| |  | |___) |\r\n|_|\_\___|_|  |_|____/       \____|_|  |_|____/ \r\n</pre>"."\r\n");
				echo('</div>'."\r\n");
				echo('<div id="page">'."\r\n");
				echo('<div id="userinfo">'."\r\n");
				if( $_SESSION['loginokay'] == $this->allgsysconf['loginokay'] ){
					echo ('Hallo User <i>'.$_SESSION['name'].'</i><br />'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_SESSION['name'].'" title="Usereinstellungen bearbeiten"><span class="ui-icon ui-icon-pencil"></span></a>'."\r\n");
				}
				else{
					echo('Nicht eingeloggt!<br /><span class="ui-icon ui-icon-cancel"></span>'."\r\n");
				}
				echo('</div>'."\r\n");
				echo('<div id="menue">'."\r\n");
echo('
<!-- Menue - jQuery UI -->
<!-- Menue - jQuery UI -->

			<ul id="menu">
			<li class="ui-state-disabled">Aberdeen</li>
			<li>Ada</li>
			<li>Adamsville</li>
			<li>Addyston</li>
			<li>Delphi
				<ul>
				<li class="ui-state-disabled">Ada</li>
				<li>Saarland</li>
				<li>Salzburg an der schönen Donau</li>
				</ul>
			</li>
			</ul>

<!-- Menue - jQuery UI -->
<!-- Menue - jQuery UI -->
');
				echo ('</div>'."\r\n");
				echo ('<div id="version">'."\r\n");
					echo ('<b>KIMB-technologies CMS<br />V. '.$this->allgsysconf['systemversion'].'</b><br />'."\r\n");
					echo ('<i>Diese Seite ist nur für Administratoren!</i><br />'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/">Zurück</a>'."\r\n");
					echo ('</div>'."\r\n");
				echo('<div id="content">'."\r\n");

					echo($this->sitecontent);
					echo("\r\n");

				echo('</div></div>'."\r\n");
		echo('</body> </html>');
	}


}

?>
