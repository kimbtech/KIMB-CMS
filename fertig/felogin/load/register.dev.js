$(function () {
	$( "input[name=captcha_code]" ).blur( checkcaptcha );
	$( "td#captchatd" ).css( "width", $( "i#pwtext" ).css("width") );
});

function checkpw() {
	var valeins = $( "input#passwort1" ).val();
	var valzwei = $( "input#passwort2" ).val();

	if( valzwei != valeins ){
		$("i#pwtext").text( passungl );
		$("i#pwtext").css( "background-color", "red" );
		$("i#pwtext").css( "color", "white" );
		$("i#pwtext").css( "padding", "5px" );
	}
	else{
		$("i#pwtext").text(passok);
		$("i#pwtext").css( "background-color", "green" );
		$("i#pwtext").css( "color", "white" );
		$("i#pwtext").css( "padding", "5px" );
	}
}

function checkmail(){
	var valmail = $( "input#mail" ).val();
	var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

	if( mailmatch.test( valmail ) ){
		$("i#mailtext").text( mailprue );
		$("i#mailtext").css( "background-color", "orange" );
		$("i#mailtext").css( "color", "white" );
		$("i#mailtext").css( "padding", "5px" );

		$("tr#mailcodeinput").css( "display", "table-row" );

	}
	else{
		$("i#mailtext").text( mailerr );
		$("i#mailtext").css( "background-color", "red" );
		$("i#mailtext").css( "color", "white" );
		$("i#mailtext").css( "padding", "5px" );
	}
}

function checkname(){
	var valname = $( "input#name" ).val();

	if( valname != "" ){
		$("i#nametext").text( nameok );
		$("i#nametext").css( "background-color", "green" );
		$("i#nametext").css( "color", "white" );
		$("i#nametext").css( "padding", "5px" );

	}
	else{
		$("i#nametext").text( nameempt );
		$("i#nametext").css( "background-color", "red" );
		$("i#nametext").css( "color", "white" );
		$("i#nametext").css( "padding", "5px" );
	}
}

function checkuser(){
	var userinput = $( "input#user" ).val();
	if( "" != userinput ){

		$( "input#checku" ).val( "nok" );
		$("i#usertext").text( userprue );
		$("i#usertext").css( "background-color", "orange" );
		$("i#usertext").css( "color", "white" );
		$("i#usertext").css( "padding", "5px" );

		$.get( siteurl + "/ajax.php?addon=felogin&user=" + userinput , function( data ) {
			if( data == "nok" ){
				$("i#usertext").text( userverg );
				$("i#usertext").css( "background-color", "red" );
			}
			else{
				$( "input#checku" ).val( "ok" );
				$("i#usertext").text( userok );
				$("i#usertext").css( "background-color", "green" );
			}
		});
	}
	else{
		$( "input#checku" ).val( "ok" );
		$("i#usertext").text( userok );
		$("i#usertext").css( "background-color", "green" );
	}
}

function sendcode(){
	var valmail = $( "input#mail" ).val();
	$.get( siteurl + "/ajax.php?addon=felogin&mail=" + valmail + "&lang=" + langfile, function( data ) {
		if( data == "ok" ){
			$("i#mailcodetext").html( codese );
			$("i#mailcodetext").css( "line-height", "25px" );
			$("i#mailcodetext").css( "background-color", "orange" );
			$("i#mailcodetext").css( "color", "white" );
			$("i#mailcodetext").css( "padding", "5px" );
			$("button#nochmalcode").css( "display", "table-cell" );
		}
		else{
			$("i#mailcodetext").text( codeanzb );
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
			$("i#mailcodetext").text( codeok );
			$("i#mailcodetext").css( "background-color", "green" );
			$("i#mailcodetext").css( "color", "white" );
			$("i#mailcodetext").css( "line-height", "25px" );
			$("i#mailcodetext").css( "padding", "5px" );
			$("button#nochmalcode").css( "display", "none" );
			$( "input#checkm" ).val( "ok" );
			$("i#mailtext").text( mailok );
			$("i#mailtext").css( "background-color", "green" );
		}
		else{
			$("i#mailcodetext").text( codeerr );
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
		$("i#captchatext").text( captchapr );
		$("i#captchatext").css( "background-color", "orange" );
		$("i#captchatext").css( "color", "white" );
		$("i#captchatext").css( "padding", "5px" );

		$.get( siteurl + "/ajax.php?addon=felogin&captcha_code=" + captchainput , function( data ) {
			if( data == "nok" ){
				$("i#captchatext").text( captchaerr );
				$("i#captchatext").css( "background-color", "red" );
			}
			else{
				$( "input#checkc" ).val( "ok" );
				$("i#captchatext").text( captchaok );
				$("i#captchatext").css( "background-color", "green" );
			}
		});
	}
	else{
		$( "input#checkc" ).val( "nok" );
		$("i#captchatext").text( captchaeint );
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
		$( "input#passwort1" ).val( SHA1( newsalt + valeins ) );
		$( "input#passwort2" ).val( " " );
		return true;
	}
}
