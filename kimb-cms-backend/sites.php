<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS
//KIMB ContentManagementSystem
//www.KIMB-technologies.eu
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

//Seite erstellen, zuordnen

if( $_GET['todo'] == 'new' ){
	check_backend_login('two');

	$sitecontent->add_site_content('<h2>Neue Seite</h2>');

	add_tiny( true, true);

	if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){

		$i=1;
		while( 5 == 5 ){
			if( !check_for_kimb_file( '/site/site_'.$i.'.kimb') && !check_for_kimb_file( '/site/site_'.$i.'_deak.kimb') ){
				break;
			}
			$i++;
		}

		$sitef = new KIMBdbf( '/site/site_'.$i.'.kimb' );

		$sitef->write_kimb_new( 'title' , $_POST['title'] );
		$sitef->write_kimb_new( 'header' , $_POST['header'] );
		$sitef->write_kimb_new( 'keywords' , $_POST['keywords'] );
		$sitef->write_kimb_new( 'description' , $_POST['description'] );
		$sitef->write_kimb_new( 'inhalt' , $_POST['inhalt'] );
		$sitef->write_kimb_new( 'footer' , $_POST['footer'] );
		$sitef->write_kimb_new( 'time' , time() );
		$sitef->write_kimb_new( 'made_user' , $_SESSION['name'] );

		open_url('/kimb-cms-backend/sites.php?todo=edit&id='.$i);
		die;

	}
	
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new" method="post"><br />');
	$sitecontent->add_site_content('<input type="text" value="Titel" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
	$sitecontent->add_site_content('<textarea name="header" style="width:74%; height:50px;"></textarea><i>HTML Header </i><br />');
	$sitecontent->add_site_content('<input type="text" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
	$sitecontent->add_site_content('<textarea name="description" style="width:74%; height:50px;"></textarea> <i>Description</i> <br />');
	$sitecontent->add_site_content('<textarea name="inhalt" id="inhalt" style="width:99%; height:300px;">&lt;h1&gt;Titel&lt;/h1&gt;</textarea> <i>Inhalt &uarr;</i> <button onclick="tinychange( \'inhalt\' ); return false;">Editor I/O</button> <br />');
	$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%; height:75px;"></textarea> <i>Footer &uarr;</i> <button onclick="tinychange( \'footer\' ); return false;">Editor I/O</button> <br />');
	$sitecontent->add_site_content('<input type="submit" value="Erstellen"></form>');	

}
elseif( $_GET['todo'] == 'list' ){
	check_backend_login('three');

	$sitecontent->add_site_content('<h2>Liste aller Seiten</h2>');

	$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

	$sitecontent->add_html_header('<script>
	var del = function( id ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=del&id="+id;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	function search(){
		var search = $( "input.search" ).val();
		window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list#" + search;
	}
	</script>');

	$sites = scan_kimb_dir('site/');

	$sitecontent->add_site_content('<span><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new"><span class="ui-icon ui-icon-plus" style="display:inline-block;" title="Eine neue Seite erstellen."></span></a>');
	$sitecontent->add_site_content('<input type="text" class="search" onkeydown="if(event.keyCode == 13){ search(); }" ><button onclick="search();" title="Nach Seitenamen suchen ( genauer Seitenname nötig ).">Suchen</button></span><hr />');
	$sitecontent->add_site_content('<table width="100%"><tr><th width="40px;" >ID</th><th>Name</th><th width="20px;">Status</th><th width="20px;">Löschen</th></tr>');

	$idfile = new KIMBdbf('menue/allids.kimb');

	foreach ( $sites as $site ){
		if( $site != 'langfile.kimb'){
			$sitef = new KIMBdbf('site/'.$site);
			$id = preg_replace("/[^0-9]/","", $site);
			$title = $sitef->read_kimb_one('title');
			$name = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$id.'" title="Seite bearbeiten.">'.$title.'</a>';
			if ( strpos( $site , 'deak' ) !== false ){
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-close" title="Diese Seite ist zu Zeit deaktiviert, also nicht auffindbar. ( click -> ändern )"></span></a>';
			}
			else{
				$status = '<a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=deakch&amp;id='.$id.'"><span class="ui-icon ui-icon-check" title="Diese Seite ist zu Zeit aktiviert, also sichtbar. ( click -> ändern )"></span></a>';
			}
			$del = '<span onclick="var delet = del( '.$id.' ); delet();"><span class="ui-icon ui-icon-trash" title="Diese Seite löschen."></span></span>';
			$zugeor = $idfile->search_kimb_xxxid( $id , 'siteid' );
			if( $zugeor == false ){
				$status .= '<span class="ui-icon ui-icon-alert" title="Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!"></span>';
			}
			$sitecontent->add_site_content('<tr><td>'.$id.'</td><td id="'.$title.'">'.$name.'</td><td>'.$status.'</td><td>'.$del.'</td></tr>');
	
			$liste = 'yes';
		}
	}
	$sitecontent->add_site_content('</table>');

	if( $liste != 'yes' ){
		$sitecontent->echo_error( 'Es wurden keine Seiten gefunden!' );
	}

	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Seite wirklich löschen?</p></div></div>');

}
elseif( $_GET['todo'] == 'edit' && is_numeric( $_GET['id'] ) ){
	check_backend_login('three');

	$sitecontent->add_site_content('<h2>Seite bearbeiten</h2>');
	
	if( !is_object( $sitef ) ){
		if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
			$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'.kimb' );
		}
		elseif( check_for_kimb_file( '/site/site_'.$_GET['id'].'_deak.kimb' ) ){
			$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'_deak.kimb' );
		}
		else{
			$sitecontent->echo_error('Die Seite wurde nicht gefunden' , '404');
			$sitecontent->output_complete_site();
			die;
		}
	}
	
	if( $allgsysconf['lang'] == 'on' && $_GET['langid'] != 0 && is_numeric( $_GET['langid'] ) ){
		$dbftag['title'] = 'title-'.$_GET['langid'];
		$dbftag['keywords'] = 'keywords-'.$_GET['langid'];
		$dbftag['description'] = 'description-'.$_GET['langid'];
		$dbftag['inhalt'] = 'inhalt-'.$_GET['langid'];
		$dbftag['footer'] = 'footer-'.$_GET['langid'];
		
		if( empty( $sitef->read_kimb_one( $dbftag['title'] ) ) && empty( $sitef->read_kimb_one( $dbftag['inhalt'] ) ) ){
			$sitef->write_kimb_one( $dbftag['title'] , 'Title' );
			$sitef->write_kimb_one( $dbftag['keywords'] , '' );
			$sitef->write_kimb_one( $dbftag['description'] , '' );
			$sitef->write_kimb_one( $dbftag['inhalt'] , '<h1>Inhalt</h1>' );
			$sitef->write_kimb_one( $dbftag['footer'] , '' );				
		}
	}
	else{
		$dbftag['title'] = 'title';
		$dbftag['keywords'] = 'keywords';
		$dbftag['description'] = 'description';
		$dbftag['inhalt'] = 'inhalt';
		$dbftag['footer'] = 'footer';
		
		$_GET['langid'] = 0;				
	}
	
	if( $allgsysconf['lang'] == 'on'){
		make_lang_dropdown( '"'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&id='.$_GET['id'].'&langid=" + val', $_GET['langid'] );
	}

	$sitecontent->add_html_header('<script>
	var del = function( id ) {
		$( "#del-confirm" ).show( "fast" );
		$( "#del-confirm" ).dialog({
		resizable: false,
		height:200,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=del&id="+id;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}

	$( function() {
		$( "#libs" ).on( "change", function() {
			var valadd, valold, valnew;

			valadd = $( "#libs" ).val();
			valold = $( "textarea[name=header]" ).val();

			valnew = valold + valadd;

			$( "textarea[name=header]" ).val( valnew );

			return false;
		});
	});
	</script>');
	add_tiny( true, true);

	if( isset( $_POST['title'] ) || isset( $_POST['inhalt'] ) ){

		$sitef->write_kimb_replace( $dbftag['title'] , $_POST['title'] );
		$sitef->write_kimb_replace( 'header' , $_POST['header'] );
		$sitef->write_kimb_replace( $dbftag['keywords'] , $_POST['keywords'] );
		$sitef->write_kimb_replace( $dbftag['description'] , $_POST['description'] );
		$sitef->write_kimb_replace( $dbftag['inhalt'] , $_POST['inhalt'] );
		$sitef->write_kimb_replace( $dbftag['footer'] , $_POST['footer'] );
		$sitef->write_kimb_replace( 'time' , time() );
		$sitef->write_kimb_replace( 'made_user' , $_SESSION['name'] );

	}
	
	$seite['title'] = $sitef->read_kimb_one( $dbftag['title'] );
	$seite['header'] = $sitef->read_kimb_one( 'header' );
	$seite['keywords'] = $sitef->read_kimb_one( $dbftag['keywords'] );
	$seite['description'] = $sitef->read_kimb_one( $dbftag['description'] );
	$seite['inhalt'] = $sitef->read_kimb_one( $dbftag['inhalt'] );
	$seite['footer'] = $sitef->read_kimb_one( $dbftag['footer'] );
	$seite['time'] = $sitef->read_kimb_one( 'time' );
	$seite['time'] = date( "d.m.Y \u\m H:i" , $seite['time'] );

	$idfile = new KIMBdbf('menue/allids.kimb');
	$id = $idfile->search_kimb_xxxid( $_GET['id'] , 'siteid' );

	if( $id == false ){
		$sitecontent->echo_message( 'Achtung, diese Seite ist noch keinem Menü zugeordnet, daher ist sie im Frontend nicht auffindbar!' );
	}

	$sitecontent->add_site_content('<span onclick="var delet = del( '.$_GET['id'].' ); delet();"><span class="ui-icon ui-icon-trash" style="display:inline-block;" title="Diese Seite löschen."></span></span>');
	$sitecontent->add_site_content('<a href="'.$allgsysconf['siteurl'].'/index.php?id='.$id.'" target="_blank"><span class="ui-icon ui-icon-newwin" style="display:inline-block;" title="Diese Seite anschauen."></span></a>');
	$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Seite wirklich löschen?</p></div></div>');
	
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=edit&amp;id='.$_GET['id'].'&amp;langid='.$_GET['langid'].'" method="post"><br />');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['title'].'" name="title" style="width:74%;"> <i>Seitentitel</i><br />');
	$sitecontent->add_site_content('<div style="position:relative;" ><textarea name="header" style="width:74%; height:50px;">'.htmlentities( $seite['header'] ).'</textarea><span style="position:absolute; top:0; left:75.5%;"> <i>HTML Header</i>');
		$sitecontent->add_site_content('<br /> <select id="libs">');
		$sitecontent->add_site_content('<option value=""></option>');
		$sitecontent->add_site_content('<option value="&lt;!-- jQuery --&gt;">jQuery</option>');
		$sitecontent->add_site_content('<option value="&lt;!-- jQuery UI --&gt;">jQuery UI</option>');
		$sitecontent->add_site_content('<option value="&lt;!-- nicEdit --&gt;">nicEdit</option>');
		$sitecontent->add_site_content('<option value="&lt;!-- TinyMCE --&gt;">TinyMCE</option>');
		$sitecontent->add_site_content('<option value="&lt;!-- Hash --&gt;">Hash</option>');
		$sitecontent->add_site_content('</select><span class="ui-icon ui-icon-info" style="display:inline-block;" title="Fügen Sie Ihrer Seite ganz einfach eine JavaScript-Bibilothek hinzu." ></span></span></div>');
	$sitecontent->add_site_content('<input type="text" value="'.$seite['keywords'].'" name="keywords" style="width:74%;"> <i>Keywords</i><br />');
	$sitecontent->add_site_content('<div style="position:relative;" ><textarea name="description" style="width:74%; height:50px;">'.$seite['description'].'</textarea><span style="position:absolute; top:0; left:75.5%;"> <i>Description</i></span></div>');
	$sitecontent->add_site_content('<textarea name="inhalt" id="inhalt" style="width:99%; height:300px;">'.$seite['inhalt'].'</textarea> <i>Inhalt &uarr;</i> <button onclick="tinychange( \'inhalt\' ); return false;">Editor I/O</button> <br />');
	$sitecontent->add_site_content('<textarea name="footer" id="footer" style="width:99%; height:75px;">'.$seite['footer'].'</textarea> <i>Footer &uarr;</i> <button onclick="tinychange( \'footer\' ); return false;">Editor I/O</button> <br />');
	$sitecontent->add_site_content('<input type="text" readonly="readonly" value="'.$seite['time'].'" name="time" style="width:74%;"> <i>Zuletzt geändert</i><br />');
	$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');

}
elseif( $_GET['todo'] == 'del' && is_numeric( $_GET['id'] ) ){
	check_backend_login('three');

	if( !is_object( $sitef ) ){
		if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
			$sitef = new KIMBdbf( '/site/site_'.$_GET['id'].'.kimb' );
		}
		else{
			$sitecontent->echo_error('Die Seite wurde nicht gefunden' , '404');
			$sitecontent->output_complete_site();
			die;
		}
	}

	$sitef->delete_kimb_file();

	open_url('/kimb-cms-backend/sites.php?todo=list');
	die;

}
elseif( $_GET['todo'] == 'deakch' && is_numeric( $_GET['id'] ) ){
	check_backend_login('three');

	if( check_for_kimb_file( '/site/site_'.$_GET['id'].'.kimb' ) ){
		rename_kimbdbf( '/site/site_'.$_GET['id'].'.kimb' , '/site/site_'.$_GET['id'].'_deak.kimb' );
	}
	elseif( !check_for_kimb_file('/site/site_'.$_GET['id'].'.kimb') && check_for_kimb_file( '/site/site_'.$_GET['id'].'_deak.kimb' )  ){
		rename_kimbdbf( '/site/site_'.$_GET['id'].'_deak.kimb' , '/site/site_'.$_GET['id'].'.kimb' );
	}
	open_url('/kimb-cms-backend/sites.php?todo=list');
	die;
}
else{
	check_backend_login('one');

	$sitecontent->add_site_content('<h2>Seiten</h2>');

	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new">Erstellen</b><br /><span class="ui-icon ui-icon-plusthick"></span><br /><i>Eine neue Seite erstellen.</i></span></a>');
	$sitecontent->add_site_content('<span id="startbox"><b><a href="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list">Auflisten</b><br /><span class="ui-icon ui-icon-calculator"></span><br /><i>Alle Seiten zum Bearbeiten, De-, Aktivieren und Löschen auflisten.</i></span></a>');

	$sitecontent->add_site_content('<hr /><u>Schnellzugriffe:</u><br /><br />');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" method="get"><input type="text" name="id" placeholder="ID"><input type="hidden" value="edit" name="todo"><input type="submit" value="Los"> <span title="Geben Sie die SeitenID ein und bearbeiten Sie sofort die Inhalte!">(Seite bearbeiten)</span></form>');
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" method="get"><input type="text" name="id" placeholder="ID"><input type="hidden" value="del" name="todo"><input type="submit" value="Los"> <span title="Geben Sie die SeitenID ein und löschen Sie sofort die Seite!">(Seite löschen)</span></form>');
}

//Add-ons Ende 
require_once(__DIR__.'/../core/addons/addons_be_second.php');

//Ausgabe
$sitecontent->output_complete_site();
?>
