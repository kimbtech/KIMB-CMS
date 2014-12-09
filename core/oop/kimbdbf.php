<?php

defined('KIMB_CMS') or die('No clean Request');

/*************************************************/
//KIMB-technologies
//KIMB dbf
//KIMB databasefile
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

error_reporting(0);

//objektorientiert
//objektorientiert
//objektorientiert

class KIMBdbf {

	//Allgemeines zur Klasse
	protected $path;
	protected $datei;
	protected $encryptkey;
	protected $dateicont = 'none';
	
	const DATEIVERSION = '3.00B';
	
	public function __construct($datei, $encryptkey = 'off', $path = __DIR__){
		$datei = preg_replace('/[\r\n]+/', '', $datei);
		$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
		$this->path = $path;
		$this->datei = $datei;
		$this->encryptkey = $encryptkey;
		if(file_exists($this->path.'/kimb-data/'.$this->datei)){
			$this->dateicont = file_get_contents($this->path.'/kimb-data/'.$this->datei);
			if($this->encryptkey != 'off'){
				$this->dateicont = mcrypt_decrypt (MCRYPT_BLOWFISH , $this->encryptkey , $this->dateicont , MCRYPT_MODE_CBC, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_BLOWFISH , MCRYPT_MODE_CBC ), MCRYPT_RAND ));
			}
		}
	}
	
	protected function umbruch_weg($teil) {
		$teil = preg_replace('/[\r\n]+/', '', $teil);
		$teil = str_replace(array('==','<[',']>','about:doc','--entfernt--'),array('=','<','>','aboutdoc','-entfernt-'), $teil);
		return $teil;
	}
	
	protected function file_write($inhalt, $art) {
		if($this->encryptkey != 'off'){
			if($art == 'w+'){
				$inhaltwr = mcrypt_encrypt (MCRYPT_BLOWFISH , $this->encryptkey , $inhalt , MCRYPT_MODE_CBC, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_BLOWFISH , MCRYPT_MODE_CBC ), MCRYPT_RAND ));
			}
			elseif($art == 'a+'){
				$inhaltwr = $this->dateicont.$inhalt;
				$inhaltwr = mcrypt_encrypt (MCRYPT_BLOWFISH , $this->encryptkey , $inhaltwr , MCRYPT_MODE_CBC, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_BLOWFISH , MCRYPT_MODE_CBC ), MCRYPT_RAND ));
			}
			else{
				echo "Error on encrypting KIMBdbf!";
				die;
			}
			$artwr = 'w+';
		}
		else{
			$inhaltwr = $inhalt;
			$artwr = $art;
		}

		$handle = fopen($this->path.'/kimb-data/'.$this->datei , $artwr);
		$ok = fwrite($handle, $inhaltwr);
		fclose($handle);
		if($art == 'w+'){
			$this->dateicont = $inhalt;
		}
		elseif($art == 'a+'){
			$this->dateicont .= $inhalt;
		}
		else{
			echo "Error on writing KIMBdbf!";
			die;
		}
		return $ok;
	}
	
	//KIMB-Dateien lesen
	public function read_kimb_one($teil){  //bei mehreren teilen, oberster treffer
		$teiltext = '<['.$this->umbruch_weg($teil).']>';
		$teile = explode($teiltext, $this->dateicont);
		return $teile[1];
	}
	
	public function read_kimb_all($teil){
		$teiltext = '<['.$this->umbruch_weg($teil).']>';
		$teile = explode($teiltext, $this->dateicont);
		$count = '0';
		$i = '0';
		foreach ($teile as $teil) {
			if ($count % 2 != 0){
				$return[$i] = $teil;
				$i++;
			}
			$count++;
		}
		return $return;
	}
		
	public function read_kimb_search($teil, $search){
		$search = $this->umbruch_weg($search);
		$teiltext = '<['.$this->umbruch_weg($teil).']>';
		$teile = explode($teiltext, $this->dateicont);
		foreach ($teile as $teil) {
			if ($count % 2 != 0){
				if($teil == $search){return true;}
			}
			$count++;
		}
		return false;
	}
	
	//KIMB-Dateien lesen $teil++
	public function read_kimb_search_teilpl($teil, $search){
		$search = $this->umbruch_weg($search);
		$teil = $this->umbruch_weg($teil); 
		$count = '1';
		$gelesen = 'start';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			if($gelesen == $search){return true;}
			$count++;
		}
		return false;
	}
	
	public function read_kimb_all_teilpl($teil){
		$teil = $this->umbruch_weg($teil);
		$count = '1';
		$gelesen = 'start';
		$i = '0';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			if($gelesen != '--entfernt--'){
				$return[$i] = $gelesen;
				$i++;
			}
			$count++;
		}
		array_splice($return, $i-1);
		return $return;
	}
	
	//KIMB-Dateien schreiben public, weiter an protected
	public function write_kimb_new($teil, $inhalt){
		$inhalt = $this->umbruch_weg($inhalt);
		$teil =  $this->umbruch_weg($teil);
		if($this->write_kimb_new_pr($teil, $inhalt)){
			return true;
		}	
		else{
			return false;
		}
	}
	
	public function write_kimb_replace($teil, $inhalt){  //teil darf nur einmal vorhanden sein!!
		$inhalt = $this->umbruch_weg($inhalt);
		$teil =  $this->umbruch_weg($teil);
		if($this->write_kimb_replace_pr($teil, $inhalt)){
			return true;
		}	
		else{
			return false;
		}
	}
	
	public function write_kimb_delete($teil){  //teil darf nur einmal vorhanden sein !!
		$teil = $this->umbruch_weg($teil);
		if($this->write_kimb_delete_pr($teil)){
			return true;
		}	
		else{
			return false;
		}
	}

	//KIMB-Dateien schreiben protected
	protected function write_kimb_new_pr($teil, $inhalt){
		if(!file_exists ($this->path.'/kimb-data/'.$this->datei)){
			$writetext .= '<[about:doc]>KIMB dbf V'.self::DATEIVERSION.' - KIMB-technologies<[about:doc]>';
		}
		$writetext .= "\r".'<['.$teil.']>'.$inhalt.'<['.$teil.']>';
		$ok = $this->file_write($writetext, 'a+');
		if($ok == "false"){return false;}	
		else{return true;}
	}
	
	protected function write_kimb_replace_pr($teil, $inhalt){  //teil darf nur einmal vorhanden sein!!
		$teiltext = '<['.$teil.']>';
		$teile = explode($teiltext, $this->dateicont);
		$writetext .= $teile[0];
		$writetext .= '<['.$teil.']>'.$inhalt.'<['.$teil.']>';
		$writetext .= $teile[2];
		$ok = $this->file_write($writetext, 'w+');
		if($ok == 'false' || $this->dateicont == ''){
			return false;
		}	
		else{
			return true;
		}
	}
	
	protected function write_kimb_delete_pr($teil){  //teil darf nur einmal vorhanden sein !!
		$teiltext = '<['.$teil.']>';
		$teile = explode($teiltext, $this->dateicont);
		$writetext .= $teile[0];
		$writetext .= $teile[2];
		$ok = $this->file_write($writetext, 'w+');
		if($ok == "false" || $this->dateicont == ''){return false;}	
		else{return true;}
	}
	
	//KIMB-Datei $teil++ schreiben
	protected function for_write_kimb_teilpl_add($teil){
		$count = '1';
		$gelesen = 'start';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			$count++;
		}
		return $count-1;
	}
	
	protected function for_write_kimb_teilpl_del($teil, $search){
		$count = '1';
		$gelesen = 'start';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			if($gelesen == $search){return $count;}
			$count++;
		}
		return false;
	}
	
	public function write_kimb_teilpl($teil, $inhalt, $todo){
		$inhalt = $this->umbruch_weg($inhalt);
		$teil =  $this->umbruch_weg($teil);
		if($todo == 'add'){
			$anzahl = $this->for_write_kimb_teilpl_add($teil);
			$teilneu = $teil.$anzahl;
			if($this->write_kimb_new_pr($teilneu, $inhalt)){return true;}
			else{return false;}
		}
		elseif($todo == 'del'){
			$teilneu = $teil.$this->for_write_kimb_teilpl_del($teil, $inhalt);
			if($this->write_kimb_replace_pr($teilneu, '--entfernt--')){return true;}
			else{return false;}
		}
		else{
			return false;
		}
	}
	
	//kimb datei loeschen
	public function delete_kimb_file(){
		if(unlink($this->path.'/kimb-data/'.$this->datei)){ return true;}
		else{return false;}
	}
	
	//gesamte kimb datei ausgaben
	public function show_kimb_file() {
		return $this->dateicont;
	}
	
	//zuordnungen id
	public function read_kimb_id($id, $xxxid = 'all') {
		$id = $this->umbruch_weg($id);
		$xxxid = $this->umbruch_weg($xxxid);
		$idinfo = $this->read_kimb_one($id);
		if ($idinfo == ''){return false;}
		$idinfos = explode('==', $idinfo);
		if($xxxid == 'all'){
			foreach ($idinfos as $info) {
				$return[$info] = $this->read_kimb_one($id.'-'.$info);
			}
			return $return;
		}	
		else{
			foreach ($idinfos as $info) {
				if($xxxid == $info){return $this->read_kimb_one($id.'-'.$info);}
			}
			return false;
		}
	}

	public function read_kimb_all_xxxid($id) {
		$id = $this->umbruch_weg($id);
		$idinfo = $this->read_kimb_one($id);
		$idteile = explode('==', $idinfo);
		return $idteile;
	}
	
	public function search_kimb_id($search, $id) {
		$search = $this->umbruch_weg($search);
		$id = $this->umbruch_weg($id);
		$idinfo = $this->read_kimb_one($id);
		if ($idinfo == ''){return false;}
		$idinfos = explode('==', $idinfo);
		foreach ($idinfos as $info) {
			if($this->read_kimb_one($id.'-'.$info) == $search){return $info;}
		}
		return false;
	}
	
	public function search_kimb_xxxid($search, $xxxid, $ende = '1000') {
		$search = $this->umbruch_weg($search);
		$xxxid = $this->umbruch_weg($xxxid);
		$id = '1';
		while ($id <= $ende) {
			$idinhalt = $this->read_kimb_id($id, $xxxid);
			if($idinhalt == $search){return $id;}
			$id++;
		}
		return false;		

	}

	public function next_kimb_id($ende = '1000'){
		$id = '1';
		while ($id <= $ende) {
			$idinhalt = $this->read_kimb_one($id);
			if($idinhalt == ''){return $id;}
			$id++;
		}
		return false;
	}
	
	public function write_kimb_id($id, $todo, $xxxid = 'none', $inhalt = '') {
		$id = $this->umbruch_weg($id);
		$xxxid = $this->umbruch_weg($xxxid);
		$inhalt = $this->umbruch_weg($inhalt);
		
		$idinfo = $this->read_kimb_one($id);
		if ($idinfo == '' && $todo != 'add'){return false;}
		if ($idinfo == ''){$new = 'yes';}
		$idinfos = explode('==', $idinfo);
	
		if($todo == 'add' && $inhalt != '' && $xxxid != 'none'){
			if($this->read_kimb_one($id.'-'.$xxxid) == ''){
				$this->write_kimb_new_pr($id.'-'.$xxxid, $inhalt);
				$newxxx = 'yes';
			}
			else{
				$this->write_kimb_replace_pr($id.'-'.$xxxid, $inhalt);
			}
			
			if($newxxx == 'yes' && $new != 'yes'){
				$infotag = $idinfo.'=='.$xxxid;
			}
			elseif($newxxx == 'yes'){
				$infotag = $xxxid;
			}
			else{
				foreach ($idinfos as $info) {
					$infotag .= $info.'==';
				}
				$laenge = strlen($infotag)-2;
				$infotag = substr($infotag, 0, $laenge);
			}
			
			if($new == 'yes'){
				$this->write_kimb_new_pr($id, $infotag);
			}
			else{
				$this->write_kimb_replace_pr($id, $infotag);
			}
			return true;
		}
		elseif($todo == 'del' && $xxxid == 'none' && $inhalt == ''){
			foreach ($idinfos as $info) {
				$this->write_kimb_delete_pr($id.'-'.$info);
			}
			$this->write_kimb_delete_pr($id);
			return true;
		}
		elseif($todo == 'del' && $xxxid != 'none' && $inhalt == ''){
			$gut = '0';
			foreach ($idinfos as $info) {
				if($xxxid == $info){$this->write_kimb_delete_pr($id.'-'.$info); $gut++;}
				if($info != $xxxid){$infotag .= $info.'==';}
			}
			$laenge = strlen($infotag)-2;
			$infotag = substr($infotag, 0, $laenge);
			if($this->write_kimb_replace_pr($id, $infotag)){$gut++;}
			if($gut == '2'){return true;}
			else{return false;}
		}
		else{
			return false;
		}
	}
}


//funktionell
//funktionell
//funktionell

$allgconfserversitepath = __DIR__;

//KIMB-Dateien lesen

function read_kimb_one($datei, $teil){  //bei mehreren teilen, oberster treffer
	global $allgconfserversitepath;
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	return $teile[1];
}
	
function read_kimb_search($datei, $teil, $search){
	$search = preg_replace('/[\r\n]+/', '', $search);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	global $allgconfserversitepath;
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	foreach ($teile as $teil) {
		if ($count % 2 != 0){
			if($teil == $search){return 'true';}
		}
		$count++;
	}
	return 'false';
}

//KIMB-Dateien schreiben
	
function write_kimb_new($datei, $teil, $inhalt){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$inhalt = preg_replace('/[\r\n]+/', '', $inhalt);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	global $allgconfserversitepath;
	if(!file_exists ($allgconfserversitepath.'/kimb-data/'.$datei)){
		$writetext .= '<[about:doc]>KIMB dbf V1.0 - KIMB-technologies<[about:doc]>';
		}
	$writetext .= "\r".'<['.$teil.']>'.$inhalt.'<['.$teil.']>';
	$handle = fopen($allgconfserversitepath.'/kimb-data/'.$datei,'a+');
	$ok = fwrite($handle, $writetext);
	fclose($handle);
	if($ok == "false"){return 'false';}	
	else{return 'true';}
}

function write_kimb_replace($datei, $teil, $inhalt){  //teil darf nur einmal vorhanden sein!!
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$inhalt = preg_replace('/[\r\n]+/', '', $inhalt);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	global $allgconfserversitepath;
	$inhalt = preg_replace('/[\r\n]+/', '', $inhalt);
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	$writetext .= $teile[0];
	$writetext .= '<['.$teil.']>'.$inhalt.'<['.$teil.']>';
	$writetext .= $teile[2];
	$handle = fopen($allgconfserversitepath.'/kimb-data/'.$datei,'w+');
	$ok = fwrite($handle, $writetext);
	fclose($handle);
	if($ok == "false" && $inhaltdatei == '' && $teile == ''){return 'false';}	
	else{return 'true';}
}

function write_kimb_delete($datei, $teil){  //teil darf nur einmal vorhanden sein !!
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	global $allgconfserversitepath;
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	$writetext .= $teile[0];
	$writetext .= $teile[2];
	$handle = fopen($allgconfserversitepath.'/kimb-data/'.$datei,'w+');
	$ok = fwrite($handle, $writetext);
	fclose($handle);
	if($ok == "false" && $inhaltdatei == '' && $teile == ''){return 'false';}	
	else{return 'true';}
}

//KIMB-Datei loeschen
function delete_kimb_datei($datei){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	global $allgconfserversitepath;
	if(unlink($allgconfserversitepath.'/kimb-data/'.$datei)){ return 'true';}
	else{ return 'false';}
}


?>
