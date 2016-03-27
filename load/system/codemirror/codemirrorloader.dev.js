//auf jQuery warten
$( function () {

	var dones = 0;
	
	//alle CSS-Dateien für codemirror
	var cssfiles = [
		 codemirrorloader_siteurl+'/load/system/codemirror/lib/codemirror.css'
	];
	
	//Dateien nach und nach laden
	cssfiles.forEach( function( v,k ) {
		//einfach an Head appenden
		$( "head" ).append( '<link rel="stylesheet" href="'+v+'">' );
		//gemacht + 1
		dones++; 
	});
	
	//alle JS Dateien
	var jsfiles = [ 	  
		codemirrorloader_siteurl+'/load/system/codemirror/lib/codemirror.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/markdown/addon_overlay.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/xml/xml.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/javascript/javascript.js', 
		codemirrorloader_siteurl+'/load/system/codemirror/mode/css/css.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/htmlmixed/htmlmixed.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/markdown/markdown.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/markdown/gfm.js'
	];

	//JS Datei für Datei laden (async)
	jsfiles.forEach( function( v,k ) {
		//per jQuery laden
		$.getScript( v ).done(function( script, textStatus ) {
			//wenn okay:
			
			//gemacht + 1
			dones++;

			//soviele gemacht, wie in Arrays angegeben?
			if( dones == ( jsfiles.length + cssfiles.length ) ){
				//Event, das CodeMirror geladen!
				$( document ).trigger( 'cms_codemirror_loaded' );
				//auch var, dass er geladen
				codemirrorloader_done = true;
			}			
		});		
	} );
});

//CodeMirror Instanzen
var codemirrorloader_instances = {};
//Einfaches Laden von CodeMirror
//	id => ID der Textarea, welche mit CodeMirror versehen werden soll
function codemirrorloader_add( id ){
		
	function add( domid ){
		codemirrorloader_instances[id] = CodeMirror.fromTextArea(document.getElementById( domid ), {
			lineNumbers: true,
			mode: "gfm"
		});
	}
		
	if( !codemirrorloader_done ){
		$( document ).on( "cms_codemirror_loaded" , function() {
			add( id );
		});
	}
	else{
		add( id );
	}
}


