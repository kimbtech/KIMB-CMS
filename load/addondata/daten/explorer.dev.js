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
		allgvars['folder'] = 'user';
		allgvars['path'] = '/';  
	}
	//Meine Gruppen
	else if ( ort == 'gr' ){
		allgvars['folder'] = 'group';
		allgvars['path'] = '/'; 
	}
	//Öffentlich
	else if ( ort == 'pu' ){
		allgvars['folder'] = 'public';
		allgvars['path'] = '/'; 
	}
	else{
		return false;
	}
	
	main_explorer();
	
}

function main_explorer(){
	
	$.post( siteurl+"/ajax.php?addon=daten", { "allgvars": allgvars, "todo": "filelist" } ).always( function( data ) {
			var html = '<ul>';
			
			data = JSON.parse(  data );
			
			$.each( data, function( k,v ){
				html += '<li>'+v+'</li>';	
			});
			
			html += '</ul>';
			
			$( "div.main_files" ).html( html );
	});
	
}