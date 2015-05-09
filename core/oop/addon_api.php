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

defined('KIMB_CMS') or die('No clean Request');

class ADDonAPI{

	protected $be, $fe, $funcclass, $addon,$cron;

	public function __construct( $addon ){
		$this->addon = $addon;

		$this->be = new KIMBdbf('addon/wish/be_all.kimb');
		$this->fe = new KIMBdbf('addon/wish/fe_all.kimb');
		$this->funcclass = new KIMBdbf('addon/wish/funcclass_stelle.kimb');
		$this->cron = new KIMBdbf('addon/wish/cron_age.kimb');
	}

	protected function get_addon_id( $fi ){

		$id = $this->$fi->search_kimb_xxxid( $this->addon , 'addon' );

		if( $id != false ){
			return $id;
		}
		else{
			$id = $this->$fi->next_kimb_id();
			if( $this->$fi->write_kimb_id( $id , 'add' , 'addon' , $this->addon ) ){
				return $id;
			}
			else{
				return false;
			}
		}
	}

	public function set_be( $reihen, $site, $rechte ){
		// Backend Wünsche speichern

		// $reihen => vorn oder hinten
		// $site => XXX.php
		// $rechte => more,less,one,six

		if( $reihen != 'vorn' && $reihen != 'hinten' ){
			return false;
		}

		$id = $this->get_addon_id( 'be' );

		if( is_numeric( $id ) ){
			$rstelle = $this->be->write_kimb_id( $id , 'add' , 'stelle' , $reihen );
			$rrecht = $this->be->write_kimb_id( $id , 'add' , 'recht' , $rechte );
			$rsite = $this->be->write_kimb_id( $id , 'add' , 'site' , $site );

			if( $rstelle && $rrecht && $rsite ){
				return true;
			}
		}

		return false;
	}

	public function set_fe( $reihen, $ids, $error ){
		// Frtontend Wünsche speichern

		// $reihen => vorn oder hinten
		// $id => r/s/a + ( ID )
		// $error => no/ all/ (nur) 404/ 403

		if( $reihen != 'vorn' && $reihen != 'hinten' ){
			return false;
		}

		$id = $this->get_addon_id( 'fe' );

		if( is_numeric( $id ) ){
			$rstelle = $this->fe->write_kimb_id( $id , 'add' , 'stelle' , $reihen );
			$rids = $this->fe->write_kimb_id( $id , 'add' , 'ids' , $ids );
			$rerror = $this->fe->write_kimb_id( $id , 'add' , 'error' , $error );

			if( $rstelle && $rids && $rerror ){
				return true;
			}
		}

		return false;
	}

	public function set_funcclass( $reihen ){
		// Funktionen und Klassen Wünsche speichern

		// $reihen => vorn oder hinten

		if( $reihen != 'vorn' && $reihen != 'hinten' ){
			return false;
		}

		$id = $this->get_addon_id( 'funcclass' );

		if( is_numeric( $id ) ){
			if( $this->funcclass->write_kimb_id( $id , 'add' , 'stelle' , $reihen ) ){
				return true;
			}
		}

		return false;
	}
	
	public function set_cron( $minage ){
		// Cron Aufrufabstand in Sekunden speichern

		// $minage => Sekunden zu warten zwischen Abrufen 

		if( is_numeric( $minage ) ){
			$id = $this->get_addon_id( 'cron' );

			if( is_numeric( $id ) ){
				if( $this->cron->write_kimb_id( $id , 'add' , 'minage' , $minage ) && $this->cron->write_kimb_id( $id , 'add' , 'lastaufruf' , '1000' ) ){
					return true;
				}
			}
		}

		return false;
	}

	public function del(){
		// Add-on Wünsche löschen

		$re = true;

		foreach( array( 'fe', 'be', 'funcclass', 'cron' ) as $fi ){
			$id = $this->get_addon_id( $fi );
			if( is_numeric( $id ) && $re == true ){
				$re = $this->$fi->write_kimb_id( $id , 'del' );
			}
			else{
				$re = false;
			}
		}

		return $re;
	}


}

?>
