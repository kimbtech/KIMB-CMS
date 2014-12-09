<?php

defined('KIMB_Backend') or die('No clean Request');

class backend_output{

	protected $header, $allgsysconf, $sitecontent, $sonderfile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
	}

	public function add_site_content($content){
		$this->sitecontent .= $content."\r\n";
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
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/nicEdit.js"></script>'."\r\n");
			echo ('<script>'."\r\n");
			echo ('$(function() {'."\r\n");
			if( $_SESSION['permission'] == 'more' ){

			}
			elseif( $_SESSION['loginokay'] == $this->allgsysconf['loginokay'] ){
				echo ('	$( "ul#menu li.admin" ).addClass("ui-state-disabled");'."\r\n");

			}
			else{
				echo ('	$( "ul#menu li.admin" ).addClass("ui-state-disabled");'."\r\n");
				echo ('	$( "ul#menu li.editor" ).addClass("ui-state-disabled");'."\r\n");
			}
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
					echo ('Hallo User <i><u>'.$_SESSION['name'].'</u></i>'."\r\n");
					echo ('<div style="float:right; position:absolute; right:10px; top:0px;">');
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_SESSION['name'].'" title="Usereinstellungen bearbeiten"><span class="ui-icon ui-icon-pencil"></span></a>'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/index.php?todo=logout" title="Abmelden und die Sitzung beenden!"><span class="ui-icon ui-icon-power"></span></a>'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/index.php" title="Hauptseite des Backends ( Login, ... )"><span class="ui-icon ui-icon-home"></span></a>'."\r\n");
					echo ('</div><br />');
					if( $_SESSION['permission'] == 'more' ){
						echo ('<i title="Sie haben alle Rechte in Backend!" >Admin</i>'."\r\n");
					}
					else{
						echo ('<i title="Sie haben eingeschränkte Rechte in Backend, einige Links sind im Menue deaktiviert!" >Editor</i>'."\r\n");
					}
					echo ('<div style="float:right; position:absolute; right:40px; bottom:7px;"><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?todo=purgecache" title="Den Cache leeren. (Dies ist nur nach einer Änderung im Menü oder für bestimmte Add-ons nötig!)"><span class="ui-icon 	ui-icon-refresh"></span></a></div>'."\r\n");
 
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
			<li class="editor" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" title="Seiten erstellen, löschen, bearbeiten"><span class="ui-icon ui-icon-document"></span>Seiten</a>
			<ul>
					<li class="editor" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new" title="Eine neue Seite erstellen."><span class="ui-icon ui-icon-plusthick"></span>Erstellen</a></li>
					<li class="editor" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list" title="Alle Seiten zum Bearbeiten, De-, Aktivieren und Löschen auflisten."><span class="ui-icon ui-icon-calculator"></span>Auflisten</a></li>
			</ul>
			</li>
			<li class="editor" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php" title="Menüs erstellen, löschen, bearbeiten"><span class="ui-icon ui-icon-newwin"></span>Menue</a>
				<ul>
					<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new" title="Einen neuen Menüpunkt erstellen."><span class="ui-icon ui-icon-plusthick"></span>Erstellen</a></li>
					<li class="editor" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect" title="Einen Seite einem Menüpunkt zuordnen."><span class="ui-icon ui-icon-arrowthick-2-e-w"></span>Zuordnen</a></li>
					<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list" title="Die gesamte Menüstruktur zum Bearbeiten und Löschen darstellen."><span class="ui-icon ui-icon-calculator"></span>Auflisten</a></li>
				</ul>
			</li>
			<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php" title="Backenduser erstellen, löschen, bearbeiten"><span class="ui-icon ui-icon-person"></span>User</a>
				<ul>
					<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new" title="Einen neuen Backenduser erstellen."><span class="ui-icon ui-icon-plusthick"></span>Erstellen</a></li>
					<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=list" title="Alle Backenduser zum Bearbeiten und Löschen auflisten."><span class="ui-icon ui-icon-calculator"></span>Auflisten</a></li>
				</ul>
			</li>
			<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php" title="Systemkonfiguration anpassen"><span class="ui-icon ui-icon-gear"></span>Konfiguration</a></li>
			<li class="editor" ><span class="ui-icon ui-icon-plusthick"></span>Add-ons
				<ul>
					<li class="editor" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less" title="Add-on Nutzung als Editor"><span class="ui-icon ui-icon-plusthick"></span>Nutzung</a></li>
					<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more" title="Add-on Konfiguration als Admin"><span class="ui-icon ui-icon-wrench"></span>Konfiguration</a></li>
					<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php" title="Add-ons installieren, löschen, de-, aktivieren"><span class="ui-icon ui-icon-circle-arrow-n"></span>Installation</a></li>
				</ul>
			</li>
			<li class="editor" ><span class="ui-icon ui-icon-help"></span>Other
				<ul>
					<li class="editor" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php" title="Dateien zum, Einbinden in Ihrer Seite, hochladen und verwalten, &apos;&apos;sichere&apos;&apos; Speicherung"><span class="ui-icon ui-icon-image"></span>Filemanager</a></li>
					<li class="admin" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_themes.php" title="Verändern Sie das Design des Frontends mit KIMB-CMS Themes"><span class="ui-icon ui-icon-contact"></span>Themes</a></li>
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