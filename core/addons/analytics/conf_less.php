<?php

defined('KIMB_Backend') or die('No clean Request');

require_once( __DIR__.'/tracking_codes.php' );

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=analytics';


$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');


$sitecontent->add_site_content('<input name="onoff" type="radio" value="off" '.$off.'><span style="display:inline-block;" title="Ausgabe deaktiviert" class="ui-icon ui-icon-closethick"></span><input name="onoff" value="on" type="radio" '.$on.'><span style="display:inline-block;" title="Ausgabe aktiviert" class="ui-icon ui-icon-check"></span><br />');

$sitecontent->add_site_content('<textarea name="sitefi" id="nicedit1" style="width:99%; height:100px;" >'.$html_out['cont']->read_kimb_one( 'sitefi' ).'</textarea> (Zusätzlicher Seiteninhalt oben &uarr; )<br />');

$sitecontent->add_site_content('<input type="submit" value="Ändern"> </form>');

?>
