<?php
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
