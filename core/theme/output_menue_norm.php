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

// Diese Datei wird für jedes Menue ausgeführt
// Folgende Variablen sind definiert:
//    $name, $niveau, $clicked, $link
// Diese Datei ist Teil eines Objekts
// Das fertige Menue wird, je nach Theme über $this->menue ausgegeben

if( !isset( $this->menuenumid ) ){
	$this->menuenumid = 0;
}
$this->menuenumid ++;

if( !isset( $this->niveau ) ){
	$this->menue .= '<li>'."\r\n";
}
elseif( $this->niveau == $niveau ){
	$this->menue .= '</li><li>'."\r\n";
}
elseif( $this->niveau < $niveau ){
	$i = 1;
	while( $this->niveau != $niveau - $i  ){
		$i++;
	}
	$this->menue .= str_repeat( '<ul>' , $i ).'<li>'."\r\n";
	$this->ulauf = $this->ulauf + $i;
}
elseif( $this->niveau > $niveau ){
	$i = 1;
	while( $this->niveau != $niveau + $i  ){
		$i++;
	}
	$this->menue .= '</li>'.str_repeat( '</ul>' , $i ).'<li>'."\r\n";
	$this->ulauf = $this->ulauf - $i;
}

if( $clicked == 'yes' ){
	$this->menue .=  '<a id="liclicked" href="'.$link.'" onclick=" return menueclick( '.$this->menuenumid.' ); ">'.$name.'</a>'."\r\n";
}
else{
	$this->menue .=  '<a href="'.$link.'" onclick=" return menueclick( '.$this->menuenumid.' ); ">'.$name.'</a>'."\r\n";
}

$this->niveau = $niveau;

?>
