//wenn Seite breit
$( function () {
	//Hauptstruktur machen
	makestructur();

	//Seite laden
	loadsite();

	//AJAX Test
	ajaxrequest( {}, structur.fra );
	//Auswertung Test
	stat();

});
//Hauptstruktur der Elemente meken
var structur = {
	main : "div#addon_survey_main"
}

//Hauptstruktur machen
function makestructur(){

	//DIV Struktur
	var struc = '<div id="fragen"></div>'
	+'<div id="navigation"></div>'
	+'<div id="auswertung"></div>';

	structur.fra = "div#fragen";
	structur.nav = "div#navigation";
	structur.aus = "div#auswertung";

	//Loading GIF bei AJAX Anfragen zeigen
	//	jQuery GET und POST Event listen
	//HTML für Loader
	var loader = '<div id="daten_loading"  style="display:none;">';
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
	var html = '<button>Umfrage starten<button>'
	html 
	$( structur.nav ).html( html );
}


/* ================================================================================================ */


//AJAX Abfrage und an Element
function ajaxrequest( data, elem ){

	//AJAX Anfrage für Explorer Dateien
	$.post( addsur.su+"/ajax.php?addon=survey", data ).always( function( data ) {
		//Daten an Element
		$( elem ).html( data );
	});
}

//Statistik machen
function stat(){
	
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

