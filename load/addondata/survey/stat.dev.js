/*
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2016 by KIMB-technologies.eu
//https://www.KIMB-technologies.eu
//http://www.gnu.org/licenses/gpl-3.0
*/

//Statistik machen
function make_ergeb(){
	//Feld anzeigen
	$( structur.aus ).css( "display", "block" );
	//Ergebnisse mit Fragen laden
	ajaxrequest( {}, draw_stat );

	//Ergebnisse JSON Log
	function logit(data){	
		$( structur.aus ).html( '<pre>' + JSON.stringify(data, null, 2) + '</pre>' );
	}

	//DOM vorbereiten
	var html = '<div id="graphnav"></div>'+ 
	'<div id="graphareagroup">'+
	'<div id="grapharea"></div>'+
	'<canvas id="auswertchart" style="width:100%;"></canvas>'+
	'</div>';
	$( structur.aus ).html( html );
	//Struktur OBJ anpassen
	structur.ausnav = structur.aus + " div#graphnav";
	structur.ausgrag = structur.aus + " div#graphareagroup";
	structur.ausgra = structur.ausgrag + " div#grapharea";
	structur.canv = structur.ausgrag + " canvas#auswertchart"

	

	function draw_stat( ergeb ){
		//Nur das OBJ mit den Ergebnissen
		ergeb = ergeb.ergebnisse;

		//Fragennavigation
		//	Home => 0
		var html = '<button class="grafrabutts" fra="0">Übersicht</button>';
		$.each( ergeb.data, function( k,v ){
			//Frage nach ID
			html += '<button class="grafrabutts" fra="'+k+'">Frage '+k+'</button>'
		});
		//	anfügen
		$( structur.ausnav ).html( html );

		//Klicks ....
		//	Übersicht Button deakt => als erstes Übersicht!
		$("button.grafrabutts[fra=0]")[0].disabled = true;
		make_uebersicht();
		//	auf Button Klicks hören
		$( "button.grafrabutts" ).click( function (){
			//Fragen ID holen
			var fra = $( this ).attr( "fra" );

			//alle Buttons klickbar machen
			$( "button.grafrabutts" ).each(function(){
				$( this )[0].disabled = false;
			});
			//ausgewählten wieder unklickbar
			$("button.grafrabutts[fra="+fra+"]")[0].disabled = true;

			//Übersicht?
			if( fra == "0" ){
				//machen
				make_uebersicht();
			}
			else{
				//machen (Graph)
				make_graph( fra );
			}
		});

		//Graphen einer Frage machen
		//	id der Frage
		function make_graph( id ){
			//Graph
			$( structur.ausgra ).html( '<pre>' + JSON.stringify(ergeb.data[id], null, 2) + '</pre>' );

			//Chart
			//	sichtbar
			$(structur.canv).css( 'display', 'block' );
			//	do
			var myChart = new Chart( $(structur.canv)[0] , {
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

		//Übersicht über alle machen
		function make_uebersicht(){

			//HTML
			var html = ergeb.teilnehmeranzahl+'<br />';
			html += ergeb.fragenanzahl+'<br />';
			html += ergeb.auswertungstyp+'<br />';
			
			//Liste der Teilnehmer wenn nach Namen

			//Übersicht
			$( structur.ausgra ).html( html );

			//kein Chart
			$(structur.canv).css( 'display', 'none' );

			//DATA Export ?

		}
	}

/*	var ctx = document.getElementById("myChart");

	
*/
}