//Inline Datei URI zu Blob Obj.
 function inline2Blob( inline ) {
	var byte;
	if ( inline.split(',')[0].indexOf('base64') >= 0 ){
		byte = atob(
			inline.split(',')[1]
		);
	}
	else{
		byte = unescape(
			inline.split(',')[1]
		);
	}
	var mime = inline.split(',')[0].split(':')[1].split(';')[0];
	var array = new Uint8Array( byte.length );
	for (var i = 0; i < byte.length; i++) {
		array[i] = byte.charCodeAt( i );
	}

	return new Blob(
		[array],
		{type:mime}
	);
}

//Datei entschlüsseln (und holen)
function encrypt() {

	//Link zur Datei erstellen
	var link = window.location+"&raw";

	//Loader
	$( "img" ).css( "visibility", "visible" );
		
	//holen
	$.get( link, function ( data ){

		//Daten okay?
		if( data != "" ){

			//Passwort lesen
			var pass = $( "input#pass" ).val();

			try{
				//entschlüsseln
				var file = sjcl.decrypt( pass, data );

				//Datei speichern
				saveAs( inline2Blob( file ) , filename );

				$( "img" ).css( "visibility", "hidden" );

			}
			catch( e ){
				$( "img" ).css( "visibility", "hidden" );
				alert( 'Konnte die Datei nicht entschlüsseln:\r\n(' + e.message + ')' );
			}				
		}
		else{
			//Fehlermeldung
			alert( "Fehler beim Laden der Datei!" );
		}
	});
}

$( function () {

	$( "input#pass" ).keyup( function( event ) {
		//Enter?
		if(event.keyCode == 13){
			encrypt();
		}
	});

	$( "button#dec" ).click( encrypt );
});