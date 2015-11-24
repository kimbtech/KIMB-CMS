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

//Freigegebene Dateien anzeigen
function show_freig_file () {
	
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
			
			//Pfad für Datei erstellen
			$file = __DIR__.'/userdata/user/'.$_GET['user'].$path;
			
			//Datei vorhanden?
			if( is_file( $file ) ){
			
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
				
				$file = $filepath.'/'.$_POST['allgvars']['file'];
				
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
				
				//Pfad zum Ordner
				$file = $filepath.'/'.$_POST['allgvars']['file'];
				
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
			if( strpos( $_FILES['name'], '..' ) === false ){
				
				//Pfad zum Ordner
				$file = $filepath.$_FILES['file']['name'];
				
				//Datei und löschen okay
				if( move_uploaded_file( $_FILES['file']['tmp_name'] , $file ) ){
					$all_output['wr'] = true;					
				}
				else{
					$all_output['wr'] = false;
				}
			}
			
		}
		elseif( $_POST['todo'] = 'newfreigabe' ){
			
			//keine .. im Pfad -  Dateisystemschutz
			if( strpos( $_POST['allgvars']['file'], '..' ) === false ){
				
				//Username (=Grundpfad)	
				$user = $_SESSION['felogin']['user'];
				//Dateipfad (bezogen auf Grundpfad)
				$file = $_POST['allgvars']['path'].$_POST['allgvars']['file'];
				//Dateiname
				$filena = $_POST['allgvars']['file'];
				
				if( is_file( __DIR__.'/userdata/user/'.$user.'/'.$file ) ){
					
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
					
					//Ausgabe
					$all_output = array( 'okay' => true, 'link' => htmlspecialchars( $allgsysconf['siteurl'].'/ajax.php?addon=daten&user='.$user.'&key='.$key ) );
					
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
	elseif( $_POST['todo'] == 'freigabeliste' ){
		
		//Username
		$user = $_SESSION['felogin']['user'];
		
		//Datei für Freigaben öffnen
		$freigfile = new KIMBdbf( 'addon/daten__user_'.$user.'.kimb' );
		
		//alle Freigaben durchgehen
		foreach( $freigfile->read_kimb_all_teilpl( 'allidslist') as $id ){
			//Daten der Freigaben in Liste lesen
			$data = $freigfile->read_kimb_id( $id );
			
			$list[] = array( 'id' => $id, 'name' => $data['name'], 'path' => $data['path'] ,'link' => htmlspecialchars( $allgsysconf['siteurl'].'/ajax.php?addon=daten&user='.$user.'&key='.$data['key'] ));
		}
		
		//Ausgabe
		$all_output = array( 'okay' => true, 'list' => $list );
		
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