<?php

defined('KIMB_CMS') or die('No clean Request');

class system_output{

	protected $title, $header, $allgsysconf, $menue, $sitecontent, $addon, $footer, $sonderfile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->footer = $this->sonderfile->read_kimb_one('footer')."\r\n";
	}

	public function add_menue_one_entry($name, $link, $niveau, $clicked , $allgrequestid){
		if( $clicked == 'yes' ){
			$this->add_html_header( '<link rel="canonical" href="'.$link.'">' );
		}
		if( isset( $this->allgsysconf['theme'] ) ){
			if( file_exists( __DIR__.'/../theme/output_menue_'.$this->allgsysconf['theme'].'.php' ) ){
				require(__DIR__.'/../theme/output_menue_'.$this->allgsysconf['theme'].'.php');
			}
			else{
				require(__DIR__.'/../theme/output_menue_norm.php');
			}
		}
		else{
			require(__DIR__.'/../theme/output_menue_norm.php');
		}
	}

	public function add_site($content){
		$this->set_title($content['title']);
		$this->add_html_header($content['header']);
		if( !empty( $content['description'] ) ){
			$this->allgsysconf['description'] = $content['description'];
		}
		if( !empty( $content['keywords'] ) ){
			$this->add_html_header('<meta name="keywords" content="'.$content['keywords'].'">');
		}
		$this->sitecontent .= $content['inhalt']."\r\n";
		$this->add_footer($content['footer']);
		if( $this->allgsysconf['show_siteinfos'] == 'on' ){
			$time = date( "d.m.Y" , $content['time'] );
			$schlusszeile .= '<div id="usertime">Erstellt von '.$content['made_user'].' am '.$time.'</div>';
			$schlusszeile .= '<div id="permalink">Permalink: <a href="'.$this->allgsysconf['siteurl'].'/index.php?id='.$content['req_id'].'">'.$this->allgsysconf['siteurl'].'/index.php?id='.$content['req_id'].'</a></div>';
			$this->sitecontent .= $schlusszeile."\r\n";
		}
	}

	public function add_site_content($content){
		$this->sitecontent .= $content;
	}

	public function add_addon_area($inhalt, $style = '', $cssclass = ''){
		$this->addon .= '<div id="apps" class="'.$cssclass.'" style="'.$style.'">'.$inhalt.'</div>'."\r\n";
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
		if( isset( $this->allgsysconf['theme'] ) ){
			if( file_exists( __DIR__.'/../theme/output_site_'.$this->allgsysconf['theme'].'.php' ) ){
				require_once(__DIR__.'/../theme/output_site_'.$this->allgsysconf['theme'].'.php');
			}
			else{
				require_once(__DIR__.'/../theme/output_site_norm.php');
			}
		}
		else{
			require_once(__DIR__.'/../theme/output_site_norm.php');
		}
	}


}

?>
