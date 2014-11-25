<?php

if($addon == 'first'){
	require_once(__DIR__'/../addons/addons_second.php');
}
elseif($addon == 'second'){
	require_once(__DIR__'/../addons/addons_second.php');
}
else{
	echo "Fehlerhafter Add-on Aufruf"; 
	die;
}

?>
