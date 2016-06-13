//Fehler anzeigen
//	Titel, Inhalt, DOM Element für Fehler
function show_error( tit, mes, dom ){
	$( dom ).html( '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 5px;"><span class="ui-icon ui-icon-alert" style="display:inline-block;"></span><span style="margin-left:10px;"><strong>'+tit+'</strong>&nbsp;&nbsp;&nbsp;'+mes+'</span></div></div>' );
	 return true;
}

//HTML SpecialChars
function htmlspecialchars(str) {
	if (typeof(str) == "string") {
		str = str.replace(/&/g, "&amp;");
		str = str.replace(/"/g, "&quot;");
		str = str.replace(/'/g, "&#039;");
		str = str.replace(/</g, "&lt;");
		str = str.replace(/>/g, "&gt;");
	}
	return str;
 }

//Loading GIF bei AJAX Anfragen zeigen
//	jQuery GET und POST Event listen
$( function() {
	//HTML für Loader
	var loader = '<div id="daten_loading"  style="display:none;">';
	loader += '<div style="position:fixed; top:0; left:0; z-index:50; background-color:gray; opacity:0.5; width:100%; height:100%;">';
	loader += '</div>';
	loader += '<div style="position:fixed; top:calc( 50% - 64px ); left:calc( 50% - 64px ); background-color:#5d7; border-radius:20px; padding:15px; z-index:51;">';
	loader += '<img src="'+add_daten.siteurl+'/load/system/spin_load.gif" title="Lädt!!" alt="Lädt!!">';
	loader += '</div>';
	loader += '</div>';
	
	//HTML -> DOM
	$( 'body' ).append( loader );
	//geich ausblenden
	$("#daten_loading").hide("highlight");
	
	//Listen on AJAX Events
	$(document).bind("ajaxSend", function(){
		$("#daten_loading").show("highlight");
	}).bind("ajaxComplete", function(){
		$("#daten_loading").hide("highlight");
	});
	
});

//Dropzone.js
Dropzone.autoDiscover = false;

//Einfacher Dialog
function j_alert( meld, dw ){
	//wenn alter Dialog div noch im DOM diesen löschen
	
	//wenn Breite definiert
	if( typeof dw === 'undefined' ){
		dw = 300;
	}
	
	//DIV von früher noch da?
	if ($( "div.j_alert" ).length ){
		$( "div.j_alert" ).remove();
	}
						
	//HTML für neuen Dialog DIV		
	var dial = '<div class="j_alert" title="Wichtig!!">'
	dial += meld;
	dial += '</div>';
						
	//HTML dem DOM anfügen   
	$( "body" ).prepend( dial );
				
	//Dialog öffnen	 
	$( "div.j_alert" ).dialog({ 
		modal:true,
		width: dw,
		responsive: true,
		buttons:{
			"OK":function(){
				$( this ).dialog( 'close' );
			}
		}
	});
}

//Dateiverwaltung laden
$( function() {
	load_daten();	
});

//DIV CSS Klasse für Dateiverwaltung
var cssclass = "div.addon_daten_main ";

//System laden
function load_daten(){
	
	//Auswahlbuttons
	$( cssclass ).html( '<h2>Dateiverwaltung</h2>'+
		'<div id="firstbutt">'+
			'<input type="radio" name="firstbutt" id="my" checked="checked"><label for="my">Mein Verzeichnis</label>'+
			'<input type="radio" name="firstbutt" id="pu"><label for="pu">Für alle User</label>'+
			'<input type="radio" name="firstbutt" id="frei"><label for="frei">Freigaben</label>'+
		'</div>'+
		'<div class="main_files">Bitte wählen Sie einen Ort &uarr;</div>' );
	
	//Buttons machen
	$( "div#firstbutt" ).buttonset();
	
	//Klicks verarbeiten
	$( "#firstbutt input[type=radio]" ).click( function() {
		set_vars( $( this ).attr( 'id' ) );
	});
	
	//Kein Klick, also Standard oder nach URL ID (Hash)
	var id = window.location.hash;
	//keine Vorauswahl
	if( id == '' ){
		//My, Path /
		set_vars( 'my' );
	}
	else{
		//Parse Hash
		if( id.substr(0, 7) == '#user:/'){
			//User gewüncht, Ordner lesen
			set_vars( 'my', id.substr(7) );
		}
		else if( id.substr(0, 9) == '#public:/'){
			//Public gewüncht, Ordner lesen und firstbutt checked anpassen
			set_vars( 'pu', id.substr(9) );
			$( "#firstbutt input[type=radio]#pu" ).attr("checked","checked")
			$( "div#firstbutt" ).buttonset( "refresh" );
		}
		else if( id == "#freig://list" ){
			//Freigabe gewünscht
			set_vars( 'frei' );
		}
		else{
			//Hash fehlerhaft
			set_vars( 'my' );
		}
	}
}

//wichtige Werte für System
var allgvars = new Object;

//Klicks des Systems verarbeiten
function set_vars( ort, path  ){

	//Pfad evtl. gewählt?
	if( typeof path == "undefined" ){
		path = '/';
	}

	//Meine Dateien
	if( ort == 'my' ){
		allgvars.folder = 'user';
		allgvars.path = path;
		allgvars.proto = 'my:/'
		
		//Explorer öffnen
		main_explorer();  
	}
	//Öffentlich
	else if ( ort == 'pu' ){
		allgvars.folder = 'public';
		allgvars.path = path;
		allgvars.proto = 'pub:/'

		
		//Explorer öffnen
		main_explorer(); 
	}
	//Freigaben
	else if ( ort == 'frei' ){
		
		//Freigaben zeigen
		show_freigaben();
		
	}
	else{
		return false;
	}
}

//Explorer
function main_explorer(){
	
	//AJAX Anfrage für Explorer Dateien
	//	allgvars mit Gruppe, Pfad
	//	ToDo
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "filelist" } ).always( function( data ) {

			//aktuellen Pfad auch als ID in URL kenntlich machen
			window.location.hash = allgvars.folder + ":/" + allgvars.path ;
			
			//Toolszeige
			var html = '<div class="toolbar">'+
					'<button id="add_table">Neue Tabelle</button>'+
					'<button id="add_file">Neue Datei</button>'+
					'<button id="add_folder">Neuer Ordner</button>'+
					' &mdash; '+
					'<button id="zwisch">Zwischenablage</button>'+
					'<br />'+
					'<span class="input" style="display:none;">'+
						'<input type="text" id="new_name" placeholder="Name">'+
						'<small>Enter zum Speichern; Ctrl zum Abbrechen; Dateien gleichen Namens werden überschrieben</small>'+
					'</span>'+
				'</div>';
			
			html += '<div class="pathbarumg">';

			//Ein Ordner hoch Button
			html += '<button id="oneup"><span class="ui-icon ui-icon-arrowthick-1-w"></span></button>';

			//Pathanzeige Pfade anklickbar
			//	nötige vars
			var pathbardata = '';
			var path = '';
			//	einfach nach jeden Slash teilen
			var pathbardata_ar =  allgvars.path.split('/');
			//	alle Teile durchgehen und je in einen "span.pathslash" packen
			$.each( pathbardata_ar, function (k,v){
				if( v != '' ){
					path += '/'+v;		
					pathbardata += '</span>/<span class="pathslash" path="'+path+'/">'; 
					pathbardata += v;
				}
			});
			//	Anfang säubern
			pathbardata = pathbardata.substr( 7 );

			//Leer => Grundordner
			if( pathbardata == '' ){
				pathbardata = '/';
			}

			//Pathanzeige
			html += '<div class="pathbar">'+allgvars.proto+pathbardata+'</div>';

			html += '</div>';
			
			//nur Main nutzen (dev für Debugging)
			data = data.main;
			
			//Ordner existent?
			var folder_ex = data.folder_ex;
			
			//Dateiliste extrahieren
			data = data.filelist;
			
			//Daten da?
			if( data != null ){
				//Liste beginnen
				html += '<ul class="files">';
				
				//leer
				var elements = 0;
				
				//alle Dateien durchgehen
				$.each( data, function( k,v ){
					//Icon Klasse
					var icon;
					
					//Incon nach Dateityp
					if( v.type == 'dir' ){
						icon = 'folder-collapsed';	
					}
					else if( v.type == 'file' ){
						icon = 'document';
					}
					else if( v.type == 'kt' ){
						icon = 'calculator';
					}
					else{
						icon = 'blank';
					}
					
					//HTML Listenelement
					html += '<li url="'+v.url+'" ftype="'+v.type+'" name="'+v.name+'"><span class="ui-icon ui-icon-'+icon+'" style="display:inline-block;"></span>'+v.name+'</li>';
					
					//ein Element mehr
					elements++;	
				});
				
				if( elements == 0 ){
					//HTML Listenelement
					html += '<li>Der Ordner ist leer!</li>';
				}
				
				//Liste beenden
				html += '</ul>';
				//Hinweis
				html += '<small>Rechtsklick auf Datei oder Ordner zum Löschen, Kopieren, Verschieben, Umbenennen oder Freigeben</small>';
				
				//Mehr als eine Element??
				if( folder_ex ){
					//Liste anzeigen
					$( "div.main_files" ).html( html );
					
					//Pfadbar Ordner anklickbar
					$( 'span.pathslash' ).unbind('click').click( function() {
						
						//Pfad lesen
						var path = $( this ).attr('path');
						//Pfad setzen
						allgvars.path = path;
						//Explorer neu laden
						main_explorer();  

					});

					//einen Ordner hoch Button
					$( 'button#oneup' ).unbind('click').click( function() {
						//letzten Slash am Ende weg (nur wenn vorhanden)
						if( allgvars.path[(allgvars.path.length-1)] == '/' ){
							//weg machen
							allgvars.path = allgvars.path.substr(0, (allgvars.path.length-1));
						}
						//letzten Slash im Str suche
						var slash = allgvars.path.lastIndexOf("/");

						//keiner mehr da, oder ganz am Anfang?
						if( slash == -1 || slash == 0 ){
							//Grundordner
							allgvars.path = '/';
						}
						else{
							//Rest abschneiden
							allgvars.path = allgvars.path.substr(0, slash );
							allgvars.path += '/';
						}

						//Explorer neu laden
						main_explorer();
					});
					
					//auf Klicks auf Dateien hören
					$( 'ul.files li' ).unbind('click').click( function() {
						//Datei laden? (evtl. für Buttonbar deaktivieren)
						if( shall_do_fileload() ){
							//Dateieigenschaften lesen
							//	Typ
							var ftype = $( this ).attr( 'ftype' );
							//	URL (Dateiname)
							var url = $( this ).attr( 'url' );
							//	Name für Datei
							var name = $( this ).attr( 'name' );
							
							//je nach Dateityp
							if( ftype == 'dir' ){
								//Ordner
								
								//Pfad anpassen
								allgvars.path = allgvars['path']+url+'/';
								//Explorer neu laden
								main_explorer();  
							}
							else if( ftype == 'file' ){
								//datei
								
								//Datei öffnen
								open_file( url );
							}
							else if( ftype == 'kt' ){
								//KIMB Tabelle?
								
								//Tabelle anzeigen
								open_table( url, name );
							}
						}
						
					});
					
					//Klick auf Buttons
					//neue Tabelle
					$( "button#add_table").unbind( 'click' ).click( function (){
						$( "div.toolbar span" ).css( 'display', 'inline' );
								
						//Knopf gedrückt?
						//	wenn Cursor in Input
						$( "div.toolbar span input" ).unbind( 'keyup' ).keyup( function( event ) {
							//Enter?
							if(event.keyCode == 13){
								//neuen Wert lesen
								var name = $( this ).val();
								//Dateiendung
								var url = name + '.kimb_table'; 
								
								//als neue Tabelle öffen
								open_table( url, name, true );
								
								//bearbeiten beenden
								$( "div.toolbar span" ).css( 'display', 'none' );
							}
							//STRG Taste (Ctrl)
							else if( event.keyCode == 17 ){
								//bearbeiten beenden
								$( "div.toolbar span" ).css( 'display', 'none' );
							}
						});
					});
					//neue Datei (Upload)
					$( "button#add_file").unbind( 'click' ).click( function (){
					
						//HTML Formular
						var html = '<h2>Dateien hochladen</h2>';
						html += '<form action="' + add_daten.siteurl + '/ajax.php?addon=daten" class="dropzone" id="dropzone">';
						html += '<input type="hidden" name="allgvars[folder]" value="' + allgvars.folder + '">';
						html += '<input type="hidden" name="allgvars[path]" value="' + allgvars.path + '">';
						html += '<input type="hidden" name="todo" value="uploadfile">';
						html += '<img src="' + add_daten.siteurl + '/load/addondata/daten/upload.png" style="display: block; margin:auto;" title="Ziehen Sie zum Hochladen Dateien über dieses Feld oder klicken Sie!" class="dz-message">';
						html += '</form>';
						
						//Dialog
						j_alert( html, 600 );
						
						//Dropzone init
						var ExplorerDropzone = new Dropzone("form#dropzone");
						//wenn fertig
						ExplorerDropzone.on( "queuecomplete", function( file ){
							//Explorer aktualisieren
							main_explorer();
						});
						
					});
					//neuer Ordner
					$( "button#add_folder").unbind( 'click' ).click( function (){
						$( "div.toolbar span" ).css( 'display', 'inline' );
								
						//Knopf gedrückt?
						//	wenn Cursor in Input
						$( "div.toolbar span input" ).unbind( 'keyup' ).keyup( function( event ) {
							//Enter?
							if(event.keyCode == 13){
								//neuen Wert lesen
								var name = $( this ).val();
							
								//Namen des neuen Ordners übertragen
								allgvars.file = name;
							
								//AJAX Anfrage für Explorer Dateien
								//	allgvars mit Gruppe, Pfad
								//	ToDo
								$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "newfolder" } ).always( function( data ) {
								
									//Okay?
									if( data != null && data.main != null && data.main.wr ){
									
										//Explorer aktualisieren
										main_explorer();	
									}
									else{
										 j_alert( "Das Erstellen der Ordners ist fehlgeschlagen!" );	
									} 	
								});							
								
								//bearbeiten beenden
								$( "div.toolbar span" ).css( 'display', 'none' );
							}
							//STRG Taste (Ctrl)
							else if( event.keyCode == 17 ){
								//bearbeiten beenden
								$( "div.toolbar span" ).css( 'display', 'none' );
							}
						});
						
					});
					//Zwischenablage
					$( "button#zwisch" ).unbind( 'click' ).click( makezwischenablagedial );

					function makezwischenablagedial(){
						//Daten lesen
						var zwisch = localStorage.getItem( "daten_zwischenablage" );
						//JSON parsen
						zwisch = JSON.parse( zwisch );

						 //wenn alter Dialog div noch im DOM diesen löschen
						if ($( "div.zwischenablage" ).length ){
							$( "div.zwischenablage" ).remove();
						}
						
						//HTML für neuen Dialog DIV		
						var dial = '<div class="zwischenablage" title="Zwischenablage">'
						dial += '<table>';
						dial += '<tr>';
						dial += '<th>Art</th>';
						dial += '<th>Name</th>';
						dial += '<th>Einfügen</th>'
						dial += '<th>Löschen</th>';
						dial += '<th>Pfad</th>';
						dial += '</tr>';

						if( zwisch != null && typeof zwisch == "object" ){
							//alle Werte durchgehen
							$.each( zwisch, function (k,v){
								dial += '<tr about="'+htmlspecialchars(JSON.stringify(v))+'" key="'+k+'">';
								dial += '<td>'+(( v.type == 'dir' ) ? '<span class="ui-icon ui-icon-folder-collapsed"></span>' : ( ( v.type == 'kt' ) ? '<span class="ui-icon ui-icon-calculator"></span>' : '<span class="ui-icon ui-icon-document"></span>' ) ) +'</td>';
								dial += '<td>'+v.viewname+'</td>';
								dial += '<td><button class="pastezwisch" title="Hier einfügen"><span class="ui-icon ui-icon-pin-s"></span></button></td>'
								dial += '<td><button class="delzwisch" title="Aus Zwischenablage löschen"><span class="ui-icon ui-icon-trash"></span></button></td>';
								dial += '<td>'+(( v.platz == 'user' ) ? 'my:/' : 'pub:/' )+v.filepath+v.filename+'</td>';
								dial += '</tr>';
							});
						}
						else{
							dial += '<td colspan="5" id="tabempt"></td>';
						}

						dial += '</table>';
						dial +='<small>Wählen Sie eine Datei, einen Ordner aus, um diesen hierhin ('+allgvars.proto+allgvars.path+') zu kopieren oder zu verschieben.</small>'
						dial +='</div>';
						
						//HTML dem DOM anfügen   
						$( "body" ).prepend( dial );

						//Fehlermeldung?
						if( $( "td#tabempt" ).length > 0 ){
							show_error( 'Leer', 'Die Zwischenablage ist leer!', "td#tabempt" );
						}
						
						//Dialog öffnen	 
						$( "div.zwischenablage" ).dialog({ 
							modal:true,
							responsive: true,
							minWidth: 500,
							buttons:{
								"Zwischenablage leeren": function (){
									localStorage.clear();
									$( this ).dialog( 'close' );
								},
								"Schließen":function(){
									$( this ).dialog( 'close' );
								}
							}
						});

						//Einfügen Button
						$( "button.pastezwisch" ).unbind( 'click' ).click( function (){
							//Key und Daten lesen
							var data = $( this ).parent('td').parent('tr').attr('about');
							var key = $( this ).parent('td').parent('tr').attr('key');
							//Daten aus JSON zu Array
							data = JSON.parse( data );

							//Fenster machen
							 //wenn alter Dialog div noch im DOM diesen löschen
							if ($( "div.zwischenablagepaste" ).length ){
								$( "div.zwischenablagepaste" ).remove();
							}
						
							//HTML für neuen Dialog DIV		
							var dial = '<div class="zwischenablagepaste" title="Zwischenablage - Einfügen">';
							dial += '<h3>Verschieben &amp; kopieren</h3>';
							dial += '<input type="text" readonly="readonly" id="oldpath"> (Quelle)<br />';
							dial += '<input type="text" readonly="readonly" id="newpath"> (Zielverzeichnis)<br />';
							dial += '<input type="text" id="file"> ('+(( data.type == 'dir' ) ? 'Ordner' : 'Datei' ) +'name)<br />';
							if( data.type == 'kt' ){
								dial += '<i style="color:orange;"><span class="ui-icon ui-icon-alert" style="display:inline-block;"></span> Die Endung ".kimb_table" muss bestehen bleiben!</i><br />';
							}
							dial += '<i id="replaceatt" style="display:none; color:red;"><span class="ui-icon ui-icon-info"  title="Die Datei wird überschrieben, sofern Sie auf Los klicken!" style="display:inline-block;"></span> Dateiname schon vergeben</i><br />';
							dial += '<input type="radio" name="art" value="rename" checked="checked">  (verschieben)<br />';
							dial += '<input type="radio" name="art"  value="copy"> (kopieren) ';
							dial += '</div>';

							//HTML dem DOM anfügen   
							$( "body" ).prepend( dial );

							//Inhalte setzen
							$("div.zwischenablagepaste input#oldpath").val( (( data.platz == 'user' ) ? 'my:/' : 'pub:/' )+data.filepath+data.filename );
							$("div.zwischenablagepaste input#newpath").val( allgvars.proto+allgvars.path );
							$("div.zwischenablagepaste input#file").val( data.filename );
							//	Dateinamen prüfen
							//		Listener
							$("div.zwischenablagepaste input#file").unbind( 'change' ).change( finachanged );
							$("div.zwischenablagepaste input#file").unbind( 'keyup' ).keyup( finachanged );
							//		Funktion
							function finachanged(){
								var val = $("div.zwischenablagepaste input#file").val();

								$("div.zwischenablagepaste  i#replaceatt" ).css( "display", "none" );

								$( "div.main_files ul.files li" ).each( function (){
									var name = $( this ).attr( 'url' );
									if( name == val ){
										$("div.zwischenablagepaste  i#replaceatt" ).css( "display", "block" );
									}
								});
							}
							//	immer einmal zu Anfang testen
							finachanged();

							//Dialog öffnen	 
							$( "div.zwischenablagepaste" ).dialog({ 
								modal:true,
								responsive: true,
								minWidth: 400,
								buttons:{
									"Los": function (){

										var infos = {
											'verz' : data.platz,
											'url' : data.filepath+data.filename,
											'name' : $("div.zwischenablagepaste input#file").val(),
											'art' : $("div.zwischenablagepaste input[name=art]:checked").val(),
										};

										//machen
										//	allgvars mit Gruppe, Pfad
										//	ToDo
										//	infos mit Quelle und neuem Dateinamen und art
										$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "zwischenabl", "infos": infos } ).always( function( data ) {
										
											//Okay?
											if( data != null && data.main != null && data.main.versch ){

												//Explorer aktualisieren
												main_explorer();
											
												//jetzt aus Zwischenablage löschen
												del_elem( key );

												//Dialog schließen
												$( "div.zwischenablagepaste" ).dialog( 'close' );

												//Zwischenablage schließen
												$( "div.zwischenablage" ).dialog( 'close' );
											}
											else{
												j_alert( "Konnte nicht einfügen!" );	
											} 	
										});										
									},
									"Abbrechen":function(){
										//nur Dialog schließen
										$( this ).dialog( 'close' );
									}
								}
							});
						});
							
						//Löschen Button
						$( "button.delzwisch" ).unbind( 'click' ).click( function (){
							//Key des Eintrags
							var key = $( this ).parent('td').parent('tr').attr('key');
							//löschen
							del_elem( key );
							//Fenster neu laden
							makezwischenablagedial();
						});

						//Element aus Zwischenablage löschen
						function del_elem( key ){
							//Daten lesen
							var zwisch = localStorage.getItem( "daten_zwischenablage" );
							//JSON parsen
							zwisch = JSON.parse( zwisch );

							//Daten okay
							if( zwisch != null && typeof zwisch == "object" ){
								//gewünschten Bereich löschen
								zwisch.splice( key , 1);

								//wieder zu JSON
								var json = JSON.stringify( zwisch );
								//leer?
								if( json != "[]" && json != "" ){
									//nicht leer, Daten speichern
									localStorage.setItem( "daten_zwischenablage", json );
								}
								else{
									//leer, löschen
									localStorage.removeItem( "daten_zwischenablage" );
								}
							}
						}
								
					};
					
					//Rechtsklick auf Datei => Löschen?
					$(document).on("contextmenu", 'ul.files li', function(e){
					
						//k und sk Werte bestimmen
						var name = $( this ).attr( 'name' );
						var url = $( this ).attr( 'url' );
						var ftype = $( this ).attr( 'ftype' );

						if(
							typeof name == "undefined" ||
							typeof url == "undefined" ||
							typeof ftype == "undefined"
						){
							return false;
						}
						
						//wenn alter Dialog div noch im DOM diesen löschen
						if ($( "div.delfile_explorer" ).length ){
							$( "div.delfile_explorer" ).remove();
						}
						
						//HTML für neuen Dialog DIV		
						var dial = '<div class="delfile_explorer" title="Dateieinstellungen">'
						dial += 'Möchten Sie "'+ name +'" löschen?<br />';
						dial += '<span id="ja">Löschen</span>';
						dial += '<div class="delfile_explorer_satus" style="display:none;">Fehler</div>';
						dial += '<hr />';
						dial += 'Möchten Sie sich "'+ name +'" in der Zwischenablage merken?<br />';
						dial += '<small>(Kopieren, Verschieben, Umbenennen)</small><br />';
						dial += '<span id="merken">Merken</span>';
						dial += '<div class="zwiabfile_explorer_satus" style="display:none;"></div>';
						dial += '<hr />';
						if( allgvars.folder == 'user' ){
							dial += 'Oder möchten Sie "'+name+'" via Link freigeben?<br />';
							dial += '<span id="frei">Freigeben</span>';
							if( ftype == 'dir' ){
								dial += '<br />';
								dial += '<small><input id="freigupload" type="checkbox" checked="checked"> Upload von Dateien in den freigegebenen Ordner erlauben.</small>';
							}
						}
						else{
							dial += 'Sie können nur Inhalte aus "Mein Verzeichnis" per Link freigeben!';
						}
						dial +='</div>';
						
						//HTML dem DOM anfügen   
						$( "body" ).prepend( dial );
						
						//Dialog öffnen	 
						$( "div.delfile_explorer" ).dialog({ 
							modal:true,
							responsive: true,
							buttons:{
								"Schließen":function(){
									$( this ).dialog( 'close' );
								}
							}
						});
						
						//Dialog Buttons
						$( "div.delfile_explorer span" ).button();

						//Button Löschen Listener
						$( "div.delfile_explorer span#ja" ).click( function () {
									
							//URL der Datei
							allgvars.file = url;
									
							$( "div.delfile_explorer_satus" ).css( { "display":"none" });								
																		
							//AJAX Anfrage Dateien löschen
							$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "delfile" } ).always( function( data ) {
									
								//Okay?
								if( data != null && data.main != null && data.main.wr ){
										
									//Explorer aktualisieren
									main_explorer();
											
									//Dialog schließen
									$( "div.delfile_explorer" ).dialog( 'close' );	
								}
								else{
									//Fehlermeldung
									$( "div.delfile_explorer_satus" ).css( { "display":"block","background-color":"red", "padding": "5px", "border-radius":"2px", "color":"white"  } );
								} 	
							});
						});

						//Button Merken Listener
						$( "div.delfile_explorer span#merken" ).click( function () {

							//Medlung ausblenden
							$( "div.zwiabfile_explorer_satus" ).css( { "display":"none" });	

							//alles Versuchen
							try{
								//Infos für die Datei holen
								var about = {
									'viewname' : name,
									'filename' : url,
									'type' : ftype,
									'filepath' : allgvars.path,
									'platz' : allgvars.folder
								};
								
								//ganze Zwischeablage lesen
								var zwisch = localStorage.getItem( "daten_zwischenablage" );

								//leer?
								if( typeof zwisch == "undefined" ||  zwisch == '' ||  zwisch == null ){
									//leeres Array
									zwisch = new Array();
								}
								else{
									//JSON Array einlesen
									zwisch = JSON.parse( zwisch );
								}

								//neues hinzufügen
								zwisch.push( about );
								//wieder zu JSON
								zwisch = JSON.stringify( zwisch );
								//wieder ablegen
								localStorage.setItem( "daten_zwischenablage", zwisch );

								//Okay Medlung
								$( "div.zwiabfile_explorer_satus" ).html( "Gemerkt" );
								$( "div.zwiabfile_explorer_satus" ).css( { "display":"block","background-color":"green", "padding": "5px", "border-radius":"2px", "color":"white"  } );
							}
							catch (exception) {
								//Fehlermeldung
								$( "div.zwiabfile_explorer_satus" ).html( "Fehler" );
								$( "div.zwiabfile_explorer_satus" ).css( { "display":"block","background-color":"red", "padding": "5px", "border-radius":"2px", "color":"white"  } );
							}
						});

						//Buttons Freigabe Listener
						$( "div.delfile_explorer span#frei" ).click( function () {	
							
							//URL der Datei
							allgvars.file = url;

							//Ordner?
							if(  ftype == 'dir' ){
								//Button Status prüfen
								if( $( "input#freigupload:checked" ).length == 1 ){
									var freigabeupload = 'yes'; 
								}
								else{
									var freigabeupload = 'no';
								}
							}
							else{
								var freigabeupload = 'no';
							}
							
							//per AJAX freigeben
							make_freigabe( freigabeupload );
							
							//Dialog schließen
							$( "div.delfile_explorer" ).dialog( 'close' );
						});
						
						return false;
					});
				}
				else{
					//Keine Dateien -> Fehler
					show_error( '404', 'Ordner nicht gefunden!', "div.main_files"  );	
				}
			}
			else{
				//Schlechte Rückgabe
				show_error( '404', 'Ordner nicht gefunden!', "div.main_files"  );
			}
	});
	
}

//KIMB-Tabelle
//	nächste ID
var table_next_id;
//	Passwort
var global_table_passw;

//Tabelle öffnen
function open_table( url, name, adding ){

	//Vars für Funktion
	var data_okay = false;
	
	//neue Tabelle?
	if( typeof adding === 'undefined' ){
		//nicht neu
		adding = false;
	}
	
	//URL zu Datei
	allgvars.file = url;

	//festes Passwort setzen
	global_table_passw = 'vqtxvpJJWiFJrNKcOQOjMgTHBxqGdiyuBhilfRktqfWLyHEw';

	//Fortsetzen
	open_table_get( name, adding, data_okay );
}

//Tabelle öffen Passwort abfragen
function open_table_passw(){

	//wenn alter Dialog div noch im DOM diesen löschen
	if ($( "div.j_promt" ).length ){
		$( "div.j_promt" ).remove();
	}
						
	//HTML für neuen Dialog DIV		
	var dial = '<div class="j_promt" title="Passwort benötigt!">'
	dial += 'Bitte geben Sie das Passwort für die Tabelle an!<br />';
	dial += '<input type="password" placeholder="Passwort" id="js_table_passw"><br />';
	dial += '<input type="checkbox" id="js_table_passw_save"> Passwort merken?<br />';
	dial += '<small>Lassen Sie das Feld für unverschlüsselte Tabellen leer.</small>';
	dial += '</div>';
						
	//HTML dem DOM anfügen   
	$( "body" ).prepend( dial );
			
	//Dialog öffnen	 
	$( "div.j_promt" ).dialog({ 
		modal:true,
		responsive: true,
		buttons:{
			"OK":function(){
				//Passwort lesen
				var pass = $( "input#js_table_passw" ).val();
				
				if ( pass != '' ) {
					//als Passwort nehmen
					global_table_passw = pass;
				}
				else{
					//auch ohne Passwort verschlüsseln, aber immer mit dem Gleichen
					global_table_passw = 'vqtxvpJJWiFJrNKcOQOjMgTHBxqGdiyuBhilfRktqfWLyHEw';
				}	
				
				//Passwort merken
				//	gewählt?
				if( $( "input#js_table_passw_save:checked" ).length == 1 ){
					localStorage.setItem( "global_table_passw", global_table_passw );
				}

				//Dialog schließen
				$( this ).dialog( 'close' );
			},
			"Passwort zeigen": function(){
				$( "input#js_table_passw" ).attr( "type", "text" );
			}
		}
	});

}

//Tabellen Inhalte bekommen
function open_table_get( name, adding, data_okay ){
	
	//neue Tabelle
	if( adding ){
		
		//Standarddaten laden
		var data = {"table":[[0,"Name","Vorname"],[1,"Meier","Heinze"]],"nextid":2};

		//Nach Passwort fragen
		open_table_passw();
		
		//Daten okay
		data_okay = true;
		
		//Tabelle anzeigen
		open_table_dialog( data_okay, data, adding, name );
	}
	else{
		//Daten für Tabelle anfragen
		//	allgvars -> User, Pfad, Datei
		//	Tablle	
		$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "table" } ).always( function( data ) {
			
			//nur main nutzen, kein dev für Debugging
			data = data.main;
			
			//Daten okay?
			if( data != null ){
				
				//JSON parsen
				data = JSON.parse( data );
					
				//Entschlüsselung versuchen
				try{
					//Daten mit SJCL entschlüsseln
					//	hier nur festes Passwort
					data = sjcl.decrypt( global_table_passw, data );
				}
				//Bei Fehler
				catch (e){

					//Gespeichertes Passwort holen
					var locpass = localStorage.getItem( "global_table_passw");

					//Passwort aus Speicherung okay?
					if( typeof locpass != "undefined" && locpass != null && locpass != '' ){

						//als global setzen
						global_table_passw = locpass;

						//Entschlüsselung damit versuchen
						try{
							//Daten mit SJCL entschlüsseln
							//	Passwort aus localStorage
							data = sjcl.decrypt( global_table_passw, data );

							//Paswort okay
							var passwokay = true;
						}
						//Bei Fehler
						catch (e){
							var passwokay = false;							
						}
					}

					//Passwort immernoch nicht okay?
					if( passwokay != true ){
						//Nach Passwort fragen
						open_table_passw();

						//Entschlüsselung mit eing. Passwort versuchen
						try{
							//Daten mit SJCL entschlüsseln
							//	Passwort aus Eingabe
							data = sjcl.decrypt( global_table_passw, data );

						}
						//Bei Fehler
						catch (e){
							//Zum Ende Fehlermeldung
							 j_alert( 'Die Tabelle konnte mit diesem Passwort nicht entschlüsselt werden!\r\n(Fehlermeldung:"' + e.message + '")' );
							return;					
						}

					}					
				}
					
				//Entschlüsselte Daten parsen
				data = JSON.parse( data );
				
				//Daten okay
				data_okay = true;
			}
			
			//Tabelle öffnen
			open_table_dialog( data_okay, data, adding, name );
		});
	}
}

//Tabelle anzeigen
function open_table_dialog( data_okay, data, adding, name ){
	//Daten okay?
	if( data_okay ){

		//NextID für Tabelle bestimmen
		table_next_id = data.nextid;
		
		//Tabellendaten lesen
		data = data.table;
			
		//Tabelle zeichen
		make_table_html( data, 'div.for_file_kimbta' );
		
		//Tabelle zeigen
		$( 'div.for_file_kimbta' ).css( 'display', 'block' );
			
		//Dialog für Tabelle
		$( 'div.for_file_kimbta' ).dialog({ width: 720, modal: true, responsive: true, title: name, beforeClose: function( event, ui ) { global_table_passw = ''; } });
		
		//neue Tabelle
		if( adding ){
			//neue Tabelle anlegen
			save_new_table( data );
			
			//Explorer aktualisieren
			main_explorer();
		}
	}
	else{
		 j_alert( "Fehler beim Lesen der Tabelle" );
	}
}

//Datei öffen
function open_file( url ){
	
	//Link zur Datei erstellen
	var link = add_daten.siteurl+"/ajax.php?addon=daten&folder="+ encodeURI( allgvars.folder ) +"&path="+ encodeURI( allgvars.path + url );
	
	//Datei in PopUp öffnen
	window.open( link, "_blank", "width=900px,height=500px,top=20px,left=20px");
}

//KIMB Tabelle HTML erzeugen
//	data => TabellenArray
//	domel => DOM Element
function make_table_html( data, domel ){
	
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
	//Buttons
	html += '<button id="new_sp">Spalte hinzufügen</button><br />';
	html += '<button id="new_ze">Zeile hinzufügen</button><br />';
	//Hinweistexte
	html += '<small>Doppenklick zum Ändern; Alt zum Speichern; Ctrl zum Verwerfen; Rechtsklick zum Löschen/ Hinzufügen</small><br />';
	html += '<button id="exp_json">JSON Export</button>';
	html += '<button id="imp_json">JSON Import</button>';
	//Feld für Statusanzeigen (Fehler, Speicherung, Gespeichert)
	html += '<span class="add_daten_status" style="display:none;"></span>';
	
	//Tabelle anzeigen
	$( domel ).html( html );
	
	//die Tabellenfelder, welche bearbeitbar sind (Klasse edit), bei Doppelklick
	//	Inhalte anpassen
	$( "div.for_file_kimbta table td.edit, div.for_file_kimbta table th.edit").unbind('dblclick').dblclick( function() {
		
			//K Wert => ID der Reihe
			var k = $( this ).attr( 'k' );
			//SK Wert => ID der Spalte
			var sk = $( this ).attr( 'sk' );
			//Inhalt des Feldes
			var val = $( this ).html();
			
			//Ist das Feld schon bearbeitbar gemacht (Input Element vorhanden)
			if( val.indexOf( '<textarea style="width:95%;">' ) === -1 ){
				//nein, also einblenden
				
				//br zu Umbrüchen
				val = val.replace(/<br \/>/g,"\r\n");
				val = val.replace(/<br>/g,"\r\n");
				
				//Input Feld im Tabellenkästchen zeigen, Wert => Inhalt des Kästchen 
				//$( this ).html( '<input type="text" value="'+val+'">' );
				$( this ).html( '<textarea style="width:95%;">'+val+'</textarea>' );
				
				//Knopf gedrückt?
				//	wenn Cursor in Input
				$( this ).children("textarea").keyup( function( event ) {
					//Alt?
					if(event.keyCode == 18){
						//neuen Wert lesen
						var newval = $( this).val();
						
						//HTML Special Chars codieren
						//	& Zeichen
						newval = newval.replace(/&/g, "&amp;");
						//	spitze Klammer auf
						newval = newval.replace(/</g, "&lt;");
						//	spitze Klammer zu
						newval = newval.replace(/>/g, "&gt;");
						//	Umbruch eins
						newval = newval.replace(/\n/g,'<br />');
						//	Umbruch zwei
						newval = newval.replace(/\r/g,'<br />');
						//	Umbruch beide
						newval = newval.replace(/\r\n/g,'<br />');
						
						//Input Feld weg und neuen Wert wieder in Kästchen setzen
						$( this ).parent().html( newval );
						//Data Array mit Tabellenwerten anpassen
						data[k][sk] = newval;
						
						//neue Tabellenwerte speichern
						save_new_table( data );
					}
					//STRG Taste (Ctrl)
					else if( event.keyCode == 17 ){
						//wieder den alten Wert setzen
						//bearbeiten beenden
						$( this ).parent().html( val );
					}
				});
			}
	});
	
	//Neue Spalte Button Klick listen
	$( "button#new_sp" ).unbind('click').click( function () {
		//am Ende einfügen
		new_spalte( 'end', data, domel );
	});
	
	//Neue Reihe Button Klick listen
	$( "button#new_ze" ).unbind('click').click( function () {
		//am Ende einfügen
		new_reihe( 'end', data, domel );
	});
	
	//JSON Export
	$( "button#exp_json ").unbind('click').click( function () {
		//Export durchführen
		make_json( data );	
	});
	
	//JSON Import
	$( "button#imp_json ").unbind('click').click( function () {
		//Import durchführen
		import_json();
	});
	
	//Rechtsklick auf Feld
	$(document).on("contextmenu", 'div.for_file_kimbta table td.edit', function(e){
	
		//k und sk Werte bestimmen
		var k = $( this ).attr( 'k' );
		var sk = $( this ).attr( 'sk' );
		
		//wenn alter Dialog div noch im DOM diesen löschen
		if ($( "div.rightclick_menue" ).length){
			$( "div.rightclick_menue" ).remove();
		}
		  
		//HTML für neuen Dialog DIV		
		var menue = '<div class="rightclick_menue" title="Tabelle hier anpassen">'
		//Buttons add
		menue += '<button id="sp_add">Spalte hier hinzufügen</button><br />';
		menue += '<button id="ro_add">Reihe hier hinzufügen</button><br />';
		//Erklärung
		menue += '<small>Jeweils oberhalb bzw. links des angeklickten Kästchens</small><br/>';
		//Buttons del
		menue += '<button id="sp_del">Spalte hier löschen</button><br />';
		menue += '<button id="ro_del">Reihe hier löschen</button>';
		menue += '</div>';
		
		//HTML dem DOM anfügen   
		$( "body" ).prepend( menue );
		
		//Dialog öffnen	 
		$( "div.rightclick_menue" ).dialog({ 
			modal:true,
			responsive: true
		});
		
		//Buttons listener
		//Spalte hinzufügen
		$( "div.rightclick_menue button#sp_add" ).click( function () {
			new_spalte(  sk, data, domel );
		});
		//Reihe hinzufügen
		$( "div.rightclick_menue button#ro_add" ).click( function () {
			new_reihe( k, data, domel );
		});
		//Splate löschen
		$( "div.rightclick_menue button#sp_del" ).click( function () {
			del_spalte( sk, data, domel );
		});
		//Reihe löchen 
		$( "div.rightclick_menue button#ro_del" ).click( function () {
			del_reihe(  k, data, domel );
		});
		
		return false;
	});
	
	
	return true;
}

//neue Spalte
//	ort => end oder ID der Spalte (immer links davon)
function new_spalte( ort, data, domel ){

	//nicht am Ende
	if( ort != 'end' ){
		//Int aus String
		ort = parseInt( ort );
	}
	
	//alle Reihen durchgehen
	for( var i = 0; i < data.length; i++){
		
		//Ende?
		if( ort == 'end' ){
			//hinten neue Spalte anfügen
			data[i].push( 'Wert' );
		}
		else{
			//an der Stelle neue Spalte anfügen
			data[i].splice( ort , 0, "Wert");
		}
	}
	
	//Tabelle zeigen
	make_table_html( data, domel );
	
	//Tabelle speichern
	save_new_table( data );
	
	return;
}

//neue Reihe
//	ort => end oder ID der Reihe (immer oberhalb davon)
function new_reihe( ort, data, domel ){
	
	//neue Reihe
	var array = new Array();
	
	//am Ende?
	if( ort != 'end' ){
		//Int aus String
		ort = parseInt( ort );
	}
	
	//Array für neue Reihe erstellen
	for( var i = 0; i < data[0].length; i++){
		
		//Wert dem Array anfügen
		array.push( "Wert" );
	}
	
	//er erste Wert wird die ID der Reihe
	//hier die nächste freie nehmen
	array[0] = table_next_id;
	//nächste frei ID um einen erhöhen
	table_next_id++;
	
	if( ort == 'end' ){
		//neue Reihe am Ende einfügen
		data.push( array );
	}
	else{
		//neue Reihe an der Stelle einfügen
		data.splice( ort , 0, array);
	}
	
	//Tabelle aktualisieren	
	make_table_html( data, domel );
	
	//Tabelle speichern
	save_new_table( data );
	
	return;
}

//Spalte löschen
//	ort => Stelle
function del_spalte( ort, data, domel ){
	
	//Ort zu Int
	ort = parseInt( ort );
	
	//alle Reihen durchgehen
	for( var i = 0; i < data.length; i++){

		//Spalte aus Reihe löschen
		data[i].splice( ort , 1);

	}

	//Tabelle aktualisieren
	make_table_html( data, domel );
	
	//Tabelle sichern
	save_new_table( data );
	
	return;
}

//Reihe löschen
function del_reihe( ort, data, domel ){
	
	//Int aus Ort
	ort = parseInt( ort );
	
	//gewünschte Reihe aus Array entfernen
	data.splice( ort , 1 );

	//Tabelle zeigen
	make_table_html( data, domel );
	
	//Tabelle sichern	
	save_new_table( data );
	
	return;
}

//JSON Export
function make_json( data ){
	
	//JSON String aus Array erzeugen
	var file = JSON.stringify( { 'table': data, 'nextid': table_next_id } );
	
	//Popup mit Daten öffnen
	window.open( "data:text/json;utf-8," + file ,"_blank", "width=900px,height=500px,top=20px,left=20px,scrollbars=yes");
}

//JSON Import
function import_json(){
	
	//evtl. noch DIV für alten Dialog im DOM
	if ($( "div.import_json" ).length){
		//löschen
		$( "div.import_json" ).remove();
	}
	
	//DIV für neuen Dialog  			
	var menue = '<div class="import_json" title="JSON in die Tabelle importieren">'
	//Infotext
	menue += 'Bitte geben Sie hier den JSON Code für die Tabelle an:<br />';
	//JSON Code Eingabe
	menue += '<textarea id="json_impcode" placeholder=\'{"table":[[0,"Vorname","Name"],[1,"Max","Muster"],[2,"Maxa","Mustera"]],"nextid":3}\' rows="5" cols="20"></textarea><br />'
	//Hiweis
	menue += '<small>Der Import ersetzt die aktuelle Tabelle!</small>';
	//Status
	menue += '<span class="imp_daten_status" style="display:none;"></span>';
	menue += '</div>';

	//HTML anfügen
	 $( "body" ).prepend( menue );
	
	//Dialog	 
	$( "div.import_json" ).dialog({ 
		modal: true,
		responsive: true,
		//Buttons definieren
		buttons: {
			//Import
			"Import Data": function(){
				
				//JSON Code lesen
				var json = $( "textarea#json_impcode" ).val();
				
				//JSON Code parsen
				try{
					var data = JSON.parse( json );
				}
				//Bei Fehler
				catch(e){
					//Fehlermeldung
					$("span.imp_daten_status").html( "Fehler" );
					$("span.imp_daten_status").css( { "display":"inline","background-color":"red", "padding": "5px", "border-radius":"2px", "color":"white", "float":"right"  } );
				}
				
				//JSON Object?
				if( typeof data === "object" ){
					
					//NextID der Importtabelle lesen
					//und global setzen
					table_next_id = data.nextid;
					
					//Tabellendaten lesen
					data = data.table; 
					
					//Tabelle zeigen
					make_table_html( data, 'div.for_file_kimbta' );
				
					//Tabelle speichern
					save_new_table( data );
					
					//Import Okay Satus
					$("span.imp_daten_status").html( "Importiert" );
					$("span.imp_daten_status").css( { "display":"inline","background-color":"green", "padding": "5px", "border-radius":"2px", "color":"white", "float":"right"  } );
					
					//Dialog nach 2sec schließen
					setTimeout( function() {
						$(  "div.import_json" ).dialog( "close");
					}, 2000);
				}
			},
			//Abbrechen -> Dialog schließen
			"Abbrechen": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	return;
}

//Tabelle speichern
//	data => Array mit Tabellendaten
function save_new_table( data ){
	
	//Speicherung beginnt als Status
	$("span.add_daten_status").html( "Speicherung ..." );
	$("span.add_daten_status").css( { "display":"inline","background-color":"orange", "padding": "5px", "border-radius":"2px", "color":"white", "float":"right"  } );
	
	//Daten der Tabelle um NextId erweitern
	data = { "table": data,  "nextid": table_next_id };
	
	//Datensatz verschlüsseln
	data = sjcl.encrypt( global_table_passw, JSON.stringify( data ) );
	
	//Daten an der Server senden
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "tablesave", "data": JSON.stringify( data ) } )
		//erfolgreich?
		.done(function( data ) {
			//auch von Sever okay bekommen?
			if( data.main.wr ){
				//Gespeichert
				$("span.add_daten_status").html( "Gespeichert" );
				$("span.add_daten_status").css( { "background-color":"green"  } );
				
				//Erfolg nach 5sec ausblenden
				setTimeout( function() {
					$("span.add_daten_status").css( "display","none");
				}, 5000);
			}
			//Verbindung okay, aber auf Server Fehler
			else{
				//anzeigen
				$("span.add_daten_status").html( "Fehler" );
				$("span.add_daten_status").css( { "background-color":"red"  } );
			}
		})
		//Fehler?
		.fail(function() {
			//Fehler anziegen
			$("span.add_daten_status").html( "Fehler" );
			$("span.add_daten_status").css( { "background-color":"red"  } );
		});
}

function make_freigabe( upload ){
	
	//Freigabeanfrage an Server senden
	//	User
	//	Pfad
	//	URL
	//	Upload okay? (nur für Ordner sinvoll)
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "newfreigabe", "upload" : upload } ).always( function( data ) {
		
		if( data.main.okay ){
			j_alert( 'Die Datei wurde freigegeben.<br /><br /><input readonly="readonly" onclick="this.focus();this.select();" style="border:1px solid black; background-color:gray; text:white; width:100%;" value="'+ data.main.link +'"><br /><br />Unter "Freigabe" sehen Sie alle Freigaben und können diese löschen!' );		
		}
		else{
			j_alert( 'Die Freigabe schlug fehl!' );
		}
		
	});
	
	return;
}

//Freigaben Zeigen
function show_freigaben(){

	//aktuellen Pfad auch als ID in URL kenntlich machen
	window.location.hash = "freig://list";
	
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "todo": "freigabeliste" } ).always( function( data ) {
		
		if( data.main.okay && data.main.list != null ){
			
			 var liste = '<table class="freigaben" width="100%">';
			 liste += '<tr>';
			 liste += '<th>Art</th>';
			 liste += '<th>Name</th>';
			 liste += '<th colspan="3">Bearbeiten</th>';
			 liste += '<th>Pfad</th>';
			 liste += '</tr>';
			
			//Liste anzeigen
			$.each( data.main.list, function ( k,v ){
			
				liste += '<tr fid="'+v.id+'" path="'+v.path+'">';
				liste += '<td><span class="ui-icon ui-icon-'+( v.type == 'folder' ? 'folder-collapsed' : 'document' )+'"></span>'+( v.upload == 'yes' ? '<span class="ui-icon ui-icon-arrowthickstop-1-n" title="Upload aktiviert"></span>' : '' )+'</td>';
				liste += '<td><span class="open_folder" title="Ordner mit Datei anzeigen">'+v.name+'</span></td>';
				liste += '<td><span class="show_link" title="Link zur Datei anzeigen"><span class="ui-icon ui-icon-link"></span></span></td>';
				liste += '<td><a href="'+v.link+'" target="_blank" title="Datei über Freigabelink öffnen"><span class="ui-icon ui-icon-extlink" style="display:inline-block"></span></a></td>';
				liste += '<td><span class="del_link" title="Freigabe löschen"><span class="ui-icon ui-icon-trash" style="display:inline-block"></span></span></td>';
				liste += '<td>my:/'+v.path+'</td>';
				liste += '</tr>';	
				
			});
			
			liste += '</table>';
			
			$( "div.main_files" ).html( liste );
			
			$( "table.freigaben" ).tooltip();
			
			//Link zeigen
			$( "table.freigaben span.show_link" ).unbind('click').click( function (){
				
				//Werte bekommen
				var link = $( this ).parent().parent().find('td a').attr( 'href' );
				
				//Dialog mit Link
				j_alert( 'Die Datei wurde freigegeben.<br /><br /><input readonly="readonly" onclick="this.focus();this.select();" style="border:1px solid black; background-color:gray; text:white; width:100%;" value="'+ link +'"><br /><br />Unter "Freigabe" sehen Sie alle Freigaben und können diese löschen!' );	
			});
			
			//Freigabe löschen
			$( "table.freigaben span.del_link" ).unbind('click').click( function (){
				
				//Wert bekommen
				var id = $( this ).parent().parent().attr( 'fid' );
				
				//Freigabe löschen
				$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "todo": "freigabedel", "id": id } ).always( function( data ) {
						
						//Server sagt okay?
						if( data != null && data.main != null && data.main.okay ){
							
							//OK-Medlung
							j_alert( 'Die Freigabe wurde gelöscht!' );
							
							//Freigaben neu laden
							show_freigaben();
						}
						else{
							//Fehlermeldung
							j_alert( 'Die Freigabe konnte nicht gelöscht werden!' );
						}
					
				});
			});
			
			//Ordner öffne
			$( "table.freigaben span.open_folder" ).unbind('click').click( function (){
				
				//Werte bekommen
				var path = $( this ).parent().attr( 'path' );

				//Werte setzten
				allgvars.folder = 'user';				
				allgvars.path = path.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '')+'/';
		
				//Explorer öffnen
				main_explorer();
				
			});
		}
		else{
			show_error( '404', 'Keine Freigaben gefunden!', "div.main_files" );
		}
		
	});
	
	return;
}

//Touch Buttons einblenden für Alt, Strg & Enter
//Rechtsklick per Button

//Anzahl der ausgeführen Events nach einer Auswahl eines Evens in der Bar
var anzahl;
//Bar nicht aktiviert ode doch?
//	true -> normales bei Klick tun
//	false -> nichts tun
var touchbar_status = true;

//wird vorm Laden eines Ordners, einer Datei aufgerufen, kann durch return false, dies unterbrechen
function shall_do_fileload(){
	return touchbar_status;
};

//Event auslösen
//	art => Art des Events ( 0 => Rechtsklick, 1 => Doppenclick, 2 => Taste)
//	elem => DOM Element auf das das Event ausgeführt werden soll
//	[ code => Code für Taste (bei Tastenvent)]
function trigger_touchbedienung( art, elem, code ){
	//nur beim ersten Klick reagieren
	if( anzahl ==  1){
		
		//nächster Klick
		anzahl++;
		
		if( art == 0 ){
			//Rechtsklick auf Element
			//	elem z.B. 'ul.files li[name=KIMB]'
			$( elem ).trigger("contextmenu");
		}
		else if( art == 1 ){
			//Doppelklick
			//	elem z.B: 'td.edit[sk=1]'
			$( elem ).trigger("dblclick");
		}
		else if( art == 2 ){
			//Tasten
			var touche = $.Event('keyup');
			touche.keyCode = code;
			//	elem z.B.: 'input#new_name'
			$( elem ).trigger(touche);
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

//Eventbar bei Touchgeräten
$( function(){
	//Touchgerät??
	if( ("ontouchstart" in window) || (navigator.msMaxTouchPoints > 0)){
	
		//HTML für Bar
		var html = '<div style="position:fixed; bottom:0; right:30px; z-index:999999; min-width:410px; max-width:100%; min-height:30px; background-color:black; padding:10px; border-top-left-radius:5px; border-top-right-radius:5px;">';
		html += '<span id="touchbed_butt">';
		html += '<input type="radio" id="alt" name="butt">';
		html += '<label for="alt">Alt</label>';
		html += '<input type="radio" id="strg" name="butt">';
		html += '<label for="strg">Strg</label>';
		html += '<input type="radio" id="enter" name="butt">';
		html += '<label for="enter">Enter</label>';
		html += '<input type="radio" id="dopp" name="butt">';
		html += '<label for="dopp">Doppelklick</label>';
		html += '<input type="radio" id="rec" name="butt">';
		html += '<label for="rec">Rechtsklick</label>';
		html += '</span>'
		html += '<span id="touchbed_clo" title="Schließen">&darr;</span>'
		html += '<br /><small style="color:white;">Button und dann Stelle für den Befehl anklicken</small>';
		html += '</div>';
		
		//Bar ins DOM
		$( 'body' ).append( html );
		//Buttons
		$( "#touchbed_butt" ).buttonset();
		//Schließen
		$( "#touchbed_clo" ).button();
		//Hinweis für Schileßen Button
		$( "#touchbed_clo" ).tooltip();
		
		//Klicks verarbeiten
		$( "#touchbed_butt input[type=radio]" ).unbind('click').click( function() {
			var id = $( this ).attr( 'id' );
			
			//neuer erster Klick
			anzahl = 1;
			
			//Touchbar aktiviert
			touchbar_status = false;
		
			//Buttons für Wahlen deaktivieren
			$( "#touchbed_butt" ).buttonset('disable');
		
			//KIMB-Tabelle
			$( "td.edit" ).unbind('click').click( function(){
				
				var elem;
				
				if( id == 'alt' ){
					elem = $( this ).children('textarea');
					trigger_touchbedienung( 2, elem, 18  );
				}	
				else if( id == 'strg' ){
					elem = $( this ).children('textarea');
					trigger_touchbedienung( 2, elem, 17  );
				}
				else if( id == 'dopp' ){
					trigger_touchbedienung( 1, this );
				}
				else if( id == 'rec' ){
					trigger_touchbedienung( 0, this );
				}
				
				//Buttons wieder aktivieren
				$( "#touchbed_butt" ).buttonset('enable');
				//Touchbar deakiviert
				touchbar_status = true;
			});
			
			//Neue Datei und Ordner
			$( "#new_name" ).unbind('click').click( function(){
				
				if( id == 'strg' ){
					trigger_touchbedienung( 2, this, 17  );
				}
				else if( id == 'enter' ){
					trigger_touchbedienung( 2,this, 13 );
				}
				
				//Buttons wieder aktivieren
				$( "#touchbed_butt" ).buttonset('enable');
				//Touchbar deakiviert
				touchbar_status = true;
			});
			
			//Liste Dateien und Ordner
			$( "ul.files li" ).click( function( e ){
				
				if( id == 'rec' ){
					trigger_touchbedienung( 0, this );
				}
				
				//Buttons wieder aktivieren
				$( "#touchbed_butt" ).buttonset('enable');
				//Touchbar deakiviert
				touchbar_status = true;
			});

		});
		$( "#touchbed_clo" ).click( function() {
			$( this ).parent().hide("blur");
		});
		
	}
});