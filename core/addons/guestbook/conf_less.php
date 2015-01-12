<?php

defined('KIMB_Backend') or die('No clean Request');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=guestbook';

$guestfile = new KIMBdbf( 'addon/guestbook__conf.kimb' );

$sitecontent->add_site_content('<hr />');

$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

if( isset( $_GET['edit'] ) && is_numeric( $_GET['id'] ) ){

	if( check_for_kimb_file( 'addon/guestbook__id_'.$_GET['id'].'.kimb' ) ){
		$sitecontent->add_site_content('<h2>Gästebuch der Seite "'.$_GET['id'].'"</h2>');
		$sitecontent->add_site_content('<br /><a href="'.$addonurl.'"><button>Zurück zur Übericht</button></a>');
		$sitecontent->add_html_header('<style>div#guestname{ position:relative; border-bottom:solid 1px #000000; font-weight:bold; }
span#guestdate{ font-weight:normal; position:absolute; right:0px; }
div#guest{ border:solid 1px #000000; border-radius:15px; background-color:#dddddd; padding:10px; margin:5px;}
div#guestinfo{ position:relative; border-top:solid 1px #000000; font-weight:bold; text-align:center; }
span#guestlinks{ font-weight:normal; position:absolute; left:0px;}
span#guestrechts{ font-weight:normal; position:absolute; right:0px;}</style>');

		$gsitefile = new KIMBdbf( 'addon/guestbook__id_'.$_GET['id'].'.kimb' );

		//
		//deakch
		//del
		//

		$ids = explode( ',' , $gsitefile->read_kimb_one( 'idlist' ) );
		$i = 0;
		foreach( $ids as $id ){
			$alles[] = $gsitefile->read_kimb_id( $id );
			$alles[$i]['id'] = $id;
			$i++;
		}

		foreach( $alles as $einer ){
			if ( $einer['status'] == 'off' ){
				$status = '<a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;deakch"><span style="display:inline-block;" class="ui-icon ui-icon-close" title="Dieser Beitrag ist zur Zeit nicht sichtbar. ( click -> ändern )"></span></a>';
			}
			else{
				$status = '<a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;deakch"><span style="display:inline-block;" class="ui-icon ui-icon-check" title="Dieser Beitrag ist zur Zeit sichtbar. ( click -> ändern )"></span></a>';
			}
			$status .= '<span id="bid'.$einer['id'].'" style="display:none;" ><a href="'.$addonurl.'&amp;id='.$_GET['id'].'&amp;bid='.$einer['id'].'&amp;edit&amp;del"><span style="display:inline-block;" class="ui-icon ui-icon-trash" title="Diesen Beitrag löschen! ( erneut clicken )"></span></a></span>';
			$status .= '<span onclick=" $(\'span#bid'.$einer['id'].'\').css( \'display\' , \'inline-block\' ); $( this ).css( \'display\' , \'none\' ); " style="display:inline-block;" class="ui-icon ui-icon-trash" title="Diesen Beitrag löschen! ( zweimal clicken )"></span>';

			$sitecontent->add_site_content( '<div id="guest" >' );		
			$sitecontent->add_site_content( '<div id="guestname" ><span title="Name des User" >'.$einer['name'].'</span>' );
			$sitecontent->add_site_content( '<span id="guestdate" title="Tag und Zeit des Erstellens">'.date( 'd-m-Y H:i:s' , $einer['time'] ).'</span>' );
			$sitecontent->add_site_content( '</div>' );
			$sitecontent->add_site_content( $einer['cont'] );
			$sitecontent->add_site_content( '<div id="guestinfo" >');
			$sitecontent->add_site_content( '<span title="IP des Users ( 0.0.0.0 wenn Speicherung aus )" id="guestlinks">'.$einer['ip'].'</span>' );
			$sitecontent->add_site_content( $status );
			$sitecontent->add_site_content( '<span title="E-Mail Adresse des Users" id="guestrechts">'.$einer['mail'].'</span>' );
			$sitecontent->add_site_content( '</div>' );
			$sitecontent->add_site_content( '</div>' );
		}

		$list = 'no';
	}
	else{
		$list = 'yes';
		$sitecontent->echo_error( 'Die gewünschte Seite hat kein Gästebuch oder es ist leer!' , 'unknown');
	}
}
else{
	$list = 'yes';
}

if( $list == 'yes' ){
	$sitecontent->add_site_content('<h2>Seiten mit Gästebuch</h2>');

	$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Hier können Sie die Beiträge verwalten. Weiters finden Sie unter Add-ons -> Konfiguration -> guestbook."></span>');
	$sitecontent->add_site_content('<table width="100%"><tr><th>SiteID</th></tr>');

	foreach( $guestfile->read_kimb_all_teilpl( 'siteid' ) as $id ){

		$sitecontent->add_site_content('<tr><td><a href="'.$addonurl.'&amp;id='.$id.'&amp;edit">'.$id.'</a></td></tr>');

		$gefunden = 'yes';
	}

	if( $gefunden != 'yes' ){
		$sitecontent->add_site_content('</table>');
		$sitecontent->echo_error( 'Es wurden keine Gästebuchseiten gefunden!' , 'unknown' );
	}
	else{
		$sitecontent->add_site_content('</table>');
	}

}

?>
