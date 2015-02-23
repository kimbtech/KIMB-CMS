<?php

defined('KIMB_CMS') or die('No clean Request');

if( $_GET['addon'] == 'sitemapxml' ){

	$conffile = new KIMBdbf( 'addon/sitemapxml__conf.kimb' );
	if( $_GET['key'] != $conffile->read_kimb_one( 'key' ) ){
		echo( 'Sie haben keine Rechte eine Sitemap zu erstellen!' );
		die;
	}

	header('Content-Type: application/xml; charset=utf-8');

	$idfile = new KIMBdbf('menue/allids.kimb');
	$menuenames = new KIMBdbf('menue/menue_names.kimb');
	make_menue_array();

	$map = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\r\n";
	
	foreach( $menuearray as $menuear ){

		if( $allgsysconf['urlrewrite'] == 'on' ){

			$niveau = $menuear['niveau'];

			if( !isset( $thisniveau ) ){
				$grpath = $allgsysconf['siteurl'].'/'.$menuear['path'];
			}
			elseif( $thisniveau == $niveau ){
				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				$grpath = $grpath.'/'.$menuear['path'];
			}
			elseif( $thisniveau < $niveau ){
				$grpath = $grpath.'/'.$menuear['path'];
				$thisulauf = $thisulauf + 1;
			}
			elseif( $thisniveau > $niveau ){
				$i = 1;
				while( $thisniveau != $niveau + $i  ){
					$i++;
					$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				}
				$thisulauf = $thisulauf - $i;

				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				$grpath = substr( $grpath , '0', '-'.strlen(strrchr( $grpath , '/')));
				$grpath = $grpath.'/'.$menuear['path'];
			}
			$url = $grpath.'/';
			$thisniveau = $niveau;

		}
		else{
			$url = $allgsysconf['siteurl'].'/index.php?id='.$menuear['requid'];
		}

		if ( $menuear['status'] == 'on' ){

			$file = new KIMBdbf( 'site/site_'.$menuear['siteid'].'.kimb' );
			$time = date( 'Y-m-d' , $file->read_kimb_one( 'time' ) );

			$map .= '<url>'."\r\n";
			$map .= '<loc>'.$url.'</loc>'."\r\n";
			$map .= '<lastmod>'.$time.'</lastmod>'."\r\n";
			$map .= '</url>'."\r\n";
		}
	}

	$map .= '</urlset> '."\r\n";

	if( isset( $_GET['save'] ) ){

		$file = fopen( __DIR__.'/../../../sitemap.xml' , 'w+' );
		if( fwrite( $file , $map ) && !empty( $map ) ){
			echo( '<xml version="1.0" encoding="UTF-8" >'."\r\n" );
			echo( '<!-- Die Sitemap wurde erfolgreich neu erstellt! -->'."\r\n" );
			echo( '<ok>yes</ok>' );
		}
		else{
			echo( '<xml version="1.0" encoding="UTF-8" >'."\r\n" );
			echo( '<!-- Die Sitemap konnte nicht erfolgreich neu erstellt werden! -->'."\r\n" );
			echo( '<ok>no</ok>' );
		}
		fclose( $file );

	}
	elseif( isset( $_GET['down'] ) ){
		header("Content-Type: application/force-download");
		header('Content-Disposition: attachment; filename= sitemap.xml');
		echo $map;
	}
	else{
		echo $map;
	}

	die;
}

?>
