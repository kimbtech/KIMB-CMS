//KIMB-Tabelle
//	nächste ID
var table_next_id;

//auch ohne Passwort verschlüsseln, aber immer mit dem Gleichen
var global_table_passw = 'vqtxvpJJWiFJrNKcOQOjMgTHBxqGdiyuBhilfRktqfWLyHEw';
//Tabllendaten
var tabdata = JSON.parse( enctab );

//erstmal versuchen Tabelle alleine zu laden
//	(Standardpasswort)
$( function () {
	//Tabellendaten
	var data = tabdata;

	//Entschlüsselung versuchen
	try{
		//Daten mit SJCL entschlüsseln
		data = sjcl.decrypt( global_table_passw, data );

		//Tabelle anzeigen
		open_table( data );
	}
	//Fehler
	catch (e){
		//Passworteingabe anzeigen
		$( "div#passinput" ).css( "display", "block" );
		//Listener adden
		$( "div#passinput button" ).click( function () {
			start_table();
		});
		$( "div#passinput input" ).keyup( function( event ) {
			//Enter?
			if(event.keyCode == 13){
				start_table();
			}
		});
	}
} )

//per (Button Passworteingabe) starten
function start_table(){

	//Tabellendaten
	var data = tabdata;
	
	//Passwort lesen
	global_table_passw = $( "input#pass" ).val();

	//Entschlüsselung versuchen
	try{
		//Daten mit SJCL entschlüsseln
		data = sjcl.decrypt( global_table_passw, data );

		//Tabelle anzeigen
		open_table( data )
	}
	//Bei Fehler Fehlermeldung ausgeben
	catch (e){
		alert( 'Die Tabelle konnte mit diesem Passwort nicht entschlüsselt werden!\r\n(Fehlermeldung:"' + e.message + '")' );
		return;
	}
}

function open_table( data ){
					
	//Entschlüsselte Daten parsen
	data = JSON.parse( data );

	//NextID für Tabelle bestimmen
	table_next_id = data.nextid;
		
	//Tabellendaten lesen
	data = data.table;
	
	//Tabellengröße
	var html = '<table width="100%" border="border">';
		
	//alle Tabellendaten durchgehen	
	$.each( data, function( k,v ){
		
		//Reihe beginnen	
		html += '<tr>';
		
		//Index 0 ?	
		if( k == 0){
			//oberste Reihe
			//	Überschrift TH
			$.each( v, function( sk, sv ){
				//erstes Feld?
				if( sk == 0){
					//nicht zu bearbeiten
					html += '<th k="'+k+'" sk="'+sk+'">'+sv+'</th>';
				}
				else{
					//bearbeitbar
					html += '<th class="edit" k="'+k+'" sk="'+sk+'">'+sv+'</th>';
				}
			});
		}
		else{
			//Rest
			$.each( v, function( sk, sv ){
				//erste Spalte?
				if( sk == 0){
					//nicht zu bearbeiten
					html += '<td k="'+k+'" sk="'+sk+'">'+sv+'</td>';
				}	
				else{
					//bearbeitbar
					html += '<td class="edit" k="'+k+'" sk="'+sk+'">'+sv+'</td>';
				}
			});
		}
		
		//Reihe beenden	
		html += '</tr>';
		
	});
	
	//Tabelle beeden
	html += '</table>';
	html += '<button id="exp_json">JSON Export</button>';
	
	//Tabelle anzeigen
	$( 'div.tabelle' ).html( html );

	
	//JSON Export
	$( "button#exp_json ").unbind('click').click( function () {
		//Export durchführen
		make_json( data );	
	});
	
	return true;
}

//JSON Export
function make_json( data ){
	
	//JSON String aus Array erzeugen
	var file = JSON.stringify( { 'table': data, 'nextid': table_next_id } );
	
	//Popup mit Daten öffnen
	window.open( "data:text/json;utf-8," + file ,"_blank", "width=900px,height=500px,top=20px,left=20px,scrollbars=yes");
}

