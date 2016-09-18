//wenn Seite breit
$( function () {
	//Hauptstruktur machen
	makestructur();

	//Seite laden
	loadsite();

});
//Hauptstruktur der Elemente meken
var structur = {
	main : "div#addon_survey_main"
}
//AJAX global
var ajaxpost = {
	data : {

	},
	task : 'umfrage',
	uid : addsur.uid
}

//Hauptstruktur machen
function makestructur(){

	//DIV Struktur
	var struc = '<div id="meldungen"></div>'
	+'<div id="navigation"></div>'
	+'<div id="fragen"></div>'
	+'<div id="auswertung"></div>';

	structur.fra = "div#fragen";
	structur.nav = "div#navigation";
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

	//alles außer NAV weg
	$( structur.fra ).css( 'display', 'none' );
	$( structur.aus ).css( 'display', 'none' ); 
}

//Seite laden
//	Navigation
function loadsite(){
	var html = '<button class="umf_nav_butt" todo="start">Umfrage starten</button>';
	html += '<button class="umf_nav_butt" todo="ergeb">Ergebnisse ansehen</button>';
	$( structur.nav ).html( html );

	//Rechte des Users prüfen
	//	Auswertung Zugriff?
	if( addsur.zugaus != "allowed" ){
		//Button weg
		$( "button.umf_nav_butt[todo=ergeb]" ).css( 'display', 'none');
		//Hinweis
		$( structur.nav ).append( '<span class="fakebutton">Kein Zugriff auf die Auswertung</span>' );
	}
	//	Umfrage Teilnahme erlaubt?
	if( addsur.zugriff != "allowed" ){
		//Button weg
		$( "button.umf_nav_butt[todo=start]" ).css( 'display', 'none');
		//Hinweis
		$( structur.nav ).prepend( '<span class="fakebutton">Keine Rechte zur Teilnahme</span>' );
	}

	//Buttons hören
	$( "button.umf_nav_butt" ).click( function (){
		//Beide Stellen verbergen
		$( structur.fra ).css( 'display', 'none' );
		$( structur.aus ).css( 'display', 'none' );
		//Buttons aktivieren
		$( "button.umf_nav_butt" ).each(function(k,v){
			$( this )[0].disabled = false;
		})
		//geklickten Button deaktivieren
		$( this )[0].disabled = true;
		//welcher?
		var todo = $( this ).attr( "todo" );
		if( todo == 'start' ){
			//allg AJAX
			ajaxpost.task = 'umfrage';
			//machen
			start_umf();
		} 
		else if( todo = 'ergeb' ){
			//allg AJAX
			ajaxpost.task = 'auswertung';
			//machen
			make_ergeb();
		}
	});
}

//Fehlermeldung
function errormessage( meldung ){
	$( structur.mel ).html( '<div class="surveyerrorbox"><p>'+meldung+'</p><p><button id="closeerrboxsur">Schließen</button></p></div>' );
	$( "button#closeerrboxsur" ).click( function(){
		$( structur.mel ).html( '' );
	});
}

/* ================================================================================================ */


//AJAX Abfrage und an Element
function ajaxrequest( elem, padd ){

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
				data = output.data;
					
				//Daten da?
				if( data != null ){

					//Daten an Element
					$( elem ).html( data );
				}
			}
		}
	});
}

/* ================================================================================================ */

function start_umf(){
	//Feld anzeigen
	$( structur.fra ).css( "display", "block" );
	//Feld in Areas teilen
	var html = '<div id="fragearea"></div>'+
	'<div id="fragenav"></div>';
	$( structur.fra ).html( html );
	//Struktur OBJ anpassen
	structur.franav = structur.fra + " div#fragenav";
	structur.fraarea = structur.fra + " div#fragearea";

	//AJAX Test
	ajaxrequest(  structur.fraarea, {dd:"ee"} );
}

/* ================================================================================================ */

//Statistik machen
function make_ergeb(){
	//Feld anzeigen
	$( structur.aus ).css( "display", "block" );

	$( structur.aus ).html('<canvas id="myChart" style="width:400px; height:400px;"></canvas>');

	var ctx = document.getElementById("myChart");

	var myChart = new Chart(ctx, {
		type: 'pie',
		data: {
			labels: [
				"Red",
				"Blue",
				"Yellow",
			],
			datasets: [
				{
					data: [300, 50, 100],
					backgroundColor: [
						"#FF6384",
						"#36A2EB",
						"#FFCE56",
					]
				}
			]
		}
	});
}

