$(function () {
	$( "input[name=captcha_code]" ).blur( checkcaptcha );
	$( "td#captchatd" ).css( "width", $( "i#pwtext" ).css("width") );
});

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

		$.get( siteurl + "/ajax.php?addon=felogin&user=" + userinput , function( data ) {
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
	$.get( siteurl + "/ajax.php?addon=felogin&mail=" + valmail , function( data ) {
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
	$.get( siteurl + "/ajax.php?addon=felogin&code=" + valcode , function( data ) {
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
	var captchainput = $( "input[name=captcha_code]" ).val();
	if( "" != captchainput ){

		$( "input#checkc" ).val( "nok" );
		$("i#captchatext").text("Captcha -- Überprüfung läuft");
		$("i#captchatext").css( "background-color", "orange" );
		$("i#captchatext").css( "color", "white" );
		$("i#captchatext").css( "padding", "5px" );

		$.get( siteurl + "/ajax.php?addon=felogin&captcha_code=" + captchainput , function( data ) {
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
		$( "input#checkc" ).val( "nok" );
		$("i#captchatext").text( "Captcha -- bitte eintragen" );
		$("i#captchatext").css( "color", "black" );
		$("i#captchatext").css( "background-color", "white" );
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
