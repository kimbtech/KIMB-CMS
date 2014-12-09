<?php

define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login();

//Menues erstellen und zuordnen

$idfile = new KIMBdbf('menue/allids.kimb');
$menuenames = new KIMBdbf('menue/menue_names.kimb');
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if( $_GET['todo'] == 'new' ){
	check_backend_login('more');

}
elseif( $_GET['todo'] == 'connect' ){

	if( $_POST['post'] == 'post' ){
		//wenn verändert
			//nache Name = RequestID && Inhalt = SiteID aufschreiben
		echo 'max';
	}

	make_menue_array();
	$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect">');
	$sitecontent->add_site_content('<table width="100%"><tr> <th width="50px;"></th> <th>MenueName</th> <th>SiteID <span class="ui-icon ui-icon-info" title="Geben Sie einfach eine vorhadene SiteID in das Kästchen ein um die Seite zuzuordnen!"></span></th> </tr>');
	foreach( $menuearray as $menuear ){

		$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
		
		$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td>  <td>'.$menuear['menuname'].'</td> <td><input type="text" value="'.$menuear['siteid'].'" name="'.$menuear['requid'].'"></td> </tr>');
	}
	$sitecontent->add_site_content('</table>');
	$sitecontent->add_site_content('<input type="hidden" value="post" name="post"><input type="submit" value="Zuordnungen ändern"></form>');

}
elseif( $_GET['todo'] == 'list' ){
	check_backend_login('more');

	make_menue_array();
	$sitecontent->add_site_content('<table width="100%"><tr> <th width="50px;"></th> <th>Pfad</th> <th>NextID</th> <th>RequestID</th> <th>Status</th> <th>MenueName</th> <th>SiteID</th> <th>MenueID</th></tr>');
	foreach( $menuearray as $menuear ){

		$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
		if( $menuear['status'] == 'off' ){
			$menuear['status'] = '<span class="ui-icon ui-icon-close" title="Diese Seite ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern )"></span>';
		}
		else{
			$menuear['status'] = '<span class="ui-icon ui-icon-check" title="Diese Seite ist zu Zeit aktiviert, also sichtbar. ( click -> ändern )"></span>';
		}
		$menuear['requid'] = $menuear['requid'].'<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$menuear['requid'].'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Diese Seite aufrufen."></span></a></span>';

		$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td> <td>'.$menuear['path'].'</td> <td>'.$menuear['nextid'].'</td> <td>'.$menuear['requid'].'</td> <td>'.$menuear['status'].'</td> <td>'.$menuear['menuname'].'</td> <td>'.$menuear['siteid'].'</td> <td>'.$menuear['menueid'].'</td></tr>');
	}
	$sitecontent->add_site_content('</table>');
}
elseif( $_GET['todo'] == 'edit' ){
	check_backend_login('more');

}
elseif( $_GET['todo'] == 'del' ){
	check_backend_login('more');

}
elseif( $_GET['todo'] == 'chdeak' ){
	check_backend_login('more');

}
else{
	check_backend_login('more');

}




$sitecontent->output_complete_site();
?>
