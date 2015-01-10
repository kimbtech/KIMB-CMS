<?php

defined('KIMB_Backend') or die('No clean Request');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=kontakt';


$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');


$sitecontent->add_site_content('<input name="onoff" type="radio" value="off" '.$off.'><span style="display:inline-block;" title="Ausgabe deaktiviert" class="ui-icon ui-icon-closethick"></span><input name="onoff" value="on" type="radio" '.$on.'><span style="display:inline-block;" title="Ausgabe aktiviert" class="ui-icon ui-icon-check"></span><br />');

$sitecontent->add_site_content('<textarea name="sitefi" style="width:60%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'sitefi' ).'</textarea> (Zusätzlicher Seiteninhalt oben)<br />');

$sitecontent->add_site_content('<textarea name="sitese" style="width:60%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'sitese' ).'</textarea> (Zusätzlicher Seiteninhalt unten)<br />');

$sitecontent->add_site_content('<textarea name="addonfi" style="width:60%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'addonfi' ).'</textarea> (Zusätzliche Add-onausgabe oben)<br />');

$sitecontent->add_site_content('<textarea name="addonse" style="width:60%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'addonse' ).'</textarea> (Zusätzliche Add-onausgabe unten)<br />');

$sitecontent->add_site_content('<textarea name="header" style="width:60%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'header' ).'</textarea> (Zusätzlicher Header)<br />');

$sitecontent->add_site_content('<input name="title" style="width:60%;" value="'.$html_out['cont']->read_kimb_one( 'title' ).'" > (Allgemeiner Titel)<br />');


$sitecontent->add_site_content('<input type="submit" value="Ändern"> <form>');

?>
