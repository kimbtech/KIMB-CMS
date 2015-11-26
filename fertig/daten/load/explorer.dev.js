//Fehler anzeigen
//	Titel, Inhalt, DOM Element für Fehler
function show_error( tit, mes, dom ){
	$( dom ).html( '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 5px;"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 6px;"></span><strong>'+tit+'</strong>&nbsp;&nbsp;&nbsp;'+mes+'</div></div>' );
	 return true;
}

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
	$( cssclass ).html( '<h2>Dateiverwaltung</h2><div id="firstbutt"><input type="radio" name="firstbutt" id="my" checked="checked"><label for="my">Mein Verzeichnis</label><input type="radio" name="firstbutt" id="pu"><label for="pu">Für alle User</label><input type="radio" name="firstbutt" id="frei"><label for="frei">Freigaben</label></div><div class="main_files">Bitte wählen Sie einen Ort &uarr;</div>' );
	
	//Buttons machen
	$( "div#firstbutt" ).buttonset();
	
	//Klicks verarbeiten
	$( "#firstbutt input[type=radio]" ).click( function() {
		set_vars( $( this ).attr( 'id' ) );
	});
	
	//Immer meine Dateien anzeigen
	set_vars( 'my' );
	
}

//wichtige Werte für System
var allgvars = new Object;

//Klicks des Systems verarbeiten
function set_vars( ort  ){
	
	//Meine Dateien
	if( ort == 'my' ){
		allgvars.folder = 'user';
		allgvars.path = '/';
		
		//Explorer öffnen
		main_explorer();  
	}
	//Öffentlich
	else if ( ort == 'pu' ){
		allgvars.folder = 'public';
		allgvars.path = '/';
		
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
			
			//Toolszeige
			var html = '<div class="toolbar"><button id="add_table">Neue Tabelle</button><button id="add_file">Neue Datei</button><button id="add_folder">Neuer Ordner</button><br />';
			html += '<span class="input" style="display:none;"><input type="text" id="new_name" placeholder="Name"><small>Enter zum Speichern; Ctrl zum Abbrechen; Dateien gleichen Namens werden überschrieben</small></span></div>';
			
			//Pathanzeige
			html += '<div class="pathbar">'+allgvars.path+'</div>';
			
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
				html += '<small>Doppelklick auf Pfad zum Bearbeiten; Rechtsklick auf Datei oder Ordner zum Löschen</small>';
				
				//Mehr als eine Element??
				if( folder_ex ){
					//Liste anzeigen
					$( "div.main_files" ).html( html );
					
					//Pfad anpassen
					$( 'div.pathbar' ).unbind('dblclick').dblclick( function() {
						
						//Inhalt
						var val = $( this ).html();
						
						//Ist das Feld schon bearbeitbar gemacht (Input Element vorhanden)
						if( val.indexOf( "<input" ) === -1 ){
							//nein, also einblenden
							
							//Input Feld zeigen 
							$( this ).html( '<input type="text" style="width:95%;" value="'+val+'">' );
							
							//Knopf gedrückt?
							//	wenn Cursor in Input
							$( this ).children("input").keyup( function( event ) {
								//Enter?
								if(event.keyCode == 13){
									//neuen Wert lesen
									var newval = $( this ).val();
									
									//Input Feld weg und neuen Wert wieder in Kästchen setzen
									$( this ).parent().html( newval );
									//Pfadwert anpassen
									allgvars.path = newval;
									//Explorer aktualisieren
									main_explorer();
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
					
					//auf Klicks auf Dateien hören
					$( 'ul.files li' ).unbind('click').click( function() {
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
					
					//Rechtsklick auf Datei => Löschen?
					$(document).on("contextmenu", 'ul.files li', function(e){
					
						//k und sk Werte bestimmen
						var name = $( this ).attr( 'name' );
						var url = $( this ).attr( 'url' );
						var ftype = $( this ).attr( 'ftype' );
						
						//wenn alter Dialog div noch im DOM diesen löschen
						if ($( "div.delfile_explorer" ).length ){
							$( "div.delfile_explorer" ).remove();
						}
						
						//HTML für neuen Dialog DIV		
						var dial = '<div class="delfile_explorer" title="Dateieinstellungen">'
						dial += 'Möchten Sie "'+ name +'" löschen?<br />';
						dial += '<span id="ja">Ja</span>';
						dial += '<div class="delfile_explorer_satus" style="display:none;">Fehler</div>';
						dial += '<div class="frei" style="display:none;"><hr />';
						dial += 'Oder klicken Sie für eine Freigabe via Link auf "Freigeben"!<br />';
						dial += '<span id="frei">Freigeben</span>';
						dial += '</div></div>';
						
						//HTML dem DOM anfügen   
						$( "body" ).prepend( dial );
						
						//Dialog öffnen	 
						$( "div.delfile_explorer" ).dialog({ 
							modal:true,
							buttons:{
								"Abbrechen":function(){
									$( this ).dialog( 'close' );
								}
							}
						});
						
						if( ( ftype == 'file' || ftype == 'kt'  ) && allgvars.folder == 'user' ){
							$( "div.delfile_explorer div.frei" ).css("display", "block");
						}
						
						//Ja/Nein Buttons
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

						//Buttons Freigabe Listener
						$( "div.delfile_explorer div span#frei" ).click( function () {	
							
							//URL der Datei
							allgvars.file = url;
							
							//per AJAX freigeben
							make_freigabe();
							
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

//Tabelle öffen
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
	
	
	//wenn alter Dialog div noch im DOM diesen löschen
	if ($( "div.j_promt" ).length ){
		$( "div.j_promt" ).remove();
	}
						
	//HTML für neuen Dialog DIV		
	var dial = '<div class="j_promt" title="Passwort benötigt!">'
	dial += 'Bitte geben Sie das Passwort für die Tabelle an!<br />';
	dial += '<input type="password" placeholder="Passwort" id="js_table_passw"><br />';
	dial += '<small>Lassen Sie das Feld für unverschlüsselte Tabellen leer.</small>';
	dial += '</div>';
						
	//HTML dem DOM anfügen   
	$( "body" ).prepend( dial );
			
	//Dialog öffnen	 
	$( "div.j_promt" ).dialog({ 
		modal:true,
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
				
				//Dialog schließen
				$( this ).dialog( 'close' );
				
				//Fortsetzen
				open_table_get( name, adding, data_okay );
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
					data = sjcl.decrypt( global_table_passw, data );
				}
				//Bei Fehler Fehlermeldung ausgeben
				catch (e){
					 j_alert( 'Die Tabelle konnte mit diesem Passwort nicht entschlüsselt werden!\r\n(Fehlermeldung:"' + e.message + '")' );
					return;
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
		$( 'div.for_file_kimbta' ).dialog({ width: 720, modal: true, title: name, beforeClose: function( event, ui ) { global_table_passw = ''; } });
		
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
			modal:true
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
	menue += '<textarea id="json_impcode" placeholder=\'[[0,"Vorname","Name"],[1,"Max","Muster"],[2,"Maxa","Mustera"]]\' rows="5" cols="20"></textarea><br />'
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

function make_freigabe(){
	
	//Freigabeanfrage an Server senden
	//	User
	//	Pfad
	//	URL
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "newfreigabe" } ).always( function( data ) {
		
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
	
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "todo": "freigabeliste" } ).always( function( data ) {
		
		if( data.main.okay && data.main.list != null ){
			
			 var liste = '<ul class="freigaben">';
			
			//Liste anzeigen
			$.each( data.main.list, function ( k,v ){
			
				liste += '<li fid="'+v.id+'" path="'+v.path+'">';
				liste += '<span class="ui-icon ui-icon-bullet" style="display:inline-block"></span>'
				liste += '<span class="show_link" title="Link zur Datei anzeigen"><span class="ui-icon ui-icon-link" style="display:inline-block"></span></span>';
				liste += '<a href="'+v.link+'" target="_blank" title="Datei über Freigabelink öffnen"><span class="ui-icon ui-icon-extlink" style="display:inline-block"></span></a>';
				liste += '<span class="del_link" title="Freigabe löschen"><span class="ui-icon ui-icon-trash" style="display:inline-block"></span></span>';
				liste += '<span class="open_folder" title="Ordner mit Datei anzeigen">'+v.name+'</span>';
				liste += '</li>';	
				
			});
			
			liste += '</ul>';
			
			$( "div.main_files" ).html( liste );
			
			$( "ul.freigaben" ).tooltip();
			
			//Link zeigen
			$( "ul.freigaben span.show_link" ).unbind('click').click( function (){
				
				//Werte bekommen
				var link = $( this ).parent().children('a').attr( 'href' );
				
				//Dialog mit Link
				j_alert( 'Die Datei wurde freigegeben.<br /><br /><input readonly="readonly" onclick="this.focus();this.select();" style="border:1px solid black; background-color:gray; text:white; width:100%;" value="'+ link +'"><br /><br />Unter "Freigabe" sehen Sie alle Freigaben und können diese löschen!' );	
			});
			
			//Freigabe löschen
			$( "ul.freigaben span.del_link" ).unbind('click').click( function (){
				
				//Wert bekommen
				var id = $( this ).parent().attr( 'fid' );
				
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
			$( "ul.freigaben span.open_folder" ).unbind('click').click( function (){
				
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