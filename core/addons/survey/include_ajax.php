<?php

/*************************************************/
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2016 by KIMB-technologies
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
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//Links für Umfrage erstellen?
if(
	isset( $_GET['todo'] ) && $_GET['todo'] == 'makelinks'
	&&
	isset( $_GET['uid'] ) && is_numeric( $_GET['uid'] ) 
){
	//Login prüfen
	check_backend_login( 'fourteen' , 'more');

	$uid = $_GET['uid'];

	if( check_for_kimb_file( 'addon/survey__'.$uid.'_conf.kimb' ) ){

		$ufile = new KIMBdbf( 'addon/survey__'.$uid.'_conf.kimb' );

		//Anzahl okay?
		if(
			isset( $_GET['anz'] ) && is_numeric( $_GET['anz'] )
		){
			//Anzahl != 0 ?
			if( $_GET['anz'] > 0 && $_GET['anz'] < 500 ){
				//gewünschte Anzahl
				$anz = $_GET['anz'];
				//aktuelle
				$ist = 0;

				//Ausgabe
				$out = array();

				//solange Codes machen, bis alles okay
				while( $ist < $anz ){
					//Code machen
					$randc = makepassw( 30, '', 'numaz' );
					//Code schon vorhanden?
					if( $ufile->read_kimb_search_teilpl( 'links', $randc ) == false ){
						//hinzufügen => okay?
						if( $ufile->write_kimb_teilpl( 'links', $randc, 'add' ) ){
							$ist++;
							$out[] = $allgsysconf['siteurl'].'/ajax.php?addon=survey&todo=link&uid='.$uid.'&code='.$randc;
						}
					}
				}

				//Ausgabe
				header('Content-Type: text\plain; charset=utf-8');
				header('Content-Disposition: inline; filename="links_'.$uid.'.txt"');
				echo implode("\r\n", $out);
			}
			//Anzahl 0 => Links löschen
			elseif( $_GET['anz'] == 0 ){
				//Löschen versuchen
				if( $ufile->write_kimb_teilpl_del_all( 'links' ) ){
					echo "200 - Alle Links wurden gelöscht!";
				}
				else{
					echo "500 - Konnte Links nicht löschen!";
				}
			}
			else{
				echo "400 - Anzahl stimmt nicht!";
			}
		}
		else{
			echo "400 - Anzahl stimmt nicht!";
		}

	}
	else{
		echo "404 - Umfrage nicht gefunden!";
	}
}
//Link für Umfrage aufgerufen?
elseif(
	isset( $_GET['todo'] ) && $_GET['todo'] == 'link'
	&&
	isset( $_GET['uid'] ) && is_numeric( $_GET['uid'] )
){

	$uid = $_GET['uid'];
	$code = $_GET['code'];

	//Umfrage vorhnaden?
	if(  check_for_kimb_file( 'addon/survey__'.$uid.'_conf.kimb' ) ){

		//Datei laden
		$file = new KIMBdbf( 'addon/survey__'.$uid.'_conf.kimb' );

		//Code prüfen
		if( $file->read_kimb_search_teilpl( 'links', $code ) ){
			//freischalten (SESSION)
			$_SESSION['addon_survey'][$uid]['zugriff'] = 'allowed';
			//auch wenn schon teilgenommen, mit zwei Links doppelt erlaubt
			$_SESSION['addon_survey'][$uid]['teilgenommen'] = 'nein';
			//Code löschen
			$file->write_kimb_teilpl( 'links', $code, 'del' );

			//Seiten ID zu RequestID umrechnen
			//	ID Zuordnungen Datei laden
			$idfile = new KIMBdbf('menue/allids.kimb');
			//	rechnen
			$requid = $idfile->search_kimb_xxxid( $uid , 'siteid' );

			if( $requid != false ){
				//Weiterleiten
				open_url( make_path_outof_reqid( $requid ) );
			}
			else{
				echo "404 - Umfrage nicht gefunden!";
			}
		}
		else{
			echo "403 - Code für die Umfrage ungültig!";
		}
	}
	else{
		echo "404 - Umfrage nicht gefunden!";
	}
	die;
}
elseif(
	isset( $_POST['task'] )
	&&
	( $_POST['task'] == 'umfrage' || $_POST['task'] == 'auswertung' )
	&&
	isset( $_POST['uid'] ) && is_numeric( $_POST['uid'] )
){
	$uid = $_POST['uid'];

	//Umfrage vorhanden?
	if( check_for_kimb_file( 'addon/survey__'.$uid.'_conf.kimb' ) ){

		//noch keine Fehler
		$outerr = false;

		//POST Daten von AJAX
		$ajaxpost = $_POST['data'];

		//Umfrage?
		if( $_POST['task'] == 'umfrage' ){
			//Session okay?
			if(
				isset( $_SESSION['addon_survey'][$uid]['zugriff'] )
				&&
				$_SESSION['addon_survey'][$uid]['zugriff'] == 'allowed'
			){
				//do it

				//Datei laden
				$ufile = new KIMBdbf( 'addon/survey__'.$uid.'_conf.kimb' );

				//alle Daten holen?
				if( $ajaxpost['action'] == "pull" ){

					//Fragen laden
					$umf = read_and_sort_fragen( $ufile );

					//Ausgabe
					//	Fragen
					$out['fragen'] = $umf;
					//	Anzahl Fragen
					$out['fragenzahl'] = count( $umf );
					//	nach Namen oder Anonym
					$out['art'] = $ufile->read_kimb_one( 'auswer' );
					//	Infotext
					$out['about'] = $ufile->read_kimb_one( 'inform-parsed' );

				}
				//Ergebnisse übernehmen?
				elseif(
					$ajaxpost['action'] == "push"
					&&
					//Hat User evtl. schon teilgenommen => darf nicht mehr
					$_SESSION['addon_survey'][$uid]['teilgenommen'] != 'ja'
				){
					
					//Ergebnisse
					$erg = $ajaxpost['erg'];
					//Fragen
					$fra = read_and_sort_fragen( $ufile );

					//Ergebnissdatei laden
					$uefile = new KIMBdbf( 'addon/survey__'.$uid.'_erg.kimb' );

					//Werte prüfen
					//	Anzahl
					if( count( $erg ) == count( $fra ) ){

						//jedes Ergebnis in Array als Bool
						$okayliste = array();

						//alle Fragen durchgehen
						foreach( $fra as $id => $frage ){

							//vorhanden?
							if( isset( $erg[$id] ) ){

								//zu prüfendes Ergebnis
								$erghier = $erg[$id];

								//nach Type prüfen
								//	Zahl?
								if( $frage['type'] == 'za' ){
									
									if(
										//nummerisch
										is_numeric( $erghier )
										&&
										//nicht zu groß?
										$erghier <= $frage["felder"][2]
										&&
										//nicht zu klein?
										$erghier >= $frage["felder"][1]

									){
										//ok
										$okayliste[$id] = true;
									}
									else{
										//fehlerhaft!
										$okayliste[$id] = false;
									}
								}
								//	Freitext
								elseif( $frage['type'] == 'ft' ){
									//leer?
									if( !empty( $erghier ) ){
										//kein HTML!
										$erg[$id] = strip_tags( $erg[$id] );
										//ok
										$okayliste[$id] = true;
									}
									else{
										//fehlerhaft!
										$okayliste[$id] = false;
									}
								}
								//	Auswahl
								elseif( $frage['type'] == 'au' ){

									if(
										//Zahl?
										is_numeric( $erghier )
										&&
										//als Feld vorhanden?
										in_array( $erghier, array_keys( $frage["felder"] ) )
									){
										
										//ok
										$okayliste[$id] = true;
									}
									else{
										//fehlerhaft!
										$okayliste[$id] = false;
									}
								}
								//	Multiple Choice
								elseif( $frage['type'] == 'mc' ){
									if(
										//Array
										is_array( $erghier )
									){
										//alle Werte in Array okay?
										foreach( $erghier as $choose){

											//gewähltes Feld vorhnaden?
											if(
												!in_array( $choose, array_keys( $frage["felder"] ) )
											){

												//Fehler
												$okayliste[$id] = false;
												//raus
												break;
										
											}
										}
										//keine Fehler
										if( !isset( $okayliste[$id] ) ){
											//okay
											$okayliste[$id] = true;
										}
									}
									else{
										//fehlerhaft!
										$okayliste[$id] = false;
									}
								}
								//	Abstufung
								elseif( $frage['type'] == 'ab' ){
									if(
										//Array
										is_array( $erghier )
										&&
										count( $frage["felder"] ) == count( $erghier )
									){
										//Werte okay
										$wok = array( 1, 2, 3, 4, 5, 6, 'ka' );

										//alle Werte in Array okay?
										foreach( $erghier as $id => $choose ){

											if(
												//Feld überhaupt vorhanden
												!in_array( $id, array_keys( $frage["felder"] ) )
												||
												//Wert okay?
												!in_array( $choose, $wok )
											){
												//Fehler
												$okayliste[$id] = false;
												//raus
												break;
											}
										}

										//keine Fehler
										if( !isset( $okayliste[$id] ) ){
											//okay
											$okayliste[$id] = true;
										}
									}
									else{
										//fehlerhaft!
										$okayliste[$id] = false;
									}
								}
								else{
									//fehlerhaft!
									$okayliste[$id] = false;
								}

							}
							else{
								//es darf nichts fehlen!
								$okayliste[$id] = false;
							}

						}

						//Prüfung auswerten
						if(
							//Anzahl okay?
							count( $okayliste ) == count( $fra )
							&&
							//und okay?
							//	nie false herausbekommen
							!in_array( false, $okayliste )
						){
							//nach Namen?
							if( $ufile->read_kimb_one( 'auswer' ) == 'na' ){

								//**
								//**
								//**
								//**
								//**
								//**
								//**

							}
							else{
								//alle Ergebnisse laden
								$allerg = $uefile->read_kimb_id_all();

								$out['dsfs'][] = $allerg;

								//noch leer?
								if( $allerg == array() ){
									//los
									$allerg = array();
									//passend bauen
									foreach( $fra as $id => $val ){
										//Freitext
										if( $val['type'] == 'ft' ){
											//einfach nur Array für die Texte
											//und Anzahl
											$allerg[$id] = array(
												'anzahl' => 0,
												'texte' => array()
											);
										}
										//Zahlen
										elseif( $val['type'] == 'za' ){
											//Array mit jedem Wert
											for( $i = $val["felder"][1]; $i <= $val["felder"][2] ; $i++ ){
												$allerg[$id][$i] = 0; 
											}
										}
										elseif( $val['type'] == 'ab' ){
											//nur die IDs extrahieren
											foreach( $val["felder"] as $i => $v ){
												//noch keine Auswahlen
												$allerg[$id][$i] = array(
													1 => 0,
													2 => 0,
													3 => 0,
													4 => 0,
													5 => 0,
													6 => 0,
													'ka'  => 0
												);
											}
										}
										//Multiple Choice und Auswahl
										else{
											//nur die IDs extrahieren
											foreach( $val["felder"] as $i => $v ){
												//noch keine Auswahlen
												$allerg[$id][$i] = 0;
											}
										} 
										
									}
								}

								//Diese Ergebnisse einbauen
								foreach( $allerg as $id => $values ){
									//**
									//**
									//**
									//**
									//**
									//**
									//**
								}

								$out['dsfs'][] = $allerg;

								//schreiben
								$allesokay = $uefile->write_kimb_id_array( $allerg );
							}

							//User nicht nochmal lassen
							if( $allesokay ){
								//Felogin User als teilgenommen in dbf
								//	Add-on vorhanden und aktiviert?
								if( check_addon( 'felogin' ) == array(true, true) ){
									//Login okay?
									if( check_felogin_login( '---session---', '---none---', true ) ){
										//User reinschreiben
										$uefile->write_kimb_teilpl( 'teilnahmeuser', $_SESSION['felogin']['user'], 'add' );
									}
								}

								//User hat jetzt teilgenommen!
								$_SESSION['addon_survey'][$uid]['teilgenommen'] = 'ja';

								//okay 
								$out['pushokay'] = true;
							}
							else{
								//okay 
								$out['pushokay'] = false;
							}
						}
						else{
							//Fehler
							$out['pushokay'] = false;
							$out['ermsg'] = 'Ergebnisse sind nicht korrekt!';
						}

					}
					else{
						//Fehler
						$out['pushokay'] = false;
						$out['ermsg'] = 'Anzahl der Ergebnisse stimmt nicht!';
					}

					
				}
				else{

					$out = "400 - Fehlerhafter Request!";
					$outerr = true;
				}
			}
			else{
				$out = "403 - Nicht erlaubt!";
				$outerr = true;
			}

		}
		//Statistik?
		else{
			//Session okay
			if(
				isset( $_SESSION['addon_survey']['ausw'][$uid]['zugriff'] )
				&&
				$_SESSION['addon_survey']['ausw'][$uid]['zugriff'] == 'allowed'
			){
				//do it
				$out = "Auswertung machen";
			}
			else{
				$out = "403 - Nicht erlaubt!";
				$outerr = true;
			}
		}

		//Ausgabe vorbereiten
		$output = array(
			'okay' => true,
			'data' => $out,
			'error' => $outerr
		);
		//Ausgabe machen
		header('Content-Type: application\json; charset=utf-8');
		echo json_encode( $output, JSON_PRETTY_PRINT );
		die;
	}
	else{
		echo "404 - Umfrage nicht gefunden!";
	}

}
else{
	echo "400 - Fehlerhafter Request!";
}
die;
	
?>
