<?php
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
	$this->menue .= '<ul><li>'."\r\n";
	$this->ulauf = $this->ulauf + 1;
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
