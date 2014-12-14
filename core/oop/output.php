<?php

defined('KIMB_CMS') or die('No clean Request');

class system_output{

	protected $title, $header, $allgsysconf, $menue, $sitecontent, $addon, $footer, $sonderfile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->footer = $this->sonderfile->read_kimb_one('footer')."\r\n";
	}

	public function add_menue_one_entry($name, $link, $niveau, $clicked ){
		if( $niveau == '1' ){
			if( $clicked == 'yes' ){
				$this->menue[1] .=  '<a style="color:#0000ff; background-color:#EEC900; border:solid 2px #ffffff;" class="menu" href="'.$link.'">'.$name.'</a>'."\r\n";
			}
			else{
				$this->menue[1] .=  '<a class="menu" href="'.$link.'">'.$name.'</a>'."\r\n";
			}
		}
		if( $niveau == '1' ){
			if( $clicked == 'yes' ){
				$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
			}
			else{
				$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
			}
		}
		elseif( $niveau == '2'){
			if( $clicked == 'yes' ){
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
				$this->menue[2] .=  '</li></ul>';
			}
			else{
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
				$this->menue[2] .=  '</li></ul>';
			}
		}
		elseif( $niveau == '3'){
			if( $clicked == 'yes' ){
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
				$this->menue[2] .=  '</li></ul>';
				$this->menue[2] .=  '</li></ul>';
			}
			else{
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
				$this->menue[2] .=  '</li></ul>';
				$this->menue[2] .=  '</li></ul>';
			}
		}
		else{
			if( $clicked == 'yes' ){
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
				$this->menue[2] .=  '</li></ul>';
				$this->menue[2] .=  '</li></ul>';
				$this->menue[2] .=  '</li></ul>';
			}
			else{
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
				$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
				$this->menue[2] .=  '</li></ul>';
				$this->menue[2] .=  '</li></ul>';
				$this->menue[2] .=  '</li></ul>';
			}
		}
	}

	public function unset_menue( $niveau ){
		$this->menue[$niveau] = '';
	}

	public function add_site($content){
		//$this->sitecontent .= $content."\r\n";
		$this->set_title($content['title']);
		$this->add_html_header($content['header']);
		$this->add_html_header('<meta name="description" content="'.$content['description'].'">');
		$this->add_html_header('<meta name="keywords" content="'.$content['keywords'].'">');
		$this->sitecontent .= $content['inhalt']."\r\n";
		$this->add_footer($content['footer']);
		$time = date( "d.m.Y" , $content['time'] );
		$schlusszeile .= '<div style="position: absolute; bottom:2px; right:2px; border:solid 2px #aaaaaa; border-radius:2px; padding:4px;">Erstellt von '.$content['made_user'].' am '.$time.'</div>';
		$schlusszeile .= '<div style="position: absolute; bottom:2px; left:2px; border:solid 2px #aaaaaa; border-radius:2px; padding:4px;">Permalink: <a href="'.$this->allgsysconf['siteurl'].'/index.php?id='.$content['req_id'].'">'.$this->allgsysconf['siteurl'].'/index.php?id='.$content['req_id'].'</a></div>';
		$this->sitecontent .= $schlusszeile."\r\n";
	}

	public function add_site_content($content){
		$this->sitecontent .= $content;
	}

	public function add_addon_area($inhalt, $style = '', $cssclass = ''){
		$this->addon .= '<div id="menu-apps" class="'.$cssid.'" style="'.$style.'">'.$inhalt.'</div>'."\r\n";
	}

	public function add_footer($inhalt){
		$this->footer .= $inhalt."\r\n";
	}

	public function add_html_header($inhalt){
		$this->header .= $inhalt."\r\n";
	}

	public function set_title($title){
		$this->title = $title;
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
			echo ('<title>'.$this->allgsysconf['sitename'].' : '.$this->title.'</title>'."\r\n");
			echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\r\n");
			echo ('<meta name="robots" content="'.$this->allgsysconf['robots'].'">'."\r\n");
			echo ('<meta name="description" content="'.$this->allgsysconf['description'].'">'."\r\n");
			echo ('<meta charset="utf-8">'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" media="all">'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/design.css" media="screen">'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/print.css" media="print">'."\r\n");
			
				echo($this->header);
				echo("\r\n");

		echo('</head><body>'."\r\n");
			echo('<div id="page">'."\r\n");
				echo('<div id="header">'."\r\n");
					echo('<a href="'.$this->allgsysconf['siteurl'].'/"><div style="float:left;"><img src="'.$this->allgsysconf['siteurl'].'/load/system/theme/logo.png" style="border:none;"></div>'."\r\n");
					echo('<div style="line-height: 100px;">'.$this->allgsysconf['sitename'].'</div></a>'."\r\n");
				echo('</div>'."\r\n");
				echo('<div id="menu">'."\r\n");

					echo($this->menue[1]);
					echo("\r\n");

				echo('</div>'."\r\n");
				echo('<div id="site">'."\r\n");
					echo('<div id="menu-apps"><ul>'."\r\n");

						echo($this->menue[2]);
						echo("\r\n");

					echo('</ul></div>'."\r\n");

						echo($this->addon);
						echo("\r\n");

					echo('<div id="content-apps" style="position: relative; padding-bottom:30px;">'."\r\n");

						echo($this->sitecontent);
						echo("\r\n");

					echo('</div>'."\r\n");
			echo('</div>'."\r\n");
			echo('<div id="footer">'."\r\n");

				echo($this->footer);
				echo("\r\n");

			echo('</div></div>'."\r\n");
		echo('</body> </html>');
	}


}

?>
