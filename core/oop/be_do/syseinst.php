<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
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



defined('KIMB_Backend') or die('No clean Request');

class BEsyseinst{
	
	protected $allgsysconf, $sitecontent, $sonder,$conffile;
	
	public $info = array(
		'sitename' => 'Name der Seite' ,
		'sitefavi' => 'URL zum favicon' ,
		'loginokay' => 'Zufälliger Code um Login zu verifizieren' ,
		'siteurl' => 'GrundURL der Seite ( ohne / am Ende )' ,
		'description' => 'Allgemeiner Beschreibungs-Meta-Tag' ,
		'urlweitermeth' => 'PHP header (1) oder Meta Refresh (2) für Weiterleitungen' ,
		'adminmail' => 'E-Mail Adresse des Administrators' ,
		'robots' => 'Meta-Robots-Tag für Homepage' ,
		'mailvon' => 'E-Mail Absender des Systems' ,
		'cache' => 'on / off des Caches ( on ist empfehlenswert )' ,
		'sitespr' => 'Sprache der Seitenelemente ( z.Z. kein Nutzen )' ,
		'systemversion' => 'Version des CMS' ,
		'build' => 'Genaue Version des Systems, wichtig für Updates,... (Beispiele: V1.0F-p0 -> Version 1.0 Final Patch 0 // V0.7B-p4,5 -> Version 0.7 Beta Patch 4 und 5 ) ( entspricht GIT Tags )' ,
		'urlrewrite' => 'on / off des URL-Rewritings ( on ist empfehlenswert )' ,
		'cachelifetime' => 'Lebensdauer des Caches in Sekunden oder always ( always ist empfehlenswert )' ,
		'use_request_url' => 'Für URL-Rewriting muss der Request entweder an /index.php?url=xxx gesendert werden oder per $SERVER[REQUEST_URI] verfügbar sein. Letzteres kann hier verboten werden, da es auf manchen Server zu Problemen führen könnte. ( ok / nok )' ,
		'show_siteinfos' => 'Unten auf den Seiten anzeigen wann und von wem die Seite geändert wurde sowie den Permalink!' ,
		'theme' => 'Wählen Sie ein installiertes Thema für Ihre Seite, ohne oder mit falschem Parameter wird das Standardthema verwendet. ( Dieser Wert wird automatisch bei einer Themeninstallation geändert. )',
		'cronkey' => 'Hier finden Sie den Cronkey. Diese Zeichenkette müssen Sie an die URL hängen, um den Systemcron ausführen zu können. (<cms-url>/cron.php?key=XXXX)',
		'lang' => 'Hier können Sie das CMS für mehrsprachige Seiten freischalten. ( on / off ) ( Wird auch über Other -> Mehrsprachige Seite verändert )'  
	);
	
	public function __construct( $allgsysconf, $sitecontent,$conffile ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
		$this->conffile = $conffile;
		$this->jsobject = new JSforBE( $allgsysconf, $sitecontent );
		
		$this->sonder = new KIMBdbf('sonder.kimb');
	}
	
	public function make_syseinst_cachedel(){
		$sitecontent = $this->sitecontent;
				
		$caches = scan_kimb_dir('cache/');
		foreach( $caches as $cache ){
			delete_kimb_datei( 'cache/'.$cache );
		}
		$sitecontent->echo_message( 'Der Cache wurde gelöscht!' );
		
		return;
	}
	
	public function make_syseinst_einst(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$info = $this->info;
		$conffile = $this->conffile;
		
		if ( $_GET['todo'] == 'del' && isset( $_GET['teil'] ) ){
		
			if( $conffile->write_kimb_id( '001' , 'del' , $_GET['teil'] ) ){
				$sitecontent->echo_message( 'Der Parameter "'.$_GET['teil'].'" wurde aus der Konfiguration entfernt!' );
			}
		}
		
		if ( isset( $_POST['1'] ) ){
			
			$i = 1;
			while( isset( $_POST[$i] ) ){
				if( $_POST[$i.'-wert'] != $allgsysconf[$_POST[$i]] ){
					if( $conffile->write_kimb_id( '001' , 'add' , $_POST[$i] , $_POST[$i.'-wert'] ) ){
						$sitecontent->echo_message( 'Der Parameter "'.$_POST[$i].'" wurde in der Konfiguration geändert!' );
					}
				}
			$i++;
			}
		
			$allgsysconf = $conffile->read_kimb_id('001');
		
		}
		
		
		$sitecontent->add_site_content('<h2>Systemkonfiguration</h2>');
		
		$this->jsobject->for_syseinst_all();
		
		$confteile = $conffile->read_kimb_all_xxxid('001');
		
		$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?konf">');
		$sitecontent->add_site_content('<table width="100%" ><tr><th>Name</th><th>Wert</th><th width="20px;">Löschen</th><th width="20px;">Info</th></tr>');
		
		$i = 1;
		foreach( $confteile as $confteil ){
		
			if( isset( $info[$confteil] ) ){
				$infotab = '<span class="ui-icon ui-icon-info" title="'.$info[$confteil].'"></span>';
			}
			else{
				$infotab = '';
			}
		
			if( $confteil == 'systemversion' || $confteil == 'build' ){
				$sitecontent->add_site_content('<tr><td><input type="text" readonly="readonly" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" readonly="readonly" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span><span class="ui-icon ui-icon-trash" title="Löschen nicht erlaubt!"></span></span></td><td>'.$infotab.'</td></tr>');
			}
			else{
				$sitecontent->add_site_content('<tr><td><input type="text" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span onclick="var delet = del( \''.$confteil.'\' ); delet(); " style="display:inline-block;" ><span class="ui-icon ui-icon-trash" title="Diesen Wert löschen."></span></span></td><td>'.$infotab.'</td></tr>');
			}
			$i++;
		
		}
		$sitecontent->add_site_content('<tr><td><input type="text" placeholder="hinzufügen" name="'.$i.'"></td><td><input type="text" placeholder="hinzufügen" name="'.$i.'-wert"></td><td></td><td><span class="ui-icon ui-icon-info" title="Fügen Sie einen eigenen Wert in die allgemeine Konfiguration ein."></span></td></tr>');
		$sitecontent->add_site_content('</table><input type="submit" value="Ändern"></form>');
	}

	public function make_syseinst_sonder_dbf( $POST ){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$sonder = $this->sonder;
		
		$dos = array( 'footer' => 'footer', 'error-404' => 'err404', 'error-403' => 'err403' );
		
		foreach( $dos as $key => $val ){
			if( $sonder->read_kimb_one( $key ) != $POST[$val] ){
				if( $sonder->write_kimb_replace( $key , $POST[$val] ) ){
					$sitecontent->echo_message( 'Der "'.$key.'" wurde geändert!' );
				}
			}
		}
		
		return true;
		
	}
	
	public function make_syseinst_sonder(){
		$sitecontent = $this->sitecontent;
		$allgsysconf = $this->allgsysconf;
		$sonder = $this->sonder;
		
		if( !empty( $_POST['footer'] ) &&!empty( $_POST['err404'] ) && !empty($_POST['err403'] ) ){
			$this->make_syseinst_sonder_dbf( $_POST );
		}

		$arr['small'] = '#footer';
		add_tiny( false, true, $arr );
		$arr['small'] = '#err404';
		add_tiny( false, true, $arr );
		$arr['small'] = '#err403';
		add_tiny( false, true, $arr );
		
		$sitecontent->add_site_content('<h2>Error- und Footertexte</h2>');
		$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?text">');
		$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%;">'.$sonder->read_kimb_one( 'footer' ).'</textarea> <i>Footer &uarr;</i> <button onclick="tinychange( \'footer\' ); return false;">Editor I/O</button> <br />');
		$sitecontent->add_site_content('<textarea name="err404" id="err404" style="width:99%;">'.$sonder->read_kimb_one( 'error-404' ).'</textarea> <i>Error 404 &uarr;</i> <button onclick="tinychange( \'err404\' ); return false;">Editor I/O</button> <br />');
		$sitecontent->add_site_content('<textarea name="err403" id="err403" style="width:99%;">'.$sonder->read_kimb_one( 'error-403' ).'</textarea> <i>Error 403 &uarr;</i> <button onclick="tinychange( \'err403\' ); return false;">Editor I/O</button> <br />');
		$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');
	}
	
	
}
?>
