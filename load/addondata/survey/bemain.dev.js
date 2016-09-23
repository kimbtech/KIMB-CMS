/*
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2016 by KIMB-technologies.eu
//https://www.KIMB-technologies.eu
//http://www.gnu.org/licenses/gpl-3.0
*/

//wenn Seite breit
$( function () {
	//Hauptstruktur machen
	makestructur();

	//Statistikt laden
	//	extra Datei (stat.min/dev.js)
	make_ergeb();

});
//Hauptstruktur der Elemente meken
var structur = {
	main : "div#addon_survey_main"
}
//AJAX global
var ajaxpost = {
	data : {

	},
	task : 'auswertung',
	uid : addsur.uid
}

//Hauptstruktur machen
function makestructur(){

	//DIV Struktur
	var struc = '<div id="meldungen"></div>'
	+'<div id="auswertung"></div>';

	structur.aus = "div#auswertung";
	structur.mel = "div#meldungen"

	//Loading GIF bei AJAX Anfragen zeigen
	//	jQuery GET und POST Event listen
	//HTML für Loader
	var loader = '<div id="daten_loading" style="display:none;">';
	loader += '<div style="position:fixed; top:0; left:0; z-index:50; background-color:gray; opacity:0.5; width:100%; height:100%;">';
	loader += '</div>';
	loader += '<div style="position:fixed; top:calc( 50% - 64px ); left:calc( 50% - 64px ); background-color:#5d7; border-radius:20px; padding:15px; z-index:51;">';
	loader += '<img src="'+addsur.su+'/load/system/spin_load.gif" title="Lädt!!" alt="Lädt!!">';
	loader += '</div>';
	loader += '</div>';
	//anfügen
	struc += loader;
	
	//Listen on AJAX Events
	$(document).bind("ajaxSend", function(){
		$("#daten_loading").show("highlight");
	}).bind("ajaxComplete", function(){
		$("#daten_loading").hide("highlight");
	});

	//alles auf Seite
	$( structur.main ).html( struc );
	$("#daten_loading").hide("highlight");
}

//Fehlermeldung
var errortimeout;
function errormessage( meldung ){
	//erstmal Timout weg
	clearTimeout( errortimeout );
	//Meldung
	$( structur.mel ).html( '<div class="surveyerrorbox"><p>'+meldung+'</p><p><button id="closeerrboxsur">Schließen</button></p></div>' );
	//Button Schließen
	$( "button#closeerrboxsur" ).click( function(){
		$( structur.mel ).html( '' );
	});
	//Tomout Schließen
	errortimeout = setTimeout(function(){
		$( structur.mel ).html( '' );
	}, 5000);
}

/* ================================================================================================ */


//AJAX Abfrage und an Element
//	padd = POST Data anfügen
//	elem = DOM-Element, welches Rückgabe HTML-erhält
//		oder
//		Callbackfunktion für Daten
function ajaxrequest( padd, elem ){

	//wenn Element geben, dann dort als HTML laden
	if( typeof elem === "function" ){
		var cb = true;
	}
	else{
		//Callbackfunktion
		var cb = false;
	}

	//POST fertig machen
	ajaxpost.data = padd;

	//AJAX Anfrage für Explorer Dateien
	$.post( addsur.su+"/ajax.php?addon=survey", ajaxpost ).always( function( output ) {

		//AJAX Daten okay?
		if(
			typeof output.data == "undefined"
			||
			typeof output.okay != "boolean"
			||
			typeof output.error != "boolean"
		){
			

			errormessage( "Der Sever antwortet nicht korrekt!" );
			
		}
		else{
			if( output.error || !output.okay ){
				errormessage( 'Es gab einen Fehler mit dem Request!' )
			}
			else{
				var data = output.data;
					
				//Daten da?
				if( data != null ){
					//Callbackfunktion?
					if( cb ){
						elem( data );
					}
					else{
						//Daten an Element
						$( elem ).html( data );
					}
				}
			}
		}
	});
}
