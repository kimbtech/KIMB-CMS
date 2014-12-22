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


// Diese Datei wird für jedes Menue ausgeführt
// Folgende Variablen sind definiert:
//    $name, $niveau, $clicked, $link
// Diese Datei ist Teil eines Objekts
// Das fertige Menue wird, je nach Theme über $this->menue ausgegeben

if( !isset( $this->niveau ) ){
	$this->menue .= '<li>'."\r\n";
}
elseif( $this->niveau == $niveau ){
	$this->menue .= '</li><li>'."\r\n";
}
elseif( $this->niveau < $niveau ){
	$this->menue .= '<ul><li>'."\r\n";
	$this->ulauf = $this->ulauf + 1;
}
elseif( $this->niveau > $niveau ){
	$this->menue .= '</li></ul><li>'."\r\n";
	$this->ulauf = $this->ulauf - 1;
}

if( $clicked == 'yes' ){
	$this->menue .=  '<a style="background-color:#000000; color: #ffffff !important; border-radius:15px;" href="'.$link.'">'.$name.'</a>'."\r\n";
}
else{
	$this->menue .=  '<a href="'.$link.'">'.$name.'</a>'."\r\n";
}

$this->niveau = $niveau;

?>
