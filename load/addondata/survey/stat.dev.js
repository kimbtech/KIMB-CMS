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
	'<div id="freitextarea"></div>'+
	'</div>';
	$( structur.aus ).html( html );
	//Struktur OBJ anpassen
	structur.ausnav = structur.aus + " div#graphnav";
	structur.ausgrag = structur.aus + " div#graphareagroup";
	structur.ausgra = structur.ausgrag + " div#grapharea";
	structur.canv = structur.ausgrag + " canvas#auswertchart";
	structur.ausfrt = structur.ausgrag + " div#freitextarea";

	

	function draw_stat( ergeb ){
		//Nur das OBJ mit den Ergebnissen
		ergeb = ergeb.ergebnisse;

		//evtl. noch keine Ergebnisse?
		if( typeof ergeb == "undefined" ){
			//Fehlermeldung
			errormessage( "Es sind noch keine Ergebnisse vorhanden!" );
			$( structur.ausnav ).html( "<h3>Es sind noch keine Ergebnisse vorhanden!</h3>" );
			//beenden
			return false;
		}

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

		//Graph Var für chart.js
		var graph = null;

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

			//sinvolle Farben
			var colorindex = 'leer';
			function makecolor( rand ){
				//alle Farben
				var color = [
					'#FAA43A',
					'#F17CB0',
					'#5DA5DA',
					'#4D4D4D',
					'#B276B2',
					'#60BD68',
					'#F15854',
					'#B2912F',
					'#DECF3F'
				];
				//Zufallsfarbe ?
				if(
					( typeof rand !== "undefined" && rand )
					||
					colorindex == 'leer'
				){
					colorindex = Math.floor(Math.random() * ( color.length + 1));
				}

				//eine weiter
				colorindex++;

				//wieder von vorne ?
				if( colorindex > color.length ){
					//ja
					colorindex = 0;
				}
				//Farbe machen
				return color[colorindex];
			}

			//Chart
			//	Daten

			//OBJ machen
			var c = {
				type: '',
				labels: [],
				label: [],
				data: [],
				bgcolor: [],
				bocolor: [],
				options: {},
				use : true
			};

			//OBJ füllen
			//	Auswahl => Torte
			if( thiserg.type == 'au' ){
				//Torte
				c.type = 'pie';

				//allgemeine Randfarbe
				c.bocolor = makecolor( true );
				//alle Ergebnisse an Graphdata
				$.each( thiserg.ergebnisse, function (k,v){
					c.labels.push( k );
					c.data.push( v );
					c.bgcolor.push( makecolor() );
				});

				//Graph machen
				var graok = true;
			}
			//	Multiple Choice oder Zahl => Balken
			else if( thiserg.type == 'mc' || thiserg.type == 'za' ){
				//Zahl?
				if(thiserg.type == 'za'){
					//horizontale Balken
					c.type = 'horizontalBar';
					//Skala nur mit vollen Zahlen
					c.options = { scales: { xAxes: [{ ticks: { stepSize: 1, beginAtZero: true } }] }, legend: { display: false } };
				}
				else{
					//Balken
					c.type = 'bar';
					//Skala nur mit vollen Zahlen
					c.options = { scales: { yAxes: [{ ticks: { stepSize: 1, beginAtZero: true } }] }, legend: { display: false } };
				}

				//allgemeine Hintergrundfarbe
				c.bgcolor = makecolor( true );
				//Titel
				c.label = 'Anzahl';
				//alle Ergebnisse an Graphdata
				$.each( thiserg.ergebnisse, function (k,v){
					c.labels.push( k );
					c.data.push( v );
					c.bocolor.push(makecolor() );
				});

				//Graph machen
				var graok = true;
			}
			//	Abstufung => Balken
			else if( thiserg.type == 'ab' ){

				//Balken
				c.type = 'bar';
				//Skala nur mit vollen Zahlen
				c.options = { scales: { yAxes: [{ ticks: { stepSize: 1, beginAtZero: true } }] } };
				//eigenes Dataset
				c.use = false;

				//eigenes Dataset bauen
				var datasethere = {
					//Labels 1-6 und ka
					labels:[
						'Sehr gut',
						'Gut',
						'Befriedigend',
						'Ausreichend',
						'Mangelhaft',
						'Ungenügend',
						'Keine Angabe'
					],
					//Dataset
					datasets:[]
				};
				
				//alle Ergebnisse an Graphdata
				$.each( thiserg.ergebnisse, function (k,v){
					//Datensatz bauen
					var set = {
						data: [],
						label: k,
						backgroundColor: makecolor(),
						borderColor: makecolor(),
						borderWidth: 2
					};
					//alle Ergebnisse an Graphdata
					$.each( v , function (kk,vv){
						//Daten anfügen
						set.data.push( vv );
					});

					//Datensatz an Dataset
					datasethere.datasets.push( set );
				});

				//Graph machen
				var graok = true;


			}
			//	Freitext
			else{
				//DIV für Freitexte zeigen
				$( structur.ausfrt ).css( 'display', 'block' );

				//Übersicht
				var html = '<h5>Übersicht</h5>'+ 
				'<ul>'+
				'<li><b>Gesamtzahl der Teilnehmer:</b> '+ergeb.teilnehmeranzahl+'</li>'+
				'<li><b>Gesamtzahl der Texte:</b> '+thiserg.ergebnisse.anzahl+'</li>'+
				'<li><b>Anteil Texte:</b> '+ Math.round( ( thiserg.ergebnisse.anzahl/ergeb.teilnehmeranzahl ) * 100 ) + '%</li>'+
				'</ul>';

				//Texte
				html += '<h5>Texte</h5>';
				html += '<table id="textetable">';
				$.each( thiserg.ergebnisse.texte , function (k,v){
					//nach Name?
					//	Name zu Texte
					var name = ( ( ergeb.auswertungstyp == 'na' ) ? '' : k );
					//Tabellenzeile
					html += '<tr>';
					html += '<td class="first">'+name+'</td>';
					html += '<td><div>'+v.replace( /(\r\n)|(\r)|(\n)/g, '<br />\r\n' )+'</div></td>';
					html += '</tr>';
				});
				html += '</table>';

				//anzeigen
				$( structur.ausfrt ).html( html );

				//Tabelle Design
				$( "table#textetable, table#textetable td" ).css({
					'width' : '100%',
					'border' : '1px solid black',
					'border-collapse' : 'collapse',
					'padding' : '5px'
				});
				$( "table#textetable td.first" ).css({
					'width' : '20%',
					'min-width' : '100px'
				});
				$( "table#textetable td div" ).css({
					'background-color' : 'grey',
					'margin' : '5px',
					'padding' : '5px',
					'border-radius' : '5px'
				});

				//keinen Graph machen
				var graok = false;
			}

			//Graph?
			if( graok ){
				//die Werte aus dem OBJ c nutzen?
				if( c.use ){
					//Datensätze für den Graphen
					var datasets = {
						labels: c.labels,
						datasets: [
							{
								data: c.data,
								label: c.label,
								backgroundColor: c.bgcolor,
								borderColor: c.bocolor,
								borderWidth: 2
							}
						]
					}
				}
				else{
					var datasets = datasethere;
				}

				//DIV für Freitexte ausblenden
				$( structur.ausfrt ).css( 'display', 'none' );
				//sichtbar
				$(structur.canv).css( 'display', 'block' );
				//Graph evtl. nicht gelöscht?
				if( graph !== null ){
					graph.destroy();
				}
				//neu machen
				graph = new Chart( $(structur.canv)[0] , {
					type: c.type,
					data: datasets,
					options: c.options
				});
			}
			else{
				//nicht sichtbar
				$(structur.canv).css( 'display', 'none' );
			}
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
			//kein Freitext
			$( structur.ausfrt ).css( 'display', 'none' );

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