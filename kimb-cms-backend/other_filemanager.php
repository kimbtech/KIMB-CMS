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

//Diese Datei ist Teil des Backends, sie wird direkt aufgerufen.

//Konfiguration & Klassen & Funktionen laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Diese Datei stellt unter Other den Teil "Filemanager" bereit

//Rechte überprüfen
check_backend_login( 'seventeen' );


$sitecontent->add_site_content('<h2>Filemanager</h2>');

//Es gibt zwei verschiedenen Ordner zur Dateispeicherung, einmal der offene, normale Ordner unter /load/,
//außerdem gibt es noch einen Ordner unter /core/secured/, die Dateien dort können nur mit einem Key abgefuden werden.

//Auswahl des Ordners
//	Der aktuell gewählte Ordner wird per Session gespeichert

//ist ein Wechsel gewünscht?
if( $_GET['secured'] == 'on' || $_GET['secured'] == 'off' ){
	//Session anpassen
	$_SESSION['secured'] = $_GET['secured'];
}
//secured-Ordner?
if( $_SESSION['secured'] == 'on' ){
	//Einstellungen dafür laden
	//	gesichert
	$secured = 'on';
	//	Pfad zum Ordner
	$grpath = __DIR__.'/../core/secured/';
	//	Datei mit den Keys laden
	$keyfile = new KIMBdbf( 'backend/filemanager.kimb' );
	//	dem User ein "Protokoll" anzeigen (soll wissen in welchem Ordner)
	$vorneprot = 'secured://';
}
//offener Ordner?
elseif( $_SESSION['secured'] == 'off' ){
	//Einstellungen dafür laden
	//	gesichert
	$secured = 'off';
	//	Pfad zum Ordner
	$grpath = __DIR__.'/../load/userdata/';
	//	dem User ein "Protokoll" anzeigen (soll wissen in welchem Ordner)
	$vorneprot = 'open://';
}
//kein Ordner gewählt?
else{
	//dem User ein Overlay mit der Wahlmöglichkeit anziegen
	$sitecontent->add_site_content( '<div class="ui-overlay chover"><div class="ui-widget-overlay"></div>');
	$sitecontent->add_site_content( '<div class="ui-widget-shadow ui-corner-all" style="width: 300px; height: 150px; position: absolute; left: 300px; top: 100px;"></div></div>');
	$sitecontent->add_site_content( '<div style="position: absolute; width: 280px; height: 130px; left: 300px; top: 100px; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all chover">');
	$sitecontent->add_site_content( '<p>Bitte wählen Sie, ob Sie auf das gesicherte oder das offenen Verzeichnis zugreifen wollen.<br />');
	//beim offenen Ordner muss nur das Overlay weg
	$sitecontent->add_site_content( '<button onclick="$( \'div.chover\' ).css(\'display\',\'none\');" >offen</button><br />');
	//für den sicheren muss die Seite neu geladen werden
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?secured=on"><button>geschichert</button></a><br /></p>');
	$sitecontent->add_site_content( '</div>');
	
	//im Hintergrund den offenen Ordner laden
	$secured = 'off';
	$grpath = __DIR__.'/../load/userdata/';
	$vorneprot = 'open://';
	
	//das auch in die Session setzen
	$_SESSION['secured'] = 'off';
}

//Das Dateisystem sichern, mit "./../"" könnte man sonst in tiefere Ordner gelangen 
if(  (strpos($_GET['path'], "..") !== false) || (strpos($_GET['del'], "..") !== false) || (strpos($_POST['newfolder'], "..") !== false) ){
	echo ('Do not hack me!!');
	die;
}

//neuen Ordner erstellen
if($_GET['todo'] == 'newf'){
	//Ordner sollte noch nicht existieren
	if(!is_dir( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' ) ){
		//Ordner erstellen
		mkdir( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' );
		//Rechte des Ordners auf die des Grundverzeichnisses setzen
		chmod( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' , (fileperms( $grpath ) & 0777));
		//Medlung
		$sitecontent->echo_message( 'Ordner erstellt' );
	}
}
//etws löschen?
if($_GET['todo'] == 'del'){
	//einen Ordner?
	if($_GET['art'] == 'folder'){
		//Alles in dem Ordner, und ihn auch, löschen
		rm_r( $grpath.$_GET['del'].'/' );
		//Meldung
		$sitecontent->echo_message( 'Ordner gelöscht' );
	}	
	//nur eine Datei?
	else{
		//löschen
		unlink( $grpath.$_GET['del'] );

		//bei einer Datei im secured-Ordner muss auch der Key weg
		if( $secured == 'on' ){
			//nach der ID es Key suchen (anhand ds)
			$id = $keyfile->search_kimb_xxxid( $_GET['del'] , 'path' );
			if( $id != false ){
				//wenn gefunden alles löschen
				$keyfile->write_kimb_id( $id , 'del' );
			}
		}
		//Meldung
		$sitecontent->echo_message( 'Datei gelöscht' );
	}
}

//neue Dateien hochladen?
if ( !empty( $_FILES['userfile']['name'][0] ) ){

	//Ein Dateiupload per multiple ist möglich, daher hier per Schleife
	
	//los geht's mit der ersten Datei'
	$i = 0;
	//	solange noch Dateinamen existieren diese verarbeiten
	while( !empty( $_FILES['userfile']['name'][$i] ) ){
		//erstmal den Dateinamen aufbereiten
		//	Umlaute, Leerzeichen usw. sind böse, sollten aber auch nicht einfach verschwinden (das verwirrt so machen User)
		$finame = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '/', '<', '>', '|', '?', ':', '|', '*'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '', '', '', '', '', '', '', ''), $_FILES['userfile']['name'][$i] );
		//	alles was jetzt nicht passt muss dann aber weg 
		$finame = preg_replace( "/[^A-Za-z0-9_.-]/" , "" , $finame );
		//	wir wollen wirklich nur den Dateinamen
		$finame = basename($finame);
		//	nur den Dateinamen sauber halten für später
		$filena = $finame;

		//gibt es diesen Dateinamen schon im gewählten Verzeichnis?
		if(file_exists($grpath.$_GET['path'].'/'.$finame)){
			//also ja
			
			//vorne vor den Namen eine Zahl setzen
			//	los geht's mit der 1
			$ii = '1';
			//das wäre der neue Name mit absolutem Pfad
			$fileneu = $grpath.$_GET['path'].'/'.$finame;
			//solange eine Datei existiert weiter einen Namen suchen
			while(file_exists($fileneu)){
				//wieder ein neuer absoluter Pfad zum Testen
				$fileneu = $grpath.$_GET['path'].'/'.$ii.$finame;
				//und ein neuer Pfad relativ zum gewählten Ordner (secured, offen)
				$filedd = $_GET['path'].'/'.$ii.$finame;
				//den Index erhöhen
				$ii++;
			}
			//freier Dateiname gefunden :-)
			$finame = $fileneu;
		}
		//den Dateinamen gibt es nicht
		else{
			//Pfade erstellen (absolut und relativ zum gewählten Ordner (secured, offen))
			$filedd = $_GET['path'].'/'.$finame;
			$finame = $grpath.$_GET['path'].'/'.$finame;
		}

		//veruchen die Datei an ihren neuen Platz zu kopieren
		if(move_uploaded_file($_FILES["userfile"]["tmp_name"][$i] , $finame)){
			//wenn okay Meldung
			$sitecontent->echo_message( 'Upload erfolgreich' );
		}
		else{
			//wenn fehlerhaft aber auch Meldung
			$sitecontent->echo_error( 'Upload fehlerhaft!' , 'unknown' );
		}

		//secured-Ordner?
		if( $secured == 'on' ){
			//Key erstellen
			$key = makepassw( 50 , '_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );

			//neue ID in der Keyfile suchen
			$id = $keyfile->next_kimb_id();

			//alle in die Keyfile schreiben
			//	Key
			$keyfile->write_kimb_id( $id , 'add' , 'key' , $key );
			//	Pfad zur Datei
			$keyfile->write_kimb_id( $id , 'add' , 'path' , $filedd );
			//	und den Dateinamen (sonst würde Datei beim Download immer "ajax.php" heißen)
			$keyfile->write_kimb_id( $id , 'add' , 'file' , $filena );

		}

		//die nächte Datei speichern (multiple Upload)
		$i++;
	}
}

//Variablen zum Lesen des Verzeichnisses feststellen
//	absoluter Pfad 
$openpath=$grpath.$_GET['path']."/";
//	Pfad relativ
$pathnow=$_GET['path'];
//	einen Ordner zurück
$hochpath = substr($_GET['path'], '0', strlen($_GET['path']) - strlen(strrchr($_GET['path'], '/')));

//JavaScript Code für den Löschen Dialog
$sitecontent->add_html_header('<script>
var del = function( art , del , path ) {
	$( "#filemanagerdel" ).show( "fast" );
	$( "#filemanagerdel" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?todo=del&del=" + del + "&art=" + art + "&path=" + path ;
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

//Der zu öffnenden Pfad sollte vorhanden sein!
if( is_dir( $openpath ) ){
	//Text des Löschen Dialoges
	$sitecontent->add_site_content('<div style="display:none;"><div id="filemanagerdel" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie wirklich löschen?</p></div></div>');

	//Zurück/ Hoch Button
	$sitecontent->add_site_content ('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?path='.urlencode($hochpath).'"><button title="<= Hoch" ><span class="ui-icon ui-icon-arrowthick-1-w" style="display:inline-block;" ></span></button></a><br />');

	//Erstellung der Leiste oben
	//	erstmal noch alles da
	$restpath = $pathnow;
	//	wir beginnen hinten
	//		den aktuellen Ordner anfügen
	//			Pfad
	$a['url'] = $allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?path='.urlencode($restpath);
	//			Name
	$a['name'] = substr($restpath, - strlen(strrchr($restpath, '/')) );
	//	Daten im Array speichern
	$seepatha[] = $a;
	
	//in einer Schleife solange die Leiste erstellen, bis kein Ordner im Pfad mehr vorhanden 
	while( strpos( $restpath , '/' ) !== false ){
		//dem vorhandenen Pfad den hintersten Ordner abschneiden (dieser ist schon in der Leiste)
		$restpath = substr($restpath, '0', strlen($restpath) - strlen(strrchr($restpath, '/')));
		//den Namen des neuen Ordners feststellen
		$name = substr($restpath, - strlen(strrchr($restpath, '/')) );
		//Name und URL ins erste Array
		$a['url'] = $allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?path='.urlencode($restpath);
		$a['name'] = $name;
		//beides ins zweite Array (von vor der Schleife)
		$seepatha[] = $a;
	}
	//wir hatten hinten begonnen, jetzt müssen wir das "zweite" Array umdrehen
	$seepatha = array_reverse( $seepatha );

	//erstmal Leiste leer
	$seepath = '';
	//das "zweite" Array durchgehen
	foreach( $seepatha as $ar ){
		if( empty( $ar['name'] ) ){
			//namenlos ist nur das Grundverzeichnis
			$ar['name'] = '/';
		}
		//die Leiste nach und nach füllen
		$seepath .= '&nbsp;&nbsp;&nbsp;<a href="'.$ar['url'].'">'.$ar['name'].'</a>';
	}

	//Die Leiste in rot ausgeben
	$sitecontent->add_site_content ('<div style="margin: 5px 0; padding: 5px; border-radius:5px; background-color:red;" title="Aktueller Pfad: Klicken Sie auf einen Ordner um dort hin zu gehen!" >'.$vorneprot.$seepath.'</div>');

	//eine Tabelle mit für die Ordner und Dateien des Verzeichnissen beginnen
	$sitecontent->add_site_content('<table width="100%">');

	//Verzeichnis auslesen und durchgehen
	foreach( scandir( $openpath ) as $file ){
		//keine Punkte
		if ($file != "." && $file != ".." ) {
			//ist es bei diesem Durchgang ein Ordner?
			if(is_dir($openpath.$file)){
				//Tabellenzeile für Ordnern erstellen
				//	Icon und Orange
				$sitecontent->add_site_content( '<tr style="padding:10px; background-color: orange; height: 40px;"><td><span class="ui-icon ui-icon-folder-collapsed"></span></td>');
				//	Löschen Button
				$sitecontent->add_site_content( '<td><span onclick="var delet = del( \'folder\' , \''.urlencode($pathnow.'/'.$file).'\' , \''.urlencode($pathnow).'\' ); delet();" class="ui-icon ui-icon-trash" title="Diesen Ordner löschen. ( Achtung, es werden alle Dateien im Ordner gelöscht! )" style="display:inline-block;" ></span></td>');
				//	Link um Ordner zu öffnen
				$sitecontent->add_site_content( '<td></td><td><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?path='.urlencode($pathnow.'/'.$file).'">'.$file.'</a></td>');
				$sitecontent->add_site_content( '</tr>');
			}
			//oder eine Datei
			else{ 
				//Tabellenzeile für Datei erstellen
				//	Icon und Grau
				$sitecontent->add_site_content( '<tr style="background-color: grey; padding:10px; height: 40px;"><td><span class="ui-icon ui-icon-document"></span></td>');
				//	Löschen Button
				$sitecontent->add_site_content( '<td><span onclick="var delet = del( \'\' , \''.urlencode($pathnow.'/'.$file).'\' , \''.urlencode($pathnow).'\' ); delet();" class="ui-icon ui-icon-trash" title="Diese Datei löschen." style="display:inline-block;" ></span></td>');
				//gesicherte Datei?
				if( $secured == 'off' ){
					//nein
					
					//Link zur Datei anzeigen (Popup)
					$sitecontent->add_site_content( '<td><a href="'.$allgsysconf['siteurl'].'/load/userdata'.$pathnow.'/'.$file.'" target="popup" onclick="window.open(\'\', \'popup\', \'width=900px,height=500px,top=20px,left=20px\'); "><span class="ui-icon ui-icon-extlink" title="Öffnet die Datei in einem Popup, die URL können Sie oben aus der Adressleiste kopieren und für Ihre Seiten verwenden." style="display:inline-block;" ></span></a></td>' );
				}
				else{
					//Key der gesicherten Datei suchen
					//	ID anhand des Pfades
					$id = $keyfile->search_kimb_xxxid( $pathnow.'/'.$file , 'path' );
					if( $id != false ){
						//gefunden -> Key merken
						$key = $keyfile->read_kimb_id( $id , 'key' );
					}
					else{
						//nicht gefunden -> Keyfehler
						$key = 'error';
					}
					//Link zur Datei anzeigen (Popup)
					//Schloss Icon -> gesicherte Datei!!
					$sitecontent->add_site_content( '<td><span class="ui-icon ui-icon-locked" title="Diese Datei ist geschützt und kann nur mit einer speziellen URL gefunden werden. ( Auf Seiten mit Login sinnvoll. )" ></span><a href="'.$allgsysconf['siteurl'].'/ajax.php?file=other_filemanager.php&amp;key='.$key.'" target="popup" onclick="window.open(\'\', \'popup\', \'width=900px,height=500px,top=20px,left=20px\'); "><span title="Öffnet die Datei in einem Popup, die URL können Sie oben aus der Adressleiste kopieren und für Ihre Seiten verwenden." class="ui-icon ui-icon-extlink"></span></a></td>' );
				}
				//Dateiname
				$sitecontent->add_site_content( '<td>'.$file.'</td></tr>'); 
			}
		}
	}
	//Tabelle beenden
	$sitecontent->add_site_content('</table>');

	//Formular für neuen Ordner
	$sitecontent->add_site_content ('<form style="padding:10px; background-color: orange;" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?todo=newf&amp;path='.urlencode($pathnow).'" method="post">');
	$sitecontent->add_site_content ('<input type="text" name="newfolder" placeholder="Neuer Ordner">');
	$sitecontent->add_site_content ('<input type="submit" value="Erstellen" title="Erstellen Sie einen neuen Ordner."></form>');

	//Formular für Upload
	$sitecontent->add_site_content ('<br /><hr /><h2>Datei hochladen</h2>');
	$sitecontent->add_site_content ('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php?path='.urlencode($pathnow).'" enctype="multipart/form-data" method="POST">');
	$sitecontent->add_site_content ('<input name="userfile[]" type="file" multiple="multiple" /><br /><input type="submit" value="Upload" />');
	$sitecontent->add_site_content ('</form><br /><br /><hr /><br />');

}
else{

	//Fehlermedlung wenn Datei nicht gefunden
	$sitecontent->echo_error( 'Das von Ihnen gewählte Verzeichnis wurde nicht gefunden!' , 'unknown', 'Datei nicht gefunden' );
	$sitecontent->add_site_content( '<br /><br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php">Zurück</a><br /><br /><br />');

}

//Button für Wechsel zwischen secured und offen
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
