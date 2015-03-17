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

$sitecontent->add_site_content('<hr /><br /><h2>Auto_Update</h2>');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more&addon=auto_update';

if( !is_object( $updatefile ) ){
	$updatefile = new KIMBdbf( 'addon/auto_update__info.kimb' );
}

if( !ini_get('allow_url_fopen') ) {
	$sitecontent->echo_error( 'PHP muss URL-fopen erlauben!' );
}
elseif( !is_writable( __DIR__.'/temp/' ) ){
	$sitecontent->echo_error( 'Der Ordner "'.__DIR__.'/temp/" muss schreibbar sein!' );
}
elseif( $_GET['task'] == 'update' ){

	$sitecontent->add_site_content('<a href="'.$addonurl.'">&larr; Übersicht</a>');

	$sitecontent->add_site_content('<h3>Update durchführen</h3>');
	$sitecontent->echo_message( 'Erstellen Sie vor jedem Update ein Backup um einem Datenverlust vorzubeugen!!<br />Add-ons werden nicht automatischt aktualisiert' );
	$sitecontent->add_site_content( '<br /><hr /><br />' );

	$addonurl .= '&task='.$_GET['task'];

	require_once( __DIR__.'/updator.php' );

}
else{
	$sitecontent->add_site_content( '<h3>Übersicht</h3>');
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( 'Hier können Sie die Aktualität Ihres CMS überprüfen und wenn möglich ein Update durchführen.');
	$sitecontent->add_site_content( 'Alle 3 Tage wird bei einem Besuch des Backends automatisch eine Prüfung der Aktualität durchgeführt.');

	if( !is_array( $updatearr ) && isset( $_GET['updstat'] ) ){

		require_once( __DIR__.'/check.php' );

		$updatefile->write_kimb_replace( 'lasttime', time() );
		$updatefile->write_kimb_replace( 'lastanswer', $update );
		$updatefile->write_kimb_replace( 'newv', $updatearr['newv'] );
		$lasttime = time();

	}
	else{
		$update = $updatefile->read_kimb_one( 'lastanswer' );
		$updatearr['newv'] = $updatefile->read_kimb_one( 'newv' );
		$lasttime = $updatefile->read_kimb_one( 'lasttime' );
		$updatearr['sysv'] = $allgsysconf['build'];
	}

	$sitecontent->add_site_content('<ul>');
	$sitecontent->add_site_content('<li>Version des Systems: '.$updatearr['sysv'].'</li>');
	$sitecontent->add_site_content('<li>Aktuelle Version: '.$updatearr['newv'].'</li>');
	$sitecontent->add_site_content('<li>Stand: '.date( 'd-m-Y H:i' , $lasttime).' <a href="'.$addonurl.'&updstat"><span class="ui-icon ui-icon-refresh" title="Status neu abfragen!" style="display:inline-block;"></span></a></li>');
	$sitecontent->add_site_content('</ul>');

	if( $update == 'yes' ){
		$sitecontent->add_site_content('<br /><br />');
		$sitecontent->add_site_content('<a href="'.$addonurl.'&task=update"><button title="Führen Sie ein automatisches Update des CMS durch!" >Update starten</button></a>');
		$sitecontent->add_site_content('<br /><br />');
	}
	else{
		$sitecontent->add_site_content('<br /><br />');
		$sitecontent->add_site_content('<button disabled="disabled" onclick="return:false;">Kein Update verfügbar!</button>');
		$sitecontent->add_site_content('<br /><br />');
	}

	foreach( scandir( __DIR__.'/temp/' ) as $zip ){
		unlink( __DIR__.'/temp/'.$zip );
	}
}

?>
