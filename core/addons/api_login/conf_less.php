<?php

defined('KIMB_Backend') or die('No clean Request');

$addonurl = $allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less&addon=api_login';
$feconf = new KIMBdbf( 'addon/felogin__conf.kimb'  );
$feuser = new KIMBdbf( 'addon/felogin__user.kimb'  );

if( isset( $_POST['send'] ) ){

	$file = file_get_contents( __DIR__.'/example.php' );

	$allowgr = "'".implode( "','", $_POST['grs'] )."'";
	$allowusr = "'".implode( "','", $_POST['usr'] )."'";
	$homeid = $feconf->read_kimb_one( 'requid' );

	$search = array( '<<[[url403]]>>', '<<[[homelink]]>>', '<<[[loginokay]]>>', '<<[[allowgr]]>>', '<<[[allowusr]]>>' );
	$replace = array( "'".$_POST['url403']."'", "'".$allgsysconf['siteurl'].'/index.php?id='.$homeid."'", "'".$feconf->read_kimb_one( 'loginokay' )."'", $allowgr, $allowusr );

	header("Content-Type: application/force-download");
	header("Content-type: text/php");
	header('Content-Disposition: attachment; filename= api_login.php');

	echo str_replace( $search, $replace, $file );

	die;

}

$sitecontent->add_site_content( '<hr /><br /><h2>API Login</h2>' );

$sitecontent->add_site_content( 'Generieren Sie hier einen Datei die es ermöglicht den Login des CMS auch auf anderen Seiten zu nutzen.<br />' );
$sitecontent->add_site_content( '<b>Die Systeme (CMS und zu sichernde Seite) müssen auf dem gleichen Server und der gleichen Domain liegen (die PHP-Session muss identisch sein).</b><br />' );
$sitecontent->add_site_content( '[Sollte dies nicht der Fall sein, können Sie unter Konfiguration -> api_login eine Verbindung zu externen Severn aufbauen.]<br />' );

$sitecontent->add_site_content( '<br /><h3>Datei erstellen</h3>' );

$sitecontent->add_site_content( '<form action="'.$addonurl.'" method="post">' );

$sitecontent->add_site_content( '<h4>User zulassen</h4>' );
$sitecontent->add_site_content( '<ul>' );
$users = $feuser->read_kimb_all_teilpl( 'userids' );
foreach( $users as $id ){
	$user = $feuser->read_kimb_id( $id );
	$sitecontent->add_site_content('<li><input type="checkbox" name="usr[]" value="'.$user['user'].'">'.$user['name'].'</li>');
}
$sitecontent->add_site_content( '</ul>' );

$sitecontent->add_site_content( '<h4>Gruppen zulassen</h4>' );
$sitecontent->add_site_content( '<ul>' );
$gruppen = explode( ',' , $feconf->read_kimb_one( 'grlist' ) );
foreach( $gruppen as $gr ){
	$read = $feconf->read_kimb_one( $gr );
	if( $read != '' ){
		$sitecontent->add_site_content('<li><input type="checkbox" name="grs[]" value="'.$gr.'">'.$gr.'</li>');
	}
}
$sitecontent->add_site_content( '</ul>' );
$sitecontent->add_site_content( '<input type="text" name="url403" placeholder="Error 403 Fehlerseite"><b title="Geben Sie eine URL zu einer &apos;403 Forbidden&apos; Fehlerseite an. ( wenn leer Vorgabe )">*</b><br />' );
$sitecontent->add_site_content( '<input type="hidden" name="send" value="send">' );
$sitecontent->add_site_content( '<input type="submit" value="Download File">' );
$sitecontent->add_site_content( '</form>' );

$sitecontent->add_site_content( '<br /><h3>Nutzung</h3>' );

$sitecontent->add_site_content( '<ol>' );
$sitecontent->add_site_content( '<li>Laden Sie die Datei auf Ihren Server</li>' );
$sitecontent->add_site_content( '<li>Inkludieren Sie die Datei in das zu sichernde System<br /><i>Nutzen Sie eine Stelle die bei jedem Aufruf ausgeführt wird z.B. in der Konfigurationsdatei.</i></li>' );
$sitecontent->add_site_content( '<li>Führen Sie die Funktion "cmsfelogin();" aus.<br /><i>Diese prüft das Login und stoppt wenn nötig die Verarbeitung.</i></li>' );
$sitecontent->add_site_content( '<li>Geben Sie den Rückgabewert der Funktion aus.<br /><i>Der HTML-Body sollte schon begonnen haben. Es wird oben rechts ein Kasten mit Wilkommensmedlung und Logoutbutton angezeigt.</i></i></li>' );
$sitecontent->add_site_content( '</ol>' );

$bsp = <<<EOD
	<code><span style="color: #000000"><br><span style="color: #0000BB">&lt;?php<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//Inkludieren<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">require(&nbsp;</span><span style="color: #0000BB">__DIR__</span><span style="color: #007700">.</span><span style="color: #DD0000">'/api_login.php'&nbsp;</span><span style="color: #007700">);<br><br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//Konfiguration<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">&#036;url&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #DD0000">'http://example.com/'</span><span style="color: #007700">;<br>&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//Blocken<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">&#036;login_info&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">cmsfelogin</span><span style="color: #007700">();<br><br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//Ablauf<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if(&nbsp;isset(&nbsp;</span><span style="color: #0000BB">&#036;_POST</span><span style="color: #007700">[</span><span style="color: #DD0000">'newname'</span><span style="color: #007700">]&nbsp;){<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">unlink</span><span style="color: #007700">(&nbsp;</span><span style="color: #DD0000">'oldfile.txt'&nbsp;</span><span style="color: #007700">);<br>&nbsp;&nbsp;&nbsp;&nbsp;}<br><br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//Ausgabe<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">echo&nbsp;</span><span style="color: #DD0000">'&lt;!DOCTYPE&nbsp;html&gt;'</span><span style="color: #007700">;<br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span style="color: #DD0000">'&lt;html&gt;&lt;head&gt;'</span><span style="color: #007700">;<br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span style="color: #DD0000">'&lt;title&gt;Info&lt;/title&gt;'</span><span style="color: #007700">;<br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span style="color: #DD0000">'&lt;/head&gt;&lt;body&gt;'</span><span style="color: #007700">;<br><br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//Infobox<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">echo&nbsp;</span><span style="color: #0000BB">&#036;login_info</span><span style="color: #007700">;<br><br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span style="color: #DD0000">'&lt;h1&gt;Datei&nbsp;gelöscht!&lt;/h1&gt;'</span><span style="color: #007700">;<br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span style="color: #DD0000">'&lt;/body&gt;&lt;/html&gt;'</span><span style="color: #007700">;<br></span><span style="color: #0000BB">?&gt;<br></span><br></span></code> 
EOD;

$sitecontent->add_site_content( '<h4>Beispiel</h4>' );
$sitecontent->add_site_content( $bsp  );

?>
