<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2015 by KIMB-technologies
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

defined('KIMB_CMS') or die('No clean Request');

//Freigegebene Dateien/ Ordner anzeigen
function show_freig_file () {
	global $allgsysconf;
	
	if( strpos( $_GET['user'], '..') === false ){
	
		//Datei für Keys öffnen
		$freigfile = new KIMBdbf( 'addon/daten__user_'.$_GET['user'].'.kimb' );
		
		//Key suchen	
		$key_id = $freigfile->search_kimb_xxxid( $_GET['key'] , 'key' );
		//Key vorhanden?
		if(  $key_id != false ){
			
			//Daten über Datei auslesen
			//	Pfad zur Datei
			$path = $freigfile->read_kimb_id( $key_id , 'path' );
			//	und den Dateinamen (sonst würde Datei beim Download immer "ajax.php" heißen)
			$name = $freigfile->read_kimb_id( $key_id , 'name' );
			//	Typ (Datei/ Ordner)
			$type = $freigfile->read_kimb_id( $key_id , 'type' );
			//	Upload erlaubt (nur bei Ordnern)
			$upload = $freigfile->read_kimb_id( $key_id , 'upload' );

			//Tracking Array lesen
			if( !is_array( $_SESSION['freigabeaufrufe'] ) || !in_array( $_GET['user'].$_GET['key'] ,$_SESSION['freigabeaufrufe'] ) ){
				$track = $freigfile->read_kimb_id( $key_id , 'track' );
				if( !is_array( $track ) ){
					$track = array();
				}
				//Daten sammeln
				$track[] = array(
					//Zeitpunkt (erster Aufruf innerhalb der Session)
					'time' => time(),
					//Linkquelle
					'ref' => ( filter_var( $_SERVER['HTTP_REFERER'] , FILTER_VALIDATE_URL ) ? $_SERVER['HTTP_REFERER'] : 'unknown' ),
					//ID (letzte Stellen [ab .] mit xxx)
					'ip' =>  ( filter_var( $_SERVER['REMOTE_ADDR'] , FILTER_VALIDATE_IP ) ? substr( $_SERVER['REMOTE_ADDR'], 0, strrpos( $_SERVER['REMOTE_ADDR'], '.' ) ).'.xxx' : 'unknown' ),
					//Host 
					'host' => ( !empty( gethostbyaddr( $_SERVER['REMOTE_ADDR'] ) ) ? gethostbyaddr( $_SERVER['REMOTE_ADDR'] ) : 'unknown' ),
					//UserAgent
					'ua' => ( !empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown' )
				);

				//Array ablegen
				$freigfile->write_kimb_id( $key_id , 'add', 'track', $track );

				//jetzt aber drin
				$_SESSION['freigabeaufrufe'][] = $_GET['user'].$_GET['key'];
			}
			
			//Pfad für Datei erstellen
			$file = __DIR__.'/userdata/user/'.$_GET['user'].$path;
			
			//Datei? (und vorhanden)
			if( is_file( $file ) && $type == 'file' ){
			
				//Datei passend anzeigen Funktion
				open_freigfile( $file, $name );
				
			}
			//Ordner ? (und vorhanden)
			elseif( is_dir( $file ) && $type ==  'folder' ){

				//Pfad prüfen
				//	Root?
				if( $_GET['path'] == '/' || $_GET['path'] == '' ){
					$openfolder = $file.'/';
				}
				//	Unterverzeichnis okay?
				else{
					//do not hack
					if( strpos( $_GET['path'], '..') !== false ){
						echo "Falscher Pfad!";
						die;
					}
					else{
						//Slashes richtig setzen (Anfang)
						if( substr( $_GET['path'], 0, 1) == '/' ){
							$openfolder = $file.$_GET['path'];
						}
						else{
							$openfolder = $file.'/'.$_GET['path'];
						}
					}
				}

				//Ordner zu öffnen ?
				if( is_dir( $openfolder ) ){
					// Slash am Ende
					if( substr( $_GET['path'], -1) != '/' ){
						$_GET['path'] .= '/';
					}

					//Dateien hochgeladen?
					if( $upload == 'yes' ){

						//für Ausgabe
						$uploads = array();

						//neue Dateien hochladen?
						if ( !empty( $_FILES['files']['name'][0] ) ){
							//Ein Dateiupload per multiple ist möglich, daher hier per Schleife
	
							//los geht's mit der ersten Datei
							$i = 0;
							//	solange noch Dateinamen existieren diese verarbeiten
							while( !empty( $_FILES['files']['name'][$i] ) ){

								//keine .. im Pfad -  Dateisystemschutz
								if( strpos( $_FILES['files']['name'][$i], '..' ) === false ){
				
									//neuen Dateinamen bereinigen
									$newdateiname = $_FILES['files']['name'][$i];
									//	Umlaute und Leerezeichen
									$newdateiname = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '.'), $newdateiname);
									//	Rest weg
									$newdateiname = preg_replace( '/([^A-Za-z0-9\_\.\-])/' , '' , $newdateiname );

									//gibt es diesen Dateinamen schon im gewählten Verzeichnis?
									if( file_exists( $openfolder.'/'.$newdateiname ) ){
										//also ja
										
										//vorne vor den Namen eine Zahl setzen
										//	los geht's mit der 1
										$ii = '1';

										//freien Namen suchen
										do{
											//wäre neuer Pfad
											$fileneu = $openfolder.'/'.$ii.$newdateiname;
											//Nummer fürs nächste Mal erhöhen
											$ii++;
											//	Datei vorhanden? (wenn ja neuen Versuch)
										}while( file_exists( $fileneu ) );
									}
									else{
										//gleich Dateinamen erstellen
										$fileneu = $openfolder.'/'.$newdateiname;
									}
				
									//Datei verschieben (mit passendem Namen)
									if( move_uploaded_file( $_FILES['files']['tmp_name'][$i] , $fileneu ) ){
										$uploads[] = '"<u>'.$newdateiname.'</u>" erfolgreich hochgeladen!';
									}
									else{
										$uploads[] = '"<u>'.$newdateiname.'</u>" <b>nicht</b> hochgeladen!';
									}
								}

								//die nächte Datei speichern (multiple Upload)
								$i++;
							}
						}

						if( $uploads != array() ){
							$message = '<div id="message">'."\r\n";
							$message .= implode( "<br />\r\n", $uploads );
							$message .= '</div>'."\r\n";
						}

					}

					//HTML um freigegebene Ordner zu zeigen
					$html = '<!DOCTYPE html>'."\r\n";
					$html .= '<html>'."\r\n";
					$html .= '<head>'."\r\n";
					$html .= '<link rel="shortcut icon" href="'.$allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
					$html .= '<link rel="icon" href="'.$allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
					$html .= '<meta name="generator" content="KIMB-technologies CMS V. '.$allgsysconf['systemversion'].'" >'."\r\n";
					$html .= '<meta name="robots" content="none">'."\r\n";
					$html .= '<meta name="description" content="'.$allgsysconf['description'].'">'."\r\n";
					$html .= '<meta charset="utf-8">'."\r\n";
					$html .= '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.css" >'."\r\n";
					$html .= '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/system/theme/fonts.min.css">'."\r\n";
					$html .= '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/addondata/daten/ordner_freigabe.min.css">'."\r\n";
					$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>'."\r\n";
					$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.js"></script>'."\r\n";
					$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/ordner_freigabe.min.js"></script>'."\r\n";
					$html .= '<title>Freigabe Ordner: '. basename($path) .'/</title>'."\r\n";					
					$html .= '</head>'."\r\n";
					$html .= '<body>'."\r\n";
					$html .= '<div id="main">'."\r\n";
					$html .= '<h1>Freigabe Ordner: '. basename($path) .'/</h1>'."\r\n";

					//Meldung?
					$html .= ( isset( $message ) ? $message : '' );

					//Linkgerüst
					$linkger = $allgsysconf['siteurl'].'/ajax.php?addon=daten&amp;user='.$_GET['user'].'&amp;key='.$_GET['key'].'&amp;path=';
					//Hoch Button
					$html .= '<button onclick="ordner_hoch( \''.$_GET['path'].'\', \''.$linkger.'\' );"><span class="ui-icon ui-icon-arrowthick-1-w"></span></button>'."\r\n";

					$html .= '<span>freig:/'.$_GET['path'].'</span>'."\r\n";

					//Dateiliste
					$html .= '<ul>'."\r\n";		

					//noch leer
					$added = false;
						
					//Ordner auslesen
					foreach( scandir( $openfolder ) as $fi ){
						//keine Steuerzeichen
						if( $fi != '.' && $fi != '..' ){

							//Unterorder?
							if( is_dir( $openfolder.'/'.$fi ) ){

								//Link
								$link = $linkger.urlencode($_GET['path'].$fi.'/' );
								//Listenelemnt
								$html .= '<li class="dir"><a href="'.$link.'">'.$fi.'</a></li>'."\r\n";
							}
							else{
								//Link
								$link = $linkger.urlencode($_GET['path'].$fi );

								//Tabelle oder Datei??
								$class = ( ( substr( $fi, -11 ) == '.kimb_table' ) ? 'table' : 'file'  );

								//Listenelement
								$html .= '<li class="'.$class.'"><a href="#" onclick="open_file(\''.$link.'\');">'.$fi.'</a></li>'."\r\n";
							}

							//jetzt gefüllt
							$added = true;
						}
					}

					//Meldung, wenn leer
					if( !$added ){
						$html .= '<li>Der Ordner ist leer!</li>'."\r\n";
					}

					//Tabelle und Seite beenden
					$html .= '</ul>'."\r\n";

					//Dateien hochladen okay?
					if( $upload == 'yes' ){
						$html .= '<h3>Dateiupload</h3>'."\r\n";
						$html .= '<form action="'.$linkger.urlencode($_GET['path']).'" method="post" enctype="multipart/form-data">'."\r\n";
						$html .= '<input type="file" name="files[]" multiple="multiple"><br />'."\r\n";
            						$html .= '<input type="submit" value="Upload"><br />'."\r\n";
						$html .= '</form>'."\r\n";
					}
						
					$html .= '</div>'."\r\n";
					$html .= '<center><small><a href="'.$allgsysconf['siteurl'].'" target="_blank">Zur Seite</a></small></center>'."\r\n";
					$html .= '</body>'."\r\n";
					$html .= '</html>'."\r\n";

					//ausgeben
					echo $html;
				}
				else{
					//Dateiname
					$name = basename( $openfolder );
					//kein Ordner, also als Datei öffnen
					open_freigfile( $openfolder, $name );
				}
			}	
			else{
				echo 'Diese Datei existiert nicht!';
			}			
		}
		else{
			echo 'Der Key für diese Datei ist nicht gültig!';
		}
	}
	else{
		echo 'Syntax des Usernamens inkorrekt!';
	}
	
	die;
}

//Freigegebene Datei öffnen (Download/ Tabelle)
function open_freigfile( $file, $name ){
	global $allgsysconf;
	
	//Tabelle ?
	if( substr( $file, -11 ) == '.kimb_table' ){
		//Dateiinhalt
		$filecont = file_get_contents( $file );
				
		//Name der Datei
		$tabname = substr( $name,0,  -11 );
					
		//HTML und JS um freigegebene Tabellen zu entschlüsseln
		$html = '<!DOCTYPE html>'."\r\n";
		$html .= '<html>'."\r\n";
		$html .= '<head>'."\r\n";
		$html .= '<link rel="shortcut icon" href="'.$allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
		$html .= '<link rel="icon" href="'.$allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
		$html .= '<meta name="generator" content="KIMB-technologies CMS V. '.$allgsysconf['systemversion'].'" >'."\r\n";
		$html .= '<meta name="robots" content="none">'."\r\n";
		$html .= '<meta name="description" content="'.$allgsysconf['description'].'">'."\r\n";
		$html .= '<meta charset="utf-8">'."\r\n";
		$html .= '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/system/theme/fonts.min.css">'."\r\n";
		$html .= '<style>body{font-family:Ubuntu,sans-serif;}</style>'."\r\n";
		$html .= '<script> var enctab = '. json_encode( $filecont ) .';</script>'."\r\n";
		$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/sjcl.min.js"></script>'."\r\n";
		$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>'."\r\n";
		$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/tabellen_freigabe.min.js"></script>'."\r\n";
		$html .= '<title>Tabelle: '. $tabname .'</title>'."\r\n";					
		$html .= '</head>'."\r\n";
		$html .= '<body>'."\r\n";
		$html .= '<h1>Tabelle: '. $tabname .'</h1>'."\r\n";
		$html .= '<div style="display:none;" id="passinput">'."\r\n";
		$html .= '<input type="password" id="pass" placeholder="Passwort"><button>Tabelle laden</button>'."\r\n";
		$html .= '</div>'."\r\n";
		$html .= '<hr /><div style="width:80%; margin-left:10%;" class="tabelle">Bitte geben Sie das Passwort oben ein!</div><hr />'."\r\n";
		$html .= '<small><a href="'.$allgsysconf['siteurl'].'" target="_blank">Zur Seite</a></small>'."\r\n";
		$html .= '</body>'."\r\n";
		$html .= '</html>'."\r\n";
					
		//ausgeben
		echo $html;
					
	}
	//verschlüsselte Datei? (HTML-Code; nicht raw)
	elseif( substr( $file, -9 ) == '.kimb_enc' && !isset( $_GET['raw'] ) ){
		//Dateiname
		$datname = substr( $name, 0, -9 );

		//verschlüsselte Datei!
		//HTML und JS um freigegebene Tabellen zu entschlüsseln
		$html = '<!DOCTYPE html>'."\r\n";
		$html .= '<html>'."\r\n";
		$html .= '<head>'."\r\n";
		$html .= '<link rel="shortcut icon" href="'.$allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
		$html .= '<link rel="icon" href="'.$allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n";
		$html .= '<meta name="generator" content="KIMB-technologies CMS V. '.$allgsysconf['systemversion'].'" >'."\r\n";
		$html .= '<meta name="robots" content="none">'."\r\n";
		$html .= '<meta name="description" content="'.$allgsysconf['description'].'">'."\r\n";
		$html .= '<meta charset="utf-8">'."\r\n";
		$html .= '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/system/theme/fonts.min.css">'."\r\n";
		$html .= '<style>body{font-family:Ubuntu,sans-serif;}</style>'."\r\n";
		$html .= '<script> var enctab = '. json_encode( $filecont ) .';</script>'."\r\n";
		$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/sjcl.min.js"></script>'."\r\n";
		$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>'."\r\n";
		$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/FileSaver.min.js"></script>'."\r\n";
		$html .= '<script> var filename = '. json_encode( $datname ) .';</script>'."\r\n";
		$html .= '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/addondata/daten/verschl_freigabe.min.js"></script>'."\r\n";
		$html .= '<title>Verschlüsselte Datei: '. $datname .'</title>'."\r\n";				
		$html .= '</head>'."\r\n";
		$html .= '<body>'."\r\n";
		$html .= '<h1>Verschlüsselte Datei: '. $datname .'</h1>'."\r\n";
		$html .= '<input type="password" id="pass" placeholder="Passwort"><br />'."\r\n";
		$html .= '<button id="dec">Datei entschlüsseln</button><br />'."\r\n";
		$html .= '<img src="'.$allgsysconf['siteurl'].'/load/system/spin_load.gif" style="visibility:hidden">'."\r\n";
		$html .= '<br /><br /><small><a href="'.$allgsysconf['siteurl'].'" target="_blank">Zur Seite</a></small>'."\r\n";
		$html .= '</body>'."\r\n";
		$html .= '</html>'."\r\n";
					
		//ausgeben
		echo $html;
	}
	else{
		//reine Datei?

		//Größe
		$filesize = filesize( $file );
		//MIME
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_file($finfo, $file);
		finfo_close($finfo);
			
		//Header
		header( 'Content-type: '.$mimetype.'; charset=utf-8' );
		header( 'Content-Disposition: inline; filename="'.$name.'"' );
		header( 'Content-Length: '.$filesize);
		//Ausgabe
		readfile( $file );
	}

	die;
}

//Systemkonfigurationsdatei
$sysfile = new KIMBdbf( 'addon/daten__conf.kimb' );

//Rechte für diese Seite??
if( check_felogin_login( '---session---', $sysfile->read_kimb_one( 'siteid' ), true ) ){
	
	//AJAX Anfrage für JSON Ausgaben?
	if( is_array( $_POST['allgvars'] ) ){
		
		//Fehler
		$errormsg = '{ "error": "Die Anfrage war fehlerhaft!"}';
		
		//Grundverzeichnis
		$filepath = __DIR__.'/userdata';
		
		//korrekte Wahl 
		//	Daten des Users
		//	Daten einer Gruppe
		//	Daten für alle User?
		if( $_POST['allgvars']['folder'] == 'public' || $_POST['allgvars']['folder'] == 'user'  ){
			//Ordner für Wahl öffnen
			$filepath .= '/'.$_POST['allgvars']['folder'];
		}
		else{
			//falsche Wahl
			echo $errormsg;
			die;
		}
		
		//Daten eines Users gewählt?
		if( $_POST['allgvars']['folder'] == 'user' ){
			
			//Username => Userverzeichnis
			$filepath .= '/'.$_SESSION['felogin']['user'];
			
			//Ordner für User vorhanden
			if( !is_dir( $filepath ) ){
				//wenn nicht, dann erstellen
				mkdir( $filepath.'/' );
				chmod( $filepath , (fileperms( __DIR__ ) & 0777));
			}
			
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['path'], '..') === false ){
				//gewünschten Pfad ansetzen
				$filepath .= $_POST['allgvars']['path'];
	
			}
			else{
				//Pfad unsicher!
				echo $errormsg;
				die;
			}
			
		}
		//Daten aller Users gewählt?
		elseif( $_POST['allgvars']['folder'] == 'public' ){
		
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['path'], '..') === false ){
				//gewünschten Pfad ansetzen
				$filepath .= $_POST['allgvars']['path'];
	
			}
			else{
				//Pfad unsicher!
				echo $errormsg;
				die;
			}
		
		}
		
		//Dateiliste gewünscht?		
		if( $_POST["todo"] == "filelist" ){

			//Ordner vorhanden?
			if( is_dir( $filepath ) ){
				
				//Dateien im Ordner lesen
				$files = scandir( $filepath );
				//Arrays für Ausgabe
				$out_dirs = array();
				$out_files = array();
			
				//alle Dateien durchgehen 
				foreach( $files as $fi ){
					
					//keine . und ..
					if( $fi != '.' && $fi != '..' ){
						
						//url => Pfad
						//fi => Name der Datei (bei Tabelle abweichend)
						$url = $fi;
						
						//Datei?
						if( is_file( $filepath.'/'.$fi ) ){
							
							//Tabelle?
							if( substr( $fi, -11 ) == '.kimb_table' ){
								//Typ und Namen anpassen
								$type = 'kt';
								$fi = substr( $fi, 0, -11 );
							}
							else{
								//Typ Datei
								$type = 'file';
							}
							
							//für Ausgabe ablegen
							$out_files[] = array( 'name' => $fi, 'url' => $url, 'type' => $type );
						}
						//Ordner
						elseif( is_dir( $filepath.'/'.$fi ) ){
							//Typ Ordner
							$type = 'dir';
							
							//für Ausgabe ablegen
							$out_dirs[] = array( 'name' => $fi, 'url' => $url, 'type' => $type );
						}				
					}
				}
				
				//Ausgabe zusammenstellen (Ordner oben, dann Dateien)
				$all_output = array_merge( $out_dirs, $out_files );
				
				//Array erweitern
				$all_output = array( 'filelist' => $all_output, 'folder_ex' => true);
			}
			else{
				$all_output = array( 'filelist' => null, 'folder_ex' => false);
			}
		}
		//Tabelle laden
		elseif( $_POST['todo'] == 'table' ){
			
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['file'], '..' ) === false ){
				
				$file = $filepath.'/'.$_POST['allgvars']['file'];
				
				//Datei vorhanden
				if( is_file( $file ) ){
					$all_output = file_get_contents( $file );
				}
				else{
					//Pfad unsicher!
					echo $errormsg;
					die;
				}
	
			}
			else{
				//Pfad unsicher!
				echo $errormsg;
				die;
			}
		}
		//Tabelle schreiben
		elseif( $_POST['todo'] == 'tablesave' ){
			
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['file'], '..' ) === false ){
				
				//neuen Dateinamen bereinigen
				$newdateiname = $_POST['allgvars']['file'];
				//	Umlaute und Leerezeichen
				$newdateiname = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '.'), $newdateiname);
				//	Rest weg
				$newdateiname = preg_replace( '/([^A-Za-z0-9\_\.\-])/' , '' , $newdateiname );
				
				$file = $filepath.'/'.$newdateiname;
				
				//Daten übertragen
				if( !empty( $_POST['data'] )){
					//Tabelle schreiben
					if( file_put_contents( $file, $_POST['data'] ) ){
						$all_output['wr'] = true;
					}
					else{
						$all_output['wr'] = false;
					}
				}
				else{
					//Pfad unsicher!
					echo $errormsg;
					die;
				}
	
			}
			else{
				//Pfad unsicher!
				echo $errormsg;
				die;
			}
		}
		//Neuer Ordner
		elseif( $_POST['todo'] == 'newfolder' ){
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['file'], '..' ) === false ){
				
				//neuen Dateinamen bereinigen
				$newdateiname = $_POST['allgvars']['file'];
				//	Umlaute und Leerezeichen
				$newdateiname = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '.'), $newdateiname);
				//	Rest weg
				$newdateiname = preg_replace( '/([^A-Za-z0-9\_\.\-])/' , '' , $newdateiname );
				
				$output_dev[] = $newdateiname;
				
				//Pfad zum Ordner
				$file = $filepath.'/'.$newdateiname;
				
				//Ordner erstellen
				if( mkdir( $file ) && chmod( $file , (fileperms( $filepath ) & 0777)) ){
					$all_output['wr'] = true;
				}
				else{
					$all_output['wr'] = false;
				}
			}
		}
		//Datei löschen
		elseif( $_POST['todo'] == 'delfile' ){
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['file'], '..' ) === false ){
				
				//Pfad zum Ordner
				$file = $filepath.'/'.$_POST['allgvars']['file'];
				
				//Datei und löschen okay
				if( is_dir( $file ) && rm_r( $file ) ){
					$all_output['wr'] = true;
				}
				//Ordner und löschen okay
				elseif( is_file( $file ) && unlink( $file ) ){
					$all_output['wr'] = true;
				}
				else{
					$all_output['wr'] = false;
				}
			}
		}
		//Datei hochladen 
		elseif( $_POST['todo'] == 'uploadfile' ) {
			
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_FILES['file']['name'], '..' ) === false ){
				
				//neuen Dateinamen bereinigen
				$newdateiname = $_FILES['file']['name'];
				//	Umlaute und Leerezeichen
				$newdateiname = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '.'), $newdateiname);
				//	Rest weg
				$newdateiname = preg_replace( '/([^A-Za-z0-9\_\.\-])/' , '' , $newdateiname );
				
				//Pfad zum Ordner
				$file = $filepath.'/'.$newdateiname;
				
				//Datei und löschen okay
				if( move_uploaded_file( $_FILES['file']['tmp_name'] , $file ) ){
					$all_output['wr'] = true;					
				}
				else{
					$all_output['wr'] = false;
				}
			}
			
		}
		elseif( $_POST['todo'] == 'newfreigabe' ){
			
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['file'], '..' ) === false ){
				
				//Username (=Grundpfad)	
				$user = $_SESSION['felogin']['user'];
				//Dateipfad (bezogen auf Grundpfad)
				$file = $_POST['allgvars']['path'].$_POST['allgvars']['file'];
				//Dateiname
				$filena = $_POST['allgvars']['file'];
				
				if( file_exists( __DIR__.'/userdata/user/'.$user.'/'.$file ) ){
					
					//Datei für Keys öffnen
					$freigfile = new KIMBdbf( 'addon/daten__user_'.$user.'.kimb' );
					
					do{
						//Key erstellen
						$key = makepassw( 50 , '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );
						
						//Key schon vergeben (so lange bis neuer gefunden, immer neuen machen)
					} while ( $freigfile->search_kimb_xxxid( $key , 'key' ) != false );

					//Datei schon freigegeben?
					//	alte ID suchen
					$old_id = $freigfile->search_kimb_xxxid( $file , 'path' );
					//	alte ID vorhanden?
					if(  $old_id != false ){
						$id = $old_id;
					}
					else{
						//neue ID in der Keyfile suchen
						$id = $freigfile->next_kimb_id();
					}
		
					//alles in die Keyfile schreiben
					//	Key
					$freigfile->write_kimb_id( $id , 'add' , 'key' , $key );
					//	Pfad zur Datei
					$freigfile->write_kimb_id( $id , 'add' , 'path' , $file );
					//	und den Dateinamen (sonst würde Datei beim Download immer "ajax.php" heißen)
					$freigfile->write_kimb_id( $id , 'add' , 'name' , $filena );
					//	Typ (Ordner/ Datei)
					$type = ( is_file( __DIR__.'/userdata/user/'.$user.'/'.$file ) ? 'file' : 'folder' );
					$freigfile->write_kimb_id( $id , 'add' , 'type' , $type );
					//	Dateiupload erlaubt
					if( $type == 'folder' ){
						//nur bei Ordnern möglich
						if( $_POST['upload'] == 'yes' ){
							$freigfile->write_kimb_id( $id , 'add' , 'upload' , 'yes' );
						}
						else{
							$freigfile->write_kimb_id( $id , 'add' , 'upload' , 'no' );
						}
					}
					else{
						$freigfile->write_kimb_id( $id , 'add' , 'upload' , 'no' );
					}
					
					$link = htmlspecialchars( $allgsysconf['siteurl'].'/ajax.php?addon=daten&user='.$user.'&key='.$key ); 

					//Ausgabe
					$all_output = array( 'okay' => true, 'link' => $link );
					
				}
				
			}
			
		}
		//Zwischenablage (kopiere/ verschieben)
		elseif( $_POST['todo'] == 'zwischenabl' ){

			//Infos holen
			$infos = $_POST['infos'];

			//Infos testen
			if(
					$infos['art'] != 'rename' && $infos['art'] != 'copy'
				||
					$infos['verz'] != 'user' && $infos['verz'] != 'public' 
				||
					strpos( $infos['url'], '..') !== false
				||
					strpos( $infos['name'], '..') !== false
			){
				$all_output['versch'] = false;
			}
			else{
			
				//Dateisystem Pfad Quelle erstellen
				$von = __DIR__.'/userdata/'.$infos['verz'].'/'.( ($infos['verz'] == 'user' ) ?  $_SESSION['felogin']['user'].'/' : '' ).$infos['url'];

				//Neuen Namen erstellen
				$newna = $infos['name'];
				//	Umlaute und Leerezeichen
				$newna = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '.'), $newna);
				//	Rest weg
				$newna = preg_replace( '/([^A-Za-z0-9\_\.\-])/' , '' , $newna );

				//Dateisystem Ziel erstellen
				$nach = $filepath.'/'.$newna;

				//Quelle vorhanden
				//Zielordner okay?
				if( file_exists( $von ) && is_dir( $filepath ) ){
					//Vorgang durchführen
					if( $infos['art'] == 'copy' ){
						//kopieren
						//	Ordner?
						if( is_dir( $von ) ){
							$all_output['versch'] = copy_r( $von, $nach );
						}
						//sonst Datei
						else{
							$all_output['versch'] = copy( $von, $nach );
						}
					}
					else{
						//verschieben
						$all_output['versch'] = rename( $von, $nach );
					}
				}
				else{
					$all_output['versch'] = false;
				}
			}
		}
		
		
		//JSON Ausgabe
		header('Content-Type: application/json');
		//richtige Daten nach main, unter dev debug Ausgaben möglich
		echo json_encode( array( 'main' => $all_output, 'dev' => $output_dev ) );
		//fertig
		die;
	}
	//Freigabeliste?
	elseif( $_POST['todo'] == 'freigabeliste' || $_POST['todo'] == 'freigabedel'  ){
		
		//Username
		$user = $_SESSION['felogin']['user'];
		
		//Datei für Freigaben öffnen
		$freigfile = new KIMBdbf( 'addon/daten__user_'.$user.'.kimb' );
		
		//Liste ?
		if( $_POST['todo'] == 'freigabeliste' ){
			//alle Freigaben durchgehen
			foreach( $freigfile->read_kimb_all_teilpl( 'allidslist') as $id ){
				//Daten der Freigaben in Liste lesen
				$data = $freigfile->read_kimb_id( $id );
				
				$list[] = array(
					'id' => $id,
					'name' => $data['name'],
					'path' => $data['path'],
					'link' => htmlspecialchars( $allgsysconf['siteurl'].'/ajax.php?addon=daten&user='.$user.'&key='.$data['key'] ),
					'upload' => $data['upload'],
					'type' => $data['type']
				);
			}
			
			//Ausgabe
			$all_output = array( 'okay' => true, 'list' => $list );
		}
		//löschen
		else{
			//ID okay??
			if( !empty( $_POST['id'] ) &&  $_POST['id'] != 0 && is_numeric( $_POST['id'] ) ){
				
				//löschen
				if( $freigfile->write_kimb_id( $_POST['id'], 'del' ) ){
					//okay
					$all_output = array( 'okay' => true );	
				}
				else{
					//Fehler
					$all_output = array( 'okay' => false );
				}
			}
			else{
				//Fehler
				$all_output = array( 'okay' => false );
			}
		}
		
		//JSON Ausgabe
		header('Content-Type: application/json');
		//richtige Daten nach main, unter dev debug Ausgaben möglich
		echo json_encode( array( 'main' => $all_output, 'dev' => $output_dev ) );
		//fertig
		die;
	}
	//Aufruf einer Datei?
	elseif( !empty( $_GET['folder'] ) && !empty( $_GET['path'] )){
		
		//Fehler
		$errormsg = '<html><h1>Fehler</h1>Die Datei wurde nicht gefunden oder Sie haben keine Rechte!</html>';
		
		//Grundverzeichnis
		$d_file = __DIR__.'/userdata';
		
		//korrekte Wahl 
		//	Daten des Users
		//	Daten einer Gruppe
		//	Daten für alle User?
		if( $_GET['folder'] == 'public' || $_GET['folder'] == 'user'  ){
			//Ordner für Wahl öffnen
			$d_file .= '/'.$_GET['folder'];
		}
		else{
			//falsche Wahl
			echo $errormsg;
			die;
		}
		
		//Daten eines Users gewählt?
		if( $_GET['folder'] == 'user' ){
			
			//Username => Userverzeichnis
			$d_file .= '/'.$_SESSION['felogin']['user'];
			
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_GET['path'], '..') === false ){
				//gewünschten Pfad ansetzen
				$d_file .= $_GET['path'];
	
			}
			else{
				//Pfad unsicher!
				echo $errormsg;
				die;
			}

		}
		//Daten aller Users gewählt?
		elseif( $_GET['folder'] == 'public' ){
		
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_GET['path'], '..') === false ){
				//gewünschten Pfad ansetzen
				$d_file .= $_GET['path'];
	
			}
			else{
				//Pfad unsicher!
				echo $errormsg;
				die;
			}
		
		}
		
		//Datei vorhanden
		if( is_file( $d_file ) ){
			//Dateiname
			$filename = basename( $d_file );
			//Größe
			$filesize = filesize( $d_file );
			//MIME
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($finfo, $d_file);
			finfo_close($finfo);
	
			//Header
			header( 'Content-type: '.$mimetype.'; charset=utf-8' );
			header( 'Content-Disposition: inline; filename="'.$filename.'"' );
			header( 'Content-Length: '.$filesize);
			//Ausgabe
			readfile( $d_file );

		}
		else{
			//Pfad fehlerhaft!
			echo $errormsg;
		}
		die;
	}
	//Dateifreigabe?
	elseif( !empty( $_GET['user'] ) && !empty( $_GET['key'] ) ){
		
		//Datei Ausgeben
		show_freig_file();
		
	}
	else{
		echo $errormsg;
		die;
	}

}
//Dateifreigabe?
elseif( !empty( $_GET['user'] ) && !empty( $_GET['key'] ) ){
	
	//Datei Ausgeben
	show_freig_file();
	
}
else{
	echo $errormsg;
	die;
}
	
?>
