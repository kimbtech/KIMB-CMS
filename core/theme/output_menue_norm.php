<?php
// Diese Datei wird für jedes Menue ausgeführt
// Folgende Variablen sind definiert:
//    $name, $niveau, $clicked, $link
// Diese Datei ist Teil eines Objekts
// Das fertige Menue wird, je nach Theme über $this->menue ausgegeben

if( $niveau == '1' ){
	if( $clicked == 'yes' ){
		$this->menue[1] .=  '<a style="color:#0000ff; background-color:#EEC900; border:solid 2px #ffffff;" class="menu" href="'.$link.'">'.$name.'</a>'."\r\n";
	}
	else{
		$this->menue[1] .=  '<a class="menu" href="'.$link.'">'.$name.'</a>'."\r\n";
	}
}
if( $niveau == '1' ){
	if( $clicked == 'yes' ){
		$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
	}
	else{
		$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
	}
}
elseif( $niveau == '2'){
	if( $clicked == 'yes' ){
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
		$this->menue[2] .=  '</li></ul>';
	}
	else{
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
		$this->menue[2] .=  '</li></ul>';
	}
}
elseif( $niveau == '3'){
	if( $clicked == 'yes' ){
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
		$this->menue[2] .=  '</li></ul>';
		$this->menue[2] .=  '</li></ul>';
	}
	else{
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
		$this->menue[2] .=  '</li></ul>';
		$this->menue[2] .=  '</li></ul>';
	}
}
else{
	if( $clicked == 'yes' ){
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li><a style="color:red;" href="'.$link.'">'.$name.'</a></li>'."\r\n";
		$this->menue[2] .=  '</li></ul>';
		$this->menue[2] .=  '</li></ul>';
		$this->menue[2] .=  '</li></ul>';
	}
	else{
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li style="list-style-type:none" ><ul>';
		$this->menue[2] .=  '<li><a href="'.$link.'">'.$name.'</a></li>'."\r\n";
		$this->menue[2] .=  '</li></ul>';
		$this->menue[2] .=  '</li></ul>';
		$this->menue[2] .=  '</li></ul>';
	}
}

?>
