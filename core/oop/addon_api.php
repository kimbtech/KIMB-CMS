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

	protected $allgsysconf, $be, $fe, $funcclass, $addon;

	public function __construct( $addon ){
		$this->allgsysconf = $allgsysconf;
		$this->addon = $addon;

		$this->be = new KIMBdbf('addon/wish/be_all.kimb');
		$this->fe = new KIMBdbf('addon/wish/fe_all.kimb');
		$this->funcclass = new KIMBdbf('addon/wish/funcclass_stelle.kimb');
	}

	protected function get_addon_id(){

		$id = $addonwish->search_kimb_xxxid( $this->addon , 'addon' );

		if( $id != false ){
			return $id;
		}
		else{
			return false;
		}
	}

	public function set_be( $reihen, $site, $rechte ){
		//Backend Wünsche speichern

		// $reihen => vorn oder hinten
		// $site => XXX.php
		// $rechte => more,less,one,six
	}

	public function set_fe( $reihen, $id, $error ){
		//Backend Wünsche speichern

		// $reihen => vorn oder hinten
		// $id => r/s/a + ( ID )
		// $error => no/ all/ (nur) 404/ 403
	}

	public function set_funcclass( $reihen ){
		//Funktionen und Klassen Wünsche speichern

		// $reihen => vorn oder hinten
	}

	public function del(){
		//Add-on Wünsche löschen
	}


}

?>
