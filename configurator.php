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
	echo('<title>KIMB CMS - Installation</title><link rel="shortcut icon" href="system/load/KIMB.ico" type="image/x-icon; charset=binary"><h1>Error - 404</h1>Bitte schalten Sie den Configurator frei, erstellen Sie eine leere "conf-enable" Datei im CMS-Root-Verzeichnis.<br /> Please activate the configurator, create an empty "conf-enable" file in the CMS root folder.'); die;
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
<script language="javascript" src="load/system/hash.js"></script>
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
	<b>Achtung:</b><br />Das Verzeichnis /core/ und seine Unterverzeichnisse sind nicht gesch&uuml;tzt! <br /> Bitte sperren Sie diese Verzeichnisse f&uuml;r jegliche Browseraufrufe!!
</div>
<br />
');



if($_GET['step'] == '2'){

	echo '<h2>Allgemeine Systemeinstellungen</h2>';
	echo '<form method="post" action="configurator.php?step=3" onsubmit=" document.getElementById(\'passw\').value = SHA1( document.getElementById(\'passw\').value ); " >';
	echo '<input type="text" name="sitename" value="KIMB CMS" size="60"><br />(Name der Seite)<br /><br />';
	echo '<input type="text" name="metades" value="CMS von KIMB-technologies" size="60"><br />(Meta Seitenbeschreibung)<br /><br />';
	echo '<input type="text" name="sysadminmail" value="serveradmin@server.com" size="60"><br />(E-Mail Adresse des Systemadministrators)<br /><br />';
	echo '<input type="radio" name="urlrew" value="off">OFF <input type="radio" name="urlrew" value="on" checked="checked">ON (Aktivieren Sie URL-Rewriting f&uuml;r das System (Dazu muss Ihr Server die .htaccess im Rootverzeichnis verwenden k&ouml;nnen oder die Variable $SERVER[REQUEST_URI] setzen.))<br /><br />';

	echo '<h2>Ersten Administrator einrichten</h2>';
	echo '<input type="text" name="user" value="admin" readonly="readonly" size="60"><br />(Username des Administrators)<br /><br />';
	echo '<input type="password" name="passhash" placeholder="123456" id="passw" size="60"><br />(Passwort des Administrators)<br /><br />';
	echo '<input type="text" name="name" value="Max Heiner" size="60"><br />(Name des Administrators)<br /><br />';
	echo '<input type="text" name="usermail" value="mail@maxheiner.org" size="60"><br />(E-Mail Adresse des Administrators)<br /><hr /><hr />';

	echo '<input type="submit" value="Weiter"> <b>Alle Felder m&uuml;ssen gef&uuml;llt sein !!</b><br />';
	echo '</form>';
}

elseif($_GET['step'] == '3'){

	if( $_POST['sitename'] == '' || $_POST['metades'] == '' || $_POST['sysadminmail'] == '' || $_POST['passhash'] == '' || $_POST['name'] == '' || $_POST['usermail'] == '' ){

		echo( '<h1 style="color:red;">Alle Felder m&uuml;ssen gef&uuml;llt sein !!</h1><br /><br />' );
		echo( '<a href="configurator.php?step=2" >Zur&uuml;ck</a>' );
		die;
	}


	//Request URL
	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$url = substr($urlg, '0', '-'.strlen(strrchr($urlg, '/')));

	//Zufallsgenerator Loginokay
	$alles = '!"&()*+,-./:;?[\]_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$laenge = '50';
	$anzahl = strlen($alles);
	$i = '1';
	$output = '';
	while($i <= $laenge){
		$stelle = mt_rand('0', $anzahl); 
		$output .= $alles{$stelle};
		$i++;
	}

	//Konfigurationsteile
	//erster
	$addconf = '<[001-sitename]>'.$_POST['sitename'].'<[001-sitename]>
<[001-sitefavi]>'.$url.'/load/system/KIMB.ico<[001-sitefavi]>
<[001-loginokay]>'.$output.'<[001-loginokay]>
<[001-siteurl]>'.$url.'<[001-siteurl]>
<[001-description]>'.$_POST['metades'].'<[001-description]>
<[001-adminmail]>'.$_POST['sysadminmail'].'<[001-adminmail]>
<[001-mailvon]>cms@'.$_SERVER['HTTP_HOST'].'<[001-mailvon]>
<[001-urlrewrite]>'.$_POST['urlrew'].'<[001-urlrewrite]>';


	$handle = fopen(__DIR__.'/core/oop/kimb-data/config.kimb', 'a+');
	fwrite($handle, $addconf);
	fclose($handle);

	//zweiter
	$adduser = '<[1-passw]>'.$_POST['passhash'].'<[1-passw]>
<[1-name]>'.$_POST['name'].'<[1-name]>
<[1-mail]>'.$_POST['usermail'].'<[1-mail]>';


	$handle = fopen(__DIR__.'/core/oop/kimb-data/backend/users/list.kimb', 'a+');
	fwrite($handle, $adduser);
	fclose($handle);

	echo('Installation erfolgreich! <a href="'.$url.'/" target="_blank"><button>Zur Seite</button></a><br />');
	echo('Installation erfolgreich! <a href="'.$url.'/kimb-cms-backend/" target="_blank"><button>Zum Backend</button></a><br />');

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
