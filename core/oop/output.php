<?php

class system_output{

	protected $title, $header, $allgsysconf, $menue, $site;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
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
		echo 'max';
	}

	public function add_addon_area($inhalt, $style = 'none', $cssid = ''){
		echo 'max';
	}

	public function add_footer($inhalt){
		echo 'max';
	}

	public function add_html_header($inhalt){
		$this->header .= $inhalt."\n\r";
	}

	public function set_title($title){
		$this->title;
	}


	public function output_complete_site(){
		echo('<!DOCTYPE html> <html> <head> <title>'.$this->allgsysconf['sitename'].': '.$this->title.'</title>'."\n\r");
		echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\n\r");
		echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\n\r");
		echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\n\r");
		echo ('<meta name="robots" content="none">'."\n\r");
		echo ('<meta name="robots" content="noindex,nofollow">'."\n\r");
		echo ('<meta charset="utf-8">'."\n\r");
		echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" media="all">'."\n\r");
		echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/design.css" media="screen">'."\n\r");
		echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/print.css" media="print">');
		echo ('<meta name="description" content="'.$this->allgsysconf['description'].'">'."\n\r");

		echo($this->header);
		echo("\n\r");

		echo('</head><body><div id="page">');
		echo('<div id="header">');
		echo('<div style="float:left;"><a href="'.$this->allgsysconf['siteurl'].'/index.php"><img src="'.$this->allgsysconf['siteurl'].'/load/system/theme/logo.png" style="border:none;"></a></div><div style="line-height: 100px;">'.$this->allgsysconf['sitename'].'</div>');
		echo('</div>');
		echo('<div id="menu">'."\n\r");

		echo($this->menue[1]);
		echo("\n\r");

		echo('</div>'."\n\r");
		echo('<div id="site">'."\n\r");
		echo('<div id="menu-apps"><ul>'."\n\r");

		echo($this->menue[2]);
		echo("\n\r");

		echo('</ul></div>'."\n\r");
		echo('<div id="content-apps">');
		echo("\n\r\n\r");
		echo($addontopcontent);
		echo("\n\r\n\r");
		echo($sitecontent);
		echo("\n\r\n\r");
		echo($addonbottomcontent);
		echo("\n\r\n\r");
		echo('</div>'."\n\r");
		echo('</div><div id="footer">'."\n\r");

		echo($this->site);
		echo("\n\r");

		echo('</div> </body> </html>');
	}
	//caches anlegen, Prüfung übernimmt generate ids

}

?>
