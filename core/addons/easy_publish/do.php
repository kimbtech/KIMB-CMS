<?php

defined('KIMB_Backend') or die('No clean Request');

if( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ){
	$req = substr( $_SERVER['REQUEST_URI'] , '0', '-'.strlen(strrchr( $_SERVER['REQUEST_URI'] , '?' )));
}
else{
	$req = $_SERVER['REQUEST_URI'];
}

$sitecontent->add_site_content( '<div class="ui-widget-overlay"></div>' );
$sitecontent->add_site_content( '<div class="ui-widget-shadow" style="position: absolute; left: 180px; top: 80px; opacity:1;">' );
$sitecontent->add_site_content( '<div class="ui-widget ui-widget-content" style="padding:20px; width:700px;">');
$sitecontent->add_site_content( '<h1>Easy Publish</h1>' );

if( substr( $req , -26 ) == 'kimb-cms-backend/sites.php' ){
	//seite neu
	if( $_GET['todo'] == 'new' ){
		$sitecontent->add_html_header('<script>
		$(function() {
			var form = $( "div#content" ).html().split( "<!-- END -->" );

			$( "div#content" ).html( form[0] );

			$( "div#easypubform" ).html( form[1] );
		});
		</script>');

		$sitecontent->add_site_content( 'Dafür finden Sie diese Maske:<br />' );
		$sitecontent->add_site_content( 'Bitte füllen Sie die Felder mit den Inhalten.<br />' );
		$sitecontent->add_site_content( '<ul><li>Seitentitel: Name der Seite ( HTML title, Menü kann anders benannt werden ) <b title="Pflichtfeld" >*</b></li>' );
		$sitecontent->add_site_content( '<li>HTML Header: Einen HTML Header der Seite hinzufügen ( nur für fortgeschrittene Benutzer )</li>' );
		$sitecontent->add_site_content( '<li>Keyword: Geben Sie für die Seite Schlüsselwörter für Suchmaschinen an</li>' );
		$sitecontent->add_site_content( '<li>Description: Geben Sie für die Seite eine Beschreibung für Suchmaschinen an</li>' );
		$sitecontent->add_site_content( '<li>Inhalt: Geben Sie den Seiteninhalt an. ( inklusive Titel ) <b title="Pflichtfeld" >*</b></li>' );
		$sitecontent->add_site_content( '<li>Footer: Geben Sie für die Seite einen Zusatz im Footer an</li>' );
		$sitecontent->add_site_content( '</ul><br /><hr />' );

		$sitecontent->add_site_content( '<br /><div id="easypubform"></div>' );
		
	}
	elseif( $_GET['todo'] == 'edit' && is_numeric( $_GET['id'] ) && !empty( $_GET['id'] ) ){

		$sitecontent->add_html_header('<script>
		$(function() {
			var form = $( "div#content" ).html().split( "<!-- END -->" );

			$( "div#content" ).html( form[0] );
		});
		</script>');

		$_SESSION['easypublish']['newsiteid'] = $_GET['id'];

		$sitecontent->add_site_content( 'Eine neue Seite ist jetzt erstellt, wir brauchen aber noch eine Menü!<br />' );
		$sitecontent->add_site_content( '<i>Manuell: Menue &rarr; Auflisten</i>' );

		$sitecontent->add_site_content( '<br /><br /><a href="menue.php?todo=list"><button>Weiter</button></a>' );

		$sitecontent->add_site_content( '<br /><br />Sie können die Seiteninhalt jederzeit bearbeiten.' );
		$sitecontent->add_site_content( '<i>Manuell: Seiten &rarr; Auflisten &rarr; "Seite aus List wählen"</i>' );

		$sitecontent->add_site_content( '<br /><br /><a href="sites.php?todo=edit&id='.$_GET['id'].'&noeasypub" target="_blank"><button>Seiteninhalt bearbeiten</button></a>' );
	}
	else{
		$sitecontent->add_site_content( 'Zuerst müssen wir eine neue Seite erstellen.<br />' );
		$sitecontent->add_site_content( '<i>Manuell: Seiten &rarr; Erstellen</i>' );

		$sitecontent->add_site_content( '<br /><br /><a href="?todo=new"><button>Weiter</button></a>' );
	}	
}
elseif( substr( $req , -26 ) == 'kimb-cms-backend/menue.php' ){
	//menue neu
	if( !isset( $_SESSION['easypublish']['newsiteid'] ) ){
		$sitecontent->add_site_content( 'Zuerst müssen wir eine neue Seite erstellen.<br />' );
		$sitecontent->add_site_content( '<i>Manuell: Seiten &rarr; Erstellen</i>' );

		$sitecontent->add_site_content( '<br /><br /><a href="sites.php?todo=new"><button>Weiter</button></a>' );
	}
	elseif( $_GET['todo'] == 'list' ){
		$sitecontent->add_html_header('<script>
		$(function() {
			var form = $( "div#content" ).html().split( "<!-- END -->" );

			$( "div#easypubform" ).html( form[1] );
		});
		</script>');

		$sitecontent->add_site_content( 'Für das Menü gibt es diese Tabelle: ( zu finden über <i> Menue &rarr; Auflisten</i> )<br />' );
		$sitecontent->add_site_content( 'Hier finden Sie alle Menüs des Systems und können die Eigenschaften verändern.<br />' );
		$sitecontent->add_site_content( 'Ganz rechts in der Spalte "Neu" können Sie einen Ort/ Niveau für Ihr Menue wählen. Das Plus erstellt das neue Menu auf dem gleiche Niveau und der Pfeil als Untermenü!<br />' );
		$sitecontent->add_site_content( 'Mit den Pfeilen links können Sie die Menüs innerhalb ihres Niveaus verschieben!<br />' );

		$sitecontent->add_site_content( '<br /><u><b>Weiter geht&apos;s mit der Auwahl eines Ortes!</b></u>' );

		$sitecontent->add_site_content( '<br /><div id="easypubform"></div>' );

		$sitecontent->add_site_content( '<br />Keine Menüs da? <a href="menue.php?todo=new&file=first&niveau=same">Hier</a> erstellen Sie ein Erstes!' );
		$sitecontent->add_site_content( '<br />Manuell finden Sie den Link unter <i>Menue &rarr; Erstellen</i>!' );

	}
	elseif( $_GET['todo'] == 'new' ){

		$sitecontent->add_html_header('<script>
		$(function() {
			var form = $( "div#content" ).html().split( "<!-- END -->" );

			$( "div#easypubform" ).html( form[1] );

			$( "input[name=siteid]" ).val( '.$_SESSION['easypublish']['newsiteid'].' );
		});
		</script>');

		$sitecontent->add_site_content( 'Ein neues Menü ( Menüpunkt ) erstellen Sie mit dieser Maske:<br />' );
		$sitecontent->add_site_content( '<ul><li>Menuename: Geben Sie dem Menüpunkt einen Namen ( meistens der Seitentitel ) <b title="Pflichtfeld" >*</b></li>' );
		$sitecontent->add_site_content( '<li>Pfad: Wählen Sie einen Pfad für den neuen Menüpunkt, wenn Sie das Feld leer lassen wird der Menuename verwendet.</li>' );
		$sitecontent->add_site_content( '<li>SiteID: Geben Sie die ID der Seite an, an welche das Menü geknüpft werden soll. ( Hier wurde automastich die ID der vorhin neu erstellten Seite genommen, diese finden Sie unter <i>Seiten &rarr; Auflisten</i>. )</li>' );
		$sitecontent->add_site_content( '</ul><br /><hr />' );
		$sitecontent->add_site_content( '<br /><div id="easypubform"></div>' );

	}
	elseif( $_GET['todo'] == 'edit' ){

		$sitecontent->add_site_content( 'Die neue Seite ist jetzt veröffentlicht!<br />' );

		$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$_GET['reqid'].'" target="_blank"><button>Seite ansehen</button></a><br /><br /><br />' );

		$sitecontent->add_site_content( '<a href="sites.php?todo=edit&id='.$_SESSION['easypublish']['newsiteid'].'&noeasypub" target="_blank"><button>Seite bearbeiten</button></a><br />' );
		$sitecontent->add_site_content( '<i>Manuell: Seiten &rarr; Auflisten &rarr; "Seite aus List wählen"</i><br /><br />' );
		$sitecontent->add_site_content( '<a href="menue.php?todo=edit&file='.$_GET['file'].'&reqid='.$_GET['reqid'].'&noeasypub" target="_blank" ><button>Menü bearbeiten</button></a><br />' );
		$sitecontent->add_site_content( '<i>Manuell Einstellungen: Menue &rarr; Auflisten &rarr; "Seite aus List wählen"</i><br />' );
		$sitecontent->add_site_content( '<i>Manuell Seitenzuordnung: Menue &rarr; Zuordnen</i><br /><br />' );

		$sitecontent->add_site_content( '<hr />' );

		$sitecontent->add_site_content( '<a href="sites.php?easypublishstart" ><button>Weitere Seite erstellen</button></a><br />' );
		
		$sitecontent->add_site_content( '<hr />' );

		$sitecontent->add_site_content( '<a href="syseinst.php?todo=purgecache" ><button>Cache Löschen</button></a> (Taucht die neue Seite in Menü nicht auf, löschen Sie den Cache!) <br />' );

		$sitecontent->add_site_content( '<br />Oder beenden Sie Easy Publish <span> &rarr; &rarr; &rarr; </span>' );

		unset( $_SESSION['easypublish']['newsiteid'] );

		$_SESSION['easypublish']['do'] = 'no';

	}
}
else{
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php"><button>Los geht&apos;s hier!</button></a>' );
}

$sitecontent->add_site_content( '<span><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?easypublishstop" style="float:right;"><button>Beenden</button></a></span></div></div>' );
$sitecontent->add_site_content( '<!-- END -->' );

?>
