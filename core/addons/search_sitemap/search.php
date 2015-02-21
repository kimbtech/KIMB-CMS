<?php

defined('KIMB_CMS') or die('No clean Request');

$sitecontent->add_site_content( '<form method="post" action="">' );
$sitecontent->add_site_content( '<input type="text" name="search" placeholder="Suchbegriff" value="'.htmlentities( $begriff ).'">' );
$sitecontent->add_site_content( '<input type="submit" value="Suchen">' );
$sitecontent->add_site_content( '</form>' );

if( !empty( $begriff ) ){

	$anzahl = 0;

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');

	make_menue_array();

	foreach( $menuearray as $arr ){
		$seachlist[] = array( 'siteid' => $arr['siteid'], 'requestid' => $arr['requid'], 'menuename' => $arr['menuname'] );
	}

	$maxveruch = $search_sitemap['file']->read_kimb_one( 'maxversuch' );
	$maxerg = $search_sitemap['file']->read_kimb_one( 'maxerg' );

	$parts = array( 'name', 'content' );

	foreach( $parts as $part ){
		foreach( $seachlist as $teil ){

			if( $part == 'name' ){
				$string = $teil['menuename'];
				$requid = $teil['requestid'];
				$sitename = $teil['menuename'];
				//Inhalte für Vorschau
			}
			elseif( $part == 'content' ){
				//Dateiinhalte
			}

			if( stripos ( $string , $begriff ) !== false ){
				$resultate .= '<li>';
				$resultate .= '<b><u><a href="'.$allgsysconf['siteurl'].'/index.php?id='.$requid.'" target="_blank">'.$sitename.'</a></b></u><br />';
				$resultate .= substr( $inhalt , '0' , '200' ).' ...';
				$resultate .= '</li>';

				$anzahl++;
				$versuch++;
			}
			else{
				$versuch++;
			}

			if( $versuch >= $maxveruch || $anzahl >= $maxerg ){
			
				$anzahl .= ' +';
				$break = 'yes';
				break;
			}
		}

		if( isset( $break ) ){
			break;
		}
	}

	$sitecontent->add_site_content( '<h2>Resultate der Suche</h2>' );
	$sitecontent->add_site_content( '<hr />' );
	$sitecontent->add_site_content( 'Anzahl der Ergebnisse: '.$anzahl );
	$sitecontent->add_site_content( '<br /><br />' );
	$sitecontent->add_site_content( '<ul>' );
	$sitecontent->add_site_content( $resultate );
	$sitecontent->add_site_content( '<ul>' );
}

?>
