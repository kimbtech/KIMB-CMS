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

	//
	//Löschen eines Wertes!!
	//
}

if ( isset( $_POST['1'] ) ){

	//
	//Alle Werte annehmen, wenn anders neu!!
	//

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

$confteile = $conffile->read_kimb_all_xxxid('001');

$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php">');
$sitecontent->add_site_content('<table width="100%" ><tr><th>Name</th><th>Wert</th><th width="20px;">Löschen</th><th width="20px;">Info</th></tr>');

$info = array( 'sitename' => 'Name der Seite' , 'sitefavi' => 'URL zum favicon' , 'loginokay' => 'Zufälliger Code um Login zu verifizieren' , 'siteurl' => 'GrundURL der Seite ( ohne / am Ende )' , 'description' => 'Allgemeiner Beschreibungs-Meta-Tag' , 'urlweitermeth' => 'PHP header (1) oder Meta Refresh (2) für Weiterleitungen' , 'adminmail' => 'E-Mail Adresse des Administrators' , 'robots' => 'Meta-Robots-Tag für Homepage' , 'mailvon' => 'E-Mail Absender des Systems' , 'cache' => 'on / off des Caches ( on ist empfehlenswert )' , 'sitespr' => 'Sprache der Seitenelemente ( z.Z. kein Nutzen )' , 'systemversion' => 'Version des CMS' , 'urlrewrite' => 'on / off des URL-Rewritings ( on ist empfehlenswert )' , 'cachelifetime' => 'Lebensdauer des Caches in Sekunden oder always ( always ist empfehlenswert )');

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

$sitecontent->add_site_content('<hr />');
$sitecontent->add_site_content('<h1>Footer und Error Messages!! Nic Edit</h1>');

$sitecontent->output_complete_site();
?>
