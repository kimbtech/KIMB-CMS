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
		html += '<br /><br />';
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

			//Daten dieser Frage
			var thiserg = ergeb.data[id];

			//HTML
			var html = '<h2>Frage '+id+'</h2>';
			html += '<button id="makejsonexp" style="float:right;">JSON Export (Frage '+id+')</button>';
			html += '<h4>Fragestellung</h4>';
			html += '<div><p>'+thiserg.text+'</p></div>';
			html += '<h4>Antworten</h4>';
			//	anfügen
			$( structur.ausgra ).html( html );

			//auf Exportbutton hören
			$( "button#makejsonexp" ).click( function() {
				//Var vorbereiten
				var exportd = thiserg;
				//User anhängen?
				if( ergeb.auswertungstyp == 'na' ){
					exportd['teilnehmerliste'] = ergeb.teilnehmerliste;
				};
				//Export
				dataexport( exportd );
			});

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
			var html = '<h2>Übersicht</h2>';
			html += '<table id="uebertable">';
			html += '<tr>';
			html += '<th>Teilnehmer</th>';
			html += '<td>'+ergeb.teilnehmeranzahl+'</td>';
			html += '</tr>';
			//Liste der Teilnehmer wenn nach Namen
			if( ergeb.auswertungstyp == 'na' ){
				html += '<tr>';
				html += '<td colspan="2"><ul>';
				$.each( ergeb.teilnehmerliste, function (k,v){
					html += '<li>'+v+'</li>';
				});
				html += '</ul></td>';
				html += '</tr>';
			}
			html += '<tr>';
			html += '<th>Fragen</th>';
			html += '<td>'+ergeb.fragenanzahl+'</td>';
			html += '</tr>';
			html += '<tr>';
			html += '<th>Typ</th>';
			html += '<td>'+( ergeb.auswertungstyp == 'na' ? 'Name' : 'Anonym' )+'</td>';
			html += '</tr>';
			html += '</table>';

			//Export?
			html += '<button id="makejsonexp">JSON Export (alles)</button>';

			//Übersicht
			$( structur.ausgra ).html( html );

			//Tabelle Design
			$( "table#uebertable, table#uebertable td, table#uebertable th" ).css({
				'width' : '100%',
				'max-width' : '500px',
				'border' : '1px solid black',
				'border-collapse' : 'collapse',
				'padding' : '5px'
			});

			//kein Chart
			$(structur.canv).css( 'display', 'none' );

			//auf Exportbutton hören
			$( "button#makejsonexp" ).click( function() {

				//Eyport
				dataexport( ergeb );
				
			});
		}

		//JSON in Exportkasten
		//	data => JS OBJ
		function dataexport( data ){

			//Kasten bauen
			$( "body" ).append(
				'<div id="mainexportbox">'+
					'<div style="position:fixed; top:0; left:0; background-color:black; opacity:0.6; width:100%; height:100%;"></div>'+
					'<div style="position:absolute; top:5px; border-radius:5px; left:calc(50% - 300px); background-color:#5d7; padding:5px; width:600px;">'+
						'<div><button style="width:100%;">Schließen</button></div>'+
						'<div class="inner">'+
							'<pre>'+
								'<code class="language-json">'+
									JSON.stringify( data , null, 2)+
								'</code>'+
							'</pre>'+
						'</div>'+			
						'<div><button style="width:100%;">Schließen</button></div>'+	
					'</div>'+
				'</div>'			
			);
			//Prism
			Prism.highlightAll();

			//Button Schließen
			$( "div#mainexportbox div button").click( function (){
				//Kasten weg
				$( "div#mainexportbox" ).remove();
			});
		}
	}

}