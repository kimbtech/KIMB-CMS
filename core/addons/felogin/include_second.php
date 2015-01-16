<?php

defined('KIMB_CMS') or die('No clean Request');

//register (captcha, mailcheck), usereinstellungen, pw-forgot,

$felogin['userfile'] = new KIMBdbf( 'addon/felogin__user.kimb'  );

if( $_GET['id'] == $felogin['requid'] && ( isset( $_GET['pwforg'] ) || isset( $_GET['register'] ) || isset( $_GET['settings'] ) ) ){

	$sitecontent->add_site_content( '<hr id="goto"/>');

	if( isset( $_GET['settings'] ) && $felogin['loginokay'] == $_SESSION['felogin']['loginokay'] && isset( $_SESSION['felogin']['user'] ) ){

		$sitecontent->add_site_content( '<h2>Usereinstellungen</h2>');

		if( isset( $_POST['user'] ) ){

			$id = $felogin['userfile']->search_kimb_xxxid( $_SESSION['felogin']['user'] , 'user' );

			if( !empty( $_POST['name'] ) && !empty( $_POST['mail'] ) ){
				if( $_POST['mail'] != $felogin['userfile']->read_kimb_id( $id , 'mail' ) ){
					if( filter_var( $_POST['mail'] , FILTER_VALIDATE_EMAIL) ){
						if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'mail' , $_POST['mail'] ) ){
							$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Die E-Mail-Adresse wurde geändert!</div>');
						}
					}
					else{
						$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Bitte geben Sie eine korrekte E-Mail-Adresse an!</div>');
					}
				}
				if( $_POST['name'] != $felogin['userfile']->read_kimb_id( $id , 'name' ) ){
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'name' , $_POST['name'] ) ){
						$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Der Name wurde geändert!</div>');
					}
				}
				if( !empty( $_POST['passwort1'] ) ){
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , $_POST['passwort1'] ) ){
						$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Das Passwort wurde geändert!</div>');
					}
				}
			}
			else{
				$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Bitte Füllen Sie den Namen und die E-Mail-Adresse!</div>');
			}
		}
		
		$sitecontent->add_html_header('<script>
		function checkpw() {
			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();

			if( valzwei != valeins ){
				$("i#pwtext").text("Passwörter stimmen nicht überein!");
				$("i#pwtext").css( "background-color", "red" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
			else{
				$("i#pwtext").text("Passwörter - OK");
				$("i#pwtext").css( "background-color", "green" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
		}

		function checkmail(){
			var valmail = $( "input#mail" ).val();
			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

			if( mailmatch.test( valmail ) ){
				$("i#mailadr").text( "E-Mail Adresse - OK" );
				$("i#mailadr").css( "background-color", "green" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
			
			}
			else{
				$("i#mailadr").text( "Die E-Mail Adresse ist fehlerhaft!" );
				$("i#mailadr").css( "background-color", "red" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
			}
		}

		function checksumbit(){

			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();
			var valmail = $( "input#mail" ).val();

			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
			if( mailmatch.test( valmail ) == false ){
				return false;
			}

			if( valzwei != valeins ){ 
				return false;
			}
			else{
				$( "input#passwort1" ).val( SHA1( valeins ) );
				$( "input#passwort2" ).val( \'\' );
			}

		}	
		</script>');

		$id = $felogin['userfile']->search_kimb_xxxid( $_SESSION['felogin']['user'] , 'user' );
		if( $id != false ){
			$user = $felogin['userfile']->read_kimb_id( $id );

			$sitecontent->add_site_content('<form action="" method="post" onsubmit="return checksumbit();" ><br />');
			$sitecontent->add_site_content('<input type="text" name="user" readonly="readonly" value="'.$user['user'].'" > <i>Username</i><br />');
			$sitecontent->add_site_content('<input type="text" name="name" value="'.$user['name'].'"> <i>Name</i><br />');
			$sitecontent->add_site_content('<input type="text" name="mail" id="mail" onchange=" checkmail(); " value="'.$user['mail'].'" > <i id="mailadr">E-Mail-Adresse - keine Änderung</i><br />');
			$sitecontent->add_site_content('<input type="text" name="gruppr" readonly="readonly" value="'.$user['gruppe'].'"><i>Gruppe</i><br />');
			$sitecontent->add_site_content('<input type="password" name="passwort1" id="passwort1" onchange=" checkpw(); "> <i id="pwtext">Passwort - keine Änderung</i> <br />');
			$sitecontent->add_site_content('<input type="password" name="passwort2" id="passwort2" onchange=" checkpw(); "> <i id="pwtext">Passwort - keine Änderung</i> <br />');
			$sitecontent->add_site_content('<input type="submit" value="Ändern" ><br />');
			$sitecontent->add_site_content('</form>');

		}
		else{
			$sitecontent->add_site_content( 'Der User existiert nicht!');
		}

	}
	elseif( isset( $_GET['pwforg'] ) ){

		if( isset( $_POST['user'] ) || isset( $_POST['mail'] ) ){

			if( empty( $_POST['user'] ) && !empty( $_POST['mail'] ) ){
				$id = $felogin['userfile']->search_kimb_xxxid( $_POST['mail'] , 'mail' );
				$ok = 'ok';

			}
			elseif( !empty( $_POST['user'] ) && empty( $_POST['mail'] )){
				$id = $felogin['userfile']->search_kimb_xxxid( $_POST['user'] , 'user' );
				$ok = 'ok';

			}
			else{
				$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Bitte füllen Sie ein Feld!</div>');
				$ok = 'nok';
			}

			if( $ok == 'ok' ){
				if( $id != false ){
					$newpass = makepassw( 10 );
					if( $felogin['userfile']->write_kimb_id( $id , 'add' , 'passw' , sha1( $newpass ) ) ){

						$inhalt .= 'Hallo '.$felogin['userfile']->read_kimb_id( $id , 'name' ).','."\r\n";
						$inhalt .= 'ihr neues Passwort lautet: '.$newpass."\r\n";
						$inhalt .= 'Gleich einloggen: '.$allgsysconf['siteurl'].'/index.php?id='.$felogin['requid']."\r\n\r\n";
						$inhalt .= $allgsysconf['sitename'];

						if( send_mail( $felogin['userfile']->read_kimb_id( $id , 'mail' ) , $inhalt ) ){
							$sitecontent->add_site_content( '<div style="color:red; font-size:20px;">Gefunden - Bitte schauen Sie nach Ihren E-Mails!</div>');
						}
					}
				}
			}

		}

		$sitecontent->add_site_content('<h2>Passwort vergessen?!</h2>');
		$sitecontent->add_site_content('Hier können Sie sich ein neues Passwort zusenden lassen! Geben Sie Ihre E-Mail-Adresse oder Ihren Usernamen an und wir schicken Ihnen Ihr neues Passwort!<br />');
		$sitecontent->add_site_content('<form action="" method="post" >');
		$sitecontent->add_site_content('<input type="text" name="mail" placeholder="E-Mail-Adresse" ><!--[if lt IE 10]> ( E-Mail-Adresse ) <![endif]-->');
		$sitecontent->add_site_content('<input type="text" name="user" placeholder="Username" ><!--[if lt IE 10]> ( Username ) <![endif]--><br />');
		$sitecontent->add_site_content('<input type="submit" value="Neues Passwort zusenden!" ><br />');
		$sitecontent->add_site_content('</form>');




	}
	elseif( isset( $_GET['register'] ) && $felogin['selfreg'] == 'on' ){


$sitecontent->add_html_header('<script>
		function checkpw() {
			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();

			if( valzwei != valeins ){
				$("i#pwtext").text("Passwörter stimmen nicht überein!");
				$("i#pwtext").css( "background-color", "red" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
			else{
				$("i#pwtext").text("Passwörter - OK");
				$("i#pwtext").css( "background-color", "green" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
		}

		function checkmail(){
			var valmail = $( "input#mail" ).val();
			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

			if( mailmatch.test( valmail ) ){
				$("i#mailtext").text( "E-Mail Adresse - Überprüfung ausstehend" );
				$("i#mailtext").css( "background-color", "orange" );
				$("i#mailtext").css( "color", "white" );
				$("i#mailtext").css( "padding", "5px" );

				$("tr#mailcodeinput").css( "display", "table-row" );
			
			}
			else{
				$("i#mailtext").text( "Die E-Mail Adresse ist fehlerhaft!" );
				$("i#mailtext").css( "background-color", "red" );
				$("i#mailtext").css( "color", "white" );
				$("i#mailtext").css( "padding", "5px" );
			}
		}

		function checkname(){
			var valname = $( "input#name" ).val();
			
			if( valname != "" ){
				$("i#nametext").text( "Name - OK" );
				$("i#nametext").css( "background-color", "green" );
				$("i#nametext").css( "color", "white" );
				$("i#nametext").css( "padding", "5px" );
			
			}
			else{
				$("i#nametext").text( "Der Name darf nicht leer sein!" );
				$("i#nametext").css( "background-color", "red" );
				$("i#nametext").css( "color", "white" );
				$("i#nametext").css( "padding", "5px" );
			}
		}

		function checkuser(){
			var userinput = $( "input#user" ).val();
			if( "" != userinput ){

				$( "input#checku" ).val( "nok" );
				$("i#usertext").text("Username -- Überprüfung läuft");
				$("i#usertext").css( "background-color", "orange" );
				$("i#usertext").css( "color", "white" );
				$("i#usertext").css( "padding", "5px" );

				$.get( "'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&user=" + userinput , function( data ) {
					if( data == "nok" ){
						$("i#usertext").text("Username - Achtung, dieser Username ist schon vergeben!!");
						$("i#usertext").css( "background-color", "red" );
					}
					else{
						$( "input#checku" ).val( "ok" );
						$("i#usertext").text("Username - OK");
						$("i#usertext").css( "background-color", "green" );
					}
				});
			}
			else{
				$( "input#checku" ).val( "ok" );
				$("i#usertext").text("(Username -- OK)");
				$("i#usertext").css( "background-color", "green" );
			}
		}

		function sendcode(){
			var valmail = $( "input#mail" ).val();
			$.get( "'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&mail=" + valmail , function( data ) {
				if( data == "ok" ){
					$("i#mailcodetext").html( "Code versandt!" );
					$("i#mailcodetext").css( "line-height", "25px" );
					$("i#mailcodetext").css( "background-color", "orange" );
					$("i#mailcodetext").css( "color", "white" );
					$("i#mailcodetext").css( "padding", "5px" );
					$("button#nochmalcode").css( "display", "table-cell" );
				}
				else{
					$("i#mailcodetext").text(" Die Anzahl an Versuchen ist beschränkt! ");
					$("i#mailcodetext").css( "background-color", "red" );
					$("i#mailcodetext").css( "line-height", "25px" );
					$("i#mailcodetext").css( "color", "white" );
					$("i#mailcodetext").css( "padding", "5px" );
				}
			});
		}

		function checkcode(){
			var valcode =  encodeURIComponent( $( "input#mailcode" ).val() );
			$.get( "'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&code=" + valcode , function( data ) {
				if( data == "ok" ){
					$("i#mailcodetext").text( "Code - OK" );
					$("i#mailcodetext").css( "background-color", "green" );
					$("i#mailcodetext").css( "color", "white" );
					$("i#mailcodetext").css( "line-height", "25px" );
					$("i#mailcodetext").css( "padding", "5px" );
					$("button#nochmalcode").css( "display", "none" );
					$( "input#checkm" ).val( "ok" );
					$("i#mailtext").text( "E-Mail Adresse - OK" );
					$("i#mailtext").css( "background-color", "green" );
				}
				else{
					$("i#mailcodetext").text( "Code fehlerhaft" );
					$("i#mailcodetext").css( "background-color", "red" );
					$("i#mailcodetext").css( "line-height", "25px" );
					$("i#mailcodetext").css( "color", "white" );
					$("i#mailcodetext").css( "padding", "5px" );
					$("button#nochmalcode").css( "display", "table-cell" );
				}
			});
		}

		function checkcaptcha(){
			var captchainput = $( "input#captcha" ).val();
			if( "" != captchainput ){

				$( "input#checkc" ).val( "nok" );
				$("i#captchatext").text("Captcha -- Überprüfung läuft");
				$("i#captchatext").css( "background-color", "orange" );
				$("i#captchatext").css( "color", "white" );
				$("i#captchatext").css( "padding", "5px" );

				$.get( "'.$allgsysconf['siteurl'].'/ajax.php?addon=felogin&captcha=" + captchainput , function( data ) {
					if( data == "nok" ){
						$("i#captchatext").text( "Captcha - Achtung, Ihre Eingabe ist falsch!!" );
						$("i#captchatext").css( "background-color", "red" );
					}
					else{
						$( "input#checkc" ).val( "ok" );
						$("i#captchatext").text("Captcha - OK");
						$("i#captchatext").css( "background-color", "green" );
					}
				});
			}
			else{
				$( "input#checkc" ).val( "ok" );
				$("i#captchatext").text("(Captcha -- OK)");
				$("i#captchatext").css( "background-color", "green" );
			}
		}

		function checksubmit(){

			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();
			var valmail = $( "input#mail" ).val();
			var valchecku = $( "input#checku" ).val();
			var valcheckc = $( "input#checkc" ).val();
			var valcheckm = $( "input#checkm" ).val();
			var valname = $( "input#name" ).val();
			var valakzep = $( "input#akzep" ).val();

			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
			if( mailmatch.test( valmail ) == false ){
				return false;
			}
			else if( valzwei != valeins ){ 
				return false;
			}
			else if( valeins == "" ){
				return false;
			}
			else if( valchecku == "nok" ){
				return false;
			}
			else if( valcheckc == "nok" ){
				return false;
			}
			else if( valcheckm == "nok" ){
				return false;
			}
			else if( valchecku == "nok" ){
				return false;
			}
			else if( valname == "" ){
				return false;
			}
			else if( !$( "input#akzep" ).is( ":checked" ) ){
				return false;
			}
			else{
				$( "input#passwort1" ).val( SHA1( valeins ) );
				$( "input#passwort2" ).val( " " );
				return true;
			}
		}
		</script>');

		$sitecontent->add_site_content('<h2>Account anlegen?!</h2>'."\r\n");
		$sitecontent->add_site_content('Hier können Sie sich einen Account anlegen!<br /><br />'."\r\n");
		$sitecontent->add_site_content('<form action="" method="post" onsubmit="return checksubmit(); " >'."\r\n");
		$sitecontent->add_site_content('<table width="100%;">'."\r\n");
		$sitecontent->add_site_content('<tr><td><input type="text" name="user" id="user" placeholder="Username" onchange=" checkuser(); " ></td> <td colspan="2"><i id="usertext">Username -- bitte eintragen</i></td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr><td><input type="text" name="name" id="name" placeholder="Name" onchange=" checkname(); "></td> <td colspan="2" ><i id="nametext">Name -- bitte eintragen</i></td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr><td><input type="text" name="mail" id="mail" placeholder="E-Mail-Adresse" onchange=" checkmail(); " ></td> <td colspan="2" ><i id="mailtext">E-Mail-Adresse -- bitte eintragen</i></td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr style="display:none;" id="mailcodeinput" ><td><input type="text" name="mailcode" id="mailcode" placeholder="E-Mail-Code" onchange=" checkcode(); " ></td> <td style="min-width:120px;"><i id="mailcodetext"><button onclick=" sendcode(); ">Verifizierungscode an Ihre E-Mail-Adresse senden</button></i><button style="display:none" id="nochmalcode" onclick=" sendcode(); ">Nochmal versuchen?!</button></td> <td>( Wir senden Ihnen einen Code an die angegebene E-Mail-Adresse und diese zu testen, bitte geben Sie den Code links ein! )</td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr><td><input type="password" name="passwort1" id="passwort1" placeholder="Passwort" onchange=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">Passwort -- bitte eintragen</i> </td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr><td><input type="password" name="passwort2" id="passwort2" placeholder="Passwort" onchange=" checkpw(); "></td> <td colspan="2" ><i id="pwtext">Passwort -- bitte eintragen</i> </td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr><td colspan="3"><input type="hidden" id="checku" value="nok" "><input type="hidden" id="checkc" value="nok" "><input type="hidden" id="checkm" value="nok"></td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr><td><img id="captcha" src="'.$allgsysconf['siteurl'].'/ajax.php?addon=captcha" id="captcha" style="border:none;" /></td> <td><a href="#" onclick=" document.getElementById( \'captcha\' ).src = \''.$allgsysconf['siteurl'].'/ajax.php?addon=captcha&\' + Math.random(); return false;">Nicht lesbar?</a></td><td>( Bitte geben Sie die Buchstaben, die Sie oben sehen, in das Feld ein. Nur so können wir sicherstellen, dass nur Menschen einen Account bekommen. )</td></tr>'."\r\n");
		$sitecontent->add_site_content('<tr><td><input name="captcha" id="captcha" autocomplete="off" placeholder="Code" onchange=" checkcaptcha(); " type="text" ></td> <td colspan="2" ><i id="captchatext">Ich bin kein Roboter - Captcha</i></td></tr>'."\r\n");
		$sitecontent->add_site_content('</table>');

		$felogin['akzepttext'] = $felogin['conf']->read_kimb_one( 'akzepttext' );
		$sitecontent->add_site_content('<input type="checkbox" name="akzep" id="akzep" value="ok">'.$felogin['akzepttext'].'<br />');
				
		$sitecontent->add_site_content('<br /><input type="submit" value="Account erstellen!" ><br />');
		$sitecontent->add_site_content('</form>');

		//js checkemailcode
		//js check hacken
		//Formualarverarbeitung

	}

	$sitecontent->add_site_content( '<hr id="end"/>');

}

unset( $felogin );

?>
