<?php

$sitecontent->add_site_content($allgmenueid.'(Menue)<br /><br />');

//lese SiteID in Ausgabe

$sitecontent->add_menue_one_entry('Google', 'http://google.com', '1');

$sitecontent->add_menue_one_entry('Google', 'http://google.com', '2');

$sitecontent->add_site_content('Inhalt');

$sitecontent->add_footer('footer');

$sitecontent->set_title('Seitentitel');

//nach cache suchen und erstellen

//mit err umgehen
?>
