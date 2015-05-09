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

check_backend_login( 'nineteen' , 'more');
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
if( !is_object( $levellist ) ){
	$levellist = new KIMBdbf( 'backend/users/level.kimb' );
}

if( $_GET['todo'] == 'new' ){
	$sitecontent->add_site_content('<h2>Neues Userlevel Backend erstellen</h2>');

	if( isset( $_POST['name'] ) ){
		$_POST['name'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_POST['name'] ) );
		if( $_POST['name'] != '' && $_POST['name'] != 'more' && $_POST['name'] != 'less' && $_POST['name'] != 'all' && $_POST['name'] != 'levellist' ){
			if( $levellist->read_kimb_one( $_POST['name'] ) == '' ){
				$levellist->write_kimb_new( $_POST['name'] , 'one,two,three' );
				$alllev = $levellist->read_kimb_one( 'levellist' );
				if( $alllev == '' ){
					$levellist->write_kimb_new( 'levellist' , $_POST['name'] );
				}
				else{
					$levellist->write_kimb_replace( 'levellist' , $alllev.','.$_POST['name'] );
				}

				open_url( '/kimb-cms-backend/other_level.php?todo=edit&level='.$_POST['name']  );
				die;
			}
			else{
				$sitecontent->echo_error( 'Der Levelname ist schon vergeben!' , 'unknown');
			}
		}
		else{
			$sitecontent->echo_error( 'Ihre Eingabe war leer oder ein verbotener Wert ( more, less, all, levellist )!' , 'unknown');
		}
	}

	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=new" method="post" >');
	$sitecontent->add_site_content('<input type="text" name="name"> <i title="Pflichtfeld , a-z">( Levelname * )</i><br />');
	$sitecontent->add_site_content('<input type="submit" value="Erstellen" >');
	$sitecontent->add_site_content('</form>');


}
elseif( $_GET['todo'] == 'edit' && isset( $_GET['level'] ) ){
	$sitecontent->add_site_content('<h2>Userlevel Backend bearbeiten</h2>');

	$_GET['level'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_GET['level'] ) );
	$alllev = $levellist->read_kimb_one( 'levellist' );
	$alllev = explode( ',' , $alllev );

	if( in_array( $_GET['level'] , $alllev ) ){

		$sitecontent->add_html_header('<script>
		function set_on( val ){
			$( "input[value=" + val + "]" ).prop( "checked" , true);
		}
		</script>');

		if( is_array( $_POST['numbers'] ) ){

			foreach( $_POST['numbers'] as $num ){
				$ges .= ','.$num;
			}
			$ges = substr( $ges , '1' );

			$levellist->write_kimb_replace( $_GET['level'] , $ges );

			$sitecontent->echo_message( 'Das Level wurde angepasst!' );
			$sitecontent->add_site_content('<br />');
		}

		$numbers = $levellist->read_kimb_one( $_GET['level'] );
		$numbers = explode( ',' , $numbers );

		foreach( $numbers as $number ){
			$checks[$number] = ' checked="checked" ';
		}

		$numbers = $levellist->read_kimb_one( 'all' );
		$numbers = explode( ',' , $numbers );

		foreach( $numbers as $number ){
			if( !isset( $checks[$number] ) ){
				$checks[$number] = ' ';
			}
		}
		
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=edit&amp;level='.$_GET['level'].'" method="post" >');
		$sitecontent->add_site_content('<input type="text" name="name" readonly="readonly" value="'.$_GET['level'].'"> <i title="Nicht zu ändern.">( Levelname * )</i><br />');
		$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Klicken Sie alle Menuepunkte an, auf die ein User der Gruppe Zugriff haben soll! ( Es dürfen nicht alle Felder deaktiviert sein! )"></span><br />');

		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="one"'.$checks['one'].'> Seiten ( one )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'one\' );" value="two"'.$checks['two'].'> Neue Seite ( two )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'one\' );" value="three"'.$checks['three'].'> Seite bearbeiten ( three )<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="four"'.$checks['four'].'> Menue ( four )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'four\' );" value="five"'.$checks['five'].'> Neues Menue ( five )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'four\' );" value="six"'.$checks['six'].'> Menue Zuordnen ( six )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'four\' );" value="seven"'.$checks['seven'].'> Menue bearbeiten ( seven )<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="eight"'.$checks['eight'].'> User ( eight ) <b title="Dies muss aktiviert sein, wenn ein User die Möglichkeit haben soll, sich selbst zu verändern! (z.B Passwort )">*</b><br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'eight\' );" value="nine"'.$checks['nine'].'> Neuer User ( nine )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'eight\' );" value="ten"'.$checks['ten'].'> User bearbeiten ( ten )<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="eleven"'.$checks['eleven'].'> Konfiguration ( eleven )<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="twelve"'.$checks['twelve'].'> Add-ons ( twelve )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'twelve\' );" value="thirteen"'.$checks['thirteen'].'> Normale-Add-on-Einstellungen ( thirteen )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'twelve\' );" value="fourteen"'.$checks['fourteen'].'> Admin-Add-on-Einstellungen ( fourteen )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'twelve\' );" value="fiveteen"'.$checks['fiveteen'].'> Add-on installieren ( fiveteen )<br />');
		$sitecontent->add_site_content('<input type="checkbox" name="numbers[]" value="sixteen"'.$checks['sixteen'].'> Other ( sixteen )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="seventeen"'.$checks['seventeen'].'> Filemanager ( seventeen )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="eightteen"'.$checks['eightteen'].'> Themes ( eightteen )<br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="nineteen"'.$checks['nineteen'].'> Userlevel ( nineteen ) <br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="twenty"'.$checks['twenty'].'> Umzug ( twenty ) <br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="twentyone"'.$checks['twentyone'].'> Mehrsprachige Seite ( twentyone ) <br />');
		$sitecontent->add_site_content('==><input type="checkbox" name="numbers[]" onclick="set_on( \'sixteen\' );" value="twentytwo"'.$checks['twentytwo'].'> Easy Menue ( twentytwo ) <br />');

		$sitecontent->add_site_content('<input type="submit" value="Ändern" >');
		$sitecontent->add_site_content('</form>');
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}

}
elseif( $_GET['todo'] == 'del' && isset( $_GET['level'] ) ){
	
	$_GET['level'] = preg_replace( "/[^a-z]/" , "" , strtolower( $_GET['level'] ) );
	$alllev = $levellist->read_kimb_one( 'levellist' );
	$alllev = explode( ',' , $alllev );

	if( in_array( $_GET['level'] , $alllev ) ){
		
		$levellist->write_kimb_delete( $_GET['level'] );

		foreach( $alllev as $lev ){
			if( $lev != $_GET['level'] ){
				$newlev .= ','.$lev;
			}
		}
		$newlev = substr( $newlev , '1' );

		if( $newlev != '' ){
			$levellist->write_kimb_replace( 'levellist' , $newlev );
		}
		else{
			$levellist->write_kimb_delete( 'levellist' );
		}

		open_url( '/kimb-cms-backend/other_level.php' );
		die;
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
else{
	$sitecontent->add_site_content('<h2>Userlevel Backend Liste</h2>');

	$sitecontent->add_html_header('<script>
	var del = function( level ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:180,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=del&level=" + level;
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

	$levs = $levellist->read_kimb_one( 'levellist' );
	$levs = explode( ',' , $levs );

	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=new"><span class="ui-icon ui-icon-plusthick" title="Eine neues Level erstellen." style="display:inline-block;" ></span></a>');
	$sitecontent->add_site_content('<table width="100%"><tr> <th>Levelname</th> <th>Rechte <span class="ui-icon ui-icon-info" style="display:inline-block;" title="Die englischen Zahlen stehen für die erlaubten Menuepunkte!"></span></th> <th>Löschen</th> </tr>');
	$sitecontent->add_site_content('<tr> <td title="Voreingestellt, nicht zu verändern" >more</td> <td><i>Alle Rechte.</i></td> <td></td> </tr>');
	$sitecontent->add_site_content('<tr> <td title="Voreingestellt, nicht zu verändern" >less</td> <td><i>Rechte die ein Editor benötigt.</i></td> <td></td> </tr>');

	foreach( $levs as $lev ){
		$read = $levellist->read_kimb_one( $lev );
		if( $read != '' ){
			$del = '<span onclick="var delet = del( \''.$lev.'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Level löschen." style="display:inline-block;" ></span></span>';
			$sitecontent->add_site_content('<tr> <td><a title="Ändern" href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php?todo=edit&amp;level='.$lev.'">'.$lev.'</a></td> <td>'.substr( $read , '0' , '50' ).' ( ... )</td> <td>'.$del.'</td> </tr>');
		}
	}
	$sitecontent->add_site_content('</table>');

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Möchten Sie das Userlevel wirklich löschen?</p></div></div>');
}

$sitecontent->add_site_content('<br /><br /><span class="ui-icon ui-icon-info" title="Die User können Sie unter &apos;User&apos; -> &apos;Auflisten&apos; -> &apos;Name ( User bearbeiten )&apos; den Gruppen zuordnen! Das Zuordnen ist nur für User der Gruppe Admin ( &apos;more&apos; ) möglich!"></span><br />');
//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
