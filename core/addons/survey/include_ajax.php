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
elseif( isset( $_GET['todo'] ) && $_GET['todo'] == 'link' ){

	echo 'Umfragelink FWD - '.$_GET['uid'].' - '.$_GET['code'];

	//Code prüfen

	//freischalten (SESSION)

	//Weiterleiten
}
else{
	echo "400 - Fehlerhafter Request!";
}
die;
	
?>
