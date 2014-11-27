<?php

class system_output{

	protected $title, $header, $allgsysconf, $menue, $sitecontent, $addon, $footer, $sonderfile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->footer = $this->sonderfile->read_kimb_one('footer')."\n\r";
	}

	public function add_menue_one_entry($name, $link, $niveau){
		if( $niveau == '1' ){
			$this->menue[1] .=  '<a class="menu" href="'.$link.'">'.$name.'</a>'."\n\r";
		}
		else{
			$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\n\r";
		}
	}

	public function add_site_content($content){
		$this->sitecontent .= $content."\n\r";
	}

	public function add_addon_area($inhalt, $style = '', $cssclass = ''){
		$this->addon .= '<div id="menu-apps" class="'.$cssid.'" style="'.$style.'">'.$inhalt.'</div>'."\n\r";
	}

	public function add_footer($inhalt){
		$this->footer .= $inhalt."\n\r";
	}

	public function add_html_header($inhalt){
		$this->header .= $inhalt."\n\r";
	}

	public function set_title($title){
		$this->title = $title;
	}

	public function echo_error($message = '', $art = 'unknown'){
		if( $art == '404' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-404');
			$this->sitecontent = '<h1>Error - 404</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i>'."\n\r";
		}
		elseif( $art == '403' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-403');
			$this->sitecontent = '<h1>Error - 403</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i>'."\n\r";
		}
		else{
			$this->sitecontent = '<h1>Error - Fehler</h1>'.$message."\n\r";
		}

		$this->output_complete_site();
		die;

	}


	public function output_complete_site(){
		echo('<!DOCTYPE html> <html> <head>'."\n\r");
			echo ('<title>'.$this->allgsysconf['sitename'].' : '.$this->title.'</title>'."\n\r");
			echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\n\r");
			echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\n\r");
			echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\n\r");
			echo ('<meta name="robots" content="'.$this->allgsysconf['robots'].'">'."\n\r");
			echo ('<meta name="description" content="'.$this->allgsysconf['description'].'">'."\n\r");
			echo ('<meta charset="utf-8">'."\n\r");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" media="all">'."\n\r");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/design.css" media="screen">'."\n\r");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/print.css" media="print">'."\n\r");
			
				echo($this->header);
				echo("\n\r");

		echo('</head><body>'."\n\r");
			echo('<div id="page">'."\n\r");
				echo('<div id="header">'."\n\r");
					echo('<a href="'.$this->allgsysconf['siteurl'].'/index.php"><div style="float:left;"><img src="'.$this->allgsysconf['siteurl'].'/load/system/theme/logo.png" style="border:none;"></div>'."\n\r");
					echo('<div style="line-height: 100px;">'.$this->allgsysconf['sitename'].'</div></a>'."\n\r");
				echo('</div>'."\n\r");
				echo('<div id="menu">'."\n\r");

					echo($this->menue[1]);
					echo("\n\r");

				echo('</div>'."\n\r");
				echo('<div id="site">'."\n\r");
					echo('<div id="menu-apps"><ul>'."\n\r");

						echo($this->menue[2]);
						echo("\n\r");

					echo('</ul></div>'."\n\r");

						echo($this->addon);
						echo("\n\r");

					echo('<div id="content-apps">'."\n\r");

						echo($this->sitecontent);
						echo("\n\r");

					echo('</div>'."\n\r");
			echo('</div>'."\n\r");
			echo('<div id="footer">'."\n\r");

				echo($this->footer);
				echo("\n\r");

			echo('</div></div>'."\n\r");
		echo('</body> </html>');
	}


}

?>
