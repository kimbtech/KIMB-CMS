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
	
	// Cache für JS files erlauben
	//	eigene getScript Funktion
	function MYgetScript( url, options ) {
		// Allow user to set any option except for dataType, cache, and url
		options = $.extend( options || {}, {
			dataType: "script",
			cache: true,
			url: url
		});
 
		// Use $.ajax() since it is more flexible than $.getScript
		// Return the jqXHR object so we can chain callbacks
		return jQuery.ajax( options );
	}

	//JS Datei für Datei laden (async)
	jsfiles.forEach( function( v,k ) {
		//per jQuery laden
		MYgetScript( v );	
	} );

	// jede JS Datei führt dieses Event aus
	$( document ).on( "cms_codemirror_nextfile" , function() {
		//eine weitere Datei geladen
		//	geladen + 1
		dones++;

		//soviele Dateien geladen, wie in Arrays angegeben?
		if( dones == ( jsfiles.length + cssfiles.length ) ){
			//Event, das CodeMirror geladen!
			$( document ).trigger( 'cms_codemirror_loaded' );
			//auch var, dass er geladen
			codemirrorloader_done = true;
		}
	});
});

//CodeMirror Instanzen
var codemirrorloader_instances = {};
//Einfaches Laden von CodeMirror
//	id => ID der Textarea, welche mit CodeMirror versehen werden soll
function codemirrorloader_add( id ){
	
	//CodeMirror einfach lade Funktion	
	function add( domid ){
		codemirrorloader_instances[id] = CodeMirror.fromTextArea(document.getElementById( domid ), {
			lineNumbers: true,
			mode: "gfm"
		});
	}
	
	//schon geladen ?
	if( !codemirrorloader_done ){
		//nein, dann erst nach Event alles laden
		$( document ).on( "cms_codemirror_loaded" , function() {
			add( id );
		});
	}
	else{
		//CM schon geladen => also gleich Textarea machen
		add( id );
	}
}


