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

/* ================================================================================================ */

//alle Ergebnisse
var ergebnisse = {};
//ID der aktuellen Frage
var thisfrage = 0;

//Array mit allen IDs der Fragen
//	0 => Übersicht, 1, 2, ... IDs der Frageb
var thisfragen = [0];
//aktueller Key im Array aller IDs der Fragen
var thisfrageid = 0;

//alle Fragen
var fragendata = {};

//schon Ergebnisse im LocalStroage?
if( localStorage.getItem( "survey_"+addsur.uid+"_ergebnisse" ) != null ){
	//Ergebnis laden
	ergebnisse = JSON.parse( localStorage.getItem( "survey_"+addsur.uid+"_ergebnisse" ) );
} 

//Umfrge anfangen
function start_umf(){
	//Feld anzeigen
	$( structur.fra ).css( "display", "block" );
	//Feld in Areas teilen
	var html = '<div id="fragearea"></div>'+
	'<hr />'+
	'<div id="fragenav"></div>';
	$( structur.fra ).html( html );
	//Struktur OBJ anpassen
	structur.franav = structur.fra + " div#fragenav";
	structur.fraarea = structur.fra + " div#fragearea";

	//AJAX Test
	ajaxrequest(  {action:"pull"}, umfragedo );
}

//Umfrage machen
function umfragedo( data ){

	//Daten global
	fragendata = data;
	//Fragennummern zu OBJ IDs - Array für Zuordnung
	thisfragen = [0].concat( Object.keys( fragendata.fragen ) );
	//immer mit ID 0 (Übersicht) beginnen
	thisfrageid = 0;

	var html = '<div class="infotext">'+data.about+'</div>'; 
	//Name abfragen?
	if( data.art == "na" ){
		//Name gewünscht
		html += '<div class="namefeld">'+
		'<p>Diese Umfrage ordnet die Ergebnisse Ihrem Namen zu.</p>'+
		'<p>Bitte geben Sie Ihren Namen an: <input type="text" placeholder="Name" id="umf_teilnehmername" value="'+addsur.username+'"></p>'
		'</div>';
	}
	else{
		//Anonym
		html += '<div class="namefeld">'+
		'<p>Dies ist eine anonyme Umfrage!</p>'+
		'</div>';

	}
	//Infotext/ Name
	$( structur.fraarea ).html( html );

	//Navigation
	$( structur.franav ).html(
		'<button class="umfdobutt center" name="los">Los</button>'+
		'<span class="fragenzahl">'+thisfrageid+'/'+data.fragenzahl+'</span>'+
		'<div class="umfdobutt center"><button class="umfdobutt" name="back">&larr; Zurück</button>'+
		'<button class="umfdobutt" name="fwd">Weiter &rarr;</button></div>'+
		'<button class="umfdobutt center" name="end">Abschicken</button>'
	);
	//Zurück & Weiter disabled
	$( "div.umfdobutt.center button.umfdobutt" ).each(function(k,v){
		$( this )[0].disabled = true;
	});
	//Absenden disabled
	$( "button.umfdobutt[name=end]" )[0].disabled = true;

	//Buttons Listener
	$( "button.umfdobutt" ).unbind('click').click(function(){
		//welcher?
		var name = $( this ).attr( "name" );

		//versuche Auswahl zu speichern
		save_frage( name );
		
		//Los?
		if( name == "los" ){
			//Button Los anpassen
			$( "button.umfdobutt[name=los]" )[0].disabled = true;

			//Name holen?
			if( data.art == "na" ){
				var name = $( "input#umf_teilnehmername" ).val();
				if( name == "" ){
					//Fehler
					errormessage( 'Sie müssen eine Namen angeben!' );
					//neu
					start_umf();
				}
				else{
					//Namen übergeben
					ergebnisse['name'] = name;
				}
			}

			//Erste Frage
			//	FrageID muss 1 sein
			thisfrageid = 1;
			//	Frage laden
			load_frage( thisfragen[thisfrageid] );
		}
		//Weiter
		else if( name == "fwd" ){
			//nächste Frage
			thisfrageid++;
			load_frage( thisfragen[thisfrageid] );
		}
		//Zurück
		else if( name == "back" ){
			//nächste Frage
			thisfrageid--;
			load_frage( thisfragen[thisfrageid] );
		}
		//Absenden?
		else if( name == "end" ){
			//alle Fragen beantwortet
			if(
				Object.keys( ergebnisse ).length == fragendata.fragenzahl
				||
				(data.art == "na" && Object.keys( ergebnisse ).length == fragendata.fragenzahl + 1 )
			){

				//Ergebnisse löschen
				//	AJAX Callback
				function delall( data ){
					//okay?
					if( data.pushokay ){
						//alles auf null
						ergebnisse = {};
						addsur.zugriff = "notallowed";
						localStorage.removeItem( "survey_"+addsur.uid+"_ergebnisse" );
						fragendata = {};

						//Meldung
						var meld = "<p>Vielen Dank für die Teilnahme.</p>";
						meld += '<p><button id="closeumfrage">Schließen</button></p>';
						$( structur.fraarea ).html( meld );

						//Buttons anpassen
						$( "button.umfdobutt[name=back]" )[0].disabled = true;
						$( "button.umfdobutt[name=fwd]" )[0].disabled = true;
						$( "button.umfdobutt[name=los]" )[0].disabled = true;
						$( "button.umfdobutt[name=end]" )[0].disabled = true;

						//Button Schließen machen
						$( "button#closeumfrage").click( function (){
							//neu
							loadsite();
							//Fragen weg
							$( structur.fra ).css( 'display', 'none' );
						});
					}
					else{
						//Fehler
						errormessage("Konnte Ihre Ergebnisse nicht senden.");
					}
				}

				//ergebnisse an den Server senden
				ajaxrequest(  { action: "push", erg : ergebnisse }, delall );
			}
			else{
				//Fehler
				errormessage("Sie müssen alle Fragen beantworten, erst dann dürfen Sie absenden!");
			}
		}

		//Buttons anpassen
		$( "button.umfdobutt[name=fwd]" )[0].disabled = ( thisfrageid == fragendata.fragenzahl ? true : false );
		$( "button.umfdobutt[name=end]" )[0].disabled = ( thisfrageid == fragendata.fragenzahl ? false : true );
		$( "button.umfdobutt[name=back]" )[0].disabled = ( thisfrageid <= 1 ? true : false );

		//wenn alle Fragen beantwortet, Ende immer möglich
		//	nur wenn nicht letzte Frage (da auch sonst möglich)
		if( thisfrageid != fragendata.fragenzahl ){
			//Button an, wenn alle Ergebnisse okay
			$( "button.umfdobutt[name=end]" )[0].disabled = ( ( Object.keys( ergebnisse ).length == fragendata.fragenzahl || (fragendata.art == "na" && Object.keys( ergebnisse ).length == fragendata.fragenzahl + 1 ) ) ? false : true );
		}
	});

}

//Frage nach Nummer laden
function load_frage( nummer ){
	//Fragennummer setzen
	thisfrage = nummer;
	//Fragennummer
	$( structur.franav+" span.fragenzahl" ).text( thisfrageid+'/'+fragendata.fragenzahl );
	//Fragendaten
	var frage = fragendata.fragen[nummer];

	//HTML
	var html = '<div id="fragetext">';
	html += frage.frage;
	html += '</div>';

	//Zahl?
	if( frage.type == 'za'  ){
		//schon ein Ergebnis?
		if( typeof ergebnisse[nummer] !== "undefined" ){
			var erge = ergebnisse[nummer];
		}
		else{
			var erge = '';
		}
		
		html += '<div id="felder">';
		html += '<input type="number" min="'+frage.felder[1]+'" max="'+frage.felder[2]+'" class="savefeld" placeholder="Zahl" value="'+erge+'">';
		html += '<p>Geben Sie eine Zahl zwischen '+frage.felder[1]+' und '+frage.felder[2]+' ein.</p>';
		html += '</div>';
	}
	//Freitext?
	else if( frage.type == 'ft'  ){
		//schon ein Ergebnis?
		if( typeof ergebnisse[nummer] !== "undefined" ){
			var erge = ergebnisse[nummer];
		}
		else{
			var erge = '';
		}
		
		html += '<div id="felder">';
		html += '<textarea class="savefeld" style="width:90%; height:100px;">'+erge+'</textarea>'
		html += '<p>Geben Sie einen Text ein. (optional)</p>'
		html += '<p><input type="checkbox" id="freitextdeak"> Das Feld leer lassen.</p>'
		html += '</div>';
	}
	//Abstufung
	else if( frage.type == 'ab'  ){

		html += '<div id="felder">';
		html += '<table>';

		html += '<td width="50%;"></td>';
		html += '<td>Sehr gut</td>';
		html += '<td>Gut</td>';
		html += '<td>Befriedigend</td>';
		html += '<td>Ausreichend</td>';
		html += '<td>Mangelhaft</td>';
		html += '<td>Ungenügend</td>';
		html += '<td>Keine Angabe</td>';

		//Felder durchgehen
		$.each( frage.felder, function( k, v ){

			//alle leer
			//	nichts gewählt
			var hchecked ={
				1:"",
				2:"",
				3:"",
				4:"",
				5:"",
				6:"",
				ka:""
			}

			//schon ein Ergebnis?
			if( typeof ergebnisse[nummer] !== "undefined" ){
				//anpassen
				//	checked setzen
				hchecked[ergebnisse[nummer][k]] = 'checked="checked"';
			}

			html += '<tr>';
			html += '<td>'+v+'</td>';
			html += '<td><input type="radio" class="savefeld" name="'+k+'" value="1" '+hchecked[1]+'></td>';
			html += '<td><input type="radio" class="savefeld" name="'+k+'" value="2" '+hchecked[2]+'></td>';
			html += '<td><input type="radio" class="savefeld" name="'+k+'" value="3" '+hchecked[3]+'></td>';
			html += '<td><input type="radio" class="savefeld" name="'+k+'" value="4" '+hchecked[4]+'></td>';
			html += '<td><input type="radio" class="savefeld" name="'+k+'" value="5" '+hchecked[5]+'></td>';
			html += '<td><input type="radio" class="savefeld" name="'+k+'" value="6" '+hchecked[6]+'></td>';
			html += '<td><input type="radio" class="savefeld" name="'+k+'" value="ka" '+hchecked['ka']+'></td>';
			html += '</tr>';
		});

		html += '</table>';
		html += '<p>Bitte wählen Sie für jede Zeile ein Feld aus.</p>'
		html += '</div>';
	}
	//Auswahl oder Multiple Choice
	else{
		html += '<div id="felder">';
		html += '<ul style="list-style-type:none;">';

		//Attribute je nach Typ
		if( frage.type == 'mc' ){
			type = 'checkbox'
			name = 'mc[]'
		}
		else{
			type = 'radio'
			name = 'au'
		}

		//Felder durchgehen
		$.each( frage.felder, function( k, v ){

			//schon ein Ergebnis?
			if( typeof ergebnisse[nummer] !== "undefined" ){
				//holen
				var erge = ergebnisse[nummer];
				//Typ beachten
				if( frage.type == 'mc' ){
					//aktuelles Feld gewählt?
					if( erge.indexOf( k ) != -1 ){
						erge = 'checked="checked"'
					}
					else{
						erge = '';
					}

				}
				else{
					//aktuelles Feld gewählt?
					if( k == erge ){
						erge = 'checked="checked"'
					}
					else{
						erge = '';
					}
				}
			}
			else{
				var erge = '';
			}

			html += '<li>';
			//passendes Input
			html += '<input type='+type+' value="'+k+'" name="'+name+'" class="savefeld" '+erge+'>'+v;
			html += '</li>';
		});

		html += '</ul>';
		//Text je nach Type
		if( frage.type == 'mc' ){
			html += '<p>Wählen Sie passende Felder.</p>'
		}
		else{
			html += '<p>Wählen Sie genau ein Feld.</p>'
		}
		html += '</div>';

	}

	//an Seite anfügen
	$( structur.fraarea ).html( html );

	//Buttons Freitext
	if( frage.type == 'ft' ){
		
		//Keine Angabe (ka)?
		//	optionales Feld
		if( erge == "ka" ){
			//Textarea ausmachen
			$( "textarea.savefeld" )[0].disabled = true;
			//Input setzen
			$( "input#freitextdeak" )[0].checked = true;
		}

		//auf Auswahlbutton hören
		//	füllen oder nicht
		$( "input#freitextdeak" ).click(function(){
			//Textarea und Inhalt anpassen
			if($(this).is(':checked')){
				$( "textarea.savefeld" ).val( "ka" );
        				$( "textarea.savefeld" )[0].disabled = true;
    			}
			else {
				$( "textarea.savefeld" )[0].disabled = false;
				$( "textarea.savefeld" ).val( "" );
			}
		});
	}
}

//Frage Antwort sichern
function save_frage( butttask ){
	//nicht die Übersichtsseite
	if( thisfrageid !== 0 ){
		//Fragendaten
		var frage = fragendata.fragen[thisfrage];

		//noch kein Fehler
		var fehlermess = false;

		//Zahl oder Freitext?
		//Auswahl?
		if( frage.type == 'ft' || frage.type == 'za' || frage.type == 'au' ){
			if( frage.type == 'au' ){
				//Value lesen
				var inh = $(".savefeld:checked").val();
			}
			else{
				//Value lesen
				var inh = $(".savefeld").val();
			}
			//muss gefüllt sein!
			if( typeof inh != "undefined" && inh != "" ){
				//sichern
				ergebnisse[thisfrage] = inh;
			}
			else{
				//Fehler
				fehlermess = true;
			}
		}
		else if( frage.type == "mc" ){
			//Array zur Speicherung
			var saved = [];
			//alle aktivierten durchgehe
			$(".savefeld:checked").each( function (k, v){
				//key lesen
				var key = $(this).val();
				//an Array anfügen
				saved.push( key );
			});

			//Array leer?
			if( saved.length > 0 ){
				//nicht leer => okay
				ergebnisse[thisfrage] = saved;
			}
			else{
				//Fehler
				fehlermess = true;
			}

		}
		else if( frage.type == "ab" ){
			//OBJ zur Speicherung
			var saved = {};
			//alle angeklickten durchgehen
			$(".savefeld:checked").each( function (k, v){
				//augewählte Abstufung lesen
				var val = $(this).val();
				//Feld ID
        				var name = $( this ).attr( 'name' );
				//speichern
				saved[name] = val;

			});

			//In jeder Reihe einer gewählt?
			//	Anzahl der Werte stimmt überein?
			if( Object.keys( frage.felder ).length == Object.keys( saved ).length ){
				//alle ausgewählt => okay
				ergebnisse[thisfrage] = saved;
			}
			else{
				//Fehler
				fehlermess = true;
			}
		}

		//Fehlermeldung
		if( fehlermess ){
			errormessage( "Sie müssen etwas eingeben bzw. auswählen!" );
			//nochmal die gleiche Frage!
			if( butttask == 'fwd' ){
				thisfrageid--;
			}
			else if( butttask == 'back' ){
				thisfrageid++;
			}
		}

		//aktuelle Ergebnisse sichern (LocalStroage)
		localStorage.setItem( "survey_"+addsur.uid+"_ergebnisse", JSON.stringify( ergebnisse ) );
	}
}
 
