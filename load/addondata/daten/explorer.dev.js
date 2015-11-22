function show_error( tit, mes, dom ){
	$( dom ).html( '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 5px;"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 6px;"></span><strong>'+tit+'</strong>&nbsp;&nbsp;&nbsp;'+mes+'</div></div>' );
	 return true;
}


$( function() {
	load_daten();	
});

var cssclass = "div.addon_daten_main ";

function load_daten(){
	
	$( cssclass ).html( '<h2>Dateiverwaltung</h2><div id="firstbutt"><input type="radio" name="firstbutt" id="my"><label for="my">Mein Verzeichnis</label><input type="radio" name="firstbutt" id="gr"><label for="gr">Gruppen</label><input type="radio" name="firstbutt" id="pu"><label for="pu">Öffentlich</label></div><div class="main_files">Bitte wählen Sie einen Ort &uarr;</div>' );
	
	$( "div#firstbutt" ).buttonset();
	
	$( "#firstbutt input[type=radio]" ).click( function() {
		set_vars( $( this ).attr( 'id' ) );
	});
	
}

//wichtige Werte für System
var allgvars = new Object;

function set_vars( ort  ){
	
	//Meine Dateien
	if( ort == 'my' ){
		allgvars.folder = 'user';
		allgvars.path = '/';  
	}
	//Meine Gruppen
	else if ( ort == 'gr' ){
		allgvars.folder = 'group';
		allgvars.path = '/'; 
	}
	//Öffentlich
	else if ( ort == 'pu' ){
		allgvars.folder = 'public';
		allgvars.path = '/'; 
	}
	else{
		return false;
	}
	
	main_explorer();
	
}

function main_explorer(){
	
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "filelist" } ).always( function( data ) {
			
			var html = '<div class="pathbar">'+allgvars.path+'</div>';
			
			data = data.main;
			
			if( data != null ){
				html += '<ul class="files">';
				
				var elements = 0;
				
				$.each( data, function( k,v ){
					var icon;
					
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
					
					html += '<li url="'+v.url+'" ftype="'+v.type+'" name="'+v.name+'"><span class="ui-icon ui-icon-'+icon+'" style="display:inline-block;"></span>'+v.name+'</li>';
					
					elements++;	
				});
				
				html += '</ul>';
				
				if( elements > 0 ){
					$( "div.main_files" ).html( html );
					
					$( 'ul.files li' ).unbind('click').click( function() {
						var ftype = $( this ).attr( 'ftype' );
						var url = $( this ).attr( 'url' );
						var name = $( this ).attr( 'name' );
						
						if( ftype == 'dir' ){
							allgvars.path = allgvars['path']+url+'/';
							main_explorer();  
						}
						else if( ftype == 'file' ){
							open_file( url );
						}
						else if( ftype == 'kt' ){
							open_table( url, name );
						}
						
					});
				}
				else{
					show_error( '404', 'Ordner nicht gefunden!', "div.main_files"  );	
				}
			}
			else{
				show_error( '404', 'Ordner nicht gefunden!', "div.main_files"  );
			}
	});
	
}

function open_table( url, name ){
	
	allgvars.file = url;
	
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "table" } ).always( function( data ) {
		
		data = data.main;
			
		if( data != null ){
		
			data = JSON.parse( data );
			
			make_table_html( data, 'div.for_file_kimbta' );
		
			$( 'div.for_file_kimbta' ).css( 'display', 'block' );
			
			$( 'div.for_file_kimbta' ).dialog({ width: 720, modal: true, title: name });
			
		}	
	});
}

function open_file( url ){
	
	var link = add_daten.siteurl+"/ajax.php?addon=daten&folder="+ encodeURI( allgvars.folder ) +"&path="+ encodeURI( allgvars.path + url );
	
	window.open( link, "_blank", "width=900px,height=500px,top=20px,left=20px");
}

function make_table_html( data, domel ){
	var html = '<table width="100%" border="border">';
			
	$.each( data, function( k,v ){
				
		html += '<tr>';
				
		if( k == 0){
			$.each( v, function( sk, sv ){
				if( sk == 0){
					html += '<th k="'+k+'" sk="'+sk+'">'+sv+'</th>';
				}
				else{
					html += '<th class="edit" k="'+k+'" sk="'+sk+'">'+sv+'</th>';
				}
			});
		}
		else{
			$.each( v, function( sk, sv ){
				if( sk == 0){
					html += '<td k="'+k+'" sk="'+sk+'">'+sv+'</td>';
				}	
				else{
					html += '<td class="edit" k="'+k+'" sk="'+sk+'">'+sv+'</td>';
				}
			});
		}
				
		html += '</tr>';
		
	});
			
	html += '</table>';
	html += '<button id="new_sp">Spalte hinzufügen</button><br />';
	html += '<button id="new_ze">Zeile hinzufügen</button><br />';
	html += '<small>Doppenklick zum Ändern; Enter zum Speichern; Ctrl zum Verwerfen; Rechtsklick zum Löschen/ Hinzufügen</small><br />';
	html += '<button id="exp_json">JSON Export</button>';
	html += '<span class="add_daten_status" style="display:none;"></span>';
	
	$( domel ).html( html );
	
	$( "div.for_file_kimbta table td.edit, div.for_file_kimbta table th.edit").unbind('dblclick').dblclick( function() {
		
			var k = $( this ).attr( 'k' );
			var sk = $( this ).attr( 'sk' );
			var val = $( this ).html();
			
			if( val.indexOf( "<input" ) === -1 ){
				
				$( this ).html( '<input type="text" value="'+val+'">' );
				
				$( this ).children("input").keyup( function( event ) {
					if(event.keyCode == 13){
						var newval = $( this).val();
						
						newval = newval.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
						
						$( this ).parent().html( newval );
						data[k][sk] = newval;
						
						save_new_table( data );
					}
					else if( event.keyCode == 17 ){
						$( this ).parent().html( val );
					}
				});
			}
	});
	
	$( "button#new_sp" ).unbind('click').click( function () {
		new_spalte( 'end', data, domel );
	});
	
	$( "button#new_ze" ).unbind('click').click( function () {
		new_reihe( 'end', data, domel );
	});
	
	$( "button#exp_json ").unbind('click').click( function () {
		make_json( data );	
	});
	
	$(document).on("contextmenu", 'div.for_file_kimbta table td.edit', function(e){
	
		var k = $( this ).attr( 'k' );
		var sk = $( this ).attr( 'sk' );
		
		if ($( "div.rightclick_menue" ).length){
			$( "div.rightclick_menue" ).remove();
		}
		  			
		var menue = '<div class="rightclick_menue" title="Tabelle hier anpassen">'
		menue += '<button id="sp_add">Spalte hier hinzufügen</button><br />';
		menue += '<button id="ro_add">Reihe hier hinzufügen</button><br />';
		menue += '<small>Jeweils oberhalb bzw. links des angeklickten Kästchens</small><br/>';
		menue += '<button id="sp_del">Spalte hier löschen</button><br />';
		menue += '<button id="ro_del">Reihe hier löschen</button>';
		menue += '</div>';
			   
		 $( "body" ).prepend( menue );
			 
		$( "div.rightclick_menue" ).dialog({ 
			modal:true
		});
		
		$( "div.rightclick_menue button#sp_add" ).click( function () {
			new_spalte(  sk, data, domel );
		});
		$( "div.rightclick_menue button#ro_add" ).click( function () {
			new_reihe( k, data, domel );
		});
		$( "div.rightclick_menue button#sp_del" ).click( function () {
			del_spalte( sk, data, domel );
		});
		$( "div.rightclick_menue button#ro_del" ).click( function () {
			del_reihe(  k, data, domel );
		});
		
		return false;
	});
	
	
	return true;
}

function new_spalte( ort, data, domel ){

	if( ort != 'end' ){
		ort = parseInt( ort );
	}
	
	for( var i = 0; i < data.length; i++){
			
		if( ort == 'end' ){
			data[i].push( 'Wert' );
		}
		else{
			data[i].splice( ort , 0, "Wert");
		}
	}
	
	make_table_html( data, domel );
		
	save_new_table( data );
	
	return;
}

function new_reihe( ort, data, domel ){
	
	var array = new Array();
	
	if( ort != 'end' ){
		ort = parseInt( ort );
	}
		
	for( var i = 0; i < data[0].length; i++){
			
		array.push( "Wert" );
	}
		
	array[0] = data[data.length - 1][0] + 1;
	
	
	if( ort == 'end' ){
		data.push( array );
	}
	else{
		data.splice( ort , 0, array);
	}
		
	make_table_html( data, domel );
		
	save_new_table( data );
	
	return;
}

function del_spalte( ort, data, domel ){
	
	ort = parseInt( ort );
	
	for( var i = 0; i < data.length; i++){

		data[i].splice( ort , 1);

	}

	make_table_html( data, domel );
		
	save_new_table( data );
	
	return;
}

function del_reihe( ort, data, domel ){
	
	ort = parseInt( ort );
	
	data.splice( ort , 1 );
	
	console.log( ort );

	make_table_html( data, domel );
		
	save_new_table( data );
	
	return;
}

function make_json( data ){
	
	var file = JSON.stringify( data );
	
	window.open( "data:text/json;utf-8," + file ,"_blank", "width=900px,height=500px,top=20px,left=20px");
}
//*****************************************************************************************************
//*****************************************************************************************************
//
//	Tabelle Neu
//	Tabelle Verschlüsselung
// 
//	Datei Upload
//	Datei Löschen
//	Ordner Neu
//	Ordner Löschen
//
//	Datei Verschlüsselung
//
//	JSON Import 
//
//*****************************************************************************************************
//*****************************************************************************************************

function save_new_table( data ){
	
	$("span.add_daten_status").html( "Speicherung ..." );
	$("span.add_daten_status").css( { "display":"inline","background-color":"orange", "padding": "5px", "border-radius":"2px", "color":"white", "float":"right"  } );
		
	$.post( add_daten.siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "tablesave", "data": JSON.stringify( data ) } )
		.done(function( data ) {
			if( data.main.wr ){
				$("span.add_daten_status").html( "Gespeichert" );
				$("span.add_daten_status").css( { "background-color":"green"  } );
				
				setTimeout( function() {
					$("span.add_daten_status").css( "display","none");
				}, 5000);
			}
			else{
				$("span.add_daten_status").html( "Fehler" );
				$("span.add_daten_status").css( { "background-color":"red"  } );
			}
		})
		.fail(function() {
			$("span.add_daten_status").html( "Fehler" );
			$("span.add_daten_status").css( { "background-color":"red"  } );
		});
}