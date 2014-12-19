<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login('more');

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//Themes


if( isset( $_FILES['userfile']['name'] ) ){
	//core/theme und load/theme
}
if( isset( $_GET['del'] ) ){
	//del
}
if( isset( $_GET['chdeak'] ) ){
	//aktivieren
}


$sitecontent->add_html_header('<script>
var del = function( theme ) {
	$( "#del-confirm" ).show( "fast" );
	$( "#del-confirm" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_themes.php?del=" + theme;
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

$sitecontent->add_site_content('<h2>Themesliste</h2>');
$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Achtung: Themes können sich gegenseitig beeinflussen, es müssen also nicht alle unten aufgeführten Themes funktionstüchtig sein, bitte installieren Sie ein Theme erneut, sollte es Probleme gibt!"></span>');
$sitecontent->add_site_content('<table width="100%"><tr> <th>Code</th> <th>Status</th> <th>Löschen</th> </tr>');

$dir = scandir( __DIR__.'/../core/theme/' );

foreach( $dir as $file ){
	if( $file != '.' && $file != '..' ){
		if( strpos( $file , "site" ) !== false ){
			$teil = substr( $file , 12 , -4 );

			if( $teil != 'norm' ){
				$del = '<span onclick="var delet = del( \''.$teil.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Theme löschen."></span></span>';
			}

			if ( $allgsysconf['theme'] == $teil ){
				$status = '<span class="ui-icon ui-icon-check" title="Dieses Theme ist zu Zeit aktiviert. ( Bitte aktivieren Sie ein anderes, um dies zu ändern. )"></span>';
			}
			elseif( !isset( $allgsysconf['theme'] ) && $teil == 'norm' ){
				$status = '<span class="ui-icon ui-icon-check" title="Dieses Theme ist zu Zeit aktiviert. ( Bitte aktivieren Sie ein anderes, um dies zu ändern. )"></span>';
			}
			else{
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_themes.php?todo=chdeak&amp;theme='.$teil.'"><span class="ui-icon ui-icon-close" title="Dieses Theme ist zu Zeit deaktiviert. ( click -> aktivieren )"></span></a>';
			}

			$sitecontent->add_site_content('<tr> <td>'.$teil.'</td> <td>'.$status.'</td> <td>'.$del.'</td> </tr>');
		}
	}
}

$sitecontent->add_site_content('</table>');

$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie dieses Theme wirklich löschen?</p></div></div>');


$sitecontent->add_site_content('<br /><br /><h2>Theme installieren</h2>');
$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/themes.php" enctype="multipart/form-data" method="post">');
$sitecontent->add_site_content('<input name="userfile" type="file" /><br />');
$sitecontent->add_site_content('<input type="submit" value="Installieren" title="Wählen Sie eine Theme Zip Datei von Ihrem Rechner zur Installation." />');
$sitecontent->add_site_content('</form>');



$sitecontent->output_complete_site();
?>
