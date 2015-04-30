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

defined('KIMB_Backend') or die('No clean Request');

class ADDonAPI{

	protected $be, $fe, $funcclass, $addon;

	public function __construct( $addon ){
		$this->addon = $addon;

		$this->be = new KIMBdbf('addon/wish/be_all.kimb');
		$this->fe = new KIMBdbf('addon/wish/fe_all.kimb');
		$this->funcclass = new KIMBdbf('addon/wish/funcclass_stelle.kimb');
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

	public function del(){
		// Add-on Wünsche löschen

		$re = true;

		foreach( array( 'fe', 'be', 'funcclass' ) as $fi ){
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
