<?php

defined('KIMB_Backend') or die('No clean Request');

$kontakt['file'] = new KIMBdbf( 'addon/kontakt__file.kimb' );

$ch = array( ' ' , ' ' , ' ' , ' ' , ' ' , ' ' );

if( $kontakt['file']->read_kimb_one( 'mail' ) == 'on' ){
	$ch[2] = ' checked="checked" ';
}
else{
	$ch[1] = ' checked="checked" ';
}
if( $kontakt['file']->read_kimb_one( 'form' ) == 'on' ){
	$ch[4] = ' checked="checked" ';
}
else{
	$ch[3] = ' checked="checked" ';
}
if( $kontakt['file']->read_kimb_one( 'other' ) == 'on' ){
	$ch[6] = ' checked="checked" ';
}
else{
	$ch[5] = ' checked="checked" ';
}

$sitecontent->add_html_header('<script>
$(function() { 
	nicEditors.allTextAreas({buttonList : [] , iconsPath : \''.$allgsysconf['siteurl'].'/load/system/nicEditorIcons.gif\'});
});
</script>');

$sitecontent->add_site_content('<br /><br /><form action="" method="post" >');

$sitecontent->add_site_content('<input readonly="readonly" name="id" type="text" value="'.$kontakt['file']->read_kimb_one( 'siteid' ).'" > ( SiteID <b title="Bitte geben Sie hier die SeitenID, der Seite auf welcher die Kontaktinfos erscheinen sollen, ein. ( Seiten -> Auflisten )">*</b> )<br />');
$sitecontent->add_site_content('<input readonly="readonly" type="radio" name="mailoo" value="off"'.$ch[1].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse deaktiviert" class="ui-icon ui-icon-closethick"></span> <input readonly="readonly" type="radio" name="mailoo" value="on"'.$ch[2].'> <span style="display:inline-block;" title="Bild der E-Mail-Adresse aktiviert" class="ui-icon ui-icon-check"></span> (E-Mail-Adresse)<br />');
$sitecontent->add_site_content('<input readonly="readonly" type="radio" name="formoo" value="off"'.$ch[3].'> <span style="display:inline-block;" title="Kontakformular deaktiviert" class="ui-icon ui-icon-closethick"></span> <input readonly="readonly" type="radio" name="formoo" value="on"'.$ch[4].'> <span style="display:inline-block;" title="Kontaktformular aktiviert" class="ui-icon ui-icon-check"></span> (Kontaktformular)<br />');
$sitecontent->add_site_content('<input readonly="readonly" type="radio" name="otheroo" value="off"'.$ch[5].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt deaktiviert" class="ui-icon ui-icon-closethick"></span> <input readonly="readonly" type="radio" name="otheroo" value="on"'.$ch[6].'> <span style="display:inline-block;" title="Über JavaScript gesicherter Inhalt aktiviert" class="ui-icon ui-icon-check"></span> (JavaScript Inhalt)<br /><br />');
$sitecontent->add_site_content('<input readonly="readonly" name="mail" type="text" value="'.$kontakt['file']->read_kimb_one( 'formaddr' ).'" > ( Mail-Adresse <b title="Die Adresse wird, wenn aktiviert, als Bild auf der Seite angezeigt und für das Kontaktformular genutzt!">*</b>)<br />');
$sitecontent->add_site_content('<textarea readonly="readonly" name="othercont" style="width:99%;">'.$kontakt['file']->read_kimb_one( 'othercont' ).'</textarea> ( Über JavaScript gesicherter Inhalt &uarr; <b title="Der Text wird so nachgeladen, dass es für Bots schwer ist ihn zu lesen, so lassen sich z.B. Telefonnummern und Adressen schützen!">*</b>)<br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern" disabled="disabled"> <span style="display:inline-block;" class="ui-icon ui-icon-info" title="Dies ist nur eine Einstellungsübersicht, bitte wählen Sie mit entsprechenden Rechten Add-ons -> Konfiguration"></span> <form>');
?>
