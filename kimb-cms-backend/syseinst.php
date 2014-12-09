<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login('more');

//Systemeinstellungen in config.kimb ( eigenes Feld möglich )

if ( $_GET['todo'] == 'purgecache' ){
	$caches = scan_kimb_dir('cache/');
	foreach( $caches as $cache ){
		delete_kimb_datei( 'cache/'.$cache );
	}
	$sitecontent->echo_message( 'Der Cache wurde gelöscht!' );
}

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
if( isset( $_POST['footer'] ) && isset( $_POST['err404'] ) && isset( $_POST['err403'] ) ){
	$sonder = new KIMBdbf('sonder.kimb');

	if( $sonder->read_kimb_one('footer') != $_POST['footer'] ){
		if( $sonder->write_kimb_replace( 'footer' , $_POST['footer'] ) ){
			$sitecontent->echo_message( 'Der Footer wurde geändert!' );
		}
	}
	if( $sonder->read_kimb_one('error-404') != $_POST['err404'] ){
		if( $sonder->write_kimb_replace( 'error-404' , $_POST['err404'] ) ){
			$sitecontent->echo_message( 'Die Fehlermedlung 404 wurde geändert!' );
		}
	}
	if( $sonder->read_kimb_one('error-403') != $_POST['err403'] ){
		if( $sonder->write_kimb_replace( 'error-403' , $_POST['err403'] ) ){
			$sitecontent->echo_message( 'Der Fehlermedlung 403 wurde geändert!' );
		}
	}
}

$sitecontent->add_site_content('<h2>Konfiguration</h2>');

$sitecontent->add_html_header('<script>
	var del = function( teil ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height: 250,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?todo=del&teil=" + teil;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	</script>');
$sitecontent->add_html_header('<script>
	$(function() { 
		nicEditors.allTextAreas({fullPanel : true, iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'});
	});
	</script>');

$confteile = $conffile->read_kimb_all_xxxid('001');

$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php">');
$sitecontent->add_site_content('<table width="100%" ><tr><th>Name</th><th>Wert</th><th width="20px;">Löschen</th><th width="20px;">Info</th></tr>');

$info = array( 'sitename' => 'Name der Seite' , 'sitefavi' => 'URL zum favicon' , 'loginokay' => 'Zufälliger Code um Login zu verifizieren' , 'siteurl' => 'GrundURL der Seite ( ohne / am Ende )' , 'description' => 'Allgemeiner Beschreibungs-Meta-Tag' , 'urlweitermeth' => 'PHP header (1) oder Meta Refresh (2) für Weiterleitungen' , 'adminmail' => 'E-Mail Adresse des Administrators' , 'robots' => 'Meta-Robots-Tag für Homepage' , 'mailvon' => 'E-Mail Absender des Systems' , 'cache' => 'on / off des Caches ( on ist empfehlenswert )' , 'sitespr' => 'Sprache der Seitenelemente ( z.Z. kein Nutzen )' , 'systemversion' => 'Version des CMS' , 'urlrewrite' => 'on / off des URL-Rewritings ( on ist empfehlenswert )' , 'cachelifetime' => 'Lebensdauer des Caches in Sekunden oder always ( always ist empfehlenswert )' , 'use_request_url' => 'Für URL-Rewriting muss der Request entweder an /index.php?url=xxx gesendert werden oder per $SERVER[REQUEST_URI] verfügbar sein. Letzteres kann hier verboten werden, da es auf manchen Server zu Problemen führen könnte. ( ok / nok )');

$i = 1;
foreach( $confteile as $confteil ){

	if( $confteil == 'systemversion' ){
		$sitecontent->add_site_content('<tr><td><input type="text" readonly="readonly" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" readonly="readonly" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span><span class="ui-icon ui-icon-trash" title="Löschen nicht erlaubt!"></span></span></td><td><span class="ui-icon ui-icon-info" title="'.$info[$confteil].'"></span></td></tr>');
	}
	else{
		$sitecontent->add_site_content('<tr><td><input type="text" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span onclick="var delet = del( \''.$confteil.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Diesen Wert löschen."></span></span></td><td><span class="ui-icon ui-icon-info" title="'.$info[$confteil].'"></span></td></tr>');
	}
	$i++;

}
$sitecontent->add_site_content('<tr><td><input type="text" placeholder="hinzufügen" name="'.$i.'"></td><td><input type="text" placeholder="hinzufügen" name="'.$i.'-wert"></td><td></td><td><span class="ui-icon ui-icon-info" title="Fügen Sie einen eigenen Wert in die allgemeine Konfiguration ein."></span></td></tr>');
$sitecontent->add_site_content('</table><input type="submit" value="Ändern"></form>');
$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>Möchten Sie den Wert wirklich löschen?<br />Tun Sie dies nur wenn Sie genau wissen was Sie tun!!</p></div></div>');

$sitecontent->add_site_content('<hr /><hr />');

$sonder = new KIMBdbf('sonder.kimb');

$sitecontent->add_site_content('<h2>Error- und Footer-Text</h2>');
$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php">');
$sitecontent->add_site_content('<textarea name="footer" style="width:99%;">'.$sonder->read_kimb_one( 'footer' ).'</textarea> <i>Footer &uarr;</i> <br />');
$sitecontent->add_site_content('<textarea name="err404" style="width:99%;">'.$sonder->read_kimb_one( 'error-404' ).'</textarea> <i>Error 404 &uarr;</i> <br />');
$sitecontent->add_site_content('<textarea name="err403" style="width:99%;">'.$sonder->read_kimb_one( 'error-403' ).'</textarea> <i>Error 403 &uarr;</i> <br />');
$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');


$sitecontent->output_complete_site();
?>