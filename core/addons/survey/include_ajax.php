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
				$out = "Umfrage machen";
				$out .= nl2br( print_r( $ajaxpost, true ) );
			}
			else{
				$out = "403 - Nicht erlaubt!";
				$outerr = false;
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
				$outerr = false;
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
