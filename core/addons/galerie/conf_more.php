<?php

defined('KIMB_Backend') or die('No clean Request');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie';

$sitecontent->add_site_content('<hr /><br /><h2>Bildergalerie</h2>');

$cfile = new KIMBdbf( 'addon/galerie__conf.kimb' );

if( is_numeric( $_GET['id'] ) ){
	$sitecontent->add_site_content('<h3>Bearbeiten</h3>');
	$sitecontent->add_site_content('<a href="'.$addonurl.'"></a>');

	if( isset( $_POST['imgpath'] ) ){
		print_r( $_POST );
	}

	$all = $cfile->read_kimb_id( $_GET['id'] );

	$sitecontent->add_site_content('<form method="post" action="'.$addonurl.'&amp;id='.$_GET['id'].'">');

	$sitecontent->add_html_header('<script>$(function(){ $( "[name=id]" ).val( '.$all['siteid'].' ); }); </script>');
	$sitecontent->add_site_content( id_dropdown( 'id', 'siteid' ).' ( SiteID <b title="Bitte wählen Sie die Seite, auf welcher die Galerie angezeigt werden soll.">*</b> )<br />');

	$sitecontent->add_site_content('<input type="text" value="'.$all['imgpath'].'" name="imgpath" readonly="readonly"> ( Bildpfad <b title="Bitte wählen Sie einen Pfad unter Others -> Filemanager. Dieser lässt sich später nicht ändern! ( Bitte laden Sie mit dem Filmanager alle ihre Dateien in diesen Pfad )">*</b> )');
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?action=rein&amp;path='.$all['imgpath'].'" target="_blank" >Zum Filemanager</a><br />');


	if( $all['rand'] == 'on' ){
		$rand[1] = ' ';
		$rand[2] = ' checked="checked"';
	}
	else{
		$rand[2] = ' ';
		$rand[1] = ' checked="checked"';
	}
	$sitecontent->add_site_content('<input type="radio" name="rand" value="off"'.$rand[1].'> <span style="display:inline-block;" title="Bilderreihenfolge zufällig." class="ui-icon ui-icon-closethick"></span>');
	$sitecontent->add_site_content('<input type="radio" name="rand" value="on"'.$rand[2].'> <span style="display:inline-block;" title="Bilderreihenfolge nach Dateireihenfolge." class="ui-icon ui-icon-check"></span> (Zufall)<br />');

	$sitecontent->add_site_content('<input type="text" name="size" value="'.$all['size'].'"> ( Größe <b title="Bitte geben Sie eine maximale Pixelzahl für die Vorschau ein.">*</b> )<br />');

	$sitecontent->add_site_content('<input type="text" name="anz" value="'.$all['anz'].'"> ( Anzahl <b title="Bitte geben Sie eine maximale Anzahl an Bilder ein. ( Leer => 99999 )">*</b> )<br />');

	$sitecontent->add_html_header('<script>$(function(){ $( "[name=pos]" ).val( \''.$all['pos'].'\' ); }); </script>');
	$sitecontent->add_site_content('<select name="pos"><option value="top">Oben</option><option value="bottom">Unten</option><option value="none">Manuell</option></select>( Platzierung <b title="Soll die Galerie oben oder unten auf der Seite sein oder platzieren Sie die Galerie per HTML-Code ( &apos;'.htmlspecialchars( '<div class="imggallerydisplayhere" style="background-color:#ddd; border-radius:15px;" >Bitte aktivieren Sie für die Bildergalerie JavaScript!</div>' ).'&apos; ).">*</b> )<br />');

	$sitecontent->add_site_content('<input type="submit" value="Anpassen"></form>');

	$sitecontent->add_site_content( '<hr /><br />'.htmlspecialchars( '<div class="imggallerydisplayhere" style="background-color:#ddd; border-radius:15px;" >Bitte aktivieren Sie für die Bildergalerie JavaScript!</div>' ) );

}
elseif( isset( $_GET['path'] ) ){

	//new

	open_url( '/kimb-cms-backend/addon_conf.php?todo=more&addon=galerie&id='.$id );
	die;
}
else{
	$sitecontent->add_site_content('<h3>Liste</h3>');
	$sitecontent->add_site_content( '<span class="ui-icon ui-icon-info" title="Hier können Sie Einstellungen Ihrer Bildergalerien bearbeiten. In der Liste werden die SiteIDs ( Seiten -> Auflisten ) angezeigt."></span>' );
	$sitecontent->add_site_content( '<table width="100%">' );
	$sitecontent->add_site_content( '<tr> <th>SiteID</th> <th>Bildpfad</th> <th>Position</th> </tr>' );

	foreach( $cfile->read_kimb_all_teilpl( 'galid' ) as $id ){
		$all = $cfile->read_kimb_id( $id );

		$sitecontent->add_site_content('<tr> <td><a href="'.$addonurl.'&amp;id='.$id.'" title="Bearbeiten">'.$all['siteid'].'</a></td> <td>'.$all['imgpath'].'</td> <td>'.$all['pos'].'</td> <tr>');
	}
	$sitecontent->add_site_content( '</table>' );
}

?>
