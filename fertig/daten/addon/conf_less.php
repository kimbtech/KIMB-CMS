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

//Konfiguration laden und Handhabung vereinfachen
$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&amp;addon=daten';

if( empty( $_GET['user'] ) ){

	//Freigaben Zugriffe
	$sitecontent->add_site_content('<h2>Zugriffe auf Freigaben</h2>');

	//Liste User
	$sitecontent->add_site_content('<ul>');

	//alle User durchgehen
	foreach( scandir( __DIR__.'/userdata/user/' ) as $file ){
		if( $file != '.' && $file != '..' ){
			$sitecontent->add_site_content('<li><a href="'.$addonurl.'&amp;user='.$file.'">'.$file.'</a></li>');			
		}
	}

	$sitecontent->add_site_content('</ul>');
	$sitecontent->add_site_content('<small>Klicken Sie auf einen User um die Zugriffe auf seine Freigabe zu sehen!</small>');
}
else{
	//Usernamen säubern
	$user = preg_replace( "/[^a-z0-9]/" , "" , strtolower( $_GET['user'] ) );

	//Freigaben Zugriffe
	$sitecontent->add_site_content('<h2>Zugriffe auf Freigaben des Users "'.$user.'"</h2>');

	//Navigation
	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Zurück</a><br /><br />');
	$addonurl .= '&amp;user='.$user;

	//User überhaupt vorhhanden?
	if( is_dir( __DIR__.'/userdata/user/'.$user ) ){

		//Freigabedatei des Users öffnen
		$freigfile = new KIMBdbf( 'addon/daten__user_'.$user.'.kimb' );

		//alles lesen
		$array = $freigfile->read_kimb_id_all();

		//Freigaben vorhanden?
		if( count( $array ) > 0 ){

			//Tabelle
			//	CSS
			$sitecontent->add_html_header('<style>table, tr, td, th { border: 1px solid black; border-collapse:collapse; padding:2px;} th{ text-align:center; } th.fest, td.fest{ text-align:center; width:100px; }</style>');
			//	HTML
			$sitecontent->add_site_content('<table width="100%">');
			$sitecontent->add_site_content('<tr>');
			$sitecontent->add_site_content('<th colspan="2"></th>');
			$sitecontent->add_site_content('<th class="fest">Anzahl</th>');
			$sitecontent->add_site_content('<th class="fest">Genaue Übersicht</th>');
			$sitecontent->add_site_content('</tr>');
			$sitecontent->add_site_content('<tr>');
			$sitecontent->add_site_content('<th class="fest">Typ</th>');
			$sitecontent->add_site_content('<th>Name</th>');
			$sitecontent->add_site_content('<th colspan="2">der Aufrufe</th>');
			$sitecontent->add_site_content('</tr>');

			//für view more
			$more = array();

			//Array durchgehen
			foreach ($array as $id => $infos) {
				$sitecontent->add_site_content('<tr>');
				$sitecontent->add_site_content('<td class="fest"><span class="ui-icon ui-icon-'.( ( $infos['type'] == 'folder' ) ? 'folder-collapsed' : ( ( substr( $infos['name'], -11 ) == '.kimb_table' )  ? 'calculator' : 'document' ) ).'"></span></td>');
				$sitecontent->add_site_content('<td title="'.$user.':/'.$infos['path'].'">'.$infos['name'].'</td>');
				$sitecontent->add_site_content('<td class="fest">'.count( $infos['track'] ).'</td>');
				$sitecontent->add_site_content('<td class="fest"><button onclick="view_tracked( '.$id.' );"><span class="ui-icon ui-icon-plusthick"></span></button></td>');
				$sitecontent->add_site_content('</tr>');

				$more[$id] = $infos['track'];
			}
			$sitecontent->add_site_content('</table>');

			//JS Funktion view_tracked
			$sitecontent->add_html_header('<script>
				var alltrackeddata = '.json_encode( $more ).';
				function view_tracked( id ){

					var seedat = alltrackeddata[id];
					var html = "<table width=\'100%\'>";

					html += "<tr>";
					html += "<th>Zeitpunkt</th>";
					html += "<th>IP</th>";
					html += "<th>Host</th>";
					html += "<th>Useragent</th>";
					html += "<th>Refer</th>";
					html += "</tr>";

					seedat.forEach( function (v,k){

						var d = new Date( v.time * 1000 );
						var date = d.getDate()+".";
						date += ( d.getMonth() + 1 )+".";
						date += d.getFullYear()+" ";
						date += d.getHours()+":";
						date += d.getMinutes()+":";
						date += d.getSeconds();

						html += "<tr>";
						html += "<td>"+date+"</td>";
						html += "<td>"+v.ip+"</td>";
						html += "<td>"+v.host+"</td>";
						html += "<td>"+v.ua+"</td>";
						html += "<td>"+v.ref+"</td>";
						html += "</tr>";	
					});
					html += "</table>";

					$( "body" ).append( \'<div id="dialogtrack">\'+html+\'</div>\' );
					$( "div#dialogtrack" ).dialog({
						modal: true,
						minWidth: 700,
						resizable: false,
						minHeight: 200,
						maxHeight: $( window ).innerHeight(),
						position: { my: "left top", at: "left top", of: "div#content" },
						title: "Genaue Übersicht der Aufrufe",
						buttons: {
							"Schließen": function (){
								$( this ).dialog( "close" );
							}
						},
						beforeClose: function () {
							$( this ).remove();
						}
					});
				}
			</script>');
		}
		else{
			$sitecontent->echo_error('User hat keine Freigaben erstellt.');	
		}
	}
	else{
		$sitecontent->echo_error('User hat keine Dateien im Cloudspeicher oder existiert nicht.');
	}
}
?>