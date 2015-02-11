<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS
//KIMB ContentManagementSystem
//KIMB-technologies.blogspot.com
/*************************************************/
//CC BY-ND 4.0
//http://creativecommons.org/licenses/by-nd/4.0/
//http://creativecommons.org/licenses/by-nd/4.0/legalcode
/*************************************************/
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
//BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
//IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
//IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
/*************************************************/



define("KIMB_Backend", "Clean Request");

//Objekte Initialisieren
require_once(__DIR__.'/../core/oop/all_oop_backend.php');
//Konfiguration laden
require_once(__DIR__.'/../core/conf/conf_backend.php');

//Menues erstellen und zuordnen

$idfile = new KIMBdbf('menue/allids.kimb');
$menuenames = new KIMBdbf('menue/menue_names.kimb');
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if( $_GET['todo'] == 'new' ){
	check_backend_login('five' , 'more');

	$sitecontent->add_site_content('<h2>Ein neues Menue erstellen</h2>');

	if( ( is_numeric( $_GET['file'] ) || $_GET['file'] == 'first' )  && ( $_GET['niveau'] == 'same' || $_GET['niveau'] == 'deeper' ) && ( is_numeric( $_GET['requid'] ) || !isset( $_GET['requid'] ) || $_GET['requid'] == '' ) ){

		if( isset( $_POST['name'] ) ){
			//nötige vars
			$newm['name'] = $_POST['name'];
			if( $_POST['pfad'] == '' ){
				$newm['pfad'] = preg_replace("/[^0-9A-Za-z_-]/","", $_POST['name']);
			}
			else{
				$newm['pfad'] = preg_replace("/[^0-9A-Za-z_-]/","", $_POST['pfad']);
			}
			$newm['nextid'] = '---empty---';
			$newm['requestid'] = $idfile->next_kimb_id();
			if( isset( $_POST['siteid'] ) && is_numeric( $_POST['siteid'] ) ){
				$newm['siteid'] = $_POST['siteid'];
			}
			else{
				$newm['siteid'] = '---empty---';
			}
			$newm['menueid'] = $newm['requestid'].mt_rand();
			$newm['status'] = 'on';

			if( $_GET['file'] == 'first' ){
				$file = new KIMBdbf( 'url/first.kimb' );
			}
			else{
				$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
			}

			if( $_GET['niveau'] == 'deeper' && is_numeric( $_GET['requid'] ) ){
				$i = 1;
				while( 5 == 5 ){
					if( !check_for_kimb_file( 'url/nextid_'.$i.'.kimb' ) ){
						break;
					}
					$i++;
				}

				//neue Datei und nextid eintragen				
				$oldid = $file->search_kimb_xxxid( $_GET['requid'] , 'requestid');
				if( $oldid == false ){
					$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
					$sitecontent->output_complete_site();
					die;
				}
				$file->write_kimb_id( $oldid , 'add' , 'nextid' , $i );

				$file = new KIMBdbf( 'url/nextid_'.$i.'.kimb' );
				
				$_GET['file'] = $i;

			}
			elseif( $_GET['niveau'] == 'deeper' ) {
				$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
				$sitecontent->output_complete_site();
				die;
			}
			$id = $file->next_kimb_id();

			$pfad = $newm['pfad'];
			$i = 1;
			while( $file->search_kimb_xxxid( $pfad , 'path') != false){
				$pfad = $newm['pfad'].$i;
				$i++;
			}
			$newm['pfad'] = $pfad;

			//url file schreiben
			$file->write_kimb_id( $id , 'add' , 'path' , $newm['pfad'] );
			$file->write_kimb_id( $id , 'add' , 'nextid' , $newm['nextid'] );
			$file->write_kimb_id( $id , 'add' , 'requestid' , $newm['requestid'] );
			$file->write_kimb_id( $id , 'add' , 'status' , $newm['status'] );
			//idfile schreiben
			$idfile->write_kimb_id( $newm['requestid'] , 'add' , 'siteid' , $newm['siteid'] );
			$idfile->write_kimb_id( $newm['requestid'] , 'add' , 'menueid' , $newm['menueid'] );
			//menuename schreiben
			$menuenames->write_kimb_new( $newm['requestid'] , $newm['name'] );

			open_url('/kimb-cms-backend/menue.php?todo=edit&file='.$_GET['file'].'&reqid='.$newm['requestid']);
			die;
		}

		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$_GET['file'].'&amp;niveau='.$_GET['niveau'].'&amp;requid='.$_GET['requid'].'" method="post">');
		$sitecontent->add_site_content('<input type="text" name="name" > <i title="Pflichtfeld">(Menuename *)</i><br />');
		$sitecontent->add_site_content('<input type="text" name="pfad" > <i title="Manuell oder automatisch aus Menuename">(Pfad)</i><br />');
		$sitecontent->add_site_content('<input type="text" name="siteid" > <i title="Auch später über Zuordnung zu definieren">(SiteID)</i><br />');
		$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
		$sitecontent->add_site_content('</form>');

	}
	else{
		$sitecontent->echo_message( 'Bitte wählen Sie zuerste eine Stelle über Menue -> Anpassen! <br />( Rechts in der Spalte Neu )<br /><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list"><button>Los geht&apos;s!</button></a>' );
		$sitecontent->add_site_content('<br /><br />');
		$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&file=first&niveau=same"><button>ODER direkt neues Menue auf Grundebene erstellen!</button></a>');
	}
}
elseif( $_GET['todo'] == 'connect' ){
	check_backend_login( 'six' );

	if( $_POST['post'] == 'post' ){
		$i = 0;
		while( 5 == 5 ){
			if( $_POST[$i.'-site'] != $idfile->read_kimb_id( $_POST[$i] , 'siteid' ) ){
				if( $idfile->write_kimb_id( $_POST[$i] , 'add' , 'siteid' , $_POST[$i.'-site'] ) ){
					$sitecontent->echo_message( 'Die Seite '.$_POST[$i.'-site'].' wurde einem Menue zugeordnet!<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$_POST[$i].'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Die Seite mit Menue aufrufen."></span></a>' );
				}
			}
			if( $_POST[$i] == '' ){
				break;
			}
			$i++;
		}
	}
	
	$sites = scan_kimb_dir('site/');
	foreach ( $sites as $site ){
		$sitef = new KIMBdbf('site/'.$site);
		$id = preg_replace("/[^0-9]/","", $site);
		$title = $sitef->read_kimb_one('title');

		$allsites[] = array( 'site' => $title, 'id' => $id );
	}

	$sitecontent->add_site_content('<h2>Ein Menue einer Seite zuordnen</h2>');

	make_menue_array();
	$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect">');
	$sitecontent->add_site_content('<table width="100%"><tr> <th width="50px;"></th> <th>MenueName</th> <th>Status</th> <th>SiteID <span class="ui-icon ui-icon-info" title="Wählen Sie bitte für jeden Menuepunkt eine Seite!"></span></th> </tr>');
	$i = 0;
	foreach( $menuearray as $menuear ){

		$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );

		if ( $menuear['status'] == 'off' ){
			$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		else{
			$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}

		$sitedr = '<select name="'.$i.'-site"><option value="none" selected="selected">None</option>';
		foreach( $allsites as $alls ){
			if( $alls['id'] == $menuear['siteid'] ){
				$sitedr .= '<option value="'.$alls['id'].'" selected="selected">'.$alls['site'].' - '.$alls['id'].'</option>';
			}
			else{
				$sitedr .= '<option value="'.$alls['id'].'">'.$alls['site'].' - '.$alls['id'].'</option>';
			}
		}
		$sitedr .= '</select>';
		
		$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td>  <td>'.$menuear['menuname'].'</td> <td>'.$status.'</td> <td>'.$sitedr.'<input type="hidden" value="'.$menuear['requid'].'" name="'.$i.'"></td> </tr>');
		$i++;

		$liste = 'yes';
	}
	$sitecontent->add_site_content('</table>');

	if( $liste != 'yes' ){
		$sitecontent->echo_error( 'Es wurden keine Menues gefunden!' );
	}

	$sitecontent->add_site_content('<input type="hidden" value="post" name="post"><input type="submit" value="Zuordnungen ändern"></form>');

}
elseif( $_GET['todo'] == 'list' ){
	check_backend_login('seven' , 'more');

	$sitecontent->add_site_content('<h2>Alle Menues auflisten</h2>');

	$sitecontent->add_html_header('<script>
	var del = function( fileid , requid , fileidbefore) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=del&file=" + fileid + "&reqid=" + requid + "&fileidbefore=" + fileidbefore;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	function delimp() {
		$( "#del-untermenue" ).show( "fast" );
		$( "#del-untermenue" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"OK": function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	var updown = function( fileid , updo , requid ){
		$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=menue.php&fileid=" + fileid + "&updo=" + updo + "&requid=" + requid , function( data ) {
			if( data == "ok" ){
				location.reload();
			}
			else{
				$( "#updown" ).show( "fast" );
				$( "#updown" ).dialog({
				resizable: false,
				height:270,
				modal: true,
				buttons: {
					"OK": function() {
						$( this ).dialog( "close" );
						return false;
					}
				}
				});
			}
		});
	}
	</script>');

	make_menue_array();
	$sitecontent->add_site_content('<table width="100%"><tr> <th title="Jedes Menü hat eine Tiefe, ein Niveau. ( ein ==> ist eine Tiefe tiefer ) ">Niveau</th> <th></th> <th title="Dieser Name wird Besuchern im Frontend angezeigt">MenueName</th> <th title="Pfad-Teil des Menues für URL-Rewriting">Pfad</th> <th title="ID für Aufruf /index.php?id=XXX">RequestID</th> <th>Status</th> <th title="ID der zugeordnenten Seite">SiteID</th> <th title="ID des Menüs ( Systemintern )">MenueID</th> <th>Löschen</th> <th>Neu</th> </tr>');
	foreach( $menuearray as $menuear ){

		$menuear['niveau'] = str_repeat( '==>' , $menuear['niveau'] );
		if ( $menuear['status'] == 'off' ){
			$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-close" title="Dieses Menue ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		else{
			$menuear['status'] = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=deakch&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'"><span class="ui-icon ui-icon-check" title="Dieses Menue ist zu Zeit aktiviert, also sichtbar. ( click -> ändern ) ((Eine Änderung wirkt sich nicht auf Untermenüs aus!))"></span></a>';
		}
		$requid = $menuear['requid'].'<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$menuear['requid'].'" target="_blank"><span class="ui-icon ui-icon-newwin" title="Diese Seite aufrufen."></span></a>';
		$menuename = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&amp;file='.$menuear['fileid'].'&amp;reqid='.$menuear['requid'].'" title="Dieses Menue bearbeiten." >'.$menuear['menuname'].'</a>';

		if( $menuear['nextid'] == ''){	
			$del = '<span onclick="var delet = del( \''.$menuear['fileid'].'\' , '.$menuear['requid'].' , \''.$menuear['fileidbefore'].'\' ); delet();"><span class="ui-icon ui-icon-trash" title="Dieses Menue löschen."></span></span>';
		}
		else{
			$del = '<span onclick="delimp();"><span class="ui-icon ui-icon-trash" title="Dieses Menue löschen."></span></span>';
		}
		$newmenue = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$menuear['fileid'].'&amp;niveau=same" ><span class="ui-icon ui-icon-plusthick" title="Auf diesem Niveau ein weiteres Menue erstellen."></span></a>';
		if( $menuear['nextid'] == ''){
			$newmenue .= '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new&amp;file='.$menuear['fileid'].'&amp;niveau=deeper&amp;requid='.$menuear['requid'].'"><span class="ui-icon ui-icon-arrow-1-se" title="Unter diesem Menue ein Untermenue erstellen."></span></a>';
		}

		$versch = '<span onclick="var updo = updown( \''.$menuear['fileid'].'\' , \'up\' , '.$menuear['requid'].' ); updo();"><span class="ui-icon ui-icon-arrowthick-1-n" title="Dieses Menue nach oben schieben."></span></span>';
		$versch .= '<span onclick="var updo = updown( \''.$menuear['fileid'].'\' , \'down\' , '.$menuear['requid'].' ); updo();"><span class="ui-icon ui-icon-arrowthick-1-s" title="Dieses Menue nach unten schieben."></span></span>';

		$sitecontent->add_site_content('<tr> <td>'.$menuear['niveau'].'</td> <td>'.$versch.'</td> <td>'.$menuename.'</td> <td>'.$menuear['path'].'</td> <td>'.$requid.'</td> <td>'.$menuear['status'].'</td> <td>'.$menuear['siteid'].'</td> <td>'.$menuear['menueid'].'</td> <td>'.$del.'</td> <td>'.$newmenue.'</td> </tr>');

		$liste = 'yes';
	}
	$sitecontent->add_site_content('</table>');

	if( $liste != 'yes' ){
		$sitecontent->echo_error( 'Es wurden keine Menues gefunden!' );
	}

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie das Menue wirklich löschen?</p></div></div>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-untermenue" title="Löschen nicht möglich!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Das Menue kann erst gelöscht werden, wenn es keine Untermenues mehr hat!</p></div></div>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="updown" title="Fehler beim Verschieben!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 100px 0;"></span>Achtung, Menues können nur innerhalb ihres Niveaus verschoben werden!<br /><br />Auch ein Verschieben auf einen höheren Platz als den Ersten oder einen tieferen als den Letzten ist nicht möglich!</p></div></div>');
}
elseif( $_GET['todo'] == 'edit' ){
	check_backend_login('seven' , 'more');

	$sitecontent->add_site_content('<h2>Ein Menue bearbeiten</h2>');

	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) ){
		if( $_GET['file'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
		}
		$id = $file->search_kimb_xxxid( $_GET['reqid'] , 'requestid');
		if( $id  != false ){

			if( isset( $_POST['name'] ) && isset( $_POST['pfad'] ) ){
				$_POST['pfad'] = preg_replace("/[^0-9A-Za-z_-]/","", $_POST['pfad']);
				$ok = $file->search_kimb_xxxid( $_POST['pfad'] , 'path');
				if( $ok == false || $ok == $id ){
					$file->write_kimb_id( $id , 'add' , 'path' , $_POST['pfad'] );
					$sitecontent->echo_message( 'Der Pfad wurde angepasst!' );
				}
				if( $_POST['name'] != '' ){
					$menuenames->write_kimb_replace( $_GET['reqid'] , $_POST['name'] );
					$sitecontent->echo_message( 'Der Name wurde angepasst!' );
				}
			}

			$sitecontent->add_html_header('<script>
			$(function() {
				$("i#pfadtext").text("(Menuepfad -- OK)");
				$("i#pfadtext").css( "background-color", "green" );
				$("i#pfadtext").css( "color", "white" );
				$("i#pfadtext").css( "padding", "5px" );
			});
			function checkpath(){
				var pathinput = $( "input#pfad" ).val();
				if( "'.$file->read_kimb_id( $id , 'path').'" != pathinput ){

					$( "input#check" ).val( "nok" );
					$("i#pfadtext").text("(Menuepfad -- Überprüfung läuft)");
					$("i#pfadtext").css( "background-color", "orange" );

					$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=menue.php&urlfile='.$_GET['file'].'&search=" + pathinput , function( data ) {
						$( "input#check" ).val( data );
						if( data == "nok" ){
							$("i#pfadtext").text("(Menuepfad -- Achtung dieser Pfad ist schon vergeben!!)");
							$("i#pfadtext").css( "background-color", "red" );
						}
						else{
							$( "input#check" ).val( "ok" );
							$("i#pfadtext").text("(Menuepfad -- OK)");
							$("i#pfadtext").css( "background-color", "green" );
						}
					});
				}
				else{
					$( "input#check" ).val( "ok" );
					$("i#pfadtext").text("(Menuepfad -- OK)");
					$("i#pfadtext").css( "background-color", "green" );
				}
			}
			</script>');

			$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=edit&amp;file='.$_GET['file'].'&amp;reqid='.$_GET['reqid'].'" method="post" onsubmit="if( document.getElementById(\'check\').value == \'nok\' ){ return false; } ">');
			$sitecontent->add_site_content('<input type="text" value="'.$menuenames->read_kimb_one( $_GET['reqid'] ).'" name="name" > <i title="Name des Menues der im Frontend angezeigt wird." >(Menuename)</i><br />');
			$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'path').'" name="pfad" id="pfad" onchange="checkpath();"> <i id="pfadtext" title="Ein Menuepfad besteht aus Buchstaben, Zahlen, &apos;_&apos; und &apos;-&apos;.">(Menuepfad)</i><br />');
			$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'status').'" name="status" readonly="readonly"> <i title="Veränderbar auf Seite Auflisten sowie Zuordnung." >(Status)</i><br />');
			$sitecontent->add_site_content('<input type="text" value="'.$file->read_kimb_id( $id , 'requestid').'" name="requid" readonly="readonly"> <i title="Automatisch bei Erstellung des Menue generiert.">(RequestID)</i><br />');
			$sitecontent->add_site_content('<input type="text" value="'.$idfile->read_kimb_id( $_GET['reqid'] , 'siteid' ).'" name="siteid" readonly="readonly"> <i title="Veränderbar auf Seite Zuordnen." >(SiteID)</i><br />');
			$sitecontent->add_site_content('<input type="hidden" value="ok" id="check">');
			$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
			$sitecontent->add_site_content('</form>');

		}
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
elseif( $_GET['todo'] == 'del' ){
	check_backend_login('seven' , 'more');

	$sitecontent->add_site_content('<h2>Ein Menue löschen</h2>');

	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) && ( $_GET['fileidbefore'] == 'first' || is_numeric( $_GET['fileidbefore'] ) || $_GET['fileidbefore'] == '' ) ){
		if( $_GET['file'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
		}
		$id = $file->search_kimb_xxxid( $_GET['reqid'] , 'requestid');
		$nextid = $file->read_kimb_id( $id , 'nextid');
		if( $id  != false && $nextid == ''){
			$wid = 1;
			while( 5 == 5 ){
				if( $wid != $id ){
					$wpath = $file->read_kimb_id( $wid , 'path' );
					$wnextid = $file->read_kimb_id( $wid , 'nextid' );
					$wrequid = $file->read_kimb_id( $wid , 'requestid' );
					$wstatus = $file->read_kimb_id( $wid , 'status');
				}
				if( $wpath == '' && $wid != $id ){
					break;
				}
				if( $wid != $id ){
					if( $wnextid == '' ){
						$wnextid = '---empty---';
					}
					$newmenuefile[] = array( 'path' => $wpath, 'nextid' => $wnextid , 'requid' => $wrequid, 'status' => $wstatus );
				}
				$wid++;
			}
			$inhalt = 'none';
			$i = 1;
			$file->delete_kimb_file();
			foreach( $newmenuefile as $newmenue ){
				$file->write_kimb_id( $i , 'add' , 'path' , $newmenue['path'] );
				$file->write_kimb_id( $i , 'add' , 'nextid' , $newmenue['nextid'] );
				$file->write_kimb_id( $i , 'add' , 'requestid' , $newmenue['requid'] );
				$file->write_kimb_id( $i , 'add' , 'status' , $newmenue['status'] );
				$inhalt = 'something';
				$i++;
			}
			if( $inhalt == 'none' && $_GET['file'] != 'first'){
				if( $_GET['fileidbefore'] == 'first' ){
					$filebef = new KIMBdbf( 'url/first.kimb' );
				}
				else{
					$filebef = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
				}
				$befid = $filebef->search_kimb_xxxid( $_GET['file'] , 'nextid');
				if( $befid  != false ){
					$filebef->write_kimb_id( $befid , 'add' , 'nextid' , '---empty---' );
				}				
			}
			$menuenames->write_kimb_id( $_GET['reqid'] , 'del' );
			$idfile->write_kimb_id( $_GET['reqid'] , 'del' );

			open_url('/kimb-cms-backend/menue.php?todo=list');
			die;
		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
		}
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
elseif( $_GET['todo'] == 'deakch' ){
	check_backend_login( 'four' );

	$sitecontent->add_site_content('<h2>Einen Menuestatus verändern</h2>');

	if( ( $_GET['file'] == 'first' || is_numeric( $_GET['file'] ) ) && is_numeric( $_GET['reqid'] ) ){
		if( $_GET['file'] == 'first' ){
			$file = new KIMBdbf( 'url/first.kimb' );
		}
		else{
			$file = new KIMBdbf( 'url/nextid_'.$_GET['file'].'.kimb' );
		}
		$id = $file->search_kimb_xxxid( $_GET['reqid'] , 'requestid');
		if( $id  != false ){
			$status = $file->read_kimb_id( $id , 'status' );
			if( $status == 'on' ){
				$file->write_kimb_id( $id , 'add' , 'status' , 'off' );
				$stat = 'off';
			}
			else{
				$file->write_kimb_id( $id , 'add' , 'status' , 'on' );
				$stat = 'on';
			}

			if( $_SESSION['permission'] == 'more' ){
				open_url('/kimb-cms-backend/menue.php?todo=list');
				die;
			}
			else{
				open_url('/kimb-cms-backend/menue.php?todo=connect');
				die;
			}

		}
		else{
			$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
		}
	}
	else{
		$sitecontent->echo_error( 'Ihre Anfrage war fehlerhaft!' , 'unknown');
	}
}
else{
	check_backend_login('four' , 'more');

	$sitecontent->add_site_content('<h2>Startseite Menue</h2>');

	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new">Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Eine neues Menue erstellen.</i></span></a>');
	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect">Zuordnen</b><br /><span class="ui-icon ui-icon-arrowthick-2-e-w"></span><br /><i>Die Menues einer Seite zuordnen und de-, aktivieren.</i></span></a>');
	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list">Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle Menues zum Bearbeiten, De-, Aktivieren und Löschen auflisten.</i></span></a>');
}


$sitecontent->output_complete_site();
?>
