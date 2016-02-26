$(function() { 
	$("div#felogin").html( htmlcodeform );
});
function submitsys() {
	var user = $( "input[name=feloginuser]" ).val();
	var pass = $( "input[name=feloginpassw]" ).val();

	if( pass == "" || user == "" ){
		return false;
	}

	var salt = $( "span#passsalt" ).text().toString();

	if( salt == "none" ){
		return true;
	}
	else if( salt != "nok" ){
	
		var newpass = SHA1( SHA1( salt + pass ) + loginrandsalt );
		newpass = newpass.toString();
		$( "input[name=feloginpassw]" ).val( newpass );

		return true;
	}
	else{
		return true;
	}
}
function getsalt(){
	var user = $( "input[name=feloginuser]" ).val();

	$( "input[type=submit]" ).attr( "disabled", "disabled" );
	$( "img#loadergif" ).css( "display", "inline-block" );
	$.get( siteurl + "/ajax.php?addon=felogin&usersalt=" + user , function( data ) {
		$( "span#passsalt" ).text( data );
		$( "img#loadergif" ).css( "display", "none" );
		$( "input[type=submit]" ).removeAttr( "disabled" );
	});	
}
