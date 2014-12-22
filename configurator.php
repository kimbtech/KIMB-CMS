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


error_reporting('0');

if(file_exists ('conf-enable') != 'true'){
	echo('<title>KIMB GMS - Installation</title><link rel="shortcut icon" href="system/load/KIMB.ico" type="image/x-icon; charset=binary"><h1>Error - 404</h1>Bitte schalten Sie den Configurator frei, erstellen Sie eine leere "conf-enable" Datei im GMS-Root-Verzeichnis.<br /> Please activate the configurator, create an empty "conf-enable" file in the GMS root folder.'); die;
}

echo('
<!DOCTYPE HTML >
<html><head>
<title>KIMB CMS - Installation</title>
<link rel="shortcut icon" href="system/load/KIMB.ico" type="image/x-icon; charset=binary">
<link rel="icon" href="system/load/KIMB.ico" type="image/x-icon; charset=binary">
<style>
body { 
	background-color:#999999; 
	font-family: Ubuntu, Arial;
	color:#000000;
}
#main {
  	width:800px;
	margin:auto;
	text-align:left;
  	background-color:#ffffff;
	border: 5px solid #55dd77;
	border-radius:20px;
	padding:20px;
}
#wichtig{
	background-color:#ff0000;
	color:#ffffff;
	border-radius:10px;
	padding:30px;
	border:solid 2px orange;

}
</style>
<script language="javascript" src="load/system/jquery/jquery.min.js"></script>
<script>
$(function() {
	var inhaltfile = "No clean Request";

	$.get( "core/conf/funktionen.php", function( data ) {
		if( data == inhaltfile){
			$( "#wichtig" ).css( "display" , "block" );
		}
	});
});
</script>
</head><body>

<div id="main">
<h1 style="border-bottom:5px solid #55dd77;" >KIMB CMS - Installation</h1>
<div style="display:none;" id="wichtig" >
	<b>Achtung:</b><br />Das Verzeichnis /core/ und seine Unterverzeichnisse sind nicht gesch&uuml;tzt! <br /> Bitte sperren Sie diese Verzeichnis f&uuml;r jegliche Browseraufrufe!!
</div>
<br />
');



if($_GET['step'] == '2'){

	echo '<form method="post" action="configurator.php?step=3">';
	echo '<input type="text" name="a101a" value="" size="60"><br />(Name der Seite)<br /><br />';
	echo '<input type="text" name="a104a" value="" size="60"><br />(Meta Seitenbeschreibung)<br /><br />';

	echo '<input type="password" name="a401a" value="" size="60"><br />(Passwort des Administrators)<br /><br />';
	echo '<input type="text" name="a402a" value="" size="60"><br />(Username des Administrators)<br /><br />';
	echo '<input type="text" name="a403a" value="" size="60"><br />(Name des Administrators)<br /><br />';
	echo '<input type="text" name="a112a" value="" size="60"><br />(E-Mail Adresse des Administrators)<br /><hr /><hr />';

	echo '<input type="submit" value="Weiter"><br />';
	echo '</form>';
}

elseif($_GET['step'] == '3'){

	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$url = substr($urlg, '0', '-'.strlen(strrchr($urlg, '/')));

	$alles = '!"#%&()*+,-./:;?[\]_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$laenge = '32';
	$anzahl = strlen($alles);
	$i = '1';
	$output = '';
	while($i <= $laenge){
		$stelle = mt_rand('0', $anzahl); 
		$output .= $alles{$stelle};
		$i++;
	}

	$addconf = '<[001-sitename]>'.$_POST['a101a'].'<[001-sitename]>
<[001-sitefavi]>'.$url.'/system/load/KIMB.ico<[001-sitefavi]>
<[001-siteurl]>'.$url.'<[001-siteurl]>
<[001-description]>'.$_POST['a104a'].'<[001-description]>
<[001-admingetvari]>'.$_POST['a105a'].'<[001-admingetvari]>
<[001-serversitepath]>'.__DIR__.'<[001-serversitepath]>
<[001-copyrightname]>'.$_POST['a107a'].'<[001-copyrightname]>
<[001-impressumlink]>'.$_POST['a109a'].'<[001-impressumlink]>
<[001-adminmail]>'.$_POST['a112a'].'<[001-adminmail]>
<[001-loginokay]>'.$output.'<[001-loginokay]>
<[012-passw]>'.md5($_POST['a401a']).'<[012-passw]>
<[012-name]>'.$_POST['a403a'].'<[012-name]>
<[012-username]>'.$_POST['a402a'].'<[012-username]>
<[011-onoff]>'.$_POST['a301a'].'<[011-onoff]>
<[011-newtoadmin]>'.$_POST['a302a'].'<[011-newtoadmin]>
<[011-usermailtest]>'.$_POST['a303a'].'<[011-usermailtest]>
<[010-onoff]>'.$_POST['a201a'].'<[010-onoff]>
<[010-abs]>gms@'.$_SERVER['HTTP_HOST'].'<[010-abs]>
<[010-logofdatapath]>'.__DIR__.'/kimb-data/log<[010-logofdatapath]>';
	
	$handle = fopen(__DIR__.'/kimb-data/configuration.kimb', 'a+');
	fwrite($handle, $addconf);
	fclose($handle);

	echo('Installation erfolgreich! <a href="'.$url.'/system/login.php"><button>Zum Frontend</button></a><br />');

	unlink('conf-enable');

}

else{

	if (version_compare(PHP_VERSION, '5.0.0', '<' )) {
    		echo '<b style="color:#dd4444; font-size:30px;">Dieses System wurde f&uuml;r PHP 5 und h&ouml;her entwickelt, bitte f&uuml;hren Sie ein PHP-Update durch!</b><br />';
	}	

	$count = '0';

	if(is_writable('core/oop/kimb-data') && is_writable('core/oop/kimb-data/addon') && is_writable('core/oop/kimb-data/cache') && is_writable('core/oop/kimb-data/menue') && is_writable('core/oop/kimb-data/site') && is_writable('core/oop/kimb-data/url') && is_writable('core/oop/kimb-data/backend') && is_writable('core/oop/kimb-data/backend/users') ){
		echo 'core/oop/kimb-data und Unterverzeichnise sind schreibbar -> OK<br />';
		$count++;
	}
	else{
		echo '<b style="color:#dd4444">core/oop/kimb-data und Unterverzeichnise sind nicht schreibbar -> Fehler!!</b><br />';
	}

	if(is_writable('core/oop/kimb-data/index.kimb')){
		echo '( Die Dateien unter core/oop/kimb-data/ sind auch schreibbar -> OK )<br /><br />';
		$count++;
	}
	else{
		echo'<b style="color:#dd4444">Die Dateien unter core/oop/kimb-data/ sind nicht schreibbar -> Fehler!!</b><br /><br />';
	}

	if(is_writable('core/addons')){
		echo 'core/addons ist schreibbar -> OK<br /><br />';
		$count++;
	}
	else{
		echo '<b style="color:#dd4444">core/addons ist nicht schreibbar -> Fehler!!</b><br /><br />';
	}

	if(is_writable('core/secured')){
		echo 'core/secured ist schreibbar -> OK<br /><br />';
		$count++;
	}
	else{
		echo '<b style="color:#dd4444">core/secured ist nicht schreibbar -> Fehler!!</b><br /><br />';
	}

	if(is_writable('core/theme')){
		echo 'core/theme ist schreibbar -> OK<br /><br />';
		$count++;
	}
	else{
		echo '<b style="color:#dd4444">core/theme ist nicht schreibbar -> Fehler!!</b><br /><br />';
	}

	if(is_writable('load/addondata')){
		echo 'load/addondata ist schreibbar -> OK<br /><br />';
		$count++;
	}
	else{
		echo '<b style="color:#dd4444">load/addondata ist nicht schreibbar -> Fehler!!</b><br /><br />';
	}

	if(is_writable('load/userdata')){
		echo 'load/userdata ist schreibbar -> OK<br /><br />';
		$count++;
	}
	else{
		echo '<b style="color:#dd4444">load/userdata ist nicht schreibbar -> Fehler!!</b><br /><br />';
	}

	if(is_writable('load/system/theme')){
		echo 'load/system/theme ist schreibbar -> OK<br /><br />';
		$count++;
	}
	else{
		echo '<b style="color:#dd4444">load/system/theme ist nicht schreibbar -> Fehler!!</b><br /><br />';
	}

	if($count == '8'){
		echo('<a href="configurator.php?step=2"><button>Weiter</button></a></br />');
	}
	else{
		echo('<b style="color:#dd4444">Die Fehler m&uuml;ssen entfernt werden.<br />Dateirechte eingestellt?</b><br /> <a href="configurator.php"><button>N&auml;chster Versuch</button></a></br />');
	}

}

echo('</div></body></html>');
?>

