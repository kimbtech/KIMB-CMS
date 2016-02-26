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
		$("i#pwtext").text( passok );
		$("i#pwtext").css( "background-color", "green" );
		$("i#pwtext").css( "color", "white" );
		$("i#pwtext").css( "padding", "5px" );
	}
}

function checkmail(){
	var valmail = $( "input#mail" ).val();
	var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

	if( mailmatch.test( valmail ) ){
		$("i#mailadr").text( mailprue );
		$("i#mailadr").css( "background-color", "orange" );
		$("i#mailadr").css( "color", "white" );
		$("i#mailadr").css( "padding", "5px" );
				
		$( "div#mailcheck" ).css( "display", "block" );
			
	}
	else{
		$("i#mailadr").text( mailerr );
		$("i#mailadr").css( "background-color", "red" );
		$("i#mailadr").css( "color", "white" );
		$("i#mailadr").css( "padding", "5px" );
	}
}
		
function sendcode(){
	var valmail = $( "input#mail" ).val();
	$.get( siteurl + "/ajax.php?addon=felogin&mail=" + valmail + "&lang=" + langfile, function( data ) {
		if( data == "ok" ){
			$("i#mailadr").html( codese );
		}
		else{
			$("i#mailadr").text( codeanzb );
			$("i#mailadr").css( "background-color", "red" );
		}
	});
}

function checkcode(){
	var valcode =  encodeURIComponent( $( "input#mailcode" ).val() );
	$.get( siteurl + "/ajax.php?addon=felogin&code=" + valcode , function( data ) {
		if( data == "ok" ){
			$("i#mailadr").text( mailok );
			$("i#mailadr").css( "background-color", "green" );
			$( "input#checkm" ).val( "ok" );
					
			$("span#codeokay" ).text( codeok );
			$("span#codeokay").css( "background-color", "green" );
			$("span#codeokay").css( "color", "white" );
			$("span#codeokay").css( "padding", "5px" );

		}
		else{
			$("i#mailadr").text( codeerr );
			$("i#mailadr").css( "background-color", "red" );
		}
	});
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
	else if( valeins != "" ){
		$( "input#passwort1" ).val( SHA1( newsalt + valeins ) );
		$( "input#passwort2" ).val( '' );
		return true;
	}
	else{
		return true;
	}
}	