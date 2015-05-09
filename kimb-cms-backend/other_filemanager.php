<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
/*************************************************/
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License version 3
//published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.
/*************************************************/
//www.KIMB-technologies.eu
//www.bitbucket.org/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/



define("KIMB_CMS", "Clean Request");

//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

check_backend_login( 'seventeen' );
//Filemanager
$sitecontent->add_site_content('<h2>Filemanager</h2>');

if( $_GET['secured'] == 'on' || $_GET['secured'] == 'off' ){
	$_SESSION['secured'] = $_GET['secured'];
}
if( $_SESSION['secured'] == 'on' ){
	$secured = 'on';
	$grpath = __DIR__.'/../core/secured/';
	$keyfile = new KIMBdbf( 'backend/filemanager.kimb' );
	$vorneprot = 'secured://';
}
elseif( $_SESSION['secured'] == 'off' ){
	$secured = 'off';
	$grpath = __DIR__.'/../load/userdata/';
	$vorneprot = 'open://';
}
else{
	$sitecontent->add_site_content( '<div class="ui-overlay"><div class="ui-widget-overlay"></div>');
	$sitecontent->add_site_content( '<div class="ui-widget-shadow ui-corner-all" style="width: 300px; height: 150px; position: absolute; left: 300px; top: 100px;"></div></div>');
	$sitecontent->add_site_content( '<div style="position: absolute; width: 280px; height: 130px; left: 300px; top: 100px; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all">');
	$sitecontent->add_site_content( '<p>Bitte wählen Sie, ob Sie auf das gesicherte oder das offenen Verzeichnis zugreifen wollen.<br />');
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?secured=off"><button>offen</button></a><br />');
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?secured=on"><button>geschichert</button></a><br /></p>');
	$sitecontent->add_site_content( '</div>');
	$secured = 'off';
	$grpath = __DIR__.'/../load/userdata/';
	$vorneprot = 'open://';
}

if(  (strpos($_GET['path'], "..") !== false) || (strpos($_GET['del'], "..") !== false) || (strpos($_POST['newfolder'], "..") !== false) ){
	echo ('Do not hack me!!');
	die;
}

if($_GET['todo'] == 'newf'){
	if(!is_dir( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' ) ){
		mkdir( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' );
		chmod( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' , (fileperms( $grpath ) & 0777));
		$sitecontent->echo_message( 'Ordner erstellt' );
	}
}
if($_GET['todo'] == 'del'){
	if($_GET['art'] == 'folder'){
		rm_r( $grpath.$_GET['del'].'/' );
		$sitecontent->echo_message( 'Ordner gelöscht' );
	}	
	else{
		unlink( $grpath.$_GET['del'] );

		if( $secured == 'on' ){
			$id = $keyfile->search_kimb_xxxid( $_GET['del'] , 'path' );
			if( $id != false ){
				$keyfile->write_kimb_id( $id , 'del' );
			}
		}
		$sitecontent->echo_message( 'Datei gelöscht' );
	}
}
if ( !empty( $_FILES['userfile']['name'][0] ) ){

	$i = 0;
	while( !empty( $_FILES['userfile']['name'][$i] ) ){
		$finame = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '/', '<', '>', '|', '?', ':', '|', '*'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '', '', '', '', '', '', '', ''), $_FILES['userfile']['name'][$i] );
		$finame = preg_replace( "/[^A-Za-z0-9_.-]/" , "" , $finame );
		$finame = basename($finame);
		$filena = $finame;

		if(file_exists($grpath.$_GET['path'].'/'.$finame)){
			$ii = '1';
			$fileneu = $grpath.$_GET['path'].'/'.$finame;
			while(file_exists($fileneu)){
				$fileneu = $grpath.$_GET['path'].'/'.$ii.$finame; 
				$filedd = $_GET['path'].'/'.$ii.$finame;
				$ii++;
			}
			$finame = $fileneu;
		}
		else{
			$filedd = $_GET['path'].'/'.$finame;
			$finame = $grpath.$_GET['path'].'/'.$finame;
		}

		if(move_uploaded_file($_FILES["userfile"]["tmp_name"][$i] , $finame)){
			$sitecontent->echo_message( 'Upload erfolgreich' );
		}
		else{
			$sitecontent->echo_error( 'Upload fehlerhaft!' , 'unknown' );
		}

		if( $secured == 'on' ){
			$key = makepassw( 50 , '_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );

			$id = $keyfile->next_kimb_id();

			$keyfile->write_kimb_id( $id , 'add' , 'key' , $key );
			$keyfile->write_kimb_id( $id , 'add' , 'path' , $filedd );
			$keyfile->write_kimb_id( $id , 'add' , 'file' , $filena );

		}

		$i++;
	}
}

if ($_GET['action']=='rein'){
	$openpath=$grpath.$_GET['path']."/";
	$pathnow=$_GET['path'];
}
elseif ($_GET['action']=='hoch'){
	$pfad = $_GET['path'];
	$openpath = $grpath.substr($pfad, '0', strlen($pfad) - strlen(strrchr($pfad, '/'))).'/';
	$pathnow = substr($pfad, '0', strlen($pfad) - strlen(strrchr($pfad, '/')));
}
else {
	$openpath=$grpath;
}

$sitecontent->add_html_header('<script>
var del = function( art , del , path ) {
	$( "#del-confirm" ).show( "fast" );
	$( "#del-confirm" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?todo=del&del=" + del + "&art=" + art + "&action=rein&path=" + path ;
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

if( is_dir( $openpath ) ){
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie wirklich löschen?</p></div></div>');

	$sitecontent->add_site_content ('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?action=hoch&amp;path='.urlencode($pathnow).'"><button title="<= Hoch" ><span class="ui-icon ui-icon-arrowthick-1-w" style="display:inline-block;" ></span></button></a><br />');

	$restpath = $pathnow;
	$a['url'] = $allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?action=rein&amp;path='.urlencode($restpath);
	$a['name'] = substr($restpath, - strlen(strrchr($restpath, '/')) );
	$seepatha[] = $a;
	while( strpos( $restpath , '/' ) !== false ){
		$restpath = substr($restpath, '0', strlen($restpath) - strlen(strrchr($restpath, '/')));
		$name = substr($restpath, - strlen(strrchr($restpath, '/')) );
		$a['url'] = $allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?action=rein&amp;path='.urlencode($restpath);
		$a['name'] = $name;
		$seepatha[] = $a;
	}
	$seepatha = array_reverse( $seepatha );

	$seepath = '';
	foreach( $seepatha as $ar ){
		if( empty( $ar['name'] ) ){
			$ar['name'] = '/';
		}
		$seepath .= '&nbsp;&nbsp;&nbsp;<a href="'.$ar['url'].'">'.$ar['name'].'</a>';
	}

	$sitecontent->add_site_content ('<div style="margin: 5px 0; padding: 5px; border-radius:5px; background-color:red;" title="Aktueller Pfad: Klicken Sie auf einen Ordner um dort hin zu gehen!" >'.$vorneprot.$seepath.'</div>');

	$sitecontent->add_site_content('<table width="100%">');

	if ($handle = opendir($openpath)) {
	    while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != ".." ) {
			if(is_dir($openpath.$file)){
				$sitecontent->add_site_content( '<tr style="padding:10px; background-color: orange; height: 40px;"><td><span class="ui-icon ui-icon-folder-collapsed"></span></td>');
				$sitecontent->add_site_content( '<td><span onclick="var delet = del( \'folder\' , \''.urlencode($pathnow.'/'.$file).'\' , \''.urlencode($pathnow).'\' ); delet();" class="ui-icon ui-icon-trash" title="Diesen Ordner löschen. ( Achtung, es werden alle Dateien im Ordner gelöscht! )" style="display:inline-block;" ></span></td>');
				$sitecontent->add_site_content( '<td></td><td><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?action=rein&amp;path='.urlencode($pathnow.'/'.$file).'">'.$file.'</a></td>');
				$sitecontent->add_site_content( '</tr>');
			}
			else{  
				$sitecontent->add_site_content( '<tr style="background-color: grey; padding:10px; height: 40px;"><td><span class="ui-icon ui-icon-document"></span></td>');
				$sitecontent->add_site_content( '<td><span onclick="var delet = del( \'\' , \''.urlencode($pathnow.'/'.$file).'\' , \''.urlencode($pathnow).'\' ); delet();" class="ui-icon ui-icon-trash" title="Diese Datei löschen." style="display:inline-block;" ></span></td>');
				if( $secured == 'off' ){
					$sitecontent->add_site_content( '<td><a href="'.$allgsysconf['siteurl'].'/load/userdata'.$pathnow.'/'.$file.'" target="_blank"><span class="ui-icon ui-icon-extlink" title="Öffnen Sie die Datei in einem neuen Fenster, die URL können Sie oben aus der Adressleiste kopieren und für Ihre Seiten verwenden." style="display:inline-block;" ></span></a></td>' );
				}
				else{
					$id = $keyfile->search_kimb_xxxid( $pathnow.'/'.$file , 'path' );
					if( $id != false ){
						$key = $keyfile->read_kimb_id( $id , 'key' );
					}
					else{
						$key = 'error';
					}
					$sitecontent->add_site_content( '<td><span class="ui-icon ui-icon-locked" title="Diese Datei ist geschützt und kann nur mit einer speziellen URL gefunden werden. ( Auf Seiten mit Login sinnvoll. )" ></span><a href="'.$allgsysconf['siteurl'].'/ajax.php?file=other_filemanager.php&amp;key='.$key.'" target="_blank"><span title="Öffnen Sie die Datei in einem neuen Fenster, die URL können Sie oben aus der Adressleiste kopieren und für Ihre Seiten verwenden." class="ui-icon ui-icon-extlink"></span></a></td>' );
				}
				$sitecontent->add_site_content( '<td>'.$file.'</td></tr>'); 
			}
		}
	    }
	    closedir($handle);
	}
	$sitecontent->add_site_content('</table>');

	$sitecontent->add_site_content ('<form style="padding:10px; background-color: orange;" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?todo=newf&amp;path='.urlencode($pathnow).'&amp;action=rein" method="post">');
	$sitecontent->add_site_content ('<input type="text" name="newfolder" placeholder="Neuer Ordner">');
	$sitecontent->add_site_content ('<input type="submit" value="Erstellen" title="Erstellen Sie einen neuen Ordner."></form>');

	$sitecontent->add_site_content ('<br /><hr /><h2>Datei hochladen</h2>');
	$sitecontent->add_site_content ('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?path='.urlencode($pathnow).'&amp;action=rein" enctype="multipart/form-data" method="POST">');
	$sitecontent->add_site_content ('<input name="userfile[]" type="file" multiple="multiple" /><br /><input type="submit" value="Upload" />');
	$sitecontent->add_site_content ('</form><br /><br /><hr /><br />');

}
else{

	$sitecontent->echo_error( 'Das von Ihnen gewählte Verzeichnis wurde nicht gefunden!' , '404' );
	$sitecontent->add_site_content( '<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php">Zurück</a><br /><br /><br />');

}

if( $_SESSION['secured'] == 'on' ){
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?secured=off"><button>In das offenen Verzeichnis wechseln</button></a><br />');
}
else{
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?secured=on"><button>In das geschicherte Verzeichnis wechseln</button></a><br /></p>');
}
//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
